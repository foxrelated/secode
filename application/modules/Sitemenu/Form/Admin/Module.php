<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Module.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_Form_Admin_Module extends Engine_Form {

  protected $_moduleId;

  public function setModuleId($id) {
    $this->_moduleId = $id;
    return $this;
  }
  
    public function init() {
      $module_id = $this->_moduleId;
      $this->setDescription('Use the form below to enable content for a module of your site. Start by selecting a content module, and then entering the various database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.');
      
      //TO CHECK IF MODULE IS CREATED OR EDITED
      if (empty($module_id)) {
        $this->setTitle('Add New Module for Menu');
        $item_category_value = $field_sponsored_value = $field_featured_value = $field_date_col = $field_comment_id = $field_like_id = $field_image_value = $field_body_value = $field_title_value = $field_owner_value = $item_table_title = $item_table_value = $field_category_title_value = '';

        // TO CREATE SELECTBOX OF MODULES
        $getModules = Engine_Api::_()->getDbTable('modules', 'sitemenu')->getModuleName();
        $module_table = Engine_Api::_()->getDbTable('modules', 'core');
        $module_name = $module_table->info('name');
        $select = $module_table->select()			
          ->from($module_name, array('name', 'title'))
          ->where($module_name . '.enabled =?', 1)
          ->where($module_name . '.type =?', 'extra');
        $getModulesAssoc = $select->query()->fetchAll();
        $ModuloeArray = array();
        $ModuloeArray[] = ' -- select --';
        foreach ($getModulesAssoc as $module) {
          if (!in_array($module['name'], $getModules)) {
              $ModuloeArray[$module['name']] = $module['title'];
          }
        }             
      } else {
        $this->setTitle('Edit Module for Menu');
        $module_obj = Engine_Api::_()->getItem('sitemenu_module', $module_id);            
        $item_table_value = $module_obj->item_type;
        $item_table_title = $module_obj->module_title;
        $field_owner_value = $module_obj->owner_field;
        $field_title_value = $module_obj->title_field;
        $field_body_value = $module_obj->body_field;
        $field_like_id = $module_obj->like_field;
        $field_comment_id = $module_obj->comment_field;
        $field_date_col = $module_obj->date_field;
        $field_featured_value = $module_obj->featured_field;
        $field_sponsored_value = $module_obj->sponsored_field;
        $field_image_option = $module_obj->image_option;
        $item_category_value = $module_obj->category_name;
        $field_category_title_value = $module_obj->category_title_field;
      }

      if(empty($module_id)){
        //MODULE NAME SELECT BOX ON CREATE
        $this->addElement('Select', 'module_name', array(
            'label' => 'Content Module',
            'multiOptions' => $ModuloeArray,
        ));
      } else {
        // DUMMY ELEMENT TO SHOW THE MENU TO BE EDITED
        $this->addElement('Dummy', 'temp_module_name', array(
          'label' => 'Content Module',
          'description' => @ucfirst($module_obj->module_name),
        ));
        
        //HIDDEN ELEMENT TO STORE THE MENUITEM BEING EDITED
        $this->addElement('Hidden', 'module_name', array(
          'value' => $module_obj->module_name
        ));
      }
      
      // MODULE TITLE THAT WILL BE SHOWN IN SELECT CONTENT MODULE IN MENU EDITOR 
      $this->addElement('Text', 'module_title', array(
          'label' => 'Content Title',
          'description' => 'Enter the content name for which you use this module. Ex: You may use the document module for ‘Tutorials’ on your site.',
          'required' => true,
          'value' => $item_table_title
      ));

      // ELEMENT item_type TO GET THE ITEM TYPE OF THE TABLE OF MODULE BEING CREATED OR EDITED
      $this->addElement('Text', 'item_type', array(
          'label' => 'Database Table Item',
          'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
          'required' => true,
          'value' => $item_table_value
      ));

      $this->addElement('Dummy', 'dummy_title', array(
          'label' => 'For the form fields below, please look at the structure of the main database table of this module.',
      ));

      // owner_id COLUMN NAME IN THE ABOVE GIVEN TABLE NAME
      $this->addElement('Text', 'table_owner', array(
          'label' => 'Content Owner Field in Table',
          'description' => 'Ex: owner_id or user_id',
          'required' => true,
          'value' => $field_owner_value
      ));
      $this->table_owner->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

      // title COLUMN NAME IN THE TABLE OF ITEM TYPE MENTION IN THE item_type ELEMENT  
      $this->addElement('Text', 'table_title', array(
          'label' => 'Content Title Field in Table',
          'description' => 'Ex: title',
          'required' => true,
          'value' => $field_title_value
      ));
      $this->table_title->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

      // body COLUMN NAME IN THE TABLE OF ITEM TYPE MENTION IN THE item_type ELEMENT 
      $this->addElement('Text', 'table_body', array(
          'label' => 'Content Body/Description Field in Table',
          'required' => true,
          'description' => 'Ex: body or description',
          'value' => $field_body_value
      ));
      $this->table_body->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

      // like count COLUMN NAME IN THE TABLE OF ITEM TYPE MENTION IN THE item_type ELEMENT 
      $this->addElement('Text', 'like_field', array(
          'label' => 'Like Column Name',
          'description' => 'Enter the like column name which you use in this module.',
          'required' => false,
          'value' => $field_like_id
      ));
      
      // comment count COLUMN NAME IN THE TABLE OF ITEM TYPE MENTION IN THE item_type ELEMENT 
      $this->addElement('Text', 'comment_field', array(
          'label' => 'Comment Column Name',
          'description' => 'Enter the comment column name which you use in this module.',
          'required' => false,
          'value' => $field_comment_id
      ));

      // modified date COLUMN NAME IN THE TABLE OF ITEM TYPE MENTION IN THE item_type ELEMENT 
      $this->addElement('Text', 'date_field', array(
          'label' => 'Creation Date Column Name',
          'description' => 'Enter the creation date column name which you use in this module.',
          'required' => false,
          'value' => $field_date_col
      ));
      // featured COLUMN NAME IN THE TABLE OF ITEM TYPE MENTION IN THE item_type ELEMENT 
      $this->addElement('Text', 'featured_field', array(
          'label' => 'Featured Column Name',
          'description' => 'Enter the featured column name which you use in this module.',
          'required' => false,
          'value' => $field_featured_value
      ));

      // sponsored COLUMN NAME IN THE TABLE OF ITEM TYPE MENTION IN THE item_type ELEMENT 
      $this->addElement('Text', 'sponsored_field', array(
          'label' => 'Sponsored Column Name',
          'description' => 'Enter the sponsored column name which you use in this module.',
          'required' => false,
          'value' => $field_sponsored_value
      ));

      //ELEMENT TO CHOOSE WHOSE IMAGE DO YOU WANT TO SHOW WITH CONTENT.
      $this->addElement('Radio', 'image_option', array(
          'label' => 'Content Image',
          'description' => 'Whose image do you want to show with content?',
          'multiOptions' => array(
              1 => 'Content',
              0 => 'Owner',
          ),
          'value' => !isset($field_image_option) ? 1 : $field_image_option,
      ));
        
      $this->addElement('Radio', 'category_option', array(
          'label' => 'Content Categories',
          'description' => 'Do you want to show content categories?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No',
          ),
          'value' => !empty ($item_category_value)? 1 : 0,
          'onchange' => 'showcategory();'
      ));

      // CATEGORY TABLE ITEM TYPE TO GET CATEGORIES
      $this->addElement('Text', 'category_name', array(
          'label' => 'Database Category Table Item',
          'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog_category'. Thus, the Database Category Table Item for blog module is: 'blog_category']",
          'value' => $item_category_value
      ));
      $this->category_name->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
      
      // CATEGORY TITLE COLUMN NAME IN THE TABLE OF ITEM TYPE MENTION IN THE category_name ELEMENT 
      $this->addElement('Text', 'category_title_field', array(
          'label' => 'Category Title Column Name',
          'description' => 'Enter the title column name of category which you use in this module.',
          'value' => $field_category_title_value
      ));

      $this->addElement('Button', 'submit', array(
          'label' => 'Save Settings',
          'type' => 'submit',
          'ignore' => true
      ));
    }

}