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

require_once APPLICATION_PATH. '/application/modules/Socialstore/Payment/Gateway/PayflowWppAbstract.php';

/**
 * @link https://cms.paypal.com/cms_content/US/en_US/files/developer/PP_WPPPF_Guide.pdf
 */
class Socialstore_Payment_Gateway_PayflowWppDirect extends Socialstore_Payment_Gateway_PayflowWppAbstract
{
    const ACTION_CARD_REFUND    = 'card_refund';

    /**
     * @return Socialstore_Payment_Request_Requirements
     */
    protected function _getRequirements()
    {
        $requirements = parent::_getRequirements();
        $requirements->setOnMethod(self::ACTION_CARD_REFUND, array('Socialstore_Payment_Method_Card'));
        return $requirements;
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
        if (!$request->getTransaction()->isFinal()) {
            $this->_queryParams['CAPTURECOMPLETE'] = 'N';
        }
        return parent::_processCapture($request);
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
     * Initialize payment method details
     *
     * @param Socialstore_Payment_Request $request
     */
    protected function _initQueryMethod($request)
    {
        parent::_initQueryMethod($request);
        $this->_queryParams['ACCTTYPE'] = $this->_getCardTypeCode($request->getMethod()->getType());
    }

    /**
     * Get card type index
     *
     * @param string $type
     * @return integer
     */
    protected function _getCardTypeCode($type)
    {
        $types = array('visa', 'mastercard', 'discover', 'amex', 'dinersclub', 'jcb');
        $type = strtolower($type);
        $index = array_search($type, $types);
        if ($index === false) {
            $index = 8;
        }
        return $index;
    }
}