<?php

abstract class Socialstore_Payment_Gateway_PaypalAbstract extends Socialstore_Payment_Gateway_Abstract
{
    const API_VERSION   = '65.0';

    const GATEWAY_URL   = 'https://api-3t.paypal.com/nvp';
    const SANDBOX_URL   = 'https://api-3t.sandbox.paypal.com/nvp';	

    const ACTION_REAUTHORIZATION          = 'reauthorization';

    /**
     * 3-day honor period, You can reauthorize within the 29-day valid period
     */
    const TRXTYPE_DO_REAUTHORIZATION    = 'DoReauthorization';
    const TRXTYPE_DO_CAPTURE            = 'DoCapture';
    const TRXTYPE_DO_VOID               = 'DoVoid';
    const TRXTYPE_REFUND                = 'RefundTransaction';
    const TRXTYPE_CARD_REFUND           = 'DoNonReferencedCredit';
    const TRXTYPE_GET_TRANSACTION_INFO  = 'GetTransactionDetails';
    const TRXTYPE_MANAGE_PENDING        = 'ManagePendingTransactionStatus';
    const TRXTYPE_TRANSACTION_SEARCH    = 'TransactionSearch';
    const TRXTYPE_REFERENCE_CAPTURE     = 'DoReferenceTransaction';

    const PAYMENT_ACTION_SALE   = 'Sale';
    const PAYMENT_ACTION_AUTH   = 'Authorization';

    const RESPONSE_APPROVED     = 'Success';
    const RESPONSE_DECLINED     = 'Failure';

    protected $_credentialKeys = array(
        'user'      => 'USER',
        'password'  => 'PWD',
        'signature' => 'SIGNATURE'
    );

    /**
     * Gateway url getter
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->isSandboxMode() ? self::SANDBOX_URL : self::GATEWAY_URL;
    }

    /**
     * Prepare list of requirements
     *
     * @return Socialstore_Payment_Request_Requirements
     */
    protected function _getRequirements()
    {
        $requirements = parent::_getRequirements();
        $requirements->setOnTransaction(self::ACTION_REAUTHORIZATION, true);
        return $requirements;
    }

