<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Style.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Style extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Edit Group Style')
            ->setDescription('Edit the CSS style of your group using the text area below, and then click "Save Style" to save changes.')
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'editstyle');

    // Element: style
    $this->addElement('Textarea', 'style', array(
        'label' => 'Custom Advanced Group Style',
        'description' => 'Add your own CSS code above to give your group a more personalized look.'
    ));
    $this->style->getDecorator('Description')->setOption('placement', 'APPEND');
    // Element: submit
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Style',
        'type' => 'submit',
    ));
  }

}

?>