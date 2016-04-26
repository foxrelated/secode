<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Metas.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Birthday_Model_DbTable_Metas extends Engine_Db_Table {

  protected $_name = 'user_fields_meta';
  protected $_rowClass = 'Birthday_Model_Meta';

  public function getFields_birthday($param = array()) {
    $date = time();
    $current_month = date('m', $date);
    $current_date_month = date('m-d', $date);
    $tomorrow_date = date('d', $date) + 1;
    $tomorrow_date_month = date('m-d', mktime(0, 0, 0, date("m"), $tomorrow_date));
    
    // in case of reminder mail to calculate the last limit to which birthdays have to be taken out
    $end_date = date('d', $date) + $param['display_today_birthday'];
    $end_date_month = date('m-d', mktime(0, 0, 0, date("m"), $end_date));
    $end_date_month_year = date('Y-m-d', mktime(0, 0, 0, date("m"), $end_date));
    $lastweek = "'". '12-'. date('d', $date). "'";
    
    $viewer_id = $param['viewer_id'];
    $rmetaName = $this->info('name');

    $valuetable = Engine_Api::_()->getDbTable('values', 'birthday');
    $rvalueName = $valuetable->info('name');
   
    $usertable = Engine_Api::_()->getDbTable('users', 'user');
    $ruserName = $usertable->info('name');
  
    $membershiptable = Engine_Api::_()->getDbTable('membership', 'user');
    $rmembershipName = $membershiptable->info('name');
    
     $select = $this->select()
		    ->setIntegrityCheck(false)
        ->from($rmetaName, array($rmetaName. '.field_id' , $rvalueName. '.item_id' , $rvalueName. '.value', $ruserName. '.photo_id', $ruserName. '.username', $ruserName. '.displayname' ))
		    ->join($rvalueName, $rvalueName . '.field_id = ' . $rmetaName . '.field_id', array("DATE_FORMAT(" . $rvalueName. " .value, '%m') AS Month", "DATE_FORMAT(" . $rvalueName. " .value, '%d') AS Day"))
		    ->join($ruserName, $rvalueName . '.item_id = ' . $ruserName . '.user_id', array())
		    ->join($rmembershipName, $rvalueName . '.item_id = ' . $rmembershipName . '.resource_id', array())
		    ->where($rmetaName . '.type = ?', 'birthdate')
		    ->where($rvalueName. '.item_id <> ?', $viewer_id)
		    ->where($rmembershipName. '.user_id = ?', $viewer_id)	
		    ->where($rmembershipName. '.active = ?', 1);
		
    if( !empty($param['display_today_birthday']) )
    {
      // In case we need to fetch birthdays of Today
      if($param['display_today_birthday'] == "TY") {
				$select->where("DATE_FORMAT(" . $rvalueName. " .value, '%m-%d') = ?", $current_date_month);
      }
       // In case we need to fetch birthdays of a particular month
       elseif($param['display_today_birthday'] == "M") {
				$active_month = date('m', $param['active_month']);
				$select->where("DATE_FORMAT(" . $rvalueName. " .value, '%m') = ?", $active_month);
      }
      // In case we need to fetch birthdays of Tomorrow
      elseif($param['display_today_birthday'] == "TW") {
				$select->where("DATE_FORMAT(" . $rvalueName. " .value, '%m-%d') = ?", $tomorrow_date_month);
      }
      else {
				$select->where("DATE_FORMAT(" . $rvalueName. " .value, '%m-%d') >= ?", $current_date_month);

        if($current_date_month >= '12-25' && $current_date_month >= $lastweek && $current_date_month <= '12-31') {
		        $select->where("DATE_FORMAT(" . $rvalueName. " .value, '%Y-%m-%d') < ?", $end_date_month_year);
				}
				else {
					$select->where("DATE_FORMAT(" . $rvalueName. " .value, '%m-%d') < ?", $end_date_month);
				}  
      }
    }	    
		if( !empty($param['limit']) ) {
      $select->limit($param['limit']);
    }

    return $select;
  }

   public function getFields2($param = array()) {

    $viewer_id = $param['viewer_id'];
    $rmetaName = $this->info('name');

    $valuetable = Engine_Api::_()->getDbTable('values', 'birthday');
    $rvalueName = $valuetable->info('name');
   
    $usertable = Engine_Api::_()->getDbTable('users', 'user');
    $ruserName = $usertable->info('name');
  
    $membershiptable = Engine_Api::_()->getDbTable('membership', 'user');
    $rmembershipName = $membershiptable->info('name');
    
    $select = $this->select()
		    ->setIntegrityCheck(false)
        ->from($rmetaName, array($rmetaName. '.field_id' , $rvalueName. '.item_id' , $rvalueName. '.value', $ruserName. '.photo_id', $ruserName. '.username', $ruserName. '.displayname' ))
		    ->join($rvalueName, $rvalueName . '.field_id = ' . $rmetaName . '.field_id', array("DATE_FORMAT(" . $rvalueName. " .value, '%m') AS Month", "DATE_FORMAT(" . $rvalueName. " .value, '%d') AS Day", "count($rvalueName. value) AS total_count"))
		    ->join($ruserName, $rvalueName . '.item_id = ' . $ruserName . '.user_id', array())
		    ->join($rmembershipName, $rvalueName . '.item_id = ' . $rmembershipName . '.resource_id', array())
		    ->where($rmetaName . '.type = ?', 'birthdate')
		    ->where($rvalueName. '.item_id <> ?', $viewer_id)
		    ->where($rmembershipName. '.user_id = ?', $viewer_id)	
		    ->where($rmembershipName. '.active = ?', 1)
		    ->group($rmembershipName. '.user_id');
    return $select;
  }

  //Get the friend and get the post of friend.
  public function activity($viewer_id, $post, $member_birthday_wish) {

    //start for Birth day work
    $date = time();
    $current_month = date('m', $date);
    $current_date_month = date('m-d', $date);

    $rmetaName = $this->info('name');
        
    $activityTable = Engine_Api::_()->getDbtable( 'actions' , 'activity' ) ;
    $activityName = $activityTable->info( 'name' ) ;

    $usertable = Engine_Api::_()->getDbTable('users', 'user');
    $ruserName = $usertable->info('name');
    
    $membershiptable = Engine_Api::_()->getDbTable('membership', 'user');
    $rmembershipName = $membershiptable->info('name');

    $maptable =  Engine_Api::_()->fields()->getTable('user', 'maps');
    $rmapName = $maptable->info('name');
    
    $metaTable = Engine_Api::_()->getDbTable('metas', 'birthday');
    $rmetaName = $metaTable->info('name');

    $valuetable = Engine_Api::_()->getDbTable('values', 'birthday');
    $rvalueName = $valuetable->info('name');

    $select = $this->select()
              ->setIntegrityCheck(false)
              ->from( $rmetaName , array($rmetaName. '.field_id' , $rvalueName. '.item_id' , $rvalueName. '.value', $ruserName.'.user_id' ))
              ->join( $rvalueName, $rvalueName . '.field_id = ' . $rmetaName . '.field_id', array("DATE_FORMAT(" . $rvalueName. " .value, '%m') AS Month", "DATE_FORMAT(" . $rvalueName. " .value, '%d')
              AS Day"))
              ->join( $ruserName, $rvalueName . '.item_id = ' . $ruserName . '.user_id', array())
              ->join( $rmembershipName, $rvalueName . '.item_id = ' . $rmembershipName . '.resource_id', array())
              ->where( "DATE_FORMAT(" . $rvalueName. " .value, '%m-%d') = ?", $current_date_month)
              ->where( $rmetaName . '.type = ?', 'birthdate')
              ->where( $rvalueName . '.item_id <> ?', $viewer_id)
              ->where( $rmembershipName . '.user_id = ?', $viewer_id)
              ->where( $rmembershipName . '.active = ?', 1);
    $field_result = $this->fetchAll($select);

    foreach($field_result as $key => $field_results) {
      $result[] = $field_results['user_id'];
    }
    if (!empty($result)) {

      $user = Engine_Api::_()->user()->getViewer();
      $user_id = $user->membership()->getMembershipsOfIds();
      $user_id = array_merge(array($viewer_id ),$user_id );
      $activity_select = $activityTable->select()
            ->setIntegrityCheck( false )
            ->from($ruserName, array($ruserName. '.displayname' , $ruserName. '.user_id', $ruserName. '.photo_id'))
           ->joinInner( $activityName , "$activityName . subject_id = $ruserName . user_id" )
             ->where( $activityName . '.subject_id IN (?)', (array)$user_id)
              ->where( $activityName . '.object_id IN (?)', (array)$result)
              ->where( $activityName . '.object_type = ?' , 'user' )
              //->group( $activityName . '.subject_id' )
              //->order( $activityName . '.action_id DESC' )
              ->order( $activityName . '.date DESC' );
              //->limit(2);

            if ($post == 'post') {
              $activity_select->where( $activityName . '.type = ?' , 'post' );
            } elseif ($member_birthday_wish == 'member_birthday_wish' ) {
              $activity_select->where( $activityName . '.type = ?' , 'member_birthday_wish' );
            }

      $activity_result = $activityTable->fetchAll($activity_select);

      //Birth day end
      return $activity_result;
    }
  }
}
?>