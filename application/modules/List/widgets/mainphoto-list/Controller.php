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
class List_Widget_MainphotoListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return $this->setNoRender();
    }

		//GET SUBJECT AND OTHER SETTINGS
    $this->view->list = Engine_Api::_()->core()->getSubject('list_listing');
		$settings_api = Engine_Api::_()->getApi('settings', 'core');
		$this->view->show_featured = $settings_api->getSetting('list.feature.image', 1);
		$this->view->featured_color = $settings_api->getSetting('list.featured.color', '#0cf523');
		$this->view->show_sponsered = $settings_api->getSetting('list.sponsored.image', 1);
		$this->view->sponsored_color = $settings_api->getSetting('list.sponsored.color', '#fc0505');
		
    //GET VIEWER AND CHECK VIEWER CAN EDIT PHOTO OR NOT
    $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->can_edit = $this->view->list->authorization()->isAllowed($viewer, "edit");
  }

}