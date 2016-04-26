<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminGlobalController.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_AdminGlobalController extends Core_Controller_Action_Admin {

  public function globalAction() {
		if( array_key_exists('submit', $_POST) ){ unset($_POST['submit']); }
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      $update_table = Engine_Api::_()->getDbtable('menuItems', 'core');
      if (!empty($values['suggestion_friend_invite'])) {
        $update_table->update(array('plugin' => 'Invite_Plugin_Menus::canInvite', 'params' => '{"route":"default", "module":"suggestion", "controller":"index", "action":"viewfriendsuggestion"}'), array('name = ?' => 'core_main_invite'));
      } else {
        $update_table->update(array('plugin' => 'Invite_Plugin_Menus::canInvite', 'params' => '{"route":"default","module":"invite"}'), array('name = ?' => 'core_main_invite'));
      }
    }
    if (!empty($_POST['suggestion_controllersettings'])) {
      $_POST['suggestion_controllersettings'] = trim($_POST['suggestion_controllersettings']);
    }
    $suggestion_form_element = array('yahoo_settings_temp', 'sugg_truncate_limit', 'submit', 'suggestion_show_webmail', 'suggestion_friend_invite', 'suggestion_friend_invite_enable', 'seaocore_siteenginessl');
    $this->view->isModsSupport = Engine_Api::_()->suggestion()->isModulesSupport();
    include_once(APPLICATION_PATH . "/application/modules/Suggestion/controllers/license/license1.php");
  }
  
  // Added phrase in language file.
  public function addPhraseAction($phrase) {
    if ($phrase) {
      //file path name
      $targetFile = APPLICATION_PATH . '/application/languages/en/custom.csv';
      if (!file_exists($targetFile)) {
      //Sets access of file
      touch($targetFile);
      //changes permissions of the specified file.
      chmod($targetFile, 0777);
      }
      if (file_exists($targetFile)) {
      $writer = new Engine_Translate_Writer_Csv($targetFile);
      $writer->setTranslations($phrase);
      $writer->write();
      //clean the entire cached data manually
      @Zend_Registry::get('Zend_Cache')->clean();
      }
    }
  }

  //SHOWING THE PLUGIN RELETED QUESTIONS AND ANSWERS
  public function faqAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sugg_admin_main', array(), 'module_suggestion_faq');
  }

  public function readmeAction() {
    
  }

  public function appconfigsAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sugg_admin_main', array(), 'suggestion_admin_global');
  }

}