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
class Sitelike_Widget_CommonFriendLikeController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

		$subject = Engine_Api::_()->core()->getSubject();
		$this->view->resource_id = $resource_id = $subject->getIdentity();
		$this->view->resource_type = $resource_type = $subject->getType();
		
		$moduleName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		$moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled($moduleName);
		$viewerId = Engine_Api::_()->user()->getViewer()->getIdentity() ;
		
    $enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitelike')->getColumnValue(array('resource_type' => $resource_type, 'module' => $moduleName, 'columnValue' => 'enabled'));

		if (empty($enabled) || empty( $moduleEnabled ) || empty( $viewerId ) || empty($moduleName) || empty($resource_type)) {
			return $this->setNoRender() ;
		}
		
	  $limit = $this->_getParam('itemCountPerPage', 3);
		$friend_likes_obj = Engine_Api::_()->sitelike()->userFriendLikes( $resource_type , $resource_id , $limit );
		if ( !empty( $friend_likes_obj ) ) {
			$this->view->friend_likes_obj = $friend_likes_obj ;
			$this->view->num_of_like = $num_of_like = Engine_Api::_()->sitelike()->friendNumberOfLike( $resource_type , $resource_id ) ;
			if ( !empty( $num_of_like ) && $num_of_like > $limit ) {
				$this->view->detail = 1 ;
			}
		}	
		else {
			return $this->setNoRender() ;
		}
	}
}