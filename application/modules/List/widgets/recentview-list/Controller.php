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
class List_Widget_RecentviewListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//GET LISTINGS
    $this->view->listings = Engine_Api::_()->getDbtable('listings', 'list')->getRecentViewedListings();

    //DONT RENDER IF LIST COUNT IS ZERO
    if (count($this->view->listings) <= 0) {
      return $this->setNoRender();
    }
  }

}