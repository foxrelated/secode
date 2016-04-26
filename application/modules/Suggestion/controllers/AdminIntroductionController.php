<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminIntroductionController.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_AdminIntroductionController extends Core_Controller_Action_Admin {

  // Saves the data of the Admin form for Site Introduction
  public function indexAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sugg_admin_main', array(), 'suggestion_introduction');
    $this->view->form = $form = new Suggestion_Form_Admin_Introduction();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      $sugggestion_admin_tab = 'admin_site_introduction';
      if (!empty($values)) {
        $sugg_admin_introduction = $values['sugg_admin_introduction'];
        $sugg_bg_color = $values['sugg_bg_color'];

        // Insert value in core setting table.
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sugg_admin_introduction', $values['sugg_admin_introduction']);
        // Insert bgcolor in "Core_Settings".
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sugg_bg_color', $values['sugg_bg_color']);
        // insert value in "suggestion_introductions"
        $intro_obj = Engine_Api::_()->getItem('suggestion_introduction', 1);
	if( empty($intro_obj) ) {
	   $db = Zend_Db_Table_Abstract::getDefaultAdapter();
	   $db = $db->query("INSERT INTO `engine4_suggestion_introductions` (`introduction_id`, `content`)VALUES ('1', '" . $values['content'] . "');");
	}else {
	  $intro_obj->content = $values['content'];
	  include_once APPLICATION_PATH . '/application/modules/Suggestion/controllers/license/license2.php';
	}
        $this->view->is_msg = 1;
      }
    }
  }

}
?>