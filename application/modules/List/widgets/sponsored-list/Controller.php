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
class List_Widget_SponsoredListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		$list_sponcerd = Zend_Registry::get('list_sponcerd');

    //GET LIST COUNT
    $totalList = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Total Sponsored List');
    $this->view->totalCount = $totalList->count();
    if ($this->view->totalCount <= 0) {
      return $this->setNoRender();
    }

    //FETCH SPONSERED LISTINGS
    $this->view->listings = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Sponsored List');

		if(empty($list_sponcerd)) {
			return $this->setNoRender();
		}
  }
}