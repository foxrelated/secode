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
class Sesvideo_Widget_FeaturedSponsoredFixedViewController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $value['limit'] = $this->_getParam('limit_data',5);
		$value['type'] = $this->_getParam('featured_sponsored_carosel','all');
    $this->view->categoryItem = $value['category'] = $this->_getParam('category','videos');
		
		$this->view->heightmain  = $this->_getParam('heightMain','450');
		$this->view->height = $this->_getParam('height','150');
		
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if ($value['category'] == 'chanels' && !$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
		$value['show_criterias'] = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria',array('like','comment','rating','by','title','socialSharing','view','videoCount','favouriteCount','featured','sponsored','favouriteButton','likeButton','hot','watchlater','duration'));
		$value['info'] = $this->_getParam('info','recently_created');
		$this->view->title_truncation = $this->_getParam('title_truncation','45');
		foreach($value['show_criterias'] as $show_criteria){
		 if($value['category'] == 'playlists' && $show_criteria == 'hot')
		 	continue;
		 if($value['category'] == 'videos' && $show_criteria == 'videoCount')
		 	continue;
		 if($value['category'] == 'chanels' && $value['category'] == 'playlists' && ($show_criteria == 'duration' || $show_criteria == 'watchlater' ))
		 	continue;
			$this->view->$show_criteria = $show_criteria;
		}
		
		if($value['type'] != 'all'){
			if($value['category'] == 'playlists'){
					if($value['type'] == 'hot' || $value['type'] == 'verified')
						unset($value['type']);
			}else if($value['category'] == 'videos'){
					if($value['type'] == 'verified')
						unset($value['type']);
			}
		}else
			unset($value['type']);
		
		if(isset($value['type']))
			$value['is_'.$value['type']] = 'is_'.$value['type'];
		
		if($value['category'] == 'playlist'){
				if($value['info'] == 'most_rated')
					$value['info'] = 'creation_date';
		}else if($value['category'] == 'videos'){
				if($value['info'] == 'most_video')
					$value['info'] = 'creation_date';
		}
		
		switch($value['info']){
			case 'recently_updated':
				$value['info'] = 'modified_date';
				break;
			case 'most_viewed':
				$value['info'] = 'view_count';
				break;
			case 'most_liked':
				$value['info'] = 'like_count';
				break;
			case 'most_rated':
				$value['info'] = 'rating';
				break;
			case 'most_commented':
				$value['info'] = 'comment_count';
				break;
			case 'most_favourite':
				$value['info'] = 'favourite_count';
				break;
			case 'most_video':
				$value['info'] = 'video_count';
				break;
			default :
				$value['info'] = 'creation_date';
				break;
		}

		
		
		$value['popularCol'] = $value['info'];
		$this->view->align = $align;
	if($value['category'] == 'chanels'){
				if($value['info'] == 'video_count'){
					unset($value['info'] );
					unset($value['popularCol']);
					$value['video_count'] = true;
				}
		}
	if($value['category'] == 'videos')
	  $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('videos', 'sesvideo')->getVideo($value);
	else if($value['category'] == 'chanels')
	  $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels($value);
	else if($value['category'] == 'playlists')
	  $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('videos', 'sesvideo')->getPlaylistSelect($value);
	else
		return $this->setNoRender();
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($value['limit']);
    $paginator->setCurrentPageNumber(1);
		 // Do not render if nothing to show
    if ($paginator->getTotalItemCount() == 0){
      return $this->setNoRender();
		}
	}
}
