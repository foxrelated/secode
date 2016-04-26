<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_ShowAlsoLikedController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $video_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id', $this->_getParam('video_id', null));
        $siteevent_video = Engine_Api::_()->getItem('siteevent_video', $video_id);

        if (empty($siteevent_video)) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $subject = Engine_Api::_()->getItem('siteevent_event', $siteevent_video->event_id);

        //FETCH VIDEOS
        $params = array();
        $widgetType = 'showalsolike';
        $params['resource_type'] = $siteevent_video->getType();
        $params['resource_id'] = $siteevent_video->getIdentity();
        $params['video_id'] = $siteevent_video->getIdentity();
        $params['view_action'] = 1;
        $params['limit'] = $this->_getParam('itemCount', 3);
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('videos', 'siteevent')->widgetVideosData($params, '', $widgetType);
        $this->view->count_video = Count($paginator);
        $this->view->limit_siteevent_video = $this->_getParam('itemCount', 3);

        if (Count($paginator) <= 0) {
            return $this->setNoRender();
        }
    }

}