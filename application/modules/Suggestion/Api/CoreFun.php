<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: coreFun.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Api_CoreFun extends Core_Api_Abstract {

  /**
   * Returns the object of table.
   *
   * @param $modName: Table Itemtype, which defined in settings/manifest.php file.
   * @param $modId: Optional( Row Id ), If required the only one row object.
   * @return Object
   */
  public function getSelfTableObj($modName, $modId = null) {
    if( empty($modName) ){ return; }
    if (!empty($modId)) {
      return Engine_Api::_()->getItem($modName, $modId);
    }
    return Engine_Api::_()->getItemTable($modName);
  }

  /**
   * Returns the "Forum Post" table object.
   * @return Object
   */
  public function getPostTableObj() {
    return Engine_Api::_()->getItemTable('forum_post');
  }

  /**
   * Returns the object of table.
   *
   * @param $modName: Module Name, which rating table object we required.
   * @return Object
   */
  public function getRatedTableObj($modName) {
    return Engine_Api::_()->getDbtable('ratings', $modName);
  }

  /**
   * Returns the "Forum Topics View" table object.
   * @return Object
   */
  public function getViewTableObj() {
    return Engine_Api::_()->getDbtable('topicviews', 'forum');
  }

  /**
   * Returns the "Core Likes" table object.
   * @return Object
   */
  public function getLikeTableObj() {
    return Engine_Api::_()->getDbtable('likes', 'core');
  }

  /**
   * Returns the "Core Commented" table object.
   * @return Object
   */
  public function getCommentTableObj() {
    return Engine_Api::_()->getDbtable('comments', 'core');
  }

  /**
   * Returns the "Poll Vote" table object.
   * @return Object
   */
  public function getVoteTableObj() {
    return Engine_Api::_()->getDbtable('votes', 'poll');
  }

  /**
   * Returns the "Album Photo" table object.
   * @return Object
   */
  public function getAlbumPhotoObj() {
    return Engine_Api::_()->getItemTable('album_photo');
  }

  /**
   * Returns the "Core Tag" table object.
   * @return Object
   */
  public function getTagObj() {
    return Engine_Api::_()->getItemTable('core_tag_map');
  }

  /**
   * Returns the object of table.
   *
   * @param $userId: Optional( Row Id ), If required the only one row object.
   * @return Object
   */
  public function getUserTableObj($userId = null) {
    if (!empty($userId)) {
      return Engine_Api::_()->getItem('user', $userId);
    }
    return Engine_Api::_()->getItemTable('user');
  }

  /**
   * Returns the object of table.
   *
   * @param $modName: Optional( Module Name ), Which membership table object we required.
   * @return Object
   */
  public function getMemberTableObj($modName = 'user') {
    return Engine_Api::_()->getDbtable('membership', $modName);
  }

  /**
   * Returns the trim string or 0.
   *
   * @param $str: String
   * @return String or 0
   */
  public function getTrimStr($str) {
		$str = trim($str, ',');
    if (empty($str)) {
      return 0;
    } else {
      $str = trim($str, ",");
      return $str;
    }
  }

  /**
   * Returns true / false if "Friend Id" is the friend of "Loggden User"
   *
   * @param $friend_id: Friend Id,
   * @return true or false
   */
  public function isMember($friend_id) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $isFriend = false;

    //FETCH FRIEND ID FROM DATABASE.
    $memberTable = Engine_Api::_()->getDbtable('membership', 'user');
    $memberTableName = $memberTable->info('name');

    $select = $memberTable->select()
                    ->where($memberTableName . '.active = ?', 1)
                    ->where($memberTableName . '.resource_id = ?', $friend_id)
                    ->where($memberTableName . '.user_id = ?', $viewer_id);

    $fetch = $select->query()->fetchAll();
    if (!empty($fetch)) {
      $isFriend = true;
    }
    return $isFriend;
  }

  /**
   * Returns Poll, which voted by loggden user.
   */
  public function getPollVotedByViewer() {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $pollIdArray = array();
    $pollIdStr = false;
    $voteTable = Engine_Api::_()->getDbtable('votes', 'poll');
    $voteTableName = $voteTable->info('name');

    $select = $voteTable->select()
                    ->setIntegrityCheck(false)
                    ->from($voteTableName, array('poll_id'))
                    ->where('user_id =?', $viewer_id);
    $fetch = $select->query()->fetchAll();
    foreach ($fetch as $id) {
      $pollIdArray[] = $id['poll_id'];
    }
    if (!empty($pollIdArray) && is_array($pollIdArray)) {
      $pollIdStr = implode(',', $pollIdArray);
    }
    return $pollIdStr;
  }

  /**
   * Returns string of Membership ( Friends Ids ) of pass "viewer id" or Loggden user. We are using this function in Suggestion Core Functions.
   *
   * @param $viewer_id: Optional, Return the "Membership Friend ( All Friend )" of this viewer id.
   * @return String.
   */
  public function getMembership($viewer_id = null) {
    if (empty($viewer_id)) {
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }

    $friendStr = $friendArray = null;

    //FETCH FRIEND ID FROM DATABASE.
    $table = Engine_Api::_()->getDbtable('membership', 'user');
    $iName = $table->info('name');

    $user_table = Engine_Api::_()->getItemTable('user');
    $uName = $user_table->info('name');

    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($iName, array('resource_id'))
                    ->joinLeft($uName, "$uName.user_id = $iName.user_id", null)
                    ->where($iName . '.user_id = ?', $viewer_id);
    $fetch_record = $select->query()->fetchAll();
    foreach ($fetch_record as $friend_id) {
      $friendArray[] = $friend_id['resource_id'];
    }
    if (!empty($friendArray) && is_array($friendArray)) {
      $friendStr = implode(',', $friendArray);
    }
    $friendStr = $this->getTrimStr($friendStr);
    return $friendStr;
  }

  /**
   * Returns array of "Mutual Friend" between "$friend_id" and "viewer_id".
   *
   * @param $friend_id: Find out mutual friend between "Pass friend id" and "Loggden user id".
   * @return Array.
   */
  public function getMutualFriend($friend_id) {
    $mutualFriendArray = array();
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $memberTable = $this->getMemberTableObj();
    $memberTableName = $memberTable->info('name');

    $select = $memberTable->select()
                    ->setIntegrityCheck(false)
                    ->from($memberTableName, array('user_id'))
                    ->join($memberTableName, "`{$memberTableName}`.`user_id`=`{$memberTableName}_2`.user_id", null)
                    ->where("`{$memberTableName}`.resource_id = ?", $friend_id) // Friend_id
                    ->where("`{$memberTableName}_2`.resource_id = ?", $viewer_id) // viewer_id
                    ->where("`{$memberTableName}`.active = ?", 1)
                    ->where("`{$memberTableName}_2`.active = ?", 1);
    $fetch_mutual_friend = $select->query()->fetchAll();
    if (!empty($fetch_mutual_friend)) {
      foreach ($fetch_mutual_friend as $mutual_friend_id) {
        $mutualFriendArray[] = $mutual_friend_id['user_id'];
      }
    }
    return $mutualFriendArray;
  }

  /**
   * Returns string "Event Id" or "Group Id", which we have joined already. 
   *
   * @param $modName: Module Name.
   * @param $notShowIds: Content Id, which will not be display as a suggestion.
   * @return String.
   */
  public function getMyJoin($modName, $notShowIds) {
    if ($modName == 'event' || $modName == 'group') {
      $memberTable = $this->getMemberTableObj($modName);
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $select = $memberTable->select()
                      ->from($memberTable, array('resource_id'))
                      ->where("user_id = $viewer_id");
      $fetch = $select->query()->fetchAll();
      foreach ($fetch as $modId) {
        $notShowIds .= ',' . $modId['resource_id'];
      }
    }
    return $notShowIds;
  }

  /**
   * Returns array of loggden user friend, which joined the "Group" or "Event".
   *
   * @param $modName: Module Name ( Ex: Group or Event ).
   * @param $modId: Id of pass module name.
   * @return Array.
   */
  public function getMyFriendJoin($modName, $modId) {
    if ($modName == 'event' || $modName == 'group') {
      $getFriednArray = array();
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $modMemberTable = $this->getMemberTableObj($modName);
      $modMemberTableName = $modMemberTable->info('name');

      $userMemberTable = $this->getMemberTableObj();
      $userMemberTableName = $userMemberTable->info('name');

      $select = $modMemberTable->select()
                      ->setIntegrityCheck(false)
                      ->from($modMemberTableName, array('user_id'))
                      ->joinInner($userMemberTableName, "$modMemberTableName . user_id = $userMemberTableName . resource_id", null)
                      ->where($modMemberTableName . '.resource_id = ?', $modId)
                      ->where($userMemberTableName . '.user_id = ?', $viewer_id)
                      ->where($userMemberTableName . '.active = ?', 1);

      $fetch = $select->query()->fetchAll();
      foreach ($fetch as $id) {
        $getFriednArray[] = $id['user_id'];
      }
      return $getFriednArray;
    }
    return;
  }

  /**
   * Returns array of loggden user friend, which post in "Forum".
   *
   * @param $modName: Module Name ( Ex: Forum ).
   * @param $modId: Id of pass module name.
   * @return Array.
   */
  public function getMyFriendPost($modName, $modId) {
    if ($modName == 'forum') {
      $getFriednArray = array();
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $postMemberTable = $this->getPostTableObj();
      $postMemberTableName = $postMemberTable->info('name');

      $userMemberTable = $this->getMemberTableObj();
      $userMemberTableName = $userMemberTable->info('name');

      $select = $postMemberTable->select()
                      ->setIntegrityCheck(false)
                      ->from($postMemberTableName, array('user_id'))
                      ->joinInner($userMemberTableName, "$postMemberTableName . user_id = $userMemberTableName . resource_id", null)
                      ->where($postMemberTableName . '.topic_id = ?', $modId)
                      ->where($userMemberTableName . '.user_id = ?', $viewer_id)
                      ->where($userMemberTableName . '.active = ?', 1);

      $fetch = $select->query()->fetchAll();
      foreach ($fetch as $id) {
        $getFriednArray[] = $id['user_id'];
      }
      return $getFriednArray;
    }
    return;
  }

  /**
   * This common function return the content, which created by my friend.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function createByFriend($modName, $notShowIds, $rejectedStr, $limit) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];

    // This function is calling for "Group" and "Event" and for other it return emmpty result.
    $notShowIds = $this->getMyJoin($modName, $notShowIds);

    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);

    $getOrder = Engine_Api::_()->getApi('modInfo', 'suggestion')->getOrder($modName);
    $getModCondition = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModCondition($modName);

    // Getting self table objects.
    $selfTable = $this->getSelfTableObj($modInfo['itemType']);
    $selfTableName = $selfTable->info('name');

    // Getting membership table objects.
    $memberTable = $this->getMemberTableObj();
    $memberTableName = $memberTable->info('name');

    $select = $memberTable->select()
                    ->setIntegrityCheck(false)
                    ->from($memberTable, array())
                    ->joinInner($selfTableName, '' . $selfTableName . '.' . $modInfo['ownerColumnName'] . ' = ' . $memberTableName . '.user_id', array($modInfo['idColumnName']))
                    ->where($memberTableName . '.resource_id = ?', $viewer_id)
                    ->where($memberTableName . '.active = ?', 1)
                    ->where($selfTableName . '.' . $modInfo['ownerColumnName'] . ' != ?', $viewer_id)
                    ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($notShowIds)")
                    ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($rejectedStr)")
                    ->order($getOrder)
                    ->limit($limit);

    // Poll which joined by loggden user will not be recommended.
    if ($modName == 'poll') {
      $pollIdStr = $this->getPollVotedByViewer();
      $pollIdStr = $this->getTrimStr($pollIdStr);
      $select->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($pollIdStr)");
    }

    // Module settings ( Ex: is searchable or not, is draft or not )
    if (!empty($getModCondition)) {
      foreach ($getModCondition as $condition => $value) {
        $select->where($selfTableName . '.' . $condition . ' =?', $value);
      }
    }

    // Show only upcomming event.
    if ($modName == 'event') {
      $select->where($selfTableName . ".endtime > FROM_UNIXTIME(?)", time());
    }

    $fetch_id = $select->query()->fetchAll();
    $itemId = array();
    foreach ($fetch_id as $id) {
      $itemId[$id[$modInfo['idColumnName']]] = $id[$modInfo['idColumnName']];
    }
    return $itemId;
  }

  /**
   * This common function return the content, which liked by my friend.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function likedByFriend($modName, $notShowIds, $rejectedStr, $limit) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];
    $getModCondition = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModCondition($modName);
    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);

    // Getting self table objects.
    $selfTable = $this->getSelfTableObj($modInfo['itemType']);
    $selfTableName = $selfTable->info('name');

    // Getting like table objects.
    $likeTable = $this->getLikeTableObj();
    $likeTableName = $likeTable->info('name');

    // Getting membership table objects.
    $memberTable = $this->getMemberTableObj();
    $memberTableName = $memberTable->info('name');

    if ($modName == 'music' || $modName == 'list' || $modName == 'sitepage') {
      $modInfo['pluginName'] = $modInfo['itemType'];
    }

    $select = $likeTable->select()
                    ->setIntegrityCheck(false)
                    ->from($memberTable, array('COUNT(' . $memberTableName . '.user_id) AS friends_like_count'))
                    ->joinInner($likeTableName, '' . $likeTableName . '.poster_id = ' . $memberTableName . '.user_id', array('resource_id'))
                    ->joinInner($selfTableName, '' . $selfTableName . '.' . $modInfo['idColumnName'] . ' = ' . $likeTableName . '.resource_id', array())
                    ->where($memberTableName . '.resource_id = ?', $viewer_id)
                    ->where($likeTableName . '.resource_type = ?', $modInfo['pluginName'])
                    ->where($selfTableName . '.' . $modInfo['ownerColumnName'] . ' != ?', $viewer_id)
                    ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($notShowIds)")
                    ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($rejectedStr)")
                    ->group($likeTableName . '.resource_id')
                    ->order('friends_like_count DESC')
                    ->limit($limit);

    // Poll which joined by loggden user will not be recommended.
    if ($modName == 'poll') {
      $pollIdStr = $this->getPollVotedByViewer();
      $pollIdStr = $this->getTrimStr($pollIdStr);
      $select->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($pollIdStr)");
    }

    // Module settings ( Ex: is searchable or not, is draft or not )
    if (!empty($getModCondition)) {
      foreach ($getModCondition as $condition => $value) {
        $select->where($selfTableName . '.' . $condition . ' =?', $value);
      }
    }

    $fetch_id = $select->query()->fetchAll();
    $itemId = array();
    foreach ($fetch_id as $id) {
      $itemId[$id['resource_id']] = $id['resource_id'];
    }
    return $itemId;
  }

  /**
   * This common function return the content, which are popular.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function popularMod($modName, $notShowIds, $rejectedStr, $limit) {
    if( empty($modName) )
      return;

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if( strstr($modName, "sitereview") ) {
      $tempModId = @explode("_", $modName);
      if( !empty($tempModId[1]) ) {
        $tempModArray = Engine_Api::_()->getItem('suggestion_modinfo', $tempModId[1]);
      }
      if( !empty($tempModArray) ) {
       $tempSettings = @unserialize($tempModArray->settings);
       $reviewListingId = !empty($tempSettings['listing_id'])? $tempSettings['listing_id']: null;
      }
    }

    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];

    // This function is calling for "Group" and "Event" and for other it return emmpty result.
    $notShowIds = $this->getMyJoin($modName, $notShowIds);

    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);

    // Getting self table objects.
    $selfTable = $this->getSelfTableObj($modInfo['itemType']);
    $selfTableName = $selfTable->info('name');

    $getModInfoTem = Engine_Api::_()->getDbtable('modinfos', 'suggestion')->getSelectedModContent($modName);
    if( !empty($getModInfoTem) && !empty($getModInfoTem[0]['default']) ) {
      $getOrder = Engine_Api::_()->getApi('modInfo', 'suggestion')->getOrder($modName); // Find out the order.
    }
    

    $getModCondition = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModCondition($modName);

    $select = $selfTable->select()
                    ->from($selfTable, array($modInfo['idColumnName']))
                    ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($notShowIds)")
                    ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($rejectedStr)");
                    
    if( !strstr($modName, 'siteestore') || !strstr($modName, 'sitestoreproduct') ) {
      $select->where($selfTableName . '.' . $modInfo['ownerColumnName'] . ' != ?', $viewer_id);
    }
    if( $modName == 'sitestoreproduct' ) {
      $otherInfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');
      $select->setIntegrityCheck(false)
             ->joinInner($otherInfoTableName, "$selfTableName.product_id = $otherInfoTableName.product_id", array("discount", "discount_start_date", "discount_end_date", "discount_permanant", "user_type", "handling_type", "discount_value", "discount_amount"))
             ->where($selfTableName . '.closed = ?', '0')
             ->where($selfTableName . '.approved = ?', '1')
             ->where($selfTableName . '.draft = ?', '0')
             ->where($selfTableName . ".search = ?", 1)
             ->where("$selfTableName .start_date <= NOW()")
             ->where("$selfTableName.end_date_enable = 0 OR $selfTableName.end_date > NOW()")
             ->where("$selfTableName.stock_unlimited = 1 OR $selfTableName.min_order_quantity <= $selfTableName.in_stock");
      
    }
    
    if( $modName == 'siteevent' ){
      $SiteEventOccuretable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
      $siteeventOccurTableName = $SiteEventOccuretable->info('name');
      
      $select ->setIntegrityCheck(false)
              ->join($siteeventOccurTableName, "$selfTableName.event_id = $siteeventOccurTableName.event_id", null)
              ->where($selfTableName . '.closed = ?', '0')
              ->where($selfTableName . '.approved = ?', '1')
              ->where($selfTableName . '.draft = ?', '0')
              ->where($selfTableName . ".search = ?", 1)              
              ->where("$siteeventOccurTableName.endtime > NOW()")
              ->group("$selfTableName.event_id")
              ->order("$siteeventOccurTableName.endtime");   
    }
                    
		    if( !empty($getOrder) ) {
			$select->order($getOrder)
			->order('creation_date DESC');
		      }else {
			$select->order('RAND()');
		      }
                    $select->limit($limit);

    // Show only upcomming event.
    if ($modName == 'event') {
      $select->where($selfTableName . ".endtime > FROM_UNIXTIME(?)", time());
    }

    // Poll which joined by loggden user will not be recommended.
    if ($modName == 'poll') {
      $pollIdStr = $this->getPollVotedByViewer();
      $pollIdStr = $this->getTrimStr($pollIdStr);
      $select->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($pollIdStr)");
    }
    
    if(($modName == 'siteestore')) {
      $select->where($selfTableName . ".status =?", 1);
    }
    
    if( strstr($modName, "sitereview") && !empty($reviewListingId) ) {
      $select->where($selfTableName . ".listingtype_id =?", $reviewListingId);
    }

    // Module settings ( Ex: is searchable or not, is draft or not )
    if (!empty($getModCondition) && !strstr($modName, 'siteestore') && !strstr($modName, 'sitestoreproduct')) {
      foreach ($getModCondition as $condition => $value) {
        $select->where($selfTableName . '.' . $condition . ' =?', $value);
      }
    }
                             
    $fetch_id = $select->query()->fetchAll();
    $itemId = array();
    foreach ($fetch_id as $id) {
      $itemId[$id[$modInfo['idColumnName']]] = $id[$modInfo['idColumnName']];
    }
    return $itemId;
  }

  /**
   * Function return the Events, which attended by my friend.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @param $isOrderSet: Getting the order for SQL.
   * @return Array.
   */
  public function intrestedByFriend($modName, $notShowIds, $rejectedStr, $limit, $isOrderSet = null) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];
    $getModCondition = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModCondition($modName);

    // This function is calling for "Group" and "Event" and for other it return emmpty result.
    $notShowIds = $this->getMyJoin($modName, $notShowIds);

    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);

    // Getting self table objects.
    $selfTable = $this->getSelfTableObj($modInfo['itemType']);
    $selfTableName = $selfTable->info('name');

    // Getting user membership table objects.
    $memberTable = $this->getMemberTableObj();
    $memberTableName = $memberTable->info('name');

    // Getting module membership table objects.
    $modMemberTable = $this->getMemberTableObj($modName);
    $modMemberTableName = $modMemberTable->info('name');

    $select = $selfTable->select()
                    ->setIntegrityCheck(false)
                    ->from($memberTableName, array('COUNT(' . $memberTableName . '.user_id) AS friends_attend_count'))
                    ->joinInner($modMemberTableName, "$modMemberTableName.user_id = $memberTableName.user_id", array('resource_id'))
                    ->joinInner($selfTableName, "$selfTableName." . $modInfo['idColumnName'] . " = $modMemberTableName.resource_id", array())
                    ->where($selfTableName . '.' . $modInfo['ownerColumnName'] . ' != ?', $viewer_id)
                    ->where($memberTableName . '.resource_id = ?', $viewer_id)
                    ->where($memberTableName . '.active = ?', 1)
                    ->where($modMemberTableName . '.active = ?', 1)
                    ->where($modMemberTableName . '.resource_approved = ?', 1)
                    ->where($modMemberTableName . '.user_approved = ?', 1)
                    ->where($modMemberTableName . ".resource_id NOT IN ($notShowIds)")
                    ->where($modMemberTableName . ".resource_id NOT IN ($rejectedStr)")
                    ->group($modMemberTableName . '.resource_id')
                    ->limit($limit);

    // Show only upcomming event.
    if ($modName == 'event') {
      $select->where($selfTableName . ".endtime > FROM_UNIXTIME(?)", time());
    }

    // Module settings ( Ex: is searchable or not, is draft or not )
    if (!empty($getModCondition)) {
      foreach ($getModCondition as $condition => $value) {
        $select->where($selfTableName . '.' . $condition . ' =?', $value);
      }
    }

    if (!empty($isOrderSet)) {
      $getOrder = Engine_Api::_()->getApi('modInfo', 'suggestion')->getOrder($modName);
      $select->order($selfTableName . '.' . $getOrder);
    } else {
      $select->order('friends_attend_count DESC');
    }

    $fetch_id = $select->query()->fetchAll();
    $itemId = array();
    foreach ($fetch_id as $id) {
      $itemId[$id['resource_id']] = $id['resource_id'];
    }
    return $itemId;
  }

  /**
   * Upcoming events belonging to the same category as the one being viewed. [This will only work if the widget is being shown on the "Event Profile" page.]
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function viewModSameCategory($modName, $notShowIds, $rejectedStr, $limit) {
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    if ((($module == 'event') || ($module == 'group')) && ($controller == 'profile') && ($action == 'index')) {
      $subject = Engine_Api::_()->core()->getSubject();
      if (empty($subject)) {
        return;
      }

      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
      $modInfo = $modInfo[$modName];
      $getModCondition = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModCondition($modName);

      // This function is calling for "Group" and "Event" and for other it return emmpty result.
      $notShowIds = $this->getMyJoin($modName, $notShowIds);

      $notShowIds = $this->getTrimStr($notShowIds);
      $rejectedStr = $this->getTrimStr($rejectedStr);

      // Getting self table objects.
      $selfTable = $this->getSelfTableObj($modInfo['itemType']);
      $selfTableName = $selfTable->info('name');

      $select = $selfTable->select()
                      ->from($selfTable, array($modInfo['idColumnName']))
                      ->where($selfTableName . '.' . $modInfo['ownerColumnName'] . ' != ?', $viewer_id)
                      ->where($selfTableName . '.category_id = ?', $subject->category_id)
                      ->where($selfTableName . '.' . $modInfo['ownerColumnName'] . ' != ?', $subject->getIdentity())
                      ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($notShowIds)")
                      ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($rejectedStr)")
                      ->limit($limit);

      // Show only upcomming event.
      if ($modName == 'event') {
        $select->where($selfTableName . ".endtime > FROM_UNIXTIME(?)", time());
      }

      // Module settings ( Ex: is searchable or not, is draft or not )
      if (!empty($getModCondition)) {
        foreach ($getModCondition as $condition => $value) {
          $select->where($selfTableName . '.' . $condition . ' =?', $value);
        }
      }

      $fetch_id = $select->query()->fetchAll();
      $itemId = array();
      foreach ($fetch_id as $id) {
        $itemId[$id[$modInfo['idColumnName']]] = $id[$modInfo['idColumnName']];
      }
      return $itemId;
    }
    return;
  }

  /**
   * Upcoming events being attended by my friends.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function orderOfAttendByFriend($modName, $notShowIds, $rejectedStr, $limit) {
    $orderOfAttendByFriend = $this->intrestedByFriend($modName, $notShowIds, $rejectedStr, $limit, 1);
    return $orderOfAttendByFriend;
  }

  /**
   * This common function return the content, which commented by my friend.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function commentByFriend($modName, $notShowIds, $rejectedStr, $limit) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];
    $getModCondition = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModCondition($modName);
    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);

    // Getting self table objects.
    $selfTable = $this->getSelfTableObj($modInfo['itemType']);
    $selfTableName = $selfTable->info('name');

    // Getting like table objects.
    $commentTable = $this->getCommentTableObj();
    $commentTableName = $commentTable->info('name');

    // Getting membership table objects.
    $memberTable = $this->getMemberTableObj();
    $memberTableName = $memberTable->info('name');

    if ($modName == 'music' || $modName == 'list' || $modName == 'sitepage') {
      $modInfo['pluginName'] = $modInfo['itemType'];
    }

    $select = $commentTable->select()
                    ->setIntegrityCheck(false)
                    ->from($memberTable, array('COUNT(' . $memberTableName . '.user_id) AS friends_comment_count'))
                    ->joinInner($commentTableName, '' . $commentTableName . '.poster_id = ' . $memberTableName . '.user_id', array())
                    ->joinInner($selfTableName, '' . $selfTableName . '.' . $modInfo['idColumnName'] . ' = ' . $commentTableName . '.resource_id', array($modInfo['idColumnName']))
                    ->where($memberTableName . '.resource_id = ?', $viewer_id)
                    ->where($commentTableName . '.resource_type = ?', $modInfo['pluginName'])
                    ->where($selfTableName . '.' . $modInfo['ownerColumnName'] . ' != ?', $viewer_id)
                    ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($notShowIds)")
                    ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($rejectedStr)")
                    ->group($commentTableName . '.resource_id')
                    ->order('friends_comment_count DESC')
                    ->limit($limit);

    // Poll which joined by loggden user will not be recommended.
    if ($modName == 'poll') {
      $pollIdStr = $this->getPollVotedByViewer();
      $pollIdStr = $this->getTrimStr($pollIdStr);
      $select->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($pollIdStr)");
    }

    // Module settings ( Ex: is searchable or not, is draft or not )
    if (!empty($getModCondition)) {
      foreach ($getModCondition as $condition => $value) {
        $select->where($selfTableName . '.' . $condition . ' =?', $value);
      }
    }

    $fetch_id = $select->query()->fetchAll();
    $itemId = array();
    foreach ($fetch_id as $id) {
      $itemId[$id[$modInfo['idColumnName']]] = $id[$modInfo['idColumnName']];
    }
    return $itemId;
  }

  /**
   * This common function return the content, which voted by my friend.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function voteByFriend($modName, $notShowIds, $rejectedStr, $limit) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];
    $getModCondition = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModCondition($modName);
    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);

    // Getting self table objects.
    $selfTable = $this->getSelfTableObj($modInfo['itemType']);
    $selfTableName = $selfTable->info('name');

    // Getting like table objects.
    $voteTable = $this->getVoteTableObj();
    $voteTableName = $voteTable->info('name');

    // Getting membership table objects.
    $memberTable = $this->getMemberTableObj();
    $memberTableName = $memberTable->info('name');

    $select = $voteTable->select()
                    ->setIntegrityCheck(false)
                    ->from($memberTable, array('COUNT(' . $memberTableName . '.user_id) AS friends_vote_count'))
                    ->joinInner($voteTableName, '' . $voteTableName . '.user_id = ' . $memberTableName . '.user_id', array($modInfo['idColumnName']))
                    ->joinInner($selfTableName, '' . $selfTableName . '.' . $modInfo['idColumnName'] . ' = ' . $voteTableName . '.' . $modInfo['idColumnName'], array())
                    ->where($memberTableName . '.resource_id = ?', $viewer_id)
                    ->where($selfTableName . '.' . $modInfo['ownerColumnName'] . ' != ?', $viewer_id)
                    ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($notShowIds)")
                    ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($rejectedStr)")
                    ->group($voteTableName . '.' . $modInfo['idColumnName'])
                    ->order('friends_vote_count DESC')
                    ->limit($limit);

    // Poll which joined by loggden user will not be recommended.
    if ($modName == 'poll') {
      $pollIdStr = $this->getPollVotedByViewer();
      $pollIdStr = $this->getTrimStr($pollIdStr);
      $select->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($pollIdStr)");
    }

    // Module settings ( Ex: is searchable or not, is draft or not )
    if (!empty($getModCondition)) {
      foreach ($getModCondition as $condition => $value) {
        $select->where($selfTableName . '.' . $condition . ' =?', $value);
      }
    }

    $fetch_id = $select->query()->fetchAll();
    $itemId = array();
    foreach ($fetch_id as $id) {
      $itemId[$id[$modInfo['idColumnName']]] = $id[$modInfo['idColumnName']];
    }
    return $itemId;
  }

  /**
   * This common function return the content, which tagged by my friend.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function tagByFriend($modName, $notShowIds, $rejectedStr, $limit) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];
    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);

    // Getting self table objects.
    $selfTable = $this->getSelfTableObj($modInfo['itemType']);
    $selfTableName = $selfTable->info('name');

    // Getting membership table objects.
    $memberTable = $this->getMemberTableObj();
    $memberTableName = $memberTable->info('name');

    $albumPhotoTable = $this->getAlbumPhotoObj();
    $albumPhotoTableName = $albumPhotoTable->info('name');

    $tagTable = $this->getTagObj();
    $tagTableName = $tagTable->info('name');

    $select = $albumPhotoTable->select()
                    ->setIntegrityCheck(false)
                    ->from($memberTable, array('COUNT(' . $memberTableName . '.user_id) AS tag_album_count'))
                    ->joinInner($tagTableName, '' . $tagTableName . '.tag_id = ' . $memberTableName . '.user_id', array())
                    ->joinInner($albumPhotoTableName, '' . $albumPhotoTableName . '.photo_id = ' . $tagTableName . '.resource_id', array('album_id'))
                    ->where($memberTableName . '.resource_id = ?', $viewer_id)
                    ->where($tagTableName . '.resource_type = ?', $modInfo['tagName'])
                    ->where($tagTableName . '.tag_type = ?', 'user')
                    ->where($albumPhotoTableName . "." . $modInfo['idColumnName'] . " NOT IN ($notShowIds)")
                    ->where($albumPhotoTableName . "." . $modInfo['idColumnName'] . " NOT IN ($rejectedStr)")
                    ->group($albumPhotoTableName . '.album_id')
                    ->order('tag_album_count DESC')
                    ->limit($limit);

    $fetch_id = $select->query()->fetchAll();
    $itemId = array();
    foreach ($fetch_id as $id) {
      $itemId[$id[$modInfo['idColumnName']]] = $id[$modInfo['idColumnName']];
    }
    return $itemId;
  }

  /**
   * Event which created by my friend in the order of most attending event by my friend.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function mostAttendingByFriend($modName, $notShowIds, $rejectedStr, $limit) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];
    $getModCondition = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModCondition($modName);

    // This function is calling for "Group" and "Event" and for other it return emmpty result.
    $notShowIds = $this->getMyJoin($modName, $notShowIds);

    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);

    $getOrder = Engine_Api::_()->getApi('modInfo', 'suggestion')->getOrder($modName);

    // Getting self table objects.
    $selfTable = $this->getSelfTableObj($modInfo['itemType']);
    $selfTableName = $selfTable->info('name');

    // Getting membership table objects.
    $memberTable = $this->getMemberTableObj();
    $memberTableName = $memberTable->info('name');


    $select = $selfTable->select()
                    ->setIntegrityCheck(false)
                    ->from($selfTableName, array($modInfo['idColumnName'], 'COUNT(' . $memberTableName . '.user_id) AS friends_attend_count'))
                    ->joinInner($memberTableName, "$memberTableName.user_id = $selfTableName." . $modInfo['ownerColumnName'], array())
                    ->where($selfTableName . '.' . $modInfo['ownerColumnName'] . ' != ?', $viewer_id)
                    ->where($memberTableName . '.resource_id = ?', $viewer_id)
                    ->where($memberTableName . '.active = ?', 1)
                    ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($notShowIds)")
                    ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($rejectedStr)")
                    ->group($selfTableName . '.' . $modInfo['idColumnName'])
                    ->order('friends_attend_count DESC')
                    ->limit($limit);

    // Module settings ( Ex: is searchable or not, is draft or not )
    if (!empty($getModCondition)) {
      foreach ($getModCondition as $condition => $value) {
        $select->where($selfTableName . '.' . $condition . ' =?', $value);
      }
    }

    // Show only upcomming event.
    if ($modName == 'event') {
      $select->where($selfTableName . ".endtime > FROM_UNIXTIME(?)", time());
    }

    $fetch_id = $select->query()->fetchAll();
    $itemId = array();
    foreach ($fetch_id as $id) {
      $itemId[$id[$modInfo['idColumnName']]] = $id[$modInfo['idColumnName']];
    }
    return $itemId;
  }

  /**
   * Forum Topics, where replied by my friends.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @param $isOrder: Getting order for SQL.
   * @return Array.
   */
  public function replyByFriend($modName, $notShowIds, $rejectedStr, $limit, $isOrder = null) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];

    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);

    $getOrder = Engine_Api::_()->getApi('modInfo', 'suggestion')->getOrder($modName);

    // Getting self table objects.
    $selfTable = $this->getSelfTableObj($modInfo['itemType']);
    $selfTableName = $selfTable->info('name');

    // Getting membership table objects.
    $memberTable = $this->getMemberTableObj();
    $memberTableName = $memberTable->info('name');

    // Getting post table objects.
    $postTable = $this->getPostTableObj();
    $postTableName = $postTable->info('name');

    $select = $postTable->select()
                    ->setIntegrityCheck(false)
                    ->from($memberTable, array('COUNT(' . $postTableName . '.user_id) AS friends_topic_count'))
                    ->joinInner($postTableName, '' . $postTableName . '.user_id = ' . $memberTableName . '.user_id', array($modInfo['idColumnName']))
                    ->joinInner($selfTableName, '' . $postTableName . '.topic_id = ' . $selfTableName . '.' . $modInfo['idColumnName'], null)
                    ->where($memberTableName . '.resource_id = ?', $viewer_id)
                    ->where($selfTableName . '.' . $modInfo['ownerColumnName'] . ' != ?', $viewer_id)
                    ->where($postTableName . '.user_id != ?', $viewer_id)
                    ->where($postTableName . ".topic_id NOT IN ($notShowIds)")
                    ->where($postTableName . ".topic_id NOT IN ($rejectedStr)")
                    ->group($postTableName . '.topic_id')
                    ->limit($limit);

    if (empty($isOrder)) {
      $select->order('friends_topic_count DESC');
    } else {
      $select->order($selfTableName . '.view_count DESC');
    }

    $fetch_id = $select->query()->fetchAll();
    $itemId = array();
    foreach ($fetch_id as $id) {
      $itemId[$id[$modInfo['idColumnName']]] = $id[$modInfo['idColumnName']];
    }
    return $itemId;
  }

  /**
   * This common function return the content, which viewed by my friend.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function viewByFriend($modName, $notShowIds, $rejectedStr, $limit) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];

    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);

    $getOrder = Engine_Api::_()->getApi('modInfo', 'suggestion')->getOrder($modName);

    // Getting self table objects.
    $selfTable = $this->getSelfTableObj($modInfo['itemType']);
    $selfTableName = $selfTable->info('name');

    // Getting membership table objects.
    $memberTable = $this->getMemberTableObj();
    $memberTableName = $memberTable->info('name');

    // Getting post table objects.
    $viewTable = $this->getViewTableObj();
    $viewTableName = $viewTable->info('name');

    $select = $viewTable->select()
                    ->setIntegrityCheck(false)
                    ->from($memberTable, array('COUNT(' . $viewTableName . '.user_id) AS friends_view_count'))
                    ->joinInner($viewTableName, '' . $viewTableName . '.user_id = ' . $memberTableName . '.user_id', array($modInfo['idColumnName']))
                    ->joinInner($selfTableName, '' . $viewTableName . '.topic_id = ' . $selfTableName . '.' . $modInfo['idColumnName'], null)
                    ->where($memberTableName . '.resource_id = ?', $viewer_id)
                    ->where($selfTableName . '.' . $modInfo['ownerColumnName'] . ' != ?', $viewer_id)
                    ->where($viewTableName . '.user_id != ?', $viewer_id)
                    ->where($viewTableName . ".topic_id NOT IN ($notShowIds)")
                    ->where($viewTableName . ".topic_id NOT IN ($rejectedStr)")
                    ->group($viewTableName . '.topic_id')
                    ->order('friends_view_count DESC')
                    ->limit($limit);

    $fetch_id = $select->query()->fetchAll();
    $itemId = array();
    foreach ($fetch_id as $id) {
      $itemId[$id[$modInfo['idColumnName']]] = $id[$modInfo['idColumnName']];
    }
    return $itemId;
  }

  /**
   * Forum Topics commented on/replied to by my friends (in the order of their views count Desc)
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function mostViewReplyByFriend($modName, $notShowIds, $rejectedStr, $limit) {
    $mostViewReplyByFriend = $this->replyByFriend($modName, $notShowIds, $rejectedStr, $limit, 1);
    return $mostViewReplyByFriend;
  }

  /**
   * Videos "Rated" by my friends in the order of number of friends rating them.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function ratedByFriend($modName, $notShowIds, $rejectedStr, $limit) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];
    $getModCondition = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModCondition($modName);
    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);

    // Getting self table objects.
    $selfTable = $this->getSelfTableObj($modInfo['itemType']);
    $selfTableName = $selfTable->info('name');

    // Getting like table objects.
    $ratedTable = $this->getRatedTableObj($modName);
    $ratedTableName = $ratedTable->info('name');

    // Getting membership table objects.
    $memberTable = $this->getMemberTableObj();
    $memberTableName = $memberTable->info('name');

    $select = $ratedTable->select()
                    ->setIntegrityCheck(false)
                    ->from($memberTable, array('COUNT(' . $memberTableName . '.user_id) AS friends_rate_count'))
                    ->joinInner($ratedTableName, '' . $ratedTableName . '.user_id = ' . $memberTableName . '.user_id', array())
                    ->joinInner($selfTableName, '' . $selfTableName . '.' . $modInfo['idColumnName'] . ' = ' . $ratedTableName . '.' . $modInfo['idColumnName'], array($modInfo['idColumnName']))
                    ->where($memberTableName . '.resource_id = ?', $viewer_id)
                    ->where($selfTableName . '.' . $modInfo['ownerColumnName'] . ' != ?', $viewer_id)
                    ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($notShowIds)")
                    ->where($selfTableName . "." . $modInfo['idColumnName'] . " NOT IN ($rejectedStr)")
                    ->group($ratedTableName . '.' . $modInfo['idColumnName'])
                    ->order('friends_rate_count DESC')
                    ->order('view_count DESC')
                    ->limit($limit);

    // Module settings ( Ex: is searchable or not, is draft or not )
    if (!empty($getModCondition)) {
      foreach ($getModCondition as $condition => $value) {
        $select->where($selfTableName . '.' . $condition . ' =?', $value);
      }
    }

    $fetch_id = $select->query()->fetchAll();
    $itemId = array();
    foreach ($fetch_id as $id) {
      $itemId[$id[$modInfo['idColumnName']]] = $id[$modInfo['idColumnName']];
    }
    return $itemId;
  }

  /**
   * Network of loggden user or pass Id and all friend which belong to these network
   *
   * @param $getNetwork: Optional, If set then return only network str.
   * @param $userId_network: Optional, Find out network of this user and return all his friend which joind his networks. 
   * @param $userId_membership: Optional, Find only his friend which joind above network.  
   * @return String.
   */
  public function getMyNetworkFriend($getNetwork = null, $userId_network = null, $userId_membership = null) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $getFriendIdWithNetworkStr = false;
    $networkStr = '';

    // Return: Given user network str and his friend which joind that network.
    if (!empty($userId_network)) {
      $viewer = Engine_Api::_()->getItem('user', $userId_network);
      $viewer_id = $viewer->getIdentity();
    }

    // Return: network from above conditions and friend of which joind these network.
    if (!empty($userId_membership)) {
      $viewer_id = $userId_membership;
    }



    $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($viewer);
    $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);
    foreach ($networks as $network) {
      $networkStr .= ',' . $network->getIdentity();
    }
    $networkStr = $this->getTrimStr($networkStr);
    if (!empty($getNetwork)) {
      return $networkStr;
    }

    // Getting membership table objects.
    $memberTable = $this->getMemberTableObj();
    $memberTableName = $memberTable->info('name');

    $networkTable = Engine_Api::_()->getDbtable('membership', 'network');
    $networkTableName = $networkTable->info('name');

    $select = $memberTable->select()
                    ->setIntegrityCheck(false)
                    ->from($memberTable, array('user_id'))
                    ->joinInner($networkTableName, $networkTableName . '.user_id = ' . $memberTableName . '.user_id', null)
                    ->where($memberTableName . '.resource_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
                    ->where($memberTableName . '.active = ?', 1)
                    ->where($networkTableName . ".resource_id IN ($networkStr)")
                    ->where($networkTableName . '.active = ?', 1)
                    ->where($networkTableName . '.resource_approved = ?', 1)
                    ->where($networkTableName . '.user_approved = ?', 1);
    $fetch = $select->query()->fetchAll();

    $friendIds = array();
    foreach ($fetch as $friend_id) {
      $friendIds[] = $friend_id['user_id'];
    }
    $friendIds = array_unique($friendIds);
    if (!empty($friendIds) && is_array($friendIds)) {
      $getFriendIdWithNetworkStr = implode(',', $friendIds);
    }
    return $getFriendIdWithNetworkStr;
  }

  /**
   * Return Arry of loggden user friend, whoem i have send message.
   */
  public function getFriendSendMessage() {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $getFriednArray = array();
    $messageTable = Engine_Api::_()->getItemTable('messages_message');
    $messageTableName = $messageTable->info('name');

    $recipientTable = Engine_Api::_()->getDbtable('recipients', 'messages');
    $recipientTableName = $recipientTable->info('name');

    $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
    $membershipTableName = $membershipTable->info('name');

    $select = $recipientTable->select()
                    ->setIntegrityCheck(false)
                    ->from($recipientTableName, array('user_id'))
                    ->joinInner($messageTableName, "$recipientTableName . conversation_id = $messageTableName . conversation_id", null)
                    ->joinInner($membershipTableName, "$membershipTableName . resource_id = $recipientTableName . user_id", null)
                    ->where($membershipTableName . '.user_id = ?', $viewer_id);

    $fetch = $select->query()->fetchAll();
    foreach ($fetch as $id) {
      $getFriednArray[] = $id['user_id'];
    }
    return $getFriednArray;
  }

  /**
   * Recommendation Widgets: Find out the friend of viewer for sending message.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function messagefriend_mix_sugg($modName, $notShowIds, $rejectedStr, $limit) {
    $getFriendStr = $getMyFriend = null;
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];
    $getModCondition = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModCondition($modName);
    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);

    // 1) We only show this suggestion to the user if he has more than 3 friends
    // 2) We randomly pick a friend to whom the user has never messaged. If this comes out to be empty, we go to step 3
    // 3) We pick up a friend to whom the user messaged first.
    // Initialized all table's.
    $messageTable = Engine_Api::_()->getItemTable('messages_message');
    $messageTableName = $messageTable->info('name');

    $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
    $membershipTableName = $membershipTable->info('name');

    $recipientTable = Engine_Api::_()->getDbtable('recipients', 'messages');
    $recipientTableName = $recipientTable->info('name');

    $userTable = Engine_Api::_()->getItemTable('user');
    $userTableName = $userTable->info('name');

    //Query for check that user should have more then 3 friend.
    $select = $userTable->select()
                    ->from($userTableName, array('member_count'))
                    ->where($userTableName . '.user_id = ?', $viewer_id);
    $fetch = $select->query()->fetchAll();
    if (!empty($fetch) && $fetch[0]['member_count'] > 3) {
      // Getting a Friend Array, whoem i have send message.
      $getFriendArray = $this->getFriendSendMessage();
      if (!empty($getFriendArray) && is_array($getFriendArray)) {
        $getFriendStr = implode(',', $getFriendArray);
      }
      $getFriendStr = $this->getTrimStr($getFriendStr);

      //Query for pick a friend to whom I've never messaged.
      $select = $membershipTable->select()
                      ->from($membershipTableName, array('resource_id'))
                      ->where($membershipTableName . '.user_id = ?', $viewer_id)
                      ->where($membershipTableName . '.active = ?', 1)
                      ->where($membershipTableName . ".resource_id NOT IN ($notShowIds)")
                      ->where($membershipTableName . ".resource_id NOT IN ($rejectedStr)")
                      ->where($membershipTableName . ".resource_id NOT IN ($getFriendStr)")
                      ->order('RAND()')
                      ->limit($limit);

      $fetch = $select->query()->fetchAll();
      if (!empty($fetch) && !empty($fetch[0]['resource_id'])) {
        $getMyFriend[$fetch[0]['resource_id']] = $fetch[0]['resource_id'];
      }

      //In this query message come if user send message to all his friend.
      if (empty($getMyFriend)) {
        //Query for pick a friend to whom i've messaged last.
        $select = $recipientTable->select()
                        ->setIntegrityCheck(false)
                        ->from($messageTableName, array())
                        ->joinInner($recipientTableName, "$recipientTableName.conversation_id = $messageTableName.conversation_id", array('user_id'))
                        ->joinInner($membershipTableName, "$membershipTableName.resource_id = $recipientTableName.user_id", array())
                        ->where($membershipTableName . '.user_id = ?', $viewer_id)
                        ->where($membershipTableName . '.active = ?', 1)
                        ->where($membershipTableName . ".resource_id NOT IN ($notShowIds)")
                        ->where($membershipTableName . ".resource_id NOT IN ($rejectedStr)")
                        ->where($messageTableName . '.user_id = ?', $viewer_id)
                        ->where($recipientTableName . '.user_id != ?', $viewer_id)
                        ->order($messageTableName . '.date ASC')
                        ->limit($limit);

        $fetch = $select->query()->fetchAll();
        if (!empty($fetch) && !empty($fetch[0]['user_id'])) {
          $getMyFriend['messagefriend_' . $fetch[0]['user_id']] = $fetch[0]['user_id'];
        }
      }
    }
    return $getMyFriend;
  }

  /**
   * Returns Array of loggden user friend, whoem i have send suggestion already of pass entity.
   */
  public function getSuggestedFriend($entity) {

    $getFriednArray = array();
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $suggestionTable = Engine_Api::_()->getItemtable('suggestion');
    $suggestionTableName = $suggestionTable->info('name');

    $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
    $membershipTableName = $membershipTable->info('name');

    $select = $suggestionTable->select()
                    ->setIntegrityCheck(false)
                    ->from($suggestionTableName, array('owner_id'))
                    ->joinInner($membershipTableName, "$membershipTableName . resource_id = $suggestionTableName . owner_id", null)
                    ->where($suggestionTableName . '.sender_id = ?', $viewer_id)
                    ->where($suggestionTableName . '.entity = ?', $entity)
                    ->where($membershipTableName . '.user_id = ?', $viewer_id)
                    ->where($membershipTableName . '.active = ?', 1);

    $fetch = $select->query()->fetchAll();
    foreach ($fetch as $id) {
      $getFriednArray[] = $id['owner_id'];
    }
    return $getFriednArray;
  }

  /**
   * Recommendation Widgets: Find out the friend of viewer which have few friend.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function friendfewfriend_mix_sugg($modName, $notShowIds, $rejectedStr, $limit) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];
    $getSuggestedFriendStr = null;
    $getFewFriend = array();
    $getModCondition = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModCondition($modName);
    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);
    $userEligibal = Engine_Api::_()->suggestion()->userFriendsEligible();

    // If site admin set "Nobody can friend".
    if ($userEligibal != 0) {
      // Get my all friend array, whoem i have send suggestion of pass entity.
      $getSuggestedFriendArray = $this->getSuggestedFriend('friend');
      if (!empty($getSuggestedFriendArray) && is_array($getSuggestedFriendArray)) {
        $getSuggestedFriendStr = implode(',', $getSuggestedFriendArray);
      }
      $getSuggestedFriendStr = $this->getTrimStr($getSuggestedFriendStr);

      $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
      $membershipTableName = $membershipTable->info('name');

      $userTable = Engine_Api::_()->getItemTable('user');
      $userTableName = $userTable->info('name');

      $select = $userTable->select()
                      ->setIntegrityCheck(false)
                      ->from($userTableName, array())
                      ->joinInner($membershipTableName, "$membershipTableName.resource_id = $userTableName.user_id", array('resource_id'))
                      ->where($membershipTableName . '.user_id = ?', $viewer_id)
                      ->where($membershipTableName . '.active = ?', 1)
                      ->where($userTableName . '.member_count < ?', 10) // Picking up friends having less than 10 friends
                      ->where($userTableName . '.search = ?', 1)
                      ->where($membershipTableName . ".resource_id NOT IN ($notShowIds)")
                      ->where($membershipTableName . ".resource_id NOT IN ($rejectedStr)")
                      ->where($membershipTableName . ".resource_id NOT IN ($getSuggestedFriendStr)")
                      ->order($userTableName . '.member_count ASC')
                      ->limit(15);
      $fetch = $select->query()->fetchAll();
      if (!empty($fetch)) {
        //Randumly pick one from the array.
        $randum_friend_key = array_rand($fetch, 1);
        $randum_one_friend = $fetch[$randum_friend_key];
        $getFewFriend[$randum_one_friend['resource_id']] = $randum_one_friend['resource_id'];
      }
    }
    return $getFewFriend;
  }

  /**
   * Recommendation Widgets: Find out the friend of viewer.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function friend_mix_sugg($modName, $notShowIds, $rejectedStr, $limit) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];
    $getModCondition = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModCondition($modName);
    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);
    $friend_id_array = Engine_Api::_()->suggestion()->suggestion_path($notShowIds, $limit, 'mix');
    return $friend_id_array;
  }

  /**
   * Recommendation Widgets: Find out the friend of viewer which required the photo suggestion.
   *
   * @param $modName: Module Name.
   * @param $notShowIds: string of content ids, which will not be display as a suggestion.
   * @param $rejectedStr: string of content ids, Which has been rejected by viewer after clicking on cross (X). This content will not show as a suggestion.
   * @param $limit: Limit.
   * @return Array.
   */
  public function friendphoto_mix_sugg($modName, $notShowIds, $rejectedStr, $limit) {
    $getSuggestedFriendStr = null;
    $getFriendPhotoArray = array();
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
    $modInfo = $modInfo[$modName];
    $getModCondition = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModCondition($modName);
    $notShowIds = $this->getTrimStr($notShowIds);
    $rejectedStr = $this->getTrimStr($rejectedStr);

    // Get my all friend array, whoem i have send suggestion of pass entity.
    $getSuggestedFriendArray = $this->getSuggestedFriend('photo');
    if (!empty($getSuggestedFriendArray) && is_array($getSuggestedFriendArray)) {
      $getSuggestedFriendStr = implode(',', $getSuggestedFriendArray);
    }
    $getSuggestedFriendStr = $this->getTrimStr($getSuggestedFriendStr);

    $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
    $membershipTableName = $membershipTable->info('name');

    $userTable = Engine_Api::_()->getItemTable('user');
    $userTableName = $userTable->info('name');

    $select = $userTable->select()
                    ->setIntegrityCheck(false)
                    ->from($userTableName, array())
                    ->joinInner($membershipTableName, "$membershipTableName.resource_id = $userTableName.user_id", array('resource_id'))
                    ->where($membershipTableName . '.user_id = ?', $viewer_id)
                    ->where($userTableName . '.photo_id = ?', 0)
                    ->where($membershipTableName . '.active = ?', 1)
                    ->where($userTableName . '.search = ?', 1)
                    ->where($membershipTableName . ".resource_id NOT IN ($notShowIds)")
                    ->where($membershipTableName . ".resource_id NOT IN ($rejectedStr)")
                    ->where($membershipTableName . ".resource_id NOT IN ($getSuggestedFriendStr)")
                    ->order('RAND()')
                    ->limit($limit);
    $fetch = $select->query()->fetchAll();
    if (!empty($fetch)) {
      //Randumly pick one from the array.
      $randum_friend_key = array_rand($fetch, 1);
      $randum_one_friend = $fetch[$randum_friend_key];
      $getFriendPhotoArray[$randum_one_friend['resource_id']] = $randum_one_friend['resource_id'];
    }
    return $getFriendPhotoArray;
  }
}
?>
