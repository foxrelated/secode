<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_Widget_FeaturelistSitestorevideosController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET SUBJECT
    $subject = Engine_Api::_()->core()->getSubject('sitestore_store');

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorevideo")) {
        return $this->setNoRender();
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'svcreate');
      if (empty($isStoreOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

    $sitestorevideo_getFeatured = Zend_Registry::isRegistered('sitestorevideo_getFeatured') ? Zend_Registry::get('sitestorevideo_getFeatured') : null;
    if (empty($sitestorevideo_getFeatured)) {
      return $this->setNoRender();
    }

    //FETCH VIDEOS
    $params = array();
    $params['store_id'] = $subject->store_id;
    $params['zero_count'] = 'featured';
    $params['profile_store_widget'] = 1;
    $params['limit'] = $this->_getParam('itemCount', 3);
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('videos', 'sitestorevideo')->widgetVideosData($params);

    if ((Count($paginator) <= 0)) {
      return $this->setNoRender();
    }
  }

}
?>