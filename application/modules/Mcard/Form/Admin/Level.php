<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Mcard_Form_Admin_Level extends Engine_Form {
  /* Method to initialize the form with elements
   */
  public function init() {
    $session = new Zend_Session_Namespace();
		$mptype_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('mptype_id');
		$level_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('level_id');

		$infoTable = Engine_Api::_()->getItemTable('mcard_info');
		$data = $infoTable->getVal($level_id, $mptype_id);
		$logoval;
		$card_image_val;
		$shinyval;
		if (!empty($data['logo'])) {
			$logoval = $data['logo'];
		} else {
			$logoval = "";
		}
		if (!empty($data['card_bg_image'])) {
			$card_image_val = $data['card_bg_image'];
			$shinyval = 0;
		} else {
      if ( empty($data['card_bg_color']) ) {
				$card_image_val = "";
				$shinyval = 0;  
			} else {
				$card_image_val = "";
				$shinyval = 1;
			}
		}
			
		$font_prepared = array(
				"Arial, Helvetica, sans-serif",
				"Arial Black, Gadget, sans-serif",
				"Bookman Old Style, serif",
				"Comic Sans MS, cursive",
				"Courier, monospace",
				"Courier New, Courier, monospace",
				"Garamond, serif",
				"Georgia, serif",
				"Impact, Charcoal, sans-serif",
				"Lucida Console, Monaco, monospace",
				"Lucida Sans Unicode, Lucida Grande, sans-serif",
				"MS Sans Serif, Geneva, sans-serif",
				"MS Serif, New York, sans-serif",
				"Palatino Linotype, Book Antiqua, Palatino, serif",
				"Symbol, sans-serif",
				"Tahoma, Geneva, sans-serif",
				"Times New Roman, Times, serif",
				"Trebuchet MS, Helvetica, sans-serif",
				"Verdana, Geneva, sans-serif",
				"Webdings, sans-serif",
				"Wingdings, Zapf Dingbats, sans-serif"
		);
		$font_prepared = array_combine($font_prepared, $font_prepared);
			
		$this->setTitle('Member Level Settings')
			->setAttrib('enctype', 'multipart/form-data')
			->setAttrib('id', 'profileform')
			->setDescription("These settings are applied on a per member level and profile type basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");
			
			
		$this->loadDefaultDecorators();
		$this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));
			
		// Prepare user levels
		$table = Engine_Api::_()->getDbtable('levels', 'authorization');
		$select = $table->select();
		$user_levels = $table->fetchAll($select);
			
		foreach ($user_levels as $user_level) {
				if($user_level->level_id != 5)
				{
				$levels_prepared[$user_level->level_id] = $user_level->getTitle();
				}
		}
			
		// Prepare Profile type
		$fieldtable = Engine_Api::_()->getItemTable('mcard_option');
		$fieldselect = $fieldtable->select()
				->where("field_id = ?", 1);
		//field_id = 1 for fields_name and field_id = 5 for male and female entry
		$mp_levels = $fieldtable->fetchAll($fieldselect);
			
		foreach ($mp_levels as $mp_level) {
			$mptype_prepared[$mp_level->option_id] = $mp_level->label;
		}
			
			
		// Member category field
		$this->addElement('Select', 'level_id', array(
				'label' => 'Member Level',
				'multiOptions' => $levels_prepared,
				'onchange' => 'javascript:fetchLevelSettings(this.value);'
		));
			
		// Member profile type field
		$this->addElement('Select', 'mptype_id', array(
				'label' => 'Profile Type',
				'multiOptions' => $mptype_prepared,
				'onchange' => 'javascript:fetchProfileTypeSettings(this.value);'
		));
		
		$this->addElement('Radio', 'card_status', array(      
		'label' => 'Enable Membership Cards',
		'description' => "Do you want to enable membership cards for users of this Member Level and Profile Type ?",   
		'multiOptions' => array(
			1 => 'Yes',
			0 => 'No'
		),
	));

		$this->addElement('Radio', 'shiny_plastic_look', array(
				'label' => 'Card Background',
				'description' => 'Choose one of the below options for the background of the Membership Cards on your site. [The card will also have a shiny plastic look.]',
				'multiOptions' => array(
			1 => 'Select a background color for the cards.',
			0 => 'Upload a background image for the cards.'
				),
				'value' => $shinyval,
		));
			
		$this->addElement('Text', 'card_bg_color', array(
				'label' => 'Customize Background Color',
				'description' => 'Select the color of the membership card. (Click on the rainbow below to choose your color.)',
				'decorators' => array(array('ViewScript', array(
				'viewScript' => '_formImagerainbow1.tpl',
				'class' => 'form element'
			)))
		));
			
		$this->addElement('File', 'card_bg_image', array(
				'label' => 'Upload Background Image',
				'description' => ' Please upload background image. [Note: The ideal dimensions of the background image are 326 * 210 pixels. Please also be sure to choose only as many profile fields to be shown in the card as might fit in it.]',
				'destination' => APPLICATION_PATH . '/public/temporary/',
				'validators' => array(		  
					array('Size', false, 612000),
					array('Extension', false, 'jpg,png,gif'),
				),
				'onchange'=>'javascript:uploadProfilebgImage();'
			));

		$this->addElement('Radio', 'logo_select', array(
				'label' => 'Show Logo',
				'description' => 'Do you want to show a Logo on the Membership Cards ?',
				'multiOptions' => array(
			1 => 'Yes',
			0 => 'No'
				),
				'value' => 1, //$logoSelectFlag,
		));

		$this->addElement('Hidden', 'upload_logo_indication', array(
				'order' => -5001,
		));

		$this->addElement('File', 'logo', array(
				'label' => 'Upload Logo',
				'destination' => APPLICATION_PATH . '/public/temporary/',
				'validators' => array(
					array('Size', false, 612000),
					array('Extension', false, 'jpg,png,gif'),
				),
				'onchange'=>'javascript:uploadProfilePhoto();'
			));
			
		$this->addElement('Hidden', 'logo_hidden', array(
				'value' => $logoval,
				'order' => -5000,
		));

		$this->addElement('Radio', 'show_card_label', array(
				'label' => 'Show Label / Title',
				'description' => 'Do you want to show a Label / Title on the Membership Cards ?',
				'multiOptions' => array(
			1 => 'Yes',
			0 => 'No'
				),
				'value' => 1,
		));
			
		$this->addElement('Text', 'card_label', array(
				'label' => 'Enter Label / Title',
		));

		$this->addElement('Text', 'label_color', array(
				'decorators' => array(array('ViewScript', array(
				'viewScript' => '_labelcolor.tpl',
				'class' => 'form element'
			)))
		));
			
		$this->addElement('Select', 'label_font', array(
				'label' => 'Customize Label / Title Font',
				'multiOptions' => $font_prepared
		));

		$this->addElement('Radio', 'profile_photo', array(
				'label' => 'Show Profile Photo',
				'description' => 'Do you want to show Profile Photo of users on their Membership Cards ?',
				'multiOptions' => array(
			1 => 'Yes',
			0 => 'No'
				),
				'value' => 1,
		));
			
		//Pickup the dynamic values in the fields_meta table according to the profile type
		$metatable = Engine_Api::_()->getItemTable('mcard_meta');
		$rmetaName = $metatable->info('name');
		$maptable = Engine_Api::_()->getItemTable('mcard_map');
		$rmapName = $maptable->info('name');
		$select = $metatable->select()
				->setIntegrityCheck(false)
				->from($metatable, array($rmetaName . '.field_id', $rmetaName . '.label'))
				->join($rmapName, $rmapName . '.child_id = ' . $rmetaName . '.field_id', array())
				->where($rmapName . '.option_id = ?', $mptype_id)
				->where($rmetaName . '.type <> ?', 'heading');
		$checkval = $metatable->fetchAll($select);
			
		//Hardcoded select_options to be filled here
		$selectOption = array(
				'displayname' => 'Display Name',
				'username' => 'Username',
				'mlevel_id' => 'Membership Level',
				'mptype' => 'Profile Type',
				'doj' => 'Joining Date',
		);
		foreach ($selectOption as $key => $value) {
			$hardCoded[] = $key;
		}

		//Dynamic select_option created here
		$storeIndex;
		foreach ($checkval->toarray() as $key => $value) {
			foreach ($value as $k => $v) {
				if ($k == 'field_id')
					$storeIndex = $v;
				if ($k == 'label')
					$selectOption[$storeIndex] = $v;
			}
		}
		$session->selectOptionFormValues = $selectOption;
		foreach ($data as $key => $value) {
			switch ($key) {
				case "card_bg_color":
				case "card_bg_image":
				case "logo":
				case "Profile Photo":
				case "card_label":
					unset($data[$key]);
					break;
				case "Profile Type":
					$data[$key] = "mptype";
					break;
				case "Membership Level":
					$data[$key] = "mlevel_id";
					break;
				default:
					break;
			}
		}
		$data = array_combine($data, array_keys($data));  //Flips the associative array
		$selectOptionCopy = $selectOption;
		$final_Select_Options = array();
		$final_intersect = array_intersect_key($data, $selectOptionCopy);
		foreach ($final_intersect as $key => $value) {
			$final_Select_Options[$key] = $value;
		}
			//  $finalhardCoded = array_unique(array_merge($hardCoded, array_keys($final_Select_Options)));
		$finalhardCoded = array_unique(array_keys($final_Select_Options));
		$final_diff = array_diff_key($selectOptionCopy, $final_Select_Options);
		foreach ($final_diff as $key => $value) {
			$final_Select_Options[$key] = $value;
		}

			
		$this->addElement('MultiCheckbox', 'select_option', array(
				'label' => 'Profile Fields on Cards',
				'description' => 'Select the items from the Profile Fields that should be shown on the Membership Cards. (Drag and drop the items below to set order.)',
				'multiOptions' => $final_Select_Options,
				'value' => $finalhardCoded
		));

		$this->addElement('Radio', 'empty_fields', array(
				'label' => 'Show Empty Fields',
				'description' => 'Do you want to show empty fields (fields with no value for the user) on the Membership Cards ?',
				'multiOptions' => array(
			1 => 'Yes',
			0 => 'No'
				),
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('empty.fields')
		));

		$this->addElement('Text', 'info_color', array(
				'decorators' => array(array('ViewScript', array(
				'viewScript' => '_infocolor.tpl',
				'class' => 'form element'
			)))
		));
			
		$this->addElement('Select', 'info_font', array(
				'label' => 'Customize Information Font',
				'multiOptions' => $font_prepared
		));
			
			
		$this->addElement('Hidden', 'card_bg_image_hidden', array(
				'value' => $card_image_val,
				'order' => 9999,
		));

		$this->addElement('Hidden', 'upload_card_bg_image', array(
				'order' => 9998,
		));

		$this->addElement('Button', 'submit', array(
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array(array('ViewScript', array(
				'viewScript' => '_formbuttons.tpl',
				'class'      => 'form element'
			)))
		));
  }
}