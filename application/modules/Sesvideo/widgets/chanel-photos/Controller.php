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

class Sesvideo_Widget_ChanelPhotosController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if (!$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
    if (isset($_POST['params']))
      $params = json_decode($_POST['params'], true);
    // Don't render this if not authorized
    if (empty($_POST['is_ajax'])) {
      if (!Engine_Api::_()->authorization()->isAllowed('sesvideo_chanelphoto', null, 'view'))
        return $this->setNoRender();
      /* check sesalbum plugin enable or not ,if no then return */
      if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesalbum'))
        return $this->setNoRender();
      // Don't render this if not authorized
      $viewer = Engine_Api::_()->user()->getViewer();
      if (!Engine_Api::_()->core()->hasSubject())
        return $this->setNoRender();
      // Get subject and check auth
      $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
      $chanel_id = $subject->chanel_id;
      $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('sesvideo_chanelphoto', null, 'create');
    }else {
      $this->view->subject = $subject = Engine_Api::_()->getItem('sesvideo_chanel', $params['chanel_id']);
      $chanel_id = $subject->chanel_id;
    }
    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
    $this->view->load_content = $load_content = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
    $this->view->height = $defaultHeight = isset($params['height']) ? $params['height'] : $this->_getParam('height', '250');
    $this->view->width = $defaultWidth = isset($params['width']) ? $params['width'] : $this->_getParam('width', '200');
    $this->view->limit_data = $value['limit_data'] = $limit_data = isset($params['limit_data']) ? $params['limit_data'] : $this->_getParam('limit_data', '10');
    $this->view->limit = ($page - 1) * $limit_data;
    $this->view->title_truncation = $title_truncation = isset($params['title_truncation']) ? $params['title_truncation'] : $this->_getParam('title_truncation', '45');
    $this->view->view_type = $view_type = isset($params['view_type']) ? $params['view_type'] : $this->_getParam('view_type', 'masonry');
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'comment', 'by', 'title', 'socialSharing', 'view','likeButton','downloadCount'));
    $this->view->fixHover = $fixHover = isset($params['fixHover']) ? $params['fixHover'] : $this->_getParam('fixHover', 'fix');
    $this->view->insideOutside = $insideOutside = isset($params['insideOutside']) ? $params['insideOutside'] : $this->_getParam('insideOutside', 'inside');
    foreach ($show_criterias as $show_criteria)
      $this->view->$show_criteria = $show_criteria;
    $params = $this->view->params = array('height' => $defaultHeight, 'width' => $defaultWidth, 'limit_data' => $limit_data, 'pagging' => $load_content, 'show_criterias' => $show_criterias, 'view_type' => $view_type, 'title_truncation' => $title_truncation, 'insideOutside' => $insideOutside, 'fixHover' => $fixHover, 'chanel_id' => $subject->chanel_id);
    $paginator = Engine_Api::_()->getDbTable('chanelphotos', 'sesvideo')->chanelphotos($subject->chanel_id);
    $this->view->paginator = $paginator;
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($limit_data);
    $this->view->page = $page;
    $paginator->setCurrentPageNumber($page);
    if ($is_ajax)
      $this->getElement()->removeDecorator('Container');
    // Add count to title if configured
    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

}
