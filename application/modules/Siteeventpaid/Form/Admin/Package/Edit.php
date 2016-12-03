<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventpaid_Form_Admin_Package_Edit extends Siteeventpaid_Form_Admin_Package_Create {

    public function init() {
        parent::init();

        $this->setTitle('Edit Package')
                ->setDescription('Edit your event package over here. Below, you can configure various settings for this package like video, overview, etc. Please note that payment parameters (Price, Duration) cannot be edited after creation. If you wish to change these, you will have to create a new package and disable the existing one.');

        // Disable some elements
        $this->getElement('price')
                ->setIgnore(true)
                ->setAttrib('disable', true)
                ->clearValidators()
                ->setRequired(false)
                ->setAllowEmpty(true)
        ;

        $this->getElement('recurrence')
                ->setIgnore(true)
                ->setAttrib('disable', true)
                ->clearValidators()
                ->setRequired(false)
                ->setAllowEmpty(true)
        ;
        $this->getElement('duration')
                ->setIgnore(true)
                ->setAttrib('disable', true)
                ->clearValidators()
                ->setRequired(false)
                ->setAllowEmpty(true)
        ;
        $this->removeElement('trial_duration');

        // Change the submit label
        $this->getElement('execute')->setLabel('Edit Package');
    }

}
