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
class Sitelike_Form_Admin_Moduleedit extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Edit Module for Likes')
            ->setDescription('Use the form below to configure content from a module of your site to be displayed in various Likes widgets and pages. For the chosen content module, enter the various
      database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.');

    $mixsettingId = Zend_Controller_Front::getInstance()->getRequest()->getParam('mixsetting_id', null);

    $getResults = Engine_Api::_()->getDbTable('mixsettings', 'sitelike')->getResults(array('mixsetting_id' => $mixsettingId, 'column_name' => array('module', 'resource_type')));
    
    $module_name[] = $getResults[0]['module'];
    $resource_type[] = $getResults[0]['resource_type'];

    $this->addElement('Select', 'module', array(
        'label' => 'Module Name',
        'allowEmpty' => false,
        'disable' => true,
        'multiOptions' => $module_name,
    ));


    $this->addElement('Select', 'resource_type', array(
        'label' => 'Database Table Item',
        'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
        'disable' => true,
        'multiOptions' => $resource_type,
    ));

    //ELEMENT PACKAGE TITLE
    $this->addElement('Text', 'item_title', array(
        'label' => 'Plural Title',
        'description' => 'Please enter the Plural Title for content from this module (Ex: Blogs, Pages, Recipes, Albums, etc.). This text will come in places like the left column of “My Friends’ Likes” page.',
        'allowEmpty' => FALSE,
        'validators' => array(
            array('NotEmpty', true),
        )
    ));

    //ELEMENT PACKAGE TITLE
    $this->addElement('Text', 'title_items', array(
        'label' => 'Singular Title',
        'description' => 'Please enter the Singular Title for content from this module (Ex: Blog, Page, Recipe, Album, etc.). This text will come in places like the left column of “My Friends’ Likes” page.',
        'allowEmpty' => FALSE,
        'validators' => array(
            array('NotEmpty', true),
        )
    ));

    $this->addElement('Checkbox', 'enabled', array(
        'description' => 'Enable Module for Likes',
        'label' => 'Make content from this module available in the various widgets and pages of this plugin (like on Liked Items, Friends\' Likes, Most Liked Items widget, etc).',
        'value' => 1
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Settings',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}