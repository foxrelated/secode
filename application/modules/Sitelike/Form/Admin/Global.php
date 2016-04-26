<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_Form_Admin_Global extends Engine_Form {

  public function init() {
  
    $mixsettingstable = Engine_Api::_()->getDbtable('mixsettings', 'sitelike');
    // My stuff
    $this->setTitle('Tabbed Widgets')
            ->setDescription("Configure the settings for the various tabbed widgets. These widgets show the maximum liked items for their respective content types, over their selected durations. Below, you can select the widgets having liked items for which you want to configure the settings and create and manage tabs for them. Maximum of 3 tabs can be created and configured in each widget. The content types that you can choose below are the ones enabled in Manage Modules section.");
            
    // Modules which are taking by us.
    $mixSettingsResults = $mixsettingstable->getMixLikeItems();
    $mixSettingsResults['mixed'] = 'Mixed';
    $mixSettingsItems = array_merge(array(""), $mixSettingsResults);

    // Element: content_type
    $this->addElement('Select', 'content_type', array(
        'label' => 'Widget Type',
        'multiOptions' => $mixSettingsItems,
        'onchange' => 'javascript:fetchLikeSettings(this.value);'
    ));

    // ARRAY CAN MAKE WITH INDEX IN DAYS
    $duration_array1 = array('1' => 'Today', '7' => 'Week', '14' => '2 Week', '21 ' => '3 Week', '30' => '1 Month', '60' => '2 Month', '90' => '3 Month', '180' => '6 Month', '365' => '1 Year', '730' => '2 Year', '1095' => '3 Year', 'overall' => 'Overall');

    $contenttype = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);
    //HERE WE CAN CHECK VALIDATION FOR CONTENT TYPE SELECT
    if (!empty($contenttype)) {

      $item_title = $mixsettingstable->getColumnValue(array('resource_type'  => $contenttype, 'columnValue' => 'item_title'));

      if ($contenttype == 'mixed') {
        $this->addElement('Dummy', 'configname', array(
            'label' => 'Configure Most Liked Mixed widget',
        ));
      } else {
        $this->addElement('Dummy', 'configname', array(
            'label' => "Configure Most Liked " . $item_title . " widget",
        ));
      }

      // Element: view_layout
      $this->addElement('Radio', 'view_layout', array(
          'label' => 'Items Layout',
          'description' => 'Select an items view layout for the items in this widget.',
          'multiOptions' => array(
              1 => 'List view.',
              0 => 'Thumbnail (with tooltip) view.'
          ),
          'value' => 1,
      ));

      $this->addElement('Dummy', 'tab1', array(
          'label' => ' Tab 1 settings:',
      ));
      // Element: tab1_show
      $this->addElement('Radio', 'tab1_show', array(
          'label' => 'Tab Visibility',
          'description' => 'Do you want to show this tab in this widget ?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1,
          'onclick' => 'showblock1(this.value)'
      ));
      // Element: tab1_title
      $this->addElement('Text', 'tab1_name', array(
          'label' => 'Tab Title',
          'description' => 'Please enter the title for this tab. The title must be entered if this tab is to be made visible.',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('tab1.name', 0)
      ));
      // Element: tab1_duration
      $this->addElement('Select', 'tab1_duration', array(
          'label' => 'Likes Duration',
          'description' => 'Please select the duration over which the most liked items are to be shown in this tab. ',
          'multiOptions' => $duration_array1,
          'value' => 7,
      ));
      // Element: tab1_entries
      $this->addElement('Text', 'tab1_entries', array(
          'label' => 'Number of Items',
          'description' => 'Please enter the maximum number of items that should be displayed in this tab.',
          'allowEmpty' => false,
          'required' => true,
          'maxlength ' => '3',
      ));


      // Element: tab2_show
      $this->addElement('Dummy', 'tab2', array(
          'label' => ' Tab 2 settings:',
      ));
      // Element: tab2_show
      $this->addElement('Radio', 'tab2_show', array(
          'label' => 'Tab Visibility ',
          'description' => 'Do you want to show this tab in this widget ?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1,
          'onclick' => 'showblock2(this.value)'
      ));
      // Element: tab2_title
      $this->addElement('Text', 'tab2_name', array(
          'label' => 'Tab Title',
          'description' => 'Please enter the title for this tab. The title must be entered if this tab is to be made visible.',
      ));
      // Element: tab2_duration
      $this->addElement('Select', 'tab2_duration', array(
          'label' => 'Likes Duration',
          'description' => 'Please select the duration over which the most liked items are to be shown in this tab.',
          'multiOptions' => $duration_array1,
          'value' => 30,
      ));
      // Element: tab1_entries
      $this->addElement('Text', 'tab2_entries', array(
          'label' => 'Number of Items',
          'description' => 'Please enter the maximum number of items that should be displayed in this tab.',
          'allowEmpty' => false,
          'required' => true,
      ));


      // Element: tab3_show
      $this->addElement('Dummy', 'tab3', array(
          'label' => ' Tab 3 settings:',
      ));
      // Element: tab3_show
      $this->addElement('Radio', 'tab3_show', array(
          'label' => 'Tab Visibility',
          'description' => 'Do you want to show this tab in this widget ?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1,
          'onclick' => 'showblock3(this.value)'
      ));
      // Element: tab3_title
      $this->addElement('Text', 'tab3_name', array(
          'label' => 'Tab Title',
          'description' => 'Please enter the title for this tab. The title must be entered if this tab is to be made visible.',
      ));
      // Element: tab3_duration
      $this->addElement('Select', 'tab3_duration', array(
          'label' => 'Likes Duration',
          'description' => 'Please select the duration over which the most liked items are to be shown in this tab.',
          'multiOptions' => $duration_array1,
          'value' => 'overall',
      ));
      // Element: tab1_entries
      $this->addElement('Text', 'tab3_entries', array(
          'label' => 'Number of Items',
          'description' => 'Please enter the maximum number of items that should be displayed in this tab.',
          'allowEmpty' => false,
          'required' => true,
      ));

      //HERE WE CAN SET THE HIDDEN FIELD SET
      $this->addElement('Hidden', 'action_id', array(
          'order' => 990,
          'filters' => array(
          ),
      ));
      // Element: Savechange
      $this->addElement('Button', 'Savechange', array(
          'label' => 'Save Changes',
          'type' => 'submit',
      ));
    }
  }

}