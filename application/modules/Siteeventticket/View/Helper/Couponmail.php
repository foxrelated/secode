<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Couponmail.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_View_Helper_Couponmail extends Zend_View_Helper_Abstract {

    public function couponmail($data = array()) {

        return $this->view->partial('coupon/_set-mail.tpl', 'siteeventticket', $data);
    }

}
