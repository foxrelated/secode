<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Level.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_Form_Admin_Level extends Engine_Form
{
  public function init()
  {
  	//Member Level Settings
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");
		$this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

    // prepare user levels
    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
    $select = $table->select();
    $user_levels = $table->fetchAll($select);
    
    foreach ($user_levels as $user_level){
      $levels_prepared[$user_level->level_id]= $user_level->getTitle();
    }
    
    // Member Level
    $this->addElement('Select', 'level_id', array(
      'label' => 'Member Level',
      'multiOptions' => $levels_prepared,
      'onchange' => 'javascript:fetchLevelSettings(this.value);',
      'ignore' => true
    ));
    
    // Send Poke
    $this->addElement('Radio', 'send', array(
      'label' => 'Allow Poking',
      'description' => 'Do you want to allow members of this member level to poke others?',
      'multiOptions' => array(
		    1=> 'Yes',
		    0=> 'No'
      ),
      'value' => 1,
      'onclick' => 'showoptions(this.value)'
    ));
    
	  // Element: auth_view
    $this->addElement('Radio', 'auth_view', array(
        'label' => 'Poke Options',
        'description' => 'Who should the members of this member level be allowed to poke?',
        'multiOptions' => array(
          'everyone'          => 'All Registered Members',
          'friend_networks'       => 'Friends and Networks',
          'mutual_friends' => 'Friends and Friends of Friends',
          'owner_member'        => 'Friends Only',
         ),
        'value' => array('everyone', 'friend_networks', 'mutual_friends', 'owner_member'),
      )); 
	    
	  //Submit   
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));
	}
}
?>