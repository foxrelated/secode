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
class Sesvideo_Widget_peopleFavouriteItemController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
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
		$this->view->item_id = $param['id'] = $subject->getIdentity();
    $this->view->height = $this->_getParam('height', '48');
    $this->view->width = $this->_getParam('width', '48');
		$this->view->title = $this->getElement()->getTitle();
		$param['type'] = 'sesvideo_'.str_replace('sesvideo_','',$subject->getType());
		$param['resource_id'] = $subject->getIdentity();
		if($subject->getType() == 'video')
    	$this->view->paginator = $paginator = Engine_Api::_()->getDbTable('videos', 'sesvideo')->getFavourite($param);
		else
			$this->view->paginator = $paginator = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getFavourite($param);
		$this->view->data_show = $limit_data = $this->_getParam('limit_data','11');
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($limit_data);
    $paginator->setCurrentPageNumber(1);
		if($this->_getParam('removeDecorator'))
			$this->getElement()->removeDecorator('Container');
    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
  }
}