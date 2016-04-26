<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Add.php 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Form_Admin_Content_Add extends Engine_Form {

  public function init() {

    //GENERAL HEADING
    $this
            ->setTitle('Add New Module for Check-ins')
            ->setDescription('Use the form below to configure content from a module of your site to enable users to check-in into its content from its view page. Start by selecting a content module, and then entering the various database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.');

    //THESE ARE THE MODULE WHICH ARE NOT INCLUDIN FOR THE CHECKIN
    $notIncludeInCheckIN = array('activity', 'advancedactivity', 'sitelike', 'sitepagebadge', 'featuredcontent', 'sitepagelikebox', 'mobi', 'advancedslideshow', 'birthday', 'birthdayemail', 'communityad', 'dbbackup', 'facebookse', 'facebooksefeed', 'facebooksepage', 'feedback', 'groupdocument', 'grouppoll', 'mapprofiletypelevel', 'sitepageurl', 'mcard', 'poke', 'sitepageinvite', 'siteslideshow', 'seaocore', 'suggestion', 'userconnection', 'sitepagegeolocation', 'sitepageintegration', 'sitepagetwitter', 'sitepageform', 'sitepageadmincontact', 'sitebusinessbadge', 'sitebusinesslikebox', 'sitebusinessinvite', 'sitebusinessform', 'sitebusinessadmincontact', 'sitetagcheckin',
    'sitegroupbadge', 'sitegrouplikebox','sitegroupgeolocation','sitegroupintegration', 'sitegrouptwitter', 'sitegroupform', 'sitegroupadmincontact', 'sitegroupurl','sitegroupinvite', 'sitestoreurl', 'sitestoreadmincontact', 'siteusercoverphoto', 'sitestorelikebox', 'sitestoreinvite', 'sitestoreform','sitebusinessurl','sitepagemember', 'sitebusinessmember', 'sitegroupmember', 'sitemobile', 'sitemobileapp', 'siteevent', 'siteeventrepeat', 'siteeventdocument', 'siteeventadmincontact', 'siteeventemail', 'sitecontentcoverphoto', 'sitemailtemplates', 'sitereview', 'sitereviewlistingtype', 'siteestore');

    //THESE ARE THE MODULE WHICH ARE INCLUDING FOR THE CHECKIN
    $includeInCheckIN = array('album', 'blog', 'classified', 'document', 'event', 'forum', 'poll', 'video', 'list', 'group', 'music', 'recipe', 'sitepage', 'sitepagenote', 'sitepagevideo', 'sitepagepoll', 'sitepagemusic', 'sitepagealbum', 'sitepageevent', 'sitepagereview', 'sitepagedocument', 'sitebusiness',
        'sitebusinessalbum', 'sitebusinessdocument', 'sitebusinessevent', 'sitebusinessnote', 'sitebusinesspoll', 'sitebusinessmusic', 'sitebusinessvideo', 'sitebusinessreview', 'sitepageoffer', 'sitepagediscussion', 'sitebusinessoffer', 'sitebusinessdiscussion', 'sitealbum', 'sitegroup', 'sitegroupnote', 'sitegroupvideo', 'sitegrouppoll', 'sitegroupmusic', 'sitegroupalbum', 'sitegroupevent', 'sitegroupreview', 'sitegroupdocument', 'sitegroupoffer', 'sitegroupdiscussion', 'sitestore', 'sitestorevideo', 'sitestorealbum', 'sitestorereview', 'sitestoreoffer', 'sitestoreproduct' );

    //FINAL CHECKIN ARRAY 
    $finalArray = array_merge($notIncludeInCheckIN, $includeInCheckIN);

    //GET CORE MODULE TABLE
    $moduleTable = Engine_Api::_()->getDbTable('modules', 'core');

    //GET CORE MODULE TABLE NAME
    $moduleTableName = $moduleTable->info('name');

    //SELECTING THE MODULE TITLE AND NAME
    $select = $moduleTable->select()
            ->from($moduleTableName, array('name', 'title'))
            ->where($moduleTableName . '.type =?', 'extra')
            ->where($moduleTableName . '.name not in(?)', $finalArray)
            ->where($moduleTableName . '.enabled =?', 1);
    $contentModule = $select->query()->fetchAll();

    //MAKING THE CHECK-IN CONTENT ARRAY
    $contentModuleArray = array();
    if (!empty($contentModule)) {
      $contentModuleArray[] = '';
      foreach ($contentModule as $modules) {
        $contentModuleArray[$modules['name']] = $modules['title'];
      }
    }

    //GET CONENT ID
    $content_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id', null);

    if (!empty($content_id)) {
      $contentModuleArray = array();
      //GET CONTENT ROW
      $getContentRow = Engine_Api::_()->getDbTable('contents', 'sitetagcheckin')->getContentInformation(array('content_id' => $content_id));

      if (!empty($getContentRow)) {
        $contentModuleArray[$getContentRow->module] = $getContentRow->module;
      }
    }

    //CHECK THERE IS ANY MODULE ADDED FOR THE CHECKIN OR NOT
    if (!empty($contentModuleArray)) {
      $this->addElement('Select', 'module', array(
          'label' => 'Content Module',
          'allowEmpty' => false,
          'onchange' => 'setModuleName(this.value)',
          'multiOptions' => $contentModuleArray,
      ));
    } else {
      $this->addElement('Dummy', 'module', array(
          'description' => "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("There are currently no new modules to be added to ‘Manage Module’ section.") . "</span></div>",
      ));
      $this->module->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }

    //GET MODULENAME
    $module_name = Zend_Controller_Front::getInstance()->getRequest()->getParam('module_name', null);

    //CHECK THERE IS ITEM DEFINED FOR MODULE OR NOT
    $contentItem = array();
    if (!empty($module_name)) {
      $this->module->setValue($module_name);
      $contentItem = Engine_Api::_()->getDbtable('contents', 'sitetagcheckin')->getContentItem($module_name, $content_id);

      if (empty($contentItem))
        $this->addElement('Dummy', 'dummy_title', array(
            'description' => 'For this module, there is  no item defined in the manifest file.',
        ));
    }

    //MAKE ELEMENT OF RESOURCE TYPE
    if (!empty($contentItem)) {
      $this->addElement('Select', 'resource_type', array(
          'label' => 'Database Table Item',
          'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
          'multiOptions' => $contentItem,
      ));

      //ENABLED CHECKIN FOR CONTENT
      $this->addElement('Checkbox', 'enabled', array(
          'description' => 'Enable Module for Check-ins',
          'label' => 'Make content from this module available to users to check-in into it.',
          'value' => 1
      ));

      //EXECUTE
      $this->addElement('Button', 'execute', array(
          'label' => 'Save Settings',
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array('ViewHelper'),
      ));

      //CANCEL
      $this->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'prependText' => ' or ',
          'ignore' => true,
          'link' => true,
          'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'manage', 'action' => 'index')),
          'decorators' => array('ViewHelper'),
      ));
    }
  }

}