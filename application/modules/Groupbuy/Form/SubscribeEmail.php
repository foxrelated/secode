<?php
class Groupbuy_Form_SubscribeEmail extends Engine_Form
{

  public function init()
  {
  	$this
      ->setAttribs(array(
        'id' => 'email_form',
        'class' => 'global_form_box',
      	'style' => 'margin-bottom: 15px'
      ));
      /*->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'module' => 'groubuy',
          'controller' => 'index',
          'action' => 'email',
        ), 'groupbuy_general'));*/
      
      
  	//$this->setTitle('Email Subscription');
  	$this->addElement('Text', 'emaildeal', array(
      'label' => 'Email Address',
      'description' => "Receive deals' news",
      'required' => true,
      'allowEmpty' => false,
      'value' => 'your email', 
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
      ),
    ));

     $this->addElement('Select', 'category_id', array(
      'label' => 'Location',
     'style' => 'width:160px',
      'multiOptions' => (array)Engine_Api::_()->getDbTable('locations','groupbuy')->getMultiOptions('..'),
      
    ));
    
        $this->addElement('Select', 'location_id', array(
      'label' => 'Category',
       'style' => 'width:160px',
      'multiOptions' => (array)Engine_Api::_()->getDbTable('categories','groupbuy')->getMultiOptions('..'),
      
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Subscribe',
      'onclick' => 'subscribeEmail()',
      'ignore' => true,
    ));
  //$this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'email'), 'groupbuy_general'));
  }
}
