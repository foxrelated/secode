<?php
 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Controller.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
	
  class Birthday_Widget_ShowBirthdaysController extends Engine_Content_Widget_Abstract
  {
    public function indexAction()
    {
      // get viewer
      $viewer = Engine_Api::_()->user()->getViewer();			
      $viewer_id = $viewer->getIdentity();
      $param['viewer_id'] = $viewer_id;


      //Pickup the dynamic values from the fields_meta table according to the profile type
      $metatable = Engine_Api::_()->getDbTable('metas', 'birthday'); 
      $rmetaName = $metatable->info('name');

      $maptable =  Engine_Api::_()->fields()->getTable('user', 'maps');
      $rmapName = $maptable->info('name');

      $valuetable = Engine_Api::_()->getDbTable('values', 'birthday'); 
      $rvalueName = $valuetable->info('name');

      // to ckeck that this profile type has birthdate as a field 
			$select = $metatable->select()
		    ->setIntegrityCheck(false)
		    ->from($rmetaName, array($rmetaName. '.field_id', $rmapName. ".option_id"))
		    ->join($rmapName, $rmetaName . '.field_id = ' . $rmapName . '.child_id', array())
		    ->join($rvalueName, $rvalueName . '.value = ' . $rmapName . '.option_id', array())
		    ->where($rvalueName . '.field_id = ?', 1)
		    ->where($rvalueName . '.item_id = ?', $viewer_id)
				->where($rmetaName . '.type = ?', 'birthdate');
      $field_result = $metatable->fetchAll($select);			

      // get viewer's friends
      $userTable = Engine_Api::_()->getDbtable('users', 'user');
      $ruserName = $userTable->info('name');
      $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
      $rmembershipName = $membershipTable->info('name');
      $viewer_friends = $membershipTable->getMembershipsOfSelect($viewer);
      $viewer_friends_result = $userTable->fetchAll($viewer_friends);

      // Do not render widget if the viewer's profile type does not contain birthday or if he has no friends
      if(!$viewer->isAdmin() && (count($field_result) == 0 || count($viewer_friends_result) == 0)) {
				return $this->setNoRender();
      }
      //check the type of widget admin wants to display for birthday notification at home page
      $this->view->display_action = $display_action = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthday.widget', 3);

      // set limit as non-empty and equal to 1 so that we can limit the incoming data which are shown in our birthday widget
      $param['display_today_birthday'] = "TY";
      $param['limit'] = $display_action;

      // unset the limits if admin has set the widget view as calender
      if($display_action == 3) {
				$param['display_today_birthday'] = "M";
				$param['limit'] = 0;
				$param['active_month'] = time();
      }
     
      // get the data about members who have their birthday on the present date
      $field_object = Engine_Api::_()->getDbTable('metas', 'birthday')->getFields_birthday($param);
      $result = Engine_Api::_()->getDbTable('metas', 'birthday')->fetchAll($field_object);

       //count the members whose birthdate is present date
      $this->view->display_birthday_count = $display_birthday_count =  count($result);
 
      // if no birthdays,then don't show the widget
      if( $display_birthday_count <= 0 && $display_action != 3) {
				return $this->setNoRender();
      }
      $this->view->result = $result;
      $this->view->display_count = 0;
      $this->view->display_entries = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthday.entries', 3);

      // CALENDER WIDGET VIEW FUNCTIONALITY

      // GET THE MONTH FROM URL IF PRESENT OTHERWISE SET IT TO THE CURRENT MONTH
      $date = $this->_getParam('date_current', null);
      if (empty($date)) {
				$date = time();
      }			

      // GET THIS, LAST AND NEXT MONTHS
      $this->view->date_current = $date = mktime(0, 0, 0, date("m", $date), 1, date("Y", $date));
      $this->view->date_next = $date_next  = mktime(0, 0, 0, date("m", $date)+1, 1, date("Y", $date));
      $this->view->date_last = $date_last  = mktime(0, 0, 0, date("m", $date)-1, 1, date("Y", $date));

      //GET THE NUMBER OF DAYS IN THE MONTH
      $days_in_month = date('t', $date);

      //GET THE FIRST DAY OF THE MONTH
      $first_day_of_month = date("w", $date);
      if($first_day_of_month == 0) { $first_day_of_month = 7; }
      $this->view->first_day_of_month = $first_day_of_month;
      
      //GET THE LAST DAY OF THE MONTH
      $this->view->last_day_of_month = $last_day_of_month = ($first_day_of_month-1)+$days_in_month;

      //GET THE TOTAL NUMBER OF CELLS TO BE DISPLAYED IN THE CALENDER TABLE
      $this->view->total_cells = $total_cells = (floor($last_day_of_month/7)+1)*7;   

      //GET CURRENT MONTH THAT HAS TO BE DISPLAYED
      $this->view->current_month = $current_month = date("m", $date);

      //GET THE TEXT OF THE CURRENT MONTH
      $this->view->current_month_text = $current_month_text = date("F", $date);

      //GET THE YEAR OF THE CURRENT MONTHS		
      $this->view->current_year = $current_month_text = date("Y", $date);

      // get the base url
      $this->view->sugg_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
    }
  }
?> 
