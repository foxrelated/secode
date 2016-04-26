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
class Facebookse_Form_Admin_Module extends Engine_Form {

    public function init() {

    $this
      ->setTitle('Add New Module for Advanced Facebook Integration')
      ->setDescription("Use the form below to configure content from a module of your site to be integrated with Facebook's Like Button and Comments Social Plugins, thus enabling users to Like and Comment on its content using Facebook's Social Plugins. Start by selecting a content module, and then entering the various database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.");

		$notInclude = array('activity', 'advancedactivity', 'sitealbum', 'sitelike', 'sitepageoffer', 'sitepagebadge','featuredcontent','sitepagediscussion', 'sitepagelikebox', 'mobi', 'advancedslideshow', 'birthday', 'birthdayemail', 'communityad', 'dbbackup', 'facebookse', 'facebooksefeed', 'facebooksepage', 'feedback', 'groupdocument', 'grouppoll', 'mapprofiletypelevel', 'mcard','poke', 'sitealbum', 'sitepageinvite', 'siteslideshow', 'socialengineaddon','seaocore', 'suggestion', 'userconnection', 'sitepageform', 'sitepageadmincontact','sitebusinessbadge','sitebusinessoffer','sitebusinessdiscussion','sitebusinesslikebox','sitebusinessinvite','sitebusinessform','sitebusinessadmincontact', 'siteestore', 'sitemailtemplate', 'sitetagcheckin', 'sitepagereport' ,'sitegroupbadge','sitegroupoffer','sitegroupdiscussion','sitegrouplikebox','sitegroupinvite','sitegroupform','sitegroupadmincontact', 'siteeventinvite','winkgreeting', 'younet-core', 'sitemobile','spamcontrol', 'sitemailtemplates','sepcore','replyrate','communityadsponsored', 'autonline', 'sitegroupmember', 'sitegroupurl', 'sitemailtemplates','sitepagemember', 'sitepageurl');


		$newArray = array('album', 'blog',  'document',   'list', 'group', 'music', 'recipe', 'user', 'sitepage', 'sitepagenote', 'classified', 'event', 'poll', 'forum','video', 'sitepagevideo','sitepagepoll', 'sitepagemusic','sitepagealbum','sitepageevent', 'sitepagereview', 'sitepagedocument', 'sitebusiness', 'sitebusinessalbum', 'sitebusinessdocument', 'sitebusinessevent', 'sitebusinessnote', 'sitebusinesspoll', 'sitebusinessmusic', 'sitebusinessvideo', 'sitebusinessreview', 'sitegroup', 'sitegroupalbum', 'sitegroupdocument', 'sitegroupevent', 'sitegroupnote', 'sitegrouppoll', 'sitegroupmusic', 'sitegroupvideo', 'sitegroupreview','siteevent', 'siteeventdocument');
    $newArray['user']= 'User Profile';
		$newArray['home']= 'Site Homepage';

		$finalArray = array_merge($notInclude, $newArray);

    $module_table = Engine_Api::_()->getDbTable('modules', 'core');
    $module_name = $module_table->info('name');
    $select = $module_table->select()
            ->from($module_name, array('name', 'title'))
            ->where($module_name . '.type =?', 'extra')
            ->where($module_name . '.name not in(?)', $finalArray)
					//	->where($module_name . '.name not in(?)', $getArray)
            ->where($module_name . '.enabled =?', 1);

    $contentModuloe = $select->query()->fetchAll(); 
    $contentModuloeArray = array();

		if( !empty($contentModuloe) ) {
			$contentModuloeArray[] = '';
			foreach ($contentModuloe as $modules) {
				$modules['title'] = $modules['title'] . ' ';
        $contentModuloeArray[$modules['name']] = $modules['title'];
			}
		}

		if( !empty($contentModuloeArray) ) {
				$this->addElement('Select', 'module', array(
						'label' => 'Content Module',
						'allowEmpty' => false,
						'onchange' => 'setModuleName(this.value)',
						'multiOptions' => $contentModuloeArray,
				));
		} else {
			//VALUE FOR LOGO PREVIEW.
			$description = "<div class='tip'><span>" . Zend_Registry::get( 'Zend_Translate' )->_( "There are currently no new modules to be added to ‘Manage Module’ section." ) . "</span></div>" ;
					$this->addElement( 'Dummy' , 'module' , array (
						'description' => $description ,
					)) ;
			$this->module->addDecorator( 'Description' , array ( 'placement' => Zend_Form_Decorator_Abstract::PREPEND , 'escape' => false ) ) ;

		}

    $module = Zend_Controller_Front::getInstance()->getRequest()->getParam('module_name', null);
    $contentItem = array();
    if (!empty($module)) {
      $this->module->setValue($module);
      $contentItem = $this->getContentItem($module);
      if (empty($contentItem))
        $this->addElement('Dummy', 'dummy_title', array(
            'description' => 'For this module, there is  no item defined in the manifest file.',

        ));
    }
    if (!empty ($contentItem)) {
      $this->addElement('Select', 'resource_type', array(
          'label' => 'Database Table Item',
          'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
        //  'required' => true,
          'multiOptions' => $contentItem,
      ));
      
		//ELEMENT PACKAGE TITLE
    $this->addElement('Text', 'owner_field', array(
			'label' => 'Content Owner Field in Table',
			'description' => 'From the above selected database table item, please enter the column / field name for the content owner. Ex: owner_id or user_id',
			'required' => true,
			'allowEmpty' => FALSE,
			'validators' => array(
					array('NotEmpty', true),
			)
    ));
    $this->owner_field->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
    
    //ELEMENT PACKAGE TITLE
    $this->addElement('Text', 'module_title', array(
				'label' => 'Content Title Field in Table',
				'description' => 'From the above selected database table item, please enter the column / field name for the content title. Ex: title',
				'required' => true,
        'allowEmpty' => FALSE,
        'validators' => array(
            array('NotEmpty', true),
        )
    ));
		$this->module_title->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

    //ELEMENT PACKAGE TITLE
    $this->addElement('Text', 'module_description', array(
				'label' => 'Content Body/Description Field in Table',
				'required' => true,
				'description' => 'From the above selected database table, please enter the column / field name for the content owner. Ex: body or description',
        'allowEmpty' => FALSE,
        'validators' => array(
            array('NotEmpty', true),
        )
    ));
    $this->module_description->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
    
   

    //ELEMENT PACKAGE TITLE
    $this->addElement('Text', 'module_name', array(
				'label' => 'Module Title',				
				'description' => "Enter the title of this module which will be displayed in the 'Module Title' field in the 'Manage Module' section. This name is only for your indicative purpose, and will not be displayed to users."
    ));

    
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
              ->from($activitytypemodule_name, array('type'))
              ->where($activitytypemodule_name . '.module =?', $module);
     
      $modules_ActivityTypes = $select->query()->fetchAll();
      
      if (count($modules_ActivityTypes) > 0) { 
		  
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
    		 $modules_ActivityTypes_array[0] = '';
    		 foreach ($modules_ActivityTypes as $value) {
    		   $modules_ActivityTypes_array[$value['type']] = $value['type'];
    		 }
    		 $this->addElement('Select', 'activityfeed_type', array(
    				'label' => 'Choose Activity Type',
    				'multiOptions' => $modules_ActivityTypes_array,
    				'onchange' => 'javascript:fetchtypeSettings(this);'    				
    		));
      }
      
      $variable = '{*actor*}, {*user_name*}, {*site_title*}, {*site_url*}, {*' . $module . '_title*}, {*' . $module . '_url*}, {*' . $module . '_desc*}, {*browse_' . $module . '_url*}';
      
     
      $this->addElement('Dummy', 'feed_var', array (
						'content' => $variable,
						'description' => 'You may use these variables / tokens in the fields below.'
		 ));
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

    // Element: execute
    $this->addElement('Button', 'execute', array(
        'label' => 'Save Settings',
        'type' => 'submit',
        'ignore' => true,
        'onclick' => 'javascript: return checkfeedvalidate();',
        'decorators' => array('ViewHelper'),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'prependText' => ' or ',
        'ignore' => true,
        'link' => true,
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
        'decorators' => array('ViewHelper'),
    ));
    } /*else {
      // Element: cancel
      $this->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'ignore' => true,
          'link' => true,
          'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
      ));
    }*/
  }

  public function getContentItem($moduleName) {

		$mixSettingsTable = Engine_Api::_()->getDbtable( 'mixsettings' , 'facebookse' );
		$mixSettingsTableName = $mixSettingsTable->info('name');
		$moduleArray = $mixSettingsTable->select()
                    ->from($mixSettingsTableName, "$mixSettingsTableName.resource_type")
                    ->where($mixSettingsTableName . '.module = ?', $moduleName)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);

    $file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
    $contentItem = array();
    if (@file_exists($file_path)) {
      $ret = include $file_path;
      if (isset($ret['items'])) {
       foreach ($ret['items'] as $item)
					if(!in_array($item , $moduleArray))
						$contentItem[$item] = $item . " ";
      }
    } 
    return $contentItem;
  }
}
