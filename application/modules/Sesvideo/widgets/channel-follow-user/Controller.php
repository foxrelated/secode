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

class Sesvideo_Widget_ChannelFollowUserController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if (!$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
    if (isset($_POST['params']))
      $params = json_decode($_POST['params'], true);

    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;

    //Get subject and check auth
    if (!$is_ajax) {
      $subject = Engine_Api::_()->core()->getSubject('sesvideo_chanel');
      $chanelItem = Engine_Api::_()->getItem('sesvideo_video', $subject->chanel_id);
      if ($subject->follow == 0 || !Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.subscription', 1))
        return $this->setNoRender();
    }

    if (!$is_ajax && !$subject)
      return $this->setNoRender();
    else if (empty($subject) && $is_ajax)
      $subject = Engine_Api::_()->getItem('sesvideo_chanel', $_POST['chanel_id']);

    $chanel_id = isset($_POST['chanel_id']) ? $_POST['chanel_id'] : $subject->chanel_id;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->loadOptionData = isset($params['loadOptionData']) ? $params['loadOptionData'] : $this->_getParam('loadOptionData', 'view_more');
    $showData = $this->_getParam('showData', 1);
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesteam') && empty($showData)) {
      $this->view->showData = $showData = isset($params['showData']) ? $params['showData'] : 0;
    } else {
      $this->view->showData = $showData = isset($params['showData']) ? $params['showData'] : '1';
    }
    $this->view->limit = $limit = isset($params['limit_data']) ? $params['limit_data'] : $this->_getParam('limit_data', '20');
    $this->view->height = isset($params['height']) ? $params['height'] : $this->_getParam('height', '200');
    $this->view->width = isset($params['width']) ? $params['width'] : $this->_getParam('width', '200');
    $this->view->center_block = isset($params['center_block']) ? $params['center_block'] : $this->_getParam('center_block', 1);
    $this->view->viewMoreText = isset($params['viewMoreText']) ? $params['viewMoreText'] : $this->_getParam('viewMoreText', 'more');
    $this->view->profileFieldCount = isset($params['profileFieldCount']) ? $params['profileFieldCount'] : $this->_getParam('profileFieldCount', 5);
    $this->view->labelBold = isset($params['labelBold']) ? $params['labelBold'] : $this->_getParam('labelBold', 1);
    $this->view->age = isset($params['age']) ? $params['age'] : $this->_getParam('age', 1);
    $this->view->content_show = isset($params['contentshow']) ? $params['contentshow'] : $this->_getParam('contentshow', array('displayname', 'description', 'email', 'phone', 'website', 'location', 'facebook', 'linkdin', 'twitter', 'googleplus'));
    $this->view->template_settings = isset($params['template']) ? $params['template'] : $this->_getParam('template', 1);
    $this->view->sesteam_social_border = isset($params['social_border']) ? $params['social_border'] : $this->_getParam('social_border', 1);

    $this->view->all_params = $values = array('loadOptionData' => $this->view->loadOptionData, 'showData' => $this->view->showData, 'limit_data' => $this->view->limit, 'height' => $this->view->height, 'width' => $this->view->width, 'center_block' => $this->view->center_block, 'viewMoreText' => $this->view->viewMoreText, 'profileFieldCount' => $this->view->profileFieldCount, 'labelBold' => $this->view->labelBold, 'age' => $this->view->age, 'contentshow' => $this->view->content_show, 'template' => $this->view->template_settings, 'social_border' => $this->view->sesteam_social_border);
    $this->view->page = $page;
    $this->view->is_ajax = $is_ajax;
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('Chanelfollows', 'sesvideo')->getChanelFollowers($chanel_id);
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
