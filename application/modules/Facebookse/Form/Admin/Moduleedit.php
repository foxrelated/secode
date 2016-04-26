<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Module.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Facebookse_Form_Admin_Moduleedit extends Engine_Form {

  public function init() {

    $this
      ->setTitle('Edit Module for  Advanced Facebook Integration')
      ->setDescription('Use the form below to configure content from a module of your site to be integrated with Facebook\'s Like Button and Comments Social Plugins, thus enabling users to Like and Comment on its content using Facebook\'s Social Plugins. For the chosen content module, enter the various database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.');

    $mixsettingId = Zend_Controller_Front::getInstance()->getRequest()->getParam('mixsetting_id', null);
    $mixsettingsTable = Engine_Api::_()->getDbTable('mixsettings', 'facebookse');
		$mixsettingsTableResult = $mixsettingsTable->fetchRow(array('mixsetting_id = ?' => $mixsettingId));		
		$moduleame = array();
		$moduleame[] = $module = $mixsettingsTableResult->module;
		$resourceType[] = $mixsettingsTableResult->resource_type;
    $this->addElement('Select', 'module', array(
        'label' => 'Module Name',
        'allowEmpty' => false,
				'disable' => true,
        'multiOptions' => $moduleame,
    ));


		$this->addElement('Select', 'resource_type', array(
				'label' => 'Database Table Item',
				'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
				'disable' => true,
				'multiOptions' => $resourceType,
		));
		
		$required = true;
    $allowEmpty = FALSE;
    $Validator = true;
    if ($module == 'home') {
      $required = FALSE;
      $allowEmpty = true;
      $Validator = FALSE;
    }
    
		//ELEMENT PACKAGE TITLE
    $this->addElement('Text', 'owner_field', array(
			'label' => 'Content Owner Field in Table',
			'description' => 'From the above selected database table item, please enter the column / field name for the content owner. Ex: owner_id or user_id',
			'required' => $required,
			'allowEmpty' => $allowEmpty,
			'validators' => array(
					array('NotEmpty', $Validator),
			)
    ));
    $this->owner_field->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
    
    $required = true;
    $allowEmpty = FALSE;
    $Validator = true;
    if ($module == 'user' || $module == 'home') {
      $required = FALSE;
      $allowEmpty = true;
      $Validator = FALSE;
    }
    
    //ELEMENT PACKAGE TITLE
    $this->addElement('Text', 'module_title', array(
				'label' => 'Content Title Field in Table',
				'description' => 'From the above selected database table item, please enter the column / field name for the content title. Ex: title',
				'required' => $required,
        'allowEmpty' => $allowEmpty,
        'validators' => array(
            array('NotEmpty', $Validator),
        )
    ));
		$this->module_title->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

    //ELEMENT PACKAGE TITLE
    $this->addElement('Text', 'module_description', array(
				'label' => 'Content Body / Description Field in Table',
				'required' => $required,
				'description' => 'From the above selected database table, please enter the column / field name for the content owner. Ex: body or description',
        'allowEmpty' => $allowEmpty,
        'validators' => array(
            array('NotEmpty', $Validator),
        )
    ));
    $this->module_description->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
    
     

    //ELEMENT PACKAGE TITLE
    $this->addElement('Text', 'module_name', array(
				'label' => 'The Title which you want to give this module type',				
				'description' => ''        
    ));
    //$this->module_name->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
    
		$this->addElement('Checkbox', 'module_enable', array(
				'description' => 'Enable Module for Advanced Facebook Integration',
				'label' => 'Make content from this module available for placing (1) Facebook Like Button and (2) Facebook Comments Box. You can enable these 2 for this content from their respective interfaces.',
				'value' => 1
		));
		
		//CHECK IF FACEBOOK FEED PUBLISHER PLUGIN IS THERE THEN WE WILL ALSO GIVE THE SETTINGS CORROSPONDING TO FEED PUBLISHER.
		
		$enable_fbfeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
		
		if ($enable_fbfeedmodule) {  
		  
		  //NOW FETCH ALL THE ACTIVITY FEED TYPES FOR THE CURRENT MODULE:
		  
		  $activitymodule_table = Engine_Api::_()->getDbTable('actionTypes', 'activity');
      $activitytypemodule_name = $activitymodule_table->info('name');
      $select = $activitymodule_table->select()
              ->from($activitytypemodule_name, array('type'));
      
      if ($module == 'home') {
          $module = 'user';
          
          $select->where($activitytypemodule_name . '.module =?', $module)
                 ->where($activitytypemodule_name . '.type =?', 'signup');
          
      }
      else if ($module == 'user') {
        $select->where($activitytypemodule_name . '.module =?', $module)
                 ->where($activitytypemodule_name . '.type =?', 'profile_photo_update');
        
      }
      else 
        $select->where($activitytypemodule_name . '.module =?', $module);
                      
      $modules_ActivityTypes = $select->query()->fetchAll();
   
      
      if (count($modules_ActivityTypes) > 0 ) { 
		  
    		 $this->addElement('Radio', 'streampublishenable', array(
    				'label' => 'Enable Facebook Feeds',
    				'description' => 'Do you want Facebook Feeds Publishing to be enabled for this action? (If yes, then users will be able to choose if they want Facebook Feeds to be published for this action, and whether the feed publishing should be automatic, or a dialog prompt should be shown for it.)',
    				'multiOptions' => array(
    					'1' => 'Yes',
    					'0'=> 'No'	
    				),
    				'value' => 0,
    				'onclick' => 'javascript:show_hideform(this);'
    				
    				));
    				
    				
    				$description =  Zend_Registry::get('Zend_Translate')->_("This plugin enables your users to publish their actions on your site onto their Facebook streams. The Feed Stories are published on the Facebook Homepages of the users and their friends, as well as on the users' Facebook Profiles. The feed stories contain appropriate media like group photo, album photo, music, video, etc. Action links in the feed stories can attract traffic to your site.<br /><br />Below, you can configure the content of the Facebook Feed stories for various actions. Every story is built up of static text and variables/tokens, where the variables are replaced by the respective actual values before publishing. The common variables available to all feed stories are:<br /> {*actor*}: User's Facebook name<br />{*user_name*}: User's name on site<br />{*site_title*}: Site's Title [%1s]<br />{*site_url*}: Site's URL [%2s]<br />");
    				
    				$this->addElement('Dummy', 'show_manual', array(
              'content' => $description,
        			
            ));
    		 
    		 //NOW FETCH ALL THE ACTIVITY FEED TYPES FOR THE CURRENT MODULE:
          if ($mixsettingsTableResult->activityfeed_type) {  
    		 $modules_ActivityTypes_array[$mixsettingsTableResult->activityfeed_type] = $mixsettingsTableResult->activityfeed_type;
           $disabled_chooseactivitytpe = true;
          }
          else {
            //NOW FETCH ALL THE ACTIVITY FEED TYPES FOR THE CURRENT MODULE:
             $modules_ActivityTypes_array[0] = '';
             foreach ($modules_ActivityTypes as $value) {
               $modules_ActivityTypes_array[$value['type']] = $value['type'];
             }
             $disabled_chooseactivitytpe = false;
          }
//    		 foreach ($modules_ActivityTypes as $value) {
//    		   $modules_ActivityTypes_array[$value['type']] = $value['type'];
//    		 }
    		 $this->addElement('Select', 'activityfeed_type', array(
    				'label' => 'Choose Activity Type',
    				'multiOptions' => $modules_ActivityTypes_array,
    				'disable' => $disabled_chooseactivitytpe,
            'onchange' => 'javascript:fetchtypeSettings(this);'             
    		));
        $plugins_temp = str_replace('sitepage', "", $module);
        if ($plugins_temp == $module) { 
          $plugins_temp = str_replace('sitebusiness', "", $module);
          if ($plugins_temp == $module) {            
             $plugins_temp = str_replace('siteevent', "", $module);
             if ($plugins_temp == $module) {
               $plugins_prefix = 'sitegroup';
               $plugin_var1 = 'sitegroups';
             }
             else {
               $plugins_prefix = 'siteevent';
               $plugin_var1 = 'siteevents';
             }             
          }
          else {
            $plugins_prefix = 'sitebusiness';
            $plugin_var1 = 'sitebusinesses';
          }
        }  
        else {
          $plugins_prefix = 'sitepage';
          $plugin_var1 = 'sitepages';
        }  
        
        if ($mixsettingsTableResult->activityfeed_type == 'signup') {
          $module = 'home';
        }
        switch ($module) { 
          
          case 'forum':
  				  $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*forum_title*}, {*forum_url*}, {*forum_desc*}, {*browse_forums_url*}, {*forumtopic_title*}, {*forumtopic_url*}, {*forumtopic_desc*}, {*browse_forumtopic_url*}';
  				  break;
  				case 'user':
  				  $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*profile_url*}';
  				  break;
  				  
  				case 'home':
  				   $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*site_description*}, {*signup_page*}';
  				  break;
  				  
  				case 'list':
  				   $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*listing_title*}, {*listing_url*}, {*listing_desc*}, {*browse_listing_url*}';
  				  break;
  				  
  				case ''. $plugins_prefix :
				  $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*'. $plugins_prefix .'_title*}, {*'. $plugins_prefix .'_url*}, {*'. $plugins_prefix .'_desc*}, {*'. $plugin_var1 .'_home_url*}';
				  break;
				  
				case ''. $plugins_prefix .'event':
				  $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*'. $plugins_prefix .'event_title*}, {*'. $plugins_prefix .'event_url*}, {*'. $plugins_prefix .'event_desc*}, {*browse_'. $plugins_prefix .'events_url*}';
				  break;
				  
				case ''. $plugins_prefix .'note':
				  $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*'. $plugins_prefix .'note_title*}, {*'. $plugins_prefix .'note_url*}, {*'. $plugins_prefix .'note_desc*}, {*browse_'. $plugins_prefix .'notes_url*}';
				  break;

				case ''. $plugins_prefix .'review':
				  $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*'. $plugins_prefix .'review_title*}, {*'. $plugins_prefix .'review_url*}, {*'. $plugins_prefix .'review_desc*}, {*browse_'. $plugins_prefix .'reviews_url*}';
				  break;

				case ''. $plugins_prefix .'album':
				  $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*'. $plugins_prefix .'album_title*}, {*'. $plugins_prefix .'album_url*}, {*'. $plugins_prefix .'album_desc*}, {*browse_'. $plugins_prefix .'albums_url*}';
				  break;

				case ''. $plugins_prefix .'video':
				  $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*'. $plugins_prefix .'video_title*}, {*'. $plugins_prefix .'video_url*}, {*'. $plugins_prefix .'video_desc*}, {*browse_'. $plugins_prefix .'videos_url*}';
				  break;

				case ''. $plugins_prefix .'poll':
				  $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*'. $plugins_prefix .'poll_title*}, {*'. $plugins_prefix .'poll_url*}, {*'. $plugins_prefix .'poll_desc*}, {*browse_'. $plugins_prefix .'polls_url*}';
				  break;

				case ''. $plugins_prefix .'discussion':
				  $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*'. $plugins_prefix .'discussion_title*}, {*'. $plugins_prefix .'discussion_url*}, {*'. $plugins_prefix .'discussion_desc*}, {*browse_'. $plugins_prefix .'discussions_url*}';
				  break;

				case ''. $plugins_prefix .'discussion':
				  $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*'. $plugins_prefix .'discussion_title*}, {*'. $plugins_prefix .'discussion_url*}, {*'. $plugins_prefix .'discussion_desc*}, {*browse_'. $plugins_prefix .'discussions_url*}';
				  break;       
  			  
          default:
             $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*' . $module . '_title*}, {*' . $module . '_url*}, {*' . $module . '_desc*}, {*browse_' . $module . '_url*}';
          
        }
        
        $this->addElement('Dummy', 'feed_var', array (
  						'content' => $variable,
  						'description' => 'You may use these variables / tokens in the fields below.'
  		 ));
        
        
        //IF THERE ARE NO VALUSE IN THE FIELDS THEN FILL WITH THE DEFAULT VALUSE.
        
        if (empty($mixsettingsTableResult->activityfeed_type)) {
              if ($module[0] === 'a' || $module[0] === 'o' || $module[0] === 'e' || $module[0] === 'i' || $module[0] === 'u') {
              $actiontype_text = 'Creating an ' . ucfirst($module);
              $caption = '{*actor*} created an '. ucfirst($module) .' on {*site_title*}: {*site_url*}.';
         }
         else {
           $actiontype_text = 'Creating a ' . ucfirst($module);
           $caption = '{*actor*} created a '. ucfirst($module) .' on {*site_title*}: {*site_url*}.';
         }    




         $this->addElement('Text', 'activityfeedtype_text', array(
              'label' => 'Choose Action Text',
              'description' => 'This will be the text which will show you in the drop-down of "Choose Action" field for this module at the "Global Setting page" of Facebook Feed Publisher Page.',
              'value' => $actiontype_text,
              'style' => 'width:300px;',
        ));

         $this->addElement('Text', 'streampublish_message', array(
              'label' => 'Message',
              'description' => 'This is the message to prefill the text field that the user will type in.',
              'value' => 'View my ' . ucfirst($module) . '!',
              'style' => 'width:300px;',
          ));

          $this->addElement('Text', 'streampublish_story_title', array(
              'label' => 'Story Title',
              'description' => 'This is the main title of the Facebook Feed Story. It appears in the story as a link pointing to the URL that you can specify in the Link field below.',
              'value' => '{*' . $module . '_title*}',
              'style' => 'width:300px;',
          ));

          $this->addElement('Text', 'streampublish_link', array(
              'label' => 'Link',
              'description' => 'This is the link URL of the Story Title attached to the feed post.',
              'value' => '{*' . $module . '_url*}',
              'style' => 'width:300px;',
          ));

          $this->addElement('Text', 'streampublish_caption', array(
              'label' => 'Caption',
              'description' => 'This is the caption of the Story Title link (appears beneath the title).',
              'value' => $caption,
              'style' => 'width:300px;',
          ));

          $this->addElement('Text', 'streampublish_description', array(
              'label' => 'Description',
              'description' => 'This is the description of the Feed story (appears beneath the story caption).',
              'value' => '{*' . $module . '_desc*}',
              'style' => 'width:300px;',
          ));

          $this->addElement('Text', 'streampublish_action_link_text', array(
              'label' => 'Action Link Text',
              'description' => 'This is the text of the action link that will appear next to the "Comment" and "Like" link under the Feed Story posts.',
              'value' => 'View ' . ucfirst($module),
              'style' => 'width:300px;',
          ));

          $this->addElement('Text', 'streampublish_action_link_url', array(
              'label' => 'Action Link URL',
              'description' => 'This is the URL of the action link that will appear next to the "Comment" and "Like" link under the Feed Story posts.',
              'value' => '{*' . $module . '_url*}',
              'style' => 'width:300px;',
          ));
          
        }
        else {  
          $this->addElement('Text', 'activityfeedtype_text', array(
                  'label' => 'Choose Action Text',
                  'description' => 'This will be the text which will show you in the drop-down of "Choose Action" field for this module at the "Global Setting page" of Facebook Feed Publisher Page.',
                  'value' => '',
                  'style' => 'width:300px;',
            ));

             $this->addElement('Text', 'streampublish_message', array(
                  'label' => 'Message',
                  'description' => 'This is the message to prefill the text field that the user will type in.',
                  'value' => '',
                  'style' => 'width:300px;',
              ));

              $this->addElement('Text', 'streampublish_story_title', array(
                  'label' => 'Story Title',
                  'description' => 'This is the main title of the Facebook Feed Story. It appears in the story as a link pointing to the URL that you can specify in the Link field below.',
                  'value' => '',
                  'style' => 'width:300px;',
              ));

              $this->addElement('Text', 'streampublish_link', array(
                  'label' => 'Link',
                  'description' => 'This is the link URL of the Story Title attached to the feed post.',
                  'value' => '',
                  'style' => 'width:300px;',
              ));

              $this->addElement('Text', 'streampublish_caption', array(
                  'label' => 'Caption',
                  'description' => 'This is the caption of the Story Title link (appears beneath the title).',
                  'value' => '',
                  'style' => 'width:300px;',
              ));

              $this->addElement('Text', 'streampublish_description', array(
                  'label' => 'Description',
                  'description' => 'This is the description of the Feed story (appears beneath the story caption).',
                  'value' => '',
                  'style' => 'width:300px;',
              ));

              $this->addElement('Text', 'streampublish_action_link_text', array(
                  'label' => 'Action Link Text',
                  'description' => 'This is the text of the action link that will appear next to the "Comment" and "Like" link under the Feed Story posts.',
                  'value' => '',
                  'style' => 'width:300px;',
              ));

              $this->addElement('Text', 'streampublish_action_link_url', array(
                  'label' => 'Action Link URL',
                  'description' => 'This is the URL of the action link that will appear next to the "Comment" and "Like" link under the Feed Story posts.',
                  'value' => '',
                  'style' => 'width:300px;',
              ));
        }
		 
		  }
		  
		}
		
		
    $this->addElement('Button', 'submit', array(
            'label' => 'Save Settings',
            'type' => 'submit',
            'onclick' => 'javascript: return checkfeedvalidate();',
            'ignore' => true
    ));
  }
}
