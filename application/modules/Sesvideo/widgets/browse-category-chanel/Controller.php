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
class Sesvideo_Widget_BrowseCategoryChanelController extends Engine_Content_Widget_Abstract {
  public function indexAction() {
		if(isset($_GET['category_id']))
			$category_id = ($_GET['category_id']);
		else if(isset($_GET['subcat_id']))
			$category_id = ($_GET['subcat_id']);
		else if(isset($_GET['subsubcat_id']))
			$category_id = ($_GET['subsubcat_id']);
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if (!$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
		$this->view->catgeoryItem = $catgeoryItem = Engine_Api::_()->getItem('sesvideo_category', $category_id);
		 
		if(!$catgeoryItem)
			header('location:'.$this->view->url(array('action' => 'browse'), 'sesvideo_chanel', true));
    // Default option for tabbed widget
    if (isset($_POST['params']))
      $params = json_decode($_POST['params'], true);
    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
    $this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
    $this->view->video_limit = $video_limit = isset($params['video_limit']) ? $params['video_limit'] : $this->_getParam('video_limit', '8');
    $this->view->chanel_limit = $chanel_limit = isset($params['chanel_limit']) ? $params['chanel_limit'] : $this->_getParam('chanel_limit', '8');
    $this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width', '120');
    $this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height', '80');
    $this->view->title_truncation = $title_truncation = isset($params['title_truncation']) ? $params['title_truncation'] : $this->_getParam('title_truncation', '100');
    $this->view->description_truncation = $description_truncation = isset($params['description_truncation']) ? $params['description_truncation'] : $this->_getParam('description_truncation', '150');
    $value['category_id'] = isset($_GET['category_id']) ? $_GET['category_id'] : (isset($params['category_id']) ? $params['category_id'] : '');
    $value['subcat_id'] = isset($_GET['subcat_id']) ? $_GET['subcat_id'] : (isset($params['subcat_id']) ? $params['subcat_id'] : '');
    $value['subsubcat_id'] = isset($_GET['subsubcat_id']) ? $_GET['subsubcat_id'] : (isset($params['subsubcat_id']) ? $params['subsubcat_id'] : '');
    $value['sort'] = isset($_GET['orderby']) ? $_GET['orderby'] : (isset($params['orderby']) ? $params['orderby'] : '');
    $value['search'] = isset($_GET['text']) ? $_GET['text'] : (isset($params['text']) ? $params['text'] : '');
    $value['tag'] = isset($_GET['tag']) ? $_GET['tag'] : (isset($params['tag']) ? $params['tag'] : '');
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('by', 'view', 'title', 'follow', 'followButton', 'featuredLabel', 'sponsoredLabel', 'description', 'chanelPhoto', 'chanelVideo', 'duration', 'watchLater', 'videoCount'));
    foreach ($show_criterias as $show_criteria)
      $this->view->{$show_criteria . 'Active'} = $show_criteria;
    $params = array('height' => $height, 'width' => $width, 'video_limit' => $video_limit, 'pagging' => $loadOptionData, 'show_criterias' => $show_criterias, 'title_truncation' => $title_truncation, 'description_truncation' => $description_truncation, 'chanel_limit' => $chanel_limit, 'category_id' => $value['category_id'], 'subcat_id' => $value['subcat_id'], 'subsubcat_id' => $value['subsubcat_id'], 'sort' => $value['sort'], 'search' => $value['search'], 'tag' => $value['tag']);
    // custom list grid view options
    $searchParams = array();
    $value['limit_data'] = $chanel_limit;
    $this->view->widgetName = 'browse-category-chanel';
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels($value);
    $paginator->setItemCountPerPage($chanel_limit);
    $paginator->setCurrentPageNumber($page);
    $resultArray = array();
    if ($paginator->getTotalItemCount() > 0) {
      if (in_array('chanelVideo', $show_criterias)) {
        foreach ($paginator as $chanelData) {
          $resultArray['videos'][$chanelData->chanel_id] = Engine_Api::_()->getDbTable('chanelvideos', 'sesvideo')->getChanelAssociateVideos($chanelData, array('limit_data' => $video_limit, 'paginator' => false));
        }
      }
    }
    $this->view->resultArray = $resultArray;
    // Get videos
    $this->view->page = $page;
    $this->view->params = $params;
    if ($is_ajax) {
      $this->getElement()->removeDecorator('Container');
    } else {
      // Do not render if nothing to show
      if ($paginator->getTotalItemCount() <= 0) {
        
      }
    }
  }

}
