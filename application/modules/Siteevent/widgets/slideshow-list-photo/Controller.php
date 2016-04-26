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
class Siteevent_Widget_SlideshowListPhotoController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //CHECK SUBJECT
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        //START PACKAGE WORK
        $hasPackageEnable = Engine_Api::_()->siteevent()->hasPackageEnable();
        if ($hasPackageEnable && !Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "photo") && !Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "video")) {
          return $this->setNoRender();
        }
        //END PACKAGE WORK
        
        $this->view->showButtonSlide = $showButtonSlide = $this->_getParam('showButtonSlide', 1);
        $this->view->mouseEnterEvent = $this->_getParam('mouseEnterEvent', 0);
        $this->view->thumbPosition = $thumbPosition = $this->_getParam('thumbPosition', 'bottom');
        $this->view->autoPlay = $this->_getParam('autoPlay', 0);

        //GET SLIDESHOW WIDTH AND HEIGHT
        $this->view->slideshow_width = $slideshow_width = $this->_getParam('slideshow_width', 600);
        $this->view->slideshow_height = $slideshow_height = $this->_getParam('slideshow_height', 400);
        $this->view->showCaption = $this->_getParam('showCaption', 1);
        $this->view->captionTruncation = $this->_getParam('captionTruncation', 200);

        $event_id = $siteevent->getIdentity();
        
        // GET THE VIDEO TYPE WHICH WE ARE USING CURRENTLY SOCIALENGINE VIDEO OR SEPARATE VIDEO
        $type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video');
        $siteeventOtherInfo = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getOtherinfo($siteevent->event_id);
        // GET THE VALUE OF VIDEO WHICH IS THE MAIN VIDEO IN THE EVENT
        $this->view->params = $params = $siteeventOtherInfo->main_video;

        if (!empty($params)) {
            $this->view->video = $video = Engine_Api::_()->siteevent()->GetEventVideo($params, $type_video);
        }

        // GET THE EMBEDED CODE OF VIDEO
        if (!empty($video)) {
            $this->view->videoEmbedded = $video->getRichContent(true);
        }

        $this->view->show_slideshow_object = Engine_Api::_()->getDbtable('photos', 'siteevent')->GetEventPhoto($event_id, array('show_slidishow' => 1, 'limit' => $this->_getParam('slidesLimit', 20), 'order' => 'order ASC'));

        //START - PACKAGE BASED CHECKS
        if ($hasPackageEnable && !Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "photo")) {
          $this->view->show_slideshow_object = array();
        }
        //END - PACKAGE BASED CHECKS
        
        if (!empty($video)) {
            $main_video_result = array();
            $main_video_result[0] = $video->toArray();
            //START - PACKAGE BASED CHECKS
            if ($hasPackageEnable) {
              if (!Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "video"))
                $main_video_result[0] = array();
              else
                $this->view->show_slideshow_object = array_merge($main_video_result, $this->view->show_slideshow_object);
            } else
              $this->view->show_slideshow_object = array_merge($main_video_result, $this->view->show_slideshow_object);
            //END - PACKAGE BASED CHECKS
            if ($video->type == 3 && $video->status == 1) {
                if (!empty($video->file_id)) {
                    $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
                    if ($storage_file) {
                        $this->view->video_location = $storage_file->map();
                    }
                }
            }
        }

        //RESULTS COUNT
        $this->view->num_of_slideshow = $num_of_slideshow = Count($this->view->show_slideshow_object);
        if ($this->view->num_of_slideshow <= 0) {
            return $this->setNoRender();
        }


        if ($showButtonSlide) {
            if ($thumbPosition == 'bottom') {
                $this->view->slidesPerRow = floor($slideshow_width / 50);
                $this->view->totalRow = ceil($num_of_slideshow / $this->view->slidesPerRow);
            } else {
                $this->view->slidesPerColumn = floor($slideshow_height / 50);
                $this->view->totalColumn = ceil($num_of_slideshow / $this->view->slidesPerColumn);
            }
        }
    }

}
