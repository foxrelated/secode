<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideoview
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2012-06-028 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideoview_Widget_ListPopularVideosController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Should we consider views or comments popular?
    $popularType = $this->_getParam('popularType', 'view');
    if (!in_array($popularType, array('view', 'comment', 'rating'))) {
      $popularType = 'view';
    }
    $this->view->popularType = $popularType;
		$sitevideoview_video_list = Zend_Registry::isRegistered('sitevideoview_video_list') ? Zend_Registry::get('sitevideoview_video_list') : null;
    if ($popularType == 'rating') {
      $this->view->popularCol = $popularCol = 'rating';
    } else {
      $this->view->popularCol = $popularCol = $popularType . '_count';
    }

    // Get paginator
    $table = Engine_Api::_()->getItemTable('video');
    $select = $table->select()
            ->where('search = ?', 1)
            ->order($popularCol . ' DESC');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 4));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Hide if nothing to show
    if (($paginator->getTotalItemCount() <= 0) || empty($sitevideoview_video_list)) {
      return $this->setNoRender();
    }
  }

}