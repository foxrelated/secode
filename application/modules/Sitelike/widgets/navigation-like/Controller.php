<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitelike_Widget_NavigationLikeController extends Seaocore_Content_Widget_Abstract {

  //protected $_navigation ;
  public function indexAction() {

    $this->view->navigation =  Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitelike_main');
  }
}
?>