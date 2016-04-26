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
class Sitegroup_Widget_PopularlocationsSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF LOCATION IS DIS-ABLED BY ADMIN
    $locationFieldEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1);
    if ( empty($locationFieldEnable) ) {
      return $this->setNoRender();
    }

    $items_count = $this->_getParam('itemCount', 3);
    $category_id = $this->_getParam('category_id',0);
    // GET SITEGROUP SITEGROUP FOR MOST RATED
    $this->view->sitegroupLocation = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getPopularLocation($items_count,$category_id);
    $this->view->searchLocation = null;
    if ( isset($_GET['sitegroup_location']) && !empty($_GET['sitegroup_location']) )
      $this->view->searchLocation = $_GET['sitegroup_location'];

    //DONT RENDER IF GROUP COUNT ZERO
    if ( !(count($this->view->sitegroupLocation) > 0) ) {
      return $this->setNoRender();
    }
  }

}
?>