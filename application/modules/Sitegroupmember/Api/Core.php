<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitegroupmember_Api_Core extends Core_Api_Abstract {

	public function joinLeave($resource, $params = null) {

		//GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
		
		$resource_id = $resource->getIdentity();
		
		$hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $resource_id);
		$owner = $resource->getOwner();
		
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
		
			if ($params == 'Join' && Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.automatically.join')) {
			
				if (empty($hasMembers)) {
				
					$membersTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
					$row = $membersTable->createRow();
					$row->resource_id = $resource_id;
					$row->group_id = $resource_id;
					$row->user_id = $viewer_id;
					
					if (empty($resource->member_approval)) {
						$row->active = 0;
						$row->resource_approved = 0;
						$row->user_approved = 0;
						
						//Get manage admin and send notifications to all manage admins.
						$manageadmins = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdmin($resource_id);
						
						foreach($manageadmins as $manageadmin) {
							$user_subject = Engine_Api::_()->user()->getUser($manageadmin['user_id']);
							Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $resource, 'sitegroupmember_approve');
						}
					}
					else {
					
						//Set the request as handled for Notifaction.
						Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $resource, 'sitegroup_join');

						//Add activity
						Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $resource, 'sitegroup_join');
						
						//Member count increase when member join the group.
            Engine_Api::_()->sitegroup()->updateMemberCount($resource);
						$resource->save();
					}
					
					//If member is already featured then automatically featured when member join the any group.
					if(!empty($hasMembers->featured)) {
						$row->featured = 1;
					}
					
					$row->save();
				}
			}
			elseif($params == 'Leave') {
			
				if (!empty($hasMembers)) {
				
					//DELETE THE RESULT FORM THE TABLE.
					Engine_Api::_()->getDbtable('membership', 'sitegroup')->delete(array('resource_id =?' => $resource_id, 'user_id = ?' => $viewer_id));

					//Delete activity feed of join group according to user id.
					$action_id = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type = ?'  => 'sitegroup_join', 'subject_id = ?' => $viewer_id, 'object_id = ?' => $resource_id));
					$action = Engine_Api::_()->getItem('activity_action', $action_id->action_id);
					$action->delete();

					//Remove the notification.
					$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($owner, $resource, 'sitegroup_join');
					if($notification) {
						$notification->delete();
					}

					if (!empty($hasMembers->active)) {
					
						//Member count decrease in the group table when member leave the group.
						$resource->member_count--;
						$resource->save();
					}
				}
			}
		}
	}
	
	
	public function getMoreGroup($groupIds, $group_title) {
	
		$memberstable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
		$tableMemberName = $memberstable->info('name'); 

		$tableGroup = Engine_Api::_()->getDbtable('groups', 'sitegroup');
		$tableGroupName = $tableGroup->info('name');

		$select = $tableGroup->select()
											->setIntegrityCheck(false)
											->from($tableGroupName, array('group_id','title','photo_id'))
											->joinleft($tableMemberName, $tableGroupName . ".group_id = " . $tableMemberName . '.resource_id', null)
											->where($tableGroupName . ".title LIKE ? OR " . $tableGroupName . ".body LIKE ? ", '%' . $group_title . '%')
											->where($tableGroupName . '.group_id NOT IN (?)', (array) $groupIds)
											->where($tableGroupName . '.closed = ?', '0')
											->where($tableGroupName . '.approved = ?', '1')
											->where($tableGroupName . '.search = ?', '1')
											->where($tableGroupName . '.declined = ?', '0')
											->where($tableGroupName . '.draft = ?', '1')
											->group('group_id');
		$moeGroups = $tableGroup->fetchAll($select);
	  return $moeGroups;
	}
	/**
   * Plugin which return the error, if Siteadmin not using correct version for the plugin.
   *
   */
  public function isModulesSupport($modName = null) {
    if( empty($modName) ) {
    $modArray = array(
      'sitegroupalbum' => '4.5.0',
      'sitegroupbadge' => '4.5.0',
      'sitegroupdocument' => '4.5.0',
      'sitegroupevent' => '4.5.0',
      'sitegroupmusic' => '4.5.0',
      'sitegroupnote' => '4.5.0',
      'sitegroupoffer' => '4.5.0',
      'sitegrouppoll' => '4.5.0',
      'sitegroupreview' => '4.5.0',
      'sitegroupurl' => '4.5.0',
      'sitegroupvideo' => '4.5.0',
      'sitelike' => '4.5.0'
    );
    } else {
      $modArray[$modName['modName']] = $modName['version'];
    }
    $finalModules = array();
    foreach ($modArray as $key => $value) {
      $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
      if (!empty($isModEnabled)) {
        $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
        $isModSupport = $this->checkVersion($getModVersion->version, $value);
        if (empty($isModSupport)) {
          $finalModules[] = $getModVersion->title;
        }
      }
    } 
    return $finalModules;
  }
  
      private function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }
}