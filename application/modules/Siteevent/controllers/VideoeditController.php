<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: VideoeditController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_VideoeditController extends Seaocore_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //LOGGED IN USER CAN EDIT OR DELETE VIDEO
        if (!$this->_helper->requireUser()->isValid())
            return;

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            return;

        //SET SUBJECT
        $event_id = $this->_getParam('event_id', $this->_getParam('event_id', null));
        if ($event_id) {
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
            if ($siteevent) {
                Engine_Api::_()->core()->setSubject($siteevent);
            }
        }

        //SITEEVENT SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject()->isValid()) {
            return;
        }
    }

    //ACTION FOR EDIT THE VIDEO
    public function editAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET SITEEVENT SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject();

        $this->view->slideShowEnanle = $this->slideShowEnable();

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }

        $this->view->content_id = Engine_Api::_()->siteevent()->getTabId('siteevent.video-siteevent');

        //SELECTED TAB
        $this->view->TabActive = "video";

        //GET VIDEOS
        $this->view->type_video = $type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video');

        $siteeventOtherInfo = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getOtherinfo($siteevent->event_id);
        if ($type_video && isset($siteeventOtherInfo->main_video['corevideo_id'])) {
            $this->view->main_video_id = $siteeventOtherInfo->main_video['corevideo_id'];
        } elseif (isset($siteeventOtherInfo->main_video['reviewvideo_id'])) {
            $this->view->main_video_id = $siteeventOtherInfo->main_video['reviewvideo_id'];
        }

        $this->view->videos = $videos = array();


        $this->view->videos = $videos = array();
        $this->view->integratedWithVideo = false;
        $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
        if ($sitevideoEnabled && (Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => "siteevent_event", 'item_module' => 'siteevent')))) {
            $params = array();
            $params['parent_type'] = $siteevent->getType();
            $params['parent_id'] = $siteevent->getIdentity();
            $this->view->videos = $videos = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
            $this->view->integratedWithVideo = true;
        } else {
            if (Engine_Api::_()->siteevent()->enableVideoPlugin() && !empty($type_video)) {
                $this->view->videos = $videos = Engine_Api::_()->getItemTable('siteevent_clasfvideo', 'siteevent')->getEventVideos($siteevent->event_id, 0, 1);
            } elseif (empty($type_video)) {
                $this->view->videos = $videos = Engine_Api::_()->getItemTable('siteevent_clasfvideo', 'siteevent')->getEventVideos($siteevent->event_id, 0, 0);
            }
        }
        //PACKAGE BASED CHECKS - AUTHORIZATION CHECK
        $allowed_upload_video = Engine_Api::_()->siteevent()->allowVideo($siteevent, $viewer, count($videos), $uploadVideo = 1);
        $this->view->upload_video = 1;
        if (Engine_Api::_()->siteevent()->hasPackageEnable()) {
            $this->view->upload_video = $allowed_upload_video;
        } else {
            if (empty($allowed_upload_video)) {
                return $this->_forwardCustom('requireauth', 'error', 'core');
            }
        }
        $this->view->count = count($videos);

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Video_Editvideo();

        foreach ($videos as $video) {

            $subform = new Siteevent_Form_Video_Edit(array('elementsBelongTo' => $video->getGuid()));

            if ($video->status != 1) {
                if ($video->status == 0 || $video->status == 2):
                    $msg = $this->view->translate("Your video is currently being processed - you will be notified when it is ready to be viewed.");
                elseif ($video->status == 3):
                    $msg = $this->view->translate("Video conversion failed. Please try again.");
                elseif ($video->status == 4):
                    $msg = $this->view->translate("Video conversion failed. Video format is not supported by FFMPEG. Please try again.");
                elseif ($video->status == 5):
                    $msg = $this->view->translate("Video conversion failed. Audio files are not supported. Please try again.");
                elseif ($video->status == 7):
                    $msg = $this->view->translate("Video conversion failed. You may be over the site upload limit.  Try  a smaller file, or delete some files to free up space.");
                endif;

                $subform->addElement('dummy', 'mssg' . $video->video_id, array(
                    'description' => $msg,
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div', 'class' => 'tip')),
                        array('Description', array('tag' => 'span', 'placement' => 'APPEND')),
                        array('Description', array('placement' => 'APPEND')),
                    ),
                ));
                $t = 'mssg' . $video->video_id;
                $subform->$t->getDecorator("Description")->setOption("placement", "append");
            }
            $subform->populate($video->toArray());
            $form->addSubForm($subform, $video->getGuid());
        }

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //GET FORM VALUES
        $values = $form->getValues();
        $siteeventOtherInfo = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getOtherinfo($siteevent->event_id);
        if (isset($_POST['corevideo_cover']) && !empty($_POST['corevideo_cover'])) {
            if (isset($siteeventOtherInfo->main_video) && !empty($siteeventOtherInfo->main_video)) {
                $siteeventOtherInfo->main_video = array_merge((array) $siteeventOtherInfo->main_video, array('corevideo_id' => $_POST['corevideo_cover']));
            } else {
                $siteeventOtherInfo->main_video = array('corevideo_id' => $_POST['corevideo_cover']);
            }
        } elseif (isset($_POST['reviewvideo_cover']) && $_POST['reviewvideo_cover']) {
            if (isset($siteeventOtherInfo->main_video) && !empty($siteeventOtherInfo->main_video)) {
                $siteeventOtherInfo->main_video = array_merge((array) $siteeventOtherInfo->main_video, array('reviewvideo_id' => $_POST['reviewvideo_cover']));
            } else {
                $siteeventOtherInfo->main_video = array('reviewvideo_id' => $_POST['reviewvideo_cover']);
            }
        }
        $siteeventOtherInfo->save();
        $siteevent->save();

        //VIDEO SUBFORM PROCESS IN EDITING
        foreach ($videos as $video) {
            $subform = $form->getSubForm($video->getGuid());

            $values = $subform->getValues();
            $values = $values[$video->getGuid()];
            if (isset($values['delete']) && $values['delete'] == '1') {
                Engine_Api::_()->getDbtable('videos', 'siteevent')->delete(array('video_id = ?' => $video->video_id, 'event_id = ?' => $siteevent->event_id));
                Engine_Api::_()->getDbtable('actions', 'activity')->delete(array('type = ?' => 'video_siteevent', 'object_id = ?' => $siteevent->event_id));
                Engine_Api::_()->getDbtable('actions', 'activity')->delete(array('type = ?' => 'video_siteevent_parent', 'object_id = ?' => $siteevent->event_id));
            } else {
                $video->setFromArray($values);
                $video->save();
            }
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'edit', 'event_id' => $siteevent->event_id), "siteevent_videospecific", true);
    }

    public function deleteAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET VIDEO ID
        $video_id = $this->_getParam('video_id');
        $viewer_id = $viewer->getIdentity();

        //GET SITEEVENT SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject();

        $can_edit = $siteevent->authorization()->isAllowed($viewer, 'edit');

        $siteevent_video = $video = Engine_Api::_()->getItem('video', $this->_getParam('video_id'));

        //VIDEO OWNER AND EVENT OWNER CAN DELETE VIDEO
        if ($viewer_id != $siteevent_video->owner_id && $can_edit != 1) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                Engine_Api::_()->getDbtable('clasfvideos', 'siteevent')->delete(array('event_id = ?' => $siteevent->event_id, 'video_id = ?' => $video_id));
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forwardCustom('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => '500',
                'parentRefreshTime' => '500',
                'format' => 'smoothbox',
                'messages' => Zend_Registry::get('Zend_Translate')->_('You have successfully deleted this video.')
            ));
        }
    }

    public function slideShowEnable() {
        //GET CONTENT TABLE
        $tableContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableContentName = $tableContent->info('name');

        //GET PAGE TABLE
        $tablePage = Engine_Api::_()->getDbtable('pages', 'core');
        $tablePageName = $tablePage->info('name');
        //GET PAGE ID
        $page_id = $tablePage->select()
                ->from($tablePageName, array('page_id'))
                ->where('name = ?', "siteevent_index_view")
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {
            return false;
        }

        $content_id = $tableContent->select()
                ->from($tableContent->info('name'), array('content_id'))
                ->where('page_id = ?', $page_id)
                ->where('name = ?', 'siteevent.slideshow-list-photo')
                ->query()
                ->fetchColumn();

        if ($content_id)
            return true;

        $params = $tableContent->select()
                ->from($tableContent->info('name'), array('params'))
                ->where('page_id = ?', $page_id)
                ->where('name = ?', 'siteevent.editor-reviews-siteevent')
                ->query()
                ->fetchColumn();
        if ($params) {
            $params = Zend_Json::decode($params);
            if (!isset($params['show_slideshow']) || $params['show_slideshow']) {
                return true;
            }
            return false;
        }
    }

}
