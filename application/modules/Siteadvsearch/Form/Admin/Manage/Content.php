<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Content.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Form_Admin_Manage_Content extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Add New Content Type')
            ->setDescription('Use the form below to add a content type for enabling users to search content related to these content types across the site.');
    $modules_notInclude = array('sitemember', 'sitemenu', 'Sitecoupon', 'sitecontentcoverphoto', 'siteeventdocument', 'sitereviewpaidlisting', 'siteevent', 'sitefaq', 'sitestaticpage', 'sitereview', 'siteadvsearch', 'sitestoreurl', 'sitestoreintegration', 'sitestoremember', 'sitestorebadge', 'sitestorediscussion', 'sitestorelikebox', 'sitestoreinvite', 'sitestoreform', 'sitestoreadmincontact', 'sitegroupurl', 'sitegroupintegration', 'sitegroupmember', 'sitepagemember', 'siteusercoverphoto', 'sitemobile', 'sitemobileapp', 'sitemailtemplates', 'sitegroupbadge', 'sitegroupdiscussion', 'sitegrouplikebox', 'sitegroupinvite', 'sitegroupform', 'sitegroupadmincontact', 'communityadsponsored', 'sitevideoview', 'sitevideoview', 'sitetagcheckin', 'sitereviewlistingtype', 'sitepageintegration', 'sitepageurl', 'forum', 'sitebusinessdiscussion', 'sitebusinesslikebox', 'sitebusinessinvite', 'sitebusinessform', 'sitebusinessadmincontact', 'mapprofiletypelevel', 'mcard', 'poke', 'sitealbum', 'sitepageinvite', 'siteslideshow', 'seaocore', 'suggestion', 'userconnection', 'sitepageform', 'sitepageadmincontact', 'sitebusinessbadge', 'sitepagebadge', 'featuredcontent', 'sitepagediscussion', 'sitepagelikebox', 'mobi', 'advancedslideshow', 'birthday', 'birthdayemail', 'communityad', 'dbbackup', 'facebookse', 'facebooksefeed', 'facebooksepage', 'feedback', 'sitelike', 'activity', 'advancedactivity', 'album', 'blog', 'classified', 'document', 'event', 'poll', 'video', 'list', 'group', 'music', 'recipe', 'user', 'groupdocument', 'grouppoll', 'sitepage', 'sitepagenote', 'sitepagevideo', 'sitepagepoll', 'sitepagemusic', 'sitepagealbum', 'sitepageevent', 'sitepagereview', 'sitepagedocument', 'sitepageoffer', 'sitebusiness', 'sitebusinessnote', 'sitebusinessvideo', 'sitebusinesspoll', 'sitebusinessmusic', 'sitebusinessalbum', 'sitebusinessevent', 'sitebusinessreview', 'sitebusinessdocument', 'sitebusinessoffer', 'sitegroup', 'sitegroupnote', 'sitegroupvideo', 'sitegrouppoll', 'sitegroupmusic', 'sitegroupalbum', 'sitegroupevent', 'sitegroupreview', 'sitegroupdocument', 'sitegroupoffer', 'sitestore', 'sitestoreproduct', 'sitestorevideo', 'sitestorealbum', 'sitestorereview', 'sitestoredocument', 'sitestoreoffer', 'siteestore', 'eventdocument', 'sitevideo', 'captivate', 'siteforum', 'nestedcomment', 'sitehomepagevideo', 'siteluminous');

    $module_table = Engine_Api::_()->getDbTable('modules', 'core');
    $moduleName = $module_table->info('name');
    $select = $module_table->select()
            ->from($moduleName, array('name', 'title'))
            ->where($moduleName . '.type =?', 'extra')
            ->where($moduleName . '.name not in(?)', $modules_notInclude)
            ->where($moduleName . '.enabled =?', 1);

    $moduleResults = $select->query()->fetchAll();
    $moduleArray = array();
    if (!empty($moduleResults)) {
      foreach ($moduleResults as $modules) {
        $contentItem = $this->getContentItem($modules['name']);
        if (empty($contentItem))
          continue;
        $moduleArray[$modules['name']] = $modules['title'];
      }
    }

    if (!empty($moduleArray)) {
      $this->addElement('Select', 'module_name', array(
          'label' => 'Content Module',
          'allowEmpty' => false,
          'onchange' => 'setModuleName(this.value)',
          'multiOptions' => $moduleArray,
      ));
    } else {
      //VALUE FOR LOGO PREVIEW.
      $description = "<div class='tip'><span>" . "There are
currently no new content types on your website that could be added for Advanced Search Page." . "</span></div>";
      $this->addElement('Dummy', 'module', array(
          'description' => $description,
      ));
      $this->module->addDecorator('Description', array('placement' =>
          Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }

    $module_name = Zend_Controller_Front::getInstance()->getRequest()->getParam('module_name', null);
    $contentItem = array();
    if (!empty($module_name) && $this->module_name) {
      $this->module_name->setValue($module_name);
      if ($module_name == 'sitereviewlistingtype') {
        $resource_type = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', null);
        $contentItem[$resource_type] = $resource_type;
      } else {
        $contentItem = $this->getContentItem($module_name);
      }
    }
    if (!empty($contentItem)) {
      $this->addElement('Select', 'resource_type', array(
          'label' => 'Content Type',
          'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
          //  'required' => true,
          'multiOptions' => $contentItem,
      ));

      $this->addElement('Text', 'resource_title', array(
          'label' => 'Content Title',
          'description' => 'Enter the content title for which you use this module. Ex: You may use the Documents module for ‘Tutorials’ on your community.',
          'required' => true
      ));

      $this->addElement('Checkbox', 'enabled', array(
          'description' => 'Enable for Advanced Search Page',
          'label' => 'Enable this content type to be part of advanced search page.',
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
          'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'siteadvsearch', 'controller' => 'manage'), 'admin_default', true),
          'decorators' => array('ViewHelper'),
      ));

      // DisplayGroup: buttons
      $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
          'decorators' => array(
              'FormElements',
              'DivDivDivWrapper',
          )
      ));
    } else {
      // Element: cancel
      $this->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'ignore' => true,
          'link' => true,
          'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'siteadvsearch', 'controller' => 'manage'), 'admin_default', true),
      ));
    }
  }

  public function getContentItem($moduleName) {

    $file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
    $items = array();
    if (@file_exists($file_path)) {
      $include_file = include $file_path;
      if (isset($include_file['items'])) {

        foreach ($include_file['items'] as $item)
          $items[$item] = $item . " ";
      }
    }
    return $items;
  }

}