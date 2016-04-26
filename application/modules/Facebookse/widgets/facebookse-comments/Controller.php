<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 8427 2011-02-09 23:11:24Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Facebookse_Widget_FacebookseCommentsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        // Get subject
        $subject = null;
        $type = null;
        $id = null;
        if (($subject = $this->_getParam('subject'))) {

            $Subject_id = explode('_', $subject);
            if (count($Subject_id) >= 3) {
                $type = $Subject_id[0] . '_' . $Subject_id[1];
                $id = $Subject_id[2];
            } else
                list($type, $id) = explode('_', $subject);

            $subject = Engine_Api::_()->getItem($type, $id);
        } else if (($type = $this->_getParam('type')) &&
                ($id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null))) {

            if ($type == 'member')
                $type = 'user';

            $subject = Engine_Api::_()->getItem($type, $id);
        }
        else if (Engine_Api::_()->core()->hasSubject()) {

            $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
            $type = $subject->getType();
            $id = $subject->getIdentity();
        }

        $this->view->subject = $subject;

        $viewer = Engine_Api::_()->user()->getViewer();
        $noPrivacyCheck = 0;
        $this->view->isajax = $isajax = @$this->_getParam('task', 0);

        $type_temp = $this->_getParam('type');

        if (empty($type)) {
            $type = $type_temp;
        }

        $module_type = @$this->_getParam('module_type', 0);
        if (empty($module_type)) {
            //GET MODULE INFO FROM THE FACEBOOK MIXSETTING TABLE

            $module_type = $type;
        }

        $this->view->removeCommentBoxClass = '.comments_options';
        $this->view->type = $type;
        $this->view->identity = $id;

        if ($subject && $type == 'sitereview_listing') {
            $type = 'sitereview_listing_' . $subject->listingtype_id;
            $id = $subject->listing_id;
            $this->view->removeCommentBoxClass = '.seaocore_replies_wrapper';
        }

        if ((empty($type) || empty($id))) {

            if (Engine_Api::_()->core()->hasSubject()) {
                $this->view->type = Engine_Api::_()->core()->getSubject()->getType();
                $this->view->identity = Engine_Api::_()->core()->getSubject()->getIdentity();
            }
        }

        $front = Zend_Controller_Front::getInstance();
        if (!empty($isajax)) {

            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
            $this->view->curr_url = $curr_url = $this->_getParam('curr_url', ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $front->getRequest()->getRequestUri());
        }
        //CHECKING IF THE REQUEST IS AJAX REQUEST
        if (!empty($isajax) && $isajax == 1) {

            $comment_setting = 0;
            $commentbox_privacy = '1';
            $width = '500';
            $color = 'lignt';

            //SPECIAL CASE FOR ALBUM PHOTO.  			
            if ($module_type == 'album_photo')
                $module_type = 'album';

            if ($type == 'sitepage_album')
                $type = 'sitepage_photo';

            if ($type == 'sitebusiness_album')
                $type = 'sitebusiness_photo';

            if ($type == 'sitegroup_album')
                $type = 'sitegroup_photo';

            $mixsettingsTable = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');
            $select = $mixsettingsTable->select(array('module'))
                    ->where('resource_type = ?', $type);
            $permissionTable_Comments = $mixsettingsTable->fetchRow($select);

            if (!empty($permissionTable_Comments)) {
                $permissionTable_Comments = $permissionTable_Comments->toArray();
                $comment_setting = $permissionTable_Comments['commentbox_enable'];
                $commentbox_privacy = $permissionTable_Comments['commentbox_privacy'];
                $width = $permissionTable_Comments['commentbox_width'];
                $color = $permissionTable_Comments['commentbox_color'];
            }

            $this->view->comment_setting = $comment_setting;
            $this->view->comment_privacy = $commentbox_privacy;
            $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
            if (!empty($this->view->comment_privacy) && (!($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity() || (!method_exists($subject, 'comments') && !method_exists($subject, 'likes')))) {
                return $this->setNoRender();
            }

            $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
            if (!empty($this->view->comment_privacy) && empty($canComment))
                return $this->setNoRender();

            $this->view->width = $width;
            $this->view->color = $color;
        }
        else if (!empty($isajax) && $isajax == 2) {

            $comment_setting = 3;
            $this->view->comment_privacy = '1';
            $width = '450';
            $color = 'light';

            //GETTING THE SETTING FOR COMMENT BOX FOR THIS MODULE.
            $mixsettingsTable = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');
            $select = $mixsettingsTable->select(array('module'))->where('resource_type = ?', $type);
            $permissionTable_Comments = $mixsettingsTable->fetchRow($select);

            if (!empty($permissionTable_Comments)) {
                $permissionTable_Comments = $permissionTable_Comments->toArray();
                $comment_setting = $permissionTable_Comments['commentbox_enable'];
                $this->view->comment_privacy = $commentbox_privacy = $permissionTable_Comments['commentbox_privacy'];
                $width = $permissionTable_Comments['commentbox_width'];
                $color = $permissionTable_Comments['commentbox_color'];
            }

            if (empty($comment_setting)) {
                return $this->setNoRender();
            }

            if (!empty($this->view->comment_privacy) && (!($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity() || (!method_exists($subject, 'comments') && !method_exists($subject, 'likes')))) {
                return $this->setNoRender();
            }

            $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
            if (!empty($this->view->comment_privacy) && empty($canComment))
                return $this->setNoRender();

            $this->view->comment_setting = $comment_setting;
            //$this->view->NoofPost = $NoofPost;
            $this->view->width = $width;
            $this->view->color = $color;
        }
        else {

            $this->view->comment_privacy = $commentbox_privacy = $this->_getParam('commentbox_privacy');

            if (!empty($this->view->comment_privacy) && (!($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity() || (!method_exists($subject, 'comments') && !method_exists($subject, 'likes')) )) {
                return $this->setNoRender();
            }

            if ($viewer->getIdentity() && ($subject instanceof Core_Model_Item_Abstract) && $subject->getIdentity()) {
                $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
                if (!empty($this->view->comment_privacy) && empty($canComment))
                    return $this->setNoRender();
            }

            $curr_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $front->getRequest()->getRequestUri();
            $this->view->comment_setting = $this->_getParam('enable', 1);

            if (empty($this->view->comment_setting)) {
                return $this->setNoRender();
            }

            $this->view->width = $this->_getParam('commentbox_width', 450);
            $this->view->color = $this->_getParam('commentbox_color', 'light');

            //CHECK IF ADMIN HAS ENABLED OR NOT THIS MODULE AT MANAGE MODULE SECTION.
            if ($subject) {
                $resourcetype = $subject->getType();

                if ($resourcetype == 'sitereview_listing') {
                    $resourcetype = $resourcetype . '_' . $subject->listingtype_id;
                }

                $module = $subject->getModuleName();
            } else {
                $resourcetype = '';
                $module = $front->getRequest()->getModuleName();
            }

            $enable_managemodule = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->isModuleEnbled($module, $resourcetype);

            if (empty($enable_managemodule))
                return $this->setNoRender();
        }

        $this->view->show_likeunlike = true;

        // Like
        if ((($subject instanceof Core_Model_Item_Abstract) && $subject->getIdentity())) {
            $this->view->subject = $subject_Like = new Engine_ProxyObject($subject, Engine_Api::_()->getDbtable('likes', 'core'));
            $this->view->islike = $like = $subject_Like->isLike($viewer);
            $like_unlike = $this->_getParam('like_unlike', 0);

            //IF THE CASE IS FOR LIKE
            // Process

            $commentedItem = $subject;
            try {
                $db = $commentedItem->likes()->getAdapter();
                $db->beginTransaction();
                if (!empty($like_unlike)) {

                    if ($this->_getParam('like_unlike') == 'like' && empty($like)) {
                        $commentedItem->likes()->addLike($viewer);
                        $like = 1;

                        // Add notification
                        $owner = $commentedItem->getOwner();
                        $this->view->owner = $owner->getGuid();

                        if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
                            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                            $notifyApi->addNotification($owner, $viewer, $commentedItem, 'liked', array(
                                'label' => $commentedItem->getShortType()
                            ));
                        }

                        //SEND LIKE FEED INTO THE ACTIVITY FEED IF SITELIKE PLUGIN IS ENABLED.
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike')) {
                            $object = $subject;
                            Engine_Api::_()->sitelike()->setLikeFeed($viewer, $object);
                        }

                        // Stats
                        Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');
                    } elseif ($this->_getParam('like_unlike') == 'unlike' && !empty($like)) {
                        $commentedItem->likes()->removeLike($viewer);
                        $like = 0;

//REMOVE LIKE FEED INTO THE ACTIVITY FEED IF SITELIKE PLUGIN IS ENABLED.
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike')) {
                            $object = $subject;
                            Engine_Api::_()->sitelike()->removeLikeFeed($viewer, $object);
                        }
                    }
                    $this->view->islike = $like;
                }

                $this->view->getAllLikesUsers = $subject->likes()->getAllLikesUsers();

                $db->commit();
            } catch (Exception $e) {
                $this->view->show_likeunlike = false;
                //$db->rollBack();
                //throw $e;
            }

            $this->view->like_unlike = $like_unlike;
            $this->view->likes = $likes = $subject_Like->getLikePaginator();
            $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
        }

        //EXPLOAD THE URL WITH THE QUERY STRING IF IT EXIST THERE.
        $curr_url_temp = explode('?', $curr_url);
        $curr_url = $curr_url_temp[0];
        $this->view->curr_url = $curr_url;

        if (!empty($curr_url) && !Engine_Api::_()->authorization()->isAllowed($subject, 'everyone', 'view')) {
            if (strstr($curr_url, '?'))
                $curr_url .= '&contentid=' . $id . '&type=' . $type;
            else
                $curr_url .= '?contentid=' . $id . '&type=' . $type;
            $this->view->curr_url = $curr_url;
        }
    }

}
