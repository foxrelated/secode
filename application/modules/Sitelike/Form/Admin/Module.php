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
class Sitelike_Form_Admin_Module extends Engine_Form {

  public function init() {

    $this->setTitle('Add New Module for Likes')
            ->setDescription('Use the form below to enable content from a module of your site to be displayed in various Likes widgets and pages. Start by selecting a content module, and then entering the various database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.');

    $notInclude = array('activity', 'advancedactivity', 'sitealbum', 'sitelike', 'sitepageoffer', 'sitepagebadge', 'featuredcontent', 'sitepagediscussion', 'sitepagelikebox', 'mobi', 'advancedslideshow', 'birthday', 'birthdayemail', 'communityad', 'dbbackup', 'facebookse', 'facebooksefeed', 'facebooksepage', 'feedback', 'groupdocument', 'grouppoll', 'mapprofiletypelevel', 'mcard', 'poke', 'sitealbum', 'sitepageinvite', 'siteslideshow', 'socialengineaddon', 'seaocore', 'suggestion', 'userconnection', 'sitepageform', 'sitepageadmincontact', 'sitebusinessbadge', 'sitebusinessoffer', 'sitebusinessdiscussion', 'sitebusinesslikebox', 'sitebusinessinvite', 'sitebusinessform', 'sitebusinessadmincontact', 'sitetagcheckin', 'sitereviewlistingtype', 'sitegroupoffer', 'sitepageintegration', 'sitebusinessintegration', 'sitegroupintegration', 'sitepagemember', 'sitebusinessmember', 'sitegroupmember', 'sitemailtemplates', 'sitepageurl', 'sitestoreadmincontact', 'sitestorealbum', 'sitestoreform', 'sitestoreinvite', 'sitestorelikebox', 'sitestoreoffer', 'sitestoreproduct', 'sitestorereview', 'sitestoreurl', 'sitestorevideo');

    $newArray = array('album', 'blog', 'classified', 'document', 'event', 'forum', 'poll', 'video', 'list', 'group', 'music', 'recipe', 'user', 'sitepage', 'sitepagenote', 'sitepagevideo', 'sitepagepoll', 'sitepagemusic', 'sitepagealbum', 'sitepageevent', 'sitepagereview', 'sitepagedocument', 'sitebusiness', 'sitebusinessalbum', 'sitebusinessdocument', 'sitebusinessevent', 'sitebusinessnote', 'sitebusinesspoll', 'sitebusinessmusic', 'sitebusinessvideo', 'sitebusinessreview', 'sitegroup', 'sitegroupalbum', 'sitegroupdocument', 'sitegroupevent', 'sitegroupnote', 'sitegrouppoll', 'sitegroupmusic', 'sitegroupvideo', 'sitegroupreview', 'sitestore');

    $finalArray = array_merge($notInclude, $newArray);

    $module_table = Engine_Api::_()->getDbTable('modules', 'core');
    $module_name = $module_table->info('name');
    $select = $module_table->select()
            ->from($module_name, array('name', 'title'))
            ->where($module_name . '.type =?', 'extra')
            ->where($module_name . '.name not in(?)', $finalArray)
            ->where($module_name . '.enabled =?', 1);

    $contentModule = $select->query()->fetchAll();
    $contentModuleArray = array();

    if (!empty($contentModule)) {
      $contentModuleArray[] = '';
      foreach ($contentModule as $modules) {
        $contentModuleArray[$modules['name']] = $modules['title'];
      }
    }

    if (!empty($contentModuleArray)) {
      $this->addElement('Select', 'module', array(
          'label' => 'Content Module',
          'allowEmpty' => false,
          'onchange' => 'setModuleName(this.value)',
          'multiOptions' => $contentModuleArray,
      ));
    } else {
      //VALUE FOR LOGO PREVIEW.
      $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("There are currently no new modules to be added to ‘Manage Module’ section.") . "</span></div>";
      $this->addElement('Dummy', 'module', array(
          'description' => $description,
      ));
      $this->module->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
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
    if (!empty($contentItem)) {
      $this->addElement('Select', 'resource_type', array(
          'label' => 'Database Table Item',
          'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
          //  'required' => true,
          'multiOptions' => $contentItem,
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
          'description' => 'Please enter the Singular Title for content from this module (Ex: Blog, Page, Recipe, Album, etc.). This text will come in places like the popup to see likes on a content,
        etc.',
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
      // Element: execute
      $this->addElement('Button', 'execute', array(
          'label' => 'Save Settings',
          'type' => 'submit',
          'ignore' => true,
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
    } /* else {
      // Element: cancel
      $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'ignore' => true,
      'link' => true,
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
      ));
      } */
  }

  public function getContentItem($moduleName) {

    $mixSettingsTable = Engine_Api::_()->getDbtable('mixsettings', 'sitelike');
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
          if (!in_array($item, $moduleArray))
            $contentItem[$item] = $item . " ";
      }
    }
    return $contentItem;
  }

}