<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesvideo_Widget_OfTheDayController extends Engine_Content_Widget_Abstract {
  public function indexAction() {
    $this->view->type = $type = $this->_getParam('ofTheDayType', 'video');
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if ($type == 'chanel' && !$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
    $this->view->height_grid = $this->view->height = $this->_getParam('height', '180');
    $this->view->width_grid = $this->view->width = $this->_getParam('width', '180');
    $this->view->view_type = 'grid';
		$this->view->viewTypeStyle = $viewTypeStyle = (isset($_POST['viewTypeStyle']) ? $_POST['viewTypeStyle'] : (isset($params['viewTypeStyle']) ? $params['viewTypeStyle'] : $this->_getParam('viewTypeStyle','fixed')));
    $this->view->title_truncation_grid = $this->_getParam('title_truncation', '45');
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'comment', 'rating', 'by', 'title', 'socialSharing', 'view', 'featuredLabel', 'sponsoredLabel', 'hotLabel', 'favouriteButton', 'likeButton'));
    foreach ($show_criterias as $show_criteria)
      $this->view->{$show_criteria . 'Active'} = $show_criteria;
    if ($type == 'video')
      $paginator = Engine_Api::_()->getDbTable('videos', 'sesvideo')->getVideo(array('widgetName' => 'oftheday'));
    elseif ($type == 'chanel')
      $paginator = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels(array('widgetName' => 'oftheday'));
    elseif ($type == 'artist')
      $paginator = Engine_Api::_()->getDbTable('artists', 'sesvideo')->getOfTheDayResults();
    elseif ($type == 'playlist')
      $paginator = Engine_Api::_()->getDbTable('playlists', 'sesvideo')->getOfTheDayResults();
    $this->view->paginator = $paginator;
		$paginator->setItemCountPerPage(1);
    $paginator->setCurrentPageNumber(1);
    if (!($paginator->getTotalItemCount())) {
      return $this->setNoRender();
    }
  }
}