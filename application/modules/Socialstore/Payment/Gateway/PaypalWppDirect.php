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

require_once APPLICATION_PATH. '/application/modules/Socialstore/Payment/Gateway/PaypalWppAbstract.php';

class Socialstore_Payment_Gateway_PaypalWppDirect extends Socialstore_Payment_Gateway_PaypalWppAbstract
{
    const TRXTYPE_DO_DIRECT_PAYMENT     = 'DoDirectPayment';

    /**
     * When you call DoCapture for the final payment, you must set the COMPLETETYPE field to
     * Complete. Prior calls to DoCapture must set this field to NotComplete.
     */

    /**
     * Supported cards:
     * US: Visa, MasterCard, Discover, Amex, Maestro, Solo
     * UK: Maestro, Solo, MasterCard, Discover, and Visa
     * CA: MasterCard and Visa
     */

    /**
     * Prepare list of requirements
     *
     * @return Socialstore_Payment_Request_Requirements
     */
    protected function _getRequirements()
    {
        $requirements = parent::_getRequirements();
        $requirements->setOnOptions(
            array(Socialstore_Payment::ACTION_AUTH, Socialstore_Payment::ACTION_SALE),
            array('customer_ip')
        );
        return $requirements;
    }

    /**
     *
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processAuth($request)
    {
        $this->_initQueryMethod($request);
        $this->_initQueryOrder($request->getOrder());
        $this->_initQueryOptions($request);
        $this->_queryParams['METHOD']           = self::TRXTYPE_DO_DIRECT_PAYMENT;
        $this->_queryParams['PAYMENTACTION']    = self::PAYMENT_ACTION_AUTH;
        $this->_queryParams['RETURNFMFDETAILS'] = 1;
        return $this->_sendRequest();
    }

    /**
     *
     *
     * @param Socialstore_Payment_Request $request
     * @return Socialstore_Payment_Response
     */
    protected function _processSale($request)
    {
        $this->_initQueryMethod($request);
        $this->_initQueryOrder($request->getOrder());
        $this->_initQueryOptions($request);
        $this->_queryParams['METHOD']           = self::TRXTYPE_DO_DIRECT_PAYMENT;
        $this->_queryParams['PAYMENTACTION']    = self::PAYMENT_ACTION_SALE;
        $this->_queryParams['RETURNFMFDETAILS'] = 1;
        return $this->_sendRequest();
    }

    /**
     * Initialize payment method details
     *
     * @param Socialstore_Payment_Request $request
     */
    protected function _initQueryMethod($request)
    {
        $method = $request->getMethod();
        $this->_queryParams['CREDITCARDTYPE']   = $method->getType();
        $this->_queryParams['ACCT']             = $method->getNumber();
        $this->_queryParams['EXPDATE']          = $method->getExpirationDate('mY');
        if ($method->getCvv()) {
            $this->_queryParams['CVV2'] = $method->getCvv();
        }
        if (in_array(strtolower($method->getType()), array('maestro', 'solo'))) {
            $this->_queryParams['STARTDATE']    = $method->getStartDate('mY');
            $this->_queryParams['ISSUENUMBER']  = $method->getIssueNumber();
        }
    }

    /**
     * Initialize order parameters
     *
     * @param Socialstore_Payment_Data_Order $order
     */
    protected function _initQueryOrder($order)
    {
        $this->_queryParams['AMT']          = $order->getTotal();
        $this->_queryParams['CURRENCYCODE'] = $order->getCurrency();
        $this->_queryParams['INVNUM']       = $order->getId();
        $this->_queryParams['SHIPPINGAMT']  = $order->getShipping();
        $this->_queryParams['HANDLINGAMT']  = $order->getHandling();
        $this->_queryParams['TAXAMT']       = $order->getTax();

        $billing    = $order->getBillingAddress();
        if ($billing) {
            $this->_queryParams['EMAIL']        = $billing->getEmail();
            $this->_queryParams['FIRSTNAME']    = $billing->getFirstName();
            $this->_queryParams['LASTNAME']     = $billing->getLastName();
            $this->_queryParams['STREET']       = $billing->getStreet();
            $this->_queryParams['STREET2']      = $billing->getStreet2();
            $this->_queryParams['CITY']         = $billing->getCity();
            $this->_queryParams['STATE']        = $billing->getRegion();
            $this->_queryParams['COUNTRYCODE']  = $billing->getCountry();
            $this->_queryParams['ZIP']          = $billing->getPostCode();
        }
        $shipping   = $order->getShippingAddress();
        if ($shipping) {
            $this->_queryParams['SHIPTONAME']       = $shipping->getFullName();
            $this->_queryParams['SHIPTOSTREET']     = $shipping->getStreet();
            $this->_queryParams['SHIPTOSTREET2']    = $shipping->getStreet2();
            $this->_queryParams['SHIPTOCITY']       = $shipping->getCity();
            $this->_queryParams['SHIPTOSTATE']      = $shipping->getRegion();
            $this->_queryParams['SHIPTOCOUNTRY']    = $shipping->getCountry();
            $this->_queryParams['SHIPTOZIP']        = $shipping->getPostCode();
            $this->_queryParams['SHIPTOPHONENUM']   = $shipping->getPhone();
        }
        $items = $order->getItems();
        if ($items) {
            $this->_initQueryItems($items);
        }
    }

    /**
     * Initialize order items
     *
     * @param array $items
     */
    protected function _initQueryItems($items)
    {
        $index = 0;
        $itemsAmount = 0;
        foreach ($items as $item) {
            $this->_queryParams['L_NAME'.$index]    = $item->getName();
            $this->_queryParams['L_DESC'.$index]    = $item->getDescription();
            $this->_queryParams['L_AMT'.$index]     = $item->getPrice();
            $this->_queryParams['L_NUMBER'.$index]  = $item->getId();
            $this->_queryParams['L_QTY'.$index]     = $item->getQty();
            if ($item->getTax()) {
                $this->_queryParams['L_TAXAMT'.$index]  = $item->getTax();
            }
            $itemsAmount+= $item->getPrice();
            $index++;
        }
        $this->_queryParams['ITEMAMT']  = $itemsAmount;
    }

    /**
     * Initialize additional options
     *
     * @param Socialstore_Payment_Request $request
     */
    protected function _initQueryOptions($request)
    {
        $info = $request->getOptions();
        $additional = $info->map(
            array(
                'customer_ip'   => 'IPADDRESS',
                'notify_url'    => 'NOTIFYURL',
                'description'   => 'DESC',
                'description'   => 'NOTE',
            )
        );
        $this->_queryParams = array_merge($this->_queryParams, $additional);
    }
}