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

class Sesvideo_Widget_BrowseChanelController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if (!$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
    // Default option for tabbed widget
    if (isset($_POST['params']))
      $params = json_decode($_POST['params'], true);
    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    if (isset($_POST['searchParams']) && $_POST['searchParams'])
      parse_str($_POST['searchParams'], $searchArray);
    $value['category_id'] = isset($searchArray['category_id']) ? $searchArray['category_id'] : (isset($_GET['category_id']) ? $_GET['category_id'] : (isset($params['category_id']) ? $params['category_id'] : ''));
    $value['sort'] = isset($searchArray['sort']) ? $searchArray['sort'] : (isset($_GET['sort']) ? $_GET['sort'] : (isset($params['sort']) ? $params['sort'] : $this->_getParam('sort', 'mostSPliked')));
    $value['subcat_id'] = isset($searchArray['subcat_id']) ? $searchArray['subcat_id'] : (isset($_GET['subcat_id']) ? $_GET['subcat_id'] : (isset($params['subcat_id']) ? $params['subcat_id'] : ''));
    $value['subsubcat_id'] = isset($searchArray['subsubcat_id']) ? $searchArray['subsubcat_id'] : (isset($_GET['subsubcat_id']) ? $_GET['subsubcat_id'] : (isset($params['subsubcat_id']) ? $params['subsubcat_id'] : ''));
    $value['show'] = isset($searchArray['show']) ? $searchArray['show'] : (isset($_GET['show']) ? $_GET['show'] : (isset($params['show']) ? $params['show'] : ''));
    $value['text'] = $text = isset($searchArray['search']) ? $searchArray['search'] : (!empty($params['search']) ? $params['search'] : (isset($_GET['search']) && ($_GET['search'] != '') ? $_GET['search'] : ''));
    if (isset($value['sort']) && $value['sort'] != '') {
      $value['getParamSort'] = str_replace('SP', '_', $value['sort']);
    } else
      $value['getParamSort'] = 'creation_date';
    $value['search'] = 1;
    if (isset($value['getParamSort'])) {
      switch ($value['getParamSort']) {
        case 'most_viewed':
          $value['popularCol'] = 'view_count';
          break;
        case 'most_liked':
          $value['popularCol'] = 'like_count';
          break;
        case 'most_commented':
          $value['popularCol'] = 'comment_count';
          break;
        case 'most_favourite':
          $value['popularCol'] = 'favourite_count';
          break;
        case 'featured':
          $value['popularCol'] = 'is_featured';
          break;
        case 'hot':
          $value['popularCol'] = 'is_hot';
          break;
        case 'sponsored':
          $value['popularCol'] = 'is_sponsored';
          break;
        case 'most_rated':
          $value['popularCol'] = 'rating';
          break;
        case 'verified':
          $value['popularCol'] = 'is_verified';
          break;
        case 'recently_created':
        default:
          $value['popularCol'] = 'creation_date';
          break;
      }
    }
		$this->view->getTypeData = $getTypeData = 'sesvideo_video';
    $value['tag'] = isset($_GET['tag_id']) ? $_GET['tag_id'] : (isset($params['tag_id']) ? $params['tag_id'] : '');
    $this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
    $this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
    $this->view->category_limit = $category_limit = isset($params['category_limit']) ? $params['category_limit'] : $this->_getParam('category_limit', '10');
    $this->view->video_limit = $video_limit = isset($params['video_limit']) ? $params['video_limit'] : $this->_getParam('video_limit', '8');
    $this->view->chanel_limit = $chanel_limit = isset($params['chanel_limit']) ? $params['chanel_limit'] : $this->_getParam('chanel_limit', '8');
    $this->view->count_chanel = $count_chanel = isset($params['count_chanel']) ? $params['count_chanel'] : $this->_getParam('count_chanel', '1');
		$this->view->view_channel_type = $view_channel_type = isset($params['view_channel_type']) ? $params['view_channel_type'] : $this->_getParam('view_channel_type', '1');
    $this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width', '120');
    $this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height', '80');
    $this->view->seemore_text = $seemore_text = isset($params['seemore_text']) ? $params['seemore_text'] : $this->_getParam('seemore_text', '+ See all [category_name]');
    $this->view->allignment_seeall = $allignment_seeall = isset($params['allignment_seeall']) ? $params['allignment_seeall'] : $this->_getParam('allignment_seeall', 'left');
    $criteriaData = isset($params['criteria']) ? $params['criteria'] : $this->_getParam('criteria', 'alphabetical');
    $this->view->title_truncation = $title_truncation = isset($params['title_truncation']) ? $params['title_truncation'] : $this->_getParam('title_truncation', '100');
    $this->view->description_truncation = $description_truncation = isset($params['description_truncation']) ? $params['description_truncation'] : $this->_getParam('description_truncation', '150');
    $value['alphabet'] = isset($_GET['alphabet']) ? $_GET['alphabet'] : (isset($params['alphabet']) ? $params['alphabet'] : '');
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('by', 'view', 'title', 'favourite', 'follow', 'followButton', 'featuredLabel', 'sponsoredLabel', 'hotLabel', 'description', 'chanelPhoto', 'chanelVideo', 'chanelThumbnail', 'videoCount', 'duration', 'watchLater', 'favouriteButton', 'verified', 'likeButton', 'like', 'comment', 'photo'));
   if(is_array($show_criterias)){
		foreach ($show_criterias as $show_criteria)
      $this->view->{$show_criteria . 'Active'} = $show_criteria;
	 }
    $params = array('height' => $height, 'width' => $width, 'category_limit' => $category_limit, 'video_limit' => $video_limit, 'count_chanel' => $count_chanel, 'seemore_text' => $seemore_text, 'allignment_seeall' => $allignment_seeall, 'pagging' => $loadOptionData, 'show_criterias' => $show_criterias, 'title_truncation' => $title_truncation, 'description_truncation' => $description_truncation, 'chanel_limit' => $chanel_limit, 'criteria' => $criteriaData, 'tag' => $value['tag'],'alphabet' => $value['alphabet'],'view_channel_type'=>$view_channel_type);

    $this->view->widgetName = 'browse-chanel';
	if($view_channel_type == 1){
    $this->view->paginatorCategory = $paginatorCategory = Engine_Api::_()->getDbTable('categories', 'sesvideo')->getCategory(array('hasChannel' => true, 'criteria' => $criteriaData, 'chanelDesc' => 'desc'), array('paginator' => 'yes'), $value);
    $paginatorCategory->setItemCountPerPage($category_limit);
    $paginatorCategory->setCurrentPageNumber($page);
    $resultArray = array();
    if ($paginatorCategory->getTotalItemCount() > 0) {
      foreach ($paginatorCategory as $key => $valuePaginator) {
        $chanelDatas = $resultArray['chanel_data'][$valuePaginator->category_id] = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels(array('category_id' => $valuePaginator->category_id, 'limit_data' => $chanel_limit, $value), false, $value);
        if (in_array('chanelVideo', $show_criterias)) {
          foreach ($chanelDatas as $chanelData) {
            $resultArray['videos'][$valuePaginator->category_id] = Engine_Api::_()->getDbTable('chanelvideos', 'sesvideo')->getChanelAssociateVideos($chanelData, array('limit_data' => $video_limit, 'paginator' => false));
            break;
          }
        }
      }
    }
    $this->view->resultArray = $resultArray;
	}else{
		$this->view->paginatorCategory = $paginatorCategory = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels(array($value), true, $value);	
		$paginatorCategory->setItemCountPerPage($chanel_limit);
    $paginatorCategory->setCurrentPageNumber($page);
	}
    // Get videos
    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('video', null, 'create');
    $this->view->page = $page;
    $this->view->params = $params;
    if ($is_ajax) {
      $this->getElement()->removeDecorator('Container');
    } else {
      if (!empty($_GET['tag_id'])) {
        $this->view->tag = Engine_Api::_()->getItem('core_tag', $_GET['tag_id'])->text;
      }
      // Do not render if nothing to show
      if ($paginatorCategory->getTotalItemCount() <= 0) {
        
      }
    }
  }

}
