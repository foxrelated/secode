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
class Siteevent_Widget_DiaryEventsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $params = array();
        $params['orderby'] = $this->_getParam('orderby', 'RAND()');
        $params['limit'] = $this->_getParam('limit', 3);
        $type = $this->_getParam('type', 'none');
        $this->view->title_truncation = $this->_getParam('truncation', 16);
        $this->view->statisticsDiary = $this->_getParam('statisticsDiary', array("entryCount", "viewCount"));
        $siteeventDiaryEvents = Zend_Registry::isRegistered('siteeventDiaryEvents') ? Zend_Registry::get('siteeventDiaryEvents') : null;

        //GET RECENT DIARY ID OF THE VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if ($type != 'none' && empty($viewer_id)) {
            return $this->setNoRender();
        }

        //FETCH FRIENDS DIARY
        $this->view->friendsDiaries = array();
        if ($type == 'friends') {
            $params['owner_ids'] = $viewer->membership()->getMembershipsOfIds();
            if (empty($params['owner_ids'])) {
                return $this->setNoRender();
            }
        } elseif ($type == 'viewer') {
            $params['owner_ids'] = array("$viewer_id");
        }

        //FETCH DIARIES
        $this->view->diaries = Engine_Api::_()->getDbtable('diaries', 'siteevent')->getBrowseDiaries($params);

        if (empty($siteeventDiaryEvents))
            return $this->setNoRender();

        if ((Count($this->view->diaries) <= 0)) {
            return $this->setNoRender();
        }
    }

}
