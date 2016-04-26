<?php
class Socialstore_Form_PublishStore extends Engine_Form
{
  public $_error = array();

  public function init()
  {   
    $this->setTitle('Publish Store')
      ->setDescription('STORE_FORM_PUBLISH_STORE_DESCRIPTION');
  	$user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $publish_fee = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_store', $user, 'store_pubfee');
    $feature_fee = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_store', $user, 'store_ftedfee');
    $view =  Zend_Registry::get('Zend_View');
        
    $this->addElement('dummy', 'store_publish_fee',array(
      'label'=>'Fee for publishing',
      'description' => $publish_fee?$view->currency($publish_fee):'Free',
    ));
	$this->store_publish_fee->getDecorator("Description")->setOption("placement", "append")->setEscape(false);
    
	$this->addElement('dummy', 'store_feature_fee',array(
      'label'=>'Fee for featuring',
      'description' => $feature_fee? $view->currency($feature_fee):'Free',
    ));
	$this->store_feature_fee->getDecorator("Description")->setOption("placement", "append")->setEscape(false);
	$translate = Zend_Registry::get('Zend_Translate');
    $this->addElement('Radio', 'publish_option', array(
        'label' => 'How do you wish to publish your store?',
        'multiOptions' => array(
          '0' => $translate->translate('Publish with no featured option').': '.'<strong>'.$view->currency($publish_fee).'</strong>',
          '1'=> $translate->translate('Publish with featured option').': '.'<strong>'.$view->currency($publish_fee + $feature_fee).'</strong>'
        ),
        'escape'=>false,
        'value'=>'0',
      ));
    
    $this->addElement('Button', 'execute', array(
      'label' => 'Publish Store',
      'type' => 'button',
      'onclick' => 'this.form.submit(); removeSubmit()',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'socialstore_mystore_general', true),
      'onclick' => '',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
     // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'execute',
    	'cancel',
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }
}