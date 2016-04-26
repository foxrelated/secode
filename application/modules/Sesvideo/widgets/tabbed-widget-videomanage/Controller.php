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
class Sesvideo_Widget_TabbedWidgetVideomanageController extends Engine_Content_Widget_Abstract {
  public function indexAction() {
    // Default option for tabbed widget
    if (isset($_POST['params']))
      $params = $_POST['params'];
    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
    $this->view->defaultOptionsArray = $defaultOptionsArray = $this->_getParam('manage_video_tabbed_option',array('videos','likedSPvideos','ratedSPvideos','favouriteSPvideos','featuredSPvideos','sponsoredSPvideos','hotSPvideos','watchSPlaterSPvideos','mySPchannels','followedSPchannels','likedSPchannels','favouriteSPchannels','featuredSPchannels','sponsoredSPchannels','hotSPchannels','mySPplaylists','featuredSPplaylists','sponsoredSPplaylists'));
    $this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
    $setting = Engine_Api::_()->getApi('settings', 'core');
    if (!$setting->getSetting('video_enable_chanel', 1)) {
      $check = true;
    } else
      $check = false;
    if (!$is_ajax && is_array($defaultOptionsArray)) {
      $defaultOptions = $arrayOptions = array();
      foreach ($defaultOptionsArray as $key => $defaultValue) {
        if ($check && strpos($defaultValue, 'channels') !== false)
          continue;
        if ($this->_getParam($defaultValue . '_order'))
          $order = $this->_getParam($defaultValue . '_order');
        else
          $order = (999 + $key);
        if ($this->_getParam($defaultValue . '_label', 0))
          $valueData = $this->_getParam($defaultValue . '_label');
        else {
          if ($defaultValue == 'videos')
            $valueData = 'My Videos';
          else if ($defaultValue == 'likedSPvideos')
            $valueData = 'Liked Videos';
          else if ($defaultValue == 'ratedSPvideos')
            $valueData = 'Rated Videos';
          else if ($defaultValue == 'favouriteSPvideos')
            $valueData = 'Favourite Videos';
          else if ($defaultValue == 'featuredSPvideos')
            $valueData = 'Featured Videos';
          else if ($defaultValue == 'sponsoredSPvideos')
            $valueData = 'Sponsored Videos';
          else if ($defaultValue == 'hotSPvideos')
            $valueData = 'Hot Channels';
          else if ($defaultValue == 'watchSPlaterSPvideos')
            $valueData = 'Watch Later Videos';
          else if ($defaultValue == 'mySPchannels')
            $valueData = 'My Channels';
          else if ($defaultValue == 'followedSPchannels')
            $valueData = 'Followed Channels';
          else if ($defaultValue == 'likedSPchannels')
            $valueData = 'Liked Channels';
          else if ($defaultValue == 'favouriteSPchannels')
            $valueData = 'Favourite Channels';
          else if ($defaultValue == 'featuredSPchannels')
            $valueData = 'Featured Channels';
          else if ($defaultValue == 'sponsoredSPchannels')
            $valueData = 'Sponsored Channels';
          else if ($defaultValue == 'hotSPchannels')
            $valueData = 'Hot Channels';
          else if ($defaultValue == 'mySPplaylists')
            $valueData = 'My Playlists';
          else if ($defaultValue == 'featuredSPplaylists')
            $valueData = 'Featured Playlists';
          else if ($defaultValue == 'sponsoredSPplaylists')
            $valueData = 'Sponsored Playlists';
        }
        $arrayOptions[$order] = $valueData . '||' . $defaultValue;
      }
      ksort($arrayOptions);
      $counter = 0;
      foreach ($arrayOptions as $key => $valueOption) {
        $key = explode('||', $valueOption);
        if ($counter == 0)
          $this->view->defaultOpenTab = $defaultOpenTab = $key[1];
        $defaultOptions[$key[1]] = $key[0];
        $counter++;
      }
    }
    if (isset($_GET['openTab']) || $is_ajax) {
      $this->view->defaultOpenTab = $defaultOpenTab = (!empty($_GET['openTab']) && $_GET['openTab'] != 'undefined' ? str_replace('_', 'SP', $_GET['openTab']) : ($this->_getParam('openTab',0) && $this->_getParam('openTab') != 'undefined' ? str_replace('_', 'SP', $this->_getParam('openTab')) : (isset($params['openTab']) ? $params['openTab'] : '' )));
    }
    $this->view->defaultOptions = $defaultOptions = isset($params['defaultOptions']) ? $params['defaultOptions'] : $defaultOptions;
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
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'comment', 'rating', 'by', 'title', 'featuredLabel', 'sponsoredLabel', 'watchLater', 'category', 'description', 'duration'));
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
    $this->view->view_type = $view_type = (isset($_POST['type']) ? $_POST['type'] : (isset($params['view_type']) ? $params['view_type'] : $view_type));
		$this->view->viewTypeStyle = $viewTypeStyle = (isset($_POST['viewTypeStyle']) ? $_POST['viewTypeStyle'] : (isset($params['viewTypeStyle']) ? $params['viewTypeStyle'] : $this->_getParam('viewTypeStyle','fixed')));
    $params = $this->view->params = array('height_list' => $defaultHeightList, 'width_list' => $defaultWidthList,'height_grid' => $defaultHeightGrid, 'width_grid' => $defaultWidthGrid,'width_pinboard' => $defaultWidthPinboard, 'limit_data' => $limit_data, 'openTab' => $defaultOpenTab, 'pagging' => $loadOptionData, 'show_criterias' => $show_criterias, 'view_type' => $view_type,'defaultOptions' => $defaultOptions,'description_truncation_list' => $description_truncation_list, 'title_truncation_list' => $title_truncation_list, 'title_truncation_grid' => $title_truncation_grid,'title_truncation_pinboard'=>$title_truncation_pinboard,'description_truncation_grid'=>$description_truncation_grid,'description_truncation_pinboard'=>$description_truncation_pinboard,'viewTypeStyle' =>$viewTypeStyle);
    $this->view->loadMoreLink = $this->_getParam('openTab') != NULL ? true : false;
    $viewer = Engine_Api::_()->user()->getViewer();
    // custom list grid view options
    $options = array('tabbed' => true, 'paggindData' => true);
    $this->view->optionsListGrid = $options;
    $this->view->widgetName = 'tabbed-widget-videomanage';
    $this->view->widgetType = 'tabbed';
    $this->view->showTabType = $this->_getParam('showTabType', '1');
    $value['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
    // initialize type variable type
    $type = '';
    switch ($defaultOpenTab) {
      case 'videos':
        $popularCol = 'my_videos';
        break;
      case 'likedSPvideos':
        $popularCol = 'liked_videos';
        break;
      case 'ratedSPvideos':
        $popularCol = 'rated_videos';
        break;
      case 'favouriteSPvideos':
        $popularCol = 'favourite_videos';
        break;
      case 'featuredSPvideos':
        $popularCol = 'featured_video';
        break;
      case 'sponsoredSPvideos':
        $popularCol = 'sponsored_video';
        break;
      case 'hotSPvideos':
        $popularCol = 'hot_video';
        break;
      case 'watchSPlaterSPvideos':
        $popularCol = 'watch_later';
        break;
      case 'mySPchannels':
        $popularCol = 'my_channel';
        break;
      case 'followedSPchannels':
        $popularCol = 'follow_chanel';
        break;
      case 'likedSPchannels':
        $popularCol = 'liked_chanel';
        break;
      case 'favouriteSPchannels':
        $popularCol = 'favourite_chanel';
        break;
      case 'featuredSPchannels':
        $popularCol = 'featured_chanel';
        break;
      case 'sponsoredSPchannels':
        $popularCol = 'sponsored_chanel';
        break;
      case 'hotSPchannels':
        $popularCol = 'hot_chanel';
        break;
      case 'mySPplaylists':
        $popularCol = 'my_playlist';
        break;
      case 'featuredSPplaylists':
        $popularCol = 'featured_playlist';
        break;
      case 'sponsoredSPplaylists':
        $popularCol = 'sponsored_playlist';
        break;
      default :
        return $this->setNoRender();
        break;
    }
    if ($popularCol == 'follow_chanel' && $popularCol == 'my_channel')
      $type = 'channel';
    else
      $type = 'video';
			$value['manageVideo'] = true;
    $this->view->type = $type;
    $this->view->tabbed_widget = true;
		$this->view->manageTabbedWidget = true;
    if ($popularCol == 'my_videos') {
      $this->view->my_videos = true;
      $this->view->can_create = Engine_Api::_()->authorization()->getPermission($viewer, 'video', 'create');
      $this->view->quota = $quota = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
      $this->view->can_edit = Engine_Api::_()->authorization()->getPermission($viewer, 'video', 'edit');
      $this->view->can_delete = Engine_Api::_()->authorization()->getPermission($viewer, 'video', 'delete');
      $paginator = Engine_Api::_()->getDbTable('videos', 'sesvideo')->getVideo($value);
    } else if ($popularCol == 'liked_videos') {
      $paginator = Engine_Api::_()->sesvideo()->getLikesContents(array('resource_type' => 'video'));
      $this->view->getVideoItem = 'getVideoItem';
    } else if ($popularCol == 'rated_videos') {
      $paginator = Engine_Api::_()->getDbTable('ratings', 'sesvideo')->getRatedItems(array('resource_type' => 'video'));
      $this->view->getVideoItem = 'getVideoItem';
    } else if ($popularCol == 'favourite_videos') {
			$this->view->getVideoItem = 'getVideoItem';
      $paginator = Engine_Api::_()->getDbTable('favourites', 'sesvideo')->getFavourites(array('resource_type' => 'sesvideo_video'));
    } else if ($popularCol == 'featured_video') {
      $paginator = Engine_Api::_()->getDbTable('videos', 'sesvideo')->getVideo(array_merge($value, array('is_featured' => true)));
    } else if ($popularCol == 'sponsored_video') {
      $paginator = Engine_Api::_()->getDbTable('videos', 'sesvideo')->getVideo(array_merge($value, array('is_sponsored' => true)));
    } else if ($popularCol == 'hot_video') {
      $paginator = Engine_Api::_()->getDbTable('videos', 'sesvideo')->getVideo(array_merge($value, array('is_hot' => true)));
    } else if ($popularCol == 'watch_later') {
      $this->view->getVideoItem = 'getVideoItem';
      $paginator = Engine_Api::_()->getDbTable('watchlaters', 'sesvideo')->getWatchlaterItems(array('resource_type' => 'sesvideo_video'));
    } else if ($popularCol == 'my_channel') {
      $this->view->my_channel = true;
      $this->view->can_edit = $can_edit = Engine_Api::_()->authorization()->getPermission($viewer, 'sesvideo_chanel', 'edit');
      $this->view->can_delete = $can_delete = Engine_Api::_()->authorization()->getPermission($viewer, 'sesvideo_chanel', 'delete');
      $paginator = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels(array('user_id' => $viewer->getIdentity()));
    } else if ($popularCol == 'follow_chanel') {
      $paginator = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels(array('follow_id' => $viewer->getIdentity()));
    } else if ($popularCol == 'liked_chanel') {
      $paginator = Engine_Api::_()->sesvideo()->getLikesContents(array('resource_type' => 'sesvideo_chanel'));
      $this->view->getChanelItem = 'getChanelItem';
    } else if ($popularCol == 'favourite_chanel') {
      $paginator = Engine_Api::_()->getDbTable('favourites', 'sesvideo')->getFavourites(array('resource_type' => 'sesvideo_chanel', 'user_id' => $viewer->getIdentity()));
      $this->view->getChanelItem = 'getChanelItem';
    } else if ($popularCol == 'featured_chanel') {
      $paginator = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels(array('user_id' => $viewer->getIdentity(), 'is_featured' => true));
    } else if ($popularCol == 'sponsored_chanel') {
      $paginator = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels(array('user_id' => $viewer->getIdentity(), 'is_sponsored' => true));
    } else if ($popularCol == 'hot_chanel') {
      $paginator = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels(array('user_id' => $viewer->getIdentity(), 'is_hot' => true));
    } else if ($popularCol == 'my_playlist') {
      $paginator = Engine_Api::_()->getDbTable('playlists', 'sesvideo')->getPlaylistPaginator(array('user' => $viewer->getIdentity()));
      $this->view->view_type = 'playlist';
      $this->view->my_playlist = true;
    } else if ($popularCol == 'featured_playlist') {
      $paginator = Engine_Api::_()->getDbTable('playlists', 'sesvideo')->getPlaylistPaginator(array('user' => $viewer->getIdentity(), 'is_featured' => true));
      $this->view->view_type = 'playlist';
    } else if ($popularCol == 'sponsored_playlist') {
      $paginator = Engine_Api::_()->getDbTable('playlists', 'sesvideo')->getPlaylistPaginator(array('user' => $viewer->getIdentity(), 'is_sponsored' => true));
      $this->view->view_type = 'playlist';
    } else {
      return $this->setNoRender();
    }
    $this->view->paginator = $paginator;
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', $limit_data));
    $this->view->page = $page;
    $paginator->setCurrentPageNumber($page);
    if ($is_ajax)
      $this->getElement()->removeDecorator('Container');
  }

}
