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
class Sesvideo_Widget_FeaturedSponsoredController extends Engine_Content_Widget_Abstract {
  public function indexAction() {
    $value['limit'] = $this->_getParam('limit_data', 5);
    $tableName = $this->_getParam('tableName', 'video');
   	$this->view->criteria =  $value['criteria'] = $this->_getParam('criteria', 5);
    $this->view->info = $value['info'] = $this->_getParam('info', 'recently_created');
    $this->view->view_type = $this->_getParam('type','list');
		$this->view->viewTypeStyle = $viewTypeStyle = (isset($_POST['viewTypeStyle']) ? $_POST['viewTypeStyle'] : (isset($params['viewTypeStyle']) ? $params['viewTypeStyle'] : $this->_getParam('viewTypeStyle','fixed')));
    $this->view->{"height_".$this->view->view_type} = $this->_getParam('height', '60');
    $this->view->{"width_".$this->view->view_type} = $this->_getParam('width', '80');
    $show_criterias = $this->_getParam('show_criteria', array('like', 'comment', 'rating', 'by', 'title', 'view','favourite','category','duration','watchLater'));
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if ($tableName == 'chanel' && !$setting->getSetting('video_enable_chanel', 1))
      return $this->setNoRender();
		$this->view->viewTypeStyle = $this->_getParam('viewTypeStyle', 'mouseover');
    foreach ($show_criterias as $show_criteria){
     	if($this->view->view_type == 'list'){
				if($show_criteria == 'socialSharing' || $show_criteria == 'likeButton' || $show_criteria == 'favouriteButton')
					continue;
			}
		  $this->view->{$show_criteria . 'Active'} = $show_criteria;
		}
     $this->view->{"title_truncation_".$this->view->view_type} = $this->_getParam('title_truncation', '45');
    if ($tableName == 'chanel') {
      $this->view->paginator = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels($value);
    } else if ($tableName == 'video') {
      $this->view->paginator = Engine_Api::_()->getDbTable('videos', 'sesvideo')->getVideo($value);
    }
    $this->view->paginator->setItemCountPerPage($value['limit'] );
    $this->view->paginator->setCurrentPageNumber(1);
    if ($this->view->paginator->getTotalItemCount() <= 0)
      return $this->setNoRender();
  }
}