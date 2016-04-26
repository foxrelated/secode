<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package Socialstore_Payment
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once APPLICATION_PATH. '/application/modules/Socialstore/Payment/GatewayAbstract.php';

abstract class Socialstore_Payment_Gateway_PayflowAbstract extends Socialstore_Payment_GatewayAbstract
{
    const GATEWAY_URL   = 'https://payflowpro.paypal.com';
    const SANDBOX_URL   = 'https://pilot-payflowpro.paypal.com';

    const ACTION_REFERENCE_AUTH                 = 'reference_auth';
    const ACTION_REFERENCE_SALE                 = 'reference_sale';

    const TRXTYPE_AUTH_ONLY         = 'A';
    const TRXTYPE_SALE              = 'S';
    const TRXTYPE_CREDIT            = 'C';
    const TRXTYPE_DELAYED_CAPTURE   = 'D';
    const TRXTYPE_VOID              = 'V';
    const TRXTYPE_VOICE_AUTH        = 'F';
    const TRXTYPE_INQUIRY           = 'I';
    const TRXTYPE_DUPLICATE         = 'N';

    const TENDER_CREDIT_CARD    = 'C';

    const RESPONSE_APPROVED                = 0;
    const RESPONSE_DECLINED                = 12;
    const RESPONSE_FRAUDSERVICE_FILTER     = 126;
    const RESPONSE_DECLINED_BY_MERCHANT    = 128;

    protected $_credentialKeys = array(
        'user'      => 'USER',
        'partner'   => 'PARTNER',
        'vendor'    => 'VENDOR',
        'password'  => 'PWD',
    );

    protected $_defaultTender   = self::TENDER_CREDIT_CARD;

    /**
     * @return Socialstore_Payment_Request_Requirements
     */
    protected function _getRequirements()
    {
        $requirements = parent::_getRequirements();
        $requirements->setOnTransaction(
            array(self::ACTION_REFERENCE_AUTH, self::ACTION_REFERENCE_SALE),
            true
        );
        return $requirements;
    }

    /**
     * Url getter
     *
     * @return string
     */
    public function getUrl ()
    {
        return $this->isSandboxMode() ? self::SANDBOX_URL : self::GATEWAY_URL;
    }

    /**
     * Method overwritten.
     *
     * The Request Body should NOT be url-encoded.
     *
     * @link https://www.x.com/docs/DOC-1642
     * @param Zend_Http_Client $httpClient
     * @return Socialstore_Payment_GatewayAbstract
     */
    protected function _setRequestParams($httpClient)
    {
        $httpClient->setHeaders(array(
            'X-VPS-VIT-CLIENT-CERTIFICATION-ID' => 'Magento',
            'X-VPS-REQUEST-ID'                  => md5(uniqid(microtime(), true)),
            'X-VPS-CLIENT-TIMEOUT'              => 45,
            'X-VPS-VIT-INTEGRATION-PRODUCT'     => 'Magento',
            'X-VPS-VIT-RUNTIME-VERSION'         => PHP_VERSION,
        ));
        $data = http_build_query($this->_queryParams, '', '&');
        $httpClient->setRawData(urldecode($data));
        return $this;
    }

