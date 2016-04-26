<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_SitemobileIndexController extends Core_Controller_Action_Standard {

  //ACTION FOR SAVING THE LOCATION
  public function saveLocationAction() {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
   
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $isajax = (int) $this->_getParam('isajax', 0);

    $getMapInfo = Engine_Api::_()->sitetagcheckin()->getMapInfo();

	  if( !$getMapInfo )
		return $this->_forward('notfound', 'error', 'core');

    //GET POST DATA
    $postData = $this->getRequest()->getPost();

    //GET SUBJECT
    $subject = isset($postData['subject']) ? $postData['subject'] : null;

    //GET ITEM
    $getItem = Engine_Api::_()->getItemByGuid($subject);

    //MAKE CHECKIN ARRAY
    $checkin_params = array();

    //GET CHECKIN CONTENT
    $checkin = $postData['checkin'];

    //GET AUTOSUGGEST LOCATION
    $location = $postData['location'];

    //PARSING THE CHECKIN DATA INTO AN ARRAY
    if(empty($isajax)) {
      parse_str($checkin, $checkin_params);
    } else {
      $checkin_params = $checkin;
    }
    $notification_location="";
    //GET RESOURCE TYPE
    $resource_type = $getItem->getType();

    //GET RESOURCE ID
    $resource_id = $getItem->getIdentity();

    //GET GUID
    $resource_guid = isset($checkin_params['resource_guid']) ? $checkin_params['resource_guid'] : 0;

    //GET TYPE
    $type = isset($checkin_params['type']) ? $checkin_params['type'] : '';

    if (!empty($resource_guid)) {
      //GET ITEM OF AUTOSUGGEST PAGES / BUSINESSES
      $item = Engine_Api::_()->getItemByGuid($resource_guid);

      //GET HREF
      $href = $item->getHref();

      //GET TITLE
      $getTitle = $item->getTitle();

      //GET LOCAITON
      $notification_location = "<a href='$href'>$getTitle</a>";

      //GET LOCATION
      $location = $item->location;
    }

    //GET ADD LOCATION TABLE
    $tableAddLocation = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');

    //GET LOCATION ID
    $location_id = 0;
    if (!empty($location) && $type != 'just_use') {
      $location_id = $tableAddLocation->getLocationId($location);
    }

    //IF EMPTY LOCATION THE SET CHECKIN PARAM NULL
    if (empty($location))
      $checkin_params = null;

    //ACTION IDENTITY
    $actionIdentity = 0;

    //GET ACTION TABLE
    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $actionTableName = $actionTable->info('name');
    //GET ATTACHMENT TABLE
    $attachmentsTable = Engine_Api::_()->getDbtable('attachments', 'activity');
    $attachmentsTableName = $attachmentsTable->info('name');
    //SELECT ACTION IDS
    $select = $actionTable->select()->from($actionTable->info('name'))
            ->where('type =?', 'sitetagcheckin_location')
            ->where('object_type =?', $resource_type)
            ->where('object_id =?', $resource_id)
            ->where('subject_id =?', $viewer_id);

    $action = $actionTable->fetchRow($select);

    if(!isset($checkin_params['label']))
     $checkin_params['label'] ='';

    //SET PARAMS
    $params = array('checkin' => $checkin_params);

    //GET PHOTO ITEM
    $photo = Engine_Api::_()->getItem($resource_type, $resource_id);

    //IF ACTION IS EXISTIN THEN DELETE
    if (!empty($action)) {
      $action->delete();
    }

    //GET PREFIX
    if (isset($checkin_params['prefixadd'])) {
      $prefixadd = strtolower($checkin_params['prefixadd']);
    } else {
      $prefixadd = $this->view->translate('at');
    }
    $is_mobile = Engine_Api::_()->seaocore()->isMobile();
    //SEND FEED
    //if (!empty($location)) {

      //SELECT ACTION IDS
      if ($resource_type == 'album') {
        $select = $actionTable->select()->from($actionTable->info('name'))
                ->where("$actionTableName.object_id =?", $resource_id)
                ->where("$actionTableName.object_type =?", $resource_type)
                ->order("$actionTableName.action_id DESC")
        ;
      } elseif ($resource_type == 'advalbum_album') {
        $select = $actionTable->select()->from($actionTable->info('name'))
                ->where("$actionTableName.object_id =?", $resource_id)
                ->where("$actionTableName.object_type =?", $resource_type)
                ->order("$actionTableName.action_id DESC")
        ;
      } elseif ($resource_type == 'sitepage_album') {
        $content_page_id = $postData['content_page_id'];
        $select = $actionTable->select()->from($actionTable->info('name'))
                ->where("$actionTableName.object_id =?", $content_page_id)
                ->where("$actionTableName.object_type =?", 'sitepage_page')
                ->order("$actionTableName.action_id DESC")
        ;
      } elseif ($resource_type == 'sitebusiness_album') {
        $content_business_id = $postData['content_business_id'];
        $select = $actionTable->select()->from($actionTable->info('name'))
                ->where("$actionTableName.object_id =?", $content_business_id)
                ->where("$actionTableName.object_type =?", 'sitebusiness_business')
                ->order("$actionTableName.action_id DESC")
        ;
      } elseif ($resource_type == 'sitegroup_album') {
        $content_group_id = $postData['content_group_id'];
        $select = $actionTable->select()->from($actionTable->info('name'))
                ->where("$actionTableName.object_id =?", $content_group_id)
                ->where("$actionTableName.object_type =?", 'sitegroup_group')
                ->order("$actionTableName.action_id DESC")
        ;
      } elseif ($resource_type == 'sitestore_album') {
        $content_store_id = $postData['content_store_id'];
        $select = $actionTable->select()->from($actionTable->info('name'))
                ->where("$actionTableName.object_id =?", $content_store_id)
                ->where("$actionTableName.object_type =?", 'sitestore_album')
                ->order("$actionTableName.action_id DESC")
        ;
      } else {
        $check_object_array = array('user');
        $select = $actionTable->select()->setIntegrityCheck(false)->from($actionTable->info('name'), array('*'))
                ->join($attachmentsTableName, "$attachmentsTableName.action_id = $actionTableName.action_id", null)
                ->where("$attachmentsTableName.id =?", $resource_id)
                ->where("$attachmentsTableName.type =?", $resource_type)
                ->where("$actionTableName.object_type in (?)", $check_object_array)
                ->order("$actionTableName.action_id DESC");
      }
      $res = $actionTable->fetchRow($select);

      if (empty($res)) {
        if(!empty($location)) {
					$action = $actionTable->addActivity($viewer, $getItem, 'sitetagcheckin_location', null, array('prefixadd' => $prefixadd, 'checkin' => $checkin_params));
					if ($action) {
						$actionTable
										->attachActivity($action, $getItem);
					}
					$actionIdentity = $action->getIdentity();

					//IF EMPTY RESOURCE ID THEN SET LOCATION
					if (empty($resource_guid)) {
						if ($type == 'just_use') {
							$notification_location = $checkin_params['label'];
						} else {
              if(isset($checkin_params['vicinity'])) {
								if(isset($checkin_params['name']) && $checkin_params['name'] && $checkin_params['name'] != $checkin_params['vicinity']) {
									$checkin_params['label'] = $checkin_params['name'] . ', ' . $checkin_params['vicinity'];
								} else {
									$checkin_params['label'] = $checkin_params['vicinity'];
								}
              }
 
							if (!$is_mobile) {
							  $notification_location = $this->view->htmlLink($this->view->url(array('guid' => $action->getGuid(),'format'=>'smoothbox'), 'sitetagcheckin_viewmap', true), $checkin_params['label'], array('class' => 'smoothbox')); 
							} else {
							  $notification_location = $this->view->htmlLink($this->view->url(array('guid' => $action->getGuid()), 'sitetagcheckin_viewmap', true), $checkin_params['label']);
							}
						}
					}
					$action->params = array_merge((array) $action->params, array('location' => $notification_location));
					$actionIdentity = $action->save();
	
					if($resource_type == 'album_photo') {
						$content = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.content', 'everyone');
						if (!Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->isLessThan417AlbumModule()) {
							$getAlbum = $photo->getAlbum();
							$objectParent = $getAlbum->getParent();
						} else {
							$getAlbum = $photo->getCollection();
							$objectParent = $getAlbum->getParent();
						}

						// Network
						if( in_array($content, array('everyone', 'networks')) ) {
							if ($getAlbum instanceof User_Model_User
									&& Engine_Api::_()->authorization()->context->isAllowed($getAlbum, 'network', 'view') ) {
								$networkTable = Engine_Api::_()->getDbtable('membership', 'network');
								$ids = $networkTable->getMembershipsOfIds($getAlbum);
								$ids = array_unique($ids);
								foreach( $ids as $id ) {
									Engine_Api::_()->sitetagcheckin()->insertPrivacyInStream('network', $id, $action);
								}
							} elseif ($objectParent instanceof User_Model_User
									&& Engine_Api::_()->authorization()->context->isAllowed($getAlbum, 'owner_network', 'view') ) {
								$networkTable = Engine_Api::_()->getDbtable('membership', 'network');
								$ids = $networkTable->getMembershipsOfIds($objectParent);
								$ids = array_unique($ids);
								foreach( $ids as $id ) {
									Engine_Api::_()->sitetagcheckin()->insertPrivacyInStream('network', $id, $action);
								}
							}
						}

						// Members
						if( $getAlbum instanceof User_Model_User ) {
							if( Engine_Api::_()->authorization()->context->isAllowed($getAlbum, 'member', 'view') ) {
								Engine_Api::_()->sitetagcheckin()->insertPrivacyInStream('members', $getAlbum->getIdentity(), $action);
							}
						} else if( $objectParent instanceof User_Model_User ) {
							// Note: technically we shouldn't do owner_member, however some things are using it
							if( Engine_Api::_()->authorization()->context->isAllowed($getAlbum, 'owner_member', 'view') ||
									Engine_Api::_()->authorization()->context->isAllowed($getAlbum, 'parent_member', 'view') ) {
									Engine_Api::_()->sitetagcheckin()->insertPrivacyInStream('members', $objectParent->getIdentity(), $action);
							}
						}

						// Registered
						if( $content == 'everyone' &&
								Engine_Api::_()->authorization()->context->isAllowed($getAlbum, 'registered', 'view') ) {
								Engine_Api::_()->sitetagcheckin()->insertPrivacyInStream('registered', 0, $action);
						}

						// Everyone
						if( $content == 'everyone' &&
								Engine_Api::_()->authorization()->context->isAllowed($getAlbum, 'everyone', 'view') ) {
								Engine_Api::_()->sitetagcheckin()->insertPrivacyInStream('everyone', 0, $action);
						}	
					}
        }
      } else {
        //IF EMPTY RESOURCE ID THEN SET LOCATION
        if (empty($resource_guid)) {
          if ($type == 'just_use') {
            $notification_location = $checkin_params['label'];
          } else {
						if(isset($checkin_params['vicinity'])) {
							if(isset($checkin_params['name']) && $checkin_params['name'] && $checkin_params['name'] != $checkin_params['vicinity']) {
								$checkin_params['label'] = $checkin_params['name'] . ', ' . $checkin_params['vicinity'];
							} else {
								$checkin_params['label'] = $checkin_params['vicinity'];
							}
						}

						if (!$is_mobile) {
              $notification_location = $this->view->htmlLink($this->view->url(array('guid' => "activity_action_" . $res->action_id,'format'=>'smoothbox'), 'sitetagcheckin_viewmap', true), $checkin_params['label'], array('class' => 'smoothbox'));
					  } else {
              $notification_location = $this->view->htmlLink($this->view->url(array('guid' => "activity_action_" . $res->action_id), 'sitetagcheckin_viewmap', true), $checkin_params['label'], array());
					  }
          }
        }
        $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
        if ($res->type == 'post_self') {
          $res->type = "sitetagcheckin_post_self";
          $streamTable->update(array('type' => "sitetagcheckin_post_self"), array('action_id = ?' => $res->action_id));
        } elseif ($res->type == 'album_photo_new') {
          $res->type = "sitetagcheckin_album_photo_new";
          $streamTable->update(array('type' => "sitetagcheckin_album_photo_new"), array('action_id = ?' => $res->action_id));
        } elseif ($res->type == 'sitetagcheckin_album_photo_new') {
          if(empty($location)) {
						$res->type = "album_photo_new";
						$streamTable->update(array('type' => "album_photo_new"), array('action_id = ?' => $res->action_id));
          }
        } else if ($res->object_type == 'sitepage_page') {
          $res->type = "sitetagcheckin_spal_photo_new";
          $streamTable->update(array('type' => "sitetagcheckin_spal_photo_new"), array('action_id = ?' => $res->action_id));
        } else if ($res->object_type == 'sitebusiness_business') {
          $res->type = "sitetagcheckin_sbal_photo_new";
          $streamTable->update(array('type' => "sitetagcheckin_sbal_photo_new"), array('action_id = ?' => $res->action_id));
        } else if ($res->object_type == 'sitegroup_group') {
          $res->type = "sitetagcheckin_sgal_photo_new";
          $streamTable->update(array('type' => "sitetagcheckin_sgal_photo_new"), array('action_id = ?' => $res->action_id));
        } else if ($res->object_type == 'sitestore_store') {
          $res->type = "sitetagcheckin_ssal_photo_new";
          $streamTable->update(array('type' => "sitetagcheckin_ssal_photo_new"), array('action_id = ?' => $res->action_id));
        } else if ($res->object_type == 'siteevent_event') {
          $res->type = "sitetagcheckin_seal_photo_new";
          $streamTable->update(array('type' => "sitetagcheckin_seal_photo_new"), array('action_id = ?' => $res->action_id));
        } else if ($res->type == 'tagged') {
          $res->type = "sitetagcheckin_tagged_new";
          $streamTable->update(array('type' => "sitetagcheckin_tagged_new"), array('action_id = ?' => $res->action_id));
        } else if ($res->type == 'profile_photo_update') {
          $res->type = "sitetagcheckin_profile_photo";
          $streamTable->update(array('type' => "sitetagcheckin_profile_photo"), array('action_id = ?' => $res->action_id));
        }
        if(!empty($location)) {
					$res->params = array_merge((array) $res->params, array('prefixadd' => $prefixadd, 'location' => $notification_location, 'checkin' => $checkin_params));
        } else {
					$res->params = (array) $res->params;
        }
        $res->save();
        $actionIdentity = $res->action_id;
      }
    //}

    $checkinContentArray = array(
        'location_id' => $location_id,
        'type' => 'tagging',
        'item_id' => $actionIdentity,
        'item_type' => 'activity_action',
        'params' => $checkin_params,
        'event_date' => date('Y-m-d H:i:s'),
        'owner_id' => $viewer_id
    );
    $content_array = array(
        'resource_id' => $resource_id,
        'resource_type' => $resource_type,
        'object_id' => $resource_id,
        'object_type' => $resource_type,
        'action_id' => $actionIdentity,
    );

    //SAVE LOCATION
    $addLocation = $tableAddLocation->saveLocation(array_merge($content_array, $checkinContentArray));

    //FOR ALBUM TAGGING
    if ($resource_type == 'sitepage_album' || $resource_type == 'album' || $resource_type == 'sitebusiness_album' || $resource_type == 'sitegroup_album') {
      switch ($resource_type) {
        case 'sitepage_album':
          $changeResourceType = 'sitepage_photo';
          break;
        case 'sitebusiness_album':
          $changeResourceType = 'sitebusiness_photo';
          break;
        case 'sitegroup_album':
          $changeResourceType = 'sitegroup_photo';
          break;
        case 'sitestore_album':
          $changeResourceType = 'sitestore_photo';
          break;
        case 'album':
          $changeResourceType = 'album_photo';
          break;
        case 'advalbum_album':
          $changeResourceType = 'advalbum_photo';
          break;
      }

      //GET PHOTO TABLE
      $photoTable = Engine_Api::_()->getItemTable($changeResourceType);

      //GET COLUMN
      $col = current($photoTable->info("primary"));

      //SELECT 
      $select = $photoTable->select()->from($photoTable->info('name'), $col)->where('album_id =?', $resource_id);

      //GET ROWS
      $rows = $photoTable->fetchAll($select);

      //GET OBJECT IDS
      $objectIds = $tableAddLocation->getObjectIds($changeResourceType);

      //MAKE ARRAY OF RESOURC IDS
      $objectIdsArray = array();
      foreach ($objectIds as $key => $value) {
        $objectIdsArray[] = $value;
      }

      //SAVE LOCATION FOR RESOURCE IDS
      foreach ($rows as $key => $value) {
        if (in_array($value->photo_id, $objectIdsArray))
          continue;
        $content = array(
            'resource_id' => $value->photo_id,
            'resource_type' => $changeResourceType,
            'object_id' => $value->photo_id,
            'object_type' => $changeResourceType,
            'action_id' => '-' . $addLocation->action_id,
        );
        $tableAddLocation->saveLocation(array_merge($content, $checkinContentArray));
      }
    }

    //SEND NOTIFICATION WHEN THERE IS ANY TAGGED USER IN THE PHOTO
    $existingTagMaps = Engine_Api::_()->getDbtable('tags', 'core')->getTagMaps($getItem);
    $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    foreach ($existingTagMaps as $tagmap) {
      if ($tagmap->tag_id != $viewer_id) {
        $ownerObj = Engine_Api::_()->getItem('user', $tagmap->tag_id);
        $notificationTable->addNotification($ownerObj, $viewer, $getItem, "sitetagcheckin_tagged_location", array("location" => $notification_location, "label" => "photo"));
      }
    }

    $type_name = Zend_Registry::get('Zend_Translate')->translate('photo');
    if (is_array($type_name))
      $type_name = $type_name[0];
    if (isset($checkin_params) && !empty($checkin_params['resource_guid'])) {
      $tag = Engine_Api::_()->getItemByGuid($checkin_params['resource_guid']);
      if ($tag && ($tag instanceof Sitepage_Model_Page)) {
        foreach ($tag->getPageAdmins() as $owner) {
          if ($owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
            $notificationTable->addNotification($owner, $viewer, $getItem, 'sitetagcheckin_page_tagged', array(
                'label' => $type_name,
            ));
          }
        }
      } else if ($tag && ($tag instanceof Sitebusiness_Model_Business)) {
        foreach ($tag->getBusinessAdmins() as $owner) {
          if ($owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
            $notificationTable->addNotification($owner, $viewer, $getItem, 'sitetagcheckin_business_tagged', array(
                'label' => $type_name,
            ));
          }
        }
      } else if ($tag && ($tag instanceof Sitegroup_Model_Group)) {
        foreach ($tag->getGroupAdmins() as $owner) {
          if ($owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
            $notificationTable->addNotification($owner, $viewer, $getItem, 'sitetagcheckin_group_tagged', array(
                'label' => $type_name,
            ));
          }
        }
      } else if ($tag && ($tag instanceof Sitestore_Model_Store)) {
        foreach ($tag->getStoreAdmins() as $owner) {
          if ($owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
            $notificationTable->addNotification($owner, $viewer, $getItem, 'sitetagcheckin_store_tagged', array(
                'label' => $type_name,
            ));
          }
        }
      } /*else if ($tag && ($tag instanceof Siteevent_Model_Event)) {
        foreach ($tag->getEventsAdmins() as $owner) {
          if ($owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
            $notificationTable->addNotification($owner, $viewer, $getItem, 'sitetagcheckin_event_tagged', array(
                'label' => $type_name,
            ));
          }
        }
      }*/
    }

    echo Zend_Json::encode(array('displayLocationWithUrl' => $prefixadd . " " . $notification_location, 'getSaveLocation' => $checkin_params['label']));
    exit();
  }

  //GET PHOTOS IN WHICH ARE LOCATION ARE ASSOCIATED
  public function getLocationPhotosAction() {

    //GET USER SUBJECT
    $subject = Engine_Api::_()->core()->getSubject('user');

    //THERE IS NO SUBECT THEN RETURN
    if (empty($subject))
      return;

    //GET ADDLOCATION TABLE
    $addlocationsTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');

    //GET PAGE
    $page = $this->_getParam('page', 1);

    //GET LOCATION ID
    $this->view->location_id = $location_id = (int) $this->_getParam('location_id');

    //GET LOCATION 
    $this->view->location = $this->_getParam('location');

    //GET FILTER BASED CATEGORY
    $this->view->feed_type = $feed_type = $this->_getParam('feed_type', '');

    //GET FILTER BASED CATEGORY
    $this->view->category = $category = (int) $this->_getParam('category', 1);

    $this->view->actions = $actionpaginator = $addlocationsTable->getFeedItems($subject, null, $location_id, $category);

    if($this->view->actions && $actionpaginator->getTotalItemCount() > 0)
    $actionpaginator->setItemCountPerPage(1)->setCurrentPageNumber($page);

    if(empty($this->view->actions)) {
      $this->view->action_count = 0;
    } else {
      $this->view->action_count = $actionpaginator->getTotalItemCount();
    }

		$db = Engine_Db_Table::getDefaultAdapter();
    
		$locationsTable = Engine_Api::_()->getDbtable('locations', 'seaocore');
		$locationsTableName = $locationsTable->info('name');
    $moduleCore = Engine_Api::_()->getDbtable('modules', 'core');
    $getEnableModuleEvent = $moduleCore->isModuleEnabled('event');
    $getEnableModuleYnEvent = $moduleCore->isModuleEnabled('ynevent');
    $selectEvent="";

    $event = array();
    if($getEnableModuleEvent) {
			$eventTable = Engine_Api::_()->getDbtable('events', 'event');
			$eventTableName = $eventTable->info('name');
			$eventMembershipTable = Engine_Api::_()->getDbtable('membership', 'event');
			$eventMembershipTableName = $eventMembershipTable->info('name');
			$selectEvent = $eventTable->select()
								->setIntegrityCheck(false)
								->from($eventTableName, array('event_id', 'photo_id', 'parent_type', 'starttime', 'location'))
								->join($eventMembershipTableName, "$eventMembershipTableName.resource_id = $eventTableName.event_id", array('user_id as event_user_id'))
								->where($eventMembershipTableName . '.rsvp =?', 2)
								->where($eventMembershipTableName . '.user_id =?', $subject->user_id)
								->where('seao_locationid = ?', $location_id)
								->where('starttime <= ?', date('Y-m-d H:i:s'));
      $event['event']  =  $selectEvent;
    }
    elseif($getEnableModuleYnEvent) {
			$eventTable = Engine_Api::_()->getItemtable('event');
			$eventTableName = $eventTable->info('name');
			$eventMembershipTable = Engine_Api::_()->getDbtable('membership', 'ynevent');
			$eventMembershipTableName = $eventMembershipTable->info('name');
			$selectEvent = $eventTable->select()
								->setIntegrityCheck(false)
								->from($eventTableName, array('event_id', 'photo_id', 'parent_type', 'starttime', 'location'))
								->join($eventMembershipTableName, "$eventMembershipTableName.resource_id = $eventTableName.event_id", array('user_id as event_user_id'))
								->where($eventMembershipTableName . '.rsvp =?', 2)
								->where($eventMembershipTableName . '.user_id =?', $subject->user_id)
								->where('seao_locationid = ?', $location_id)
								->where('starttime <= ?', date('Y-m-d H:i:s'));
      $event['event']  =  $selectEvent;
    }

    $getEnableModulePageEvent = $moduleCore->isModuleEnabled('sitepageevent');
    $selectPageEvent="";
    if($getEnableModulePageEvent) {
			$pageeventTable = Engine_Api::_()->getDbtable('events', 'sitepageevent');
			$pageeventTableName = $pageeventTable->info('name');
			$pageventMembershipTable = Engine_Api::_()->getDbtable('membership', 'sitepageevent');
			$pageeventMembershipTableName = $pageventMembershipTable->info('name');
			$selectPageEvent = $pageeventTable->select()
								->setIntegrityCheck(false)
								->from($pageeventTable, array('event_id', 'photo_id', 'parent_type', 'starttime', 'location'))
								->join($pageeventMembershipTableName, "$pageeventMembershipTableName.resource_id = $pageeventTableName.event_id",  array('user_id as event_user_id'))
								->where($pageeventMembershipTableName . '.rsvp =?', 2)
								->where($pageeventMembershipTableName . '.user_id =?', $subject->user_id)
								->where("$pageeventTableName.seao_locationid = ?", $location_id)
								->where("$pageeventTableName.starttime <= ?", date('Y-m-d H:i:s'));
      $event['pageevent']  =  $selectPageEvent;
    }

    $getEnableModuleBusinessEvent = $moduleCore->isModuleEnabled('sitebusinessevent');
    $selectBusinessEvent="";
    if($getEnableModuleBusinessEvent) {
			$businesseventTable = Engine_Api::_()->getDbtable('events', 'sitebusinessevent');
			$businesseventTableName = $businesseventTable->info('name');
			$businesseventMembershipTable = Engine_Api::_()->getDbtable('membership', 'sitebusinessevent');
			$businesseventMembershipTableName = $businesseventMembershipTable->info('name');
			$selectBusinessEvent = $businesseventTable->select()
								->setIntegrityCheck(false)
								->from($businesseventTable, array('event_id', 'photo_id', 'parent_type', 'starttime', 'location'))
								->join($businesseventMembershipTableName, "$businesseventMembershipTableName.resource_id = $businesseventTableName.event_id", array('user_id as event_user_id'))
								->where($businesseventMembershipTableName . '.rsvp =?', 2)
								->where($businesseventMembershipTableName . '.user_id =?', $subject->user_id)
								->where("$businesseventTableName.seao_locationid = ?", $location_id)
								->where("$businesseventTableName.starttime <= ?", date('Y-m-d H:i:s'));
      $event['businessevent']  =  $selectBusinessEvent;
    }

    $getEnableModuleGroupEvent = $moduleCore->isModuleEnabled('sitegroupevent');
    $selectGroupEvent="";
    if($getEnableModuleGroupEvent) {
			$groupeventTable = Engine_Api::_()->getDbtable('events', 'sitegroupevent');
			$groupeventTableName = $groupeventTable->info('name');
			$groupeventMembershipTable = Engine_Api::_()->getDbtable('membership', 'sitegroupevent');
			$groupeventMembershipTableName = $groupeventMembershipTable->info('name');
			$selectGroupEvent = $groupeventTable->select()
								->setIntegrityCheck(false)
								->from($groupeventTableName, array('event_id', 'photo_id', 'parent_type', 'starttime', 'location'))
								->join($groupeventMembershipTableName, "$groupeventMembershipTableName.resource_id = $groupeventTableName.event_id", array('user_id as event_user_id'))
								->where($groupeventMembershipTableName . '.rsvp =?', 2)
								->where($groupeventMembershipTableName . '.user_id =?', $subject->user_id)
								->where("$groupeventTableName.seao_locationid = ?", $location_id)
								->where("$groupeventTableName.starttime <= ?", date('Y-m-d H:i:s'));
      $event['groupevent']  =  $selectGroupEvent;
    }

    $union_event_array= array();
    if(isset($event['event'])) { 
      $union_event_array[] = $selectEvent;
    } 

    if(isset($event['pageevent'])) {
      $union_event_array[] = $selectPageEvent;
    }

    if(isset($event['businessevent'])) {
      $union_event_array[] = $selectBusinessEvent;
    }

    if(isset($event['groupevent'])) {
      $union_event_array[] = $selectGroupEvent;
    }

    $select="";
    if(!empty($union_event_array))
    $select = $db->select()->union($union_event_array);

    $this->view->eventlocations = 0;

    if($select) {
			$this->view->eventlocations = $eventpaginator = Zend_Paginator::factory($select);
			if($eventpaginator && $eventpaginator->getTotalItemCount() > 0)
			$eventpaginator->setItemCountPerPage(1)->setCurrentPageNumber($page);
    }

    if(empty($this->view->eventlocations)) {
      $this->view->event_count = 0;
    } else {
      $this->view->event_count = $eventpaginator->getTotalItemCount();
    }

    //GET AJAX PARAMETER
    $this->view->isajax = $this->_getParam('isajax', 0);
    //SHOW MAP
    $this->view->show_map = 1;

  } 

  //ACTION FOR FETCING THE FEED ITEMS
  public function getFeedItemsAction() {

    //GET USER SUBJECT
    $subject = $this->_getParam('subject');

    //GET SUBJECT ITEM
    $getItem = '';
    if (!empty($subject))
      $getItem = Engine_Api::_()->getItemByGuid($subject);

    //THERE IS NO SUBECT THEN RETURN
    if (empty($getItem))
      return;

    //GET PAGE
    $page = $this->_getParam('page', 1);

    $this->view->content_feeds = $content_feeds = $this->_getParam('content_feeds', 0);

    //GET ADDLOCATION TABLE
    $addlocationsTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');

    //GET ACTIONS
    $this->view->actions = $paginator = $addlocationsTable->getFeedItems($getItem, null, null, null, $content_feeds);

    //GET AJAX PARAMETER
    $this->view->is_ajax = $this->_getParam('is_ajax', 0);

    //SHOW MAP
    $this->view->show_map = 0;

    //GET LIMIT
    $limit = Zend_Controller_Front::getInstance()->getRequest()->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));

    //SET PAGINATOR COUNT 
    if ($paginator)
      $paginator->setItemCountPerPage($limit)->setCurrentPageNumber($page);
  }

  //ACTION FOR SHOWING LOCAITON IN MAP WITH GET DIRECTION
  public function viewMapAction() {
    $this->view->format=$this->_getParam('format',null);
    if($this->view->format)
    $this->_helper->layout->setLayout('default-simple');
    if (!$this->_getParam('guid'))
      return $this->_forward('notfound', 'error', 'core');
    $item = Engine_Api::_()->getItemByGuid($this->_getParam('guid'));
    if (!$item)
      return $this->_forward('notfound', 'error', 'core');
    $params = (array) $item->params;
    if($item->type == 'sitetagcheckin_album_photo_new') {
			$explodeArray = explode("_", $this->_getParam('guid'));

			//GET ACTION TABLE
			$actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');

			//GET ACTION TABLE NAME
			$actionsTableName = $actionsTable->info('name');
			
			//GET ADDLOCATION TABLE NAME
			$addlocationsTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');
			$addlocationsTableName = $addlocationsTable->info('name');
			//SELCET ACTION IDS
			$select = $addlocationsTable->select()/*->setIntegrityCheck(false)*/
							->from($addlocationsTableName, array('params'))
							->where("$addlocationsTableName.action_id =?", -$explodeArray[2])
							->order("$addlocationsTableName.modified_date DESC");
      $photoparams = (array) $addlocationsTable->fetchRow($select)->params;
    
      if(!empty($photoparams)) {
        $params = $photoparams;
      }

    }
    if (is_array($params) && isset($params['checkin'])) {
      $this->view->checkin = $checkin = $params['checkin'];
    } else {
      return $this->_forward('notfound', 'error', 'core');
    }
  }
  
  //ACTION FOR BROWSE LOCATION PAGES.
	public function byLocationsAction() {

    return $this->_forward('notfound', 'error', 'core');
	  if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'event' )) {
	    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('event_main', array(), 'sitetagcheckin_main_location'); 
		  $this->_helper->content->setEnabled();
		} elseif ( Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'ynevent' )) {
			$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('ynevent_main', array(), 'sitetagcheckin_main_bylocation'); 
		  $this->_helper->content->setEnabled();
		} else {
			return $this->_forward('notfound', 'error', 'core');
		}
  }

  //ACTION FOR BROWSE LOCATION PAGES.
	public function mobilebyLocationsAction() {
    return $this->_forward('notfound', 'error', 'core');
	  if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'event' )) {
	    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('event_main', array(), 'sitetagcheckin_main_location'); 
		  $this->_helper->content->setEnabled();
		} elseif ( Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'ynevent' )) {
			$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('ynevent_main', array(), 'sitetagcheckin_main_bylocation'); 
		  $this->_helper->content->setEnabled();
		} else {
			return $this->_forward('notfound', 'error', 'core');
		}
  }

  public function getAlbumsPhotosAction() {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
   
    //GET VIEWER ID
    $viewer_id = $viewer->getIdentity();
    $moduleCore = Engine_Api::_()->getDbtable('modules', 'core');
    $getEnableModuleAdvalbum = $moduleCore->isModuleEnabled('advalbum');
    if($getEnableModuleAdvalbum) {
			$photoTable = Engine_Api::_()->getItemTable('advalbum_photo');
    } else {
      $photoTable = Engine_Api::_()->getItemTable('album_photo');
    }
    $photoTableName = $photoTable->info('name');

    //CHECK REQUEST IS AJAX OR NOT
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    $skip_photo_id = $this->_getParam('skip_photo_id', null);
    if (!empty($is_ajax) && !empty($skip_photo_id)) {
      $photoTable->update(array('skip_photo' => 1), array('photo_id = ?' => $skip_photo_id));
    }

    $photoTableName = $photoTable->info('name');
    $addlocationsTableName = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin')->info('name');
		$select = $photoTable
									->select()
									->setIntegrityCheck(false)
									->from($photoTableName, array('photo_id'))
									->join($addlocationsTableName, "$addlocationsTableName.resource_id = $photoTableName.photo_id",null)
									->where("$photoTableName.owner_id =?", $viewer_id)
                  ->group("$photoTableName.photo_id")
									->order("$addlocationsTableName.addlocation_id ASC");
    if($getEnableModuleAdvalbum) {
      $select->where("$addlocationsTableName.resource_type =?", 'advalbum_photo');
    } else {
      $select->where("$addlocationsTableName.resource_type =?", 'album_photo');
    }                 
    $photoWithLocationIds = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
		$select = $photoTable->select()->from($photoTableName, array('*'))->where("$photoTableName.owner_id =?", $viewer_id)->where("$photoTableName.skip_photo =?", 0);

    if($photoWithLocationIds)
    $select->where("$photoTableName.photo_id not in (?)", new Zend_Db_Expr(trim(implode(",", $photoWithLocationIds))));

		$this->view->paginator = $paginator = Zend_Paginator::factory($select);	
    $paginator->setItemCountPerPage($this->_getParam('photoCount', 7));
  }

  //ACTION FOR EDIT LOCATION
  public function editLocationAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET EVENT ID AND EVENT ITEM  
    $this->view->event_id = $event_id = $this->_getParam('event_id');
    $this->view->seao_locationid = $seao_locationid = $this->_getParam('seao_locationid');
    $this->view->event = $event = Engine_Api::_()->getItem('event', $event_id);

    $value['id'] = $seao_locationid;
    $this->view->location = $location = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocations($value);

    //Get form
    if (!empty($location)) {

      $this->view->form = $form = new Seaocore_Form_Location(array(
				'item' => $event,
				'location' => $location->location
      ));

      if (!$this->getRequest()->isPost()) {
        $form->populate($location->toarray()
      );
        return;
      }

      //FORM VALIDAITON
      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }

      //FORM VALIDAITON
      if ($form->isValid($this->getRequest()->getPost())) {

        $values = $form->getValues();
        unset($values['submit']);
        unset($values['location']);

 				$seLocationsTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
        $seLocationsTable->update($values, array('locationitem_id =?' => $seao_locationid));
      }
      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }
    $this->view->location = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocations($value);
  }

  //ACTION FOR EDIT ADDRESS
  public function editAddressAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET PAGE ID, PAGE OBJECT AND THEN CHECK PAGE VALIDATION
    $seao_locationid = $this->_getParam('seao_locationid');
    $event_id = $this->_getParam('event_id');
    $event = Engine_Api::_()->getItem('event', $event_id);

    $this->view->form = $form = new Sitetagcheckin_Form_Address(array('item' => $event));

    //POPULATE FORM
    if (!$this->getRequest()->isPost()) {
      $form->populate($event->toArray());
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

      $values = $form->getValues();

      $event->location = $values['location'];
      if (empty($values['location'])) {
			  //DELETE THE RESULT FORM THE TABLE.
				Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $event_id, 'resource_type = ?' => 'event'));
				$event->seao_locationid = '0';
			}
      $event->save();
      unset($values['submit']);

			if (!empty($values['location'])) {
			
				//DELETE THE RESULT FORM THE TABLE.
				Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $event_id, 'resource_type = ?' => 'event'));
			
				$seaoLocation = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($values['location'], '', 'event', $event_id);

				//event table entry of location id.
				Engine_Api::_()->getDbtable('events', 'event')->update(array('seao_locationid'=>  $seaoLocation), array('event_id =?' => $event_id));
			}

      $db->commit();
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 500,
        'parentRedirect' => $this->_helper->url->url(array('action' => 'edit-location', 'seao_locationid' => $seaoLocation, 'event_id' => $event_id), 'sitetagcheckin_specific', true),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your event location has been modified successfully.'))
      ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }
}