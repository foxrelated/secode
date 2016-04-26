<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideoview
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2012-06-028 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideoview_Widget_ProfileVideosController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();
    if ($request->isPost()) {
      $this->getElement()->removeDecorator('Title');
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    // Get paginator
    $profile_owner_id = $subject->getIdentity();
    $videoApiName = "video";
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('ynvideo')) {
      $videoApiName = 'ynvideo';
    }

		$sitevideoview_profile_video = Zend_Registry::isRegistered('sitevideoview_profile_video') ? Zend_Registry::get('sitevideoview_profile_video') : null;
    $this->view->paginator = $paginator = Engine_Api::_()->$videoApiName()->getVideosPaginator(array(
        'user_id' => $profile_owner_id,
        'status' => 1,
        'search' => 1
            ));

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if (($paginator->getTotalItemCount() <= 0) || empty($sitevideoview_profile_video)) {
      return $this->setNoRender();
    } else {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}