<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthdayemail
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: AdminSettingsController.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Birthdayemail_AdminSettingsController extends Core_Controller_Action_Admin
{
  const IMAGE_WIDTH = 200;
  const IMAGE_HEIGHT = 200;
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('birthday_admin_main', array(), 'birthdayemail_admin_main_settings');
    $birthday_admin_tabb = 'birthday_email';
    // generate the form
    $this->view->form  = $form = new Birthdayemail_Form_Admin_Email();
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if(!($coreversion < '4.1.0')) {
      $enable_var = "processes";
    }
    else {
      $enable_var = "enabled";
    }
    $taskstable = Engine_Api::_()->getDbtable('tasks', 'core');
    $rtasksName = $taskstable->info('name');
    $taskstable_result = $taskstable->select()
				    ->from($rtasksName, array($enable_var,'timeout'))
				    ->where('title = ?', 'Birthday Reminder')
				    ->orwhere('title = ?', 'Birthday Wish')
				    ->where('plugin = ?', 'Birthdayemail_Plugin_Task_ReminderMail')
				    ->orwhere('plugin = ?', 'Birthdayemail_Plugin_Task_WishMail' );
    $prefields = $taskstable->fetchAll($taskstable_result);

    // populate form
     $form->populate(array(
      'birthdayemail_reminder' => $prefields[0][$enable_var],
      'birthdayemail_wish' => $prefields[1][$enable_var]
    ));
    
    // check post
    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      if( isset($_FILES['birthdayemail_wish_image']) && is_uploaded_file($_FILES['birthdayemail_wish_image']['tmp_name']) )
	  {
	    $file = $_FILES['birthdayemail_wish_image'];
	    $name = basename($file['tmp_name']);
	    $path = dirname($file['tmp_name']);
	    $mainName  = $path.'/'.$file['name'];

	    // Get Viewer Id
	    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	    $photo_params = array(
	      'parent_id'  => $viewer_id, 
	      'parent_type'=> "birthdayemail",
	    );
	    
	    // Resize Image
	    $image = Engine_Image::factory();
	    $image->open($file['tmp_name'])
		->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
		->write($mainName)
		->destroy();

	    try {
	      $photoFile = Engine_Api::_()->storage()->create($mainName,  $photo_params); }
	    catch (Exception $e) { 
	      if ($e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE)
	      {
		echo $e->getMessage();
		exit();
	      }
	    }

	    // Delete previous file
	    $previous_photo_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail_wish_image', 0);
	    if(!empty($previous_photo_id)) {
	      $table = Engine_Api::_()->getItemTable('storage_file');
	      $select = $table->select()
			      ->from($table->info('name'), 'file_id')
			      ->where('file_id = ?', $previous_photo_id);                                                
	      $rows = $table->fetchAll($select)->toArray();
	      if(!empty($rows)) {
		      $file_id = $rows[0]['file_id'];                
		      $file = Engine_Api::_()->getItem('storage_file', $file_id);
		      $file->delete();
	      }
	    }
	  // Save the photo id of uploaded image from Storage files table in the core settings table
	  Engine_Api::_()->getApi('settings', 'core')->setSetting('birthdayemail_wish_image', $photoFile->file_id);
	}

	

      // get admin settings
      $values = $form->getValues();
      $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
      $coreversion = $coremodule->version;
      if(!($coreversion < '4.1.0')) {
	      $enable_var = 'processes';
      }
      else {
	      $enable_var = 'enabled';
      }
      //check if Sitemailtemplates Plugin is enabled
			$sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');
      Engine_Api::_()->getApi('settings', 'core')->setSetting('birthdayemail_reminder_time', $values['birthdayemail_reminder_options']);
      if(empty($sitemailtemplates)) {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('birthdayemail_color', $values['birthdayemail_color']);
				Engine_Api::_()->getApi('settings', 'core')->setSetting('birthdayemail_site_title', $values['birthdayemail_site_title']);
				Engine_Api::_()->getApi('settings', 'core')->setSetting('birthdayemail_title_color', $values['birthdayemail_title_color']);
      }
      Engine_Api::_()->getApi('settings', 'core')->setSetting('birthdayemail_demo', $values['birthdayemail_demo']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('birthdayemail_admin', $values['birthdayemail_admin']);
      $tasksTable = Engine_Api::_()->getDbtable('tasks', 'core');
      $tasksTable->update(array($enable_var => $values['birthdayemail_wish']), array('title = ?' => 'Birthday Wish', 'plugin = ?' => 'Birthdayemail_Plugin_Task_WishMail'));
      $tasksTable->update(array($enable_var => $values['birthdayemail_reminder']), array('title = ?' => 'Birthday Reminder', 'plugin = ?' => 'Birthdayemail_Plugin_Task_ReminderMail'));      

      // SEND TEST MAILS IF ADMIN WANTS
      if($values['birthdayemail_demo'] == 1) {

	// create an object for view
	$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

	// get site title and color
	$site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.site.title',  Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1));
	$reciever_email = $values['birthdayemail_admin'];
	$reciever_name = "Sample name";

	// initialize the string to be send in the wish mail
	$string = '';
	$template_header = "";
	$template_footer = "";

  if(!$sitemailtemplates) {
		$site_title_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.title.color', "#ffffff");
		$site_header_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.color', "#79b4d4");
		$template_header.= "<table width='98%' cellspacing='0' border='0'><tr><td width='100%' bgcolor='#f7f7f7' style='font-family:arial,tahoma,verdana,sans-serif;padding:40px;'><table width='620' cellspacing='0' cellpadding='0' border='0'>";
		$template_header.= "<tr><td style='background:".$site_header_color."; color:$site_title_color;font-weight:bold;font-family:arial,tahoma,verdana,sans-serif; padding: 4px 8px;vertical-align:middle;font-size:16px;text-align: left;' nowrap='nowrap'>". $site_title. "</td></tr><tr><td valign='top' style='background-color:#fff; border-bottom: 1px solid #ccc; border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; font-family:arial,tahoma,verdana,sans-serif; padding: 15px;padding-top:0;' colspan='2'><table width='100%'><tr><td>";
	
		$template_footer.= "</td></tr></table></td></tr></table>";

  }

	// Get Image
	$image_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.wish.image', 0);
	if(!empty($image_id)) {
	  //$path = 'http://' . $_SERVER['HTTP_HOST']. Engine_Api::_()->storage()->get($image_id, '')->getHref();
	  $cdn_path = Engine_Api::_()->birthdayemail()->getCdnPath();
	  $img_path = Engine_Api::_()->storage()->get($image_id, '')->getPhotoUrl();
	  if($cdn_path == "") {
	    $path = 'http://' . $_SERVER['HTTP_HOST'] . $img_path;
	  }
	  else {
	    $img_cdn_path = str_replace($cdn_path, '',  $img_path);
	    $path = $cdn_path. $img_cdn_path;
	  }
	}
	else {
	  // By Default image
	  $path = 'http://' . $_SERVER['HTTP_HOST']. $view->baseUrl(). '/application/modules/Birthdayemail/externals/images/ChocolateBirthdayCake.jpg';
	}
	$image = "<img src = '$path' width='200' height='200' />";
	$string.= "<table><tr><td>". $image. "</td></tr></table>";
	
	  // send test mail for wish
	  $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;                                  
	  Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciever_email, 'birthday_wish', array(
	      'recipient_title' => $reciever_name,
	      'template_header' => $template_header,
	      'wish_image' => $string,
	      'template_footer' => $template_footer,
	      'site_title' => $site_title,
	      'email' => $email,
	      'queue' => false ));

	// VARIABLES FOR THE REMINDER MAIL
	$days_string = "";
	$birthdayemail_reminder_time = $values['birthdayemail_reminder_options'];

	switch($birthdayemail_reminder_time) {

	  // 1st Case when mail reminder for the tomorrow's birthdays has to be sent
	  case 0:
	    $days_string.= $this->view->translate("tomorrow");
	    break;

	  // 2nd Case when mail reminder for this week's birthdays has to be sent on sunday
	  case 1:
	    $days_string.= $this->view->translate("this week");
	    break;
	  
	  // 3rd Case when mail reminder for this month's birthdays has to be sent on 1st day of the month
	  case 2:
	    $days_string.= $this->view->translate("this month");
	    break;
	}

	$friends_string = '';
	$flag = 0;
	$count_friends = 3;
	$age = 25;
	$i = 1;
	$tomorrow_date = date('d', time()) + 1;
	$j = date('d', mktime(0, 0, 0, date("m"), $tomorrow_date));
	while($i <= $count_friends) {
	  $title = $this->view->translate('Sample Friend'). "\t". $i;
	  $profile_link = 'http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl();
	  $itemphoto = $view->layout()->staticBaseUrl.  "application/modules/User/externals/images/nophoto_user_thumb_icon.png";

	  $friends_string.= "<table width='100%'>";

	  if($flag == 0 || !empty($birthdayemail_reminder_time)) {
	    $friends_string.=  "<tr><td style='padding:5px; font-size: 11px; border-bottom: 1px solid rgb(204, 204, 204); min-height: 18px;' colspan='2'>" . $j. "\t". $this->view->translate('November'). "</td></tr>";
	    $flag = 1;
	  }
	  $photosrc =  'http://' . $_SERVER['HTTP_HOST'] . $itemphoto;
	  $title_photo_link = "<tr><td width='60px' style='padding:5px 0;'><a href = $profile_link title = $title ><img src = '$photosrc' style='border:1px solid #ccc;'  /></a></td><td style='padding: 5px 0px 10px;'><span style='font-weight: bold; font-size: 13px;'><a href = $profile_link title = '$title' style='text-decoration:none;color:#3B5998;'>$title</a></span><br>";
	  $friends_string.= $title_photo_link. "<span style='font-size: 11px;'>" .$age. "\t". $this->view->translate('years old'). "&nbsp;&nbsp;|&nbsp;&nbsp;<a href = '$profile_link' style='text-decoration:none;color:#3B5998;'>". $view->translate('Write on wall'). "</a></span></td></tr></table>";
	  $i++;
	  $j++;
	}
	  // send test mail for reminder                              
	  Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciever_email, 'birthday_reminder', array(
	    'recipient_title' => $reciever_name,
	    'template_header' =>$template_header,
	    'friend_list' => $friends_string,
	    'template_footer' => $template_footer,
	    'count_friends' => $count_friends,
	    'days' => $days_string,
	    'email' => $email,
			'host' => $_SERVER['HTTP_HOST'],
	    'queue' => false )); 
      }
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    } 
  }
}
