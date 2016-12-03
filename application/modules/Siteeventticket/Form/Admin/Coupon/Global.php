<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Admin_Coupon_Global extends Engine_Form {

    public function init() {
        
        $this
            ->setTitle('General Settings')
            ->setDescription('These settings affect all members in your community.');

        $this->addElement('Text', 'siteeventticket_couponmanifesturl', array(
            'label' => 'Event Coupons URL alternate text for "event-coupons"',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Please enter the text below which you want to display in place of "eventticketcoupon" in the URLs of this plugin.',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.couponmanifesturl', "event-coupons"),
        ));

        $this->addElement('Radio', 'siteeventticket_couponapproval', array(
            'label' => 'Coupon Approval Moderation',
            'description' => 'Do you want new Coupons to be automatically approved?',
            'multiOptions' => array(
                1 => 'Yes, automatically approve Coupon.',
                0 => 'No, site admin approval will be required for all Coupon.'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.couponapproval', 1),
        ));

        $this->addElement('Radio', 'siteeventticket_couponprivate', array(
            'label' => 'Make Coupons as Private / Public',
            'description' => 'Do you want to use coupon as private?',
            'multiOptions' => array(
                1 => 'Yes, Use as Private.',
                0 => 'No, Use as Public.'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.couponprivate', 0),
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
