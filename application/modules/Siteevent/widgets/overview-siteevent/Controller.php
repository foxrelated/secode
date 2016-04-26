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
class Siteevent_Widget_OverviewSiteeventController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET EVENT SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        $this->view->showComments = $this->_getParam('showComments', 0);

        //GET EDITOR REVIEW ID
        $params = array();
        $params['resource_id'] = $siteevent->event_id;
        $params['resource_type'] = $siteevent->getType();
        $params['type'] = 'editor';
        $showAfterEditorReview = $this->_getParam('showAfterEditorReview', 1);

        if (Engine_Api::_()->siteevent()->hasPackageEnable() && !Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "overview")) {
          return $this->setNoRender();
        }
    if ($showAfterEditorReview < 2) {
            $editor_review_id = Engine_Api::_()->getDbTable('reviews', 'siteevent')->canPostReview($params);

            //DONT RENDER IF NO REVIEW ID IS EXIST
            if (empty($editor_review_id) || empty($showAfterEditorReview)) {
                return $this->setNoRender();
            }
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

                $this->view->showContent = true;
            }
        } else {
            $this->view->showContent = true;
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1)) {
            return $this->setNoRender();
        }

        //GET VIEWER DETAIL
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'siteevent');
        $this->view->overview = $overview = $tableOtherinfo->getColumnValue($siteevent->getIdentity(), 'overview');

        if (empty($overview) && !$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->setNoRender();
        }

        if (empty($overview) && !$siteevent->authorization()->isAllowed($viewer, 'overview')) {
            return $this->setNoRender();
        }
    }

}