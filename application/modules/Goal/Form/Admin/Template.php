<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */

class Goal_Form_Admin_Template extends Engine_Form
{
  protected $_field;

  public function init()
  {
    $this->setMethod('post');

    $label = new Zend_Form_Element_Text('label');
    $label->setLabel('Goal Template Name')
      ->addValidator('NotEmpty')
      ->setRequired(true)
      ->setAttrib('class', 'text');


    $id = new Zend_Form_Element_Hidden('id');
  

    $this->addElements(array(
      $label,
      $id
    ));
    
    //description
      $this->addElement('TinyMce', 'description', array(
      'label' => 'Description',
      'required' => true,
          
      'editorOptions' => array(
        'bbcode' => true,
        'html' => true,
      ),
      'allowEmpty' => false,        
    ));
    // Photo
    $this->addElement('File', 'photo', array(
      'label' => 'Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Goal Template',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
    
    
  
    

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');

  }

  public function setField($template)
  {
    $this->_field = $template;

    // Set up elements
    $this->label->setValue($template->title);
    $this->id->setValue($template->template_id);
    $this->description->setValue($template->description);
    $this->submit->setLabel('Edit Goal Template');
    
  }
}