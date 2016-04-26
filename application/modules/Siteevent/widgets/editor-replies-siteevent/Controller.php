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
class Siteevent_Widget_EditorRepliesSiteeventController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('user')) {
            return $this->setNoRender();
        }

        $user = Engine_Api::_()->core()->getSubject();

        //GET SEARCHING PARAMETERS
        $this->view->page = $params['page'] = $this->_getParam('page', 1);
        $this->view->itemCount = $params['per_page'] = $this->_getParam('itemCount', 5);
        $this->view->truncation = $this->_getParam('truncation', 60);

        $this->view->replies = $this->getAllCommentsByUserPaginator($user, $params);
        $this->view->replyCount = $this->view->replies->getTotalItemCount();
        $siteeventEditorReplies = Zend_Registry::isRegistered('siteeventEditorReplies') ? Zend_Registry::get('siteeventEditorReplies') : null;

        if (empty($siteeventEditorReplies) || empty($this->view->replyCount))
            return $this->setNoRender();

        $this->view->is_ajax = $this->_getParam('is_ajax', 0);

        if (!empty($this->view->is_ajax)) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }
        
        if(!$this->view->is_ajax) {
            $this->view->params = array_merge($params, $this->_getAllParams());
            if ($this->_getParam('loaded_by_ajax', true)) {
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
        else {
            $this->view->showContent = true;
        }            
    }

    public function getAllCommentsByUserSelect(User_Model_User $user, $params) {

        $commentsTable = Engine_Api::_()->getDbtable('comments', 'core');
        $commentsTableName = $commentsTable->info('name');
        $eventsTable = Engine_Api::_()->getDbtable('events', 'siteevent');
        $eventsTableName = $eventsTable->info('name');
        $select = $commentsTable->select()
                ->from($commentsTableName)
                ->where("poster_type = ?", $user->getType())
                ->where("poster_id = ?", $user->getIdentity())
                ->where("resource_type = 'siteevent_event' OR resource_type = 'siteevent_review'")
                ->order("$commentsTableName.creation_date DESC");

        $select
                ->setIntegrityCheck(false)
                ->join($eventsTableName, $eventsTableName . '.event_id = ' . $commentsTableName . '.resource_id', null);


        return $select;
    }

    public function getAllCommentsByUserPaginator(User_Model_User $user, $params = array()) {

        $paginator = Zend_Paginator::factory($this->getAllCommentsByUserSelect($user, $params));
        if (!isset($params['per_page']) || empty($params['per_page']))
            $params['per_page'] = 4;
        $paginator->setItemCountPerPage($params['per_page']);
        if (!isset($params['page']) || empty($params['page']))
            $params['page'] = 1;
        $paginator->setCurrentPageNumber($params['page']);
        return $paginator;
    }

}