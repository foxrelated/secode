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

class Socialstore_Payment_Gateway_PayflowWppAbstract extends Socialstore_Payment_Gateway_PayflowAbstract
{
    /**
     * Overrides with assing paypal_transaction_id to result
     *
     * @param Zend_Http_Response $response
     * @param array $responseMap
     * @return Socialstore_Payment_Response
     */
    protected function _prepareResponse(Zend_Http_Response $response, $responseMap)
    {
        $responseMap['PPREF'] = 'paypal_transaction_id';
        return parent::_prepareResponse($response, $responseMap);
    }

}