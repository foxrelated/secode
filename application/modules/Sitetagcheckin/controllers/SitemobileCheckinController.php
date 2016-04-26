<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: CheckinController.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_SitemobileCheckinController extends Core_Controller_Action_Standard {

  //ACTION FOR SHOWING THE CHECKIN BUTTON
  public function checkInAction() {
   
    //MAKE SURE USER EXISTS
    if (!$this->_helper->requireUser()->isValid())
      return;
      
    $getMapInfo = Engine_Api::_()->sitetagcheckin()->getMapInfo();

	  if( !$getMapInfo )
		return $this->_forward('notfound', 'error', 'core');

    //GET SUBJECT IF NECESSARY
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET VIEWER ID
    $viewer_id = $viewer->getIdentity();

    //SET AJAX REQUEST
    $isajax = $this->_getParam('isajax', 0);

    //GET RESOURCE TYPE
    $resource_type_user = $this->view->resource_type = $resource_type = $this->_getParam('resource_type', 'user');

    //GET RESOURCE ID
    $this->view->resource_id = $resource_id = $this->_getParam('resource_id', $viewer_id);

    $resource = Engine_Api::_()->getItem($resource_type, $resource_id);
    $this->view->resource_href = $href = $resource->getHref();
    //CHECK IN VERB
    $this->view->checkin_verb = $checkinto_verb = $this->_getParam('checkin_verb', 'Check-in');

    //CHECKED IN VERB
    $this->view->checkedinto_verb = $checkedinto_verb = $this->_getParam('checkedinto_verb', 'checked-into');
    
    //CHECKED IN VERB
    $this->view->checkin_your = $checkin_your = $this->_getParam('checkin_your', "You have checked-in here");

    //GET HOW TO USE THIS WIDGET
    $this->view->checkin_use = $checkin_use = $this->_getParam('checkin_use', 1);
    $tab = $this->view->tab = $this->_getParam('tab', null);
    $getMapInfo = Engine_Api::_()->sitetagcheckin()->getMapInfo();

    //CHECK SUBJECT IS EXIST OR NOT IF NOT EXIST THEN SET ACCORDING TO THE PAGE ID AND PHOTO ID
    if (!Engine_Api::_()->core()->hasSubject()) {
      if (0 !== ($resource_id = (int) $this->_getParam('resource_id')) &&
              null !== ($resource = Engine_Api::_()->getItem($resource_type, $resource_id))) {
        Engine_Api::_()->core()->setSubject($resource);
      }
    }
    $this->view->photoUploadUrl = $photoUploadUrl = 'album/album/compose-upload/type/wall';
    $location_id = 0;
    $this->view->location = $location = Engine_Api::_()->sitetagcheckin()->getCustomFieldLocation($resource);

    //CHECK REQUEST
    $is_ajax = 0;

    //ASSIGN THE COMPOSING VALUES
    $composePartials = array();
    $manifest = Zend_Registry::get('Engine_Manifest');
    if (isset($manifest['album']) && isset($manifest['album']['composer']) && isset($manifest['album']['composer']['photo'])) {
      $config = $manifest['album']['composer']['photo'];
      if (empty($config['auth']) || Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
        $config['script'][1] = 'sitetagcheckin';
        $composePartials[] = $config['script'];
      }
    }

    if (isset($manifest['advancedactivity']) && isset($manifest['advancedactivity']['composer']) && isset($manifest['advancedactivity']['composer']['tag'])) {
      $config = $manifest['advancedactivity']['composer']['tag'];
      if (empty($config['auth']) || Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
        $composePartials[] = $config['script'];
      }
    }

    $this->view->composePartials = $composePartials;
    $this->view->settingsApi = $settings = Engine_Api::_()->getApi('settings', 'core');
    //CHECK ADVANCED ACTIVITY FEED IS ENABLED OR NOT
    $advancedactivity_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');

    //SET PRIVACY AND SMILES WHEN THERE IS ADVANCED ACTIVITY FEED
    if ($advancedactivity_enabled) {

      $this->view->showPrivacyDropdown = in_array('userprivacy', $settings->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy")));
      if ($this->view->showPrivacyDropdown)
        $this->view->showDefaultInPrivacyDropdown = $userPrivacy = Engine_Api::_()->getDbtable('settings', 'user')->getSetting($viewer, "aaf_post_privacy");

      if (empty($userPrivacy))
        $this->view->showDefaultInPrivacyDropdown = $userPrivacy = "everyone";

      $this->view->availableLabels = $availableLabels = array('everyone' => 'Everyone', 'networks' => 'Friends &amp; Networks', 'friends' => 'Friends Only', 'onlyme' => 'Only Me');

      $this->view->enableList = $userFriendListEnable = $settings->getSetting('user.friends.lists');

      if ($userFriendListEnable && !empty($viewer_id)) {
        $listTable = Engine_Api::_()->getItemTable('user_list');
        $this->view->lists = $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));
        $this->view->countList = $countList = @count($lists);
        if (!empty($countList) && !empty($userPrivacy) && !in_array($userPrivacy, array('everyone', 'networks', 'friends', 'onlyme'))) {
          $privacylists = $listTable->fetchAll($listTable->select()->where('list_id IN(?)', array(explode(",", $userPrivacy))));
          $temp_list = array();
          foreach ($privacylists as $plist) {
            $temp_list[$plist->list_id] = $plist->title;
          }
          if (count($temp_list) > 0) {
            $this->view->privacylists = $temp_list;
          } else {
            $this->view->showDefaultInPrivacyDropdown = $userPrivacy = "friends";
          }
        }
      } else {
        $userFriendListEnable = 0;
      }

      $this->view->enableList = $userFriendListEnable;
    }
    
    //CHECK IF POST
    if (!$this->getRequest()->isPost()) {
      if (empty($is_ajax)) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not post');
        return;
      } else {
        echo Zend_Json::encode(array('status' => false, 'error' => Zend_Registry::get('Zend_Translate')->_('Not post')));
        exit();
      }
    }

    //CHECK IF FORM IS VALID
    $postData = $this->getRequest()->getPost();
    //print_r($postData);
    $body = @$postData['body'];
    $privacy = null;

    //SET PRIVACY AND SMILES WHEN THERE IS ADVANCED ACTIVITY FEED
    if ($advancedactivity_enabled) {
      $privacy = $settings->getSetting('activity.content', 'everyone');
      if (isset($postData['auth_view']))
        $privacy = @$postData['auth_view'];
    }

    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    $postData['body'] = $body;

    //SET UP ACTION VARIABLE
    $action = null;

    //GET ACTIVITY TABLE
    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');

    //PROCESS
    $db = $actionTable->getAdapter();
    $db->beginTransaction();
    try {
      //TRY ATTACHMENT GETTING STUFF
      $attachment = null;
      $attachmentData = $this->getRequest()->getParam('attachment');

      if (!empty($attachmentData) && !empty($attachmentData['type'])) {
        $type = $attachmentData['type'];
        $config = null;
        foreach (Zend_Registry::get('Engine_Manifest') as $data) {
          if (!empty($data['composer'][$type])) {
            $config = $data['composer'][$type];
          }
        }
        if (!empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
          $config = null;
        }
        if ($config) {
          $plugin = Engine_Api::_()->loadClass($config['plugin']);
          $method = 'onAttach' . ucfirst($type);
          $attachment = $plugin->$method($attachmentData);
        }
      }

      $body = preg_replace('/<br[^<>]*>/', "\n", $body);

      //GET RESOURCE TITLE
      $getTitle = $resource->getTitle();

      //GET RESOURCE HREF
       $href = $resource->getHref();
      $prefixadd = $this->view->translate('at');
      $location_label = "";
      if ($resource_type == 'event' || $resource_type == 'classified' || $resource_type == 'list_listing' || $resource_type == 'recipe' || $resource_type == 'sitepageevent_event' || $resource_type == 'sitebusinessevent_event' || $resource_type == 'sitegroupevent_event' ) {
        $location_label = $location;
        if (empty($location)) {
          $location_label = "";
          $prefixadd = "";
          $location = "";
        }
      } elseif ($resource_type == 'sitepage_page' || $resource_type == 'sitebusiness_business' || $resource_type == 'sitegroup_group' || $resource_type == 'sitestore_store' || $resource_type == 'siteevent_event') {
        $location_label = $getTitle;
      }

      //SET CHECKIN PARAMS
      $checkin_array = array(
          'type' => ucfirst($resource->getShortType()),
          'resource_guid' => $resource->getGuid(),
          'label' => $location_label,
          'prefixadd' => $prefixadd
      );
      $is_mobile = Engine_Api::_()->seaocore()->isMobile();
      //MAKE CHECKIN PARMAS
      $checkin_params = array("checkin" => $checkin_array);

      //INITIALISE FEED EVENT DATE
      $feed_event_date = "";

      //IF EMPTY LOCATION THEN SET ACTION TYPE
      if (empty($checkin_use)) {
        $activityType = 'sitetagcheckin_content';
        $event_date = date('Y-m-d');
      } else {
        if($postData['month'] < 10) {
         $postData['month'] = "0". $postData['month'];
        }
        if($postData['day'] < 10) {
         $postData['day'] = "0". $postData['day'];
        }
        $event_date = $postData['year'] . '-' . $postData['month'] . '-' . $postData['day'];
        if(isset($postData['checkinstr_status'])) {
          $activityType = 'sitetagcheckin_lct_add_to_map';
          $checkin  = $postData['checkinstr_status'];
          $checkinUser = 1;
        } else{
          $activityType = 'sitetagcheckin_add_to_map';
        }
        if ($postData['month'] == "00") {
          $feed_event_date = $this->view->translate(" in ") . $postData['year'];
					$postData['day'] = "00";
          $event_date = $postData['year'] . '-' . $postData['month'] . '-' . $postData['day'];
        } else if ($postData['month'] != "00" && $postData['day'] == "00") {
          $feed_event_date = $this->view->translate(" in ") . date('F', mktime(0, 0, 0, $postData['month'])) . ', ' . $postData['year'];
        } else {
          $feed_event_date = $this->view->translate(" on ") . date("F j, Y", strtotime($event_date));
        }
      }

      //IF ADVANCED ACTIVITY IS ENABLED THEN ADD ACVITY
      if ($advancedactivity_enabled) {

        $advancedactivityActions = Engine_Api::_()->getDbtable('actions', 'advancedactivity');
        $showPrivacyDropdown = in_array('userprivacy', $settings->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy")));

        if ($showPrivacyDropdown) {
          Engine_Api::_()->getDbtable('userSettings', 'seaocore')->setSetting($viewer, "aaf_post_privacy", $privacy);
        }
				//ADD ACTIVITY
        if($activityType == 'sitetagcheckin_content' || $activityType == 'sitetagcheckin_add_to_map') {
					$action = $advancedactivityActions->addActivity($viewer, $resource, $activityType, $body, $privacy, array('checked_into_verb' => $checkedinto_verb, 'checkin' => $checkin_array, 'event_date' => $feed_event_date));
        } else if($activityType == 'sitetagcheckin_lct_add_to_map') {
          parse_str($checkin, $checkin_locationparams);
          $checkin_params = array("checkin" => $checkin_locationparams);
          $location = $checkin_locationparams['label'];
					if($checkin_locationparams['vicinity']) {
						if(isset($checkin_locationparams['name']) && $checkin_locationparams['name'] && $checkin_locationparams['name'] != $checkin_locationparams['vicinity']) {
							$checkin_locationparams['label'] = $checkin_locationparams['name'] . ', ' . $checkin_locationparams['vicinity'];
						} else {
							$checkin_locationparams['label'] = $checkin_locationparams['vicinity'];
						}
					}
  
					$action = $advancedactivityActions->addActivity($viewer, $resource, $activityType, $body, $privacy, array('prefixadd' => $checkin_locationparams['prefixadd'], 'checkin' => $checkin_locationparams, 'event_date' => $feed_event_date));   
					if (!$is_mobile) {
						$checkin_locationparams['label'] = $this->view->htmlLink($this->view->url(array('guid' => $action->getGuid(),'format'=>'smoothbox'), 'sitetagcheckin_viewmap', true), $checkin_locationparams['label'], array('class' => 'smoothbox'));  
					} else {
						$checkin_locationparams['label'] = $this->view->htmlLink($this->view->url(array('guid' => $action->getGuid()), 'sitetagcheckin_viewmap', true), $checkin_locationparams['label'], array());
					}

          if(empty($checkin_locationparams['resource_guid'])) {
						$action->params = array_merge($action->params, array('location' => $checkin_locationparams['label']));
						$action_id = $action->save(); 
						$object_id = $resource_id = $action_id;
						$object_type = $resource_type = "activity_action";
          } else {
            $getItemFor =  Engine_Api::_()->getItemByGuid($checkin_locationparams['resource_guid']);
						$object_id = $resource_id = $getItemFor->getIdentity();
						$object_type = $resource_type= $getItemFor->getType();
						//GET RESOURCE TITLE
						$getTitleFor = $getItemFor->getTitle();
						//GET RESOURCE HREF
						$hrefFor = $getItemFor->getHref();
						$action->params = array_merge($action->params, array('location' => "<a href='$hrefFor'>$getTitleFor</a>"));
						$action_id = $action->save(); 
          }
        }

				//TRY TO ATTACH IF NECESSARY
				if ($action && $attachment) {
					$advancedactivityActions->attachActivity($action, $attachment);
				}
  
      } else {
        if($activityType == 'sitetagcheckin_content' || $activityType == 'sitetagcheckin_add_to_map') {
					$action = $actionTable->addActivity($viewer, $resource, $activityType, $body, array('checked_into_verb' => $checkedinto_verb, 'checkin' => $checkin_array, 'event_date' => $feed_event_date));
        } 
        else {
					parse_str($checkin, $checkin_locationparams);
					$checkin_params = array("checkin" => $checkin_locationparams);
					$location = $checkin_locationparams['label'];
					if($checkin_locationparams['vicinity']) {
						if(isset($checkin_locationparams['name']) && $checkin_locationparams['name'] && $checkin_locationparams['name'] != $checkin_locationparams['vicinity']) {
							$checkin_locationparams['label'] = $checkin_locationparams['name'] . ', ' . $checkin_locationparams['vicinity'];
						} else {
							$checkin_locationparams['label'] = $checkin_locationparams['vicinity'];
						}
					}

					$action = $actionTable->addActivity($viewer, $resource, $activityType, $body, array('prefixadd' => $checkin_locationparams['prefixadd'], 'checkin' => $checkin_locationparams,'event_date' => $feed_event_date));    
					if (!$is_mobile) {
						$checkin_locationparams['label'] = $this->view->htmlLink($this->view->url(array('guid' => $action->getGuid(),'format'=>'smoothbox'), 'sitetagcheckin_viewmap', true), $checkin_locationparams['label'], array('class' => 'smoothbox'));  
					} else {
						$checkin_locationparams['label'] = $this->view->htmlLink($this->view->url(array('guid' => $action->getGuid()), 'sitetagcheckin_viewmap', true), $checkin_locationparams['label'], array());
					}
					if(empty($checkin_locationparams['resource_guid'])) {
						$action->params = array_merge($action->params, array('location' => $checkin_locationparams['label']));
						$action_id = $action->save(); 
						$object_id = $resource_id = $action_id;
						$object_type = $resource_type = "activity_action";
					} else {
						$getItemFor =  Engine_Api::_()->getItemByGuid($checkin_locationparams['resource_guid']);
						$object_id = $resource_id = $getItemFor->getIdentity();
						$object_type = $resource_type= $getItemFor->getType();
						//GET RESOURCE TITLE
						$getTitleFor = $getItemFor->getTitle();
						//GET RESOURCE HREF
						$hrefFor = $getItemFor->getHref();
						$action->params = array_merge($action->params, array('location' => "<a href='$hrefFor'>$getTitleFor</a>"));
						$action_id = $action->save(); 
					}
        }
				//TRY TO ATTACH IF NECESSARY
				if ($action && $attachment) {
					$actionTable->attachActivity($action, $attachment);
				}
      }

      //START THE WORK FOR TAGGING
      if ($action && isset($postData['toValues']) && !empty($postData['toValues'])) {
        $actvityNotification = Engine_Api::_()->getDbtable('notifications', 'activity');
        $actionTag = new Engine_ProxyObject($action, Engine_Api::_()->getDbtable('tags', 'core'));
        $users = array_values(array_unique(explode(",", $postData['toValues'])));
        $params = (array) $action->params;
				$type_name = $this->view->translate(str_replace('_', ' ', 'post'));
        foreach (Engine_Api::_()->getItemMulti('user', $users) as $tag) {
          $actionTag->addTagMap($viewer, $tag, null);
          if (!(is_array($params) && isset($params['checkin']))) {
            $actvityNotification->addNotification(
                    $tag, $viewer, $action, 'tagged', array(
                'object_type_name' => $type_name,
                'label' => $type_name,
                    )
            );
          } else {
            //GET LABEL
            $label = $params['checkin']['label'];
            $checkin_resource_guid = $params['checkin']['resource_guid'];
            //MAKE LOCATION LINK
            if (isset($checkin_resource_guid) && empty($checkin_resource_guid)) {
              $locationLink = $this->view->htmlLink('https://maps.google.com/?q=' . urlencode($label), $label, array('target' => '_blank'));
            } else {
              $pageItem = Engine_Api::_()->getItemByGuid($checkin_resource_guid);
              $pageLink = $pageItem->getHref();
              $pageTitle = $pageItem->getTitle();
              $locationLink = "<a href='$pageLink'>$pageTitle</a>";
            }
            //SEND NOTIFICATION
            $actvityNotification->addNotification($tag, $viewer, $action, "sitetagcheckin_tagged", array("location" => $locationLink, "label" => $type_name));
          }
        }

        if($activityType == 'sitetagcheckin_lct_add_to_map') {
					$apiSitetagCheckin = Engine_Api::_()->sitetagcheckin();
					$users = array_values(array_unique(explode(",", $postData['toValues'])));
					$actionParams = (array) $action->params;
					if (isset($actionParams['checkin'])) {
						foreach (Engine_Api::_()->getItemMulti('user', $users) as $tag) {
							$apiSitetagCheckin->saveCheckin($actionParams['checkin'], $action, $actionParams, $tag->user_id);
						}
					}
        }
      }

      //GET ADDLOCAITON TABLE
      $addLocationTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');

      //SET LOCATION ID
      if (!empty($location)) {
        $location_id = $addLocationTable->getLocationId($location);
      }

      //SET ITEM ID
      $action_id = $action->getIdentity();

      //CHECKIN ARRAY
      $content = array(
          'location_id' => $location_id,
          'type' => 'checkin',
          'item_id' => $action_id,
          'item_type' => 'activity_action',
          'params' => $checkin_params,
          'action_id' => $action_id,
          'event_date' => $event_date,
          'owner_id' => $viewer_id
      );
  
      //GET ATTACHMENT CUNT
      $attachmentCount = count($action->getAttachments());
      if (empty($attachmentCount)) {
        $content_array = array(
            'resource_id' => $resource_id,
            'resource_type' => $resource_type,
            'object_id' => $resource_id,
            'object_type' => $resource_type
        );
        $addLocationTable->saveLocation(array_merge($content_array, $content));
      } else {
        foreach ($action->getAttachments() as $attachment) {
          $attact_resource_type = $attachment->meta->type;
          $attach_resource_id = $attachment->meta->id;
        }
        if ($resource_type == 'blog' || $resource_type == 'group' || $resource_type == 'poll' || $resource_type == 'video' || $resource_type == 'document' || $resource_type == 'forum' || $resource_type == 'music' || $resource_type == 'album' || $resource_type == 'sitepagenote_note' || $resource_type == 'sitepageevent_event'  || $resource_type == 'sitebusinessnote_note' || $resource_type == 'sitebusinessevent_event' || $resource_type == 'sitegroupnote_note' || $resource_type == 'sitegroupevent_event') {
          $attach_resource_id = $resource_id;
          $attact_resource_type = $resource_type;
        }
        $content_array = array(
            'resource_id' => $attach_resource_id,
            'resource_type' => $attact_resource_type,
            'object_id' => $resource_id,
            'object_type' => $resource_type
        );
        $addLocationTable->saveLocation(array_merge($content_array, $content));
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e; //THIS SHOULD BE CAUGHT BY ERROR HANDLER
    }

    if($resource_type_user == 'user') {
			if( isset($viewer->username) && '' != trim($viewer->username) ) {
				$profileAddress = $viewer->username;
			} else if( isset($viewer->user_id) && $viewer->user_id > 0 ) {
				$profileAddress = $viewer->user_id;
			}
      $this->_forward('success', 'utility', 'core', array(       
        'redirect' => $this->_helper->url->url(array('id' => $profileAddress, 'tab' => $tab), 'user_profile', true),       
        'messages' => array(Zend_Registry::get('Zend_Translate')->_("You've Checked In Successfully.")
            )));
    } else {
			//GET CHECKIN COUNT
			$getCheckinCount = $addLocationTable->getCheckinCount($viewer, $resource_id, $resource_type, 'checkin', 'parent_id');
			$this->view->show_success_message ="";
			$this->view->status = true;
			$checkin_location = "<a href='$href'>$getTitle</a>";
			$show_label = $this->view->translate(array("%3s %1s. %4s %2s time.", "%3s %1s. %4s %2s times.", $getCheckinCount), $this->view->translate($checkin_your), $checkin_location,$this->view->translate($checkin_your),  $this->view->locale()->toNumber($getCheckinCount));
			$this->_forward('success', 'utility', 'core', array(
						'messages' => array($show_label),					
            'redirect' => $resource->getHref()
						
				));
    }
  }

  //ACTION FOR SHOWING THE CHECKIN USERS
  public function seeAllCheckinUserAction() {

    //GET RESOURCE TYPE
    $this->view->resource_type = $resource_type = $this->_getParam('resource_type');

    //GET RESOURCE ID
    $this->view->resource_id = $resource_id = $this->_getParam('resource_id');

    //GET CHECKED IN STATUS
    $this->view->checkedin_see_all_heading = $this->_getParam('checkedin_see_all_heading');

    //CHECK SUBJECT IS EXIST OR NOT IF NOT EXIST THEN SET ACCORDING TO THE PAGE ID AND PHOTO ID
    if (!Engine_Api::_()->core()->hasSubject()) {
      if (0 !== ($resource_id = (int) $this->_getParam('resource_id')) &&
              null !== ($resource = Engine_Api::_()->getItem($resource_type, $resource_id))) {
        Engine_Api::_()->core()->setSubject($resource);
      }
    }

    //GET PAGE NUMBER
    $this->view->page = $page = $this->_getParam('page', 1);

    //GET SEARCH TEXT
    $this->view->search = $search = $this->_getParam('search', '');

    //SET AJAX REQUEST
    $this->view->is_ajax = $this->_getParam('is_ajax', 0);

    //SET CALL STATUS
    $call_status = $this->_getParam('call_status');

    //GET CHECKIN COUNT
    $this->view->checkedin_item_count = $checkedin_item_count = (int) $this->_getParam('checkedin_item_count');

    //GET CHECKED IN STATUS
    $checkedin_status = $this->_getParam('checkedin_status');

    //SET CALL STATUS PUBLIC IF RESOURCE TYPE FORUM AND EMPTY
    if (empty($call_status) && $resource_type == 'forum_topic') {
      $call_status = 'public';
    }

    //SEND CALL STATUS TO THE TPL
    $this->view->call_status = $call_status;

    //GET ADDLOCAITON TABLE
    $addlocationsTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');

    //SELECT CHECKIN
    $checkin_fetch = $addlocationsTable->getCheckinUsers($resource, $checkedin_status, $search, $call_status);

    //COUNT TOTAL CHECKIN
    $check_in_result = $checkin_fetch->getTotalItemCount();

    if (!empty($check_in_result)) {
      $this->view->user_obj = $checkin_fetch;
    } else {
      $this->view->no_result_msg = $this->view->translate('No results were found.');
    }
    $checkin_fetch->setCurrentPageNumber($page);
    $checkin_fetch->setItemCountPerPage($checkedin_item_count);

    //GET COUNT OF ALL USERS
    $this->view->public_count = $addlocationsTable->getCheckinUsers($resource, $checkedin_status, $search, 'public', 1);

    //GET COUNT OF FRIEND
    $this->view->friend_count = $addlocationsTable->getCheckinUsers($resource, $checkedin_status, $search, 'friend', 1);
  }

  //ACTION FOR GETTING THE INITIALISE SUGGESTION 
  public function suggestAction() {

    $coreModule = Engine_Api::_()->getDbtable('modules', 'core');
    //INITIALISE DATA ARRAY
    $data = array();

    //GET TYPING CONTENT
    $text = $this->_getParam('suggest', null);

    //INITIALISE SUGGESION
    $initial_suggestion = 0;

    //FOR FIRST TIME IF THERE IS NO TEXT 
    if (empty($text)) {
      $text = $this->_getParam('location_detected', null);
      $initial_suggestion = 1;
    }

    //CHECK SITEPAGE IS ENABLED OR NOT
    $sitepageEnabled = $coreModule->isModuleEnabled('sitepage');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    //CHECK SITEBUSIENSS IS ENABLED OR NOT
    $sitebusinessEnabled = $coreModule->isModuleEnabled('sitebusiness');
    $sitegroupEnabled = $coreModule->isModuleEnabled('sitegroup');
    $sitestoreEnabled = $coreModule->isModuleEnabled('sitestore');
		$siteeventEnabled = $coreModule->isModuleEnabled('siteevent');
    //CEHCK TEXT
    if (null !== $text) {

      //GET LATITUDE
      $latitude = $this->_getParam('latitude', 0);

      //GET LONGITUDE
      $longitude = $this->_getParam('longitude', 0);

      //COUNT
      $count = 0;

      //GET SITETAGCHECKIN API
      $apiSitetagcheckin = Engine_Api::_()->sitetagcheckin();

      //INITIALISE RESOURCE PAGE IDS
      $resourcePageIds = '';
      //INITIALISE PREVIOUS PAGE DATA
      $previousPageData = array();
      //INITIALISE PAGE DATA
      $pageData = array();
      //INITIALISE PAGE FlAG
      $pageFlag = 1;

      //INITIALISE RESOURCE BUSINESS IDS
      $resourceBusinessIds = '';
      //INITIALISE PREVIOUS BUSINESS DATA
      $previousBusinessData = array();
      //INITIALISE BUSINESS DATA
      $businessData = array();
      //INITIALISE BUSINESS FlAG
      $businessFlag = 1;

      //INITIALISE RESOURCE GROUP IDS
      $resourceGroupIds = '';
      //INITIALISE PREVIOUS GROUP DATA
      $previousGroupData = array();
      //INITIALISE GROUP DATA
      $groupData = array();
      //INITIALISE GROUP FlAG
      $groupFlag = 1;

      //INITIALISE RESOURCE STORE IDS
      $resourceStoreIds = '';
      //INITIALISE PREVIOUS GROUP DATA
      $previousStoreData = array();
      //INITIALISE GROUP DATA
      $storeData = array();
      //INITIALISE GROUP FlAG
      $storeFlag = 1;

      //INITIALISE RESOURCE EVENT IDS
      $resourceEventIds = '';
      //INITIALISE PREVIOUS EVENT DATA
      $previousEventData = array();
      //INITIALISE EVENT DATA
      $eventData = array();
      //INITIALISE EVENT FlAG
      $eventFlag = 1;

      //INITIALISE PREVIOUS PLACES
      $previousPlaces = array();

      $tagged_location = $settings->getSetting('sitetagcheckin.tagged.location', 1);

      //SHOW SELECTABLE CONETNT
      $showSelectableContents = $settings->getSetting('sitetagcheckin.selectable', '');

      //INITIALISE GOOGLE PLACE FlAG
      $googleplacesFlag = 1;

      //IF ADMIN HAS SET TO DISPLAY THE PAGES / BUSINESSES / GOOGLE PLACES TO SHOW IN THE AUTOSUGGEST
      if (!empty($showSelectableContents)) { 
         $checkMobMode = !Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode');
        if (!in_array('pages', $showSelectableContents)) {
          $pageFlag = 0;
        }
        if (!in_array('businesses', $showSelectableContents)) {
          $businessFlag = 0;
        }
        if (!in_array('groups', $showSelectableContents)) {
          $groupFlag = 0;
        }
        if (!in_array('stores', $showSelectableContents)) {
          $storeFlag = 0;
        }
        if (!in_array('events', $showSelectableContents)  || $checkMobMode) {
          $eventFlag = 0;
        }
        if (!in_array('googleplaces', $showSelectableContents)) {
          $googleplacesFlag = 0;
        }
      }

      //CHECK INITIALISE SUGGESION AND ALSO IF HE WAT TO SAVE THE PREVIOUS CHECKIN LOCATIONS
      if ($initial_suggestion == 1 && $tagged_location) {

        //GET PREVIOUS GOOGLE PLACES
        if (!empty($googleplacesFlag)) {
          $previousGooglePlacesResults = $apiSitetagcheckin->getPreviousGooglePlacesResults();
        }

        //SITEPAGEENALEB THEN GETITNG THE PREVIOUS SUGGEST CONTENT
        if ($sitepageEnabled && $pageFlag) {
          $previousPageResult = $apiSitetagcheckin->getPreviousSuggestContent($text, 'sitepage_page');
          foreach ($previousPageResult as $pageResult) {
            $pageResult['id'] = 'sitetagcheckin_' . $count++;
            $resourcePageIds .= $pageResult['resource_id'] . ',';
            $previousPageData[] = $pageResult;
          }
        }

        //SITEBUSINESSENALEB THEN GETITNG THE PREVIOUS SUGGEST CONTENT
        if ($sitebusinessEnabled && $businessFlag) {
          $previousBusinessResult = $apiSitetagcheckin->getPreviousSuggestContent($text, 'sitebusiness_business');
          foreach ($previousBusinessResult as $businessResult) {
            $businessResult['id'] = 'sitetagcheckin_' . $count++;
            $resourceBusinessIds .= $businessResult['resource_id'] . ',';
            $previousBusinessData[] = $businessResult;
          }
        }

        //SITEGROUPENALEB THEN GETITNG THE PREVIOUS SUGGEST CONTENT
        if ($sitegroupEnabled && $groupFlag) {
          $previousGroupResult = $apiSitetagcheckin->getPreviousSuggestContent($text, 'sitegroup_group');
          foreach ($previousGroupResult as $groupResult) {
            $groupResult['id'] = 'sitetagcheckin_' . $count++;
            $resourceGroupIds .= $groupResult['resource_id'] . ',';
            $previousGroupData[] = $groupResult;
          }
        }

        //SITEGROUPENALEB THEN GETITNG THE PREVIOUS SUGGEST CONTENT
        if ($sitestoreEnabled && $storeFlag) {
          $previousStoreResult = $apiSitetagcheckin->getPreviousSuggestContent($text, 'sitestore_store');
          foreach ($previousStoreResult as $stroeResult) {
            $stroeResult['id'] = 'sitetagcheckin_' . $count++;
            $resourceStoreIds .= $stroeResult['resource_id'] . ',';
            $previousStoreData[] = $stroeResult;
          }
        }

        //SITEGROUPENALEB THEN GETITNG THE PREVIOUS SUGGEST CONTENT
        if ($siteeventEnabled && $eventFlag) {
          $previousEventResult = $apiSitetagcheckin->getPreviousSuggestContent($text, 'siteevent_event');
          foreach ($previousEventResult as $eventResult) {
            $eventResult['id'] = 'sitetagcheckin_' . $count++;
            $resourceEventIds .= $eventResult['resource_id'] . ',';
            $previousEventData[] = $eventResult;
          }
        }

        //MAKE PREVIOUS GOOGLE PLACE RESULTS ARRAY
        foreach ($previousGooglePlacesResults as $previousGooglePalces) {
          $previousGooglePalces['id'] = 'sitetagcheckin_' . $count++;
          $previousGooglePalces['type'] = 'place';
          $previousGooglePalces['prefixadd'] = 'in';
          $previousGooglePalces['photo'] = '<img class="thumb_icon item_photo_user" alt="" src="application/modules/Sitetagcheckin/externals/images/map_icon.png" />';
          $previousPlaces[] = $previousGooglePalces;
        }
      }

      //MAKE PAGE ARRAY
      if (!empty($pageFlag) && $sitepageEnabled) {
        $pageResult = $apiSitetagcheckin->getSuggestContent($text, 'sitepage_page', $resourcePageIds);
        foreach ($pageResult as $page) {
          $page['id'] = 'sitetagcheckin_' . $count++;
          $pageData[] = $page;
        }

        if (!empty($previousPageData)) {
          $pageData = array_merge($previousPageData, $pageData);
        }
      }

      //MAKE BUSINESS ARRAY
      if (!empty($businessFlag) && $sitebusinessEnabled) {
        $businessResult = $apiSitetagcheckin->getSuggestContent($text, 'sitebusiness_business', $resourceBusinessIds);
        foreach ($businessResult as $business) {
          $business['id'] = 'sitetagcheckin_' . $count++;
          $businessData[] = $business;
        }

        if (!empty($previousBusinessData)) {
          $businessData = array_merge($previousBusinessData, $businessData);
        }
      }

      //MAKE GROUP ARRAY
      if (!empty($groupFlag) && $sitegroupEnabled) {
        $groupResult = $apiSitetagcheckin->getSuggestContent($text, 'sitegroup_group', $resourceGroupIds);
        foreach ($groupResult as $group) {
          $group['id'] = 'sitetagcheckin_' . $count++;
          $groupData[] = $group;
        }

       if (!empty($previousGroupData)) {
          $groupData = array_merge($previousGroupData, $groupData);
       }

      }

      //MAKE Event ARRAY
      if (!empty($eventFlag) && $siteeventEnabled) {
        $eventResult = $apiSitetagcheckin->getSuggestContent($text, 'siteevent_event', $resourceEventIds);
        foreach ($eventResult as $event) {
          $event['id'] = 'sitetagcheckin_' . $count++;
          $eventData[] = $event;
        }

       if (!empty($previouseventData)) {
          $eventData = array_merge($previouseventData, $eventData);
       }

      }

      //MAKE GOOGLE PLACE ARRAY
      if (!empty($googleplacesFlag)) {
        $suggestGooglePalces = $apiSitetagcheckin->getSuggestGooglePalces($text, $latitude, $longitude);
        foreach ($suggestGooglePalces as $key => $palces) {
          if (!empty($previousGooglePlacesResults)) {
            foreach ($previousGooglePlacesResults as $previousGooglePlaces) {
              if (isset($palces['label']) && isset($previousGooglePlaces['label']) && $palces['label'] == $previousGooglePlaces['label']) {
                unset($suggestGooglePalces[$key]);
              }
            }
          }
        }

        foreach ($suggestGooglePalces as $key => $palces) {
          $palces['id'] = 'sitetagcheckin_' . $count++;
          $palces['type'] = 'place';
          $palces['prefixadd'] = 'in';
          $palces['photo'] = '<img class="thumb_icon item_photo_user" alt="" src="application/modules/Sitetagcheckin/externals/images/map_icon.png" />';
          $previousPlaces[] = $palces;
        }
      }

      //MAKE PAGE AND BUSINESS DATA
      $data = array_merge($pageData, $businessData);

      $data = array_merge($data, $groupData);
      
      $data = array_merge($data, $storeData);

			$data = array_merge($data, $eventData);

      //MAKE FINAL DATA
      $data = array_merge($data, $previousPlaces);

      //IF JUST USE BY ADMIN FOR LOCAITON THEN MAKE THE ARRAY FOR JUST USE
      $text = $this->_getParam('suggest', null);
      if (!empty($text)) {
        $data[] = array("id" => 'just_use_li', 'type' => 'just_use', 'label' => $text, 'prefixadd' => $this->view->translate('at'), 'latitude' => 0, 'longitude' => 0, 'li_html' => $this->view->translate('Just use') . ' "' . $text . '"', 'google_id' => 1);
      }
    }

    if ($this->_getParam('sendNow', true)) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }
}
