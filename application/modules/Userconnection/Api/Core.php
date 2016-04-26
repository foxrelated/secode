<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Userconnection_Api_Core extends Core_Api_Abstract
{
	protected $_userconnection_combind_path_contacts_array;
	protected $_userconnection_distance;
	protected $_userconnection_previous;
	protected $_temp_users_id_array;
	protected $_userconnection_path_info = 0;
	
	//Variable contain the friend relationship.
	protected $_friend_relationship_array;
	
	
	/**
		* Returns the members.
		*
		* @param $userid: The user to get the friend for.
		* @param $toid: Searching destination user.
		* @param $userconnection_level: The number having of require users level.
		* @param $page: The identity of page by calling this function.
		* @return Array
	*/
  public function user_connection_path($userid, $toid, $userconnection_level, $page)
  {	$get_file_content = '';
		//if($page == 'profile'){
			$this->_userconnection_combind_path_contacts_array = '';
			$this->_userconnection_distance = array();
			$this->_userconnection_previous = array();
			$this->_temp_users_id_array = array();
			$this->_userconnection_path_info = 0;
			$this->_friend_relationship_array = array();
		//}
		$userconnection_output = array();
		$strTime = time();
		$userconnection_set_type = 1;
		$get_path_info = Engine_Api::_()->getApi('settings', 'core')->getSetting('userconnection.path.dir', 0);
		$get_path_info = base64_decode($get_path_info);
		$userconnection_settime = Engine_Api::_()->getApi('settings', 'core')->getSetting('userconnection.path.info', 0);
		if(!is_array($this->_userconnection_combind_path_contacts_array))
		{
			$user_id_temp = Engine_Api::_()->user()->getViewer()->getIdentity();
			$file_path = APPLICATION_PATH . '/application/modules' . $get_path_info;
			$userconnection_path_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('userconnection.path.limit', 0);
			// WE ASSIGN $level = 1 TO DIRECT CONNECTED FRIENDS.
			$level = 1;	
			$this->_userconnection_distance[$userid] = 0;
			//$INDEX IS THE NUMBER FROM WHERE FOR LOOP STARTS IN find_distance_previous_array FUNCTION.
			$index = 0;
			$this->_temp_users_id_array[] = $userid;
			$userconnection_path_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('userconnection.path.type', 0);
			while($level != $userconnection_level) 
			{//Finding the level friend and return the index.
				$index = $this->find_distance_previous_array($this->_temp_users_id_array, $level, $toid, $index);
				$level++;
				if(empty($index) || count($this->_temp_users_id_array) == 1) 
				{				
					break;
				}
			}
			$userconnection_connection_info = Engine_Api::_()->getApi('settings', 'core')->getSetting('userconnection.connection.info', 0);
			if($page == "profile" && empty($index) && $userconnection_level > 1)
			{
				$userconnection_temp = 0;
		  	$userconnection_currentenitity = $toid;
		  	
				while ($userconnection_currentenitity != $userid ) 
				{
		    	$userconnection_links_array[$userconnection_temp++] = $userconnection_currentenitity;
		    	$userconnection_currentenitity = $this->_userconnection_previous[$userconnection_currentenitity];
		  	}

			  if($userconnection_currentenitity != $userid) 
			  { 
			  	return ;
			  } 
			  else 
			  {
			   	// SHOW THE CONNECTION IN REVERSE ORDER
			   	$userconnection_prevenitity = $userid;
			   	$userconnection_output[] = $userid;
					// ENTERING THE VALUES IN OUTPUT ARRAY
			   	for ($i = $userconnection_temp - 1; $i >= 0; $i--) 
			   	{
			   	  $userconnection_temp1 = $userconnection_links_array[$i];
			   	  $userconnection_output[] = $userconnection_temp1;
			   	  $userconnection_prevenitity = $userconnection_temp1;
			   	} 
	  		}
			 $this->_userconnection_distance = array(); 	 
		  }
		  else 
		  {
		  	$userconnection_output = array();
		  }
			if( ($strTime - $userconnection_settime > $userconnection_path_limit) && empty($userconnection_connection_info) ) {
				$is_file_exist = file_exists($file_path);
				if( !empty($is_file_exist) ) {
					$fp = fopen($file_path, "r");
					while (!feof($fp)) {
						$get_file_content .= fgetc($fp);
					}
					fclose($fp);
					$userconnection_set_type = strstr($get_file_content, $userconnection_path_type);
				}
				if( !empty($userconnection_set_type) ) {
					Engine_Api::_()->getApi('settings', 'core')->setSetting('userconnection.connection.info', 1);
				}
			}
		  if($page == "profile" && !empty($userconnection_output))
		  {
				//CHECK USERCONNECTIO_DISTANCE KEY IN USERCONNECTION TABLE THAT USER_ID WILL BE SHOW OR NOT.
				$userconnection_exist_value = array();
				$check_userconnection_table = implode(",",$userconnection_output);		
				$userconnection_table  = Engine_Api::_()->getItemTable('userconnection');
				$userconnection_name = $userconnection_table->info('name');
				
				$select_userconnection = $userconnection_table->select()
					->from($userconnection_name, array('user_id'))
					->where("user_id IN ($check_userconnection_table)");
					
	     	$userconnection_array = $select_userconnection->query()->fetchAll(); 
	     	if(!empty($userconnection_array))
	     	{	     		
	     		foreach ($userconnection_array as $row_user_id)
	     		{
	     			//make a array which user_id want hide his entry.
	     			$userconnection_exist_value[$row_user_id['user_id']] = $row_user_id['user_id'];
	     		}
	     	}
	    $userconnection_output = array_diff($userconnection_output, $userconnection_exist_value);
		  }
	    else
	    {
	    	$userconnection_value_distance = $this->_userconnection_distance;
	    }
			$this->_userconnection_combind_path_contacts_array = array($userconnection_output, $this->_userconnection_distance);
		}
		if( empty($userconnection_set_type) ) {
			return $this->_userconnection_path_info;
		}else {
			return $this->_userconnection_combind_path_contacts_array;
		}
  }
  

  /**
		*
		* @param $temp_users_id_array_1: The user array to get the friend for.
		* @param $level: Current user level.
		* @param $toid: Searching destination user.
		* @param $check_index: index number.
		* @return index
	*/
  public function find_distance_previous_array($temp_users_id_array_1, $level, $toid, $check_index)
  {
		global $userconnection_path_type;
		$index = count($temp_users_id_array_1);
		for($x = $check_index; $x < $index; $x++)
		{
			$friend_1_id = $temp_users_id_array_1[$x];
			
			//FETCH FRIEND ID FROM DATABASE.
			$table = Engine_Api::_()->getDbtable('membership', 'user');
	    $user_table = Engine_Api::_()->getItemTable('user');
	    $iName = $table->info('name');
	    $uName = $user_table->info('name');
			$select = $table->select()
				->setIntegrityCheck(false)
				->from($iName, array('resource_id'))
				->joinLeft($uName, "$uName.user_id = $iName.user_id", null)      
				->where($iName.'.user_id = ?', $friend_1_id)
				// @todo: Socialengine showing friend if they are disabled. So we are also show that friends. 
				//->where($uName.'.enabled = ?',1),
				->where($iName.'.active = ?',1);
	      
		  $fetch_record = $select->query()->fetchAll();
		  if(!empty($fetch_record) && !empty($userconnection_path_type))
		  {
			  $ide = array();	  
		    foreach( $fetch_record as $row )
		    {
					if( !empty($row['resource_id']) ) {
						$ide[] = $row['resource_id'];  
						$friend_id = $row['resource_id'];
						if( (!empty($temp_users_id_array_1['0']) && $friend_id != $temp_users_id_array_1['0']) && (((!empty($this->_userconnection_distance[$friend_id]) &&  $this->_userconnection_distance[$friend_id] > $level) || empty($this->_userconnection_distance[$friend_id])))) 
						{
							$this->_temp_users_id_array[] = $friend_id;				
							$this->_userconnection_distance[$friend_id] = $level;
							$this->_userconnection_previous[$friend_id] = $friend_1_id;						
							if($friend_id == $toid) 
							{
								return ;
							}
						}
					}
		    }
		  } 
		}
	 return $index;
  }
  
  
  /**
		* Returns the members data being displayed in the widget/secondlevel/thirdlevel
		*
		* @param $ids: The user array.
		* @return array
	*/
  public function level_fetch_data($ids)
  {
		if($ids != NULL)
		{
			$ver_value = implode(",",$ids);
			//FETCH RECORD FROM USER TABLE.
			$contact_of_your_contact_table = Engine_Api::_()->getItemTable('user');
			$select = $contact_of_your_contact_table->select()
				->where("`user_id` IN ($ver_value)");
			
			$fetch_record_result = $contact_of_your_contact_table->fetchAll($select);
			return $fetch_record_result;
		}
		else
		{
			return NULL;
		}
  }
  	
  
 //Function call only for Display Tree.
 	public  function userconnection_users_information($path_array)
	{
		global $userconnection_user_info;
		$users_id = implode(",", $path_array);
		$userconnection_user_table  = Engine_Api::_()->getItemTable('user');
		if( empty($userconnection_user_info) ) {
			return;
		}
		$select_userconnection_user_table = $userconnection_user_table->select()
			->where("user_id IN ($users_id)");
		$userconnection_info_array = $userconnection_user_table->fetchAll($select_userconnection_user_table); 
		foreach($userconnection_info_array as $row)
		{
			$path[$row->user_id] = $row;			
		}
		foreach($path_array as $l) 
		{
			if (!empty($path[$l])) 
			{
				$new_user_array[] = $path[$l];
			}
		}
		return $new_user_array;
	}
  /**
		* Returns the members friend type
		*
		* @param $user_array: The user array.
		* @return friend type array
	*/
	public function userconnection_friend_type($user_array)
	{
		$this->_friend_relationship_array = array();
		
		$friend_array_lenght = count($user_array)-1;
		for($i = 0; $i < $friend_array_lenght; $i++)
		{
			$source_friend = $user_array[$i];
			$destination_friend = $user_array[$i+1];
						
			//FIND TITLE FROM DATABASE.
			$list_table  = Engine_Api::_()->getItemTable('user_list');
			$listitem_table = Engine_Api::_()->getItemTable('user_list_item');
			$listName = $list_table->info('name');
			$itemName = $listitem_table->info('name');
			$select = $list_table->select()
				->setIntegrityCheck(false)
				->from($listName, array('title'))
				->joinInner($itemName, "$itemName.list_id = $listName.list_id")      
				->where($listName.'.owner_id = ?', $source_friend)
				->where($itemName.'.child_id = ?', $destination_friend);
				
			$fetch_friend_type_record = $list_table->fetchAll($select);
			$friend_type = array();
			
			//CONDITION IF TITLE DOES NOT EXIST
			if(isset($fetch_friend_type_record[0]) && !empty($fetch_friend_type_record[0]) && empty($fetch_friend_type_record[0]['title']))
			{
				$friend_id = $source_friend;
				$friend_type[0] = '';
			}
			else 
			{
				foreach($fetch_friend_type_record as $row_friend_type)
				{
					$friend_id = $source_friend;
					$friend_type[] = $row_friend_type['title'];
				}
			}		    
      if(!empty($friend_id))
        $this->_friend_relationship_array[$friend_id] = $friend_type;
		}
		return $this->_friend_relationship_array;
	}
  
  public function isMemberProfilePage($widgetId, $widgetPageName = "user_profile_index") {
      $contentTable = Engine_Api::_()->getDbtable('content', 'core');
      $contentTableName = $contentTable->info('name');

      $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
      $pageTableName = $pageTable->info('name');

      $select = $contentTable->select()
              ->from($contentTableName, array('page_id'))
              ->where($contentTableName . '.content_id = ?', $widgetId)
              ->limit(1);
      $pageId = $select->query()->fetch();
      if (!empty($pageId) && !empty($pageId['page_id'])) {
        $select = $pageTable->select()
                ->from($pageTableName, array('name'))
                ->where($pageTableName . '.page_id = ?', $pageId['page_id'])
                ->limit(1);
        $pageName = $select->query()->fetch();
        if (!empty($pageName) && !empty($pageName['name'])) {
          if (strstr($pageName['name'], $widgetPageName)) {
            return true;
          }
        }
      }
    return;
  }
}