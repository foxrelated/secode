<?php
class Groupbuy_Form_Email extends Engine_Form
{

  public function init()
  {
  	$this
      ->setAttribs(array(
        'id' => 'subscription_email_form',
        'class' => 'global_form_box',
      	'style' => 'margin-bottom: 15px'
      ))
	  ->setDescription("Verify your email address and get deals every day! \r\n");
      
  	$this->addElement('Text', 'email', array(
      'label' => 'Email Address',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
      ),
    ));	
	
	$this->addElement('Select', 'category_id', array(
      'label' => 'Category',
      'multiOptions' => Engine_Api::_()->getDbTable('categories','groupbuy')->getMultiOptions('..'),
      
    ));
	
	$this->addElement('Select', 'location_id', array(
      'label' => 'Location',
      'multiOptions' => Engine_Api::_()->getDbTable('locations','groupbuy')->getMultiOptions('..'),
    ));

      $this -> addElement('Text', 'address', array(
          'label' => 'Address',
          'decorators' => array( array(
              'ViewScript',
              array(
                  'viewScript' => '_location_subscribe.tpl',
              )
          )),
      ));

      $this -> addElement('Text', 'within', array(
          'label' => 'Radius (mile)',
          'placeholder' => Zend_Registry::get('Zend_Translate')->_('Radius (mile)..'),
          'maxlength' => '60',
          'required' => false,
          'style' => "display: block",
          'validators' => array(
              array(
                  'Int',
                  true
              ),
              new Engine_Validate_AtLeast(0),
          ),
      ));

      $this -> addElement('hidden', 'slat', array(
          'value' => '0',
          'order' => '98'
      ));

      $this -> addElement('hidden', 'slong', array(
          'value' => '0',
          'order' => '99'
      ));
	
	 $this->addElement('text', 'age', array(
      'label' => 'Age',
    ));
			
	

    $this->addElement('Button', 'subscribe', array(
      'label' => 'Subscribe',
      'onclick' => 'en4.groupbuy.subscribeEmail()',
      'ignore' => true,
    ));
   $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'groupbuy','controller'=>'subscription','action' => 'subscribe-widget'), 'default'));
  }
}