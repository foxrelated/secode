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

require_once APPLICATION_PATH. '/application/modules/Socialstore/Payment/Gateway/PayflowAbstract.php';

/**
 * PaflowPro gateway integration
 *
 * @link https://cms.paypal.com/cms_content/US/en_US/files/developer/PP_PayflowPro_Guide.pdf
 */
class Socialstore_Payment_Gateway_PayflowPro extends Socialstore_Payment_Gateway_PayflowAbstract
{
    const ACTION_CAPTURE_VOICE_AUTHORIZATION    = 'capture_voice_authorization';
    const ACTION_VERIFICATION                   = 'verification';
    const ACTION_INQUIRY                        = 'inquiry';
    const ACTION_CARD_REFUND                    = 'card_refund';

    /**
     * @return Socialstore_Payment_Request_Requirements
     */
    protected function _getRequirements()
    {
        $requirements = parent::_getRequirements();
        $requirements->setOnTransaction(self::ACTION_INQUIRY, true);
        $requirements->setOnMethod(
            array(self::ACTION_CARD_REFUND, self::ACTION_VERIFICATION),
            array('Socialstore_Payment_Method_Card')
        );
        $requirements->setOnOptions(self::ACTION_CAPTURE_VOICE_AUTHORIZATION, array('auth_code'));
        return $requirements;
    }

    /**
     * Send verification request to gateway
     *
     * Account Verification, also known as zero dollar Authorization,
     * verifies credit card information.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processVerification($request)
    {
        $this->_initQueryMethod($request);
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_AUTH_ONLY;
        $this->_queryParams['AMT']      = 0;

        return $this->_sendRequest();
    }

    /**
     * Send capture request to gateway based on auth_code
     *
     * A Voice Authorization transaction is a transaction
     * that is authorized over the telephone from the processing network.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processCaptureVoiceAuthorization($request)
    {
        $this->_initQueryInfo($request);
        $this->_queryParams['TRXTYPE']   = self::TRXTYPE_VOICE_AUTH;

        return $this->_sendRequest();
    }

    /**
     * Send credit card refund request to gateway
     *
     * The Credit transaction refunds the specified amount to the cardholder.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processCardRefund($request)
    {
        $this->_initQueryMethod($request);
        $this->_initQueryTotals($request);
        $this->_queryParams['TRXTYPE']   = self::TRXTYPE_CREDIT;

        return $this->_sendRequest();
    }

    /**
     * Send inquiry request to gateway
     *
     * An Inquiry transaction returns the result and status of a transaction.
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processInquiry($request)
    {
        $this->_queryParams['ORIGID']   = $request->getTransaction()->getId();
        $this->_queryParams['TRXTYPE']  = self::TRXTYPE_INQUIRY;
        return $this->_sendRequest();
    }

    /**
     *
     * @param Socialstore_Payment_Data_Order_Address $address
     */
    protected function _initQueryBillingAddress($address)
    {
        parent::_initQueryBillingAddress($address);
        $this->_queryParams['BILLTOCOUNTRY']= $address->getCountry();
    }
}
