<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_Widget_CommentsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //GET SUBJECT
        $subject = null;
        if (Engine_Api::_()->core()->hasSubject()) {
            $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        } else if (($subject = $this->_getParam('subject'))) {
            list($type, $id) = explode('_', $subject);
            $this->view->subject = $subject = Engine_Api::_()->getItem($type, $id);
        } else if (($type = $this->_getParam('type')) &&
                ($id = $this->_getParam('id'))) {
            $this->view->subject = $subject = Engine_Api::_()->getItem($type, $id);
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->subjectSet = 0;

        if ($this->view->identity && Engine_Api::_()->seaocore()->checkEnabledNestedComment('sitestaticpage_page')) {
            $page_id = $this->getWidgetizedPageId($this->view->identity);
            if ($page_id) {
                $name = $this->getWidgetizedPageName($page_id);
                if ($name) {
                    $explode_array = explode("_", $name);
                    if (isset($explode_array['4'])) {
                        $static_page_id = $explode_array['4'];
                        if ($static_page_id && strstr($name, "sitestaticpage")) {
                            $this->view->subject = $subject = Engine_Api::_()->getItem('sitestaticpage_page', $static_page_id);
                            if (empty($this->view->subject))
                                $this->view->subjectSet = 0;
                        }
                    }
                }
            }
        }

        if (empty($subject)) {

            if (!$viewer->getIdentity()) {
                return $this->setNoRender();
            }

            if ((isset($viewer->level_id) && $viewer->level_id != 1)) {
                return $this->setNoRender();
            }
        }

        if ($subject) {
            $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Nestedcomment/View/Helper', 'Nestedcomment_View_Helper');
            $this->view->subjectSet = 1;
            $params = $this->_getAllParams();
            $nestedcomment_verfications = Zend_Registry::isRegistered('nestedcomment_verfications') ? Zend_Registry::get('nestedcomment_verfications') : null;
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

            if ($this->_getParam('taggingContent')) {
                $this->view->taggingContent = implode($this->_getParam('taggingContent'), ",");
            }

            if (empty($nestedcomment_verfications))
                return $this->setNoRender();

            $this->view->showComposerOptions = $showComposerOptions = $this->_getParam('showComposerOptions', array('addLink', 'addPhoto', 'addSmilies'));

            $this->view->showAsNested = $this->_getParam('showAsNested', 1);
            $this->view->showAsLike = $this->_getParam('showAsLike', 1);
            $this->view->commentsorder = $this->_getParam('commentsorder', 1);
            $this->view->showDislikeUsers = $this->_getParam('showDislikeUsers', 1);
            $this->view->showLikeWithoutIcon = $this->_getParam('showLikeWithoutIcon', 1);
            $this->view->showLikeWithoutIconInReplies = $this->_getParam('showLikeWithoutIconInReplies', 1);
            $this->view->showAddLink = 0;
            $this->view->showAddPhoto = 0;
            $this->view->showSmilies = 0;
            $this->view->photoLightboxComment = $this->_getParam('photoLightboxComment', 0);

            if (!empty($showComposerOptions)) {
                if (in_array('addLink', $showComposerOptions)) {
                    $this->view->showAddLink = 1;
                }

                if (in_array('addPhoto', $showComposerOptions)) {
                    $this->view->showAddPhoto = 1;
                }
                if (in_array('addSmilies', $showComposerOptions)) {
                    $this->view->showSmilies = 1;
                }
            }

            $this->view->nestedCommentPressEnter = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.comment.pressenter', 1);


            $subjectParent = $subject;
            //GET USER LEVEL ID
            if (!empty($viewer_id)) {
                $level_id = $viewer->level_id;
            } else {
                $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
            }

            if ($subject->getType() == 'sitestaticpage_page') {
                $this->view->canComment = $canComment = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestaticpage_page', 'comment');
                $this->view->canEdit = $this->view->canDelete = $canDelete = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestaticpage_page', 'edit');
            } else {
                $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
                $this->view->canEdit = $this->view->canDelete = $canDelete = $subject->authorization()->isAllowed($viewer, 'edit');
            }

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
                $this->view->canEdit = $this->view->canDelete = $canDelete = $pageApi->isManageAdmin($pageSubject, 'edit');
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
                $this->view->canEdit = $this->view->canDelete = $canDelete = $businessApi->isManageAdmin($businessSubject, 'edit');
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
                $this->view->canEdit = $this->view->canDelete = $canDelete = $groupApi->isManageAdmin($groupSubject, 'edit');
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
                $this->view->canEdit = $this->view->canDelete = $canDelete = $storeApi->isManageAdmin($storeSubject, 'edit');
            }

            if ($subject->getType() == 'siteevent_event') {
                $subjectParent = Engine_Api::_()->getItem($subject->getParent()->getType(), $subject->getParent()->getIdentity());
            }
        }
        $commentSelect = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->comments($subject)->getCommentSelect();
        $comments = Zend_Paginator::factory($commentSelect);
        $this->view->likes = $likes = Engine_Api::_()->getDbtable('likes', 'nestedcomment')->likes($subject)->getLikePaginator();
        $this->view->dislikes = $dislikes = Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikePaginator($subject);
        // Hide if can't post and no comments
        if (!$canComment && !$canDelete && count($comments) <= 0 && count($likes) <= 0 && count($dislikes) <= 0) {
            $this->setNoRender();
        }
    }

    /**
     * Gets widgetized page
     *
     * @return Zend_Db_Table_Select
     */
    public function getWidgetizedPageId($content_id) {

        //GET CORE CONTENT TABLE
        $tableNamecore = Engine_Api::_()->getDbtable('content', 'core');
        $page_id = $tableNamecore->select()
                ->from($tableNamecore->info('name'), 'page_id')
                ->where('content_id =?', $content_id)
                ->where('name =?', 'nestedcomment.comments')
                ->query()
                ->fetchColumn();
        return $page_id;
    }

    public function getWidgetizedPageName($page_id) {
        //GET CORE CONTENT TABLE
        $tableNamePages = Engine_Api::_()->getDbtable('pages', 'core');
        $name = $tableNamePages->select()
                ->from($tableNamePages->info('name'), 'name')
                ->where('page_id =?', $page_id)
                ->query()
                ->fetchColumn();
        return $name;
    }

}
