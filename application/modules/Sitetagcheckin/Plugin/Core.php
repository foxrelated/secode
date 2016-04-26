<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function routeShutdown(Zend_Controller_Request_Abstract $request) {

    //CHECK IF ADMIN
    if (substr($request->getPathInfo(), 1, 5) == "admin") {
      return;
    }
    
    $module = $request->getModuleName(); 
		$controller = $request->getControllerName();
		$action = $request->getActionName();
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('mobi'))
      return;

    $mobile = $request->getParam("mobile");
    $session = new Zend_Session_Namespace('mobile');

    if ($mobile == "1") {
      $mobile = true;
      $session->mobile = true;
    } elseif ($mobile == "0") {
      $mobile = false;
      $session->mobile = false;
    } else {
      if (isset($session->mobile)) {
        $mobile = $session->mobile;
      } else {
        //CHECK TO SEE IF MOBILE
        if (Engine_Api::_()->mobi()->isMobile()) {
          $mobile = true;
          $session->mobile = true;
        } else {
          $mobile = false;
          $session->mobile = false;
        }
      }
    }

    if (!$mobile) {
      return;
    }
    
    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();
    if ($module == "sitetagcheckin") {
      if ($controller == "index" && $action == "by-locations") {
        $request->setControllerName('index');
        $request->setActionName('mobileby-locations');
      }
    }

    //CREATE LAYOUT
    $layout = Zend_Layout::startMvc();

    //SET OPTIONS
    $layout->setViewBasePath(APPLICATION_PATH . "/application/modules/Mobi/layouts", 'Core_Layout_View')
            ->setViewSuffix('tpl')
            ->setLayout(null);
  }
  
  public function onActivityActionDeleteBefore($event) {
    $item = $event->getPayload();
    Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin')->delete(array(
        'action_id = ?' => $item->action_id
    ));
  }

  public function onCoreTagMapDeleteBefore($event) {
    $item = $event->getPayload();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (isset($item->tag_id) && $item->tag_id != $viewer_id) {
      Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin')->delete(array(
          'owner_id = ?' => $item->tag_id, 'object_type = ?' => $item->resource_type, 'object_id =?' => $item->resource_id));
    }
  }

  public function onCoreTagMapCreateAfter($event) {
    $item = $event->getPayload();

    //GET OBJECT TYPE
    $object_type = $item->resource_type;
    if ($object_type == 'activity_action' || strstr($object_type, 'photo')) {

      //GET USER ID
      $user_id = $item->tag_id;

      //GET USER ITEM
      $userItem = Engine_Api::_()->getItem("user", $user_id);


      if ($object_type == 'activity_action') {
        $type = 'post';
      } else {
        $type = 'photo';
      }

      //GET OBJECT ID
      $object_id = $item->resource_id;

      //GET VIEWER INFORMATION
      $viewer = Engine_Api::_()->user()->getViewer();

      //GET VIEWER ID
      $viewer_id = $viewer->getIdentity();

      //ADDLOCATION TABLE
      $addLocationTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');

      //SET PARAMS
      $params = array();
      $params['checkowner'] = 2;
      $params['owner_id'] = $viewer_id;

      if ($object_type == 'activity_action') {
        $params['item_type'] = $object_type;
        $params['item_id'] = $object_id;
      } else {
        $params['object_type'] = $object_type;
        $params['object_id'] = $object_id;
      }
      //GET RESULTS
      $results = $addLocationTable->getCheckinsDetails($params);

      //CHECK RESULTS
      if (!empty($results)) {

        //GET ROWS CONTENT
        $rowsContent = $results->toarray();

        //UNSETTING THE PRIMARY KEY    
        unset($rowsContent['addlocation_id']);

        //SET PARAMS
        $params['owner_id'] = $user_id;

        //GET ROWS 
        $rowADD = $addLocationTable->getCheckinsDetails($params);

        //CHECK ROW
        if (empty($rowADD)) {

          //CREATE ROW
          $content = $addLocationTable->createRow();
          $content->setFromArray($rowsContent);
          $content->owner_id = $user_id;
          $content->item_type = $object_type;
          $content->item_id = $object_id;
          $content->save();
          $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
          //GET LABEL
          $label = $rowsContent['params']['checkin']['label'];
          $resource_guid = $rowsContent['params']['checkin']['resource_guid'];
          $checkintype = $rowsContent['params']['checkin']['type'];
          $locationLink = '';
          $guid = '';
          $object_type_array = array('activity_action', 'album_photo', 'group_photo', 'event_photo', 'list_photo', 'recipe_photo', 'sitepage_photo', 'sitepagenote_photo', 'sitebusiness_photo', 'sitebusinessnote_photo', 'sitegroup_photo', 'sitegroupnote_photo', 'sitestore_photo', 'siteevent_photo');
          $is_mobile = Engine_Api::_()->seaocore()->isMobile();
          //MAKE LOCATION LINK
          if (isset($resource_guid) && empty($resource_guid)) {
            if ($checkintype == 'just_use') {
              $locationLink = $label;
            } else {
              if (in_array($object_type, $object_type_array)) {
                $guid = 'activity_action_' . $rowsContent['action_id'];
                if(!$is_mobile) {
									$locationLink = $view->htmlLink($view->url(array('guid' => $guid,'format'=>'smoothbox'), 'sitetagcheckin_viewmap', true), $label, array('class' => 'smoothbox'));
                } else {
									$locationLink = $view->htmlLink($view->url(array('guid' => $guid), 'sitetagcheckin_viewmap', true), $label, array());
                }
              }
            }
          } else {
            $pageItem = Engine_Api::_()->getItemByGuid($resource_guid);
            $pageLink = $pageItem->getHref();
            $pageTitle = $pageItem->getTitle();
            $locationLink = "<a href='$pageLink'>$pageTitle</a>";
          }
          $getitem = Engine_Api::_()->getItem($object_type, $object_id);
          //SEND NOTIFICATION
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($userItem, $viewer, $getitem, "sitetagcheckin_tagged", array("location" => $locationLink, "label" => $type));
        }
      }
    }
  }
  
  public function addActivity($event) {

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    $payload = $event->getPayload();

    if (($module == 'group' || $module == 'advgroup') && ($action == 'create' || $action == 'edit') && ($controller == 'index' || $controller == 'group')) {

			//GET GROUP ID
      $group_id = $payload['object']->group_id;

			//GET GROUP OBJECT
      $group = Engine_Api::_()->getItem('group', $group_id);

			if ($action == 'edit') {
			  //DELETE THE RESULT FORM THE TABLE.
				Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $group_id, 'resource_type = ?' => 'group'));
			}

			if (!empty($_POST['location'])) {
				$seao_locationid = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($_POST['location'], '', 'group', $group_id);

				Engine_Api::_()->getItemTable('group')->update(array('seao_locationid'=>  $seao_locationid, 'location' => $_POST['location']), array('group_id =?' => $group_id));
			} else {
				Engine_Api::_()->getItemTable('group')->update(array('seao_locationid'=>  0, 'location' => ''), array('group_id =?' => $group_id));
			}
    }
    
