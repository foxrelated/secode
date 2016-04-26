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

class Sesvideo_Widget_ShowSameTagsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Check subject
    // Check subject
    if (!Engine_Api::_()->core()->hasSubject('video') && !Engine_Api::_()->core()->hasSubject('sesvideo_chanel')) {
      return $this->setNoRender();
    }
		if(Engine_Api::_()->core()->hasSubject('video'))
   	 $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('video');
		else{
				$setting = Engine_Api::_()->getApi('settings', 'core');
				if (!$setting->getSetting('video_enable_chanel', 1)) {
					return $this->setNoRender();
				}
			 $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sesvideo_chanel');
		}
    // Set default title
    if (!$this->getElement()->getTitle()) {
      $this->getElement()->setTitle('Similar '.ucwords(str_replace('sesvideo_','',$subject->getType())));
    }

    // Get tags for this video
    $itemTable = Engine_Api::_()->getItemTable($subject->getType());
    $tagMapsTable = Engine_Api::_()->getDbtable('tagMaps', 'core');
    $tagsTable = Engine_Api::_()->getDbtable('tags', 'core');

    // Get tags
    $tags = $tagMapsTable->select()
            ->from($tagMapsTable, 'tag_id')
            ->where('resource_type = ?', $subject->getType())
            ->where('resource_id = ?', $subject->getIdentity())
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
		
    // No tags
    if (empty($tags)) {
      return $this->setNoRender();
    }
		$value['limit'] = $this->_getParam('limit_data', 5);
    $tableName = $this->_getParam('tableName', 'video');
		$this->view->view_type = $this->_getParam('type','list');
		$this->view->viewTypeStyle = $this->_getParam('viewTypeStyle', 'mouseover');
		$this->view->viewTypeStyle = $viewTypeStyle = (isset($_POST['viewTypeStyle']) ? $_POST['viewTypeStyle'] : (isset($params['viewTypeStyle']) ? $params['viewTypeStyle'] : $this->_getParam('viewTypeStyle','fixed')));
    $this->view->{"height_".$this->view->view_type} = $this->_getParam('height', '60');
    $this->view->{"width_".$this->view->view_type} = $this->_getParam('width', '80');
    // Get likes		
     $show_criterias = $this->_getParam('show_criteria', array('like', 'comment', 'rating', 'by', 'title', 'view','favourite','category','duration','watchLater'));
		if(is_array($show_criterias)){
			foreach($show_criterias as $show_criteria)
				$this->view->{$show_criteria . 'Active'} = $show_criteria;
		}
      $this->view->{"title_truncation_".$this->view->view_type} = $this->_getParam('title_truncation', '45');

    if ($tableName == 'chanel') {
      $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels(array('sameTagresource_id'=>$subject->getIdentity(),'sameTagTag_id'=>$tags,'sameTag'=>'sameTag'));
    } else if ($tableName == 'video') {
      $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('videos', 'sesvideo')->getVideo(array('sameTagresource_id'=>$subject->getIdentity(),'sameTagTag_id'=>$tags,'sameTag'=>'sameTag'));
    }
    // Get paginator
    $this->view->paginator = $paginator ;

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($value['limit']);
    $paginator->setCurrentPageNumber(1);

    // Hide if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }
  }

}
