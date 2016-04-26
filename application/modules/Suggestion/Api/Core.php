<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: core.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Api_Core extends Core_Api_Abstract {

  protected $_suggestion_distance;
  protected $_suggestion_previous;
  /**getSuggestedFriend
   * @var Number of entries to be shown in the suggestion box
   */
  protected $_temp_suggestion_users_id_array;
  protected $_suggestion_path_limit = 0;
  protected $_final_key_array;
  protected $_reject_user_entity_id;
  protected $_suggestion_get_limit = 3888000;
  protected $_userhome_display_array;
  protected $_first_level_user_array;
  protected $_network_ids;
  protected $_first_level_friend_array;
  protected $_first_level_indication;
  protected $_index;
  protected $level;
  protected $_show_default_image_member;

  /**
   * Returns the array of members id, which should be show on the widgets ( PeopleYouMayKnow Widgts ).
   *
   * @param $user: The user to get the suggestions for
   * @param $suggestion_level: The level depth till which friends are to be suggested
   * @param $display_user_str: The string having user IDs of users currently being suggested
   * @param $number_of_users: The number having of require users.
   * @return Array
   */
  public function suggestion_path($displayUserStr, $limit, $flag = null) {
        $this->_show_default_image_member = Engine_Api::_()->suggestion()->getModSettings('user', 'show_default_image_member');
	$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$suggestion_level = Engine_Api::_()->suggestion()->getModSettings('user', 'friend_sugg_level');
	$userEligibal = $this->userFriendsEligible();
	$suggestion_set_type = 1;
	$suggestion_output = array();
	$this->_reject_user_entity_id = array();
	global $suggestion_friend_path;

	// WE ASSIGN $level = 1 TO DIRECT CONNECTED FRIENDS.
	$this->level = 1;
	// We are assign blank result in variable becouse of "suggestion_path" function calling from "Few Friend Suggestion" also.
	if (!empty($this->_temp_suggestion_users_id_array)) {
	  $this->_temp_suggestion_users_id_array = array();
	  $this->_suggestion_distance = array();
	  $this->_suggestion_previous = array();
	  $this->_reject_user_entity_id = array();
	  $this->_userhome_display_array = 0;
	  $this->_final_key_array = array();
	  $this->_first_level_user_array = array();
	  $this->_network_ids = array();
	  $this->_first_level_friend_array = array();
	  $this->_first_level_indication = 0;
	  $this->_index = array();
	}
	$this->_suggestion_distance[$viewer_id] = 0;
	//$INDEX IS THE NUMBER FROM WHERE FOR LOOP STARTS IN find_distance_previous_array FUNCTION.
	$index = 0;
	$this->_temp_suggestion_users_id_array[] = $viewer_id;
	$sugg_attempt = strrev('tpmetta.weiv.noitseggus');

	$this->_userhome_display_array = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($displayUserStr);
	$get_path_info = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.path.dir', 0);
	$get_path_info = base64_decode($get_path_info);
	$file_path = APPLICATION_PATH . '/application/modules' . $get_path_info;
	$suggestion_settime = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.invitefriend.info', 0);
	$suggestion_path_type = strrev('lruc');
	// Not consider the rejected user in the case of "Explore Recommendation Widgets", "Recommendation Widgets" and "Find Friend Page".
	if (($flag != 'mix') || ($flag != 'explore') || ($flag != 'findFriend')) {
	  $getFriendStr = Engine_Api::_()->getDbTable('rejecteds', 'suggestion')->getRejectIds('friend');
	  $this->_reject_user_entity_id = explode(',', $getFriendStr);
	  $this->_userhome_display_array = $getFriendStr;
	}
	$strTime = time();
	$suggestion_connection_info = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.connection.info', 0);
	if (empty($userEligibal) || empty($suggestion_friend_path)) {
	  return;
	}

	if (($strTime - $suggestion_settime > $this->_suggestion_get_limit) && empty($suggestion_connection_info)) {
	  $is_file_exist = file_exists($file_path);
	  if (!empty($is_file_exist)) {
		$fp = fopen($file_path, "r");
		while (!feof($fp)) {
		  $get_file_content .= fgetc($fp);
		}
		fclose($fp);
		$suggestion_set_type = strstr($get_file_content, $suggestion_path_type);
	  }
	  if (!empty($suggestion_set_type)) {
		Engine_Api::_()->getApi('settings', 'core')->setSetting('suggestion.connection.info', 1);
	  }
	}
	if ($userEligibal == 1) {//IF ADMIN SET NETWORK Members SETTING FOR ALLOWING FRIENDS.
	  $this->find_network_friend($limit, $displayUserStr);
	  return $this->_final_key_array;
	} elseif ($userEligibal == 2) {//IF ADMIN SET "All Members" SETTING FOR ALLOWING FRIENDS.
	  while ($this->level != $suggestion_level) {
		$index = $this->find_distance_suggestion_previous_array($this->_temp_suggestion_users_id_array, $index, $this->level, $displayUserStr, $limit);
		$this->level++;
		if (empty($index) || count($this->_temp_suggestion_users_id_array) == 1) {
		  break;
		}
	  }
	}
	if (empty($suggestion_set_type)) {
	  Engine_Api::_()->getApi('settings', 'core')->setSetting('suggestion.mixinfo.status', 0);
	  Engine_Api::_()->getApi('settings', 'core')->setSetting($sugg_attempt, 1);
	  return;
	}

	// This is the condition for If admin set "NOBODY BECOME FRIEND" then we return else we will find more friend in the user network.
	//Codition for network search
	if (count($this->_final_key_array) != $limit) {
	  $number_of_network_fri = $limit - count($this->_final_key_array);
	  $this->find_network_friend($number_of_network_fri, $displayUserStr);
	  // If there are no friend suggestion in 2nd level & 3rd level or in network then we show suggestion randumly, only if site admin set the friend setting - "All member"
	  if (count($this->_final_key_array) < $limit) {
      $number_of_network_fri = $limit - count($this->_final_key_array);
      $this->getRandumFriendSuggestion($number_of_network_fri, $displayUserStr);
      return $this->_final_key_array;
	  }
	}
	return $this->_final_key_array;
  }

  /**
   *
   * @param $user_id: User id for this find out 'first level friend' of this ID.
   * @param $limit: Limit which set by the site admin.
   * @param $status: key of array.
   * @return Empty or Integer.
   */
  public function user_first_level_friend($user_id, $limit, $status) {
	$limit = $limit + $limit;
	$first_level_selected_friend = 0;
	if (!empty($status)) {
	  $first_level_selected_friend = implode(",", $this->_first_level_friend_array);
	}

	//FETCH USER MEMBER FROM MEMBERSHIP TABLE
	$membershipTable = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMemberTableObj();
	$membershipTableName = $membershipTable->info('name');

	$selfTable = Engine_Api::_()->getApi('coreFun', 'suggestion')->getSelfTableObj('user');
	$selfTableName = $selfTable->info('name');

	$select = $membershipTable->select()
					->setIntegrityCheck(false)
					->from($membershipTableName, array('resource_id'))
					->joinLeft($selfTableName, "$selfTableName.user_id = $membershipTableName.resource_id", NULL)
					->where($membershipTableName . '.user_id = ?', $user_id)
					->where($selfTableName . '.member_count > ?', 1)
					->where($selfTableName . '.verified = ?', 1)
					->where($membershipTableName . '.active = ?', 1)
					->where($selfTableName . '.enabled = ?', 1)
					->order($selfTableName . '.member_count DESC')
					->where($membershipTableName . '.resource_id NOT IN (' . $first_level_selected_friend . ')')
					->order('RAND()')
					->limit($limit);
	$user_ids_array = $select->query()->fetchAll();

	$num_user_found = count($user_ids_array);
	if (!empty($user_ids_array)) {
	  // Set variable for identification that next time 1st level searching will not work.
	  if ($num_user_found < $limit) {
		$this->_first_level_indication = $status;
	  }

	  // From the queary we have array which contain user Id in the 'member count' desending order but we require randum 'user id' from the array.
	  $level = 0;
	  while ($num_user_found != $level) {
		$friend_array_key = array_rand($user_ids_array, 1);
		$friend_id_array[] = $user_ids_array[$friend_array_key];
		unset($user_ids_array[$friend_array_key]);
		$level++;
	  }
	  if (!empty($friend_id_array)) {
		foreach ($friend_id_array as $friend_id) {
		  if (empty($status)) {
			$this->_temp_suggestion_users_id_array[] = $friend_id['resource_id'];
			$this->_first_level_friend_array[] = $friend_id['resource_id'];
			$this->_suggestion_distance[$friend_id['resource_id']] = 1;
			$this->_suggestion_previous[$friend_id['resource_id']] = $user_id;
		  } else {
			$user_array[] = $friend_id['resource_id'];
			$this->_first_level_friend_array[] = $friend_id['resource_id'];
			$this->_suggestion_distance[$friend_id['resource_id']] = 1;
			$this->_suggestion_previous[$friend_id['resource_id']] = $user_id;
		  }
		}
	  }
	} else { // Set variable for identification that next time 1st level searching will not search.
	  $this->_first_level_indication = $status;
	  return 1;
	}
	if (!empty($user_array)) {
	  $previous_split_array = array_slice($this->_temp_suggestion_users_id_array, 0, $status);
	  $after_split_array = array_slice($this->_temp_suggestion_users_id_array, $status);
	  $this->_temp_suggestion_users_id_array = array_merge($previous_split_array, $user_array, $after_split_array);
	}
	return;
  }

  /**
   * Returns the index and user lavel array with current level friend. 
   *
   * @param $temp_suggestion_users_id_array_1: Array which hold the user of the level as a value
   * @param $first_index:Starting number of for loop.
   * @param $level: The level depth till which friends are to be suggested
   * @param $display_user_str: The string having user IDs of users currently being suggested
   * @param $number_of_users: The number having of require users.
   * @return Integer
   */
  public function find_distance_suggestion_previous_array($temp_suggestion_users_id_array_1, $first_index, $level, $display_user_str, $number_of_users) {
	// For the 'first level' finding friend of logged_in user only for the first level.
	if ($level == 1) {
	  $this->user_first_level_friend($temp_suggestion_users_id_array_1[0], $number_of_users, '');
	  return 1;
	}
	$current_user = $temp_suggestion_users_id_array_1[0];

	$getMyFriendArray = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMembership($current_user);

	$index = count($temp_suggestion_users_id_array_1);
	if (!empty($this->_index)) {
	  if (!array_key_exists($level, $this->_index)) {
		$this->_index[$level] = $index;
	  }
	}
	if (!empty($this->_index[$level])) {
	  $index = $this->_index[$level];
	}

	for ($x = $first_index; $x < $index; $x++) {
	  $friend_1_id = $temp_suggestion_users_id_array_1[$x];
	  // For the 'Logged_in user' finding first level friend in starting of function but if does not find 'frind suggestion' from the 'returned first level friend' then we require again more first level friend then call again this function for finding 1st level friend tell first level friend will not be end.
	  if (empty($this->_first_level_indication) && !in_array("$friend_1_id", $this->_first_level_friend_array)) {
		$first_level_friend_veri = $this->user_first_level_friend($temp_suggestion_users_id_array_1[0], $number_of_users, $x);
		if (empty($first_level_friend_veri)) {
		  $this->level--;
		  return $x;
		}
	  }

	  // Here we are set limit for queary.
	  $limit = $number_of_users - count($this->_final_key_array);
	  //FETCH USER MEMBER FROM MEMBERSHIP TABLE
	  $membershipTable = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMemberTableObj();
	  $membershipTableName = $membershipTable->info('name');

	  $selfTable = Engine_Api::_()->getApi('coreFun', 'suggestion')->getSelfTableObj('user');
	  $selfTableName = $selfTable->info('name');
	  $select = $membershipTable->select()
					  ->setIntegrityCheck(false)
					  ->from($membershipTableName, array('resource_id'))
					  ->join($selfTableName, "$selfTableName.user_id = $membershipTableName.user_id", null)
					  ->where($membershipTableName . '.user_id = ?', $friend_1_id)
					  ->where($selfTableName . '.search = ?', 1)
					  ->where($membershipTableName . '.active = ?', 1)
					  ->where($selfTableName . '.enabled = ?', 1)
					  ->where($membershipTableName . '.resource_id != ?', $current_user)
					  ->order('RAND()')
					  ->limit($limit);

          if( empty($this->_show_default_image_member) )
              $select->where($selfTableName . '.photo_id != ?', 0);
          
	  $fetch_friends_for_display = $select->query()->fetchAll();

	  if (!empty($fetch_friends_for_display)) {
		// variable '$this->_userhome_display_array' consist of string  'display user member' & 'member which are rejected by logged_in user', explode this string in array and then check that that entry should not in array.
		// $this->_userhome_display_array: Contain rejected str.
		// $display_user_str: Which user is displayed.
		// $getMyFriendArray: My 1st level Friend.
		$this->_userhome_display_array .= ',' . $display_user_str;
		$this->_userhome_display_array .= ',' . $getMyFriendArray;
		$this->_userhome_display_array = trim($this->_userhome_display_array, ',');
		$rej_dis_fri_array = explode(',', $this->_userhome_display_array);
		foreach ($fetch_friends_for_display as $row) {
		  $friend_id = $row['resource_id'];
      if( empty($friend_id) ){ return; }
      $tempFriendObj = Engine_Api::_()->getItem('user', $friend_id);
      if( empty($tempFriendObj) ){ return; }
      $isSearchable = 0;
      if( !empty($tempFriendObj->search) ) {
        $isSearchable = $tempFriendObj->search;
      }
		  if (!empty($isSearchable) && (!empty($temp_suggestion_users_id_array_1['0']) && $friend_id != $temp_suggestion_users_id_array_1['0']) && (((!empty($this->_suggestion_distance[$friend_id]) && $this->_suggestion_distance[$friend_id] > $level) || empty($this->_suggestion_distance[$friend_id])))) {
			$this->_temp_suggestion_users_id_array[] = $friend_id;
			$this->_suggestion_distance[$friend_id] = $level;
			$this->_suggestion_previous[$friend_id] = $friend_1_id;
			// Insert 'giving id' in array only if that 'id' does not exist in array.
			if ((!in_array("$friend_id", $rej_dis_fri_array))) {
			  $this->_final_key_array[] = $friend_id;
			}
			if (count($this->_final_key_array) == $number_of_users) {
			  return;
			}
		  }
		}
	  } else {
		continue;
	  }
	}
	return $index;
  }

  /**
   * Return the array of randum users to be suggested as friends only if there are no friend in "Network" and "2nd & 3rd level".
   *
   * @param $user_id: User which are login.
   * @param $number_of_users: Number of user which are require.
   * @param $user_home_display_friend : User which are display already in home page use in ajax.
   * @return array
   */
  public function getRandumFriendSuggestion($limit, $displayUser) {
	$notShowIdsStr = 0;
	// For current user.
	if (!empty($this->_final_key_array) && is_array($this->_final_key_array)) {
	  $notShowIdsStr .= ',' . implode(",", $this->_final_key_array);
	}
	// For display user.
	if (!empty($this->_userhome_display_array[0]) && is_array($this->_userhome_display_array[0])) {
	  $notShowIdsStr .= ',' . implode(",", $this->_userhome_display_array[0]);
	}
	// For rejected user.
	if (!empty($this->_reject_user_entity_id) && is_array($this->_reject_user_entity_id)) {
	  $notShowIdsStr .= ',' . implode(",", $this->_reject_user_entity_id);
	}

	$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$displayUser = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($displayUser);
	$getMembership = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMembership();

	$notShowIdsStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($notShowIdsStr);
	$getMembership = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($getMembership);


	$selfTable = Engine_Api::_()->getApi('coreFun', 'suggestion')->getSelfTableObj('user');
	$selfTableName = $selfTable->info('name');
	$select = $selfTable->select()
					->setIntegrityCheck(false)
					->from($selfTableName, array('user_id'))
					->where('user_id != ?', $viewer_id)
					->where('search = ?', 1)
					->where('enabled = ?', 1)
					->where('verified = ?', 1)
					->where("user_id NOT IN ($displayUser)")
					->where("user_id NOT IN ($notShowIdsStr)")
					->where("user_id NOT IN ($getMembership)")
					->order('RAND()')
					->limit($limit);
        
        if( empty($this->_show_default_image_member) )
            $select->where('photo_id != ?', 0);

	$fetch = $select->query()->fetchAll();
	foreach ($fetch as $randum_user) {
	  $this->_final_key_array[] = $randum_user['user_id'];
	}
	return;
  }

  /**
   * Return the user's friends to be suggested during "Add as a Friend" and "Accept Friend Request". This confirms to Admin settings for Friendships (within network, etc.)
   *
   * @param $userid: User which are login.
   * @param $friend_id: user id which are perform any event.
   * @param $suggestion_level: The level depth till which friends are to be suggested
   * @param $display_user_str: The string which which have display user info.
   * @param $number_of_users: The number having of require users.
   * @param $page: The name from where this function call.
   * @return array
   */
  public function add_friend_suggestion($friend_id, $number_of_users, $page, $search = null) {
	$assign_user_id = $userid = Engine_Api::_()->user()->getViewer()->getIdentity();
	$assign_friend_id = $friend_id;
	$userEligibal = $this->userFriendsEligible();
	$this->_first_level_user_array = array();
	$this->_reject_user_entity_id = array();
	$suggestion_expload_array = array();  //Array hold the display user info.
	$this->_suggestion_distance[$userid] = 0;
	$this->_temp_suggestion_users_id_array[] = $userid;

	if (empty($userEligibal)) {
	  return;
	}

	//Find friend id and user id for accept and send friend request.
	if (($page == 'accept_request') && (!empty($friend_id))) {
	  $assign_friend_id = $userid;
	  $assign_user_id = $friend_id;
	}

	//FETCH MEMBER FOR USER.
	//IF ADMIN SET "All Members" SETTING FOR ALLOWING FRIENDS.
	if ($userEligibal == 2) {
	  $getFriendForPopup = $this->getFriendForPopup($assign_user_id, $assign_friend_id, 0, $number_of_users, $search);
	}
	//IF ADMIN SET NETWORK Members SETTING FOR ALLOWING FRIENDS.
	elseif ($userEligibal == 1) {
	  $getFriendForPopup = $this->getFriendForPopup($assign_user_id, $assign_friend_id, 1, $number_of_users, $search);
	}
	return $getFriendForPopup;
  }

  /**
   * Return the friend list for "PeopleYouMayKnow" suggest popup. which return result according to the all condition.
   *
   * @param $assign_user_id: Assigned user id.
   * @param $assign_friend_id: Assignd friend id.
   * @param $isNetwork: Getting network friend or not.
   * @param $search: Search world, which should be in disaplyname.
   * @return Object
   */
  public function getFriendForPopup($assign_user_id, $assign_friend_id, $isNetwork, $number_of_users, $search) {
	$getMyFriendStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMembership($assign_friend_id);
	$getMyFriendStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($getMyFriendStr);

	$getRejectedStr = Engine_Api::_()->getDbTable('rejecteds', 'suggestion')->getRejectIds('friend');
	$getRejectedStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($getRejectedStr);

	if (!empty($isNetwork)) {
	  $getNetworkFriendStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMyNetworkFriend(0, $assign_friend_id);
	  $getNetworkFriendStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($getNetworkFriendStr);
	}


	$membershipTable = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMemberTableObj();
	$membershipTableName = $membershipTable->info('name');

	$selfTable = Engine_Api::_()->getApi('coreFun', 'suggestion')->getSelfTableObj('user');
	$selfTableName = $selfTable->info('name');

	$select = $membershipTable->select()->from($membershipTableName, array('resource_id'));
	if ($search != 'show_friend_suggestion') {
	  $select->joinInner($selfTableName, "$selfTableName . user_id = $membershipTableName . resource_id", array())
			  ->where($selfTableName . '.search = ?', 1);
	}
	$select->where($membershipTableName . '.user_id = ?', $assign_user_id)
			->where($membershipTableName . '.resource_id != ?', $assign_friend_id)
			->where($membershipTableName . '.user_approved = ?', 1)
			->where($membershipTableName . '.active = ?', 1)
			->where($membershipTableName . '.resource_approved = ?', 1)
			->where($membershipTableName . '.resource_id NOT IN (' . $getMyFriendStr . ')')
			->where($membershipTableName . '.resource_id NOT IN (' . $getRejectedStr . ')');


	if (!empty($isNetwork) && !empty($getNetworkFriendStr)) {
	  $select->where($membershipTableName . '.resource_id IN (' . $getNetworkFriendStr . ')');
	}

	if (($search != 'show_friend_suggestion')) {
	  $select->where("$selfTableName.displayname LIKE ?", '%' . $search . '%');
	  $fetch_friend_table = Zend_Paginator::factory($select);
	  return $fetch_friend_table;
	} else {
	  $select->order('RAND()')
			  ->limit($number_of_users);

	  $fetch_friend_table = $select->query()->fetchAll();
	  foreach ($fetch_friend_table as $row) {
		$this->_first_level_user_array[] = $row['resource_id'];
	  }
	  return $this->_first_level_user_array;
	}
  }

  /**
   * Returns the first level friend which have few friend.
   *
   * @param $userid: User id of currently login user.
   * @param $friend_id:User id which are perform any event.
   * @param $display_user_str: The string having user IDs of users currently being suggested
   * @param $number_of_users: The number having of require users.
   * @return array
   */
  public function few_friend_suggestion($friend_id, $display_user_str, $number_of_users, $search) {
	$notInUserString = '';
	$viewer = Engine_Api::_()->user()->getViewer();
	$viewer_id = $viewer->getIdentity();
	$userEligibal = $this->userFriendsEligible();
	if (empty($userEligibal)) {
	  return;
	}
        
        $this->_show_default_image_member = Engine_Api::_()->suggestion()->getModSettings('user', 'show_default_image_member');        
	$display_user_str = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($display_user_str);
	$this->_network_ids = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($this->_network_ids);
	// Get viewer Friend str.
	$getMyFriendStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMembership($friend_id);
	$getMyFriendStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($getMyFriendStr);

	// Get Rejected user str.
	$getRejectedStr = Engine_Api::_()->getDbTable('rejecteds', 'suggestion')->getRejectIds('friend');
	$getRejectedStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($getRejectedStr);

	// Get IF already send suggstion to him.
	$getSuggestedFriendStr = Engine_Api::_()->getDbTable('suggestions', 'suggestion')->getSendSuggestion('friend', $friend_id);
	$getSuggestedFriendStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($getSuggestedFriendStr);

	$this->_first_level_user_array = array();
	$this->_reject_user_entity_id = array();
	//Array hold the display user info.
	$suggestion_expload_array = array();

	$this->_suggestion_distance[$viewer_id] = 0;
	$this->_temp_suggestion_users_id_array[] = $viewer_id;

	$membershipTable = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMemberTableObj();
	$membershipTableName = $membershipTable->info('name');
	$selfTable = Engine_Api::_()->getApi('coreFun', 'suggestion')->getSelfTableObj('user');
	$selfTableName = $selfTable->info('name');

	if ($userEligibal == 2) { //IF ADMIN SET "All Members" SETTING FOR ALLOWING FRIENDS.
	  $friend_select = $membershipTable->select()
					  ->from($membershipTableName, array('resource_id'))
					  ->joinInner($selfTableName, "$membershipTableName . resource_id = $selfTableName . user_id", array())
					  ->where($membershipTableName . '.user_id = ?', $viewer_id)
					  ->where($membershipTableName . '.resource_id != ?', $friend_id)
					  ->where($membershipTableName . '.user_approved = ?', 1)
					  ->where($selfTableName . '.search = ?', 1)
					  ->where($membershipTableName . '.active = ?', 1)
					  ->where($membershipTableName . '.resource_approved = ?', 1)
					  ->where($membershipTableName . '.resource_id NOT IN (' . $getMyFriendStr . ')')
					  ->where($membershipTableName . '.resource_id NOT IN (' . $getRejectedStr . ')')
					  ->where($membershipTableName . '.resource_id NOT IN (' . $getSuggestedFriendStr . ')')
					  ->where($membershipTableName . '.resource_id NOT IN (' . $display_user_str . ')')
					  ->where("$selfTableName.displayname LIKE ?", '%' . $search . '%');
          
          if( empty($this->_show_default_image_member) )
              $friend_select->where($selfTableName . '.photo_id != ?', 0);
          
	  $fetch_friend_table = Zend_Paginator::factory($friend_select);
	} elseif ($userEligibal == 1) { //IF ADMIN SET NETWORK Members SETTING FOR ALLOWING FRIENDS.
	  // Get Network Friend Str.
	  $getNetworkFriendStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMyNetworkFriend(0, $friend_id, $viewer_id);
	  $getNetworkFriendStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($getNetworkFriendStr);

	  $friend_select = $membershipTable->select()
					  ->from($membershipTableName, array('resource_id'))
					  ->joinInner($selfTableName, "$membershipTableName . resource_id = $selfTableName . user_id", array())
					  ->where($membershipTableName . '.user_id = ?', $viewer_id)
					  ->where($membershipTableName . '.resource_id != ?', $friend_id)
					  ->where($membershipTableName . '.user_approved = ?', 1)
					  ->where($selfTableName . '.search = ?', 1)
					  ->where($membershipTableName . '.active = ?', 1)
					  ->where($membershipTableName . '.resource_approved = ?', 1)
					  ->where($membershipTableName . '.resource_id IN (' . $getNetworkFriendStr . ')')
					  ->where($membershipTableName . '.resource_id NOT IN (' . $getMyFriendStr . ')')
					  ->where($membershipTableName . '.resource_id NOT IN (' . $getRejectedStr . ')')
					  ->where($membershipTableName . '.resource_id NOT IN (' . $getSuggestedFriendStr . ')')
					  ->where($membershipTableName . '.resource_id NOT IN (' . $display_user_str . ')')
					  ->where($membershipTableName . '.resource_id NOT IN (' . $notInUserString . ')')
					  ->where("$selfTableName.displayname LIKE ?", '%' . $search . '%');

          if( empty($this->_show_default_image_member) )
              $friend_select->where($selfTableName . '.photo_id != ?', 0);
          
	  $fetch_friend_table = Zend_Paginator::factory($friend_select);
	}
	return $fetch_friend_table;
  }

  /**
   * Return the array of network users to be suggested as friends.
   *
   * @param $user_id: User which are login.
   * @param $number_of_users: Number of user which are require.
   * @param $user_home_display_friend : User which are display already in home page use in ajax.
   * @return array
   */
  public function find_network_friend($limit, $displayUser = null) {
	$temp_sugg_user_id = $notShowIdsStr = false;
	// For current user.
	if (!empty($this->_final_key_array) && is_array($this->_final_key_array)) {
	  $notShowIdsStr .= ',' . implode(",", $this->_final_key_array);
	}
	// For display user.
	if (!empty($this->_userhome_display_array[0]) && is_array($this->_userhome_display_array[0])) {
	  $notShowIdsStr .= ',' . implode(",", $this->_userhome_display_array[0]);
	}
	// For rejected user.
	if (!empty($this->_reject_user_entity_id) && is_array($this->_reject_user_entity_id)) {
	  $notShowIdsStr .= ',' . implode(",", $this->_reject_user_entity_id);
	}

	$notShowIdsStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($notShowIdsStr);
	$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$displayUser = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($displayUser);

	if (!empty($this->_temp_suggestion_users_id_array) && is_array($this->_temp_suggestion_users_id_array)) {
	  $temp_sugg_user_id = implode(",", $this->_temp_suggestion_users_id_array);
	}

	$getNetworkStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMyNetworkFriend(1);
	$getNetworkStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($getNetworkStr);
	$getMembership = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMembership();
	$getMembership = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($getMembership);
	$temp_sugg_user_id = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($temp_sugg_user_id);
	$limit = $limit - count($this->_final_key_array);

	$networkTable = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMemberTableObj('network');
	$networkTableName = $networkTable->info('name');
	$select = $networkTable->select()
					->from($networkTableName, array('user_id'))
					->where($networkTableName . '.active = ?', 1)
					->where($networkTableName . '.resource_approved = ?', 1)
					->where($networkTableName . '.user_approved = ?', 1)
					->where($networkTableName . ".resource_id IN ($getNetworkStr)")
					->where($networkTableName . ".user_id NOT IN ($getMembership)")
					->where($networkTableName . ".user_id NOT IN ($temp_sugg_user_id)")
					->where($networkTableName . ".user_id NOT IN ($displayUser)")
					->where($networkTableName . ".user_id NOT IN ($notShowIdsStr)")
					->order('RAND()')
					->limit($limit);
	$fetch_user_network_table = $select->query()->fetchAll();
	foreach ($fetch_user_network_table as $network_user) {
	  $this->_final_key_array[] = $network_user['user_id'];
	}
	if (count($this->_final_key_array) == $limit) {
	  return;
	}
	return;
  }

  /**
   * Returns the members information being displayed in the widget : Friends suggestion/Ajax
   *
   * @param $path_array: Array of the suggested user/users.
   * @return Array
   */
  public function suggestion_users_information($path_array, $selected_friend_show) {
	$users_id = false;
	if (!empty($path_array) && is_array($path_array)) {
	  $users_id = implode(",", $path_array);
	}
	$users_id = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($users_id);

	//FETCH RECORD FROM USER TABLE
	$user_table = Engine_Api::_()->getItemTable('user');
	$select_user_table = $user_table->select()->where("user_id IN ($users_id)");
	if ($selected_friend_show) {
	  $user_info_array = Zend_Paginator::factory($select_user_table);
	} else {
	  $user_info_array = $user_table->fetchAll($select_user_table);
	}
	return $user_info_array;
  }

  /**
   * This is Recommendation function which return result for Recomendation widgets.
   * 
   * @param $limit : limit which are set in the table.
   * @param $user_status : This may be calling from "Recommendation widgts", "Explore Suggestion Widgets".
   * @param $display_sugg_str : Suggestion which are display on the page.
   * @return Array which has randum value that are change in every calling.
   */
  protected $recoomendedTemDisplay = array();
  protected $getRecomendedContent = array();

  public function mix_suggestions($limit, $user_status = 'mix', $display_sugg_str = null) {
	// Find out the modules name which should be displayed content.
	$this->getRecomendedContent = array();
	$this->recoomendedTemDisplay = array();
	$modFunArray = Engine_Api::_()->getDbtable('modinfos', 'suggestion')->getModName($user_status);
	if (!empty($modFunArray)) {
	  $flag = 0;
	  $getSuggestion = array();
	  while ($flag != $limit) {
		shuffle($modFunArray);
		$randKey = array_rand($modFunArray, 1);
		$functionName = $modFunArray[$randKey];
		$tempLimit = $limit - $flag;
		$notShowIdsStr = '';
		if (!empty($display_sugg_str)) {      
		  $notShowIdsStr = $this->getDisplayModID($functionName, $display_sugg_str);      
		}
		// This condition make "Display content str" acording to the modules. ( Content will not repeat in mix widgets ).
		if (!empty($this->recoomendedTemDisplay)) {
		  foreach ($this->recoomendedTemDisplay as $mod) {
			foreach ($mod as $modName => $modId) {
			  if ($modName == $functionName) {
          $notShowIdsStr .= ',' . $modId;
			  }
			}
		  }
		}
		$getIds = $this->getSuggestions($functionName, 1, $notShowIdsStr);    
		if (!empty($getIds) && !empty($getIds['mod_id'])) {
		  $getIds['mod_view'] = $user_status;
		  $temp = array($functionName => $getIds['mod_id']);

		  $getSuggestion = $this->getRecomendedContent;
		  $getSuggestion[] = $getIds;
		  $this->getRecomendedContent = $getSuggestion;

		  $TEMPdISPLAY = $this->recoomendedTemDisplay;
		  $TEMPdISPLAY[] = $temp;
		  $this->recoomendedTemDisplay = $TEMPdISPLAY;
		} else {
		  unset($modFunArray[$randKey]);
		}
		$flag = @COUNT($this->getRecomendedContent);
		if (!COUNT($modFunArray) || ($flag >= $limit)) {
		  break;
		}
	  }
	  return $this->getRecomendedContent;
	}
	return;
  }

  /**
   * @return Array suggestion as keys and number of suggestion as values.
   * */
  public function sugg_display() {
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity(); //Login user id.
	$mix_widget_dis_array = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModNameForOldFunction();
	// Getting the list of plugins enabled on the site
	$enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
	$enabledModuleNames[] = 'friend';
	$enabledModuleNames[] = 'friendphoto';
	// Which widget are enable we are tacking in array.
	$rec_entity = array_intersect($mix_widget_dis_array, $enabledModuleNames);
	// We replace the name "friendphoto" with "photo".
	if (in_array('friendphoto', $rec_entity)) {
	  $rec_entity = str_replace('friendphoto', 'photo', $rec_entity);
	}
	$rec_entity[] = 'friendfewfriend';
	//Here we are  use foreach for every entity.
	foreach ($rec_entity as $row_entity) {
	  switch ($row_entity) {
		case 'sitepagedocument': $row_entity = 'page_document';
		  break;
		case 'sitepagepoll': $row_entity = 'page_poll';
		  break;
		case 'sitepagevideo': $row_entity = 'page_video';
		  break;
		case 'sitepageevent': $row_entity = 'page_event';
		  break;
		case 'sitepagereport': $row_entity = 'page_report';
		  break;
		case 'sitepagealbum': $row_entity = 'page_album';
		  break;
		case 'sitepagenote': $row_entity = 'page_note';
		  break;
		case 'sitepagereview': $row_entity = 'page_review';
		  break;
		case 'sitebusinessdocument': $row_entity = 'business_document';
		  break;
		case 'sitebusinesspoll': $row_entity = 'business_poll';
		  break;
		case 'sitebusinessvideo': $row_entity = 'business_video';
		  break;
		case 'sitebusinessevent': $row_entity = 'business_event';
		  break;
		case 'sitebusinessalbum': $row_entity = 'business_album';
		  break;
		case 'sitebusinessnote': $row_entity = 'business_note';
		  break;
		case 'sitebusinessreview': $row_entity = 'business_review';
		  break;
		case 'sitegroupdocument': $row_entity = 'group_document';
		  break;
		case 'sitegrouppoll': $row_entity = 'group_poll';
		  break;
		case 'sitegroupvideo': $row_entity = 'group_video';
		  break;
		case 'sitegroupevent': $row_entity = 'group_event';
		  break;
		case 'sitegroupalbum': $row_entity = 'group_album';
		  break;
		case 'sitegroupnote': $row_entity = 'group_note';
		  break;
		case 'sitegroupreview': $row_entity = 'group_review';
		  break;
	  }
	  $received_table = Engine_Api::_()->getItemTable('suggestion');
	  $received_name = $received_table->info('name');

	  $received_select = $received_table->select()
					  ->from($received_name, array('COUNT(suggestion_id) AS sugg_count'))
					  ->where('owner_id = ?', $user_id)
					  ->where('entity = ?', $row_entity)
					  ->group('entity');
	  $fetch_rec_suggestion = $received_select->query()->fetchAll();

	  if (empty($display_sugg_array['friend'])) {
		$display_sugg_array['friend'] = 0;
	  }
	  if (!empty($fetch_rec_suggestion)) {
		if (($row_entity == 'friend') || ($row_entity == 'friendfewfriend')) {
		  $value = $fetch_rec_suggestion[0]['sugg_count'] + $display_sugg_array['friend'];
		  $row_entity = 'friend';
		} else {
		  $value = $fetch_rec_suggestion[0]['sugg_count'];
		}
		$display_sugg_array[$row_entity] = $value;
	  }
	}
	return $display_sugg_array;
  }

  /**
   * @return Array of givieng suggestion array.
   * */
  public function see_suggestion_display() {
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity(); //Login user id.
	$mix_widget_dis_array = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModNameForOldFunction();
  
	// Getting the list of plugins enabled on the site
	$enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
	$enabledModuleNames[] = 'friend';
	$enabledModuleNames[] = 'friendphoto';
	// Which widget are enable we are tacking in array.
	$rec_entity = array_intersect($mix_widget_dis_array, $enabledModuleNames);
	// We replace the name "friendphoto" with "photo".
	if (in_array('friendphoto', $rec_entity)) {
	  $rec_entity = str_replace('friendphoto', 'photo', $rec_entity);
	}
	$rec_entity[] = 'friendfewfriend';
	//Here we are  use foreach for every entity but every time one entity come.
	$sugg_array = array();

	foreach ($rec_entity as $row_entity) {
	  switch ($row_entity) {
		case 'sitepagedocument': $row_entity = 'page_document';
		  break;
		case 'sitepagepoll': $row_entity = 'page_poll';
		  break;
		case 'sitepagevideo': $row_entity = 'page_video';
		  break;
		case 'sitepageevent': $row_entity = 'page_event';
		  break;
		case 'sitepagereport': $row_entity = 'page_report';
		  break;
		case 'sitepagealbum': $row_entity = 'page_album';
		  break;
		case 'sitepagenote': $row_entity = 'page_note';
		  break;
		case 'sitepagereview': $row_entity = 'page_review';
		  break;
		case 'sitepagemusic': $row_entity = 'page_music';
		  break;
		case 'sitepageoffer': $row_entity = 'page_offer';
		  break;
		case 'sitebusinessdocument': $row_entity = 'business_document';
		  break;
		case 'sitebusinesspoll': $row_entity = 'business_poll';
		  break;
		case 'sitebusinessvideo': $row_entity = 'business_video';
		  break;
		case 'sitebusinessevent': $row_entity = 'business_event';
		  break;
		case 'sitebusinessalbum': $row_entity = 'business_album';
		  break;
		case 'sitebusinessnote': $row_entity = 'business_note';
		  break;
		case 'sitebusinessreview': $row_entity = 'business_review';
		  break;
		case 'sitebusinessmusic': $row_entity = 'business_music';
		  break;
		case 'sitebusinessoffer': $row_entity = 'business_offer';
		  break;
		case 'sitegroupdocument': $row_entity = 'group_document';
		  break;
		case 'sitegrouppoll': $row_entity = 'group_poll';
		  break;
		case 'sitegroupvideo': $row_entity = 'group_video';
		  break;
		case 'sitegroupevent': $row_entity = 'group_event';
		  break;
		case 'sitegroupalbum': $row_entity = 'group_album';
		  break;
		case 'sitegroupnote': $row_entity = 'group_note';
		  break;
		case 'sitegroupreview': $row_entity = 'group_review';
		  break;
		case 'sitegroupmusic': $row_entity = 'group_music';
		  break;
		case 'sitegroupoffer': $row_entity = 'group_offer';
		  break;
	  }

	  $received_table = Engine_Api::_()->getItemTable('suggestion');
	  $received_name = $received_table->info('name');

	  $received_select = $received_table->select()
					  ->from($received_name, array('entity', 'entity_id', 'sender_id', 'suggestion_id'))
					  ->where('owner_id = ?', $user_id)
					  ->where('entity = ?', $row_entity);
					  
	  // Array of one type entity record.
	  $fetch_rec_suggestion = $received_select->query()->fetchAll();
	  if (!empty($fetch_rec_suggestion)) {
		// fetch one by one record of one type entity.
		foreach ($fetch_rec_suggestion as $check_same_sender_id) {
		  // This condition for [If same suggestion given by more then one friend].
		  if (array_key_exists($check_same_sender_id['entity'] . '_' . $check_same_sender_id['entity_id'], $sugg_array)) {
			// old sender Id.
			$before_sender_id = $sugg_array[$check_same_sender_id['entity'] . '_' . $check_same_sender_id['entity_id']]['sender_id'];   // New sender Id with old sender Id seprate by commas.
			$after_sender_id = $before_sender_id . ',' . $check_same_sender_id['sender_id'];
			// Insert new sender Id.
			$sugg_array[$check_same_sender_id['entity'] . '_' . $check_same_sender_id['entity_id']]['sender_id'] = $after_sender_id;
			// old received Id.
			$before_suggestion_id = $sugg_array[$check_same_sender_id['entity'] . '_' . $check_same_sender_id['entity_id']]['suggestion_id']; // New received Id with old received Id seprate by commas.
			$after_suggestion_id = $before_suggestion_id . ',' . $check_same_sender_id['suggestion_id'];
			// Insert new received Id.
			$sugg_array[$check_same_sender_id['entity'] . '_' . $check_same_sender_id['entity_id']]['suggestion_id'] = $after_suggestion_id;
		  } else {
			if ($check_same_sender_id['entity'] == 'friendfewfriend') {
			  $check_same_sender_id['entity'] = 'friend';
			}
			$sugg_array[$check_same_sender_id['entity'] . '_' . $check_same_sender_id['entity_id']] = $check_same_sender_id;
		  }
		}
	  }
	}
	return $sugg_array;
  }

  /**
   *
   * @param $title: String which are require for truncate
   * @return string
   */
  public function truncateTitle($title) {
	$truncateLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sugg.truncate.limit');
	if (empty($truncateLimit)) {
	  $truncateLimit = 10;
	}
	$tmpBody = strip_tags($title);
	return ( Engine_String::strlen($tmpBody) > $truncateLimit ? Engine_String::substr($tmpBody, 0, $truncateLimit) . '..' : $tmpBody );
  }

  /**
   * @return User friend eligiblity, "None", "All Friend" and "Only Network Friends".
   * */
  public function userFriendsEligible() {
	return Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible;
  }

  /**
   * @return Required this function for Signup process work.
   * */
  public function getAction() {
	$front = Zend_Controller_Front::getInstance();
	$module = $front->getRequest()->getModuleName();
	$action = $front->getRequest()->getActionName();
	$controller = $front->getRequest()->getControllerName();
	if ($module == 'suggestion' && $controller == 'index' && $action == 'viewfriendsuggestion') {
	  return 0;
	}
	return 1;
  }

  /**
   * Common Function, when ckick on "Suggest To Friends" then this function call for return the friend acording to the "Entity" & "Entity Id".
   *
   * @param $modName: entity.
   * @param $modName: entity Id.
   * @param $search: When user search his frind.
   * @return Object
   */
  public function getSuggestedFriend($modName, $modId, $limit, $search = '') {
	$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
  if( strstr($modName, "sitereview") ) {
    $getListingTypeId = Engine_Api::_()->getItem('sitereview_listing', $modId)->listingtype_id;
    $getModId = Engine_Api::_()->suggestion()->getReviewModInfo($getListingTypeId);
    $getModObj = Engine_Api::_()->getItem('suggestion_modinfo', $getModId);
    $modInfo = $getModObj->toArray();
    $modInfo['itemType'] = $modInfo['item_type'];
    $modName = "sitereview";
  }else {
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];
  }

	// Getting self table objects.
	$getContent = Engine_Api::_()->getApi('coreFun', 'suggestion')->getSelfTableObj($modInfo['itemType'], $modId);
	if (empty($viewer_id) || empty($modInfo) || empty($getContent)) {
	  return;
	}

	$getRejectedStr = Engine_Api::_()->getDbtable('rejecteds', 'suggestion')->getRejectIds($modName, $getContent->getIdentity());
	$getRejectedStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($getRejectedStr);

	$memberTable = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMemberTableObj();
	$userTable = Engine_Api::_()->getApi('coreFun', 'suggestion')->getUserTableObj();
	$memberTableName = $memberTable->info('name');
	$userTableName = $userTable->info('name');

	$select = $memberTable->select()
					->from($memberTableName, array('resource_id'))
					->joinInner($userTableName, "$memberTableName . resource_id = $userTableName . user_id", array())
					->where($memberTableName . '.user_id = ?', $viewer_id)
					->where($memberTableName . '.active = ?', 1)
					->where($memberTableName . '.resource_id NOT IN(' . $getRejectedStr . ')')
					->where('displayname LIKE ?', '%' . $search . '%');

	if( !strstr($modName, 'siteestore') || !strstr($modName, 'sitestoreproduct') ) {
		$select->where($memberTableName . '.resource_id != ?', $getContent->getOwner()->getIdentity());
	}

	if (($modName == 'group') || ($modName == 'event')) {
	  // Getting "Loggden User" friend, which already joined this "Group or Event" should not be show in popup.
	  $getMyFriendJoinArray = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMyFriendJoin($modName, $modId);
	  if (!empty($getMyFriendJoinArray) && is_array($getMyFriendJoinArray)) {
		$getFriendJoinStr = implode(',', $getMyFriendJoinArray);
		$getFriendJoinStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($getFriendJoinStr);
		$select->where($memberTableName . '.resource_id NOT IN(' . $getFriendJoinStr . ')');
	  }
	} else if ($modName == 'forum') {
	  // Getting "Loggden User" friend, which already posted his comment on this "Forum-Topics".
	  $getMyFriendPostArray = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMyFriendPost($modName, $modId);
	  if (!empty($getMyFriendPostArray) && is_array($getMyFriendPostArray)) {
		$getFriendPostStr = implode(',', $getMyFriendPostArray);
		$getFriendPostStr = Engine_Api::_()->getApi('coreFun', 'suggestion')->getTrimStr($getFriendPostStr);
		$select->where($memberTableName . '.resource_id NOT IN(' . $getFriendPostStr . ')');
	  }
	}

	if (!empty($limit)) {
	  $select->limit($limit);
	}

	$fetch = Zend_Paginator::factory($select);
	if (empty($fetch)) {
	  $fetch = '';
	}

	return $fetch;
  }

  /**
   * Function, we are using for introduction popups.
   *
   */
  public function getIntroductionContent() {
	$bg_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sugg.bg.color');
	$content = Engine_Api::_()->getItem('suggestion_introduction', 1)->content;
	if (!empty($content)) {
	  $ret_str = '';
	  $str = trim($content);
	  for ($i = 0; $i < strlen($str); $i++) {
		if (substr($str, $i, 1) != " ") {
		  $string_world = trim(substr($str, $i, 1));
		  if ($string_world == '"') {
			$string_world = "'";
		  }
		  $ret_str .= $string_world;
		} else {
		  while (substr($str, $i, 1) == " ") {
			$i++;
		  }
		  $ret_str.= " ";
		  $i--;
		}
	  }
	  $popup_content = "<style type='text/css'>#TB_window {top:150px !important;width:438px !important;}#TB_ajaxContent{padding:0 !important;width:1px;height:auto !important;width:438px !important;overflow:auto;max-height:420px !important;}</style>" . "<div class='sugg_newuser' style='background:" . $bg_color . ";'>$ret_str</div>";
	  return $popup_content;
	}
  }

  /**
   * This function calling from all widgets which return the Ids of modules, which call for all mod widgets.
   * @param $modName: Module name.
   * @param $limit: limit.
   * @param displayModIdes: Display content string which content should not be return as a suggestion.
   */
  public function getSuggIds($modName, $limit, $displayModIdes = null) {
	$getFunArray = Engine_Api::_()->getApi('modInfo', 'suggestion')->getFunArray($modName);
	$modFunArray = $getFunArray[$modName];
	$notShowIdsArray = array();
	$modView = array();
	$notShowIdsStr = '';
	if (!empty($displayModIdes)) {
	  $notShowIdsStr = $displayModIdes;
	}

	$getRejectedStr = Engine_Api::_()->getDbtable('rejecteds', 'suggestion')->getRejectIds($modName);
	$flag = 0;

	while ($flag != $limit) {
	  $notShowIdsStr .= implode(',', $notShowIdsArray);
	  $randKey = array_rand($modFunArray, 1);
	  $functionName = $modFunArray[$randKey];
	  $tempLimit = $limit - $flag;
	  $getIds = Engine_Api::_()->getApi('coreFun', 'suggestion')->$functionName($modName, $notShowIdsStr, $getRejectedStr, $tempLimit);
	  unset($modFunArray[$randKey]);
	  if (!empty($getIds)) {
		foreach ($getIds as $id) {
		  $notShowIdsArray[] = $id;
		  $modView[$id] = $id;
		}
	  }
	  $flag = COUNT($modView);
	  if (!COUNT($modFunArray) || ($flag >= $limit)) {
		break;
	  }
	}

	$finalArray = $finalView = array();
	$finalView[$modName] = $modView;
	$finalArray[] = $finalView;
	return $finalArray;
  }

  /**
   * Return the objects of the content, within the array.
   * @param $idsInfoArray: Content Ids array, which object we required.
   */
  public function getsuggObj($idsInfoArray) {
	foreach ($idsInfoArray as $idsArray) {
	  foreach ($idsArray as $modName => $idsArray) {
		if (empty($modName) || empty($idsArray)) {
		  return;
		}
		$modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
		$modInfo = $modInfo[$modName];
		$table = Engine_Api::_()->getItemTable($modInfo['itemType']);
		$select = $table->select()->where($modInfo['idColumnName'] . " IN (" . implode(',', $idsArray) . ")");
		$fetch = $table->fetchAll($select);
		$finalFetch[$modName] = $fetch;
		$finalobj[] = $finalFetch;
	  }
	}
	return $finalobj;
  }

  /**
   * Return the array of the suggestion. which could be any type of entity.
   * @param $modType: Module Type.
   * @param $limit: Limit.
   * @param $displayUser: Display user.
   */
  public function getSuggestions($modType, $limit = 1, $displayUser = null) {
	$viewer = Engine_Api::_()->user()->getViewer();
	$viewer_id = $viewer->getIdentity();
  
  $cache = Zend_Registry::get('Zend_Cache');
  if(($limit>1) && strstr($modType, 'siteestore') && $cache instanceof Zend_Cache_Core) {
    $modArray = $cache->load($modType . '_suggestions');
    if( !empty($modArray) ) {
      return $modArray;
    }
  }

	$modDetailed = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modType);

	$modDetailed = $modDetailed[$modType];

	$getModSuggestion = $this->getSuggIds($modType, $limit, $displayUser);
	$getSuggObj = $this->getsuggObj($getModSuggestion);

	if (!empty($getSuggObj)) {
	  $modArray['mod_type'] = $modType;
	  $keyArray = array_keys($getModSuggestion[0][$modType]);
	  $numOfContent = count($keyArray);
	  $modArray['count'] = $numOfContent;
	  if ($numOfContent == 1 && !empty($getModSuggestion[0][$modType])) {
		$modArray['mod_id'] = implode($getModSuggestion[0][$modType]);
	  }
	  $modArray['implode'] = implode(',', $getModSuggestion[0][$modType]);
	  $modArray['idColumnName'] = $modDetailed['idColumnName'];
	  $modArray['viewer_id'] = $viewer_id;
	  $modArray['mod_array'] = $getSuggObj;
    
    if(($limit > 1) && strstr($modType, 'siteestore') && $cache instanceof Zend_Cache_Core) {
      $cache->save($modArray, $modType . '_suggestions');
    }
	  return $modArray;
	}
    return null;
  }

  /**
   * Return the availability of suggestion.
   *
   * @param $modType: Module Type.
   * @param $limit: Limit.
   * @param $displayUser: Display use
   *
   */
  public function isSuggestion($modType, $limit = 1, $displayUser = null) {
	if (($modType == 'mix') || ($modType == 'explore')) {
	  $suggestedArray = $this->mix_suggestions($limit, $modType, $displayUser);
	  return @COUNT($suggestedArray);
	}
	$modType = strstr($modType, 'friend') ? 'user' : $modType;
  $tempModName = $modType;
  if( strstr($modType, "sitereview") ){ $tempModName = "sitereview"; }
	$ismodEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($tempModName);
	if (empty($ismodEnabled)) {
	  return;
	}

	$suggestedArray = $this->getSuggIds($modType, $limit, $displayUser);
	return @COUNT($suggestedArray[0][$modType]);
  }

  /**
   * This function callig from Ajax, where we passed the javascript display user str and function retun us "display id" of the plugins.
   *
   * @param $modType: Module Type.
   * @param $display_mod_str: Display content ids string
   *
   */
  public function getDisplayModID($modName, $display_mod_str) {
	$display_mod_id = '';
	$getModArray = explode(",", $display_mod_str);
	foreach ($getModArray as $getMod) {
	  $getModInfo = explode("_", $getMod);
	  if (!empty($getModInfo) && !empty($getModInfo[0]) && !empty($getModInfo[1])) {
		if (($modName == $getModInfo[0]) && is_numeric($getModInfo[1])) {
		  $display_mod_id .= ',' . $getModInfo[1];
		}
	  }
	}
	$display_mod_id = trim($display_mod_id, ",");
	return $display_mod_id;
  }

  /**
   * Plugin which return the error, if Siteadmin not using correct version for the plugin.
   *
   */
  public function isModulesSupport() {
	$modArray = array(
		'document' => '4.2.3',
		'sitepage' => '4.2.3',
		'sitepagedocument' => '4.2.3',
		'sitepagepoll' => '4.2.3',
		'sitepagevideo' => '4.2.3',
		'sitepagenote' => '4.2.3',
		'sitepageoffer' => '4.2.3',
		'sitepagereview' => '4.2.3',
		'sitepagemusic' => '4.2.3',
		'sitepagealbum' => '4.2.3',
		'sitepageevent' => '4.2.3',
    'sitebusiness' => '4.2.3',
    'sitebusinessdocument' => '4.2.3',
    'sitebusinesspoll' => '4.2.3',
    'sitebusinessvideo' => '4.2.3',
    'sitebusinessnote' => '4.2.3',
    'sitebusinessoffer' => '4.2.3',
    'sitebusinessreview' => '4.2.3',
    'sitebusinessmusic' => '4.2.3',
    'sitebusinessalbum' => '4.2.3',
    'sitebusinessevent' => '4.2.3',
    'sitegroup' => '4.5.0',
    'sitegroupdocument' => '4.5.0',
    'sitegrouppoll' => '4.5.0',
    'sitegroupvideo' => '4.5.0',
    'sitegroupnote' => '4.5.0',
    'sitegroupoffer' => '4.5.0',
    'sitegroupreview' => '4.5.0',
    'sitegroupmusic' => '4.5.0',
    'sitegroupalbum' => '4.5.0',
    'sitegroupevent' => '4.5.0',
		'recipe' => '4.2.3',
		'list' => '4.2.3',
		'sitelike' => '4.2.3',
		'sitealbum' => '4.2.3',
	);
	$finalModules = array();
	foreach ($modArray as $key => $value) {
	  $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
	  if (!empty($isModEnabled)) {
		$getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
		$isModSupport = strcasecmp($getModVersion->version, $value);
		if ($isModSupport < 0) {
		  $finalModules[] = $getModVersion->title;
		}
	  }
	}
	return $finalModules;
  }

  /**
   * Returns the array of "Item types" of the modules, which are available in the manifest.php file of the modules.
   *
   * @param $moduleName: Name of the module.
   * @return Array
   */
  public function getContentItem($moduleName) {
	$mixSettingsTable = Engine_Api::_()->getDbtable('modinfos', 'suggestion');
	$mixSettingsTableName = $mixSettingsTable->info('name');
	$moduleArray = $mixSettingsTable->select()
					->from($mixSettingsTableName, "$mixSettingsTableName.item_type")
					->where($mixSettingsTableName . '.module = ?', $moduleName)
					->query()
					->fetchAll(Zend_Db::FETCH_COLUMN);

	$file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
	$contentItem = array();
	if (@file_exists($file_path)) {
	  $ret = include $file_path;
	  if (isset($ret['items'])) {
		foreach ($ret['items'] as $item)
		  if (!in_array($item, $moduleArray))
			$contentItem[$item] = $item . " ";
	  }
	}
	return $contentItem;
  }

   /**
   * Returns the array of settings of selected modules.
   *
   * @param $mod: Name of the module.
   * @param $setting: Name of settings, which value will be retrive.
   * @return Array
   */
  public function getModSettings($mod, $setting) {
	$getModArray = $getSettings = array();

  if( strstr($mod, "sitereview_") ) {
    $tempModArray = @explode("_", $mod);
    $modArray = Engine_Api::_()->getItem('suggestion_modinfo', $tempModArray[1]);
    if( empty($modArray) )
      return;
    $getModArray = $modArray->toArray();
  }else {
    $modArray = Engine_Api::_()->getDbtable('modinfos', 'suggestion')->getSelectedModContent($mod);
  }

	if (empty($getModArray) && !empty($modArray)) {
	  $getModArray = $modArray[0];
	}
	if (!empty($getModArray) && !empty($getModArray['settings'])) {
	  $getSettings = unserialize($getModArray['settings']);
	}

	$getFinalArray = array_merge($getModArray, $getSettings);
	if (array_key_exists('settings', $getFinalArray)) {
	  unset($getFinalArray['settings']);
	}

  $getResult = array();
//	$getResult = $getFinalArray;

	if (!empty($getFinalArray) && !empty($setting)) {
            if( array_key_exists($setting, $getFinalArray) ) { 
              $getResult = $getFinalArray[$setting];
            }else if( $setting == "show_default_image_member" ){
                return true;
            }
	}

	$getResult = empty($getResult) ? 0 : $getResult;

	return $getResult;
  }
  
  
  public function deleteListingType($listingtype_id) {
    if( empty($listingtype_id) ){ return; }
    $notificationType = 'sitereview_' . $listingtype_id . '_suggestion';
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    // DELETE FROM SUGGESTION MODULE TABLE.
    $db->query('DELETE FROM `engine4_suggestion_module_settings` WHERE `engine4_suggestion_module_settings`.`notification_type` LIKE "' . $notificationType . '"');

    $getNotification = $db->query('SELECT * FROM `engine4_activity_notifications` WHERE `type` LIKE "' . $notificationType . '" ')->fetchAll();
    foreach($getNotification as $notification) {
      // DELETE FROM SUGGESTION TABLE.
      Engine_Api::_()->getItem('suggestion', $notification['object_id'])->delete();
      
      // DELETE FROM NOTIFICATION TABLE.
      Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('notification_id = ?' => $notification['notification_id']));
    }
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
  
  public function getReviewModInfo($listing_id) {
    $queryObj = Zend_Db_Table_Abstract::getDefaultAdapter();
    $getReviewModules = $queryObj->query("SELECT * FROM `engine4_suggestion_module_settings` WHERE `module` LIKE 'sitereview'")->fetchAll();
    $listingId = 0;
    if( !empty($getReviewModules) )  {
      foreach( $getReviewModules as $module ) {
        $settingsArray = @unserialize($module['settings']);
        if( $listing_id == $settingsArray['listing_id'] ) {
          $listingId = $module['modinfo_id'];
          break;
        }
      }
    }
    
    return $listingId;
  }

}
?>
