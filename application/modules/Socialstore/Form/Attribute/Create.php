<?php 
class Socialstore_Form_Attribute_Create extends Engine_Form
{
  public function init()
  {
    $this->setMethod('POST')
      ->setAttrib('class', 'global_form_smoothbox')
      ->setTitle('Create Attribute');
  	$this->addElement('Hidden','set_id');
    // Add type
    $categories = Engine_Api::_()->fields()->getFieldInfo('categories');
    $types = Engine_Api::_()->fields()->getFieldInfo('fields');
    $fieldByCat = array();
    $temp = array();
    $availableTypes = array();
    foreach( $types as $fieldType => $info ) {
      $fieldByCat[$info['category']][$fieldType] = $info['label'];
    }
    foreach( $categories as $catType => $categoryInfo ) {
      $label = $categoryInfo['label'];
      if ($catType == 'generic') {
  	  	$temp[$catType]['text'] = 'Single-line Text Input';  
      	$temp[$catType]['select']	= 'Select Box';
	    $availableTypes[$label] = $temp[$catType];
      }
    }

    $this->addElement('Select', 'type', array(
      'label' => 'Attribute Type',
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => $availableTypes,
      /* 'multiOptions' => array(
        'text' => 'Text Field',
        'textarea' => 'Multi-line Textbox',
        'select' => 'Pull-down Select Box',
        'radio' => 'Radio Buttons',
        'checkbox' => 'Checkboxes',
        'date' => 'Date Field'
      ) */
     'onchange' => 'var form = this.getParent("form"); form.method = "get"; form.submit();',
    ));

    // Add label
    $this->addElement('Text', 'label', array(
      'label' => 'Attribute Label',
      'required' => true,
      'allowEmpty' => false,
    ));

    // Add description
    $this->addElement('Textarea', 'content', array(
      'label' => 'Content',
      'rows' => 6,
    ));

    // Add submit
    $this->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
      'order' => 10000,
      'ignore' => true,
    ));

    // Add cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'onclick' => 'parent.Smoothbox.close();',
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
      'order' => 10001,
      'ignore' => true,
    ));

    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
      'order' => 10002,
    ));
  }
}