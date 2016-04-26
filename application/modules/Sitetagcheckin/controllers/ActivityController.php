<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ActivityController.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_ActivityController extends Core_Controller_Action_Standard {

  /**
   * Handles HTTP request to like an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /sitetagcheckin/activity/like
   *   *
   * @throws Engine_Exception If a user lacks authorization
   * @return void
   */
  public function likeAction() {
    //MAKE SURE USER EXISTS
    if (!$this->_helper->requireUser()->isValid())
      return;

    //COLLECT PARAMS
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');
    $viewer = Engine_Api::_()->user()->getViewer();

    //START TRANSACTION
    $db = Engine_Api::_()->getDbtable('likes', 'activity')->getAdapter();
    $db->beginTransaction();

    try {
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);

      //ACTION
      if (!$comment_id) {

        //CHECK AUTHORIZATION
        if ($action && !Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment')) {
          throw new Engine_Exception('This user is not allowed to like this item');
        }

        $action->likes()->addLike($viewer);

        //ADD NOTIFICATION FOR OWNER OF ACTIVITY (IF USER AND NOT VIEWER)
        if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
          $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);

          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($actionOwner, $viewer, $action, 'liked', array(
              'label' => 'post'
          ));
        }
      }
      //COMMENT
      else {
        $comment = $action->comments()->getComment($comment_id);

        //CHECK AUTHORIZATION
        if (!$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment')) {
          throw new Engine_Exception('This user is not allowed to like this item');
        }

        $comment->likes()->addLike($viewer);

        //@TODO MAKE SURE NOTIFICATIONS WORK RIGHT
        if ($comment->poster_id != $viewer->getIdentity()) {
          Engine_Api::_()->getDbtable('notifications', 'activity')
                  ->addNotification($comment->getPoster(), $viewer, $comment, 'liked', array(
                      'label' => 'comment'
                  ));
        }

        //ADD NOTIFICATION FOR OWNER OF ACTIVITY (IF USER AND NOT VIEWER)
        if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
          $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
        }
      }

      //STATS
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //SUCCESS
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('You now like this action.');
    $this->view->sitetagcheckin_id = $this->_getParam('sitetagcheckin_id');

    //REDIRECT IF NOT JSON CONTEXT
    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
      $this->view->body = $this->view->sitetagcheckinactivity($action, array('noList' => true, 'getUpdate' => true, 'sitetagcheckin_id' => $this->view->sitetagcheckin_id));
    }
  }

  /**
   * Handles HTTP request to unlike an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /sitetagcheckin/activity/unlike
   *   *
   * @throws Engine_Exception If a user lacks authorization
   * @return void
   */
  public function unlikeAction() {
    //MAKE SURE USER EXISTS
    if (!$this->_helper->requireUser()->isValid())
      return;

    //COLLECT PARAMS
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');
    $viewer = Engine_Api::_()->user()->getViewer();

    //START TRANSACTION
    $db = Engine_Api::_()->getDbtable('likes', 'activity')->getAdapter();
    $db->beginTransaction();

    try {
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);

      //ACTION
      if (!$comment_id) {

        //CHECK AUTHORIZATION
        if (!Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment')) {
          throw new Engine_Exception('This user is not allowed to unlike this item');
        }

        $action->likes()->removeLike($viewer);
      }

      //COMMENT
      else {
        $comment = $action->comments()->getComment($comment_id);

        //CHECK AUTHORIZATION
        if (!$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment')) {
          throw new Engine_Exception('This user is not allowed to like this item');
        }

        $comment->likes()->removeLike($viewer);
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //SUCCESS
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('You no longer like this action.');
    $this->view->sitetagcheckin_id = $this->_getParam('sitetagcheckin_id');
    //REDIRECT IF NOT JSON CONTEXT
    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
      $this->view->body = $this->view->sitetagcheckinactivity($action, array('noList' => true, 'getUpdate' => true, 'sitetagcheckin_id' => $this->view->sitetagcheckin_id));
    }
  }

  /**
   * Handles HTTP request to comment an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /sitetagcheckin/activity/comment
   *   *
   * @throws Engine_Exception If a user lacks authorization
   * @return void
   */
  public function commentAction() {
    //MAKE SURE USER EXISTS
    if (!$this->_helper->requireUser()->isValid())
      return;

    //MAKE FORM
    $this->view->form = $form = new Sitetagcheckin_Form_Comment();

    //NOT POST
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not a post');
      return;
    }

    //NOT VALID
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    //START TRANSACTION
    $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $action_id = $this->view->action_id = $this->_getParam('action_id', $this->_getParam('action', null));
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
      if (!$action) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Activity does not exist');
        return;
      }
      $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
      $body = $form->getValue('body');

      //CHECK AUTHORIZATION
      if (!Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'))
        throw new Engine_Exception('This user is not allowed to comment on this item.');

      //ADD THE COMMENT
      $action->comments()->addComment($viewer, $body);

      //NOTIFICATIONS
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

      //ADD NOTIFICATION FOR OWNER OF ACTIVITY (IF USER AND NOT VIEWER)
      if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
        $notifyApi->addNotification($actionOwner, $viewer, $action, 'commented', array(
            'label' => 'post'
        ));
      }

      //ADD A NOTIFICATION FOR ALL USERS THAT COMMENTED OR LIKE EXCEPT THE VIEWER AND POSTER
      //@TODO WE SHOULD PROBABLY LIMIT THIS
      foreach ($action->comments()->getAllCommentsUsers() as $notifyUser) {
        if ($notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
          $notifyApi->addNotification($notifyUser, $viewer, $action, 'commented_commented', array(
              'label' => 'post'
          ));
        }
      }

      //ADD A NOTIFICATION FOR ALL USERS THAT COMMENTED OR LIKE EXCEPT THE VIEWER AND POSTER
      //@TODO WE SHOULD PROBABLY LIMIT THIS
      foreach ($action->likes()->getAllLikesUsers() as $notifyUser) {
        if ($notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
          $notifyApi->addNotification($notifyUser, $viewer, $action, 'liked_commented', array(
              'label' => 'post'
          ));
        }
      }

      //STATS
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //ASSIGN MESSAGE FOR JSON
    $this->view->status = true;
    $this->view->message = 'Comment posted';
    $this->view->sitetagcheckin_id = $this->_getParam('sitetagcheckin_id');
    //REDIRECT IF NOT JSON
    if (null === $this->_getParam('format', null)) {
      $this->_redirect($form->return_url->getValue(), array('prependBase' => false));
    } else if ('json' === $this->_getParam('format', null)) {
      $this->view->body = $this->view->sitetagcheckinactivity($action, array('noList' => true, 'getUpdate' => true, 'sitetagcheckin_id' => $this->view->sitetagcheckin_id, 'submitComment' => true));
    }
  }

  /**
   * Handles HTTP request to view comment an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /sitetagcheckin/activity/viewcomment
   *   *
   * @throws Engine_Exception If a user lacks authorization
   * @return void
   */
  public function viewcommentAction() {
    //COLLECT PARAMS
    $action_id = $this->_getParam('action_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->sitetagcheckin_id = $sitetagcheckin_id = $this->_getParam('sitetagcheckin_id', null);
    $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
    $form = $this->view->form = new Sitetagcheckin_Form_Comment();
    $form->setActionIdentity($action_id, $sitetagcheckin_id);

    //REDIRECT IF NOT JSON CONTEXT
    if (null === $this->_getParam('format', null)) {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else if ('json' === $this->_getParam('format', null)) {
      $this->view->body = $this->view->sitetagcheckinactivity($action, array('viewAllComments' => true, 'noList' => $this->_getParam('nolist', false), 'sitetagcheckin_id' => $this->sitetagcheckin_id, 'getUpdate' => true,));
    }
  }

  /**
   * Handles HTTP POST request to delete a comment or an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/delete
   *
   * @return void
   */
  function deleteAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');


    //IDENTIFY IF IT'S AN ACTION_ID OR COMMENT_ID BEING DELETED
    $this->view->comment_id = $comment_id = $this->_getParam('comment_id', null);
    $this->view->action_id = $action_id = $this->_getParam('action_id', null);
    $this->view->sitetagcheckin_id = $sitetagcheckin_id = $this->_getParam('sitetagcheckin_id', null);

    $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
    if (!$action) {
      //TELL SMOOTHBOX TO CLOSE
      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.');
      $this->view->smoothboxClose = true;
      return $this->render('deletedItem');
    }

    //SEND TO VIEW SCRIPT IF NOT POST
    if (!$this->getRequest()->isPost())
      return;


    //BOTH THE AUTHOR AND THE PERSON BEING WRITTEN ABOUT GET TO DELETE THE ACTION_ID
    if (!$comment_id && (
            $activity_moderate ||
            ('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) || //owner of profile being commented on
            ('user' == $action->object_type && $viewer->getIdentity() == $action->object_id))) {   //commenter
      //DELETE ACTION ITEM AND ALL COMMENTS/LIKES
      $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
      $db->beginTransaction();
      try {
        $action->deleteItem();
        $db->commit();

        //TELL SMOOTHBOX TO CLOSE
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('This activity item has been removed.');
        $this->view->smoothboxClose = true;
        return $this->render('deletedItem');
      } catch (Exception $e) {
        $db->rollback();
        $this->view->status = false;
      }
    } elseif ($comment_id) {
      $comment = $action->comments()->getComment($comment_id);
      //ALLOW DELETE IF PROFILE/ENTRY OWNER
      $db = Engine_Api::_()->getDbtable('comments', 'activity')->getAdapter();
      $db->beginTransaction();
      if ($activity_moderate ||
              ('user' == $comment->poster_type && $viewer->getIdentity() == $comment->poster_id) ||
              ('user' == $action->object_type && $viewer->getIdentity() == $action->object_id)) {
        try {
          $action->comments()->removeComment($comment_id);
          $db->commit();
          $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment has been deleted');
          return $this->render('deletedComment');
        } catch (Exception $e) {
          $db->rollback();
          $this->view->status = false;
        }
      } else {
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('You do not have the privilege to delete this comment');
        return $this->render('deletedComment');
      }
    } else {
      //NEITHER THE ITEM OWNER, NOR THE ITEM SUBJECT.  DENIED!
      $this->_forward('requireauth', 'error', 'core');
    }
  }

  /**
   * Handles HTTP request to get an activity feed item's likes and returns a 
   * Json as the response
   *
   * Uses the default route and can be accessed from
   *  - /sitetagcheckin/activity/viewlike
   * 
   * @return void
   */
  public function viewlikeAction() {
    //COLLECT PARAMS
    $action_id = $this->_getParam('action_id');
    $viewer = Engine_Api::_()->user()->getViewer();

    $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);

    $this->view->sitetagcheckin_id = $sitetagcheckin_id = $this->_getParam('sitetagcheckin_id', null);
    //REDIRECT IF NOT JSON CONTEXT
    if (null === $this->_getParam('format', null)) {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else if ('json' === $this->_getParam('format', null)) {
      $this->view->body = $this->view->sitetagcheckinactivity($action, array('viewAllLikes' => true, 'getUpdate' => true, 'noList' => $this->_getParam('nolist', false), 'sitetagcheckin_id' => $sitetagcheckin_id));
    }
  }

  /**
   * Handles HTTP request to get an activity feed item's likes and returns a 
   * Json as the response
   *
   * Uses the default route and can be accessed from
   *  - /sitetagcheckin/activity/getLikes
   * 
   * @return void
   */
  public function getLikesAction() {
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');

    if (!$action_id ||
            !$comment_id ||
            !($action = Engine_Api::_()->getItem('activity_action', $action_id)) ||
            !($comment = $action->comments()->getComment($comment_id))) {
      $this->view->status = false;
      $this->view->body = '-';
      return;
    }

    $likes = $comment->likes()->getAllLikesUsers();
    $this->view->body = $this->view->translate(array('%s likes this', '%s like this',
        count($likes)), strip_tags($this->view->fluentList($likes)));
    $this->view->status = true;
  }

}