    /**
     * Send authorization request to gateway
     *
     * An Authorization transaction places a hold on the cardholders open-to-buy limit,
     * lowering the cardholders limit by the amount of the transaction.
     * It does not transfer funds.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processAuth($request)
    {
        $this->_initQueryMethod($request);
        $this->_initQueryOrder($request->getOrder());
        $this->_initQueryOptions($request);
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_AUTH_ONLY;

        return $this->_sendRequest();
    }

    /**
     * Send sale transaction request to gateway
     *
     * The Sale transaction charges the specified amount against the account,
     * and marks the transaction for immediate fund transfer during the next settlement period.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processSale($request)
    {
        $this->_initQueryMethod($request);
        $this->_initQueryOrder($request->getOrder());
        $this->_initQueryOptions($request);
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_SALE;
        return $this->_sendRequest();
    }

    /**
     * Send capture request to gateway based on previous transaction id
     *
     * A Delayed Capture transaction is performed after an Authorization to capture
     * the original Authorization amount.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processCapture($request)
    {
        $transaction = $request->getTransaction();
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_DELAYED_CAPTURE;
        $this->_queryParams['ORIGID']   = $transaction->getId();
        if ($transaction->getAmount()) {
            $this->_queryParams['AMT'] = $transaction->getAmount();
        }
        return $this->_sendRequest();
    }

    /**
     * Send void request to gateway
     *
     * The Void transaction prevents a transaction from being settled.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processVoid($request)
    {
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_VOID;
        $this->_queryParams['ORIGID']   = $request->getTransaction()->getId();
        return $this->_sendRequest();
    }

    /**
     * Send refund request to gateway based on previous transaction id
     *
     * The Credit transaction refunds the specified amount to the cardholder.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processRefund($request)
    {
        $transaction = $request->getTransaction();
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_CREDIT;
        $this->_queryParams['ORIGID']   = $transaction->getId();
        if ($transaction->getAmount()) {
            $this->_queryParams['AMT'] = $transaction->getAmount();
        }
        return $this->_sendRequest();
    }

    /**
     * Send referenced authorization request based on previous transaction id
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processReferenceAuth($request)
    {
        $transaction = $request->getTransaction();
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_AUTH_ONLY;
        $this->_queryParams['TENDER']   = $this->_defaultTender;
        $this->_queryParams['ORIGID']   = $transaction->getId();
        $this->_queryParams['AMT']      = $transaction->getAmount();
        return $this->_sendRequest();
    }

    /**
     * Send referenced sale request based on previous transaction id
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processReferenceSale($request)
    {
        $transaction = $request->getTransaction();
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_SALE;
        $this->_queryParams['TENDER']   = $this->_defaultTender;
        $this->_queryParams['ORIGID']   = $transaction->getId();
        $this->_queryParams['AMT']      = $transaction->getAmount();
        return $this->_sendRequest();
    }

    /**
     * Basic parameters initialization
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_GatewayAbstract
     */
    protected function _initQuery ($request)
    {
        parent::_initQuery($request);
        $this->_queryParams['VERBOSITY']= 'MEDIUM';
        return $this;
    }

    /**
     * Initialize payment method details
     *
     * @param Socialstore_Payment_Request $request
     */
    protected function _initQueryMethod($request)
    {
        $method = $request->getMethod();
        $this->_queryParams['TENDER']   = self::TENDER_CREDIT_CARD;
        $this->_queryParams['ACCT']     = $method->getNumber();
        $this->_queryParams['EXPDATE']  = $method->getExpirationDate();
        if ($method->getCvv()) {
            $this->_queryParams['CVV2'] = $method->getCvv();
        }
    }

    /**
     * Initialize query order data
     *
     * @param Socialstore_Payment_Data_Order $order
     */
    protected function _initQueryOrder($order)
    {
        $this->_queryParams['AMT']      = $order->getTotal();
        $this->_queryParams['CURRENCY'] = $order->getCurrency();
        $this->_queryParams['INVNUM']   = $order->getId();
        if ($order->getShipping()) {
            $this->_queryParams['FREIGHTAMT']    = $order->getShipping();
        }
        if ($order->getTax()) {
            $this->_queryParams['TAXAMT']   = $order->getTax();
        }
        if ($order->getHandling()) {
            $this->_queryParams['HANDLINGAMT'] = $order->getHandling();
        }

        $items = $order->getItems();
        if ($items) {
            $this->_initQueryOrderItems($items);
        }
        $this->_initQueryOrderAddresses($order);
    }

    /**
     * Initialize query order item data
     *
     * @param array $items
     */
    protected function _initQueryOrderItems($items)
    {
        $index = 0;
        $amount = 0;
        foreach ($items as $item) {
            $this->_initQueryOrderItem($item, $index);
            $amount+= $item->getQty()*$item->getPrice();
            $index++;
        }
        $this->_queryParams['ITEMAMT'] = $amount;
    }

