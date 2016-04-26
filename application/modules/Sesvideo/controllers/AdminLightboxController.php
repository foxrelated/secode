<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminLightboxController.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_AdminLightboxController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_lightbox');

    $this->view->form = $form = new Sesvideo_Form_Admin_Lightbox();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();

      if (!$values['sesvideo_private_photo'])
        unset($values['sesvideo_private_photo']);

      if (isset($values['dummy']) || $values['dummy'] == '')
        unset($values['dummy']);

      foreach ($values as $key => $value)
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);

      $form->addNotice('Your changes have been saved.');

      $this->_helper->redirector->gotoRoute(array());
    }
  }

}
