<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: WatchlaterController.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_WatchlaterController extends Core_Controller_Action_Standard {

  public function addAction() {
    $video_id = $this->_getParam('id', false);
    $error = true;
    $status = false;
    if ($video_id) {
      $params['video_id'] = $video_id;
      $insertVideo = Engine_Api::_()->sesvideo()->deleteWatchlaterVideo($params);
      echo json_encode($insertVideo);
      die;
    }
    echo json_encode(array('status' => $status, 'error' => $error));
    die;
  }

}
