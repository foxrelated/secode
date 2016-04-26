<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Form_Admin_Global extends Engine_Form {

  public function init() {

    //GENERAL HEADING
    $this
            ->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'sitetagcheckin_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.lsettings'),
    ));
    
    if (APPLICATION_ENV == 'production') {
      $this->addElement('Checkbox', 'environment_mode', array(
          'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
          'description' => 'System Mode',
          'value' => 1,
      ));
    } else {
      $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
    }

    // Add submit button
    $this->addElement('Button', 'submit_lsetting', array(
        'label' => 'Activate Your Plugin Now',
        'type' => 'submit',
        'ignore' => true
    ));

    //if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('event')) {
			//VALUE FOR ENABLE /DISABLE Proximity Search IN Kilometer
			$this->addElement('Radio', 'sitetagcheckin_proximity_search_kilometer', array(
					'label' => 'Location & Proximity Search Metric',
					'description' => 'What metric do you want to be used for location & proximity Search Metric? (This will enable users to search for Events / Groups / Members within a certain distance from their current location or any particular location.)',
					'multiOptions' => array(
							0 => 'Miles',
							1 => 'Kilometers'
					),
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.proximity.search.kilometer', 0),
			));
    //}


		$this->addElement('Radio', 'sitetagcheckin_groupsettings', array(
				'label' => 'Groups Location & Proximity Search',
				'description' => "Do you want to enable location & proximity search for Groups on this site? (Selecting ‘Yes’ over here will enable members to add location to their Groups and search other Groups with location. This is a very useful feature if you want to have locations based Groups on your site. Users will be able to search for Groups within a certain distance from their current location or any particular location.)",
				'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
				),
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.groupsettings', 0),
		));
		
    if( !Engine_Api::_()->hasModuleBootstrap('sitemember')  ) {
		$this->addElement('Radio', 'sitetagcheckin_usersettings', array(
				'label' => 'Members Location & Proximity Search',
				'description' => 'Do you want to enable location & proximity search for Members on this site? (Selecting ‘Yes’ will enable "Edit My Location" link on Member Profile page and after entering the location, members can search other members having location associated with their profiles on "Browse Members’ Locations" page.',
				'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
				),
			  'onclick' => 'showUserSettingsOption(this.value)',
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.usersettings', 0),
		));
		
		$this->addElement('Radio', 'sitetagcheckin_userstatus', array(
				'label' => 'Members Status',
				'description' => 'Do you want to show Members status? (If you select "Yes" over here, then users will be shown an on-line )',
				'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
				),
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.userstatus', 0),
		));
		
	  $this->addElement('Radio', 'sitetagcheckin_mapshow', array(
				'label' => 'Show Map',
				'description' => 'Do you want to show Map on the "Browse by Location" page?',
				'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
				),
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.mapshow', 1),
		));
		
	  $this->addElement('Radio', 'sitetagcheckin_layouts_oder', array(
				'label' => 'Default View For Members Location',
				'description' => 'Select a default view type for Members Location.',
				'multiOptions' => array(
						0 => 'List View',
						1 => 'Image View'
				),
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.layouts.oder', 1),
		));

		
	  $this->addElement('Radio', 'sitetagcheckin_levelsettings', array(
				'label' => 'Member Levels based Members Search',
				'description' => 'Do you want to enable Members search based on Member Levels?',
				'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
				),
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.levelsettings', 0),
		));
		
	  $this->addElement('Radio', 'sitetagcheckin_networksettings', array(
				'label' => 'Networks based Members Search',
				'description' => 'Do you want to enable Members search based on Networks?',
				'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
				),
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.networksettings', 1),
		));

		$this->addElement( 'Text' , 'sitetagcheckin_memberlimit' , array (
			'label' => 'Browse Members Locations widget' ,
			'description' => 'How many members will be shown in the browse members locations widget?' ,
			'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('sitetagcheckin.memberlimit' , 15)
		));

		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$URL = $view->url(array('module' => 'sitetagcheckin', 'controller' => 'settings', 'action' => 'manage'), 'admin_default', true);
    $click = '<a href="' . $URL . '" target="_blank">click here</a>';
    $customBlocks = sprintf("To maintain consistency in the ‘Members Location & Proximity Search’ for the member locations entered from the ‘Edit Profile’ pages and ‘Edit Location’ pages, both these locations should be synchronized. For this syncing, Location type field for different Profile types should be mapped. Thus, %s (will open in new tab) to map the fields. After mapping, you must also sync member locations from ‘Member Locations’ section of this plugin.", $click);
		$this->addElement( 'Dummy' , 'sitetagcheckinprofile_mapping' , array (
			'label' => 'Profile Type - Location Field Mapping' ,
			'description' => $customBlocks,
		));
		$this->getElement('sitetagcheckinprofile_mapping')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

