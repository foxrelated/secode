<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Mcard_Api_Core extends Core_Api_Abstract {

  /**
   * This function save the image and store in "storeage table" and then return the photo id.
   *
   * @param $photo: The from photo field object.
   * @param $parent_id: The level id.
   * @param $parent_type: The name of folder which will contains the images.
   * @return photo id
   */
  function savePhoto($photo, $parent_id, $parent_type = 'mcard') 
  {
	$file = $photo->getFileName();
	$name = basename($file);
	$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
	$params = array(
	    'parent_type' => $parent_type,
	    'parent_id' => $parent_id
	);
    
	// Saves the image into the storage table
	$storage = Engine_Api::_()->storage();
	$image = Engine_Image::factory();
	$image->open($file)
		->resize(326, 210)
		->write($path . '/p_' . $name)
		->destroy();
	$image = Engine_Image::factory();
	$image->open($file)
		->resize(110, 27)
		->write($path . '/is_' . $name)
		->destroy();
	// Stores the corresponding entry into the table
	$iProfile = $storage->create($path . '/p_' . $name, $params);
	$iSmall = $storage->create($path . '/is_' . $name, $params);
	$iProfile->bridge($iProfile, 'thumb.profile');
	$iProfile->bridge($iSmall, 'thumb.icon');
	//file_id is the id assigned to the file that is saved in the memory
	return $iProfile->file_id;
  }

  /**
   * This function return the complete path of image, from the photo id.
   *
   * @param $id: The photo id.
   * @param $type: The type of photo required.
   * @return Image path.
   */

  public function displayPhoto( $id, $type = 'thumb.profile' ) 
  {
    if (empty($id)) {
      return null;
    }
    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($id, $type);
    if (!$file) {
      return null;
    }

    // Get url of the image
    $src = $file->map();
    return $src;
  }
  
  /**
   * This function return the Profile Type Ids
   *
   * @param $user_id: user id.
   * @return ID.
   */
  public function getProfileTypeId($user_id) {

		$user = Engine_Api::_()->getItem('user', $user_id);
		$aliasedFields = $user->fields()->getFieldsObjectsByAlias();
    if( isset($aliasedFields['profile_type']) ) {
      $aliasedFieldValue = $aliasedFields['profile_type']->getValue($user);     
      $topLevelValue = ( is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null );
    }
		if( !empty($topLevelValue) ) {
			return $topLevelValue;
		} else {
			return 0;
		}
  }

  /**
   * This function return the Member Ship Card, only for "When click on print Member ship card". Here define the 'Inline css' becouse this function use for printing card.
   *
   * @param $level_id: The Level id of user.In which level he is.
   * @param $user_id: The loggedin user id.
   * @param $mp_id: Profile type.
   * @return Complete card for "Printing".
   */
  public function showCard($level_id, $user_id, $mp_id = null) {
    $sample = Engine_Api::_()->getItem('user', $user_id);
		global $mcard_profile_status;
    //Get Values from database
    $infoTable = Engine_Api::_()->getItemTable('mcard_info');
		$check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.check.variables');
		$base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.base.time');
		$get_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.get.path' );
    $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$mcard_profile_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.profile.type', 0);
    $dbvalues = $infoTable->getVal($level_id, $mp_id);
		$mcard_time_var = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.time.var' );
		$controllersettings_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.controllersettings');
		$currentbase_time = time();
		$controller_result_lenght = strlen($controllersettings_result_show);
    if (!Engine_Api::_()->core()->hasSubject('user'))
      Engine_Api::_()->core()->setSubject(Engine_Api::_()->user()->getUser($user_id));
    $subject = Engine_Api::_()->core()->getSubject(Engine_Api::_()->user()->getUser($user_id));
    $finaldbdata = $dbvalues;
    $retHtmlString = '';

    // Get level object
    if (null !== $level_id) {
      $level = Engine_Api::_()->getItem('authorization_level', $level_id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

		if( empty($mcard_profile_status) ) {
			return;
		}
    // Member type
    $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);
    $memberType = "No member type";
    if (!empty($fieldsByAlias['profile_type'])) {
      $optionId = $fieldsByAlias['profile_type']->getValue($subject);
      if ($optionId) {
        $optionObj = Engine_Api::_()->fields()
                        ->getFieldsOptions($subject)
                        ->getRowMatching('option_id', $optionId->value);
        if ($optionObj) {
          $memberType = $optionObj->label;
        }
      }
    }

		if( ($currentbase_time - $base_result_time > $mcard_time_var) && empty($check_result_show) ) {
			if( $controller_result_lenght != 20 ) {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('mcard.per.print', 1);
				ngine_Api::_()->getApi('settings', 'core')->setSetting('mcard.flag.value', 1);
				return;
			} else {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('mcard.check.variables', 1);
			}
		}

    foreach ($dbvalues as $key => $values) {
      switch ($key) {
        case "logo":
				case "card_status";
        case "card_bg_image":
        case "card_bg_color":
        case "label_color":
        case "label_font":
        case "info_color":
        case "info_font":
        case "card_label":
        case "logo_select":
          $$key = $values;
          break;
        case "Profile Photo":
          $profile_photo = 1;
          break;

        default:
          break;
      }
    }

    // Load fields view helpers present in the fields package
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    // $fieldStructure contains all the fields corresponding to the respected category of the user
    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);
    if (count($fieldStructure) <= 1) {
      return;
    }
    //Refined field structure selected as per the admin
    $newFieldStructure = array();
    foreach ($dbvalues as $dbkey => $dbval) {
      foreach ($fieldStructure as $key => $fieldValue) {
        if ($dbval == $fieldValue->child_id) {
          $newFieldStructure[$key] = $fieldValue;
          continue;
        }
      }
    }
		if( empty($mcard_profile_type) ) {
			return;
		}

    
    /*
     * return string is processed here
     * $card_bg_image:   background image if selected by the user
     * $card_bg_color:   background color if selected by the user
     * $label_color     
     */

    // Check card back groud image set by admin or not.
    if (isset($card_bg_image)) 
    {
      $card_bg_image = Engine_Api::_()->mcard()->displayPhoto($card_bg_image);
      $retHtmlString.= "<div style='background:url($card_bg_image);width:326px;display:block;float:left;-moz-border-radius:6px;border-radius:6px;'><div style='display:block;float:left;padding:3px;width:320px;min-height:200px;'>";
    }
    else 
    {
      // If admin not set "Bg image" then check "card bg color" set or not.
      if (isset($card_bg_color)) 
      {
        $card_bg_image = $baseUrl . '/application/modules/Mcard/externals/images/default.png';

        $retHtmlString.= "<div style='background:$card_bg_color url($card_bg_image) no-repeat top right;width:326px;display:block;float:left;-moz-border-radius:6px;border-radius:6px;'><div style='display:block;float:left;background:url(application/modules/Mcard/externals/images/default_inner.png) no-repeat bottom left;padding:3px;width:320px;min-height:200px;'>";
      }
      else 
      {
				$card_bg_image = $view->layout()->staticBaseUrl . 'application/modules/Mcard/externals/images/default-card-bg.jpg';
				$retHtmlString.= "<div style='background:url($card_bg_image);width:326px;display:block;float:left;-moz-border-radius:6px;border-radius:6px;'><div style='display:block;float:left;padding:3px;width:320px;min-height:200px;'>";
      }
    }

    //Start the logo div here
    $retHtmlString.= "<div style='float:left;height:50px;margin-left:5px;'>";
    if (isset($logo)) {
      $logo = Engine_Api::_()->mcard()->displayPhoto($logo);
      $retHtmlString.= '<img src="' . $logo . '" style=" max-width:150px; max-height:50px;" />';
    } elseif ($logo_select) {
      $retHtmlString.=  '<img src="' . $view->layout()->staticBaseUrl . 'application/modules/Mcard/externals/images/default-card--logo.jpg' . '" style=" max-width:150px;
  max-height:50px;" />';
    }
    $retHtmlString.= "</div> ";     
    //End of logo div

    //Start the head div here
    $retHtmlString.= "<div style='color:$label_color; font-family:$label_font; float:right; font-weight:bold; font-size:14px; line-height:30px; margin-right:5px;'>";
    if (isset($card_label)) {
      $retHtmlString.= $card_label;
    }
    $retHtmlString.= "</div>";  
    //End of head div i.e. for card_label

    //Start the member-details div here
    $retHtmlString.= "<div style='float:left;width:100%;margin-top:10px;'>";
    if (isset($profile_photo)) {
      if(empty($sample->photo_id))
      {
	$retHtmlString.= "<div style='float:left;margin:0 5px;'>";      
	$retHtmlString.=  $view->itemPhoto($subject, 'thumb.profile', '', array('style'=>'float:left;width:100px;')); // "<img src='" . $baseUrl . "/application/modules/User/externals/images/nophoto_user_thumb_profile.png' style='  float:left; width:100px;'/>";
	$retHtmlString.= "</div>";  //End of member-photo div
      }
      else {
	$retHtmlString.= "<div style='float:left;margin:0 5px;'>";      
	$retHtmlString.= $view->itemPhoto($sample->getOwner(), 'thumb.profile','', array("style"=> "float:left;
width:100px; max-height:120px;"));
	$retHtmlString.= "</div>";  //End of member-photo div
      }
    }

    //Start the info div here
   $retHtmlString.= "<div style='color:$info_color; font-family:$info_font; float:left; font-size:12px; line-height:14px;width:205px;overflow:hidden;'>";
    $dynamic_array = $view->fieldValueLoop($view->subject(), $newFieldStructure);
    $dynamic_array = strip_tags($dynamic_array, "<span><li>");
    $liexploded = explode("</li>", $dynamic_array);
    foreach ($liexploded as $key => $lival) {
      $spanexploded[] = explode("</span>", $lival);
    }
    $processed_dynamic_array = array();
    foreach ($spanexploded as $key => $spanval) {
      $tkey = "";
      if (!empty($spanval[0])) {
        $tkey = strip_tags($spanval[0]);
      }
      if (!empty($tkey)) {
        $processed_dynamic_array[$tkey] = !empty($spanval[1]) ? strip_tags($spanval[1]) : "";
      }
    }
    foreach ($finaldbdata as $key => $value) {
      switch ($key) {
        case "card_bg_color":
        case "card_bg_image":
				case "card_status";
        case "logo":
        case "Profile Photo":
        case "card_label":
        case "label_color":
        case "label_font":
        case "info_color":
        case "info_font":
        case "logo_select":
          unset($finaldbdata[$key]);
          break;
        case "Display Name":
          $finaldbdata[$key] = $subject->displayname;
          break;
        case "Joining Date":
          $finaldbdata[$key] = gmdate("F d, Y", strtotime($subject->creation_date));
          break;
        case "Profile Type":
          $finaldbdata[$key] = $memberType;
          break;
        case "Membership Level":
          $finaldbdata[$key] = $level->title;
          break;
        case "Username":
          $finaldbdata[$key] = $subject->username;
          break;
        default:
          break;
      }
    }

		foreach($finaldbdata as $key => $value) {
			unset($finaldbdata[$key]);
			$key = $view->translate($key);
			$finaldbdata[$key] = $value;
		}

    foreach ($finaldbdata as $key => $value) {
      foreach ($processed_dynamic_array as $pkey => $pvalue) {
        if ($key == trim($pkey)) {
          $finaldbdata[$key] = $pvalue;
          $value = $pvalue;
        }
      }
      if (is_numeric($value))
      {
      	$fields_show_status = Engine_Api::_()->getApi('settings', 'core')->getSetting('empty.fields');
      	if(empty($fields_show_status))
      	{
      		unset($finaldbdata[$key]);
      	}
      	else {
      		$finaldbdata[$key] = "";
      	}
      }
    }
    array_unique($finaldbdata);

    foreach ($finaldbdata as $key => $value) {
      if( $key == 'Display Name' ) {	
	$key = 'Name';
      }
      if ( $key == 'Joining Date' ) {
	$key = 'Joined';
      }
      if ( $key == 'Profile Type' ) {
	$key = 'Profile';
      }
      if ( $key == 'Membership Level' ) {
	$key = 'Membership';
      }
      $retHtmlString.= $view->translate($key) . ":&nbsp;<b style='font-weight:bold; font-family:$info_font'>";
      $retHtmlString.= $value . "</b><br/>";
    }
    $retHtmlString.= "</div></div></div></div>";  //End if info div, member-details div, mc-card div
    return $retHtmlString;
  }
  
  /**
   * This function return the Member Ship Card, only for "When click on preview". Here get the value from by passing a array which make by the help  queary string.
   *
   * @param $profile_type_array: Array which contains the all information which are require for preview.
   * @return Complete card for "Preview".
   */
  public function showPreview($profile_type_array)
  {
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $level_id = Engine_Api::_()->getItem('user', $user_id)->level_id;
    $sample = Engine_Api::_()->getItem('user', $user_id);

    //Get Values from database
    $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $dbvalues = $profile_type_array['profile_type'];
    if (!Engine_Api::_()->core()->hasSubject('user'))
      Engine_Api::_()->core()->setSubject(Engine_Api::_()->user()->getUser($user_id));
    $subject = Engine_Api::_()->core()->getSubject(Engine_Api::_()->user()->getUser($user_id));
    $finaldbdata = $dbvalues;
    $retHtmlString = '';

    // Get level object
    if (null !== $level_id) {
      $level = Engine_Api::_()->getItem('authorization_level', $level_id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    // Member type
    $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);
    $memberType = "No member type";
    if (!empty($fieldsByAlias['profile_type'])) {
      $optionId = $fieldsByAlias['profile_type']->getValue($subject);
      if ($optionId) {
	$optionObj = Engine_Api::_()->fields()
			->getFieldsOptions($subject)
			->getRowMatching('option_id', $optionId->value);
	if ($optionObj) {
	  $memberType = $optionObj->label;
	}
      }
    }

    // Load fields view helpers present in the fields package
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    // $fieldStructure contains all the fields corresponding to the respected category of the user
    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);
    if (count($fieldStructure) <= 1) {
      return;
    }
    //Refined field structure selected as per the admin
    $newFieldStructure = array();
    foreach ($dbvalues as $dbkey => $dbval) {
      foreach ($fieldStructure as $key => $fieldValue) {
	if ($dbval == $fieldValue->child_id) {
	  $newFieldStructure[$key] = $fieldValue;
	  continue;
	}
      }
    }
      
    /*
    * return string is processed here
    * $card_bg_image:   background image if selected by the user
    * $card_bg_color:   background color if selected by the user
    * $label_color     
    */

    //Start the mc-card div here
    if( empty($profile_type_array['card_background']) ) { // Show thw back groud image.
      if (!empty($profile_type_array['card_background_image'])) {
				$card_bg_image = $profile_type_array['card_background_image'];
				$retHtmlString.= "<div class='mc-card'  style='background:url($card_bg_image);'><div class='mc-card-inner' style='bakground:none;'>";
      }
      else {
				if( !empty($profile_type_array['database_background_image']) ) // Image which are set in data base.
				{
					$bg_image = Engine_Api::_()->mcard()->displayPhoto($profile_type_array['database_background_image']);
					$retHtmlString.= "<div class='mc-card'  style='background:url($bg_image);'><div class='mc-card-inner' style='bakground:none;'>";
				}
				else // Show the default image
				{
					$bg_image = $view->layout()->staticBaseUrl . 'application/modules/Mcard/externals/images/default-card-bg.jpg';
					$retHtmlString.= "<div class='mc-card'  style='background:url($bg_image);'><div class='mc-card-inner' style='bakground:none;'>";
				}
      }
    }
    else { // Show the back ground color.
      $card_bg_image = $baseUrl . '/application/modules/Mcard/externals/images/default_big.png';
      if (!empty($profile_type_array['card_bg_color'])) {      
				$card_bg_color = $profile_type_array['card_bg_color'];
				$retHtmlString.= "<div class='mc-card'  style='background:$card_bg_color url($card_bg_image) no-repeat;'><div class='mc-card-inner'>";
      } else {
				$retHtmlString.= "<div class='mc-card'  style='background:url($card_bg_image);'><div class='mc-card-inner'>";
      }
    }

    //Start the logo div here
    $retHtmlString.= "<div class='logo'>";
    if( !empty($profile_type_array['logo_select']) )
    {
      if ( !empty($profile_type_array['upload_logo_image'])) {
	$logo = $profile_type_array['upload_logo_image'];//Engine_Api::_()->mcard()->displayPhoto($logo);
	$retHtmlString.= $view->htmlImage($logo);
      } else {
	  if( $profile_type_array['database_logo_image'] != 'null' )
	  {
	    $logo = Engine_Api::_()->mcard()->displayPhoto($profile_type_array['database_logo_image']);
	    $retHtmlString.= $view->htmlImage($logo);
	  }
	  else { 
	  $retHtmlString.= $view->htmlImage($view->layout()->staticBaseUrl . 'application/modules/Mcard/externals/images/default-card--logo.jpg');
	}
      }
    }
    $retHtmlString.= "</div> ";     //End of logo div
    //Start the head div here
    $retHtmlString.= "<div class='head' style='color:" . $profile_type_array['title_color'] . "; font-family:" . $profile_type_array['title_font'] . ";'>";
    if ( !empty($profile_type_array['show_title_value']) && !empty($profile_type_array['show_title']) ) {
      $retHtmlString.= $profile_type_array['show_title_value'];
    }
    $retHtmlString.= "</div>";  //End of head div i.e. for card_label
    //Start the member-details div here
    $retHtmlString.= "<div class='member-details'>";
    if ( !empty($profile_type_array['profile_photo_display']) ) { 
      $retHtmlString.= "<div class='member-photo'>";
      $retHtmlString.= $view->itemPhoto($sample->getOwner(), 'thumb.profile');
      $retHtmlString.= "</div>";  //End of member-photo div
    }

    //Start the info div here
    $retHtmlString.= "<div class='info' style='color:" . $profile_type_array['information_color'] . "; font-family: " . $profile_type_array['information_font'] . " ;'>";
    $dynamic_array = $view->fieldValueLoop($view->subject(), $newFieldStructure);
    $dynamic_array = strip_tags($dynamic_array, "<span><li>");
    $liexploded = explode("</li>", $dynamic_array);
    foreach ($liexploded as $key => $lival) {
      $spanexploded[] = explode("</span>", $lival);
    }
    $processed_dynamic_array = array();
    foreach ($spanexploded as $key => $spanval) {
      $tkey = "";
      if (!empty($spanval[0])) {
	$tkey = strip_tags($spanval[0]);
      }
      if (!empty($tkey)) {
	$processed_dynamic_array[$tkey] = !empty($spanval[1]) ? strip_tags($spanval[1]) : "";
      }
    }
    $profile_data_array = array();
    foreach ( $profile_type_array['profile_type'] as $profile_type_id )
    {
      if ( $profile_type_id == 'displayname' ) {
	$profile_data_array['Display Name'] = $subject->displayname;
      }
      elseif ( $profile_type_id == 'mlevel_id' ) {
	$profile_data_array['Membership Level'] = $level->title;
      }
      elseif ( $profile_type_id == 'mptype' ) {
	$profile_data_array['Profile Type'] = $memberType;
      }
      elseif ( $profile_type_id == 'doj' ) {
	$profile_data_array['Joining Date'] = gmdate("F d, Y", strtotime($subject->creation_date));
      }
      elseif ( $profile_type_id == 'username' ) {
	$profile_data_array['Username'] = $subject->username;
      }
      elseif ( is_numeric($profile_type_id) ) {
	$profile_type_key = Engine_Api::_()->getItem('mcard_meta', $profile_type_id)->label;
	$profile_data_array[$profile_type_key] = $profile_type_id;
      }
    }

		foreach($profile_data_array as $key => $value) {
			unset($profile_data_array[$key]);
			$key = $view->translate($key);
			$profile_data_array[$key] = $value;
		}

    foreach ($profile_data_array as $key => $value) {
      foreach ($processed_dynamic_array as $pkey => $pvalue) {
	if ($key == trim($pkey)) {
	  $profile_data_array[$key] = $pvalue;
	  $value = $pvalue;
	}
      }
      if (is_numeric($value))
      {
	$fields_show_status = $profile_type_array['show_empty_field'];
	if(empty($fields_show_status))
	{
		unset($profile_data_array[$key]);
	}
	else {
		$profile_data_array[$key] = "";
	}
      }
    } 
    array_unique($profile_data_array);
    foreach ($profile_data_array as $key => $value) {
      if( $key == 'Display Name' ) {	
	$key = 'Name';
      }
      if ( $key == 'Joining Date' ) {
	$key = 'Joined';
      }
      if ( $key == 'Profile Type' ) {
	$key = 'Profile';
      }
      if ( $key == 'Membership Level' ) {
	$key = 'Membership';
      }
      $retHtmlString.= $view->translate($key) . ":&nbsp;<b style='font-weight:bold;'>";
      $retHtmlString.= $value . "</b><br/>";
    }
    $retHtmlString.= "</div></div></div></div>";  //End if info div, member-details div, mc-card div
    return $retHtmlString;
  }
}