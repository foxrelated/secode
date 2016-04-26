<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_Widget_CommonLikeController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

		$moduleName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled($moduleName);
    $likeBrowseShow = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting('like.browse.auth');

    $subject = Engine_Api::_()->core()->getSubject();
    $this->view->resource_id = $resource_id = $subject->getIdentity() ;
    $this->view->resource_type = $resource_type = $subject->getType();
    $this->view->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    $enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitelike')->getColumnValue(array('resource_type' => $resource_type, 'module' => $moduleName, 'columnValue' => 'enabled'));
										
    if (empty($enabled) || empty($moduleEnabled) || empty($moduleName) || empty($likeBrowseShow) || empty($resource_type)) {
      return $this->setNoRender() ;
    }

    $LIMIT = $this->_getParam('itemCountPerPage', 3);

    $fetch_sub = Engine_Api::_()->sitelike()->likePeopleWidget($resource_type , $resource_id, $LIMIT) ;
    
    if ( !empty( $fetch_sub ) ) {
      foreach ( $fetch_sub as $fetch_id ) {
        $like_user_object[] = Engine_Api::_()->getItem( 'user' , $fetch_id['poster_id'] ) ;
      }
      $this->view->user_obj = $like_user_object ;
      // FIND OUT THE NUMBER OF LIKE OF THIS CONTENT.
      $this->view->num_of_like = $num_of_like = Engine_Api::_()->getApi('like', 'seaocore')->likeCount( $resource_type , $resource_id);
      if ( !empty( $num_of_like ) && $num_of_like > $LIMIT ) {
        $this->view->detail = 1 ;
      }
    }	
    else {
			return $this->setNoRender() ;
    }
  }
}