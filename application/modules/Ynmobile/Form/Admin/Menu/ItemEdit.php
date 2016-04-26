<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: ItemEdit.php minhnc $
 * @author     MinhNC
 */

/**
 * Class Ynmobile_Form_Admin_Menu_ItemEdit
 */
class Ynmobile_Form_Admin_Menu_ItemEdit extends Engine_Form
{
  public function init()
  {
      $this->setTitle('Edit Menu Item')
          ->setAttrib('class', 'global_form_popup');

      $this -> addElement('Text', 'label', array(
          'label' => 'Label',
          'required' => true,
          'allowEmpty' => false,
      ));

      $this -> addElement('Text', 'name', array(
          'label' => 'Name',
          'required' => true,
          'allowEmpty' => false,
      ));

      $this -> addElement('Text', 'layout', array(
          'label' => 'Layout',
          'required' => true,
          'allowEmpty' => false,
      ));

      $this -> addElement('Text', 'uri', array(
          'label' => 'URL',
          'required' => true,
          'allowEmpty' => false,
          'style' => 'width: 500px',
      ));

      $this -> addElement('Text', 'icon', array(
          'label' => 'Icon',
      ));

      $this -> addElement('Checkbox', 'enabled', array(
          'label' => 'Enabled?',
          'checkedValue' => '1',
          'uncheckedValue' => '0',
          'value' => '1',
      ));

      // Buttons
      $this -> addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array('ViewHelper')
      ));

      $this -> addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'link' => true,
          'prependText' => ' or ',
          'href' => '',
          'onclick' => 'parent.Smoothbox.close();',
          'decorators' => array('ViewHelper')
      ));

      $this -> addDisplayGroup(array(
          'submit',
          'cancel'
      ), 'buttons');


  }
}