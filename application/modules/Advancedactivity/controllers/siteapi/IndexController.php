<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    IndexController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_IndexController extends Siteapi_Controller_Action_Standard {

    /**
     * Feed action id
     *
     * @var int
     */
    protected $_action_id;

    /**
     * Feed object
     *
     * @var object
     */
    protected $_action;

    /**
     * Init model
     *
     */
    public function init() {
        // Throw error for logged-out user.
        if (!$this->_helper->requireUser()->isValid()) {
            $this->_forward('throw-error', 'index', 'advancedactivity', array(
                "error_code" => "unauthorized"
            ));
            return;
        }
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // If action_id available then create the feed object otherwise return the error.
        if (($this->_action_id = (int) $this->getRequestParam('action_id', 0)) && !empty($this->_action_id)) {
            $this->_action = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getActionById($this->_action_id);
        } else {
            $this->_forward('throw-error', 'index', 'advancedactivity', array(
                "error_code" => "parameter_missing",
                "message" => "action_id"
            ));
            return;
        }

        // Set subject
        $subject_type = $this->getRequestParam('subject_type');
        if (0 !== ($subject_id = (int) $this->getRequestParam('subject_id')) &&
                null !== ($subject = Engine_Api::_()->getItem($subject_type, $subject_id)))
            Engine_Api::_()->core()->setSubject($subject);
    }

    /**
     * Throw the init constructor errors.
     *
     * @return array
     */
    public function throwErrorAction() {
        $message = $this->getRequestParam("message", null);
        if (($error_code = $this->getRequestParam("error_code")) && !empty($error_code)) {
            if (!empty($message))
                $this->respondWithValidationError($error_code, $message);
            else
                $this->respondWithError($error_code);
        }

        return;
    }

    /**
     * Feed Menus - Save Feed (Save the activity feed for logged-in user)  
     *
     * @return array
     */
    public function updateSaveFeedAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $isFeedSaved = 0;
        $viewer = Engine_Api::_()->user()->getViewer();

        $table = Engine_Api::_()->getDbtable('saveFeeds', 'advancedactivity');
        $table->setSaveFeeds($viewer, $this->_action_id, $this->_action->type);

        if (null === ($prev = $table->getSaveFeed($viewer, $this->_action_id)) ||
                false === $prev)
            $isFeedSaved = 1;

        $this->respondWithSuccess($isFeedSaved);
    }

    /**
     * Edit feed body
     */
    public function editFeedAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $action_id = $this->_getParam('action_id');

        if (!$action_id)
            $this->respondWithValidationError('parameter_missing', 'action_id');

        $subject = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getActionById($this->_action_id);

        if (!$subject)
            $this->respondWithError('no_record');

        // Check logged-in user ownership for feed.
        if ($subject->getType() == 'siteevent_event' && ($subject->getParent()->getType() == 'sitepage_page' || $subject->getParent()->getType() == 'sitbusiness_business' || $subject->getParent()->getType() == 'sitegroup_group' || $subject->getParent()->getType() == 'sitestore_store')) {
            $subject = Engine_Api::_()->getItem($subject->getParent()->getType(), $subject->getParent()->getIdentity());
        }

        switch ($subject->getType()) {
            case 'user':
                $is_owner = $viewer->isSelf($subject);
                break;
            case 'sitepage_page':
            case 'sitebusiness_business':
            case 'sitegroup_group':
            case 'sitestore_store':
                $is_owner = $subject->isOwner($viewer);
                break;
            case 'sitepageevent_event':
            case 'sitebusinessevent_event':
            case 'sitegroupevent_event':
            case 'sitestoreevent_event':
                $is_owner = $viewer->isSelf($subject);
                if (empty($is_owner)) {
                    $is_owner = $subject->getParent()->isOwner($viewer);
                }
                break;
            default :
                $is_owner = $viewer->isSelf($subject->getOwner());
                break;
        }

        if (!$activity_moderate && !$is_owner)
            $this->respondWithError('unauthorized');

        $body = $this->_getParam('body');

        if (empty($body))
            $this->respondWithValidationError('parameter_missing', 'body');

        $subject->body = $body;
        $subject->save();
        $this->successResponseNoContent('no_content');
    }

    /**
     * Feed Menus - Delete Feed (Delete feed OR feed comment)  
     *
     * @return array
     */
    function deleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        // Get params
        $is_owner = false;
        $comment_id = $this->getRequestParam('comment_id', null);
        $viewer = Engine_Api::_()->user()->getViewer();
        $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');

        // Check logged-in user ownership for feed.
        if (Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject();
            if ($subject->getType() == 'siteevent_event' && ($subject->getParent()->getType() == 'sitepage_page' || $subject->getParent()->getType() == 'sitbusiness_business' || $subject->getParent()->getType() == 'sitegroup_group' || $subject->getParent()->getType() == 'sitestore_store')) {
                $subject = Engine_Api::_()->getItem($subject->getParent()->getType(), $subject->getParent()->getIdentity());
            }
            switch ($subject->getType()) {
                case 'user':
                    $is_owner = $viewer->isSelf($subject);
                    break;
                case 'sitepage_page':
                case 'sitebusiness_business':
                case 'sitegroup_group':
                case 'sitestore_store':
                    $is_owner = $subject->isOwner($viewer);
                    break;
                case 'sitepageevent_event':
                case 'sitebusinessevent_event':
                case 'sitegroupevent_event':
                case 'sitestoreevent_event':
                    $is_owner = $viewer->isSelf($subject);
                    if (empty($is_owner)) {
                        $is_owner = $subject->getParent()->isOwner($viewer);
                    }
                    break;
                default :
                    $is_owner = $viewer->isSelf($subject->getOwner());
                    break;
            }
        }

        // Both the author and the person being written about get to delete the action_id
        if (!$comment_id && (
                $activity_moderate || $is_owner ||
                ('user' == $this->_action->subject_type && $viewer->getIdentity() == $this->_action->subject_id) || // owner of profile being commented on
                ('user' == $this->_action->object_type && $viewer->getIdentity() == $this->_action->object_id))) {   // commenter
            // Delete action item and all comments/likes
            $db = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getAdapter();
            $db->beginTransaction();
            try {
                if ($this->_action->getTypeInfo()->commentable <= 1) {
                    $comments = $this->_action->getComments(1);
                    if ($comments) {
                        foreach ($comments as $action_comments) {
                            $action_comments->delete();
                        }
                    }
                }
                $this->_action->deleteItem();
                $db->commit();

                $this->successResponseNoContent('no_content', 'feed_index_homefeed');
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        } elseif ($comment_id) {
            $comment = $this->_action->comments()->getComment($comment_id);
            $db = Engine_Api::_()->getDbtable('comments', 'activity')->getAdapter();
            $db->beginTransaction();
            if ($activity_moderate || $is_owner ||
                    ('user' == $comment->poster_type && $viewer->getIdentity() == $comment->poster_id) ||
                    ('user' == $this->_action->object_type && $viewer->getIdentity() == $this->_action->object_id)) {
                try {
                    $this->_action->comments()->removeComment($comment_id);
                    $db->commit();

                    $this->successResponseNoContent('no_content', 'feed_index_homefeed');
                } catch (Exception $e) {
                    $db->rollBack();
                    $this->respondWithValidationError('internal_server_error', $e->getMessage());
                }
            } else {
                $this->respondWithError('unauthorized');
            }
        }
    }

    /**
     * Feed Menus - Enable Comments / Disable Comments (Modified the comments for feed)  
     *
     * @return array
     */
    public function updateCommentableAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $db = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getAdapter();
        $db->beginTransaction();
        try {
            $this->_action->commentable = !$this->_action->commentable;
            $this->_action->save();
            $db->commit();

            $this->respondWithSuccess($this->_action->commentable);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Feed Menus - Lock this Feed / Unlock this Feed (Modified the shareable for feed)  
     *
     * @return array
     */
    public function updateShareableAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $db = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getAdapter();
        $db->beginTransaction();
        try {
            $this->_action->shareable = !$this->_action->shareable;
            $this->_action->save();
            $db->commit();

            $this->respondWithSuccess($this->_action->shareable);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Handles HTTP request to like an activity feed item
     *
     * @return array
     */
    public function likeAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // Get Params
        $comment_id = $this->getRequestParam('comment_id', null);

        $viewer = Engine_Api::_()->user()->getViewer();

        // Start transaction
        $db = Engine_Api::_()->getDbtable('likes', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            // Action
            if (!$comment_id) {
                // Check authorization
                if ($this->_action && !Engine_Api::_()->authorization()->isAllowed($this->_action->getObject(), null, 'comment'))
                    $this->respondWithError('unauthorized');

                $this->_action->likes()->addLike($viewer);

                // Add notification for owner of activity (if user and not viewer)
                if ($this->_action->subject_type == 'user' && $this->_action->subject_id != $viewer->getIdentity()) {
                    $actionOwner = Engine_Api::_()->getItemByGuid($this->_action->subject_type . "_" . $this->_action->subject_id);

                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($actionOwner, $viewer, $this->_action, 'liked', array(
                        'label' => 'post'
                    ));
                }
            }
            // Comment
            else {
                $comment = $this->_action->comments()->getComment($comment_id);

                // Check authorization
//                if (!$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment'))
//                    $this->respondWithError('unauthorized');

                if (!$comment)
                    $this->respondWithError('no_record');

                $comment->likes()->addLike($viewer);

                // @todo make sure notifications work right
                if ($comment->poster_id != $viewer->getIdentity()) {
                    Engine_Api::_()->getApi('Siteapi_Core', 'activity')
                            ->addNotification($comment->getPoster(), $viewer, $comment, 'liked', array(
                                'label' => 'comment'
                    ));
                }

                // Add notification for owner of activity (if user and not viewer)
                if ($this->_action->subject_type == 'user' && $this->_action->subject_id != $viewer->getIdentity()) {
                    $actionOwner = Engine_Api::_()->getItemByGuid($this->_action->subject_type . "_" . $this->_action->subject_id);
                }
            }

            // Stats
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

//    $bodyArray = array();
//    $bodyArray["name"] = "unlike";
//    $bodyArray["label"] = $this->translate("Unlike");
//    $bodyArray["isLike"] = 1;
//
//    if ( !empty($comment_id) ) {
//      $bodyArray["url"] = "unlike";
//      $bodyArray["urlParams"] = array(
//          "action_id" => $this->_action_id,
//          "subject_type" => $this->_action->getObject()->getType(),
//          "subject_id" => $this->_action->getObject()->getIdentity(),
//          "comment_id" => $comment_id
//      );
//    } else {
//      $bodyArray["url"] = "unlike";
//      $bodyArray["urlParams"] = array(
//          "action_id" => $this->_action_id,
//          "subject_type" => $this->_action->getObject()->getType(),
//          "subject_id" => $this->_action->getObject()->getIdentity()
//      );
//    }
//    $this->respondWithSuccess($bodyArray);

        $this->successResponseNoContent('no_content');
    }

    /**
     * Handles HTTP request to remove a like from an activity feed item
     *
     * @return array
     */
    public function unlikeAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        $comment_id = $this->getRequestParam('comment_id');
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$comment_id) {
            // Check authorization
            if (!Engine_Api::_()->authorization()->isAllowed($this->_action->getObject(), null, 'comment'))
                $this->respondWithError('unauthorized');
        }else {
            $comment = $this->_action->comments()->getComment($comment_id);

            // Check authorization
//            if (!$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment'))
//                $this->respondWithError('unauthorized');

            if (!$comment)
                $this->respondWithError('no_record');
        }

        // Start transaction
        $db = Engine_Api::_()->getDbtable('likes', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            // Action
            if (!$comment_id)
                $this->_action->likes()->removeLike($viewer);
            else
                $comment->likes()->removeLike($viewer);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }

        $this->successResponseNoContent('no_content');
    }

    /**
     * Handles HTTP POST request to comment on an activity feed item
     *
     * @return array
     */
    public function commentAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // Start transaction
        $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
        $db->beginTransaction();
        $send_notification = $this->getRequestParam('send_notification', 1);

        try {
            $viewer = Engine_Api::_()->user()->getViewer();

            $postData = $_REQUEST;

            if (isset($postData['body']) && !empty($postData['body']))
                $body = $postData['body'];

            if (empty($body))
                $this->respondWithError('validation_fail');

            // Check authorization
            if (!Engine_Api::_()->authorization()->isAllowed($this->_action->getObject(), null, 'comment'))
                $this->respondWithError('unauthorized');

            // Add the comment
            $comment = $this->_action->comments()->addComment($viewer, $body);
            if (empty($comment))
                $this->respondWithError('unauthorized');

            // Notifications
            $notifyApi = Engine_Api::_()->getApi('Siteapi_Core', 'activity');


            if (isset($send_notification) && !empty($send_notification)) {
                $actionOwner = Engine_Api::_()->getItemByGuid($this->_action->subject_type . "_" . $this->_action->subject_id);
                // Add notification for owner of activity (if user and not viewer)
                if ($this->_action->subject_type == 'user' && $this->_action->subject_id != $viewer->getIdentity()) {
                    $notifyApi->addNotification($actionOwner, $viewer, $this->_action, 'commented', array(
                        'label' => 'post'
                    ));
                }

//             Add a notification for all users that commented or like except the viewer and poster
//             @todo we should probably limit this
                foreach ($this->_action->comments()->getAllCommentsUsers() as $notifyUser) {
                    if ($notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
                        $notifyApi->addNotification($notifyUser, $viewer, $this->_action, 'commented_commented', array(
                            'label' => 'post'
                        ));
                    }
                }
//
//            // Add a notification for all users that commented or like except the viewer and poster
//            // @todo we should probably limit this
                foreach ($this->_action->likes()->getAllLikesUsers() as $notifyUser) {
                    if ($notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {

                        $notifyApi->addNotification($notifyUser, $viewer, $this->_action, 'liked_commented', array(
                            'label' => 'post'
                        ));
                    }
                }
            }
            $canComment = Engine_Api::_()->authorization()->isAllowed($this->_action->getObject(), null, 'comment');
            $canDelete = Engine_Api::_()->authorization()->isAllowed($this->_action->getObject(), null, 'edit');

            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');
            $commentInfo = array();
            $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
            $commentInfo["action_id"] = $this->_action_id;
            $commentInfo["comment_id"] = $comment->comment_id;

            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poster);
            $commentInfo = array_merge($commentInfo, $getContentImages);

            //to provide the same image names as in likes-comment response
            $getContentImages = array();
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poster, false, 'author');
            $commentInfo = array_merge($commentInfo, $getContentImages);
            $commentInfo["author_title"] = $poster->getTitle();
            $commentInfo["comment_body"] = $comment->body;
            $commentInfo["comment_date"] = $comment->creation_date;

            if (!empty($canDelete) || $poster->isSelf($viewer)) {
                $commentInfo["delete"] = array(
                    "name" => "delete",
                    "label" => $this->translate("Delete"),
                    "url" => "comment-delete",
                    'urlParams' => array(
                        "action_id" => $this->_action_id,
                        "subject_type" => $this->_action->getType(),
                        "subject_id" => $this->_action->getIdentity(),
                        "comment_id" => $comment->comment_id
                    )
                );
            } else {
                $commentInfo["delete"] = null;
            }

            if (!empty($canComment)) {
                $isLiked = $comment->likes()->isLike($viewer);
                if (empty($isLiked)) {
                    $likeInfo["name"] = "like";
                    $likeInfo["label"] = $this->translate("Like");
                    $likeInfo["url"] = "like";
                    $likeInfo["urlParams"] = array(
                        "action_id" => $this->_action_id,
                        "subject_type" => $this->_action->getType(),
                        "subject_id" => $this->_action->getIdentity(),
                        "comment_id" => $comment->getIdentity()
                    );
                    $likeInfo["isLike"] = 0;
                } else {
                    $likeInfo["name"] = "unlike";
                    $likeInfo["label"] = $this->translate("Unlike");
                    $likeInfo["url"] = "unlike";
                    $likeInfo["urlParams"] = array(
                        "action_id" => $this->_action_id,
                        "subject_type" => $this->_action->getType(),
                        "subject_id" => $this->_action->getIdentity(),
                        "comment_id" => $comment->getIdentity()
                    );

                    $likeInfo["isLike"] = 1;
                }
                $commentInfo["like_count"] = $comment->likes()->getLikeCount();
                $commentInfo["like"] = $likeInfo;
            } else {
                $commentInfo["like"] = null;
            }

            $db->commit();

            $this->respondWithSuccess($commentInfo);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    public function addCommentNotificationsAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        // Start transaction
        $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $actionOwner = Engine_Api::_()->getItemByGuid($this->_action->subject_type . "_" . $this->_action->subject_id);

            $postData = $_REQUEST;
            $comment_id = $this->getRequestParam('comment_id');

            $comment = $this->_action->comments()->getComment($comment_id);

            if (empty($comment))
                $this->respondWithError('validation_fail');

            // Check authorization
            if (!Engine_Api::_()->authorization()->isAllowed($this->_action->getObject(), null, 'comment'))
                $this->respondWithError('unauthorized');


            // Notifications
            $notifyApi = Engine_Api::_()->getApi('Siteapi_Core', 'activity');


            // Add notification for owner of activity (if user and not viewer)
            if ($this->_action->subject_type == 'user' && $this->_action->subject_id != $viewer->getIdentity()) {
                $notifyApi->addNotification($actionOwner, $viewer, $this->_action, 'commented', array(
                    'label' => 'post'
                ));
            }
//             Add a notification for all users that commented or like except the viewer and poster
//             @todo we should probably limit this
            foreach ($this->_action->comments()->getAllCommentsUsers() as $notifyUser) {
                if ($notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
                    $notifyApi->addNotification($notifyUser, $viewer, $this->_action, 'commented_commented', array(
                        'label' => 'post'
                    ));
                }
            }
//
//            // Add a notification for all users that commented or like except the viewer and poster
//            // @todo we should probably limit this
            foreach ($this->_action->likes()->getAllLikesUsers() as $notifyUser) {
                if ($notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
                    $notifyApi->addNotification($notifyUser, $viewer, $this->_action, 'liked_commented', array(
                        'label' => 'post'
                    ));
                }
            }

            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

}
