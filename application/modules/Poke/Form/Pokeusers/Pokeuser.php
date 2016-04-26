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
class Poke_Form_Pokeusers_Pokeuser extends Engine_Form
{
  public function init()
  {
  	//Getting the poke user id.Means profile owner id.
    $pokeuser_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('pokeuser_id', null);
    //Getting the allinformaiton of the owner.
    $subject = Engine_Api::_()->getItem('user', $pokeuser_id);
    
    //Making view for getting the profile owner photo.
   	$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $userphoto = $view->itemPhoto($subject, 'thumb.profile', $subject->getTitle());
    
    //Getting the allinformaiton of the owner.
    $user = Engine_Api::_()->getItem('user', $pokeuser_id); 
		$displayname = Zend_Registry::get('Zend_Translate')->_("Poke %s?");
		
		$displayname = sprintf($displayname, $subject->getTitle());    
    $description = Zend_Registry::get('Zend_Translate')->_("<div>%s You are about to poke %s.</div><div style='color: grey;'> %s will be informed of this on homepage.</div>");
    $description = sprintf($description, $userphoto, $subject->getTitle(), $subject->getTitle());    
		//Set Title
	  $this->setTitle($displayname)
    		 ;
    
		$this->addElement('Dummy', 'dummy', array(
	      'description' => $description,
					'decorators' => array(
					        'ViewHelper'),));

    
	  //Making a Poke submit button.
    $this->dummy->addDecorator('Description', array('tag' => 'div', 'class' => 'form-wrapper', 'placement' => 'PREPEND', 'escape' => false));
			$this->addElement('Button', 'submit', array(
      'label' => 'Poke',
      'type' => 'submit',
      'value' => 'submit',
			'ignore' => true,
      'decorators' => array(
				        'ViewHelper'),
    ));
    
    //Making a Poke cancel button.
    $this->addElement('Button', 'cancel', array(
      'label' => 'Cancel',
      'onclick' => 'closesmoothbox();',
     'decorators' => array(
				        'ViewHelper'),));
 		$this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper'); 
    
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