    /**
     * Initialize order item data
     *
     * @param Socialstore_Payment_Data_Order_Item $item
     * @param int $index
     */
    protected function _initQueryOrderItem($item, $index)
    {
        $this->_queryParams['L_NAME'.$index] = $item->getName();
        $this->_queryParams['L_DESC'.$index] = $item->getDescription();
        $this->_queryParams['L_COST'.$index] = $item->getPrice();
        if ($item->getTax()) {
            $this->_queryParams['L_TAXAMT'.$index] = $item->getTax();
        }
        $this->_queryParams['L_QTY'.$index] = $item->getQty();
        $this->_queryParams['L_SKU'.$index] = $item->getId();
    }

    /**
     * Initialize order addresses
     *
     * @param Socialstore_Payment_Data_Order $order
     */
    protected function _initQueryOrderAddresses($order)
    {
        $billing    = $order->getBillingAddress();
        $shipping   = $order->getShippingAddress();
        if ($billing) {
            $this->_initQueryBillingAddress($billing);
        }
        if ($shipping) {
            $this->_initQueryShppingAddress($shipping);
        }
    }

    /**
     * Prepare billing information for gateway request
     *
     * @param Socialstore_Payment_Data_Order_Address $address
     */
    protected function _initQueryBillingAddress($address)
    {
        $this->_queryParams['EMAIL']        = $address->getEmail();
        $this->_queryParams['PHONENUM']     = $address->getPhone();
        $this->_queryParams['FIRSTNAME']    = $address->getFirstName();
        $this->_queryParams['MIDDLENAME']   = $address->getMiddleName();
        $this->_queryParams['LASTNAME']     = $address->getLastName();
        $this->_queryParams['COMPANYNAME']  = $address->getCompany();
        $this->_queryParams['STREET']       = $address->getFullStreet();
        $this->_queryParams['CITY']         = $address->getCity();
        $this->_queryParams['STATE']        = $address->getRegion();
        $this->_queryParams['COUNTRY']      = $address->getCountry();
        $this->_queryParams['ZIP']          = $address->getPostCode();
    }

    /**
     *
     * @param Socialstore_Payment_Data_Order_Address $address
     */
    protected function _initQueryShppingAddress($address)
    {
        $this->_queryParams['SHIPTOFIRSTNAME']  = $address->getFirstName();
        $this->_queryParams['SHIPTOMIDDLENAME'] = $address->getMiddleName();
        $this->_queryParams['SHIPTOLASTNAME']   = $address->getLastName();
        $this->_queryParams['SHIPTOSTREET']     = $address->getFullStreet();
        $this->_queryParams['SHIPTOCITY']       = $address->getCity();
        $this->_queryParams['SHIPTOSTATE']      = $address->getRegion();
        $this->_queryParams['SHIPTOCOUNTRY']    = $address->getCountry();
        $this->_queryParams['SHIPTOZIP']        = $address->getPostCode();
    }

    /**
     * Initialize additional options
     *
     * @param Socialstore_Payment_Request $request
     */
    protected function _initQueryOptions($request)
    {
        $options = $request->getOptions();
        if ($options) {
            $additional = $options->map(array(
                'invoice_id'    => 'INVNUM',
                'order_id'      => 'PONUM',
                'description'   => 'COMMENT1',
                'customer_id'   => 'CUSTCODE',
                'customer_ip'   => 'CUSTIP',
                'auth_code'     => 'AUTHCODE',
            ));
            $this->_queryParams = array_merge($this->_queryParams, $additional);
        }
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
        $responseMap = array_merge($responseMap, array(
            'PNREF'     => 'transaction_id',
            'RESULT'    => 'code',
            'RESPMSG'   => 'message',
            'AUTHCODE'  => 'auth_code',
            'PROCAVS'   => 'avs_code',
            'CVV2MATCH' => 'ccv_code',
        ));
        $options = new Socialstore_Payment_Data_Options($body);
        $options->import($body, $responseMap);

        switch ((int)$options->get('code')) {
            case self::RESPONSE_APPROVED:
                $status = Socialstore_Payment_Response::STATUS_APPROVED;
                break;
            case self::RESPONSE_DECLINED:
                $status = Socialstore_Payment_Response::STATUS_DECLINED;
                break;
            case self::RESPONSE_FRAUDSERVICE_FILTER:
                $status = Socialstore_Payment_Response::STATUS_PENDING;
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
