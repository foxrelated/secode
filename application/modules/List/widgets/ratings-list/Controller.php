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
class List_Widget_RatingsListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
 
		//DON'T RENDER IF SUBJECT IS NOT SET
		if(!Engine_Api::_()->core()->hasSubject('list_listing')) {
			return $this->setNoRender();
		}

		//GET FAQ SUBJECT
		$this->view->list = $list = Engine_Api::_()->core()->getSubject();
		if(empty($list)) {
			return $this->setNoRender();
		}

		//GET VIEWER DETAIL
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->viewer_id = $viewer_id = $viewer->getIdentity();

		if(empty($viewer_id) && $list->rating <= 0) {
			return $this->setNoRender();
		}

		//GET RATING TABLE
		$tableRating = Engine_Api::_()->getDbTable('ratings', 'list');
    $this->view->rating_count = $tableRating->countRating($list->getIdentity());
    $this->view->list_rated = $tableRating->isRated($list->getIdentity(), $viewer_id);
  }
}