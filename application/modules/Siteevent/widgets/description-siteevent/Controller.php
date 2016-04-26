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
class Siteevent_Widget_DescriptionSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET LISTING SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        if (empty($this->view->siteevent->body))
            return $this->setNoRender();

        $this->view->showComments = $this->_getParam('showComments', 0);

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

        $showAlways = $this->_getParam('showAlways', 1);
        if ($showAlways < 2) {
            $hasOverview = true;
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1)) {
                $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'siteevent');
                $overview = $tableOtherinfo->getColumnValue($siteevent->getIdentity(), 'overview');
                $hasOverview = !empty($overview);
            }

            //GET EDITOR REVIEW ID
            $params = array();
            $params['resource_id'] = $siteevent->getIdentity();
            $params['resource_type'] = $siteevent->getType();
            $params['type'] = 'editor';

            $editor_review_id = Engine_Api::_()->getDbTable('reviews', 'siteevent')->canPostReview($params);
            //DONT RENDER IF NO REVIEW ID IS EXIST
            $hasReview = !empty($editor_review_id);

            if (!($hasOverview || $hasReview))
                return $this->setNoRender();
        }
    }

}