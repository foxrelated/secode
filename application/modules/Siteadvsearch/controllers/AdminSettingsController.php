<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_AdminSettingsController extends Core_Controller_Action_Admin {

  public function __call($method, $params) {
    /*
     * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
     * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
     * REMEMBER:
     *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
     *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
     */

    if (!empty($method) && $method == 'Siteadvsearch_Form_Admin_Settings_Global') {
      
    }
    return true;
  }

  public function indexAction() {

    // FOR INSERTING ITEM TYPE VALUE IN CORE SEARCH TABLE DURING ADVANCED SEARCH PLUGIN DISABLE
    $db = Engine_Db_Table::getDefaultAdapter();
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview')) {
      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_search', array('id', 'item_type'))->where('type =?', 'sitereview_listing')->where("(`item_type` NOT LIKE  '%sitereview_listingtype_%')")->limit('1000');
      $coreSearchResults = $select->query()->fetchAll();
      foreach ($coreSearchResults as $result) {
        $itemId = $result['id'];
        $listingtype_id = Engine_Api::_()->getDbtable('listings', 'sitereview')->getListingTypeId($itemId);
        $type = 'sitereview_listingtype_' . $listingtype_id;
        $db->query("UPDATE `engine4_core_search` SET `item_type` = '$type' WHERE `id` = $itemId LIMIT 1;");
      }
    }

    $pluginName = 'siteadvsearch';
    if (!empty($_POST[$pluginName . '_lsettings']))
      $_POST[$pluginName . '_lsettings'] = @trim($_POST[$pluginName . '_lsettings']);
    
    include APPLICATION_PATH . '/application/modules/Siteadvsearch/controllers/license/license1.php';
    $this->view->isModsSupport = Engine_Api::_()->getApi('core', 'siteadvsearch')->isModulesSupport();
  }

  public function faqAction() {

    // GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('siteadvsearch_admin_main', array(), 'siteadvsearch_admin_main_faq');
    $this->view->faq = 1;
  }

  public function readmeAction() {
    
  }

}