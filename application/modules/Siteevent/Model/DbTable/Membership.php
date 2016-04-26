<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Membership.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Membership extends Core_Model_DbTable_Membership {

    protected $_type = 'siteevent_event';
    protected $_serializedColumns = array('notification');

    /**
     * Does membership require approval of the resource?
     *
     * @param Core_Model_Item_Abstract $resource
     * @return bool
     */
    public function isResourceApprovalRequired(Core_Model_Item_Abstract $resource) {
        //IF THE VIEWER IS OWNER OF THE EVENT THEN WE WILL RETURN ALWAYS FALSE HERE

        $viewer = Engine_Api::_()->user()->getViewer();
        if ($resource->owner_id != $viewer->getIdentity())
            return $resource->approval;

        return false;
    }

    /**
     * Add $user as member to $resource
     *
     * @param Core_Model_Item_Abstract $resource
     * @param User_Model_User $user
     * @return Core_Model_DbTable_Membership
     */
    public function addMember(Core_Model_Item_Abstract $resource, User_Model_User $user) {
        $this->_isSupportedType($resource);
        $row = $this->getRow($resource, $user);
        $viewer = Engine_Api::_()->user()->getViewer();
        if (null !== $row) {
            throw new Core_Model_Exception('That user is already a member');
        }
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if (isset($request->occurrence_id) && !empty($request->occurrence_id))
            $occurrence_id = $request->occurrence_id;
        else
            $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;

        if (!$occurrence_id) {
            $occurrence_id = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($resource->getIdentity(), 1);
        }

        $id = $resource->getIdentity() . '_' . $user->getIdentity();
        $row = $this->getTable()->createRow();
        $row->setFromArray(array(
            'resource_id' => $resource->getIdentity(),
            'user_id' => $user->getIdentity(),
            'resource_approved' => !$this->isResourceApprovalRequired($resource),
            'user_approved' => 0,
            'active' => 0,
            'occurrence_id' => $occurrence_id
        ));

        $row->save();

				//GET THE LEADERS LIST AND CHECK IF THE VIEWER IS LEADER OR NORMAL USER.
				if ($resource->owner_id == $viewer->getIdentity()) {
					$isLeader = 1;
				} else { 
						$list = $resource->getLeaderList();
						$listItem = $list->get($viewer);
						$isLeader = ( null !== $listItem );
				}

        if($isLeader) {
					$row->notification = '{"email":"1","notification":"1","action_notification":["posted","created","joined","comment","like","follow","rsvp","title","location","time","venue"],"action_email":["posted","created","joined","rsvp"]}';
				} else {
					$row->notification = '{"email":"0","notification":"1","action_notification":["posted","created","joined","title","location","time","venue"],"action_email":["posted","created"]}';
				}

        $row->save();
        $this->_rows[$id] = $row;
        $this->_checkActive($resource, $user);

        return $this;
    }

    public function removeMember(Core_Model_Item_Abstract $resource, User_Model_User $user) {
        $this->_isSupportedType($resource);
        $row = $this->getRow($resource, $user);

        if (null === $row) {
            throw new Core_Model_Exception("Membership does not exist");
        }
//
//        if (isset($resource->member_count) && $row->active) {
//            $resource->member_count--;
//            $resource->save();
//        }
       
        $row->delete();
        $member_count = $resource->membership()->getMemberCount();
        $resource->member_count = $member_count;
        $resource->save();

        return $this;
    }

    // General

    /**
     * Gets the row associated with the specified resource/member pair in the db
     *
     * @param Core_Model_Item_Abstract $resource
     * @param User_Model_User $user
     * @return Engine_Db_Table_Row|false
     */
    public function getRow(Core_Model_Item_Abstract $resource, User_Model_User $user, $params = array()) {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $occurrence_id = isset($params['occurrence_id']) ? $params['occurrence_id'] : 0;
        if (empty($occurrence_id)) {
            $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
        }
        if (empty($occurrence_id) && isset($request->occurrence_id) && !empty($request->occurrence_id))
            $occurrence_id = $request->occurrence_id;

        if (!empty($occurrence_id))
            $id = $occurrence_id . '_' . $user->getIdentity();
        else
            $id = $resource->getIdentity() . '_' . $user->getIdentity();
        if (!isset($this->_rows[$id])) {
            $table = $this->getTable();
            $select = $table->select()
                    ->where('resource_id = ?', $resource->getIdentity())
                    ->where('user_id = ?', $user->getIdentity());
            if (!empty($occurrence_id))
                $select->where('occurrence_id = ?', $occurrence_id);

            $select = $select->limit(1);
            $row = $table->fetchRow($select);
            //if( $row === null )
            //{
            //  $this->_rows[$id] = false;
            //}
            //else
            //{
            $this->_rows[$id] = $row;
            //}
        }
        return $this->_rows[$id];
    }

    /**
     * Gets the number of members a resource has
     *
     * @param Core_Model_Item_Abstract $resource
     * @param bool $active
     * @return int
     */
    public function getMemberCount(Core_Model_Item_Abstract $resource, $active = true, $other_conditions = array()) {
//    if( isset($resource->member_count) && $active && empty($other_conditions))
//    {
//      return $resource->member_count;
//    }
//    else
//    {
        $table = $this->getTable();
        $select = new Zend_Db_Select($table->getAdapter());
        $select->from($table->info('name'), new Zend_Db_Expr('COUNT(DISTINCT user_id) as member_count'))
                ->where('resource_id = ?', $resource->getIdentity());
        if (null != $active) {
            $select->where('active = ?', (bool) $active);
        }
        foreach ($other_conditions as $condition_name => $condition_value) {

            $select = $select->where($condition_name . '= ?', $condition_value);
        }
     
        $row = $table->getAdapter()->fetchRow($select);
        return $row['member_count'];
        // }
    }

    //GET INVITE COUNT

    public function getInviteCount($user_id) {

        $table = $this->getTable();
        $select = $table->select()
                ->from($this->info('name'), array('COUNT(*) AS count'))
                ->where('rsvp = ?', 3)
                ->where('active = ?', 0)
                ->where('user_approved = ?', 0)
                ->where('user_id = ?', $user_id);
        $totalEvents = $select->query()->fetchColumn();
        return $totalEvents;
    }

    //DELETE MEMBER
    public function deleteEventMember($user, $event_id) {
        if ($event_id)
            $this->delete(array('resource_id = ?' => $event_id));
    }

    //DELETE MEMBER
    public function deleteOccurrenceEventMember($occurrence_id) {
        if ($occurrence_id) {
            $totalMembers = $this->getOccurrenceMemberCount(array('occurrence_id' => $occurrence_id));
            $this->delete(array('occurrence_id = ?' => $occurrence_id));
            return $totalMembers;
        }
    }

    public function getMembersObjectSelect(Core_Model_Item_Abstract $resource, $active = true) {
        $table = Engine_Api::_()->getDbtable('users', 'user');
        $subtable = $this->getTable();
        $tableName = $table->info('name');
        $subtableName = $subtable->info('name');

        $select = $table->select()
                ->setIntegrityCheck(false)
                ->from($tableName)
                ->joinRight($subtableName, '`' . $subtableName . '`.`user_id` = `' . $tableName . '`.`user_id`', array('occurrence_id', 'rsvp'))
                ->where('`' . $subtableName . '`.`resource_id` = ?', $resource->getIdentity())
        ;

        if ($active !== null) {
            $select->where('`' . $subtableName . '`.`active` = ?', (bool) $active);
        }

        return $select;
    }

    public function getMembersIds(Core_Model_Item_Abstract $resource, $active = true) {
        $ids = array();
        $select = $this->getMembersSelect($resource, $active);
        foreach ($this->fetchAll($select) as $row) {
            $ids[] = $row->user_id;
        }
        return $ids;
    }

    public function getMemberInfoCustom(Core_Model_Item_Abstract $resource, User_Model_User $user, $params = array()) {

        $table = $this->getTable();
        $select = $table->select()
                ->where('resource_id = ?', $resource->getIdentity())
                ->where('user_id = ?', $user->getIdentity());
        if (isset($user->occurrence_id))
            $select->where('occurrence_id = ?', $user->occurrence_id);
        elseif (isset($params['occurrence_id']))
            $select->where('occurrence_id = ?', $params['occurrence_id']);
        $select = $select->limit(1);
        $row = $table->fetchRow($select);
        return $row;
    }

    public function isMember(Core_Model_Item_Abstract $resource, User_Model_User $user, $active = null) {
        $this->_isSupportedType($resource);
        $row = $this->getRow($resource, $user);
        if ($row === null) {
            return false;
        }

        if (null === $active) {
            return true;
        }

        return ( $active == $row->active );
    }

    public function isEventMember(Core_Model_Item_Abstract $resource, User_Model_User $user, $active = null) {
        $this->_isSupportedType($resource);

        $table = $this->getTable();
        $select = $table->select()
                ->where('resource_id = ?', $resource->getIdentity())
                ->where('user_id = ?', $user->getIdentity());

        $row = $table->fetchRow($select);
        if ($row === null) {
            return false;
        }

        if (null === $active) {
            return true;
        }

        return ( $active == $row->active );
    }

    public function hasEventMember(Core_Model_Item_Abstract $resource, User_Model_User $user, $active = true) {
        //IF SITEREPEAT EVENT MODULE IS NOT ENABLED THEN WE WILL ALWAYS SEND TRUE HERE.
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat'))
            return true;

        $this->_isSupportedType($resource);
        $membershipTableName = $this->info('name');

        $host = $resource->getHost();
        //GET LEADER LIST OF EVENTS
        $leaderList = $resource->getLeaderList();
        if ($leaderList->child_count)
            $eventLeaders = Engine_Api::_()->getItemTable('siteevent_list')->getLeaders($leaderList->list_id);

        $eventLeadersTotal = $resource->owner_id;
        if (!empty($eventLeaders))
            $eventLeadersTotal = $eventLeadersTotal . ',' . $eventLeaders;
        if ($host && $host->getType() == 'user')
            $eventLeadersTotal = $eventLeadersTotal . ',' . $host->getIdentity();


        // $table = $this->getTable();
        $table = $this->getTable();
        $select = $table->select()
                ->where('resource_id = ?', $resource->getIdentity())
                ->where('user_id NOT IN (' . $eventLeadersTotal . ')')
                ->where('resource_approved = ?', 1)
                ->where('user_approved = ?', 1);
        if ($active)
            $select->where('active = ?', $active);

        $row = $table->fetchRow($select);
        if ($row === null) {
            return true;
        }

        return false;
    }

    /**
     * Return member object
     *
     * @param int $memberId
     * @return Zend_Db_Table_Select
     */
    public function getMembersObject($memberId) {

        $membershipTableName = $this->info('name');
        $select = $this->select()
                ->where($membershipTableName . '.member_id = ?', $memberId);
        return $this->fetchRow($select);
    }

    /**
     * Check member is join.
     *
     * @param int $viewer_id
     * @param int $group_id
     * @param string $params
     * @return Zend_Db_Table_Select and paginator
     */
    public function hasMembers($viewer_id, $event_id = NULL, $params = NULL) {

        $membershipTableName = $this->info('name');

        $select = $this->select()
                ->from($membershipTableName)
                ->where('user_id = ?', $viewer_id);

        if (!empty($event_id)) {
            $select->where($membershipTableName . '.resource_id = ?', $event_id);
        }

        if ($params == 'Leave') {
            $select->where('active = ?', 1);
        }

        if ($params == 'Cancel' || $params == 'Accept' || $params == 'Reject') {
            $select->where('active = ?', 0);
        }
        if ($params == 'Cancel') {
            $select->where('resource_approved = ?', 0)
                    ->where('user_approved = ?', 0);
        }

        if ($params == 'Accept') {
            $select->where('resource_approved = ?', 1);
        }

        if ($params == 'Invite') {
            $select->where('resource_approved = ?', 1)
                    ->where('user_approved = ?', 1)
                    ->where('active = ?', 1);
        }
        $select = $this->fetchRow($select);

        return $select;
    }

    public function getOccurrenceMemberCount($params = array()) {
        
        $select = $this->select()->from($this->info('name'), array('COUNT(*) AS count'));
        
        if(!empty($params['occurrence_id'])) {
            $select->where('occurrence_id = ?', $params['occurrence_id']);
        }
        
        if(!empty($params['rsvp'])) {
            $select->where('rsvp = ?', $params['rsvp']);
        }        
        
        return $select->query()->fetchColumn();
    }

    public function isMemberOfPastOccurrence(Core_Model_Item_Abstract $resource, User_Model_User $user, $active = null) {

        $membershipTableName = $this->info('name');
        $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
        $occurrenceTableName = $occurrenceTable->info('name');

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($membershipTableName, array())
                ->join($occurrenceTableName, "$occurrenceTableName.occurrence_id = $membershipTableName.occurrence_id", array('occurrence_id'))
                ->where($occurrenceTableName . ".endtime < NOW()")
                ->where($membershipTableName . '.resource_id = ?', $resource->getIdentity())
                ->where($membershipTableName . '.user_id = ?', $user->getIdentity());

        if ($active !== null) {
            $select->where("$membershipTableName.active = ?", (bool) $active);
        }

        $results = $this->fetchAll($select);

        return Count($results);
    }
    
    /**
   * Set membership as being approved by the resource
   *
   * @param Core_Model_Item_Abstract $resource
   * @param User_Model_User $user
   * @return Core_Model_DbTable_Membership
   */
  public function setResourceApproved(Core_Model_Item_Abstract $resource, User_Model_User $user)
  {
    $this->_isSupportedType($resource);
    $row = $this->getRow($resource, $user);

    if( null === $row )
    {
      throw new Core_Model_Exception("Membership does not exist");
    }

    if( !$row->resource_approved )
    {
      $row->resource_approved = true;
      if( $row->resource_approved && $row->user_approved )
      {
        $row->active = true;
        if( isset($resource->member_count) )
        {
          $resource->member_count++;
          $resource->save();
        }
      }
      $this->_checkActive($resource, $user);
      $row->save();
    }

    $member_count = $resource->membership()->getMemberCount();
    $resource->member_count = $member_count;
    $resource->save();
    return $this;
  }

  /**
   * Set membership as being approved by the user
   *
   * @param Core_Model_Item_Abstract $resource
   * @param User_Model_User $user
   * @return Core_Model_DbTable_Membership
   */
  public function setUserApproved(Core_Model_Item_Abstract $resource, User_Model_User $user)
  {
    $this->_isSupportedType($resource);
    $row = $this->getRow($resource, $user);

    if( null === $row )
    {
      throw new Core_Model_Exception("Membership does not exist");
    }

    if( !$row->user_approved )
    {
      $row->user_approved = true;
      if( $row->resource_approved && $row->user_approved )
      {
        $row->active = true;
        if( isset($resource->member_count) )
        {
          $resource->member_count++;
          $resource->save();
        }
      } 
      $this->_checkActive($resource, $user);
      $row->save();
    }
     $member_count = $resource->membership()->getMemberCount();
    $resource->member_count = $member_count;
    $resource->save();
    return $this;
  }
  
  // Utility

  /**
   * Used to check and update active status after addMember, set*Approved
   *
   * @param Core_Model_Item_Abstract $resource
   * @param User_Model_User $user
   */
  protected function _checkActive(Core_Model_Item_Abstract $resource, User_Model_User $user)
  {
    $row = $this->getRow($resource, $user);

    if( null === $row )
    {
      throw new Core_Model_Exception("Membership does not exist");
    }

    if( $row->resource_approved && $row->user_approved && !$row->active )
    {
      $row->active = true;
      $row->save();
      if( isset($resource->member_count) ) {
        $resource->member_count++;
        $resource->save();
      }
    }
  }
  
  //GET ALL EVENT MEMBERS IDS OF AN EVENT
  
  public function getEventMembers(Core_Model_Item_Abstract $resource, $event_id, $occurrence_id = null, $active = null, $excludeNotAttending = 0) {
    if(empty($event_id)) return;
    
    $select = $this->select()
                ->from($this->info('name'), 'user_id')
                ->where('resource_id = ?', $event_id);                
    if($occurrence_id)
                $select->where('occurrence_id = ?', $occurrence_id);
    if ($active !== null) {
        $select->where("active = ?", (bool) $active);
    }
    
    if(!empty($excludeNotAttending)) {
        $select->where("rsvp = ?", 2);
    }
    
    $user_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
    return $user_ids;
  }
  
  //GET THE FRIENDS OF VIEWER WHO HAS JOINED THIS EVENT.
  public function getEventFriends(Core_Model_Item_Abstract $resource, $params = array()) {
     $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();     
     $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
     $membershipTableName = $membershipTable->info('name');
     $userTable = Engine_Api::_()->getItemTable('user');
     $userTableName = $userTable->info('name');
     $occurrence_id = null;
     if(isset($params['occurrence_id']))
       $occurrence_id = $params['occurrence_id'];
     $user_ids_Joined = $resource->membership()->getEventMembers($resource->event_id, $occurrence_id, true);
     if(in_array($user_id, $user_ids_Joined)) {
       $key = array_search($user_id, $user_ids_Joined);
       unset($user_ids_Joined[$key]);
     }
     if(empty($user_ids_Joined)) return;
     
      $select = $membershipTable->select()
                  ->setIntegrityCheck(false)                 
                  ->where($membershipTableName . '.user_id = ?', $user_id)
                  ->where($userTableName . '.verified = ?', 1)
                  ->where($membershipTableName . '.active = ?', 1)
                  ->where($userTableName . '.enabled = ?', 1)
                  ->order($userTableName . '.member_count DESC');
     $select->where($membershipTableName . '.resource_id IN (?)', (array) $user_ids_Joined);
     
     //FIRST CHECK IF COUNT SET THEN WE WILL MAKE A QUERY FOR COUNT ALSO.
     if(isset($params['count'])) {
       $select_count = clone $select; 
       $select_count 
                ->from($membershipTableName, array('COUNT(*) AS count'))
                ->joinLeft($userTableName, "$userTableName.user_id = $membershipTableName.resource_id", null);
       $totalFriends = $select_count->query()->fetchColumn();
     }
     $select     
            ->from($membershipTableName, array('resource_id'))
            ->joinLeft($userTableName, "$userTableName.user_id = $membershipTableName.resource_id", array('username', 'displayname')); 
     if(isset($params['limit']))
       $select->limit($params['limit']);
    $friendsInfo = $friends =  $select->query()->fetchAll();
    if(isset($totalFriends)) 
      $friendsInfo = array('friends_count' => $totalFriends, 'friends' => $friends);
    return $friendsInfo;
    
  }

}
