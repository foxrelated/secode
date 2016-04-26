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
class Siteevent_Widget_AboutEditorSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET EVENT SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');

        //EDITOR REVIEW HAS BEEN POSTED OR NOT
        $params = array();
        $params['resource_id'] = $siteevent->event_id;
        $params['resource_type'] = $siteevent->getType();
        $params['viewer_id'] = 0;
        $params['type'] = 'editor';
        $isEditorReviewed = $reviewTable->canPostReview($params);
        if (empty($isEditorReviewed)) {
            return $this->setNoRender();
        }

        //GET USER ID
        $user_id = $reviewTable->getColumnValue($isEditorReviewed, 'owner_id');
        if (empty($user_id)) {
            return $this->setNoRender();
        }

        $siteeventAboutEditor = Zend_Registry::isRegistered('siteeventAboutEditor') ? Zend_Registry::get('siteeventAboutEditor') : null;
        if (empty($siteeventAboutEditor))
            return $this->setNoRender();

        $editor_id = Engine_Api::_()->getDbTable('editors', 'siteevent')->getColumnValue($user_id, 'editor_id');

        $this->view->editor = Engine_Api::_()->getItem('siteevent_editor', $editor_id);
        $this->view->user = Engine_Api::_()->getItem('user', $user_id);
    }

}
