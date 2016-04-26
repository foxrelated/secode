<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Style.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Form_Style extends Engine_Form {

  public function init() {
    $this
        ->setTitle('Edit Listing Style')
         ->setDescription('Edit the CSS style of your listing using the text area below, and then click "Save Style" to save changes.')
        ->setMethod('post')
        ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Textarea', 'style', array(
            'label' => 'Custom Advanced Listing Style',
            'description' => 'Add your own CSS code above to give your listing a more personalized look.'
    ));
    $this->style->getDecorator('Description')->setOption('placement', 'APPEND');

    $this->addElement('Button', 'submit', array(
            'label' => 'Save Style',
            'type' => 'submit',
    ));
  }
}