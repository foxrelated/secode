<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreadmincontact
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminMailsettingsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreadmincontact_AdminMailsettingsController extends Core_Controller_Action_Admin {

  //ACTION FOR MAIL SETTINGS
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestoreadmincontact');    
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreadmincontact_admin_main', array(), 'sitestoreadmincontact_admin_main_mailsettings');
  }

}

?>