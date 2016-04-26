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
class Siteevent_Widget_VideoSiteeventController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        if (!$this->_getParam('loaded_by_ajax', false) && Engine_Api::_()->siteevent()->hasPackageEnable() && !Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "video")) {
              return $this->setNoRender();
        }
        
        //GET VIEWER DETAIL
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $this->view->type_video = $type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video');

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        
        //VIDEO IS ENABLED OR NOT
        $allowed_upload_videoEnable = Engine_Api::_()->siteevent()->enableVideoPlugin();
        if (!$allowed_upload_videoEnable) {
            return $this->setNoRender();
        }

        $this->view->title_truncation = $this->_getParam('truncation', 35);
        $this->view->itemCount = $itemCount = $this->_getParam('count', 10);

        //VIDEO TABLE
        $videoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');

        //TOTAL VIDEO COUNT FOR THIS EVENT
        $this->view->count_video = $counter = $videoTable->getEventVideoCount($siteevent->event_id);
        //AUTHORIZATION CHECK
        $this->view->allowed_upload_video = Engine_Api::_()->siteevent()->allowVideo($siteevent, $viewer, $counter);

        //FETCH RESULTS
        $this->view->paginator = Engine_Api::_()->getDbTable('clasfvideos', 'siteevent')->getEventVideos($siteevent->event_id, 1, $type_video);
        $this->view->paginator->setCurrentPageNumber($this->_getParam('page'));
        $this->view->paginator->setItemCountPerPage($itemCount);

        $counter = $this->view->paginator->getTotalItemCount();

        if (empty($counter) && empty($this->view->allowed_upload_video)) {
            return $this->setNoRender();
        }

        //ADD VIDEO COUNT
        if ($this->_getParam('titleCount', false)) {
            $this->_childCount = $counter;
        }

        $params = $this->_getAllParams();
        $this->view->params = $params;
        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                return;
            }
        } 
        
        $this->view->showContent = true;


        $this->view->can_edit = $siteevent->authorization()->isAllowed($viewer, "edit");

        //IS SITEVIDEOVIEW MODULE ENABLED
        $this->view->sitevideoviewEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideoview');
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
