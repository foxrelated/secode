<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Mysettings.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Facebookse_Form_Mysettings extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Facebook Feed Stories')
      ->setDescription("Choose your settings for publishing newsfeed stories to Facebook")
      ->setAttrib('id',      'form-setting-fbfeed');
    $translate = Zend_Registry::get('Zend_Translate');
   //FETCHING THE SETTINGS OF ALL MODULE POST EITHER ADMIN HAS ENABLED THE MODULE POST OR NOT.IF NOT THEN WE WILL NOT SHOWO USER THAT MODULE POST OPTION IF SETTING TAB.
    $feedstype_array_temp_mine = $feedstype_array_mine = array('blog_new' => 'Creating a Blog-post', 'group_create' => 'Creating a Group' , 'event_create' => 'Creating an Event' , 'list_new' => 'Creating a Listing / Catalog item' , 'album_photo_new' => 'Creating an Album / Add Photo' , 'music_playlist_new' => 'Creating a Music Playlist' , 'video_new' => 'Creating a Video', 'classified_new' => 'Creating a Classified', 'poll_new' => 'Creating a Poll', 'document_new' => 'Creating a Document', 'profile_photo_update' => 'Uploading new Profile Photo', 'forum_topic_create' => 'Creating a Forum Topic', 'forum_topic_reply' => 'Replying to a Forum Topic', 'signup' => 'Linking to Facebook', 'sitepage_new' => 'Pages & It\'s Extensions' , 'recipe_new' => 'Creating a Recipe', 'sitebusiness_new' => 'Businesses & It\'s Extensions' , 'sitegroup_new' => 'Groups & It\'s Extensions', 'siteevent_new' => 'Events & It\'s Extensions');
    
    
    $feedtypes_array_temp = $feedtypes_array = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getModuleTypes();
    $plugins = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    
   foreach ($feedtypes_array_temp as $key => $feedtype) { 
     
     $module_temp = explode("_", $feedtype['activityfeed_type']);
     if ($module_temp[0] == 'sitereview') {
       $module_temp[0] = $feedtype['module_name'];
     }
     if (!array_key_exists($feedtype['activityfeed_type'], $feedstype_array_mine) && strstr($feedtype['activityfeed_type'], 'sitepage') === false && strstr($feedtype['activityfeed_type'], 'sitebusiness') === false && strstr($feedtype['activityfeed_type'], 'sitegroup') === false && strstr($feedtype['activityfeed_type'], 'siteevent') === false) { 
       
       $firstlatter = @$module_temp[0][0];
       if ($firstlatter === 'a' || $firstlatter === 'o' || $firstlatter === 'e' || $firstlatter === 'i' || $firstlatter === 'u') 
          $feedstype_array_mine [$feedtype['activityfeed_type']] = $translate->translate('Creating an') . ' ' .  ucfirst($module_temp[0]);
       else 
         $feedstype_array_mine [$feedtype['activityfeed_type']] = $translate->translate('Creating a') . ' ' . ucfirst($module_temp[0]);      
       
     }
     else if (!array_key_exists($feedtype['activityfeed_type'], $feedstype_array_mine) && (strstr($feedtype['activityfeed_type'], 'sitepage') !== false || strstr($feedtype['activityfeed_type'], 'sitebusiness') !== false || strstr($feedtype['activityfeed_type'], 'sitegroup') !== false || strstr($feedtype['activityfeed_type'], 'siteevent')) ) { 
       unset($feedstype_array_mine[$feedtype['activityfeed_type']]);  
     }
     if ($feedtype['module'] == 'sitereview')
       $module_temp[0] = $feedtype['module'];
     if (!in_array($module_temp[0], $plugins) && $feedtype['activityfeed_type'] != 'signup' && $feedtype['activityfeed_type'] != 'profile_photo_update' ) {
       
       unset($feedstype_array_mine[$feedtype['activityfeed_type']]);       
     }    
     
   }  
  
    $item_array = array();
    foreach ($feedtypes_array_temp as $item) { 
     if (empty($item['streampublishenable'])) {
       unset($feedstype_array_mine[$item['activityfeed_type']]);
       unset($feedstype_array_temp_mine[$item['activityfeed_type']]);
     }   
      
    }
    
    $show_autopublish_option = Engine_Api::_()->getApi('settings', 'core')->getSetting('show.autopublish', 1);
    if (!empty($show_autopublish_option)) {
    $Array_options = array( 
					'1' => 'Automatically',
					'2'=> 'Prompt me before publishing',
					'3' => 'Do not publish'	
				);
    }
    else {
      $Array_options = array( 
            '2'=> 'Prompt me before publishing',
            '3' => 'Do not publish'	
      );
    }  
  
 
   foreach ($feedstype_array_mine as $key => $feeddesc) { 
     $module_temp = explode("_", $key);
     if ($module_temp[0] == 'forum')  {
       $field = $module_temp[0].$module_temp[1] . '_' . $module_temp[2]. '_' . 'post';
     }
     else if ($module_temp[0] == 'sitereview')  {
       $field = $key;
     }
     else if ($key == 'signup') {
       $field = 'signup';
     }
     else if ($key == 'profile_photo_update') {
       
       $field = 'profile_update';
     }
     else if ($key == 'recipe_new' || $key == 'sitepage_new' || $key == 'sitebusiness_new' || $key == 'sitegroup_new' || $key == 'sitestore_new') {
       $field = $key;
     }
     else {
       $field = $module_temp[0] . '_post';
     }
     $this->addElement('Radio', $field, array(
				'label' => $feeddesc,
				'description' => "How do you want your Facebook newsfeed stories for this to be published?",
				'multiOptions' =>$Array_options,
				'value' => 2
					
			));
     
    }   
   
    
     $this->addElement('Hidden', 'feedsetting_id', array(
				'order' => 1001
			));

   // Add submit button
    $this->addElement('Button', 'submit_form', array(
      'label' => 'Save Settings',
      'type' => 'submit',     
    ));
  }
}