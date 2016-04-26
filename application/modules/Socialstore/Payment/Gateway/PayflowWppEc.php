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

/**
 * @link https://cms.paypal.com/cms_content/US/en_US/files/developer/PFP_ExpressCheckout_PP.pdf
 */
class Socialstore_Payment_Gateway_PayflowWppEc extends Socialstore_Payment_Gateway_PayflowWppAbstract
{
    const LIVE_CHECKOUT_URL     = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
    const SANDBOX_CHECKOUT_URL  = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';

    const ACTION_CHECKOUT_DETAILS   = 'checkout_details';
    const ACTION_ORDER              = 'order';
    const ACTION_ORDER_AUTH         = 'order_auth';
    const ACTION_REAUTHORIZATION    = 'reauthorization';

    const TRXTYPE_ORDER             = 'O';

    const TENDER_PAYPAL = 'P';

    const EC_SET    = 'S';
    const EC_GET    = 'G';
    const EC_DO     = 'D';

    protected $_defaultTender   = self::TENDER_PAYPAL;

    /**
     * Prepare list of requirements
     *
     * @return Socialstore_Payment_Request_Requirements
     */
    protected function _getRequirements()
    {
        $requirements = parent::_getRequirements();
        $requirements->setOnOptions(Socialstore_Payment::ACTION_INIT, array('return_url', 'cancel_url'));
        $requirements->setOnOptions(self::ACTION_CHECKOUT_DETAILS, array('token'));
        $requirements->setOnOptions(
            array(Socialstore_Payment::ACTION_AUTH, Socialstore_Payment::ACTION_SALE, self::ACTION_ORDER),
            array('token', 'payer_id')
        );
        $requirements->setOnOrder(self::ACTION_ORDER, true);
        $requirements->setOnTransaction(array(self::ACTION_ORDER_AUTH, self::ACTION_REAUTHORIZATION), true);
        return $requirements;
    }

    /**
     * The Set Express Checkout request passes the transaction details
     * from your website to PayPal when a buyer chooses to pay with PayPal.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processInit($request)
    {
        /* TRXTYPE=A allow use both (sale, order and authorization) after checkout is done */
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_AUTH_ONLY;
        $this->_queryParams['ACTION']   = self::EC_SET;
        $this->_queryParams['RETURNURL']= $request->getOption('return_url');
        $this->_queryParams['CANCELURL']= $request->getOption('cancel_url');
        $this->_initQueryOrder($request->getOrder());
        $response = $this->_sendRequest(array('TOKEN' => 'token'));

        $url = $this->isSandboxMode() ? self::SANDBOX_CHECKOUT_URL : self::LIVE_CHECKOUT_URL;
        $url.=$response->getOption('token');
        $response->getOptions()->set('redirect_url', $url);
        return $response;
    }

    /**
     * Obtains information about the buyer from PayPal, including shipping information.
     *
     * The Get Express Checkout Details request enables you to retrieve the buyer�s billing
     * information, such as the shipping address and email address
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processCheckoutDetails($request)
    {
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_AUTH_ONLY;
        $this->_queryParams['ACTION']   = self::EC_GET;
        $this->_queryParams['TOKEN']    = $request->getOptions()->get('token');
        return $this->_sendRequest();
    }

    /**
     * An Authorization transaction represents an agreement to pay. It places the buyer�s funds on
     * hold for a three-day honor period is valid for 29 days.
     *
     * If your business does not provide immediate fulfillment of products or services, an
     * Authorization enables you to capture funds with a Delayed Capture transaction when backordered
     * merchandise, for example, does become available. You can capture up to the
     * authorized amount specified in the original Authorization transaction.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processAuth($request)
    {
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_AUTH_ONLY;
        $this->_initQueryDoCheckoutInfo($request);
        return $this->_sendRequest();
    }

    /**
     * A Sale transaction charges the specified amount against the account,
     * and marks the transaction for immediate fund transfer.
     *
     * Use a Sale transaction when you can fulfill an order immediately and you know the final
     * amount of the payment at the time you send the Do Express Checkout Payment Details
     * request.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processSale($request)
    {
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_SALE;
        $this->_initQueryDoCheckoutInfo($request);
        return $this->_sendRequest();
    }

    /**
     * An Order transaction represents an agreement to pay one or more authorized amounts up to
     * the specified total over a maximum of 29 days.
     *
     * Orders provide you with greater flexibility in delivering merchandise than Authorizations. You
     * should use an Order when a Sale or an Authorization with a single Do Reauthorization do not
     * meet your needs.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processOrder($request)
    {
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_SALE;
        $this->_initQueryDoCheckoutInfo($request);
        return $this->_sendRequest();
    }

    /**
     * Send authorization request based on transaction id from original Order transaction
     *
     * Applicable for TRXTYPE=0
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processOrderAuth($request)
    {
        $transaction = $request->getTransaction();
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_AUTH_ONLY;
        $this->_queryParams['ORIGID']   = $transaction->getId();
        if ($transaction->getAmount()) {
            $this->_queryParams['AMT'] = $transaction->getAmount();
        }
        return $this->_sendRequest();
    }

    /**
     * To reauthorize an Authorization for an additional three-day honor period,
     * you can use a Do Reauthorization transaction.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processReauthorization($request)
    {
        $transaction = $request->getTransaction();
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_AUTH_ONLY;
        $this->_queryParams['ORIGID']   = $transaction->getId();
        $this->_queryParams['DOREAUTHORIZATION'] = 1;
    }

    /**
     * Added partial capture functionality
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processCapture($request)
    {
        if ($request->getTransaction()->isFinal()) {
            $this->_queryParams['CAPTURECOMPLETE'] = 'Y';
        } else {
            $this->_queryParams['CAPTURECOMPLETE'] = 'N';
        }
        $this->_queryParams['NOTE']     = $request->getOption('note');
        return parent::_processCapture($request);
    }

    /**
     * Added refund comments functionality
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processRefund($request)
    {
        $this->_queryParams['MEMO'] = $request->getOption('note');
        return parent::_processRefund($request);
    }

    /**
     * Added void comment
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processVoid($request)
    {
        $this->_queryParams['NOTE']     = $request->getOption('note');
        return parent::_processVoid($request);
    }

    /**
     * Override with adding TENDER to request
     *
     * @param Socialstore_Payment_Request $request
     */
    protected function _initQuery($request)
    {
        parent::_initQuery($request);
        $this->_queryParams['TENDER']   = self::TENDER_PAYPAL;
    }

    /**
     *
     * @param Socialstore_Payment_Request $request
     */
    protected function _initQueryDoCheckoutInfo($request)
    {
        $this->_queryParams['TOKEN']    = $request->getOptions()->get('token');
        $this->_queryParams['ACTION']   = self::EC_DO;
        $this->_queryParams['PAYERID']  = $request->getOptions()->get('payer_id');
        $this->_initQueryOrder($request->getOrder());
    }
}