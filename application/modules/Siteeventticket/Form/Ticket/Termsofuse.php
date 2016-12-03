<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Termofuse.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Ticket_Termsofuse extends Engine_Form {

    public function init() {

        $this
                ->setTitle("Edit Terms & Conditions")
                ->setMethod('post')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        $this->addElement('Textarea', 'terms_of_use', array(
            'label' => "Terms & Conditions",
            'description' => "Below, you can add Terms and Conditions for your event. This information will get printed on your event tickets and sent with the ticket PDF via email. Event's overview will be printed and sent incase you have not defined anything in the Terms and Conditions block below. This can be useful to send some information about your event to ticket buyers.<br/>[Note: Only 700 characters will get printed on your event tickets.]"
        ));
        $this->terms_of_use->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
        ));
    }

}
