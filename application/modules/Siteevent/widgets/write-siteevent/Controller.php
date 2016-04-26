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
class Siteevent_Widget_WriteSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //GET MODULE NAME
        $module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
        if ($module != 'siteevent') {
            return $this->setNoRender();
        }

        if ($this->_getParam('removeContent', false)) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }

        //GET VIEWER ID
        $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->isOwner = 0;

        //DONT RENDER IF SUBJECT IS NOT SET
        if (Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('siteevent_event');
            $this->view->subjectId = $subject->event_id;

            if ($subject->owner_id == $viewer_id) {
                $this->view->isOwner = 1;
            }

            $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'siteevent');
            $this->view->aboutSubject = $tableOtherinfo->getColumnValue($this->view->subjectId, 'about');
        } elseif (Engine_Api::_()->core()->hasSubject('user')) {

            $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
            $this->view->subjectId = $subject->user_id;
            if ($subject->user_id == $viewer_id) {
                $this->view->isOwner = 1;
            }

            $user_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('user_id', null);
            $editor_id = Engine_Api::_()->getDbTable('editors', 'siteevent')->getColumnValue($user_id, 'editor_id', 0);
            $this->view->editor = $editor = Engine_Api::_()->getItem('siteevent_editor', $editor_id);
            $editor = Engine_Api::_()->getItem('siteevent_editor', $editor_id);
            $this->view->aboutSubject = $editor->about;
        } else {
            return $this->setNoRender();
        }
        
        if(!$this->view->aboutSubject && empty($this->view->isOwner )){
        return $this->setNoRender();
    }
    }

}