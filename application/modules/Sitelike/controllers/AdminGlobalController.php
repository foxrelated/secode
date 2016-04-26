<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminGlobalController.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_AdminGlobalController extends Core_Controller_Action_Admin {
  const IMAGE_WIDTH = 200 ;
  const IMAGE_HEIGHT = 200 ;

  public function indexAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation( 'sitelike_admin_main', array(), 'sitelike_admin_global_settings' ); 

    $this->view->form  = $form = new Sitelike_Form_Admin_Settings();

    if( $this->getRequest()->isPost() ) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value)
      {
	      if ($key != 'MAX_FILE_SIZE') {
		      if($key == 'like_link_position')
		      {
			      $menu_table = Engine_Api::_()->getDbtable('menuitems', 'core');
			      if($value == 1)
			      {
				      $menu_table->update(array('menu' => 'core_footer', 'plugin' => ''),array('name =?' => 'core_main_sitelike'));
			      }
			      else if($value == 3)
			      {
				      $menu_table->update(array('menu' => 'core_main', 'plugin' => ''),array('name =?' => 'core_main_sitelike'));
			      }
			      else if($value == 2)
			      {
				      $menu_table->update(array('menu' => 'core_mini', 'plugin' => ''),array('name =?' => 'core_main_sitelike'));
			      }
			      else if(empty($value)) {
				      $menu_table->update(array('menu' => 'user_home', 'plugin' => 'Sitelike_Plugin_Menus'),array('name =?' => 'core_main_sitelike'));
			      }
		      }
		      if( $key != 'submit' ) {
			      Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
		      }
	      }
      }

      if (!empty($_FILES['like_thumbsup_image'])) {
	      $this->uploadimage ($_FILES, 'like_thumbsup_image');
      }
      if (!empty($_FILES['like_thumbsdown_image'])) {
	      $this->uploadimage ($_FILES, 'like_thumbsdown_image');
      }
    }
  }

  public function uploadimage( $FILES , $_file ) {

    if ( isset( $FILES[$_file] ) && is_uploaded_file( $FILES[$_file]['tmp_name'] ) ) {
      $file = $FILES[$_file] ;
      $name = basename( $file['tmp_name'] ) ;
      $path = dirname( $file['tmp_name'] ) ;
      $mainName = $path . '/' . $file['name'] ;

      //GET VIEWER ID.
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
      $photo_params = array (
        'parent_id' => $viewer_id ,
        'parent_type' => "sitelike" ,
      ) ;

      //RESIZE IMAGE.
      $image = Engine_Image::factory() ;
      $image->open( $file['tmp_name'] )
          ->resize( self::IMAGE_WIDTH , self::IMAGE_HEIGHT )
          ->write( $mainName )
          ->destroy() ;

      try {
        $photoFile = Engine_Api::_()->storage()->create( $mainName , $photo_params ) ;
      }
      catch ( Exception $e ) {
        if ( $e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE ) {
          echo $e->getMessage() ;
          exit() ;
        }
      }
      Engine_Api::_()->getApi( 'settings' , 'core' )->setSetting( $_file , $photoFile->file_id ) ;
    }
  }

	//SHOWING THE PLUGIN RELETED QUESTIONS AND ANSWERS
  public function faqAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi( 'menus' , 'core' )
            ->getNavigation( 'sitelike_admin_main' , array ( ) , 'sitelike_admin_faqs' ) ;
  }
}
?>