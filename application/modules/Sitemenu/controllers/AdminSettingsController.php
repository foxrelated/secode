<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_AdminSettingsController extends Core_Controller_Action_Admin {

  public function indexAction() {
    
    //REMOVE CACHEING OF MAIN MENU WHEN MENU EDITOR IS VISITED ON ADMIN SIDE CONTROL PANEL
    $cache = Zend_Registry::get('Zend_Cache');
    $cache->remove('footer_menu_cache');
    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
    foreach ($levels as $level_id => $level_name) {
      $cache->remove('main_menu_html_for_' . $level_id);
      $cache->remove('main_menu_cache_level_'.$level_id);
    }
    if($this->getRequest()->isPost() && isset($_POST['sitemenu_cache_enable'])){
      Engine_Api::_()->getApi("settings", "core")->setSetting('sitemenu.cache.enable', $_POST['sitemenu_cache_enable']);
      Engine_Api::_()->getApi("settings", "core")->setSetting('sitemenu.cache.lifetime', $_POST['sitemenu_cache_lifetime']);
    }
    
    $this->_forward('editor', 'admin-menu-settings', 'sitemenu', array(
        'viewType' => 'global',
    ));
  }

  public function faqAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitemenu_admin_main', array(), 'sitemenu_admin_main_help');
  }
  
  public function readmeAction() {
  }
}
