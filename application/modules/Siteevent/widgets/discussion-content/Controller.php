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
class Siteevent_Widget_DiscussionContentController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    //ACTION FOR FETCHING THE DISCUSSIONS FOR THE EVENTS
    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_topic')) {
            return $this->setNoRender();
        }

        //GET EVENT SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_topic')->getParent();
        $order = $this->_getParam('postorder', 0);
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET VIEWER
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        //SEND TAB ID TO THE TPL
        $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id');
        //GET TOPIC  SUBJECT
        $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();
				$this->view->canEdit = $topic->canEdit();
        //GET SITEEVENT OBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $topic->event_id);

        //WHO CAN POST TOPIC
        $this->view->canPost = $canPost = $siteevent->authorization()->isAllowed($viewer, "topic");

        //INCREASE THE VIEW COUNT
        if (!$viewer || !$viewer_id || $viewer_id != $topic->user_id) {
            $topic->view_count = new Zend_Db_Expr('view_count + 1');
            $topic->save();
        }

        //CHECK WATHCHING
        $isWatching = null;
        if ($viewer->getIdentity()) {
            $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'siteevent');
            $isWatching = $topicWatchesTable->isWatching($siteevent->getIdentity(), $topic->getIdentity(), $viewer_id);
            if (false == $isWatching) {
                $isWatching = null;
            } else {
                $isWatching = (bool) $isWatching;
            }
        }
        $this->view->isWatching = $isWatching;

        //GET POST ID
        $this->view->post_id = $post_id = (int) $this->_getParam('post');

        $table = Engine_Api::_()->getDbtable('posts', 'siteevent');
        $select = $table->select()
                ->where('event_id = ?', $siteevent->getIdentity())
                ->where('topic_id = ?', $topic->getIdentity());

        if ($order == 1) {
            $select->order('creation_date DESC');
        } else {
            $select->order('creation_date ASC');
        }

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber(Zend_Controller_Front::getInstance()->getRequest()->getParam('page'));
        //SKIP TO PAGE OF SPECIFIED POST
//        if (0 != ($post_id = (int) $this->_getParam('post_id')) &&
//                null != ($post = Engine_Api::_()->getItem('siteevent_post', $post_id))) {
//            $icpp = $paginator->getItemCountPerPage();
//            $page = ceil(($post->getPostIndex() + 1) / $icpp);
//            $paginator->setCurrentPageNumber($page);
//        }
//        //USE SPECIFIED PAGE
//        else if (0 != ($page = (int) $this->_getParam('page'))) {
//            $paginator->setCurrentPageNumber($this->_getParam('page'));
//        }

        if ($canPost && !$topic->closed) {
            $this->view->form = $form = new Siteevent_Form_Post_Create();
            $form->populate(array(
                'topic_id' => $topic->getIdentity(),
                'ref' => $topic->getHref(),
                'watch' => ( false == $isWatching ? '0' : '1' ),
            ));
        }
    }

}