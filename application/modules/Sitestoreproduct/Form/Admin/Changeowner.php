<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Changeowner.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Admin_Changeowner extends Engine_Form {

  public function init() {

    $this->setMethod('post');
    $this->setTitle("Change Owner")
            ->setDescription('Select an owner for this product from the auto-suggest field given below and then click on "Save Changes" to save it.');

    $label = new Zend_Form_Element_Text('title');
    $label->setLabel('Owner Name')
            ->addValidator('NotEmpty')
            ->setRequired(true)
            ->setAttrib('class', 'text')
            ->setAttrib('style', 'width:250px;');

    $this->addElement('Hidden', 'user_id', array());
    $this->addElements(array(
        $label,
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'onclick' => 'return check_submit();',
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',        
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}