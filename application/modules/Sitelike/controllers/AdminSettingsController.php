<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_AdminSettingsController extends Core_Controller_Action_Admin {

  public function indexAction() {

    if ( !$this->_helper->requireUser()->isValid() )
      return ;

		//GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi( 'menus' , 'core' )
            ->getNavigation( 'sitelike_admin_main' , array ( ) , 'sitelike_admin_main_settings' ) ;

    $sitelikeSettingTable = Engine_Api::_()->getDbtable( 'settings' , 'sitelike' ) ;
    $this->view->form = $form = new Sitelike_Form_Admin_Global() ;
    $contenttype = $this->_getParam( 'id' ) ;

		if ($contenttype == 'member') { $contenttype = 'user'; }
    if ( empty( $contenttype ) ) {
      $this->view->content_type = '' ;
    }
    else {
      $this->view->content_type = $contenttype ;
      $select = $sitelikeSettingTable->select()->where( 'content_type = ?' , $contenttype ) ;
      $result = $sitelikeSettingTable->fetchRow( $select ) ;
      if ( !empty( $result ) ) {
        $like_array = $result->toarray() ; 
        $this->view->tab1_show = $like_array['tab1_show'] ;
        $this->view->tab2_show = $like_array['tab2_show'] ;
        $this->view->tab3_show = $like_array['tab3_show'] ;
      }
      else {
        $this->view->tab1_show = 1 ;
        $this->view->tab2_show = 1 ;
        $this->view->tab3_show = 1 ;
      }
    }
    if ( !empty( $contenttype ) ) {
      $select = $sitelikeSettingTable->select()->where( 'content_type = ?' , $contenttype ) ;
      $result = $sitelikeSettingTable->fetchRow( $select ) ;
      if ( $result !== null ) {
        $like_array = $result->toarray() ;
      }
      if ( !empty( $like_array ) ) {
        $form->populate( $like_array ) ;
        $form->action_id->setValue( $like_array['setting_id'] ) ;
      }
      else {
        $form->content_type->setValue( $contenttype ) ;
        $form->action_id->setValue( 0 ) ;
      }
    }
    if ( !$this->getRequest()->isPost() ) {
      return ;
    }
    if ( !$form->isValid( $this->getRequest()->getPost() ) ) {
      return ;
    }

    if ( !empty( $contenttype ) ) {
      $sitelike_admin_tabb = 'tabbed_widgets' ;
      $values = $form->getValues() ;

			if($values['content_type'] == 'member') {
				$values['content_type'] = 'user';
			}

      if ( !empty( $like_array['setting_id'] ) ) { 
	$like = Engine_Api::_()->getItem('sitelike_setting',$like_array['setting_id']);
	$like->setFromArray($values);
	$like->save();
      }
      if ( !empty( $contenttype ) ) {
        $this->view->tab1_show = $values['tab1_show'] ;
        $this->view->tab2_show = $values['tab2_show'] ;
        $this->view->tab3_show = $values['tab3_show'] ;
      }
    }
  }
  
	public function popupcssfileAction() {
	
	}
	
  public function defaultcsspopupAction() {
	
	}
	
  public function likesettingsAction() {

    if ( !$this->_helper->requireUser()->isValid() )
      return ;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi( 'menus' , 'core' )
            ->getNavigation( 'sitelike_admin_main' , array ( ) , 'sitelike_admin_like_settings' ) ;

    $this->view->form = $form = new Sitelike_Form_Admin_Likesettings() ;
    if ( $this->getRequest()->isPost() && $form->isValid( $this->getRequest()->getPost() ) ) {
      $values = $this->getRequest()->getPost() ;

      $check_action = array_key_exists( "default_settings" , $values ) ;
      if ( !empty( $check_action ) ) {
        foreach ( $values as $key => $value ) {
          if ( $key != 'MAX_FILE_SIZE' && $key != 'default_settings' ) {
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting($key)) { 
							Engine_Api::_()->getApi( 'settings' , 'core' )->removeSetting($key);
						}
          }
        }
				if (Engine_Api::_()->getApi('settings', 'core')->getSetting('like.thumbsup.image')) { 
					Engine_Api::_()->getApi( 'settings' , 'core' )->removeSetting( 'like.thumbsup.image' ) ;
				}
				if (Engine_Api::_()->getApi('settings', 'core')->getSetting('like.thumbsdown.image')) { 
					Engine_Api::_()->getApi( 'settings' , 'core' )->removeSetting( 'like.thumbsdown.image' ) ;
				}
      }
      else {
        $sitelike_admin_tabb = 'like_button_view' ;
	foreach ($values as $key => $value)
	{
	  if( !empty($value) ) {
	    if ($key != 'MAX_FILE_SIZE') {
	      Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
	    }
	  }
	}
        if ( empty( $_FILES['like_thumbsup_image']['error'] ) ) {
          $this->uploads( $_FILES , 'like_thumbsup_image' ) ;
        }
        if ( empty( $_FILES['like_thumbsdown_image']['error'] ) ) {
          $this->uploads( $_FILES , 'like_thumbsdown_image' ) ;
        }
      }
      
			$this->upgradeStyleCssFile();
			$this->view->error_message = 1;
			
			$check_action = array_key_exists( "default_settings", $values );
			if($check_action) {
				$this->view->message = 1;
			} else {
				$this->view->message = 0;
			}
		//	$this->_helper->redirector->gotoRoute( array ( 'action' => 'likesettings' ) ) ;
			
    }
  }

  public function uploads( $FILES , $_file ) {

    if ( empty( $FILES[$_file] ) ) {
      $this->view->error = Zend_Registry::get( 'Zend_Translate' )->_( 'File failed to upload. Check your server settings (such as php.ini max_upload_filesize).' ) ;
      //return;
    }
    $file_path = APPLICATION_PATH . '/public/sitelike' ;
    if ( !is_dir( $file_path ) && !mkdir( $file_path , 0777 , true ) ) {
      //$filename = APPLICATION_PATH . "/application/languages/$localeCode/custom.csv";
      mkdir( dirname( $file_path ) ) ;
      chmod( dirname( $file_path ) , 0777 ) ;
      touch( $file_path ) ;
      chmod( $file_path , 0777 ) ;
    }

    // Prevent evil files from being uploaded
    $disallowedExtensions = array ( 'php' ) ;
    if ( in_array( end( explode( "." , $FILES[$_file]['name'] ) ) , $disallowedExtensions ) ) {
      $this->view->error = Zend_Registry::get( 'Zend_Translate' )->_( 'File type or extension forbidden.' ) ;
      return ;
    }

    $info = $FILES[$_file] ;
    $targetFile = $file_path . '/' . $info['name'] ;
    $vals = array ( ) ;
    move_uploaded_file( $info['tmp_name'] , $targetFile ) ;

    // Get Viewer Id
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    $photo_params = array (
      'parent_id' => $viewer_id ,
      'parent_type' => "sitelike" ,
    ) ;
    try {
      $photoFile = Engine_Api::_()->storage()->create( $targetFile , $photo_params ) ;
    }
    catch ( Exception $e ) {
      if ( $e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE ) {
        echo $e->getMessage() ;
        exit() ;
      }
    }
    // Save the photo id of uploaded image from Storage files table in the core settings table
    Engine_Api::_()->getApi( 'settings' , 'core' )->setSetting( $_file , $photoFile->file_id ) ;

//     if( file_exists($targetFile) ) {
//       $deleteUrl = $this->view->url(array('action' => 'delete')) . '?path=' . $relPath . '/' . $info['name'];
//       $deleteUrlLink = '<a href="'.$this->view->escape($deleteUrl) . '">' . Zend_Registry::get('Zend_Translate')->_("delete") . '</a>';
//       $this->view->error = Zend_Registry::get('Zend_Translate')->_("File already exists. Please %s before trying to upload.", $deleteUrlLink);
//       return;
//     }
//
//     if( !is_writable($file_path) ) {
//       $this->view->error = Zend_Registry::get('Zend_Translate')->_('Path is not writeable. Please CHMOD 0777 the public/admin directory.');
//       return;
//     }
//
//     // Try to move uploaded file
//     if( !move_uploaded_file($info['tmp_name'], $targetFile) ) {
//       $this->view->error = Zend_Registry::get('Zend_Translate')->_("Unable to move file to upload directory.");
//       return;
//     }

    $this->view->status = 1 ;

//     if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
//       return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
//     }
  }

	private function upgradeStyleCssFile() {

	  $path = APPLICATION_PATH . '/application/modules/Sitelike/externals/styles/likesettings.css';
    @chmod($path, 0777);
		if (!@is_writeable($path)) { 
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelike.button.likeupdatefile', 0);
      return;
    }
		$view = Zend_Registry::isRegistered( 'Zend_View' ) ? Zend_Registry::get( 'Zend_View' ) : null ;
		// Get Image
		$image_id = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.thumbsup.image' , 0 ) ;
		if(!empty($image_id)) {
			$cdn_path = Engine_Api::_()->sitelike()->getCdnPath();
			$img_path = Engine_Api::_()->storage()->get($image_id, '')->getHref();
			if($cdn_path == "") {
				$image_likethumbsup = 'http://' . $_SERVER['HTTP_HOST'] . $img_path;
			}
			else {
				$img_cdn_path = str_replace($cdn_path, '',  $img_path);
				$image_likethumbsup = $cdn_path. $img_cdn_path;
			}
		}
		else {
			// By Default image
			$image_likethumbsup = $view->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/images/thumb-up.png' ;
		}

		$image_id = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.thumbsdown.image' , 0 ) ;
		if(!empty($image_id)) {
			$cdn_path = Engine_Api::_()->sitelike()->getCdnPath();
			$img_path = Engine_Api::_()->storage()->get($image_id, '')->getHref();
			if($cdn_path == "") {
				$image_likethumbsdown = 'http://' . $_SERVER['HTTP_HOST'] . $img_path;
			}
			else {
				$img_cdn_path = str_replace($cdn_path, '',  $img_path);
				$image_likethumbsdown = $cdn_path. $img_cdn_path;
			}
		}
		else {
			// By Default image
			$image_likethumbsdown = $view->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/images/thumb-down.png' ;
		}
		$background_color = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.background.color' , '#f1f2f1' ) ;
		$background_haour_color = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.background.haourcolor' , '#f1f2f1' ) ;
		$text_haour_color = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.haour.color' , '#666666' ) ;
		$text_color = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.text.color' , '#666666' ) ;

    // Read the file in as an array of lines
		$bgc=$img=$clr=0;
    $fileData = file($path);
    $i = 0;
    $newArray = null;

    foreach ($fileData as $key => $line) {

      // find the line that starts with width: and change it to custome width
      if (preg_match('/color:/', $line)) {
				if (empty($clr))
				$color = $text_color;
				else
				$color = $text_haour_color;
        $explode = explode(":", $line);
        $explode[1] = $color . ';' . "\n";
        $line = implode(":", $explode);
        $clr++;
      }
			 if (preg_match('/background-color:/', $line)) {
				$clr = 0;
				if (empty($bgc))
				$bgrcolor = $background_color;
				else
				$bgrcolor = $background_haour_color;
        $explode = explode(":", $line);
        $explode[1] = $bgrcolor . ';' . "\n";
        $line = implode(":", $explode);
        $bgc++;
      }
			if (preg_match('/background-image:/', $line)) {

				if (empty($img))
				$image = $image_likethumbsup;
				else
				$image = $image_likethumbsdown;
        $explode = explode("image:", $line);
			
        $explode[1] = 'url('.$image . ');' . "\n";
				
        $line = implode("image:", $explode);
        $img++;
      }
      $newArray .= $line;
    }

    // Overwrite test.txt
    $fp = fopen($path, 'w');
    fwrite($fp, $newArray);
    @chmod($path, 0755);
    fclose($fp);
		Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelike.button.likeupdatefile', 1);
		
  }
}
?>