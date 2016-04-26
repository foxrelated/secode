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

class Sesvideo_Widget_ProfileVideosController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (empty($_POST['is_ajax'])) {
      // Don't render this if not authorized
      if (!Engine_Api::_()->core()->hasSubject()) {
        return $this->setNoRender();
      }
      // Get subject and check auth
      $subject = Engine_Api::_()->core()->getSubject();
      if (!$subject->authorization()->isAllowed($viewer, 'view')) {
        return $this->setNoRender();
      }
    }
    // Default option for widget
    if (isset($_POST['params']))
      $params = $_POST['params'];
    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
    $this->view->defaultOptionsArray = $defaultOptionsArray = $this->_getParam('search_type',array('mySPvideo','mySPchanel','mySPplaylist'));
    $this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
    $this->view->widgetType = 'tabbed';
		$setting = Engine_Api::_()->getApi('settings', 'core');
    if (!$is_ajax && is_array($defaultOptionsArray)) {
      $defaultOptions = $arrayOptions = array();
      foreach ($defaultOptionsArray as $key => $defaultValue) {
				if ($defaultValue == 'mySPchanel' && !$setting->getSetting('video_enable_chanel', 1)) {
					continue;
				}
        if ($this->_getParam($defaultValue . '_order'))
          $order = $this->_getParam($defaultValue . '_order') . '||' . $defaultValue;
        else
          $order = (999 + $key) . '||' . $defaultValue;
        if ($this->_getParam($defaultValue . '_label'))
          $valueLabel = $this->_getParam($defaultValue . '_label');
        else {
          if ($defaultValue == 'mySPvideo')
            $valueLabel = 'Videos';
          else if ($defaultValue == 'mySPchanel')
            $valueLabel = 'Chanels';
          else if ($defaultValue == 'mySPplaylist')
            $valueLabel = 'Playlist';
        }
        $arrayOptions[$order] = $valueLabel;
      }
      ksort($arrayOptions);
      $counter = 0;
      foreach ($arrayOptions as $key => $valueOption) {
        $key = explode('||', $key);
        if ($counter == 0)
          $this->view->defaultOpenTab = $defaultOpenTab = $key[1];
        $defaultOptions[$key[1]] = $valueOption;
        $counter++;
      }
      $this->view->defaultOptions = $defaultOptions;
    }

    if (isset($_GET['openTab']) || $is_ajax) {
      $this->view->defaultOpenTab = $defaultOpenTab = (isset($_GET['openTab']) && $_GET['openTab'] != 'undefined'  ? $_GET['openTab'] : ($this->_getParam('openTab') != NULL && $_GET['openTab'] != 'undefined' ? $this->_getParam('openTab') : (isset($params['openTab']) ? $params['openTab'] : '' )));
    }
    
		 $this->view->height_list = $defaultHeightList = isset($params['height_list']) ? $params['height_list'] : $this->_getParam('height_list','160');
    $this->view->width_list = $defaultWidthList = isset($params['width_list']) ? $params['width_list'] : $this->_getParam('width_list','140');
		$this->view->height_grid = $defaultHeightGrid = isset($params['height_grid']) ? $params['height_grid'] : $this->_getParam('height_grid','160');
    $this->view->width_grid = $defaultWidthGrid = isset($params['width_grid']) ? $params['width_grid'] : $this->_getParam('width_grid','140');
		$this->view->width_pinboard = $defaultWidthPinboard = isset($params['width_pinboard']) ? $params['width_pinboard'] : $this->_getParam('width_pinboard','300');
    $this->view->limit_data = $limit_data = isset($params['limit_data']) ? $params['limit_data'] : $this->_getParam('limit_data', '10');
    $this->view->limit = ($page - 1) * $limit_data;
     $this->view->title_truncation_list = $title_truncation_list = isset($params['title_truncation_list']) ? $params['title_truncation_list'] : $this->_getParam('title_truncation_list', '100');
    $this->view->title_truncation_grid = $title_truncation_grid = isset($params['title_truncation_grid']) ? $params['title_truncation_grid'] : $this->_getParam('title_truncation_grid', '100');
		$this->view->title_truncation_pinboard = $title_truncation_pinboard = isset($params['title_truncation_pinboard']) ? $params['title_truncation_pinboard'] : $this->_getParam('title_truncation_pinboard', '100');
    $this->view->description_truncation_list = $description_truncation_list = isset($params['description_truncation_list']) ? $params['description_truncation_list'] : $this->_getParam('description_truncation_list', '100');
		$this->view->description_truncation_grid = $description_truncation_grid = isset($params['description_truncation_grid']) ? $params['description_truncation_grid'] : $this->_getParam('description_truncation_grid', '100');
		$this->view->description_truncation_pinboard = $description_truncation_pinboard = isset($params['description_truncation_pinboard']) ? $params['description_truncation_pinboard'] : $this->_getParam('description_truncation_pinboard', '100');
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'comment', 'rating', 'by', 'title', 'featuredLabel', 'sponsoredLabel', 'watchLater', 'category', 'description', 'duration', 'hotLabel', 'favouriteButton', 'playlistAdd', 'likeButton', 'socialSharing', 'view'));
    foreach ($show_criterias as $show_criteria)
      $this->view->{$show_criteria . 'Active'} = $show_criteria;
    if (!$is_ajax) {
			$this->view->bothViewEnable = false;
      $this->view->optionsEnable = $optionsEnable = $this->_getParam('enableTabs', array('list', 'grid', 'pinboard'));
      $view_type = $this->_getParam('openViewType', 'list');
      if (count($optionsEnable) > 1) {
        $this->view->bothViewEnable = true;
      }
    }
    if (empty($_POST['is_ajax'])) {
      if ($subject->user_id != $viewer->getIdentity()) {
        $userObject = Engine_Api::_()->getItem('user', $subject->user_id);
        $profile = 'other';
        $userId = $subject->user_id;
      } else {
        $userObject = Engine_Api::_()->getItem('user', $viewer->getIdentity());
        $profile = 'own';
        $userId = $viewer->getIdentity();
      }
    } else
      $userId = $params['identityObject'];
    $this->view->view_type = $view_type = (isset($_POST['type']) ? $_POST['type'] : (isset($params['view_type']) ? $params['view_type'] : $view_type));
		$this->view->viewTypeStyle = $viewTypeStyle = (isset($_POST['viewTypeStyle']) ? $_POST['viewTypeStyle'] : (isset($params['viewTypeStyle']) ? $params['viewTypeStyle'] : $this->_getParam('viewTypeStyle','fixed')));
    $params = $this->view->params = array('height_list' => $defaultHeightList, 'width_list' => $defaultWidthList,'height_grid' => $defaultHeightGrid, 'width_grid' => $defaultWidthGrid,'width_pinboard' => $defaultWidthPinboard,  'limit_data' => $limit_data, 'openTab' => $defaultOpenTab, 'pagging' => $loadOptionData, 'show_criterias' => $show_criterias, 'view_type' => $view_type,'description_truncation_list' => $description_truncation_list, 'title_truncation_list' => $title_truncation_list, 'title_truncation_grid' => $title_truncation_grid,'title_truncation_pinboard'=>$title_truncation_pinboard,'description_truncation_grid'=>$description_truncation_grid,'description_truncation_pinboard'=>$description_truncation_pinboard, 'identityObject' => $userId,'viewTypeStyle' =>$viewTypeStyle);
    $this->view->loadMoreLink = $this->_getParam('openTab') != NULL ? true : false;
    if (empty($_POST['is_ajax'])) {
      // owner type
      if ($profile == 'own') {
        $this->view->profile = 'own';
      } else {
        $name = explode(' ', $userObject->displayname);
        if (isset($name[0]))
          $name = ucfirst($name[0]);
        else
          $name = ucfirst($name[1]);
        $this->view->profile = $name;
      }
    }
		$this->view->loadJs = true;
    // custom list grid view options
    $options = array('profileTabbed' => true, 'paggindData' => true);
    $this->view->optionsListGrid = $options;
    $this->view->widgetName = 'profile-videos';
    $this->view->showTabType = $this->_getParam('showTabType', '1');
    // initialize type variable type
    $type = '';
    switch ($defaultOpenTab) {
      case 'mySPvideo':
        $popularCol = 'my_videos';
        break;
      case 'mySPchanel':
        $popularCol = 'my_channel';
        break;
      case 'mySPplaylist':
        $popularCol = 'my_playlist';
        break;
			default :
				return $this->setNoRender();
    }
    $this->view->type = $type;
    if ($popularCol == 'my_videos') {
      $this->view->my_videos = true;
      $this->view->can_create = false;
      //$this->view->quota = $quota = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
      $this->view->can_edit = false;
      $this->view->can_delete = false;
      $paginator = Engine_Api::_()->getDbTable('videos', 'sesvideo')->getVideo(array('user_id' => $userId));
    } else if ($popularCol == 'my_channel') {
      $this->view->my_channel = true;
      $this->view->can_edit = $can_edit = false;
      $this->view->can_delete = $can_delete = false;
      $paginator = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels(array('user_id' => $userId));
    } else if ($popularCol == 'my_playlist') {
      $paginator = Engine_Api::_()->getDbTable('playlists', 'sesvideo')->getPlaylistPaginator(array('user' => $userId));
      $this->view->view_type = 'playlist';
    }else{
				return $this->setNoRender();
		}
    $this->view->tabbed_widget = true;
    $this->view->paginator = $paginator;
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', $limit_data));
    $this->view->page = $page;
    $paginator->setCurrentPageNumber($page);
    if ($is_ajax)
      $this->getElement()->removeDecorator('Container');
    else {
      // Do not render if nothing to show
      if ($paginator->getTotalItemCount() <= 0) {
        $checkVideoCount = Engine_Api::_()->getDbTable('videos', 'sesvideo')->countVideos();
        if ($checkVideoCount->getTotalItemCount() <= 0) {
          return $this->setNoRender();
        }
      }
    }
  }

}
