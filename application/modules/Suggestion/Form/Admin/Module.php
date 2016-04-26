<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Module.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Form_Admin_Module extends Engine_Form {

  protected $_edit;
  protected $_defaultMod;

  public function getedit() {
    return $this->_edit;
  }

  public function setedit($id) {
    $this->_edit = $id;
    return $this;
  }

  public function getDefaultMod() {
    return $this->_defaultMod;
  }

  public function setDefaultMod($id) {
    $this->_defaultMod = $id;
    return $this;
  }

  public function init() {

    $flag_module_name = $temp_module_name = Zend_Controller_Front::getInstance()->getRequest()->getParam('module_name', null);
    if (strstr($flag_module_name, 'sitereview')) {
      $flag_module_name = 'sitereview';
    }
    $modinfoId = Zend_Controller_Front::getInstance()->getRequest()->getParam('modinfo_id', 0);
    if (!empty($modinfoId)) {
      $manageModules = Engine_Api::_()->getItem('suggestion_modinfo', $modinfoId);
    }

    $setModules = Engine_Api::_()->getDbTable('modinfos', 'suggestion')->getRestrictedModule();


    $finalArray = $setModules;

    if (empty($this->_edit)) {
      $module_table = Engine_Api::_()->getDbTable('modules', 'core');
      $module_name = $module_table->info('name');
      $select = $module_table->select()
              ->from($module_name, array('name', 'title'))
              ->where($module_name . '.type =?', 'extra')
              ->where($module_name . '.name not in(?)', $finalArray)
              ->where($module_name . '.enabled =?', 1);

      $contentModuloe = $select->query()->fetchAll();
      $contentModuloeArray = array();

      if (!empty($contentModuloe)) {
        $contentModuloeArray[] = '-- Select --';
        foreach ($contentModuloe as $modules) {
          $contentModuloeArray[$modules['name']] = $modules['title'];
        }
      }

      $isSitereviewExist = Engine_Api::_()->getDbtable('modules', 'core')->getModule("sitereview");
      if (!empty($isSitereviewExist)) {
        $queryObj = Zend_Db_Table_Abstract::getDefaultAdapter();
        $getReviewModules = $queryObj->query("SELECT * FROM `engine4_suggestion_module_settings` WHERE `module` LIKE 'sitereview'")->fetchAll();
        $listingTypeIds = array();
        if (!empty($getReviewModules)) {
          foreach ($getReviewModules as $module) {
            $settingsArray = @unserialize($module['settings']);
            $listingTypeIds[] = $settingsArray['listing_id'];
          }
        }

        if (!empty($listingTypeIds)) {
          $listingTypeIdsStr = @implode(",", $listingTypeIds);
          $listingTypeTable = Engine_Api::_()->getDbTable('listingtypes', 'sitereview');
          $listingTypeModName = $listingTypeTable->info('name');
          $select = $listingTypeTable->select()
                  ->setIntegrityCheck(false)
                  ->from($listingTypeModName, array('listingtype_id', 'title_singular'))
                  ->where($listingTypeModName . '.visible =?', 1)
                  ->where($listingTypeModName . '.listingtype_id NOT IN (' . $listingTypeIdsStr . ')');
          $contentListingType = $select->query()->fetchAll();
          if (!empty($contentListingType)) {
            foreach ($contentListingType as $listingType) {
              $contentModuloeArray['sitereview_' . $listingType['listingtype_id']] = $listingType['title_singular'];
            }
          }
        }
      }

      if (!empty($contentModuloeArray)) {
        $this->addElement('Select', 'module', array(
            'label' => 'Content Module',
            'allowEmpty' => false,
            'onchange' => 'setModuleName(this.value)',
            'multiOptions' => $contentModuloeArray
        ));
      } else {
        //VALUE FOR LOGO PREVIEW.
        $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("There are currently no new modules to be added to ‘Manage Module’ section.") . "</span></div>";
        $this->addElement('Dummy', 'module', array(
            'description' => $description,
        ));
        $this->module->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
      }

      $contentItem = array();
      if (!empty($flag_module_name)) {
        if (strstr($flag_module_name, 'sitereview')) {
          $this->module->setValue($temp_module_name);
          $contentItem['sitereview_listing'] = 'sitereview_listing';
        } else {
          $this->module->setValue($flag_module_name);
        $contentItem = $this->getContentItem($flag_module_name);
        if (empty($contentItem))
          $this->addElement('Dummy', 'dummy_title', array(
              'description' => 'For this module, there is  no item defined in the manifest file.',
          ));
        }
      }
    }

    if (!empty($contentItem) || !empty($this->_edit)) {

      if (!empty($this->_edit)) {
        $this->addElement('Select', 'module', array(
            'label' => 'Content Module',
            'attribs' => array('disable' => true)
                )
        );
      }

      if (empty($this->_edit)) {
        $this->addElement('Select', 'item_type', array(
            'label' => 'Database Table Item',
            'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
            'multiOptions' => $contentItem,
        ));
      } else {
        $this->addElement('Select', 'item_type', array(
            'label' => 'Database Table Item',
            'attribs' => array('disable' => true)
        ));
      }

      if(!strstr($flag_module_name, 'sitereview')) {
      $this->addElement('Text', 'owner_field', array(
          'label' => 'Content Owner Field in Table',
          'description' => 'Ex: owner_id or user_id',
          'required' => true,
          'value' => 'owner_id'
      ));
      $this->owner_field->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
      }

      //ELEMENT PACKAGE TITLE
      $this->addElement('Text', 'item_title', array(
          'label' => 'Item Title',
          'description' => 'Please enter the Title for content from this module (Ex: Albums, Blogs etc.). This text will come in places where content from this module is displayed at your site.',
          'allowEmpty' => FALSE,
          'validators' => array(
              array('NotEmpty', true),
          )
      ));


      //ELEMENT PACKAGE TITLE
      $this->addElement('Text', 'button_title', array(
          'label' => 'Button Title',
          'description' => 'Please enter the Title which you want to be displayed on the link for the content from this module in the widgets (Ex: View this Album, View this Blog etc.).',
          'allowEmpty' => FALSE,
          'validators' => array(
              array('NotEmpty', true),
          )
      ));

      $qualityOfSuggestion = array(1 => 'Good', 0 => 'Average');
      if (!empty($this->_defaultMod)) {
        $qualityOfSuggestion[2] = 'High';
      }
      krsort($qualityOfSuggestion);

      $this->addElement('Radio', 'quality', array(
          'label' => 'Quality of Suggestions',
          'description' => 'Select the quality of suggestions of this suggestion type that should be shown to users. (Note: A higher quality of suggestion uses a better algorithm for computing suggestions and therefore accordingly, also uses more computation resources.)',
          'multiOptions' => $qualityOfSuggestion,
          'value' => 1
      ));

      $this->addElement('Radio', 'link', array(
          'label' => 'Suggest to Friends link',
          'multiOptions' => array(
              1 => 'Yes, show this link.',
              0 => 'No, do not show this link.'
          ),
          'value' => 0
      ));

      if (strstr($manageModules->module, "sitereview")) {
        $tempModName = 'sitereview_' . $manageModules->modinfo_id;

        $this->addElement('Radio', 'popup', array(
            'label' => 'Suggestions popup after Creating a listing in ' . $manageModules->item_title,
            'description' => 'Do you want the suggestions popup to be shown to a user after Creating a ' . $manageModules->item_title . ' ? [This popup enables the user to suggest the newly created listing to his/her friends, so that they may view it.]',
            'multiOptions' => array(
                1 => 'Yes, show this suggestions popup.',
                0 => 'No, do not show this suggestions popup.'
            ),
            'value' => 0
        ));
      } else {
        if (!empty($this->_defaultMod)) {
          $this->addElement('Radio', 'popup', array(
              'label' => 'Suggestions popup after Creating a Selected Module',
              'description' => 'Do you want the suggestions popup to be shown to a user after Creating a Video ? [This popup enables the user to suggest the newly created video to his/her friends, so that they may view it.]',
              'multiOptions' => array(
                  1 => 'Yes, show this suggestions popup.',
                  0 => 'No, do not show this suggestions popup.'
              ),
              'value' => 0
          ));
        }
      }

      $this->addElement('Checkbox', 'enabled', array(
          'description' => 'Enable Module for Suggestion',
          'order' => 49,
          'label' => 'Make content from this module available in the various widgets and pages of this plugin (like on Explore Suggestions Page,  Recommendations (selected content), Explore Suggestions widget, Invite Friends widget, People You May Know widget, Recommendations widget, Suggest to Friend widget, etc.).',
          'value' => 1
      ));

      // Element: execute
      $this->addElement('Button', 'execute', array(
          'label' => 'Save Settings',
          'order' => 50,
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array('ViewHelper'),
      ));

      // Element: cancel
      $this->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'order' => 51,
          'prependText' => ' or ',
          'ignore' => true,
          'link' => true,
          'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage-module')),
          'decorators' => array('ViewHelper'),
      ));
    }
  }

  // Returns the array of "Item types" of the modules, which are available in the manifest.php file of the modules.
  public function getContentItem($moduleName) {
    $file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
    $contentItem = array();
    if (@file_exists($file_path)) {
      $ret = include $file_path;
      if (isset($ret['items'])) {
        foreach ($ret['items'] as $item)
          $contentItem[$item] = $item . " ";
      }
    }
    return $contentItem;
  }

}