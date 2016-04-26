<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Api_SubCore extends Core_Api_Abstract {

  /**
   * Get feeds for group profile group
   *
   * @$user User_Model_User
   * @param array $params
   */
  public function getEveryoneGroupProfileFeeds(Core_Model_Item_Abstract $about, array $params = array()) {
    $ids = array();
    if (!(bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.feed.everyone', 0))
      return $ids;
    //Proc args
    extract($params); //action_id, limit, min_id, max_id

    $actionDbTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $select = $actionDbTable->select();
    if ($about->getType() == 'sitegroup_group') {
      $select->where("(subject_type ='sitegroup_group'  and subject_id = ? ) OR ( (type <> 'sitegroup_new' AND type <> 'like_sitegroup_group') and object_type ='sitegroup_group'  and object_id = ?) ", $about->getIdentity());
    } elseif ($about->getType() == 'sitegroupevent_event') {
      $select->where("(subject_type ='sitegroupevent_event'  and subject_id = ? ) OR ( (type <> 'like_sitegroupevent_event') and object_type ='sitegroupevent_event'  and object_id = ?) ", $about->getIdentity());
    }
    $select->order('action_id DESC')
            ->limit($limit);

    // Add action_id/max_id/min_id
    if (null !== $action_id) {
      $select->where('action_id = ?', $action_id);
    } else {
      if (null !== $min_id) {
        $select->where('action_id >= ?', $min_id);
      } else if (null !== $max_id) {
        $select->where('action_id <= ?', $max_id);
      }
    }
    $results = $actionDbTable->fetchAll($select);
    foreach ($results as $actionData)
      $ids[] = $actionData->action_id;
    return $ids;
  }

  /**
   * Delete Create Activity Feed Of Item Before Delete Item
   *
   * $item
   * @$actionsType array $actionsType
   */
  public function deleteCreateActivityOfExtensionsItem($item, $actionsType=array()) {

    $attachmentsTable = Engine_Api::_()->getDbtable('attachments', 'activity');
    $attachmentsTableName = $attachmentsTable->info('name');
    $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $actionsTableName = $actionsTable->info('name');
    $select = $attachmentsTable->select()
                    ->setIntegrityCheck(false)
                    ->from($attachmentsTableName, array($attachmentsTableName . '.action_id'))
                    ->join($actionsTableName, "`{$attachmentsTableName}`.action_id = `{$actionsTableName}`.action_id  ", null)
                    ->where($attachmentsTableName . '.id = ?', $item->getIdentity())
                    ->where($attachmentsTableName . '.type = ?', $item->getType())
                    ->where($actionsTableName . '.type in(?)', new Zend_Db_Expr("'" . join("', '", $actionsType) . "'"));

    $row = $attachmentsTable->fetchRow($select);
    if (!empty($row)) {
      $action = $actionsTable->fetchRow(array('action_id =?' => $row->action_id));
      if (!empty($action)) {
        $action->deleteItem();
        $action->delete();
      }
    }
  }

  /**
   * Group base network enable
   *
   * @return bool
   */
  public function groupBaseNetworkEnable() {
    return (bool) ( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.networks.type', 0) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.network', 0) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.default.show', 0)));
  }

  /**
   * Content in File or not
   *
   * @return bool
   */
  public function isContentInFile($path, $string) {

    $isContentInFile = 0;
    if (is_file($path)) {
      @chmod($path, 0777);
      $fileData = file($path);
      foreach ($fileData as $key => $value) {
        $pos = strpos($value, $string);
        if ($pos !== false) {
          $isContentInFile = 1;
          break;
        }
      }
    }
    return $isContentInFile;
  }

  /**
   * Activity Feed Widget
   *
   * @return bool
   */
  public function isCoreActivtyFeedWidget($groupName, $widgetName, $params =array()) {
    $isCoreActivtyFeedWidget = false;

    $groupsTable = Engine_Api::_()->getDbtable('pages', 'core');
    $groupsTableName = $groupsTable->info('name');
    $contentsTable = Engine_Api::_()->getDbtable('content', 'core');
    $contentsTableName = $contentsTable->info('name');

    $select = $contentsTable->select()
                    ->setIntegrityCheck(false)
                    ->from($contentsTableName, array($contentsTableName . '.name'))
                    ->join($groupsTableName, "`{$groupsTableName}`.page_id = `{$contentsTableName}`.page_id  ", null)
                    ->where($groupsTableName . '.name = ?', $groupName)
                    ->where($contentsTableName . '.name = ?', $widgetName);
    $row = $contentsTable->fetchRow($select);
    if (!empty($row))
      $isCoreActivtyFeedWidget = true;
    return $isCoreActivtyFeedWidget;
  }

  /**
   * Activity Feed Widget
   *
   * @return bool
   */
  public function isGroupCoreActivtyFeedWidget($widgetName, $params =array()) {
    $isCoreActivtyFeedWidget = false;
    $contentsTable = Engine_Api::_()->getDbtable('admincontent', 'sitegroup');
    $select = $contentsTable->select()
                    ->where('name = ?', $widgetName);
    $row = $contentsTable->fetchRow($select);
    if (!empty($row))
      $isCoreActivtyFeedWidget = true;
    return $isCoreActivtyFeedWidget;
  }
  
  /**
   * GET TRUE OR FALSE FOR SAMPLE AD WIDGET
   *
   * @return bool
   */
  public function getSampleAdWidgetEnabled($sitegroup) {   
    
    //CHECK GROUP OWNER IS THERE OR NOT
    $isManageAdmin = Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup);
    if(!$isManageAdmin) {
      return false;
    }   
    
    //CHECK WHETHER THE SITEGROUP MODULE IN THE COMMUNITYAD TABLE OR NOT
    $ismoduleads_enabled = Engine_Api::_()->getDbtable('modules', 'communityad')->ismoduleads_enabled("sitegroup");
		if(!$ismoduleads_enabled) {
			return false;
		}  
    
    //CHECK WHETHER THE AD BELONG TO THE SITEGROUP MODULE OR NOT
    $useradsTable = Engine_Api::_()->getDbtable('userads', 'communityad');
		$select = $useradsTable->select();
    $select
        ->from($useradsTable->info('name'), array('userad_id'))
				->where('resource_type = ?', "sitegroup")
        ->where('resource_id = ?', $sitegroup->group_id)
        ->limit(1);
		$ad_exist = $useradsTable->fetchRow($select);
    if(!empty($ad_exist)) {
			return false;
		}
    
    //CHECK THE CREATE LINK OR ADPREVIEW LINK YES OR NOT FROM THE ADMIN
    if((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adcreatelink', 1) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adpreview', 1))) {
      return true;
    }    
  }
  
    /**
   * GET GROUPS ON WHICH HE HAS ACTIVTYF FEED
   * 
   * @$member User_Model_User
   * @return bool
   */
  public function getMemberFeedsForGroupOfIds($member) {
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $groupids = $streamTable->select()
            ->from($streamTable->info('name'), "target_id")
            ->where('subject_id = ?', $member->getIdentity())
            ->where('subject_type = ?', $member->getType())
            ->where('target_type = ?', 'sitegroup_group')
            ->group('target_id')
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
    $ids = array();
    foreach ($groupids as $id) {
      $group = Engine_Api::_()->getItem('sitegroup_group', $id);
      if (empty($group) || !$group->isViewableByNetwork())
        continue;
      $ids[] = $id;
    }
    return $ids;
  }

  /**
   * DELETE ACTIVITY FEED STREAM PRIVACY
   * 
   * @$member User_Model_User
   * @return bool
   */
  public function deleteFeedStream($action, $onlyCheckForNetwork = false) {
    if (empty($action))
      return;
    $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
    if (!$onlyCheckForNetwork && !empty($settingsCoreApi->sitegroup_feed_type) && !empty($settingsCoreApi->sitegroup_feed_onlyliked)) {
      $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
      $streamTable->delete(array(
          'action_id = ?' => $action->getIdentity(),
          'target_type NOT IN(?)' => array('sitegroup_group', 'owner', 'parent')
      ));
    }

    $enableNetwork = $this->groupBaseNetworkEnable();
    $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.networkprofile.privacy', 0);
    if ($enableNetwork && $viewPricavyEnable && $action->object_type = 'sitegroup_group') {
      $sitegroup = $action->getObject();
      if ($sitegroup->networks_privacy) {
        $groupNetworkIds = explode(",", $sitegroup->networks_privacy);
        if (count($groupNetworkIds)) {
          $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
          $streamTable->delete(array(
              'action_id = ?' => $action->getIdentity(),
              'target_type IN (?)' => array('everyone', 'registered', 'network', 'members')
          ));
        }
        $target_type = 'network';
        foreach ($groupNetworkIds as $target_id) {
          $streamTable->insert(array(
              'action_id' => $action->action_id,
              'type' => $action->type,
              'target_type' => (string) $target_type,
              'target_id' => (int) $target_id,
              'subject_type' => $action->subject_type,
              'subject_id' => $action->subject_id,
              'object_type' => $action->object_type,
              'object_id' => $action->object_id,
          ));
        }
      }
    }
  }

  /**
   * DELETE ACTIVITY FEED STREAM PRIVACY WHICH ARE NOT NEED
   * 
   * @$member User_Model_User
   * @return bool
   */
  public function getGroupFeedActionIds() {

    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $actionIds = $streamTable->select()
            ->from($streamTable->info('name'), "action_id")
            ->where('target_type = ?', 'sitegroup_group')
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);

    if (!empty($actionIds)) {
      $streamTable->delete(array(
          'action_id  In(?)' => $actionIds,
          'target_type <> ?' => 'sitegroup_group'
      ));
    }
  }

}
?>