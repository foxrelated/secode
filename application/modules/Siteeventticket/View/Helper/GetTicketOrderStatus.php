<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GetTicketOrderStatus.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_View_Helper_GetTicketOrderStatus extends Zend_View_Helper_Abstract {

    /**
     * Assembles action string
     * 
     * @return string
     */
    public function getTicketOrderStatus($order_status, $user_class = null, $admin_class = null) {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $statusValues = array();
        switch ($order_status) {
            case 0 :
                $current_status = $view->translate('Approval Pending');
                if (!empty($user_class)) {
                    $statusValues['class'] = 'seaocore_txt_light';
                }
                if (!empty($admin_class)) {
                    $statusValues['class'] = 'seaocore_txt_light';
                }
                break;
            case 1 :
                $current_status = $view->translate('Payment Pending');
                if (!empty($user_class)) {
                    $statusValues['class'] = 'seaocore_txt_light';
                }
                if (!empty($admin_class)) {
                    $statusValues['class'] = 'seaocore_txt_light';
                }
                break;
            case 2 :
                $current_status = $view->translate('Completed');
                if (!empty($user_class)) {
                    $statusValues['class'] = 'siteeventticket_order_status_complete';
                }
                if (!empty($admin_class)) {
                    $statusValues['class'] = 'siteeventticket_order_status_complete';
                }
                break;
            default :
                $current_status = $view->translate('No Order Status Found');
                $statusValues['class'] = 'seaocore_txt_red';
        }

        if (!empty($user_class)) {
            $statusValues['title'] = $current_status;
            return $statusValues;
        }

        if (!empty($admin_class)) {
            $statusValues['title'] = $current_status;
            return $statusValues;
        }

        return $current_status;
    }

}
