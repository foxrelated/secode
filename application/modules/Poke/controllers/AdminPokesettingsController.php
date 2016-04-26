<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: AdminPokesettingsController.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_AdminPokesettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('poke_admin_main', array(), 'poke_admin_main_pokesettings');

    $this->view->form = $form = new Poke_Form_Admin_Pokesettings();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      if (empty($values['poke_title_turncation'])) {
	$form->addError($this->view->translate('Title *  Please complete this field - it is required.'));
      }

      if (empty($this->viw->error)) {
	foreach ($values as $key => $value) {
	  Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
	}
      }
    }
  }

  public function faqAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('poke_admin_main', array(), 'poke_admin_main_faq');
  }
}
?>