//     if (($module == 'video' || $module == 'advvideo') && ($action == 'create' || $action == 'edit') && ($controller == 'index' || $controller == 'video')) {
// 
// 			//GET VIDEO ID
//       $video_id = $payload['object']->video_id;
// 
// 			//GET VIDEO OBJECT
//       $video = Engine_Api::_()->getItem('video', $video_id);
// 
// 			if ($action == 'edit') {
// 			  //DELETE THE RESULT FORM THE TABLE.
// 				Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $video_id, 'resource_type = ?' => 'video'));
// 			}
// 
// 			if (!empty($_POST['location'])) {
// 				$seao_locationid = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($_POST['location'], '', 'video', $video_id);
// 
// 				Engine_Api::_()->getItemTable('video')->update(array('seao_locationid'=>  $seao_locationid, 'location' => $_POST['location']), array('video_id =?' => $video_id));
// 			} else {
// 				Engine_Api::_()->getItemTable('video')->update(array('seao_locationid'=>  0, 'location' => ''), array('video_id =?' => $video_id));
// 			}
//     }

//    if (($module == 'album') && ($action == 'upload' || $action == 'edit') && ($controller == 'index' || $controller == 'album')) {
//
//			//GET VIDEO ID
//      $album_id = $payload['object']->album_id;
//
//			//GET VIDEO OBJECT
//      $album = Engine_Api::_()->getItem('album', $album_id);
//
//			if ($action == 'edit') {
//			  //DELETE THE RESULT FORM THE TABLE.
//				Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $album_id, 'resource_type = ?' => 'album'));
//			}
//
//			if (!empty($_POST['location'])) {
//				$seao_locationid = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($_POST['location'], '', 'album', $album_id);
//
//				Engine_Api::_()->getItemTable('album')->update(array('seao_locationid'=>  $seao_locationid, 'location' => $_POST['location']), array('album_id =?' => $album_id));
//			} else {
//				Engine_Api::_()->getItemTable('album')->update(array('seao_locationid'=>  0, 'location' => ''), array('album_id =?' => $album_id));
//			}
//    }
  }
	
  public function onEventCreateAfter($event) {

    $item = $event->getPayload();
    $front = Zend_Controller_Front::getInstance();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();

    if ($controller == 'index' && $action == 'create') {
			//Accrodeing to event  location entry in the seaocore location table.
			if (!empty($item->location)) {
				$seao_locationid = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($item->location, '', 'event', $item->event_id);

				//event table entry of location id.
				Engine_Api::_()->getItemTable('event')->update(array('seao_locationid'=>  $seao_locationid), array('event_id =?' => $item->event_id));
			}
		}
  }

	public function onEventUpdateAfter($event) {

    $item = $event->getPayload();
    $front = Zend_Controller_Front::getInstance();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();

    if ($controller == 'event' && $action == 'edit') {
			if (!empty($item->location)) {
			
				//DELETE THE RESULT FORM THE TABLE.
				Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $item->event_id, 'resource_type = ?' => 'event'));
			
				$seaoLocation = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($item->location, '', 'event', $item->event_id);

				//event table entry of location id.
				Engine_Api::_()->getItemTable('event')->update(array('seao_locationid'=>  $seaoLocation), array('event_id =?' => $item->event_id));
			}
		}
	}
	
  public function onUserCreateAfter($event) {

    $item = $event->getPayload();

    $front = Zend_Controller_Front::getInstance();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    $userSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.usersettings');
    if (!empty($userSettings)) {
			if ($controller == 'signup' && $action == 'index') {
				if (empty($_SESSION["User_Plugin_Signup_Account"]["data"]["profile_type"])) {
					$_SESSION["User_Plugin_Signup_Account"]["data"]["profile_type"] = 1;
				}
				if (!empty($_SESSION["User_Plugin_Signup_Account"]["data"]["profile_type"])) {

					$profile_type_id = $_SESSION["User_Plugin_Signup_Account"]["data"]["profile_type"];
					
					$db = Zend_Db_Table_Abstract::getDefaultAdapter();
					$table_exist = $db->query('SHOW TABLES LIKE \'engine4_user_fields_search\'')->fetch();
					if (!empty($table_exist)) {
						$column_exist = $db->query('SHOW COLUMNS FROM engine4_user_fields_search LIKE \'location\'')->fetch();
					}
	
					$profilemapsTable = Engine_Api::_()->getDbtable('profilemaps', 'sitetagcheckin');
					$profilemapsTablename = $profilemapsTable->info('name');
					
					$select = $profilemapsTable->select()
										->from($profilemapsTablename, array('profile_type'))
										->where($profilemapsTablename . '.option_id = ?', $profile_type_id);
					$option_id = $select->query()->fetchColumn();

					if (!empty($option_id)) {
					
						$option_id_location = $_SESSION["User_Plugin_Signup_Fields"]["data"][$option_id];
						if (!empty($option_id_location)) {
							if( $item instanceof User_Model_User ) {
							
								$seaoLocation = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($option_id_location, '', 'user', $item->getIdentity());
								
								if (!empty($column_exist)) {
									Engine_Api::_()->fields()->getTable('user', 'search')->update(array('location' => $option_id_location), array('item_id =?' => $item->getIdentity()));
								}
								
								Engine_Api::_()->getDbtable('users', 'user')->update(array('seao_locationid'=>  $seaoLocation, 'location' => $option_id_location), array('user_id =?' => $item->getIdentity()));
							}
						}
					}
				}
			}
    }
	}
	
	public function onUserUpdateAfter($event) {

    $item = $event->getPayload();
    $front = Zend_Controller_Front::getInstance();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    $userSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.usersettings');
    if (!empty($userSettings)) {
			if ($controller == 'edit' && $action == 'profile') {

				$user = Engine_Api::_()->core()->getSubject();
				
				// Update display name
				$aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
				
				$db = Zend_Db_Table_Abstract::getDefaultAdapter();
				$table_exist = $db->query('SHOW TABLES LIKE \'engine4_user_fields_search\'')->fetch();
				if (!empty($table_exist)) {
					$column_exist = $db->query('SHOW COLUMNS FROM engine4_user_fields_search LIKE \'location\'')->fetch();
				}

				$profilemapsTable = Engine_Api::_()->getDbtable('profilemaps', 'sitetagcheckin');
				$profilemapsTablename = $profilemapsTable->info('name');
				
				$select = $profilemapsTable->select()
									->from($profilemapsTablename, array('profile_type'))
									->where($profilemapsTablename . '.option_id = ?', $aliasValues['profile_type']);
				$option_id =  $select->query()->fetchColumn();
				
				if (!empty($option_id)) {
				
					$valuesTable = Engine_Api::_()->fields()->getTable('user', 'values');
					$valuesTableName = $valuesTable->info('name');

					$select = $valuesTable->select()
										->from($valuesTableName, array('value'))
										->where($valuesTableName . '.item_id = ?', $item->user_id)
										->where($valuesTableName . '.field_id = ?', $option_id);
					$valuesResultsLocation = $select->query()->fetchColumn();

					if (!empty($valuesResultsLocation)) {

						//DELETE THE RESULT FORM THE TABLE.
						Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $item->user_id, 'resource_type = ?' => 'user'));

						$seaoLocation = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($valuesResultsLocation, '', 'user', $item->user_id);
						
						if (!empty($column_exist)) {
							Engine_Api::_()->fields()->getTable('user', 'search')->update(array('location' => $valuesResultsLocation), array('item_id =?' => $item->user_id));
            }
            
						//event table entry of location id.
						Engine_Api::_()->getDbtable('users', 'user')->update(array('seao_locationid'=>  $seaoLocation, 'location' => $valuesResultsLocation), array('user_id =?' => $item->user_id));
					}
					else {
						//DELETE THE RESULT FORM THE TABLE.
						Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $item->user_id, 'resource_type = ?' => 'user'));

						//event table entry of location id.
						Engine_Api::_()->getDbtable('users', 'user')->update(array('seao_locationid'=>  0, 'location' => ''), array('user_id =?' => $item->user_id));
					}
				}
      }
		}
	}
  
	public function  onItemDeleteBefore($event) {
  
    $front = Zend_Controller_Front::getInstance();
		$module = $front->getRequest()->getModuleName();
    $item = $event->getPayload();
  
    if ($module == 'event' || $module == 'user' || $module == 'video' || $module == 'group' || $module == 'album') {
      $resourceId  = $item->getIdentity();
      switch ($module) {
        case 'event':
          $resourceType = 'event';
        break;
        case 'album':
          $resourceType = 'album';
        break;
        case 'video':
          $resourceType = 'video';
        break;
        case 'group':
          $resourceType = 'group';
        break;
        case 'user':
          $resourceType = 'user';
        break;
      }
    
      if($resourceId && $resourceType) {
        Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $resourceId, 'resource_type = ?' => $resourceType));
      }
    }
  }

  public function onRenderLayoutDefault($event) {
	
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$front = Zend_Controller_Front::getInstance();
		$module = $front->getRequest()->getModuleName();
		$controller = $front->getRequest()->getControllerName();
		$action = $front->getRequest()->getActionName();
		
		$userSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.usersettings');
		
		$URL = $view->url(array(), 'sitetagcheckin_userbylocation', true);
		$Browse = $view->translate("Browse By Locations");
		if (!empty($userSettings)) {
		
			if ($module == 'user' && $controller == 'index' &&  $action == 'browse') {
			
				$script = <<<EOF
				window.addEvent('domready', function()
				{
				if($('global_content').getElement('.field_search_criteria')) {
					var element = $('global_content').getElement('.field_search_criteria');
				}
						new Element('a', {
						'id' : 'getcodeLink',
						'class' : 'buttonlink stcheckin_icon_map_search',
						'style' : 'margin-bottom:10px;',
						'href' : "$URL",
						'html' : "$Browse",
					}).inject(element, 'before');
				});
EOF;
				$view->headScript()->appendScript($script);
			}
		}
	}
}