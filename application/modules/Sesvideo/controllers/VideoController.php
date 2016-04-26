<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: VideoController.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_VideoController extends Core_Controller_Action_Standard {

  public function init() {
    // Must be able to use videos
    if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid()) {
      return;
    }

    // Get subject
    $video = null;
    $id = $this->_getParam('video_id', $this->_getParam('id', null));
    if ($id) {
      $video = Engine_Api::_()->getItem('video', $id);
      if ($video) {
        Engine_Api::_()->core()->setSubject($video);
      }
    }

    // Require subject
    if (!$this->_helper->requireSubject()->isValid()) {
      return;
    }

    // Require auth
    if (!$this->_helper->requireAuth()->setAuthParams($video, null, 'view')->isValid()) {
      return;
    }
  }

  public function embedAction() {
    // Get subject
    $this->view->video = $video = Engine_Api::_()->core()->getSubject('video');

    // Check if embedding is allowed
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1)) {
      $this->view->error = 1;
      return;
    } else if (isset($video->allow_embed) && !$video->allow_embed) {
      $this->view->error = 2;
      return;
    }

    // Get embed code
    $this->view->embedCode = $video->getEmbedCode();
  }

  public function externalAction() {
    // Get subject
    $this->view->video = $video = Engine_Api::_()->core()->getSubject('video');

    // Check if embedding is allowed
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1)) {
      $this->view->error = 1;
      return;
    } else if (isset($video->allow_embed) && !$video->allow_embed) {
      $this->view->error = 2;
      return;
    }

    // Get embed code
    $embedded = "";
    if ($video->status == 1) {
      $video->view_count++;
      $video->save();
      $embedded = $video->getRichContent(true);
    }

    // Track views from external sources
    Engine_Api::_()->getDbtable('statistics', 'core')
            ->increment('video.embedviews');

    // Get file location
    if ($video->type == 3 && $video->status == 1) {
      if (!empty($video->file_id)) {
        $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
        if ($storage_file) {
          $this->view->video_location = $storage_file->map();
        }
      }
    }
		$this->view->isMap = isset($_GET['type']) && $_GET['type'] == 'map_video' ? true : false;
    $this->view->rating_count = Engine_Api::_()->getDbtable('ratings', 'sesvideo')->ratingCount($video->getIdentity(), 'video');
    $this->view->video = $video;
    $this->view->videoEmbedded = $embedded;
    if ($video->category_id != 0) {
      $this->view->category = Engine_Api::_()->sesvideo()->getCategory($video->category_id);
    }
  }

}
