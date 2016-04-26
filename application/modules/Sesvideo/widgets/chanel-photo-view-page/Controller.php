<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Widget_chanelPhotoViewPageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if (!$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
    if (isset($_POST['params'])) {
      $params = json_decode($_POST['params'], true);
    }
    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    if (Engine_Api::_()->core()->hasSubject('sesvideo_chanelphoto') && !$is_ajax)
      $photo = Engine_Api::_()->core()->getSubject('sesvideo_chanelphoto');
    else if (isset($_POST['photo_id'])) {
      $photo = Engine_Api::_()->getItem('sesvideo_chanelphoto', $_POST['photo_id']);
      Engine_Api::_()->core()->setSubject($photo);
      $photo = Engine_Api::_()->core()->getSubject();
    } else
      return $this->setNoRender();
    $likeStatus = isset($_POST['criteria']) ? $_POST['criteria'] : $this->_getParam('criteria', array('like', 'slideshowPhoto'));
    $this->view->maxHeight = isset($_POST['maxHeight']) ? $_POST['maxHeight'] : $this->_getParam('maxHeight', 900);
    $view_more_like = $this->_getParam('view_more_like', '10');
    if ($view_more_like == 0)
      $view_more_like = 10;
    $view_more_like = isset($_POST['view_more_like']) ? $_POST['view_more_like'] : $view_more_like;
    foreach ($likeStatus as $value)
      $this->view->{"status_" . $value} = ${"status_" . $value} = true;
    $params = $this->view->params = array('likeStatus' => $likeStatus, 'view_more_like' => $view_more_like);
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo;
    $this->view->chanel = $chanel = $photo->getChanel();
    if ($viewer->getIdentity() > 0) {
      $this->view->canEdit = $canEdit = $photo->authorization()->isAllowed($viewer, 'edit');
      $this->view->canComment = $canComment = $photo->authorization()->isAllowed($viewer, 'comment');
      $this->view->canDelete = $canDelete = $photo->authorization()->isAllowed($viewer, 'delete');
    }
    $this->view->nextPhoto = $photo->getNextPhoto();
    $this->view->previousPhoto = $photo->getPreviousPhoto();
    $this->view->photo_id = $photo->chanelphoto_id;
    if (isset($status_like) && $status_like) {
      // Get like paginator
      $this->view->photo_id = $paramData['id'] = $photo->getIdentity();
      $paramData['type'] = 'sesvideo_chanelphoto';
      $this->view->paginator_like = $paginator_like = Engine_Api::_()->sesalbum()->likeItemCore($paramData);
      $this->view->data_show_like = $view_more_like;
      // Set item count per page and current page number
      $paginator_like->setItemCountPerPage($view_more_like);
      $paginator_like->setCurrentPageNumber(1);
    }
    if ($is_ajax) {
      $this->getElement()->removeDecorator('Container');
    } else {
      $this->view->doctype('XHTML1_RDFA');
    }
  }

}