// 	  $optionsTable = Engine_Api::_()->fields()->getTable('user', 'options');
// 		$optionsTableName = $optionsTable->info('name');
// 		
// 		$select = $optionsTable->select()
// 		                    ->from($optionsTableName)
// 												->where($optionsTableName . '.field_id = ?', 1);
// 		$metaResults = $optionsTable->fetchAll($select);
// 	  if (count($metaResults) != 0) {
// 			$au_title[0] = "Select Profile Type";
// 			foreach ($metaResults as $metaResult) {
// 				$au_title[$metaResult->option_id] = $metaResult->label;
// 			}
// 
// 			$this->addElement('Select', 'sitetagcheckin_option_id', array(
// 				'label' => 'Profile Type',
// 				'description' => 'Select Profile Type',
// 				'multiOptions' => $au_title,
// 				'onchange' => 'fetchLikeSettings(this.value);'
// 			));
// 		}
// 		
//     $option_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('option_id',null);
// 		if (!empty($option_id)) {
// 			$metaTable = Engine_Api::_()->fields()->getTable('user', 'meta');
// 			$metaTableName = $metaTable->info('name');
// 			
// 			$mapsTable = Engine_Api::_()->fields()->getTable('user', 'maps');
// 			$mapsTableName = $mapsTable->info('name');
// 			
// 			$select = $metaTable->select()
// 								->setIntegrityCheck(false)
// 								->from($metaTableName, array('label', 'field_id', 'type'))
// 								->joinLeft($mapsTableName, "$metaTableName.field_id = $mapsTableName.child_id", null)
// 								->where($mapsTableName . '.option_id = ?', $option_id)
// 								->where($metaTableName . '.type = ?', 'location')
// 								->where($metaTableName . '.display = ?', '1')
// 								->where($metaTableName . '.search = ?', '1');
// 			$locationResult = $metaTable->fetchAll($select);
// 
// 			if (count($locationResult) != 0) {
// 				$auTitle[0] = "Select Location Field";
// 				foreach ($locationResult as $locationResults) {
// 					$auTitle[$locationResults->field_id] = $locationResults->label;
// 				}
// 
// 				$this->addElement('Select', 'sitetagcheckin_field_id', array(
// 					'label' => 'Location Fields',
// 					'description' => 'Which Location field associate.',
// 					'multiOptions' => $auTitle,
// 				));
// 			}
//     }
    }
    $this->addElement('Text', 'sitetagcheckin_map_city', array(
        'label' => 'Centre Location for Map',
        'description' => 'Enter the location which you want to be shown at centre of the map which is shown on Map.(To show the whole world on the map, enter the word "World" below.)',
        'required' => true,
        'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('sitetagcheckin.map.city', "World"),
    ));
    
    $this->addElement('Select', 'sitetagcheckin_map_zoom', array(
        'label' => "Default Zoom Level for Map at Content Profile / View Pages and Member Profile Page",
        'description' => 'Select the default zoom level for the map which is shown when Map View is chosen to view Location Markers and Check-in Markers on Content Profile / View Pages and Member Profile Pages respectively. (Note that as higher zoom level you will select, the more number of surrounding cities/locations you will be able to see.)',
        'multiOptions' => array(
            '1' => "1",
            "2" => "2",
            "4" => "4",
            "6" => "6",
            "8" => "8",
            "10" => "10",
            "12" => "12",
            "14" => "14",
            "16" => "16"
        ),
        'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('sitetagcheckin.map.zoom', 1),
    ));

    $this->addElement('Text', 'sitetagcheckin_default_textarea_text', array(
        'label' => "Default Text",
        'description' => 'Please enter the default text which will be displayed to users while adding location and checking into the various  contents on your site. (Note: Users will be able to edit this text.)',
        'value' => Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('sitetagcheckin.default.textarea.text', 'I am here!'),
    ));

    //SUBMIT BUTTON
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }
}
