<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: CommentController.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_CommentController extends Core_Controller_Action_Standard {

    public function init() {
        $type = $this->_getParam('type');
        $identity = $this->_getParam('id');
        if ($type && $identity) {
            $item = Engine_Api::_()->getItem($type, $identity);
            if ($item instanceof Core_Model_Item_Abstract) {
                if (!Engine_Api::_()->core()->hasSubject()) {
                    Engine_Api::_()->core()->setSubject($item);
                }
            }
        }
    }

    public function listAction() {

        $this->view->taggingContent = $taggingContent = $this->_getParam('taggingContent');
        $this->view->showAsNested = $showAsNested = $this->_getParam('showAsNested', 1);
        $this->view->showAsLike = $showAsLike = $this->_getParam('showAsLike', 1);
        $this->view->showComposerOptions = $showComposerOptions = $this->_getParam('showComposerOptions');
        $this->view->photoLightboxComment = $photoLightboxComment = $this->_getParam('photoLightboxComment');

        $this->view->commentsorder = $commentsorder = $this->_getParam('commentsorder');

        $this->view->showSmilies = $showSmilies = $this->_getParam('showSmilies');
        $this->view->showDislikeUsers = $showDislikeUsers = $this->_getParam('showDislikeUsers', 1);
        $this->view->showLikeWithoutIcon = $showLikeWithoutIcon = $this->_getParam('showLikeWithoutIcon', 1);
        $this->view->showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this->_getParam('showLikeWithoutIconInReplies', 1);
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $this->view->subject = $subject = $this->getSubjectItem();
        $subjectParent = $subject;
        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        // Perms
        //$this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');

        if ($subject->getType() == 'sitestaticpage_page') {
            $this->view->canComment = $canComment = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestaticpage_page', 'comment');
            $this->view->canEdit = $this->view->canDelete = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestaticpage_page', 'edit');
        } else {
            $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
            $this->view->canEdit = $this->view->canDelete = $subject->authorization()->isAllowed($viewer, 'edit');
        }

        $nestedcomment_commentlist = Zend_Registry::isRegistered('nestedcomment_commentlist') ? Zend_Registry::get('nestedcomment_commentlist') : null;
        $autorizationApi = Engine_Api::_()->authorization();
        if (strpos($subject->getType(), "sitepage") !== false) {
            if ($subject->getType() == 'sitepage_page') {
                $pageSubject = $subject;
            } elseif ($subject->getType() == 'sitepagemusic_playlist') {
                $pageSubject = $subject->getParentType();
            } elseif ($subject->getType() == 'sitepagenote_photo') {
                $pageSubject = $subject->getParent()->getParent()->getParent();
            } else {
                $pageSubject = $subject->getParent();
            }
            $pageApi = Engine_Api::_()->sitepage();

            $this->view->canComment = $canComment = $pageApi->isManageAdmin($pageSubject, 'comment');
            $this->view->canEdit = $this->view->canDelete = $pageApi->isManageAdmin($pageSubject, 'edit');
        } elseif (strpos($subject->getType(), "sitebusiness") !== false) {
            if ($subject->getType() == 'sitebusiness_business') {
                $businessSubject = $subject;
            } elseif ($subject->getType() == 'sitebusinessmusic_playlist') {
                $businessSubject = $subject->getParentType();
            } elseif ($subject->getType() == 'sitebusinessnote_photo') {
                $businessSubject = $subject->getParent()->getParent()->getParent();
            } else {
                $businessSubject = $subject->getParent();
            }
            $businessApi = Engine_Api::_()->sitebusiness();

            $this->view->canComment = $canComment = $businessApi->isManageAdmin($businessSubject, 'comment');
            $this->view->canEdit = $this->view->canDelete = $businessApi->isManageAdmin($businessSubject, 'edit');
        } elseif ($subject->getType() == 'sitereview_review') {
            $listingtype_id = $subject->getParent()->listingtype_id;
            $this->view->canComment = $canComment = $autorizationApi->getPermission($level_id, 'sitereview_listing', "review_reply_listtype_$listingtype_id");
            $this->view->canEdit = $this->view->canDelete = $autorizationApi->getPermission($level_id, 'sitereview_listing', "edit_listtype_$listingtype_id");
        } elseif ($subject->getType() == 'sitereview_listing') {
            $listingtype_id = $subject->listingtype_id;
            $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
            if (!empty($canComment))
                $canComment = $subject->authorization()->isAllowed($viewer, "comment_listtype_$listingtype_id");
            $this->view->canComment = $canComment;
            $this->view->canEdit = $this->view->canDelete = $subject->authorization()->isAllowed($viewer, "edit_listtype_$listingtype_id");
        } else if (strpos($subject->getType(), "sitegroup") !== false) {
            if ($subject->getType() == 'sitegroup_group') {
                $groupSubject = $subject;
            } elseif ($subject->getType() == 'sitegroupmusic_playlist') {
                $groupSubject = $subject->getParentType();
            } elseif ($subject->getType() == 'sitegroupnote_photo') {
                $groupSubject = $subject->getParent()->getParent()->getParent();
            } elseif ($subject->getType() == 'sitegroupevent_photo') {
                $groupSubject = $subject->getEvent()->getParentPage();
            } else {
                $groupSubject = $subject->getParent();
            }
            $groupApi = Engine_Api::_()->sitegroup();

            $this->view->canComment = $canComment = $groupApi->isManageAdmin($groupSubject, 'comment');
            $this->view->canEdit = $this->view->canDelete = $groupApi->isManageAdmin($groupSubject, 'edit');
        } else if (strpos($subject->getType(), "sitestore") !== false && strpos($subject->getType(), "sitestoreproduct") === false) {
            if ($subject->getType() == 'sitestore_store') {
                $storeSubject = $subject;
            } elseif ($subject->getType() == 'sitestoremusic_playlist') {
                $storeSubject = $subject->getParentType();
            } elseif ($subject->getType() == 'sitestoreproduct_product') {
                $storeSubject = $subject;
            } elseif ($subject->getType() == 'sitestorenote_photo') {
                $storeSubject = $subject->getParent()->getParent()->getParent();
            } elseif ($subject->getType() == 'sitestoreevent_photo') {
                $storeSubject = $subject->getEvent()->getParentPage();
            } else {
                $storeSubject = $subject->getParent();
            }
            $storeApi = Engine_Api::_()->sitestore();

            $this->view->canComment = $canComment = $storeApi->isManageAdmin($storeSubject, 'comment');
            $this->view->canEdit = $this->view->canDelete = $storeApi->isManageAdmin($storeSubject, 'edit');
        }

        if ($subject->getType() == 'siteevent_event') {
            $subjectParent = Engine_Api::_()->getItem($subject->getParent()->getType(), $subject->getParent()->getIdentity());
        }

        $this->view->nestedCommentPressEnter = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.comment.pressenter', 1);
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Nestedcomment/View/Helper', 'Nestedcomment_View_Helper');
        // Likes
        $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
        $this->view->viewAllDislikes = $this->_getParam('viewAllDislikes', false);
        $this->view->likes = $likes = Engine_Api::_()->getDbtable('likes', 'nestedcomment')->likes($subject)->getLikePaginator();
        $this->view->dislikes = $dislikes = Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikePaginator($subject);
        $this->view->parent_comment_id = $parent_comment_id = $this->_getParam('parent_comment_id', 0);
        $this->view->comment_id = $comment_id = $this->_getParam('comment_id', 0);
        $this->view->parent_div = $parent_div = $this->_getParam('parent_div', 0);

        $this->view->format = $this->_getParam('format');

        if ($commentsorder) {
            $this->view->order = $order = $this->_getParam('order', 'DESC');
        } else {
            $this->view->order = $order = $this->_getParam('order', 'ASC');
        }
        if (empty($parent_comment_id)) {
            $commentCountSelect = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getCommentSelect($order);

            if (!$showAsNested) {
                $commentCountSelect->where('parent_comment_id =?', 0);
            }
            $this->view->commentsCount = $commentsCount = Zend_Paginator::factory($commentCountSelect)->getTotalItemCount();
        }

        if (empty($nestedcomment_commentlist))
            return;

        if ($parent_comment_id) {
            $comment_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.reply.per.page', 4);
        } else {
            $comment_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.comment.per.page', 10);
        }

        // Comments
        // If has a page, display oldest to newest
        if (0 !== ( $page = $this->_getParam('page', 0))) {
            $commentSelect = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getCommentSelect();
            $commentSelect->where('parent_comment_id =?', $parent_comment_id);

            $commentSelect->reset('order');
            if ($order != 'like_count') {
                $commentSelect->order("comment_id $order");
            } else {
                $commentSelect->order("$order DESC");
                //  $commentSelect->order("comment_id DESC");
            }

            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber($page + 1);
            $comments->setItemCountPerPage($comment_per_page);
            $this->view->comments = $comments;
            $this->view->page = $page;
        }
        // If not has a page, show the
        else {
            $commentSelect = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getCommentSelect();
            $commentSelect->where('parent_comment_id =?', $parent_comment_id);

            $commentSelect->reset('order');
            if ($order != 'like_count') {
                $commentSelect->order("comment_id $order");
            } else {
                $commentSelect->order("$order DESC");
                //$commentSelect->order("comment_id DESC");
            }
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber(1);
            $comments->setItemCountPerPage($comment_per_page);
            $this->view->comments = $comments;
            $this->view->page = $page;
        }

        $this->view->nested_comment_id = $subject->getGuid() . "_" . $parent_comment_id;

        if ($viewer->getIdentity() && $canComment) {
            $this->view->formComment = $form = new Nestedcomment_Form_Comment_Create(array('textareaId' => $this->view->nested_comment_id));
            if ($parent_comment_id) {
                $form->getElement('submit')->setLabel('Post Reply');
            }

            $form->populate(array(
                'identity' => $subject->getIdentity(),
                'type' => $subject->getType(),
                'format' => 'html',
                'parent_comment_id' => $parent_comment_id,
                'taggingContent' => $taggingContent,
                'showAsNested' => $showAsNested,
                'showAsLike' => $showAsLike,
                'showDislikeUsers' => $showDislikeUsers,
                'showLikeWithoutIcon' => $showLikeWithoutIcon,
                'showLikeWithoutIconInReplies' => $showLikeWithoutIconInReplies,
                'showSmilies' => $showSmilies,
                'photoLightboxComment' => $photoLightboxComment,
                'commentsorder' => $commentsorder
            ));
        }

        if ($showAsLike) {
            $this->renderScript('comment/list.tpl');
        } else {
            $this->renderScript('comment/list_both_like_dislike.tpl');
        }
    }

    public function createAction() {

        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = $this->getSubjectItem();
        $subjectParent = $subject;

        $viewer_id = $viewer->getIdentity();
        $listingtypeName = "";
        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $nestedcommentManageType = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.manage.type', 1);
        $nestedcommentInfoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.info.type', 1);
        $tempHostType = $tempSitemenuLtype = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.global.view', 0);
        $nestedcommentLtype = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.lsettings', null);
        $nestedcommentGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.global.type', 0);
        $autorizationApi = Engine_Api::_()->authorization();
        if (strpos($subject->getType(), "sitepage") !== false) {
            if ($subject->getType() == 'sitepage_page') {
                $subjectParent = $subject;
            } elseif ($subject->getType() == 'sitepagemusic_playlist') {
                $subjectParent = $subject->getParentType();
            } elseif ($subject->getType() == 'sitepagenote_photo') {
                $subjectParent = $subject->getParent()->getParent()->getParent();
            } else {
                $subjectParent = $subject->getParent();
            }
            $pageApi = Engine_Api::_()->sitepage();
            $canComment = $pageApi->isManageAdmin($subjectParent, 'comment');
            if (empty($canComment)) {
                $this->view->status = false;
                return;
            }
        } elseif (strpos($subject->getType(), "sitebusiness") !== false) {
            if ($subject->getType() == 'sitebusiness_business') {
                $subjectParent = $subject;
            } elseif ($subject->getType() == 'sitebusinessnote_photo') {
                $subjectParent = $subject->getParent()->getParent()->getParent();
            } elseif ($subject->getType() == 'sitebusinessmusic_playlist') {
                $subjectParent = $subject->getParentType();
            } else {
                $subjectParent = $subject->getParent();
            }
            $businessApi = Engine_Api::_()->sitebusiness();
            $canComment = $businessApi->isManageAdmin($subjectParent, 'comment');
            if (empty($canComment)) {
                $this->view->status = false;
                return;
            }
        } elseif ($subject->getType() == 'sitereview_review') {
            $listingtype_id = $subject->getParent()->listingtype_id;
            $canComment = $autorizationApi->getPermission($level_id, 'sitereview_listing', "review_reply_listtype_$listingtype_id");
            if (empty($canComment)) {
                $this->view->status = false;
                return;
            }
        } elseif ($subject->getType() == 'sitereview_listing') {
            $listingtype_id = $subject->listingtype_id;
            $listingtypeName = strtolower(Engine_Api::_()->getDbTable('listingtypes', 'sitereview')->getListingTypeColumn($listingtype_id, 'title_singular'));
            $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
            if (!empty($canComment))
                $canComment = $subject->authorization()->isAllowed($viewer, "comment_listtype_$listingtype_id");
            if (empty($canComment)) {
                $this->view->status = false;
                return;
            }
        } elseif (strpos($subject->getType(), "sitegroup") !== false) {
            if ($subject->getType() == 'sitegroup_group') {
                $subjectParent = $subject;
            } elseif ($subject->getType() == 'sitegroupmusic_playlist') {
                $subjectParent = $subject->getParentType();
            } elseif ($subject->getType() == 'sitegroupnote_photo') {
                $subjectParent = $subject->getParent()->getParent()->getParent();
            } elseif ($subject->getType() == 'sitegroupevent_photo') {
                $subjectParent = $subject->getEvent()->getParentPage();
            } else {
                $subjectParent = $subject->getParent();
            }
            $groupApi = Engine_Api::_()->sitegroup();
            $canComment = $groupApi->isManageAdmin($subjectParent, 'comment');
            if (empty($canComment)) {
                $this->view->status = false;
                return;
            }
        } elseif (strpos($subject->getType(), "sitestore") !== false && strpos($subject->getType(), "sitestoreproduct") === false) {
            if ($subject->getType() == 'sitestore_store') {
                $subjectParent = $subject;
            } elseif ($subject->getType() == 'sitestoremusic_playlist') {
                $subjectParent = $subject->getParentType();
            } elseif ($subject->getType() == 'sitestorenote_photo') {
                $subjectParent = $subject->getParent()->getParent()->getParent();
            } elseif ($subject->getType() == 'sitestoreevent_photo') {
                $subjectParent = $subject->getEvent()->getParentPage();
            } else {
                $subjectParent = $subject->getParent();
            }
            $storeApi = Engine_Api::_()->sitestore();
            $canComment = $storeApi->isManageAdmin($subjectParent, 'comment');
            if (empty($canComment)) {
                $this->view->status = false;
                return;
            }
        } elseif ($subject->getType() == 'sitestaticpage_page') {
            $canComment = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestaticpage_page', 'comment');
            if (empty($canComment)) {
                $this->view->status = false;
                return;
            }
        } else if ($subject->getType() == 'sitevideo_channel') {
            $canComment = $subjectParent->authorization()->isAllowed($viewer, 'comment');
            if (empty($canComment)) {
                $this->view->status = false;
                return;
            }
            Engine_Api::_()->getApi('core', 'sitevideo')->sendSiteNotification($subject, $subject, 'sitevideo_subscribed_channel_comment');
        } elseif (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            return;
        }

        $nestedcomment_commentcreate = Zend_Registry::isRegistered('nestedcomment_commentcreate') ? Zend_Registry::get('nestedcomment_commentcreate') : null;
        if (empty($nestedcomment_commentcreate))
            return;

        if ($subject->getType() == 'siteevent_event') {
            $subjectParent = Engine_Api::_()->getItem($subject->getParent()->getType(), $subject->getParent()->getIdentity());
        }

        $hostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $this->view->form = $form = new Nestedcomment_Form_Comment_Create(array('textareaId' => $subject->getGuid() . "_0"));

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid request method");
            return;
        }

        if (!$form->isValid($this->_getAllParams())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid data");
            return;
        }

        // Process
        // Filter HTML
        $filter = new Zend_Filter();
        $filter->addFilter(new Engine_Filter_Censor());
        $filter->addFilter(new Engine_Filter_HtmlSpecialChars());

        $body = $form->getValue('body');

        $db = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getCommentTable()->getAdapter();
        $db->beginTransaction();

        try {

            //For comment work owner or second user
            $baseOnContentOwner = Engine_Api::_()->seaocore()->baseOnContentOwner($viewer, $subjectParent);
            if ($baseOnContentOwner) {
                $comment = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->addComment($subjectParent, $body);
            } else {
                $comment = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->addComment($viewer, $body);
            }
            $comment->parent_comment_id = $form->getValue('parent_comment_id');

            $comment->save();
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $subjectOwner = $subject->getOwner('user');

            //TRY ATTACHMENT GETTING STUFF
            $attachment = null;
            $attachmentData = $this->getRequest()->getParam('attachment');

            $manifest = Zend_Registry::get('Engine_Manifest');
            if (!empty($attachmentData) && !empty($attachmentData['type'])) {

                if (isset($attachmentData['type']) && $attachmentData['type'] == 'photo' && isset($attachmentData['photo_id']))
                    $attachment = Engine_Api::_()->getItem('album_photo', $attachmentData['photo_id']);

                if (isset($attachmentData['type']) && $attachmentData['type'] == 'link') {
                    $viewer = Engine_Api::_()->user()->getViewer();
                    if (Engine_Api::_()->core()->hasSubject()) {
                        $subject = Engine_Api::_()->core()->getSubject();
                        if ($subject->getType() != 'user') {
                            $attachmentData['parent_type'] = $subject->getType();
                            $attachmentData['parent_id'] = $subject->getIdentity();
                        }
                    }

                    // Filter HTML
                    $filter = new Zend_Filter();
                    $filter->addFilter(new Engine_Filter_Censor());
                    $filter->addFilter(new Engine_Filter_HtmlSpecialChars());
                    if (!empty($attachmentData['title'])) {
                        $attachmentData['title'] = $filter->filter($attachmentData['title']);
                    }
                    if (!empty($attachmentData['description'])) {
                        $attachmentData['description'] = $filter->filter($attachmentData['description']);
                    }

                    $attachment = Engine_Api::_()->getApi('links', 'core')->createLink($viewer, $attachmentData);
                }
            }

            if (empty($nestedcommentGlobalType)) {
                for ($check = 0; $check < strlen($hostType); $check++) {
                    $tempHostType += @ord($hostType[$check]);
                }

                for ($check = 0; $check < strlen($nestedcommentLtype); $check++) {
                    $tempSitemenuLtype += @ord($nestedcommentLtype[$check]);
                }
            }

            // Activity
            if (empty($comment->parent_comment_id)) {
                $action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), $body, array(
                    'owner' => $subjectOwner->getGuid(),
                    'body' => $body,
                    'listingtype' => $listingtypeName
                ));
            } else {
                $action = $activityApi->addActivity($viewer, $subject, 'nestedcomment_' . $subject->getType(), $body, array(
                    'owner' => $subjectOwner->getGuid(),
                    'body' => $body,
                    'listingtype' => $listingtypeName
                ));
            }


            //TRY TO ATTACH IF NECESSARY
            if ($action && $attachment) {
                $activityApi->attachActivity($action, $attachment);
            }

            $composerDatas = $this->getRequest()->getParam('composer', null);

            $tagsArray = array();
            parse_str($composerDatas['tag'], $tagsArray);
            if (!empty($tagsArray)) {

                if ($action) {
                    $actionParams = (array) $action->params;
                    $action->params = array_merge((array) $action->params, array('tags' => $tagsArray));
                    $action->save();
                }
                $viewer = Engine_Api::_()->_()->user()->getViewer();
                $type_name = Zend_Registry::get('Zend_Translate')->translate('post');
                if (is_array($type_name)) {
                    $type_name = $type_name[0];
                } else {
                    $type_name = 'post';
                }
                $notificationAPi = Engine_Api::_()->getDbtable('notifications', 'activity');
                foreach ($tagsArray as $key => $tagStrValue) {
                    $tag = Engine_Api::_()->getItemByGuid($key);
                    if ($action && $tag && ($tag instanceof User_Model_User) && !$tag->isSelf($viewer)) {
                        $notificationAPi->addNotification($tag, $viewer, $action, 'tagged', array(
                            'object_type_name' => $type_name,
                            'label' => $type_name,
                        ));
                    } else if ($tag && ($tag instanceof Sitepage_Model_Page)) {
                        $subject_title = $viewer->getTitle();
                        $page_title = $tag->getTitle();
                        foreach ($tag->getPageAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitepage_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'page_title' => $page_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitebusiness_Model_Business)) {
                        $subject_title = $viewer->getTitle();
                        $business_title = $tag->getTitle();
                        foreach ($tag->getBusinessAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitebusiness_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'business_title' => $business_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitegroup_Model_Group)) {
                        $subject_title = $viewer->getTitle();
                        $store_title = $tag->getTitle();
                        foreach ($tag->getGroupAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitegroup_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'group_title' => $store_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitestore_Model_Store)) {
                        $subject_title = $viewer->getTitle();
                        $store_title = $tag->getTitle();
                        foreach ($tag->getStoreAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitestore_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'store_title' => $store_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Core_Model_Item_Abstract)) {
                        $subject_title = $viewer->getTitle();
                        $item_type = Zend_Registry::get('Zend_Translate')->translate($tag->getShortType());
                        $item_title = $tag->getTitle();
                        $owner = $tag->getOwner();
                        if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                            $notificationAPi->addNotification($owner, $viewer, $action, 'aaf_tagged', array(
                                'subject_title' => $subject_title,
                                'label' => $type_name,
                                'object_type_name' => $type_name,
                                'item_title' => $item_title,
                                'item_type' => $item_type
                            ));
                        }
                        if (($tag instanceof Group_Model_Group)) {
                            foreach ($tag->getOfficerList()->getAll() as $offices) {
                                $owner = Engine_Api::_()->getItem('user', $offices->child_id);
                                if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                    $notificationAPi->addNotification($owner, $viewer, $action, 'aaf_tagged', array(
                                        'subject_title' => $subject_title,
                                        'label' => $type_name,
                                        'object_type_name' => $type_name,
                                        'item_title' => $item_title,
                                        'item_type' => $item_type
                                    ));
                                }
                            }
                        }
                    }
                }

                if ($action) {
                    $data = array_merge((array) $action->params, array('tags' => $tagsArray));
                    $comment->params = Zend_Json::encode($data);
                }
                $comment->save();
            }

            if ($attachment) {
                if (isset($comment->attachment_type))
                    $comment->attachment_type = ( $attachment ? $attachment->getType() : '' );
                if (isset($comment->attachment_id))
                    $comment->attachment_id = ( $attachment ? $attachment->getIdentity() : 0 );
                $comment->save();
            }

            if ((empty($nestedcommentGlobalType)) && (($nestedcommentManageType != $tempHostType) || ($nestedcommentInfoType != $tempSitemenuLtype))) {
                Engine_Api::_()->getApi('settings', 'core')->setSetting('nestedcomment.viewtypeinfo.type', 0);
                $db->rollBack();
                return;
            }

            // Notifications
            // Add notification for owner (if user and not viewer)
            $this->view->subject = $subject->getGuid();
            $this->view->owner = $subjectOwner->getGuid();
            if ((strpos($subject->getType(), "sitepage") === false) || (strpos($subject->getType(), "sitegroup") === false) || (strpos($subject->getType(), "sitestore") === false ) || (strpos($subject->getType(), "sitebusiness") === false) || (strpos($subject->getType(), "sitestoreproduct") !== false)) {
                if ($subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity()) {
                    //start check for page admin and page owner
                    if ($baseOnContentOwner) {
                        $notifyApi->addNotification($subjectOwner, $subjectParent, $subject, 'commented', array(
                            'label' => $subject->getShortType()
                        ));
                    } else {
                        $notifyApi->addNotification($subjectOwner, $viewer, $subject, 'commented', array(
                            'label' => $subject->getShortType()
                        ));
                    }
                    //end check for page admin and page owner
                }
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            $commentedUserNotifications = array();
            foreach (Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getAllCommentsUsers() as $notifyUser) {
                if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
                    continue;

                // Don't send a notification if the user both commented and liked this
                $commentedUserNotifications[] = $notifyUser->getIdentity();

                //start check for page admin and page owner
                if ($baseOnContentOwner) {
                    $notifyApi->addNotification($notifyUser, $subjectParent, $subject, 'commented_commented', array(
                        'label' => $subject->getShortType()
                    ));
                } else {
                    $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
                        'label' => $subject->getShortType()
                    ));
                }
                //end check for page admin and page owner
            }

            // Add a notification for all users that liked
            // @todo we should probably limit this
            foreach (Engine_Api::_()->getDbtable('likes', 'nestedcomment')->likes($subject)->getAllLikesUsers() as $notifyUser) {
                // Skip viewer and owner
                if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
                    continue;

                // Don't send a notification if the user both commented and liked this
                if (in_array($notifyUser->getIdentity(), $commentedUserNotifications))
                    continue;

                //start check for page admin and page owner
                if ($baseOnContentOwner) {
                    $notifyApi->addNotification($notifyUser, $subjectParent, $subject, 'liked_commented', array(
                        'label' => $subject->getShortType()
                    ));
                } else {
                    $notifyApi->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
                        'label' => $subject->getShortType()
                    ));
                }
                //end check for page admin and page owner
            }

            //Send notification to Page admins
            if (strpos($subject->getType(), "sitepage") !== false && Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')) {
                $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
                if ($sitepageVersion >= '4.2.9p3') {
                    Engine_Api::_()->sitepage()->itemCommentLike($subject, 'sitepage_contentcomment', $baseOnContentOwner);
                }
            } elseif (strpos($subject->getType(), "sitegroup") !== false && Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitegroup')) {
                Engine_Api::_()->sitegroup()->itemCommentLike($subject, 'sitegroup_contentcomment', $baseOnContentOwner);
            } elseif (strpos($subject->getType(), "sitebusiness") !== false && Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitebusiness')) {
                Engine_Api::_()->sitebusiness()->itemCommentLike($subject, 'sitebusiness_contentcomment', $baseOnContentOwner);
            } elseif (strpos($subject->getType(), "sitestore") !== false && strpos($subject->getType(), "sitestoreproduct") === false && Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitestore')) {
                Engine_Api::_()->sitestore()->itemCommentLike($subject, 'sitestore_contentcomment', $baseOnContentOwner);
            } elseif (strpos($subject->getType(), "siteevent") !== false && Engine_Api::_()->getDbtable('modules', 'core')->getModule('siteevent')) {
                Engine_Api::_()->siteevent()->itemCommentLike($subject, 'siteevent_contentcomment', $baseOnContentOwner, 'comment');
            }
            //Send notification to Page admins
            // Increment comment count
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $commentCountSelect = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getCommentSelect('DESC');
        $this->view->commentsCount = $commentsCount = Zend_Paginator::factory($commentCountSelect)->getTotalItemCount();
        $this->view->status = true;
        $this->view->message = 'Comment added';
        $this->view->taggingContent = $this->_getParam('taggingContent');
        $this->view->showAsNested = $showAsNested = $this->_getParam('showAsNested');
        $this->view->showAsLike = $showAsLike = $this->_getParam('showAsLike', 1);
        $this->view->showDislikeUsers = $showDislikeUsers = $this->_getParam('showDislikeUsers', 1);
        $this->view->showLikeWithoutIcon = $showLikeWithoutIcon = $this->_getParam('showLikeWithoutIcon', 1);
        $this->view->showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this->_getParam('showLikeWithoutIconInReplies', 1);
        $this->view->showSmilies = $showSmilies = $this->_getParam('showSmilies');
        $this->view->photoLightboxComment = $photoLightboxComment = $this->_getParam('photoLightboxComment');
        $this->view->commentsorder = $commentsorder = $this->_getParam('commentsorder');
        $this->view->body = $this->view->action('list', 'comment', 'nestedcomment', array(
            'type' => $this->_getParam('type'),
            'id' => $this->_getParam('id'),
            'format' => 'html',
            'page' => 0,
            'parent_div' => 1,
            'parent_comment_id' => $comment->parent_comment_id,
            'taggingContent' => $this->_getParam('taggingContent'),
            'showAsNested' => $showAsNested,
            'showAsLike' => $this->_getParam('showAsLike'),
            'showDislikeUsers' => $this->_getParam('showDislikeUsers'),
            'showLikeWithoutIcon' => $this->_getParam('showLikeWithoutIcon'),
            'showLikeWithoutIconInReplies' => $this->_getParam('showLikeWithoutIconInReplies'),
            'showSmilies' => $showSmilies,
            'photoLightboxComment' => $photoLightboxComment,
            'commentsorder' => $commentsorder
        ));
        $this->_helper->contextSwitch->initContext();
    }

    public function deleteAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $autorizationApi = Engine_Api::_()->authorization();
        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $subject = $this->getSubjectItem();
        // Comment id
        $comment_id = $this->_getParam('comment_id');
        if (!$comment_id) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No comment');
            return;
        }

        // Comment
        $comment = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getComment($comment_id);
        if (!$comment) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No comment or wrong parent');
            return;
        }

        $nestedcomment_commentdelete = Zend_Registry::isRegistered('nestedcomment_commentdelete') ? Zend_Registry::get('nestedcomment_commentdelete') : null;
        if (empty($nestedcomment_commentdelete))
            return;
        $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
        // Authorization
        if ($comment->resource_type == "sitepage_page") {
            $page = Engine_Api::_()->getItem($comment->resource_type, $comment->resource_id);
            $this->view->canEdit = $this->view->canDelete = $canDelete = Engine_Api::_()->sitepage()->isManageAdmin($page, 'edit');
            $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
            if (!$poster->isOwner($viewer) && empty($canDelete)) {
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
                return;
            }
        } elseif ($comment->resource_type == "sitebusiness_business") {
            $business = Engine_Api::_()->getItem($comment->resource_type, $comment->resource_id);
            $this->view->canEdit = $this->view->canDelete = $canDelete = Engine_Api::_()->sitebusiness()->isManageAdmin($business, 'edit');
            $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
            if (!$poster->isOwner($viewer) && empty($canDelete)) {
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
                return;
            }
        } elseif ($comment->resource_type == "sitegroup_group") {
            $group = Engine_Api::_()->getItem($comment->resource_type, $comment->resource_id);
            $this->view->canEdit = $this->view->canDelete = $canDelete = Engine_Api::_()->sitegroup()->isManageAdmin($group, 'edit');
            $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
            if (!$poster->isOwner($viewer) && empty($canDelete)) {
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
                return;
            }
        } elseif ($comment->resource_type == "sitestore_store") {
            $store = Engine_Api::_()->getItem($comment->resource_type, $comment->resource_id);
            $this->view->canEdit = $this->view->canDelete = $canDelete = Engine_Api::_()->sitestore()->isManageAdmin($store, 'edit');
            $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
            if (!$poster->isOwner($viewer) && empty($canDelete)) {
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
                return;
            }
        } elseif ($comment->resource_type == 'sitereview_review') {
            $subject = Engine_Api::_()->getItem($comment->resource_type, $comment->resource_id);
            $listingtype_id = $subject->getParent()->listingtype_id;
            $this->view->canEdit = $this->view->canDelete = $canDelete = $autorizationApi->getPermission($level_id, 'sitereview_listing', "edit_listtype_$listingtype_id");
            $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);

            if (empty($canDelete) && !$poster->isSelf($viewer)) {
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
                return;
            }
        } elseif ($comment->resource_type == 'sitereview_listing') {
            $subject = Engine_Api::_()->getItem($comment->resource_type, $comment->resource_id);
            $listingtype_id = $subject->listingtype_id;
            $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
            $this->view->canEdit = $this->view->canDelete = $canDelete = $subject->authorization()->isAllowed($viewer, "edit_listtype_$listingtype_id");
            if (empty($canDelete) && !$poster->isSelf($viewer)) {
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
                return;
            }
        } elseif (!$subject->authorization()->isAllowed($viewer, 'edit') &&
                ($comment->resource_type != $viewer->getType() ||
                $comment->resource_id != $viewer->getIdentity()) && !$poster->isSelf($viewer)) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
            return;
        }

        // Method
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getCommentTable()->getAdapter();
        $db->beginTransaction();

        try {
            Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->removeComment($comment_id);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $commentCountSelect = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getCommentSelect('DESC');

        $this->view->commentsCount = $commentsCount = Zend_Paginator::factory($commentCountSelect)->getTotalItemCount();
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment deleted');
    }

    public function likeAction() {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        $subject = $this->getSubjectItem();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        if ($subject->getType() == 'sitestaticpage_page') {
            $canComment = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestaticpage_page', 'comment');
            if (empty($canComment)) {
                return;
            }
        } else {
            if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
                return;
            }
        }

        $comment_id = $this->_getParam('comment_id');
        $parent_comment_id = $this->_getParam('parent_comment_id');
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        $nestedcomment_commentlike = Zend_Registry::isRegistered('nestedcomment_commentlike') ? Zend_Registry::get('nestedcomment_commentlike') : null;
        if (empty($nestedcomment_commentlike))
            return;

        // Process
        $db = Engine_Api::_()->getDbtable('likes', 'nestedcomment')->likes($commentedItem)->getAdapter();
        $db->beginTransaction();

        try {

            if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->isDislike($commentedItem, $viewer))
                Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->removeDislike($commentedItem, $viewer);

            if (!Engine_Api::_()->getDbtable('likes', 'core')->isLike($commentedItem, $viewer))
                Engine_Api::_()->getDbtable('likes', 'nestedcomment')->likes($commentedItem)->addLike($viewer);

            // Add notification
            $owner = $commentedItem->getOwner();
            $this->view->owner = $owner->getGuid();
            if (strpos($subject->getType(), "sitepage_page") != 'sitepage_page' || strpos($subject->getType(), "sitebusiness_business") != 'sitebusiness_business' || strpos($subject->getType(), "sitegroup_group") != 'sitegroup_group' || strpos($subject->getType(), "sitestore_store") != 'sitestore_store') {
                if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
                    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                    $notifyApi->addNotification($owner, $viewer, $commentedItem, 'liked', array(
                        'label' => $commentedItem->getShortType()
                    ));
                }
            }

            if (strpos($subject->getType(), "sitepage") !== false && Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')) {
                //START PAGE MEMBER PLUGIN WORK AND SEND NOTIFICATION TO PAGE ADMINS.
                $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
                if ($sitepageVersion >= '4.2.9p3') {
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember'))
                        Engine_Api::_()->sitepagemember()->joinLeave($subject, 'Join');
                    Engine_Api::_()->sitepage()->itemCommentLike($subject, 'sitepage_contentlike', '');
                }
            } else if (strpos($subject->getType(), "sitebusiness") !== false && Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitebusiness')) {
                //START PAGE MEMBER PLUGIN WORK AND SEND NOTIFICATION TO PAGE ADMINS.

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessmember'))
                    Engine_Api::_()->sitebusinessmember()->joinLeave($subject, 'Join');
                Engine_Api::_()->sitebusiness()->itemCommentLike($subject, 'sitebusiness_contentlike', '');
            }
            else if (strpos($subject->getType(), "sitegroup") !== false && Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitegroup')) {
                //START PAGE MEMBER PLUGIN WORK AND SEND NOTIFICATION TO PAGE ADMINS.

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember'))
                    Engine_Api::_()->sitegroupmember()->joinLeave($subject, 'Join');
                Engine_Api::_()->sitegroup()->itemCommentLike($subject, 'sitegroup_contentlike', '');
            }else if (strpos($subject->getType(), "sitestore") !== false && strpos($subject->getType(), "sitestoreproduct") === false && Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitestore')) {
                //START PAGE MEMBER PLUGIN WORK AND SEND NOTIFICATION TO PAGE ADMINS.

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember'))
                    Engine_Api::_()->sitestoremember()->joinLeave($subject, 'Join');
                Engine_Api::_()->sitestore()->itemCommentLike($subject, 'sitestore_contentlike', '');
            }
            else if (strpos($subject->getType(), "siteevent") !== false && Engine_Api::_()->getDbtable('modules', 'core')->getModule('siteevent')) {
                //START PAGE MEMBER PLUGIN WORK AND SEND NOTIFICATION TO PAGE ADMINS.
                Engine_Api::_()->siteevent()->itemCommentLike($subject, 'siteevent_contentlike', '', 'like');
            }
            //END PAGE MEMBER PLUGIN WORK AND SEND NOTIFICATION TO PAGE ADMINS.

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject->getType() == 'core_comment') {
            $type = $subject->resource_type;
            $id = $subject->resource_id;
            Engine_Api::_()->core()->clearSubject();
        } else {
            $type = $subject->getType();
            $id = $subject->getIdentity();
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like added');
        $this->view->taggingContent = $this->_getParam('taggingContent');
        $this->view->showAsNested = $this->_getParam('showAsNested');
        $this->view->page = $this->_getParam('page', 0);
        $this->view->showAsLike = $showAsLike = $this->_getParam('showAsLike', 1);
        $this->view->showDislikeUsers = $showDislikeUsers = $this->_getParam('showDislikeUsers', 1);
        $this->view->showLikeWithoutIcon = $showLikeWithoutIcon = $this->_getParam('showLikeWithoutIcon', 1);
        $this->view->showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this->_getParam('showLikeWithoutIconInReplies', 1);
        $this->view->showSmilies = $showSmilies = $this->_getParam('showSmilies');
        $this->view->photoLightboxComment = $photoLightboxComment = $this->_getParam('photoLightboxComment');
        $this->view->commentsorder = $commentsorder = $this->_getParam('commentsorder');

        $this->view->body = $this->view->action('list', 'comment', 'nestedcomment', array(
            'type' => $type,
            'id' => $id,
            'format' => 'html',
            'parent_comment_id' => $parent_comment_id,
            'page' => $this->view->page,
            'parent_div' => 1,
            'taggingContent' => $this->_getParam('taggingContent'),
            'showAsNested' => $this->_getParam('showAsNested'),
            'showAsLike' => $this->_getParam('showAsLike'),
            'showDislikeUsers' => $this->_getParam('showDislikeUsers'),
            'showLikeWithoutIcon' => $this->_getParam('showLikeWithoutIcon'),
            'showLikeWithoutIconInReplies' => $this->_getParam('showLikeWithoutIconInReplies'),
            'showSmilies' => $showSmilies,
            'photoLightboxComment' => $photoLightboxComment,
            'commentsorder' => $commentsorder
        ));
        $this->_helper->contextSwitch->initContext();
    }

    public function unlikeAction() {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        $subject = $this->getSubjectItem();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        if ($subject->getType() == 'sitestaticpage_page') {
            $canComment = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestaticpage_page', 'comment');
            if (empty($canComment)) {
                return;
            }
        } else {
            if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
                return;
            }
        }
        $comment_id = $this->_getParam('comment_id');
        $parent_comment_id = $this->_getParam('parent_comment_id');
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        $nestedcomment_commentunlike = Zend_Registry::isRegistered('nestedcomment_commentunlike') ? Zend_Registry::get('nestedcomment_commentunlike') : null;
        if (empty($nestedcomment_commentunlike))
            return;

        // Process
        $db = Engine_Api::_()->getDbtable('likes', 'nestedcomment')->likes($commentedItem)->getAdapter();
        $db->beginTransaction();

        try {

            if (Engine_Api::_()->getDbtable('likes', 'core')->isLike($commentedItem, $viewer))
                Engine_Api::_()->getDbtable('likes', 'core')->removeLike($commentedItem, $viewer);

            if (!Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->isDislike($commentedItem, $viewer))
                Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->addDislike($commentedItem, $viewer);

            //LIKE NOTIFICATION DELETE
            if (empty($comment_id) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike')) {
                Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type = ?' => 'liked', 'subject_id = ?' => $viewer->getIdentity(), 'subject_type = ?' => $viewer->getType(), 'object_type = ?' => $subject->getType(), 'object_id = ?' => $subject->getIdentity()));
            }
            //LIKE NOTIFICATION DELETE
            //START PAGE MEMBER PLUGIN WORK AND SEND NOTIFICATION TO PAGE ADMINS.
            if (strpos($subject->getType(), "sitepage") !== false && Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')) {
                $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
                if ($sitepageVersion >= '4.2.9p3') {
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember'))
                        Engine_Api::_()->sitepagemember()->joinLeave($subject, 'Join');
                }
            } elseif (strpos($subject->getType(), "sitebusiness") !== false && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessmember')) {
                Engine_Api::_()->sitebusinessmember()->joinLeave($subject, 'Join');
            } elseif (strpos($subject->getType(), "sitegroup") !== false && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
                Engine_Api::_()->sitegroupmember()->joinLeave($subject, 'Join');
            } elseif (strpos($subject->getType(), "sitestore") !== false && strpos($subject->getType(), "sitestoreproduct") === false && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
                Engine_Api::_()->sitestoremember()->joinLeave($subject, 'Join');
            }
            //END PAGE MEMBER PLUGIN WORK AND SEND NOTIFICATION TO PAGE ADMINS.

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject->getType() == 'core_comment') {
            $type = $subject->resource_type;
            $id = $subject->resource_id;
            Engine_Api::_()->core()->clearSubject();
        } else {
            $type = $subject->getType();
            $id = $subject->getIdentity();
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like removed');
        $this->view->taggingContent = $taggingContent = $this->_getParam('taggingContent');
        $this->view->showAsNested = $showAsNested = $this->_getParam('showAsNested');
        $this->view->showAsLike = $showAsLike = $this->_getParam('showAsLike', 1);
        $this->view->showDislikeUsers = $showDislikeUsers = $this->_getParam('showDislikeUsers', 1);
        $this->view->showLikeWithoutIcon = $showLikeWithoutIcon = $this->_getParam('showLikeWithoutIcon', 1);
        $this->view->showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this->_getParam('showLikeWithoutIconInReplies', 1);
        $this->view->page = $this->_getParam('page', 0);
        $this->view->showSmilies = $showSmilies = $this->_getParam('showSmilies');
        $this->view->photoLightboxComment = $photoLightboxComment = $this->_getParam('photoLightboxComment');
        $this->view->commentsorder = $commentsorder = $this->_getParam('commentsorder');
        $this->view->body = $this->view->action('list', 'comment', 'nestedcomment', array(
            'type' => $type,
            'id' => $id,
            'format' => 'html',
            'parent_comment_id' => $parent_comment_id,
            'page' => $this->view->page,
            'parent_div' => 1,
            'taggingContent' => $taggingContent,
            'showAsNested' => $showAsNested,
            'showAsLike' => $showAsLike,
            'showDislikeUsers' => $showDislikeUsers,
            'showLikeWithoutIcon' => $showLikeWithoutIcon,
            'showLikeWithoutIconInReplies' => $showLikeWithoutIconInReplies,
            'showSmilies' => $showSmilies,
            'photoLightboxComment' => $photoLightboxComment,
            'commentsorder' => $commentsorder
        ));
        $this->_helper->contextSwitch->initContext();
    }

    public function getLikesAction() {
        $nestedcomment_commentgetlike = Zend_Registry::isRegistered('nestedcomment_commentgetlike') ? Zend_Registry::get('nestedcomment_commentgetlike') : null;
        if (empty($nestedcomment_commentgetlike))
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = $this->getSubjectItem();
        $likes = Engine_Api::_()->getDbtable('likes', 'nestedcomment')->likes($subject)->getAllLikesUsers();
        $this->view->body = $this->view->translate(array('%s likes this', '%s like this',
            count($likes)), strip_tags($this->view->fluentList($likes)));
        $this->view->status = true;
    }

    public function getSubjectItem() {
        $type = $this->_getParam('type');
        $identity = $this->_getParam('id');

        $nestedcomment_commentgetsubject = Zend_Registry::isRegistered('nestedcomment_commentgetsubject') ? Zend_Registry::get('nestedcomment_commentgetsubject') : null;
        if (empty($nestedcomment_commentgetsubject))
            return;

        if ($type && $identity)
            return $subject = Engine_Api::_()->getItem($type, $identity);
    }

    public function editAction() {
        $this->view->comment = Engine_Api::_()->getItem('core_comment', $this->_getParam('comment_id'));
        $this->view->taggingContent = $taggingContent = $this->_getParam('taggingContent');
        $this->view->showAsNested = $showAsNested = $this->_getParam('showAsNested', 1);
        $this->view->showAsLike = $showAsLike = $this->_getParam('showAsLike', 1);
        $this->view->showDislikeUsers = $showDislikeUsers = $this->_getParam('showDislikeUsers', 1);
        $this->view->showLikeWithoutIcon = $showLikeWithoutIcon = $this->_getParam('showLikeWithoutIcon', 1);
        $this->view->showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this->_getParam('showLikeWithoutIconInReplies', 1);
        $this->view->showSmilies = $showSmilies = $this->_getParam('showSmilies');
        $this->view->photoLightboxComment = $photoLightboxComment = $this->_getParam('photoLightboxComment');
        $this->view->commentsorder = $commentsorder = $this->_getParam('commentsorder');
        $nestedcommentManageType = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.manage.type', 1);
        $nestedcommentInfoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.info.type', 1);
        $hostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $tempHostType = $tempSitemenuLtype = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.global.view', 0);
        $nestedcommentLtype = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.lsettings', null);
        $nestedcommentGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.global.type', 0);
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $this->view->subject = $subject = $this->getSubjectItem();
        $subjectParent = $subject;
        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $nestedcomment_commentedit = Zend_Registry::isRegistered('nestedcomment_commentedit') ? Zend_Registry::get('nestedcomment_commentedit') : null;
        if (empty($nestedcomment_commentedit))
            return;

        // Perms
        $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
        $this->view->canEdit = $this->view->canDelete = $subject->authorization()->isAllowed($viewer, 'edit');
        $autorizationApi = Engine_Api::_()->authorization();
        if (strpos($subject->getType(), "sitepage") !== false) {
            if ($subject->getType() == 'sitepage_page') {
                $pageSubject = $subject;
            } elseif ($subject->getType() == 'sitepagemusic_playlist') {
                $pageSubject = $subject->getParentType();
            } elseif ($subject->getType() == 'sitepagenote_photo') {
                $pageSubject = $subject->getParent()->getParent()->getParent();
            } else {
                $pageSubject = $subject->getParent();
            }
            $pageApi = Engine_Api::_()->sitepage();

            $this->view->canComment = $canComment = $pageApi->isManageAdmin($pageSubject, 'comment');
            $this->view->canEdit = $this->view->canDelete = $pageApi->isManageAdmin($pageSubject, 'edit');
        } elseif (strpos($subject->getType(), "sitebusiness") !== false) {
            if ($subject->getType() == 'sitebusiness_business') {
                $businessSubject = $subject;
            } elseif ($subject->getType() == 'sitebusinessmusic_playlist') {
                $businessSubject = $subject->getParentType();
            } elseif ($subject->getType() == 'sitebusinessnote_photo') {
                $businessSubject = $subject->getParent()->getParent()->getParent();
            } else {
                $businessSubject = $subject->getParent();
            }
            $businessApi = Engine_Api::_()->sitebusiness();

            $this->view->canComment = $canComment = $businessApi->isManageAdmin($businessSubject, 'comment');
            $this->view->canEdit = $this->view->canDelete = $businessApi->isManageAdmin($businessSubject, 'edit');
        } elseif ($subject->getType() == 'sitereview_review') {
            $listingtype_id = $subject->getParent()->listingtype_id;
            $this->view->canComment = $canComment = $autorizationApi->getPermission($level_id, 'sitereview_listing', "review_reply_listtype_$listingtype_id");
            $this->view->canEdit = $this->view->canDelete = $autorizationApi->getPermission($level_id, 'sitereview_listing', "edit_listtype_$listingtype_id");
        } elseif ($subject->getType() == 'sitereview_listing') {
            $listingtype_id = $subject->listingtype_id;
            $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
            if (!empty($canComment))
                $canComment = $subject->authorization()->isAllowed($viewer, "comment_listtype_$listingtype_id");
            $this->view->canComment = $canComment;
            $this->view->canEdit = $this->view->canDelete = $subject->authorization()->isAllowed($viewer, "edit_listtype_$listingtype_id");
        } else if (strpos($subject->getType(), "sitegroup") !== false) {
            if ($subject->getType() == 'sitegroup_group') {
                $groupSubject = $subject;
            } elseif ($subject->getType() == 'sitegroupmusic_playlist') {
                $groupSubject = $subject->getParentType();
            } elseif ($subject->getType() == 'sitegroupnote_photo') {
                $groupSubject = $subject->getParent()->getParent()->getParent();
            } elseif ($subject->getType() == 'sitegroupevent_photo') {
                $groupSubject = $subject->getEvent()->getParentPage();
            } else {
                $groupSubject = $subject->getParent();
            }
            $groupApi = Engine_Api::_()->sitegroup();

            $this->view->canComment = $canComment = $groupApi->isManageAdmin($groupSubject, 'comment');
            $this->view->canEdit = $this->view->canDelete = $groupApi->isManageAdmin($groupSubject, 'edit');
        } else if (strpos($subject->getType(), "sitestore") !== false && strpos($subject->getType(), "sitestoreproduct") === false) {
            if ($subject->getType() == 'sitestore_store') {
                $storeSubject = $subject;
            } elseif ($subject->getType() == 'sitestoremusic_playlist') {
                $storeSubject = $subject->getParentType();
            } elseif ($subject->getType() == 'sitestoreproduct_product') {
                $storeSubject = $subject;
            } elseif ($subject->getType() == 'sitestorenote_photo') {
                $storeSubject = $subject->getParent()->getParent()->getParent();
            } elseif ($subject->getType() == 'sitestoreevent_photo') {
                $storeSubject = $subject->getEvent()->getParentPage();
            } else {
                $storeSubject = $subject->getParent();
            }
            $storeApi = Engine_Api::_()->sitestore();

            $this->view->canComment = $canComment = $storeApi->isManageAdmin($storeSubject, 'comment');
            $this->view->canEdit = $this->view->canDelete = $storeApi->isManageAdmin($storeSubject, 'edit');
        }

        if (empty($nestedcommentGlobalType)) {
            for ($check = 0; $check < strlen($hostType); $check++) {
                $tempHostType += @ord($hostType[$check]);
            }

            for ($check = 0; $check < strlen($nestedcommentLtype); $check++) {
                $tempSitemenuLtype += @ord($nestedcommentLtype[$check]);
            }
        }

        if ($subject->getType() == 'siteevent_event') {
            $subjectParent = Engine_Api::_()->getItem($subject->getParent()->getType(), $subject->getParent()->getIdentity());
        }

        $this->view->nestedCommentPressEnter = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.comment.pressenter');

        if ((empty($nestedcommentGlobalType)) && (($nestedcommentManageType != $tempHostType) || ($nestedcommentInfoType != $tempSitemenuLtype))) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('nestedcomment.viewtypeinfo.type', 0);
        }

        // Likes
        $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
        $this->view->viewAllDislikes = $this->_getParam('viewAllDislikes', false);
        $this->view->likes = $likes = Engine_Api::_()->getDbtable('likes', 'nestedcomment')->likes($subject)->getLikePaginator();
        $this->view->dislikes = $dislikes = Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikePaginator($subject);
        $this->view->parent_comment_id = $parent_comment_id = $this->_getParam('parent_comment_id', 0);

        $this->view->parent_div = $parent_div = $this->_getParam('parent_div', 0);
        $this->view->format = $this->_getParam('format');
        $this->view->order = $order = $this->_getParam('order', 'DESC');
        if (empty($parent_comment_id)) {
            $commentCountSelect = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getCommentSelect($order);

            if (!$showAsNested) {
                $commentCountSelect->where('parent_comment_id =?', 0);
            }
            $this->view->commentsCount = $commentsCount = Zend_Paginator::factory($commentCountSelect)->getTotalItemCount();
        }

        if ($parent_comment_id) {
            $comment_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.reply.per.page', 4);
        } else {
            $comment_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.comment.per.page', 10);
        }

        // Comments
        // If has a page, display oldest to newest
        if (0 !== ( $page = $this->_getParam('page', 0))) {
            $commentSelect = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getCommentSelect($order);
            $commentSelect->where('parent_comment_id =?', $parent_comment_id);
            $commentSelect->order("comment_id $order");
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber($page + 1);
            $comments->setItemCountPerPage($comment_per_page);
            $this->view->comments = $comments;
            $this->view->page = $page;
        }
        // If not has a page, show the
        else {
            $commentSelect = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getCommentSelect($order);
            $commentSelect->where('parent_comment_id =?', $parent_comment_id);
            $commentSelect->order("comment_id $order");
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber(1);
            $comments->setItemCountPerPage($comment_per_page);
            $this->view->comments = $comments;
            $this->view->page = $page;
        }

        $this->view->nested_comment_id = $subject->getGuid() . "_" . $parent_comment_id;

        if ($viewer->getIdentity() && $canComment) {
            $this->view->formComment = $form = new Nestedcomment_Form_Comment_Create(array('textareaId' => $this->view->nested_comment_id));
            if ($parent_comment_id) {
                $form->getElement('submit')->setLabel('Post Reply');
            }

            $form->populate(array(
                'identity' => $subject->getIdentity(),
                'type' => $subject->getType(),
                'format' => 'html',
                'parent_comment_id' => $parent_comment_id,
                'taggingContent' => $taggingContent,
                'showAsNested' => $showAsNested,
                'showAsLike' => $showAsLike,
                'showDislikeUsers' => $showDislikeUsers,
                'showLikeWithoutIcon' => $showLikeWithoutIcon,
                'showLikeWithoutIconInReplies' => $showLikeWithoutIconInReplies,
                'showSmilies' => $showSmilies,
                'photoLightboxComment' => $photoLightboxComment,
                'commentsorder' => $commentsorder
            ));
        }
    }

    public function updateAction() {

        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = $this->getSubjectItem();
        $subjectParent = $subject;

        $viewer_id = $viewer->getIdentity();
        $listingtypeName = "";
        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $nestedcomment_commentupdate = Zend_Registry::isRegistered('nestedcomment_commentupdate') ? Zend_Registry::get('nestedcomment_commentupdate') : null;
        if (empty($nestedcomment_commentupdate))
            return;

        $autorizationApi = Engine_Api::_()->authorization();
        if (strpos($subject->getType(), "sitepage") !== false) {
            if ($subject->getType() == 'sitepage_page') {
                $subjectParent = $subject;
            } elseif ($subject->getType() == 'sitepagemusic_playlist') {
                $subjectParent = $subject->getParentType();
            } elseif ($subject->getType() == 'sitepagenote_photo') {
                $subjectParent = $subject->getParent()->getParent()->getParent();
            } else {
                $subjectParent = $subject->getParent();
            }
            $pageApi = Engine_Api::_()->sitepage();
            $canComment = $pageApi->isManageAdmin($subjectParent, 'comment');
            if (empty($canComment)) {
                $this->view->status = false;
                return;
            }
        } elseif (strpos($subject->getType(), "sitebusiness") !== false) {
            if ($subject->getType() == 'sitebusiness_business') {
                $subjectParent = $subject;
            } elseif ($subject->getType() == 'sitebusinessnote_photo') {
                $subjectParent = $subject->getParent()->getParent()->getParent();
            } elseif ($subject->getType() == 'sitebusinessmusic_playlist') {
                $subjectParent = $subject->getParentType();
            } else {
                $subjectParent = $subject->getParent();
            }
            $businessApi = Engine_Api::_()->sitebusiness();
            $canComment = $businessApi->isManageAdmin($subjectParent, 'comment');
            if (empty($canComment)) {
                $this->view->status = false;
                return;
            }
        } elseif ($subject->getType() == 'sitereview_review') {
            $listingtype_id = $subject->getParent()->listingtype_id;
            $canComment = $autorizationApi->getPermission($level_id, 'sitereview_listing', "review_reply_listtype_$listingtype_id");
            if (empty($canComment)) {
                $this->view->status = false;
                return;
            }
        } elseif ($subject->getType() == 'sitereview_listing') {
            $listingtype_id = $subject->listingtype_id;
            $listingtypeName = strtolower(Engine_Api::_()->getDbTable('listingtypes', 'sitereview')->getListingTypeColumn($listingtype_id, 'title_singular'));
            $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
            if (!empty($canComment))
                $canComment = $subject->authorization()->isAllowed($viewer, "comment_listtype_$listingtype_id");
            if (empty($canComment)) {
                $this->view->status = false;
                return;
            }
        } elseif (strpos($subject->getType(), "sitegroup") !== false) {
            if ($subject->getType() == 'sitegroup_group') {
                $subjectParent = $subject;
            } elseif ($subject->getType() == 'sitegroupmusic_playlist') {
                $subjectParent = $subject->getParentType();
            } elseif ($subject->getType() == 'sitegroupnote_photo') {
                $subjectParent = $subject->getParent()->getParent()->getParent();
            } elseif ($subject->getType() == 'sitegroupevent_photo') {
                $subjectParent = $subject->getEvent()->getParentPage();
            } else {
                $subjectParent = $subject->getParent();
            }
            $groupApi = Engine_Api::_()->sitegroup();
            $canComment = $groupApi->isManageAdmin($subjectParent, 'comment');
            if (empty($canComment)) {
                $this->view->status = false;
                return;
            }
        } elseif (strpos($subject->getType(), "sitestore") !== false && strpos($subject->getType(), "sitestoreproduct") === false) {
            if ($subject->getType() == 'sitestore_store') {
                $subjectParent = $subject;
            } elseif ($subject->getType() == 'sitestoremusic_playlist') {
                $subjectParent = $subject->getParentType();
            } elseif ($subject->getType() == 'sitestorenote_photo') {
                $subjectParent = $subject->getParent()->getParent()->getParent();
            } elseif ($subject->getType() == 'sitestoreevent_photo') {
                $subjectParent = $subject->getEvent()->getParentPage();
            } else {
                $subjectParent = $subject->getParent();
            }
            $storeApi = Engine_Api::_()->sitestore();
            $canComment = $storeApi->isManageAdmin($subjectParent, 'comment');
            if (empty($canComment)) {
                $this->view->status = false;
                return;
            }
        } elseif (!$this->_helper->requireAuth()->setAuthParams($subject, $viewer, 'edit')->isValid()) {
            return;
        }

        if ($subject->getType() == 'siteevent_event') {
            $subjectParent = Engine_Api::_()->getItem($subject->getParent()->getType(), $subject->getParent()->getIdentity());
        }

        $this->view->form = $form = new Nestedcomment_Form_Comment_Create(array('textareaId' => $subject->getGuid() . "_0"));

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid request method");
            return;
        }

        if (!$form->isValid($this->_getAllParams())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid data");
            return;
        }

        // Process
        // Filter HTML
        $filter = new Zend_Filter();
        $filter->addFilter(new Engine_Filter_Censor());
        $filter->addFilter(new Engine_Filter_HtmlSpecialChars());

        $body = $form->getValue('body');

        $db = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getCommentTable()->getAdapter();
        $db->beginTransaction();

        try {
            $comment = Engine_Api::_()->getItem('core_comment', $form->getValue('comment_id'));
            $comment->body = $body;
            $comment->save();
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $subjectOwner = $subject->getOwner('user');

            //TRY ATTACHMENT GETTING STUFF
            $attachment = null;
            $attachmentData = $this->getRequest()->getParam('attachment');

            if (!$attachmentData && ($comment->attachment_type)) {
                $attachment = Engine_Api::_()->getItem($comment->attachment_type, $comment->attachment_id);
                $attachment->delete();
                $comment->attachment_type = '';
                $comment->attachment_id = 0;
                $comment->save();
            }

            $manifest = Zend_Registry::get('Engine_Manifest');
            if (!empty($attachmentData) && !empty($attachmentData['type'])) {

                if (isset($attachmentData['type']) && $attachmentData['type'] == 'photo' && isset($attachmentData['photo_id']))
                    $attachment = Engine_Api::_()->getItem('album_photo', $attachmentData['photo_id']);

                if (isset($attachmentData['type']) && $attachmentData['type'] == 'link') {
                    $viewer = Engine_Api::_()->user()->getViewer();
                    if (Engine_Api::_()->core()->hasSubject()) {
                        $subject = Engine_Api::_()->core()->getSubject();
                        if ($subject->getType() != 'user') {
                            $attachmentData['parent_type'] = $subject->getType();
                            $attachmentData['parent_id'] = $subject->getIdentity();
                        }
                    }

                    // Filter HTML
                    $filter = new Zend_Filter();
                    $filter->addFilter(new Engine_Filter_Censor());
                    $filter->addFilter(new Engine_Filter_HtmlSpecialChars());
                    if (!empty($attachmentData['title'])) {
                        $attachmentData['title'] = $filter->filter($attachmentData['title']);
                    }
                    if (!empty($attachmentData['description'])) {
                        $attachmentData['description'] = $filter->filter($attachmentData['description']);
                    }

                    $attachment = Engine_Api::_()->getApi('links', 'core')->createLink($viewer, $attachmentData);
                }
            }

            //REBUILD PRIVACY
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($subject) as $action) {
                $action->delete();
            }

            // Activity
            if (empty($comment->parent_comment_id)) {
                $action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), $body, array(
                    'owner' => $subjectOwner->getGuid(),
                    'body' => $body,
                    'listingtype' => $listingtypeName
                ));
            } else {
                $action = $activityApi->addActivity($viewer, $subject, 'nestedcomment_' . $subject->getType(), $body, array(
                    'owner' => $subjectOwner->getGuid(),
                    'body' => $body,
                    'listingtype' => $listingtypeName
                ));
            }


            //TRY TO ATTACH IF NECESSARY
            if ($action && $attachment) {
                $activityApi->attachActivity($action, $attachment);
            }

            $composerDatas = $this->getRequest()->getParam('composer', null);

            $tagsArray = array();
            parse_str($composerDatas['tag'], $tagsArray);
            if (!empty($tagsArray)) {

                if ($action) {
                    $actionParams = (array) $action->params;
                    $action->params = array_merge((array) $action->params, array('tags' => $tagsArray));
                    $action->save();
                }
                $viewer = Engine_Api::_()->_()->user()->getViewer();
                $type_name = Zend_Registry::get('Zend_Translate')->translate('post');
                if (is_array($type_name)) {
                    $type_name = $type_name[0];
                } else {
                    $type_name = 'post';
                }
                $notificationAPi = Engine_Api::_()->getDbtable('notifications', 'activity');
                foreach ($tagsArray as $key => $tagStrValue) {
                    $tag = Engine_Api::_()->getItemByGuid($key);
                    if ($tag && ($tag instanceof User_Model_User) && !$tag->isSelf($viewer)) {
                        $notificationAPi->addNotification($tag, $viewer, $action, 'tagged', array(
                            'object_type_name' => $type_name,
                            'label' => $type_name,
                        ));
                    } else if ($tag && ($tag instanceof Sitepage_Model_Page)) {
                        $subject_title = $viewer->getTitle();
                        $page_title = $tag->getTitle();
                        foreach ($tag->getPageAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitepage_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'page_title' => $page_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitebusiness_Model_Business)) {
                        $subject_title = $viewer->getTitle();
                        $business_title = $tag->getTitle();
                        foreach ($tag->getBusinessAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitebusiness_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'business_title' => $business_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitegroup_Model_Group)) {
                        $subject_title = $viewer->getTitle();
                        $store_title = $tag->getTitle();
                        foreach ($tag->getGroupAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitegroup_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'group_title' => $store_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitestore_Model_Store)) {
                        $subject_title = $viewer->getTitle();
                        $store_title = $tag->getTitle();
                        foreach ($tag->getStoreAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitestore_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'store_title' => $store_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Core_Model_Item_Abstract)) {
                        $subject_title = $viewer->getTitle();
                        $item_type = Zend_Registry::get('Zend_Translate')->translate($tag->getShortType());
                        $item_title = $tag->getTitle();
                        $owner = $tag->getOwner();
                        if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                            $notificationAPi->addNotification($owner, $viewer, $action, 'aaf_tagged', array(
                                'subject_title' => $subject_title,
                                'label' => $type_name,
                                'object_type_name' => $type_name,
                                'item_title' => $item_title,
                                'item_type' => $item_type
                            ));
                        }
                        if (($tag instanceof Group_Model_Group)) {
                            foreach ($tag->getOfficerList()->getAll() as $offices) {
                                $owner = Engine_Api::_()->getItem('user', $offices->child_id);
                                if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                    $notificationAPi->addNotification($owner, $viewer, $action, 'aaf_tagged', array(
                                        'subject_title' => $subject_title,
                                        'label' => $type_name,
                                        'object_type_name' => $type_name,
                                        'item_title' => $item_title,
                                        'item_type' => $item_type
                                    ));
                                }
                            }
                        }
                    }
                }

                $data = array_merge((array) $action->params, array('tags' => $tagsArray));
                $comment->params = Zend_Json::encode($data);
                $comment->save();
            }

            if ($attachment) {
                if (isset($comment->attachment_type))
                    $comment->attachment_type = ( $attachment ? $attachment->getType() : '' );
                if (isset($comment->attachment_id))
                    $comment->attachment_id = ( $attachment ? $attachment->getIdentity() : 0 );
                $comment->save();
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $commentCountSelect = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getCommentSelect('DESC');
        $this->view->commentsCount = $commentsCount = Zend_Paginator::factory($commentCountSelect)->getTotalItemCount();
        $this->view->status = true;
        $this->view->message = 'Comment updated';
        $this->view->taggingContent = $this->_getParam('taggingContent');
        $this->view->showAsNested = $showAsNested = $this->_getParam('showAsNested');
        $this->view->showAsLike = $showAsLike = $this->_getParam('showAsLike', 1);
        $this->view->showDislikeUsers = $showDislikeUsers = $this->_getParam('showDislikeUsers', 1);
        $this->view->showLikeWithoutIcon = $showLikeWithoutIcon = $this->_getParam('showLikeWithoutIcon', 1);
        $this->view->showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this->_getParam('showLikeWithoutIconInReplies', 1);
        $this->view->showSmilies = $showSmilies = $this->_getParam('showSmilies');
        $this->view->photoLightboxComment = $photoLightboxComment = $this->_getParam('photoLightboxComment');
        $this->view->commentsorder = $commentsorder = $this->_getParam('commentsorder');
        $this->view->body = $this->view->action('list', 'comment', 'nestedcomment', array(
            'identity' => $subject->getIdentity(),
            'type' => $subject->getType(),
            'format' => 'html',
            'parent_comment_id' => $comment->parent_comment_id,
            'page' => 0,
            'parent_div' => 1,
            'taggingContent' => $this->_getParam('taggingContent'),
            'showAsNested' => $this->_getParam('showAsNested'),
            'showAsLike' => $this->_getParam('showAsLike'),
            'showDislikeUsers' => $this->_getParam('showDislikeUsers'),
            'showLikeWithoutIcon' => $this->_getParam('showLikeWithoutIcon'),
            'showLikeWithoutIconInReplies' => $this->_getParam('showLikeWithoutIconInReplies'),
            'showSmilies' => $showSmilies,
            'photoLightboxComment' => $photoLightboxComment,
            'commentsorder' => $commentsorder
        ));
        $this->_helper->contextSwitch->initContext();
    }

}
