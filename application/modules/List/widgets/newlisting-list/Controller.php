<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Controller.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Widget_NewlistingListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET NAVIGATION
    $this->view->quickNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('list_quick');

		//DONT RENDER IF NO ELEMENT IS THERE
		if(Count($this->view->quickNavigation) <= 0) {
			return $this->setNoRender();
		}
  }

}