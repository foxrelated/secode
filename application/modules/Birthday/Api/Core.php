<?php

 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Core.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Birthday_Api_Core extends Core_Api_Abstract
{
	public function get_dateDisplay ($date_value, $timestamp, $month_value = null)
	{
		//$timestamp = mktime(0, 0, 0,$date_value_array[1], $date_value_array[2], $date_value_array[3]);
		$day_text = date('l', $timestamp); 
		if(empty($month_value)) {
			$month_value = Zend_Registry::get('Zend_Translate')->_(date('F', $timestamp));
		}
		else {
			$month_value = Zend_Registry::get('Zend_Translate')->_($month_value);
		}
		$date_string = '';
		$format = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthday.listformat', 0);

		switch($format) {
			case 0 :
			$date_string.= Zend_Registry::get('Zend_Translate')->_($day_text). ', '. $month_value. ' '. $date_value;
			break;

			case 1 :
			$date_string.= Zend_Registry::get('Zend_Translate')->_($day_text). ', '. $date_value. ' '. $month_value;
			break;

			case 2 :
			$date_string.= $month_value. ' '. $date_value. ', '. Zend_Registry::get('Zend_Translate')->_($day_text);
			break;

			case 3 :
			$date_string.= $date_value. ' '. $month_value. ', '. Zend_Registry::get('Zend_Translate')->_($day_text);
			break;
		}
		return $date_string;
	}


  //for activity feed birthday work
  public function get_activityDisplay($object_id) {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $subjectIdArray = array();
    $viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
    $activityTable = Engine_Api::_()->getDbtable( 'actions' , 'activity' ) ;
    $activityName = $activityTable->info( 'name' ) ;

    $activity_select = $activityTable->select()
             ->where( $activityName . '.type = ?' , 'post' )
              ->where( $activityName . '.object_id = ?' , $object_id )
              // ->where( $activityName . '.subject_id != ?' , $viewerId )
              ->order( $activityName . '.date DESC' );

    $activity_result = $activityTable->fetchAll($activity_select);

    foreach( $activity_result as $modArray ) {
      $subjectIdArray[] = $modArray['subject_id'];
    }

    //$subjectIdArray = @array_unique($subjectIdArray);
    $subjectIdArray = @array_slice($subjectIdArray, 0, 3);
    $friendCount = @COUNT($subjectIdArray);

    $friendFlag = 0;
    
    //$subjectIdArray[] = 3;
    //$friendCount = 5;
    
    foreach( $subjectIdArray as $friendId ) {

      $friendObj = Engine_Api::_()->getItem('user', $friendId);
      $profile_url = $view->url(array('id' => $friendId), 'user_profile');
      if( empty($friendFlag) ){
      $friendid = $friendObj->getIdentity();
        $mainImage = '<a href= "'. $profile_url. '" title="'.
        $friendObj->getTitle() .'" target= "_blank">' .  $view->itemPhoto($friendObj, 'thumb.icon') . '</a>';
      }
      $userTitle = '<a href="'. $profile_url. '" title="'.
      $friendObj->getTitle() .'" target= "_blank">'  . $friendObj->getTitle() . '</a>';

     $friendStr = $this->getLikeStr($friendCount, $userTitle, $object_id, $friendid);
//       $friendObj = Engine_Api::_()->getItem('user', $friendId);
//       $profile_url = $view->url(array('id' => $friendId), 'user_profile');
//       if( empty($friendFlag) ){
//         $mainImage = '<a href= "'. $profile_url. '" title="'.
//         $friendObj->getTitle() .'" target= "_blank">' .  $view->itemPhoto($friendObj, 'thumb.icon') . '</a>';
//       }
//       $userTitle = '<a href="'. $profile_url. '" title="'.
//       $friendObj->getTitle() .'" target= "_blank">'  . $friendObj->getTitle() . '</a>';
//       //$friendStr = '';
//       if( $friendCount == 1 ) {
//         $friendStr = $userTitle;
//       } else if( $friendCount == 2 ) {
//         $friendStr .= ' and ' . $userTitle;        
//         $friendStr = trim($friendStr, ' and ');
// 
//         $tempSecondfriend .= ' , ' . $userTitle;
//         $tempSecondfriend = trim($tempSecondfriend, ' , ');
//       } else {
//       echo ' | ' . $friendStr . ' | ';die;
//         $friendStr .= $friendCount . 'Other ';
//         $friendStr = trim($friendStr, ' Other ');
//       }

      $friendFlag++;
    }
    $result['image'] = $mainImage;
    if (!empty($friendStr))
    $result['titleStr'] = $friendStr;
    return $result;
  }

  public function getLikeStr($friendCount, $userTitle, $object_id,$friendid) {

    //$friendStr = '';
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if( $friendCount == 1 ) {
      $friendStr = $userTitle;
    } else if( $friendCount == 2 ) {
      $friendStr .= ' and ' . $userTitle;
      $friendStr = trim($friendStr, ' and ');
    } else {
      $friendCount = $friendCount - 1;
      $URL = $view->url(array('module' => 'birthday', 'controller' => 'index', 'action' => 'getotherpost' , 'id' =>  $object_id, 'temp_id' => $friendid));
      $friendStr = $userTitle . ' and ' . "<a href='$URL' class='smoothbox'>" . $friendCount . ' other friends also </a>' ;
    }
    return $friendStr;
  }
}
