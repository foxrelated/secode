<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Pokeuser.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_Form_Sitemobile_Pokeusers_Pokeuser extends Engine_Form
{
  public function init()
  {
  	//Getting the poke user id.Means profile owner id.
    $pokeuser_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('pokeuser_id', null);
    //Getting the allinformaiton of the owner.
    $subject = Engine_Api::_()->getItem('user', $pokeuser_id);
    
    //Making view for getting the profile owner photo.
   	$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $userphoto = $view->itemPhoto($subject, 'thumb.icon', $subject->getTitle());
    
    //Getting the allinformaiton of the owner.
    $user = Engine_Api::_()->getItem('user', $pokeuser_id); 
		$displayname = Zend_Registry::get('Zend_Translate')->_("Poke %s?");
		
		$displayname = sprintf($displayname, $subject->getTitle());    
    $description = Zend_Registry::get('Zend_Translate')->_("<div style='vertical-align:top;'>%s You are about to poke %s.</div>");
    $description = sprintf($description, $userphoto, $subject->getTitle(), $subject->getTitle());    
		//Set Title
	  $this->setTitle($displayname)
    		 ;
    
		$this->addElement('Dummy', 'dummy', array(
	      'description' => $description,
					'decorators' => array(
					        'ViewHelper')));

    
	  //Making a Poke submit button.
    $this->dummy->addDecorator('Description', array('tag' => 'div', 'class' => 'form-wrapper', 'placement' => 'PREPEND', 'escape' => false));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Poke',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'data-rel' => 'back',
      'prependText' => ' or ',
      'href' => '',
     // 'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');


    $this->addElement('Hidden', 'pokeuser_id', array(
    	'value' => $pokeuser_id,
      'order' => 10001,
    ));
    $this->addElement('Hidden', 'pokeback', array(
    	'value' => 0,
      'order' => 10002,
    ));
  }
}