    /**
     * Reauthorize a previously authorized payment
     *
     * Because the honor period expire in 3 days, you should explicitly call
     * reauthorization before calling captureAuthorization to determine whether the buyer�s funds are
     * still available.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processReauthorization($request)
    {
        $this->_queryParams['METHOD']           = self::TRXTYPE_DO_REAUTHORIZATION;
        $this->_queryParams['AUTHORIZATIONID']  = $request->getTransaction()->getId();
        $this->_queryParams['AMT']              = $request->getTransaction()->getAmount();
        $this->_queryParams['CURRENCY']         = $request->getTransaction()->getCurrency();
        return $this->_sendRequest(array(
            /* Status of the payment */
            'PAYMENTSTATUS' => 'payment_status',
            /* PendingReason is returned in the response only if PaymentStatus is Pending */
            'PENDINGREASON' => 'pending_reason',
            /* The the kind of seller protection in force for the transaction */
            'PROTECTIONELIGIBILITY' => 'eligibility',
        ));
    }

    /**
     * Send capture request to gateway based on previous transaction id.
     *
     * Capture an authorized payment (DoCapture). Authorization honor period is 3 days.
     * If you the honor period has expired, you should explicitly call
     * _sendReathorization (DoReauthorization) before calling captureAuthorization.
     * Partial capture is allowed with total isFinal flag
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processCapture($request)
    {
        $transaction = $request->getTransaction();
        $this->_queryParams['METHOD']           = self::TRXTYPE_DO_CAPTURE;
        $this->_queryParams['AUTHORIZATIONID']  = $transaction->getId();
        if ($transaction->isFinal()) {
            $this->_queryParams['COMPLETETYPE'] = 'Complete';
        } else {
            $this->_queryParams['COMPLETETYPE'] = 'NotComplete';
        }
        $this->_queryParams['AMT']      = $transaction->getAmount();
        $this->_queryParams['CURRENCY'] = $transaction->getCurrency();
        return $this->_sendRequest();
    }

    /**
     * Void an order or an authorization transaction
     *
     * transaction_id is original authorization ID specifying the authorization to void or,
     * to void an order, the order ID.
     * If you are voiding a transaction that has been reauthorized,
     * use the ID from the original authorization, and not the reauthorization.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processVoid($request)
    {
        $transaction = $request->getTransaction();
        $this->_queryParams['METHOD']           = self::TRXTYPE_DO_VOID;
        $this->_queryParams['AUTHORIZATIONID']  = $transaction->getId();
        return $this->_sendRequest();
    }

    /**
     * Refund request
     *
     * Use the RefundTransaction API to issue one or more refunds associated with a transaction,
     * such as a transaction created by a capture of a payment.
     * The transaction is identified by a transaction ID that PayPal assigns when the payment is captured.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processRefund($request)
    {
        $transaction = $request->getTransaction();
        $options     = $request->getOptions();
        $this->_queryParams['METHOD']           = self::TRXTYPE_REFUND;
        $this->_queryParams['TRANSACTIONID']    = $transaction->getId();
        if ($transaction->isFinal()) {
            $this->_queryParams['REFUNDTYPE'] = 'Full';
        } else {
            $this->_queryParams['REFUNDTYPE'] = 'Partial';
        }
        $this->_queryParams['AMT']      = $transaction->getAmount();
        $this->_queryParams['CURRENCY'] = $transaction->getCurrency();
        if ($options && $options->get('invoice_id')) {
            $this->_queryParams['INVOICEID']    = $options->get('invoice_id');
        }
        if ($options && $options->get('description')) {
            $this->_queryParams['NOTE'] = $options->get('description');
        }

        return $this->_sendRequest(array(
            /* Unique transaction ID of the refund. */
            'REFUNDTRANSACTIONID'   => 'transaction_id',
            /* Transaction fee refunded to original recipient of payment */
            'FEEREFUNDAMT'          => 'refunded_fee',
            /* Amount refunded to original payer */
            'GROSSREFUNDAMT'        => 'refunded_amount',
            /* Amount subtracted from PayPal balance of original recipient of payment to make this refund */
            'NETREFUNDAMT'          => 'balance_amount',
            /* Total of all refunds associated with this transaction */
            'TOTALREFUNDEDAMT'      => 'amount'
        ));
    }

    /**
     * Basic parameters initialization
     *
     * @param Socialstore_Payment_Request   $request
     * @return Socialstore_Payment_GatewayAbstract
     */
    protected function _initQuery ($request)
    {
        parent::_initQuery($request);
        $this->_queryParams['VERSION']  = self::API_VERSION;
        return $this;
    }

    /**
     * Prepare unified response based on HTTP response
     *
     * @param Zend_Http_Response $response
     * @param array              $responseMap
     * @return Socialstore_Payment_Response
     */
    protected function _prepareResponse(Zend_Http_Response $response, $responseMap)
    {
        $body = array();
        parse_str($response->getRawBody(), $body);
        $responseMap['AUTHORIZATIONID'] = 'transaction_id';
        $responseMap['TRANSACTIONID']   = 'transaction_id';
        $responseMap['ACK']             = 'code';
        $responseMap['L_LONGMESSAGE0']  = 'message';
        $responseMap['AVSCODE']         = 'avs_code';
        $responseMap['CVV2MATCH']       = 'card_verification';
        $responseMap['CORRELATIONID']   = 'paypal_correlation_id';

        $options = new Socialstore_Payment_Options($body);
        $options->import($body, $responseMap);

        switch ($options->get('code')) {
            case self::RESPONSE_APPROVED:
                $status = Socialstore_Payment_Response::STATUS_APPROVED;
                break;
            case self::RESPONSE_DECLINED:
                $status = Socialstore_Payment_Response::STATUS_DECLINED;
                break;
            default:
                $status = Socialstore_Payment_Response::STATUS_ERROR;
                break;
        }
        $result = new Socialstore_Payment_Response($status);
        $result->setMessages($options->get('message'));
        $result->setOptions($options);
        return $result;
    }
}
