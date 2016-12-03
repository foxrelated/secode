<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Mcard_AdminSettingsController extends Core_Controller_Action_Admin 
{
  public function indexAction() 
  {
		if( !empty($_POST['mcard_controllersettings']) ) { $_POST['mcard_controllersettings'] = trim($_POST['mcard_controllersettings']); }
		$mcard_form_content =  array('mcard_visibility', 'mcard_print', 'submit');
 		include_once APPLICATION_PATH . '/application/modules/Mcard/controllers/license/license1.php';
  }

  public function previewAction()
  {
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		// GET LEVEL ID.
		$level_id = Engine_Api::_()->getItem('user', $user_id)->level_id;			
		$mp_id = Engine_Api::_()->mcard()->getProfileTypeId($user_id);

    $this->_helper->layout->setLayout('admin-simple'); // Dont show "Header" & "Footer" in the page. 
    $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl(); // Find out the "Base Url".
		if(strstr($baseUrl,"index.php")) {
			$baseUrl = '';
		}
    // Make a array which consist of all values, which are set by site admin for the preview.
    $previewDataArray  = array();
    $previewDataArray['level_id'] = $_GET['level_id'];
    $previewDataArray['profile_type'] = $_GET['profile_type'];
    $previewDataArray['show_empty_field'] = $_GET['show_empty_field'];
    $previewDataArray['logo_select'] = $_GET['logo_select'];
    $previewDataArray['show_title'] = $_GET['show_title'];
    $previewDataArray['title_color'] = '#' . $_GET['title_color'];
    $previewDataArray['title_font'] = $_GET['title_font'];
    $previewDataArray['profile_photo_display'] = $_GET['profile_photo_display'];
    $previewDataArray['information_color'] = '#' . $_GET['information_color'];
    $previewDataArray['information_font'] = $_GET['information_font'];
    $previewDataArray['card_background'] = $_GET['card_background'];
    $previewDataArray['card_background'] = $_GET['card_background'];
		$pre_level_id = $_GET['pre_level_id'];
		$pre_mp_id = $_GET['pre_mp_id'];
    // Check the "Logo Image" set or not by site admin.
    if( !empty($_GET['upload_logo_image']) ) {
    	$upload_image = explode("\\", $_GET['upload_logo_image']);
			$_GET['upload_logo_image'] = $upload_image[count($upload_image) - 1];
      $previewDataArray['upload_logo_image'] = $baseUrl . '/public/temporary/' . $_GET['upload_logo_image'];
    }
    else {
			$crad_status = Engine_Api::_()->getItemTable('mcard_info')->getVal($pre_level_id, $pre_mp_id);
			if ( !empty($crad_status['logo']) ) {
				$previewDataArray['database_logo_image'] = $crad_status['logo'];
			}
			else {
				$previewDataArray['database_logo_image'] = 0;
			}
      $previewDataArray['upload_logo_image'] = 0 ;
    }
    $previewDataArray['show_title_value'] = $_GET['show_title_value'];
    $previewDataArray['card_bg_color'] = '#' . $_GET['card_bg_color'];
    // Check the "Card Background Image" set or not by site admin.
    if( !empty($_GET['card_background_image']) ) {
    	$upload_bgimage = explode("\\", $_GET['card_background_image']);
			$_GET['card_background_image'] = $upload_bgimage[count($upload_bgimage) - 1];
      $previewDataArray['card_background_image'] = $baseUrl . '/public/temporary/' . $_GET['card_background_image'];
    }
    else {
			$crad_status = Engine_Api::_()->getItemTable('mcard_info')->getVal($pre_level_id, $pre_mp_id);
			if ( !empty($crad_status['card_bg_image']) ) {
				$previewDataArray['database_background_image'] = $crad_status['card_bg_image'];
			}
			else {
				$previewDataArray['database_background_image'] = 0;
			}
      $previewDataArray['card_background_image'] = 0 ;
    }
    $previewDataArray['profile_type'] = explode(",", $_GET['profile_type']);
    $this->view->userCard = Engine_Api::_()->mcard()->showPreview($previewDataArray);
  }

	//SHOWING THE PLUGIN RELETED QUESTIONS AND ANSWERS
  public function faqAction()
  {
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
     ->getNavigation('mcard_admin_main', array(), 'mcard_admin_faqs'); 
  }

  public function readmeAction() { }
}