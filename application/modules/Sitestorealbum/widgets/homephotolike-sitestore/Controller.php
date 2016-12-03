<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorealbum_Widget_HomephotolikeSitestoreController extends Engine_Content_Widget_Abstract {

	//ACTION FOR MOST LIKED PHOTOS ON STORE PROFILE STORE 
  public function indexAction() {
  	
    //SEARCH PARAMETER
    $params = array();
    $params['category_id'] = $this->_getParam('category_id',0);
		$params['orderby'] = 'like_count DESC';
		$params['zero_count'] = 'like_count';
		$params['limit'] = $this->_getParam('itemCount', 4);
    $this->view->displayStoreName = $this->_getParam('showStoreName', 0);
    $this->view->displayUserName = $this->_getParam('showUserName', 0);
    $this->view->showFullPhoto = $this->_getParam('showFullPhoto', 0);
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitestore');
		$this->view->paginator = $paginator = $photoTable->widgetPhotos($params);   
    
    $this->view->count =  $photoTable->countTotalPhotos($params);
    
    if (Count($paginator) <= 0) {
      return $this->setNoRender();
    }
    
    //SHOWS PHOTOS IN THE LIGHTBOX
    $this->view->showLightBox = Engine_Api::_()->sitestore()->canShowPhotoLightBox();
    
  }

}

?>