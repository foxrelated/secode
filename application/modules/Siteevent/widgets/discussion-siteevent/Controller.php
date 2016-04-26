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
class Siteevent_Widget_DiscussionSiteeventController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET EVENT SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //WHO CAN POST THE DISCUSSION
        $this->view->canPost = Engine_Api::_()->authorization()->isAllowed($siteevent, $viewer, 'topic');

        //GET PAGINATOR
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('siteevent_topic')->getEventTopices($siteevent->getIdentity());

        //DONT RENDER IF NOTHING TO SHOW
        if (($paginator->getTotalItemCount() <= 0 && (!$viewer->getIdentity() || empty($this->view->canPost)))) {
            return $this->setNoRender();
        }

        //ADD COUNT TO TITLE
        if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
            $this->_childCount = $paginator->getTotalItemCount();
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
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}