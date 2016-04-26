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
class List_Widget_PopularlocationListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF LOCATION IS DIS-ABLED BY ADMIN
    $locationFieldEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.locationfield', 1);
    if ( empty($locationFieldEnable) ) {
      return $this->setNoRender();
    }

    $items_count = $this->_getParam('itemCount', 5);

    //GET LIST LIST FOR MOST RATED
    $this->view->listLocation = Engine_Api::_()->getDbTable('listings', 'list')->getPopularLocation($items_count);

    //DONT RENDER IF LIST COUNT IS ZERO
    if (!(count($this->view->listLocation) > 0)) {
      return $this->setNoRender();
    }

    $this->view->searchLocation = null;
    if (isset($_GET['list_location']) && !empty($_GET['list_location'])) {
      $this->view->searchLocation = $_GET['list_location'];
		}
  }

}
