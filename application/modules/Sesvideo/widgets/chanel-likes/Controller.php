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
class Sesvideo_Widget_ChanelLikesController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if (!$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    // Get subject and check auth
    if (!$is_ajax)
      $subject = Engine_Api::_()->core()->getSubject('sesvideo_chanel');
    if (!$is_ajax && !$subject)
      return $this->setNoRender();
    else if (empty($subject) && $is_ajax)
      $subject = Engine_Api::_()->getItem('sesvideo_chanel', $_POST['chanel_id']);
    $chanel_id = isset($_POST['chanel_id']) ? $_POST['chanel_id'] : $subject->chanel_id;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->limit = $limit = isset($_POST['limit_data']) ? $_POST['limit_data'] : $this->_getParam('limit_data', '20');
    $this->view->loadOptionData = isset($_POST['loadOptionData']) ? $_POST['loadOptionData'] : $this->_getParam('loadOptionData', 'view_more');
    $this->view->page = $page;
    $this->view->is_ajax = $is_ajax;
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('chanels', 'sesvideo')->chanelLikes(array('id' => $chanel_id));
    $this->view->users = $paginator;
    $paginator->setItemCountPerPage($limit);
    $this->view->chanel_id = $subject->chanel_id;
    $paginator->setCurrentPageNumber($page);
    $this->view->totalUsers = $paginator->getTotalItemCount();
    $this->view->userCount = $paginator->getCurrentItemCount();
    if ($is_ajax)
      $this->getElement()->removeDecorator('Container');
  }

}
