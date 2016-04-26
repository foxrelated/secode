<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Pokesettings.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_Form_Admin_Pokesettings extends Engine_Form
{
  public function init()
  {
  	//Set title for admin setting page.
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
	
    //Here we setting the admin want to send email to the member when somebody poke other member. 
    $this->addElement('Radio', 'poke_mailoption', array(
        'label' => 'Email Notifications',
        'description' => "Do you want email notifications to be sent to users who are poked?",
        'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->poke_mailoption,));

        
    //Here we setting the admin want to send email to the member when somebody poke other member. 
    $this->addElement('Radio', 'poke_updateoption', array(
        'label' => 'Updates',
        'description' => "Do you want notification updates to be created for users when they are poked?",
        'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->poke_updateoption,));    

        
        //Here we setting the admin want to send email to the member when somebody poke other member. 
     $this->addElement('Radio', 'poke_conn_setting', array(
        'label' => 'User Poke Settings',
        'description' => 'Do you want users to be able to choose whether they should be poked or not? (If yes, then users will see a "Poke Settings" link in the left menu of their home page.)',
        'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->poke_conn_setting,));   
           
    //Limit for Title turncation    
		$this->addElement('Text', 'poke_title_turncation', array(
		      'label' => 'Title Truncation Limit',
		      'description' => 'What limit should be applied to the number of characters in the titles of items in the widgets? (Complete titles will be shown on mouseover.)',
		        'allowEmpty' => false,
             'maxlength' => '3',
            'required' => true,
		      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('poke.title.turncation', 16),
		    ));        

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
?>