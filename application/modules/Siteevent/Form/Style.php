<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Style.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Style extends Engine_Form {

    public function init() {

        $this
                ->setTitle("Edit Event Style")
                ->setDescription("Edit the CSS style of your event using the text area below, and then click 'Save Style' to save changes.")
                ->setMethod('post')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        $this->addElement('Textarea', 'style', array(
            'label' => "Custom Advanced Events Style",
            'description' => "Add your own CSS code above to give your event a more personalized look."
        ));
        $this->style->getDecorator('Description')->setOption('placement', 'APPEND');

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Style',
            'type' => 'submit',
        ));
    }

}