<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Map.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Form_Admin_Map extends Engine_Form {

  public function init() {
    $this
            ->setMethod('post')
            ->setAttrib('class', 'global_form_box')
            ->setTitle("Add Mapping");
//             ->setDescription("Select a “Location” type profile field to be used as the primary location for searching members of this Profile Type based on location & proximity, then click 'Add'.
// Note: To sync locations of users who have already entered their location for the selected profile field with “Members Location & Proximity Search”, you need to sync the members from the ‘Member Locations’ section of this plugin. If for this Profile Type, you are mapping a new “Location” type field, then the location which users have entered from their ‘Edit My Location’ page will be automatically synced with the selected field.
// Please make sure that this is your final selection, because deleting this field might create inconsistencies on your site for location based members searching.");

    //Element: profile_type
//     $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitepage_page');
//     if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
//       $profileTypeField = $topStructure[0]->getChild();
//       $options = $profileTypeField->getOptions();
//       if (count($options) > 0) {
//         $options = $profileTypeField->getElementParams('sitepage_page');
//         unset($options['options']['order']);
//         unset($options['options']['multiOptions']['0']);
//         $this->addElement('Select', 'profile_type', array_merge($options['options'], array(
//                     'required' => true,
//                     'allowEmpty' => false,
//                 )));
//       } else if (count($options) == 1) {
//         $this->addElement('Hidden', 'profile_type', array(
//             'value' => $options[0]->option_id
//         ));
//       }
//     }
    
    $option_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('option_id',null);
		if (!empty($option_id)) {
			$typeField = array('location', 'city', 'country');
			
			$metaTable = Engine_Api::_()->fields()->getTable('user', 'meta');
			$metaTableName = $metaTable->info('name');
			
			$mapsTable = Engine_Api::_()->fields()->getTable('user', 'maps');
			$mapsTableName = $mapsTable->info('name');

			$select = $metaTable->select()
								->setIntegrityCheck(false)
								->from($metaTableName, array('label', 'field_id', 'type'))
								->joinLeft($mapsTableName, "$metaTableName.field_id = $mapsTableName.child_id", null)
								->where($mapsTableName . '.option_id = ?', $option_id)
								->where($metaTableName . '.display = ?', '1')
								->where($metaTableName . '.type IN (?)', (array) $typeField);
								//->where($metaTableName . '.type = ?', 'location')
							//->orwhere($metaTableName . '.type = ?', 'country')
								//->orwhere($metaTableName . '.type = ?', 'city');
								//->where($metaTableName . '.search = ?', '1');
			$locationResult = $metaTable->fetchAll($select);

			if (count($locationResult) != 0) {
				$auTitle[0] = "";
				foreach ($locationResult as $locationResults) {
					$auTitle[$locationResults->field_id] = $locationResults->label;
				}

				$this->addElement('Select', 'profile_type', array(
					'label' => 'Location Type Field',
					'multiOptions' => $auTitle,
				));
			
    

    $this->addElement('Button', 'yes_button', array(
        'label' => 'Add',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

//     $this->addElement('Button', 'no_button', array(
//         'label' => 'No',
//         'type' => 'submit',
//         'ignore' => true,
//         'decorators' => array('ViewHelper')
//     ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('yes_button', 'no_button', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    }
		else {
		$description = "<div class='tip'><span>" . Zend_Registry::get( 'Zend_Translate' )->_( "You have currently not created any “Location” type profile field for this Profile Type. To create a “Location” type field for this Profile Type, please go to ‘Settings’ > ‘Profile Questions’ section of your ‘Admin Panel’." ) . "</span></div>" ;

    //VALUE FOR LOGO PREVIEW.
    $this->addElement( 'Dummy' , 'no_profile_type' , array (
      'description' => $description ,
    )) ;
    $this->no_profile_type->addDecorator( 'Description' , array ( 'placement' => Zend_Form_Decorator_Abstract::PREPEND , 'escape' => false ) ) ;
        
				$this->addElement('Button', 'no_button', array(
					'label' => 'Cancel',
					'type' => 'submit',
					'ignore' => true,
					'onClick' => 'javascript:parent.Smoothbox.close();',
					'decorators' => array('ViewHelper')
			));
			}
    } 
  }

}

?>