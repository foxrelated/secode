<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthdayemail
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: WishMail.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Birthdayemail_Plugin_Task_WishMail extends Core_Plugin_Task_Abstract
{
   public function execute()
  {
     // fetch that time stamp when the wish mail was last sent
    $taskstable = Engine_Api::_()->getDbtable('tasks', 'core');
    $rtasksName = $taskstable->info('name');
    $taskstable_result = $taskstable->select()
				    ->from($rtasksName, array('started_last'))
				    ->where('title = ?', 'Birthday Wish')
				    ->where('plugin = ?', 'Birthdayemail_Plugin_Task_WishMail')
				    ->limit(1);
				  
    $value = $taskstable->fetchRow($taskstable_result);
    $old_started_last = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.wishstartedlast', 0);

    // check if it is upgraded version
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if($coreversion == '4.1.1') {
                        if(!Engine_Api::_()->birthdayemail()->canRunTask("birthdayemail","Birthdayemail_Plugin_Task_WishMail", $old_started_last)){
                                return ;
                        }
    }
    Engine_Api::_()->getApi('settings', 'core')->setSetting('birthdayemail_wishstartedlast', $value['started_last']);
 
    // create an object for view
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    // get site title and color
    $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.site.title',  Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1));
    $site_title_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.title.color', "#ffffff");
    $site_header_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.color', "#79b4d4");
    
    $usertable = Engine_Api::_()->getDbTable('users', 'user');
    $ruserName = $usertable->info('name');

    $metaTable = Engine_Api::_()->getDbTable('metas', 'birthday');
    $rmetaName = $metaTable->info('name');

    $valuetable = Engine_Api::_()->getDbTable('values', 'birthday');
    $rvalueName = $valuetable->info('name');
    
    $select = $usertable->select()
		    ->setIntegrityCheck(false)
                    ->from($ruserName, array($ruserName. '.displayname' , $ruserName. '.user_id',  $ruserName. '.email', $ruserName. '.photo_id'))
		    ->join($rvalueName, $rvalueName . '.item_id = ' . $ruserName . '.user_id', array()) 
        ->join($rmetaName, $rmetaName . '.field_id = ' . $rvalueName . '.field_id', array())
        ->where($rmetaName . '.type = ?', 'birthdate')
		    ->where("DATE_FORMAT(" . $rvalueName. " .value, '%m-%d') = ?", date('m-d'))
		    ;

    $result = $usertable->fetchAll($select); 

    // for each member with birthday today
    foreach($result as $results) {
      $reciever_email = $results->email;
      $reciever_name = $results->displayname;
      $reciever_id = $results->user_id;

      // initialize the string to be send in the mail
      $string = '';
      $template_header = "";
      $template_footer = "";

      $template_header.= "<table width='98%' cellspacing='0' border='0'><tr><td width='100%' bgcolor='#f7f7f7' style='font-family:arial,tahoma,verdana,sans-serif;padding:40px;'><table width='620' cellspacing='0' cellpadding='0' border='0'>";
			$template_header.= "<tr><td style='background:".$site_header_color."; color:$site_title_color;font-weight:bold;font-family:arial,tahoma,verdana,sans-serif; padding: 4px 8px;vertical-align:middle;font-size:16px;text-align: left;' nowrap='nowrap'>". $site_title. "</td></tr><tr><td valign='top' style='background-color:#fff; border-bottom: 1px solid #ccc; border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; font-family:arial,tahoma,verdana,sans-serif; padding: 15px;padding-top:0;' colspan='2'><table width='100%'><tr><td>";

       // Get Image
      $image_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.wish.image', 0);
      if(!empty($image_id)) {
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
      $image = "<img src = '$path' width ='200' height ='200' />";
      $string.= "<table><tr><td>". $image. "</td></tr></table>";
      $template_footer.= "</td></tr></table></td></tr></table>";

      // send mail
      $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;                                  
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciever_email, 'birthday_wish', array(
		      'recipient_title' => $reciever_name,
		      'template_header' => $template_header,
		      'wish_image' => $string,
		      'template_footer' => $template_footer,
		      'site_title' => $site_title,
		      'email' => $email,
					'host' => $_SERVER['HTTP_HOST'],
		      'queue' => true ));
    }
  }
}
