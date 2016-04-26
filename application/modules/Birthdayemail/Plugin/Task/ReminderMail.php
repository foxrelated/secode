<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthdayemail
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: ReminderMail.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Birthdayemail_Plugin_Task_ReminderMail extends Core_Plugin_Task_Abstract
{
   public function execute() 
  {
    // fetch that time stamp when the reminder mail was last sent
    $taskstable = Engine_Api::_()->getDbtable('tasks', 'core');
    $rtasksName = $taskstable->info('name');
    $taskstable_result = $taskstable->select()
				    ->from($rtasksName, array('started_last'))
				    ->where('title = ?', 'Birthday Reminder')
				    ->where('plugin = ?', 'Birthdayemail_Plugin_Task_ReminderMail')
				    ->limit(1);
				   
    $value = $taskstable->fetchRow($taskstable_result);
    $old_started_last = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.startedlast', 0);

    // check if it is upgraded version
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if($coreversion == '4.1.1') {
                        if(!Engine_Api::_()->birthdayemail()->canRunTask("birthdayemail","Birthdayemail_Plugin_Task_ReminderMail", $old_started_last)){
                                return ;
                        }
    }
    Engine_Api::_()->getApi('settings', 'core')->setSetting('birthdayemail_startedlast', $value['started_last']);
    
    // create an object for view
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $date = time();
    $days_string = "";
    $birthdayemail_reminder_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.reminder.time', 1);
     
    // find the difference between current time and the time when the mail was last sent
    $diff = (int)(($date - $old_started_last)/86400);

    switch($birthdayemail_reminder_time) {

      // 1st Case when mail reminder for the tomorrow's birthdays has to be sent
      case 0:
	$days_string.= $view->translate("tomorrow");
	$this->mail_send("TW", $days_string);
	break;

      // 2nd Case when mail reminder for this week's birthdays has to be sent on sunday
      case 1:
	$days_string.= $view->translate("this week");
	$current_day = date('w', $date);

	// days left in this week
	$days_left = 7 - $current_day;

	// if current day is sunday, send mail in any case
	if($current_day == 0) {
	    $this->mail_send($days_left, $days_string);
	}

	// if due to inactive state of site mail was not sent on sunday send it on tuesday or so on till saturday
	else {

	  // check if the time difference is greater than the time that has passed from this week's start
	  if($diff > $current_day) {
	    $this->mail_send($days_left, $days_string);   
	  }
	}
	break;
      
      // 3rd Case when mail reminder for this month's birthdays has to be sent on 1st day of the month
      case 2:
	$days_string.= $view->translate("this month");
	$current_date = date('j', $date);
	$days_in_current_month = date('t', $date);

	// days left in this month
	$days_left = $days_in_current_month - $current_date + 1;

	// if current day is 1st date of the month, send mail in any case
	if($current_date == 1) {
	  $this->mail_send($days_left, $days_string);
	}

	// if due to inactive state of site mail was not sent on 1st send it on 2nd or so on till 2nd last date of the month
	else {

	  // check if the time difference is greater than the time that has passed from this month's start
	  if($diff > $current_date) {
	    $this->mail_send($days_left, $days_string);
	  }
	}
	break;
    }
  }
public function mail_send($days_left, $days_string)
  {
   // create an object for view
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    //check Birthday field privacy so as to show age or not
    $metatable = Engine_Api::_()->getDbTable('metas', 'birthday');
    $rmetaName = $metatable->info('name');
    $select = $metatable->select()
			->from($rmetaName, array('display'))
			->where('type = ?', 'birthdate')
			->limit(1);
    $result = $metatable->fetchRow($select);
    $age_display = $result['display'];

    $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.site.title',  Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1));
    $site_title_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.title.color', "#ffffff");
    $site_header_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.color', "#79b4d4");

    $current_year = date('Y', time());
    
    $table = Engine_Api::_()->getDbTable('users', 'user');
    $select = $table->select();
    $result = $table->fetchAll($select);

    // for each member
    foreach($result as $values) {
      $owner_id = $values->user_id;
      $owner_email = $values->email;
      $owner_name = $values->displayname;

      // check enabled notification settings
      $notificationsettingstable = Engine_Api::_()->getDbtable('notificationSettings', 'activity');
      $notificationsettings_result = $notificationsettingstable->select()
					->where('user_id = ?', $owner_id)
					->where('type = ?', 'birthday_reminder')
					->limit(1);

      $row = $notificationsettingstable->fetchRow($notificationsettings_result);
      if( null === $row ) {

  // initialize the string to be send in the mail
  $friends_string = '';
  $template_header = "";
  $template_footer = "";

  //check if Sitemailtemplates Plugin is enabled
  $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');

  if(!$sitemailtemplates) {
		$template_header.= "<table width='98%' cellspacing='0' border='0'><tr><td width='100%' bgcolor='#f7f7f7' style='font-family:arial,tahoma,verdana,sans-serif;padding:40px;'><table width='620' cellspacing='0' cellpadding='0' border='0'>";
		$template_header.= "<tr><td style='background:".$site_header_color."; color:$site_title_color;font-weight:bold;font-family:arial,tahoma,verdana,sans-serif; padding: 4px 8px;vertical-align:middle;font-size:16px;text-align: left;' nowrap='nowrap'>". $site_title. "</td></tr><tr><td valign='top' style='background-color:#fff; border-bottom: 1px solid #ccc; border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; font-family:arial,tahoma,verdana,sans-serif; padding: 15px;padding-top:0;' colspan='2'><table width='100%'><tr><td>";
		$template_footer.= "</td></tr></table></td></tr></table>";

  }
	// check birthdays limit
	$param['display_today_birthday'] = $days_left;
	
	// set limit to user and display birthdays according to admin settings
	$param['limit'] = 0;
	$param['viewer_id'] = $owner_id;
	$day_order = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31";

	$table = Engine_Api::_()->getDbtable('metas', 'birthday');
	$field_object = $table->getFields_birthday($param);
	$field_object->order("field(Day,".$day_order.')');
	$friends_result = $table->fetchAll($field_object);

	// count number of birthdays in this week
	$count_friends = count($friends_result);
	
	// take a flag to check that cureent birthday entry group has been started or not
	$flag = 0;
	$old_date = date('d', time());

	// for each friend with birthday in this week
	foreach($friends_result as $friends) {

	  $date_array = explode("-", $friends->value);
	  $age = $current_year - $date_array[0];
	  
	  // text of the month in which this birthday occurs
	  $month_text = date('F', mktime(0, 0, 0, $date_array[1]));

    $friends_string.= "<table width='100%'>";
	  
	  if($flag == 0 || $old_date != $date_array[2]) {
	    $friends_string.=  "<tr><td style='padding:5px; font-size: 11px; border-bottom: 1px solid rgb(204, 204, 204); min-height: 18px;' colspan='2'>" . $date_array[2] ."\t". $view->translate($month_text)."</td></tr>";
	    $flag = 1;
	  }
	
		$friends_user = Engine_Api::_()->user()->getUser($friends->item_id);
		$profile_link = 'http://' . $_SERVER['HTTP_HOST'] . $friends_user->getHref();
	  
	  // if member has his own profile photo
	  if(!empty($friends->photo_id)) {
	    $photosrc = 'http://' . $_SERVER['HTTP_HOST'] .  $friends_user->getPhotoUrl('thumb.icon');
	  }
	  // By default photo
	   else {
	    $photosrc = 'http://' . $_SERVER['HTTP_HOST']. $view->layout()->staticBaseUrl. "application/modules/User/externals/images/nophoto_user_thumb_icon.png";
	  }
	  
	  $title_photo_link = "<tr><td width='60px' style='padding:5px 0;'><a href = $profile_link title = $friends->displayname ><img src = '$photosrc' style='border:1px solid #ccc;' width='48px' height='48px' /></a></td><td style='padding: 5px 0px 10px;'><span style='font-weight: bold; font-size: 13px;'><a href = $profile_link title = '$friends->displayname' style='text-decoration:none;color:#3B5998;'>$friends->displayname</a></span><br>";
	  $friends_string.= $title_photo_link. "<span style='font-size: 11px;'>";
	  if(!empty($age_display) && !empty($date_array[0])) {
	    $friends_string.= $age. "\t". $view->translate("years old"). "&nbsp;&nbsp;|&nbsp;&nbsp;";
	  }
	  $friends_string.= "<a href = '$profile_link' style='text-decoration:none;color:#3B5998;'>". $view->translate('Write on wall'). "</a></span></td></tr></table>";

	  $old_date = $date_array[2];
	}
		
	// send mail to member if there are any of his friends' birthday in this week
	if(!empty($count_friends)) {
	    $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;                                  
	    Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner_email, 'birthday_reminder', array(
	      'recipient_title' => $owner_name,
	      'template_header' =>$template_header,
	      'friend_list' => $friends_string,
	      'template_footer' => $template_footer,
	      'count_friends' => $count_friends,
	      'days' => $days_string,
	      'email' => $email,
				'host' => $_SERVER['HTTP_HOST'],
	      'queue' => true )); 
	}
      }
    }
  }
}