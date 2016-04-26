<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_OptionsSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//DON'T RENDER THIS IF NOT AUTHORIZED
    $sitegroup_options = Zend_Registry::isRegistered('sitegroup_options') ? Zend_Registry::get('sitegroup_options') : null;
		if (empty($sitegroup_options)) {
      return $this->setNoRender();
    }

		//DON'T RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET NAVIGATION
    $this->view->gutterNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_gutter');
  }
}

?>