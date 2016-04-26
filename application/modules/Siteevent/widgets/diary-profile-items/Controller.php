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
class Siteevent_Widget_DiaryProfileItemsController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_diary')) {
            return $this->setNoRender();
        }

        $this->view->isAjax = $this->_getParam('isAjax', false);
        if ($this->view->isAjax) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }

        //GET SETTINGS
        $this->view->params = $params = $this->_getAllParams();
        $this->view->statisticsDiary = $this->_getParam('statisticsDiary', array("entryCount", "viewCount"));
        $this->view->truncationDescription = $this->_getParam('truncationDescription', 100);
        $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);
        $this->view->itemWidth = $this->view->params['itemWidth'] = $this->_getParam('itemWidth', 220);
        $this->view->postedby = $this->_getParam('postedby', 1);
//    $this->view->postedbyInList = $this->_getParam('postedbyInList', 1);
        $this->view->itemCount = $params['itemCount'] = $itemCount = $this->_getParam('itemCount', 10);
        $params['orderby'] = $this->view->params['orderby'] = $this->_getParam('orderby', 'date');
        $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
        if (!$this->view->isAjax) {
            $this->view->shareOptions = $this->_getParam('shareOptions', array("siteShare", "friend", "report", "print", "socialShare"));
        }
        $this->view->params = $params;

        //GET SUBJECT
        $this->view->diary = $diary = Engine_Api::_()->core()->getSubject('siteevent_diary');

        //GET VIEWER INFO
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $this->view->level_id = $level_id = $viewer->level_id;
        } else {
            $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        //GET LEVEL SETTING
        $this->view->can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_diary', "create");

        if (!$this->view->isAjax) {
            //SHOW MESSAGE OWNER LINK TO USER IF MESSAGING IS ENABLED FOR THIS LEVEL
            $showMessageOwner = 0;
            $showMessageOwner = Engine_Api::_()->authorization()->getPermission($level_id, 'messages', 'auth');
            if ($showMessageOwner != 'none') {
                $showMessageOwner = 1;
            }

            //RETURN IF NOT AUTHORIZED
            $this->view->messageOwner = 1;
            if ($diary->owner_id == $viewer_id || empty($viewer_id) || empty($showMessageOwner)) {
                $this->view->messageOwner = 0;
            }
        }

        $params['eventType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('eventType', null);
        if (empty($contentType)) {
            $params['eventType'] = $this->_getParam('eventType', 'All');
        }
        $this->view->contentType = $params['eventType'];

        //FETCH RESULTS
        $this->view->paginator = Engine_Api::_()->getDbTable('diarymaps', 'siteevent')->diaryEvents($diary->diary_id, $params);
        $this->view->paginator->setItemCountPerPage($itemCount);
        $this->view->paginator->setCurrentPageNumber($this->_getParam('currentpage', 1));
        $this->view->total_item = $this->view->paginator->getTotalItemCount();

        $this->view->show_buttons = $this->_getParam('show_buttons', array("diary", "comment", "like", 'share', 'facebook', 'twitter', 'pinit'));
        $this->view->statistics = $this->_getParam('eventInfo', array("likeCount", "reviewCount", "memberCount"));
    }

}
