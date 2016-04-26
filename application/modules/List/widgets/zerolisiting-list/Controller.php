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
class List_Widget_ZerolisitingListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
		
		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

		//CAN CREATE LISTINGS OR NOT
    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('list_listing', $viewer, 'create');
    $values['type'] = 'browse_home_zero';

    //GET LISTS
    $listings = Engine_Api::_()->getDbTable('listings', 'list')->getListsPaginator($values);

    if (count($listings) > 0) {
      return $this->setNoRender();
    }
  }

}