<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminMixController.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_AdminMixController extends Core_Controller_Action_Admin {

  public function indexAction() {

    if ( !$this->_helper->requireUser()->isValid() )
      return ;

		//GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi( 'menus' , 'core' )
            ->getNavigation( 'sitelike_admin_main' , array ( ) , 'sitelike_admin_mix_settings' ) ;

    $this->view->form = $form = new Sitelike_Form_Admin_Mix() ;

    if ( $this->getRequest()->isPost() && $form->isValid( $this->getRequest()->getPost() ) ) {
      $sitelike_admin_tabb = 'mixed_content_widgets' ;
      $like_serial_array = array ( ) ;
      $sitelike_serial_array = array ( ) ;
      $values = $form->getValues() ;
      $serial_array = array ( ) ;
      foreach ( $values as $key => $value ) {
        if ( $key == 'like_mix_wid' ) {
          Engine_Api::_()->getApi( 'settings' , 'core' )->setSetting( $key , $value );
        }
				else {
					Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' )->setSetting( $key , $value );
				}
      }
    }
  }
}
?>