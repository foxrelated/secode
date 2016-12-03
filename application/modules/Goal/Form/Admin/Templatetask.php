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

class Goal_Form_Admin_Templatetask extends Engine_Form
{
  protected $_field;

  public function init()
  {
    $this->setMethod('post');

    $label = new Zend_Form_Element_Text('label');
    $label->setLabel('Task Name')
      ->addValidator('NotEmpty')
      ->setRequired(true)
      ->setAttrib('class', 'text');


    $id = new Zend_Form_Element_Hidden('id');


    $this->addElements(array(
      $label,
      $id
    ));
    
    //Task Completion days
    // Durations
    $this->addElement('Text', 'duration', array(
      'label' => 'Duration (Days)',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
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
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Goal Template Task',
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
    $this->duration->setValue($template->duration);
     $this->description->setValue($template->description);
    $this->submit->setLabel('Edit Goal Template');
    
  }
}