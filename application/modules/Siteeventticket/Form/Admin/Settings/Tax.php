<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Tax.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Admin_Settings_Tax extends Engine_Form {

    public function init() {

        $this->setTitle('Tax Settings')
                ->setDescription('Here, you can configure the Tax related settings for your event tickets.

')
                ->setName('siteeventticket_tax_settings');

        $settings = Engine_Api::_()->getApi('settings', 'core');

        //VALUE FOR ENABLE/DISABLE TAX
        $this->addElement('Radio', 'siteeventticket_tax_enabled', array(
            'label' => 'Tax',
            'description' => 'Do you want to enable tax on tickets?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'javascript:showOtherElements(this.value)',
            'value' => $settings->getSetting('siteeventticket.tax.enabled', 0),
        ));

        $this->addElement('Radio', 'siteeventticket_tax_mandatory', array(
            'label' => 'Tax: Optional / Mandatory',
            'required' => true,
            'description' => 'How do you want to set tax while ticket creation?',
            'multiOptions' => array(
                1 => 'Mandatory, Event owner requires to add tax amount while ticket creation.',
                0 => 'Optional, Event owner may or may not add tax amount while ticket creation.'
            ),
            'value' => $settings->getSetting('siteeventticket.tax.mandatory', 0),
        ));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
