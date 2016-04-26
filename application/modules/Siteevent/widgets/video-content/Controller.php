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
class Siteevent_Widget_VideoContentController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //GET VIDEO ID AND OBJECT
        $video_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id', $this->_getParam('video_id', null));
        $siteevent_video = Engine_Api::_()->getItem('siteevent_video', $video_id);

        if (empty($siteevent_video)) {
            return $this->setNoRender();
        }

        //GET TAB ID
        $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id');

        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //IF THIS IS SENDING A MESSAGE ID, THE USER IS BEING DIRECTED FROM A CONVERSATION
        //CHECK IF MEMBER IS PART OF THE CONVERSATION
        $message_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('message');
        $message_view = false;
        if ($message_id) {
            $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
            if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer()))
                $message_view = true;
        }
        $this->view->message_view = $message_view;

        //SET SITEEVENT SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $siteevent_video->event_id);

        $this->view->can_create = Engine_Api::_()->siteevent()->allowVideo($siteevent, $viewer);

        $this->view->allowView = $siteevent_video->authorization()->isAllowed($viewer, "view");

        $can_edit = $this->view->can_edit = $siteevent_video->canEdit();

        if ($can_edit != 1 && ($siteevent_video->status != 1 || $siteevent_video->search != 1)) {
            return $this->setNoRender();
        }

        //GET VIDEO TAGS
        $this->view->videoTags = $siteevent_video->tags()->getTagMaps();

        //CHECK IF EMBEDDING IS ALLOWED
        $can_embed = true;
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.video.embeds', 1)) {
            $can_embed = false;
        } else if (isset($siteevent_video->allow_embed) && !$siteevent_video->allow_embed) {
            $can_embed = false;
        }
        $this->view->can_embed = $can_embed;

        $this->view->videoEmbedded = $embedded = "";

        //INCREMENT IN NUMBER OF VIEWS
        $owner = $siteevent_video->getOwner();
        if (!$owner->isSelf($viewer)) {
            $siteevent_video->view_count++;
        }
        $siteevent_video->save();

        if ($siteevent_video->type != 3) {
            $this->view->videoEmbedded = $embedded = $siteevent_video->getRichContent(true);
        }

        //SET EVENT-VIDEO SUBJECT
        if (Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->clearSubject();
        }
        Engine_Api::_()->core()->setSubject($siteevent_video);

        //VIDEO FROM MY COMPUTER WORK
        if ($siteevent_video->type == 3 && $siteevent_video->status != 0) {
            $siteevent_video->save();

            if (!empty($siteevent_video->file_id)) {
                $storage_file = Engine_Api::_()->getItem('storage_file', $siteevent_video->file_id);
                if ($storage_file) {
                    $this->view->video_location = $storage_file->map();
                    $this->view->video_extension = $storage_file->extension;
                }
            }
        }

        $this->view->rating_count = Engine_Api::_()->getDbTable('videoratings', 'siteevent')->ratingCount($siteevent_video->getIdentity());
        $this->view->video = $siteevent_video;
        $this->view->rated = Engine_Api::_()->getDbTable('videoratings', 'siteevent')->checkRated($siteevent_video->getIdentity(), $viewer->getIdentity());

        //TAG WORK
        $this->view->limit_siteevent_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventvideo.tag.limit', 3);

        //VIDEO TABLE
        $videoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');

        //TOTAL VIDEO COUNT FOR THIS EVENT
        $this->view->count_video = $videoTable->getEventVideoCount($siteevent_video->event_id);
    }

}