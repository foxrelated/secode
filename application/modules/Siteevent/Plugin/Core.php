<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Plugin_Core extends Zend_Controller_Plugin_Abstract {

//  public function onRenderLayoutDefault($event) {
//    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
//    $view->headTranslate(array("now", 'in a few seconds', 'a few seconds ago', '%s minute ago', 'in %s minute', '%s hour ago', 'in %s hour', '%s at %s', 'Remove All'));
//  }

    public function onStatistics($event) {

        $table = Engine_Api::_()->getDbTable('events', 'siteevent');
        $select = new Zend_Db_Select($table->getAdapter());
        $select->from($table->info('name'), 'COUNT(*) AS count');
        $event->addResponse($select->query()->fetchColumn(0), 'event');
    }

//    public function onRenderLayoutMobileSMDefault($event) {
//        $view = $event->getPayload();
//        if (!($view instanceof Zend_View_Interface)) {
//            return;
//        }
//        $view->headScriptSM()
//                ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/sitemobile/core.js');
//    }

    //DELETE USERS BELONGINGS BEFORE THAT USER DELETION
    public function onItemDeleteBefore($event) {

        $item = $event->getPayload();
        if ($item instanceof Video_Model_Video) {
            Engine_Api::_()->getDbtable('clasfvideos', 'siteevent')->delete(array('video_id = ?' => $item->getIdentity()));
        }

        if ($item instanceof Siteevent_Model_Organizer || $item instanceof Sitepage_Model_Page || $item instanceof User_Model_User || $item instanceof Sitegroup_Model_Group || $item instanceof Sitestore_Model_Store || $item instanceof Sitebusiness_Model_Business) {
            $events = Engine_Api::_()->getDbTable('events', 'siteevent')->fetchAll(array('host_type' => $item->getType(), 'host_id' => $item->getIdentity()));
            foreach ($events as $event) {
                if ($event->owner_id !== $item->getIdentity() || $item->getType() != 'user') {
                    $event->host_type = 'user';
                    $event->host_id = $event->owner_id;
                }
            }
        }
    }

    public function onUserDeleteBefore($event) {

        //GET VIEWER ID
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $payload = $event->getPayload();
        if ($payload instanceof User_Model_User) {

            //VIDEO TABLE
            $siteeventvideoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');
            $siteeventvideoSelect = $siteeventvideoTable->select()->where('owner_id = ?', $payload->getIdentity());

            //RATING TABLE
            $ratingTable = Engine_Api::_()->getDbtable('videoratings', 'siteevent');

            foreach ($siteeventvideoTable->fetchAll($siteeventvideoSelect) as $siteeventvideo) {
                $ratingTable->delete(array('videorating_id = ?' => $siteeventvideo->video_id));
                $siteeventvideo->delete();
            }

            $ratingSelect = $ratingTable->select()->where('user_id = ?', $payload->getIdentity());
            $ratingVideoDatas = $ratingTable->fetchAll($ratingSelect)->toArray();

            if (!empty($ratingVideoDatas)) {
                foreach ($ratingVideoDatas as $ratingvideo) {
                    $ratingTable->delete(array('user_id = ?' => $ratingvideo['user_id']));
                    $video_id = $ratingvideo['videorating_id'];
                    $avg_rating = $ratingTable->rateVideo($ratingvideo['videorating_id']);
                    $siteeventvideoTable->update(array('rating' => $avg_rating), array('video_id = ?' => $ratingvideo['videorating_id']));
                }
            }

            //DELETE SITEEVENTS
            $siteeventTable = Engine_Api::_()->getDbtable('events', 'siteevent');
            $siteeventSelect = $siteeventTable->select()->where('owner_id = ?', $payload->getIdentity());
            foreach ($siteeventTable->fetchAll($siteeventSelect) as $siteevent) {
                $siteevent->delete();
            }

            //DELETE REVIEWS
            $siteeventTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');
            $siteeventSelect = $siteeventTable->select()->where('owner_id = ?', $payload->getIdentity())->where('type in (?)', array('user', 'visitor'));
            foreach ($siteeventTable->fetchAll($siteeventSelect) as $siteevent) {
                $siteevent->delete();
            }

            //LIKE COUNT DREASE FORM EVENT TABLE.
            $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
            $likesTableSelect = $likesTable->select()->where('poster_id = ?', $payload->getIdentity())->Where('resource_type = ?', 'siteevent_event');
            $results = $likesTable->fetchAll($likesTableSelect);
            foreach ($results as $user) {
                $resource = Engine_Api::_()->getItem('siteevent_event', $user->resource_id);
								if($resource) {
									$resource->like_count--;
									$resource->save();
								}
            }

            //COMMENT COUNT DECREASE FORM EVENT TABLE.
            $commentsTable = Engine_Api::_()->getDbtable('comments', 'core');
            $commentsTableSelect = $commentsTable->select()->where('poster_id = ?', $payload->getIdentity())->Where('resource_type = ?', 'siteevent_event');
            $results = $commentsTable->fetchAll($commentsTableSelect);
            foreach ($results as $user) {
                $resource = Engine_Api::_()->getItem('siteevent_event', $user->resource_id);
								if($resource) {
									$resource->comment_count--;
									$resource->save();
								}
            }

            $commentsTableSelect = $commentsTable->select()->where('poster_id = ?', $payload->getIdentity())->Where('resource_type = ?', 'siteevent_review');
            $results = $commentsTable->fetchAll($commentsTableSelect);
            foreach ($results as $user) {
                $resource = Engine_Api::_()->getItem('siteevent_review', $user->resource_id);
								if($resource) {
									$resource->comment_count--;
									$resource->save();
								}
            }

            //LIKE COUNT DREASE FORM EVENT TABLE.
            $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
            $likesTableSelect = $likesTable->select()->where('poster_id = ?', $payload->getIdentity())->Where('resource_type = ?', 'siteevent_review');
            $results = $likesTable->fetchAll($likesTableSelect);
            foreach ($results as $user) {
                $resource = Engine_Api::_()->getItem('siteevent_review', $user->resource_id);
								if($resource) {
									$resource->like_count--;
									$resource->save();
								}
            }

            //GET EDITOR TABLE
            $editorTable = Engine_Api::_()->getDbTable('editors', 'siteevent');
            $isSuperEditor = $editorTable->getColumnValue($payload->getIdentity(), 'super_editor', 0);

            if ($isSuperEditor) {
                $totalEditors = $editorTable->getEditorsCount(0);

                if ($totalEditors == 2) {
                    $editorTable->delete(array('user_id = ?' => $payload->getIdentity()));

                    $editor_id = $editorTable->getColumnValue(0, 'editor_id', 0);
                    $editor = Engine_Api::_()->getItem('siteevent_editor', $editor_id);
                    $editorTable->update(array('super_editor' => 1), array('user_id = ?' => $editor->user_id));

                    //IF EDITOR IS NOT EXIST
                    $isExist = $editorTable->isEditor($editor->user_id);
                    if (empty($isExist)) {
                        $editorNew = $editorTable->createRow();
                        $editorNew->user_id = $editor->user_id;
                        $editorNew->designation = $editor->designation;
                        $editorNew->details = $editor->details;
                        $editorNew->about = $editor->about;
                        $editorNew->super_editor = 1;
                        $editorNew->save();
                    }
                } elseif ($totalEditors == 1) {
                    $editorTable->delete(array('user_id = ?' => $payload->getIdentity()));

                    //IF EDITOR IS NOT EXIST
                    $isExist = $editorTable->isEditor($viewer_id);
                    if (empty($isExist)) {
                        $editorNew = $editorTable->createRow();
                        $editorNew->user_id = $viewer_id;
                        $editorNew->designation = 'Super Editor';
                        $editorNew->details = '';
                        $editorNew->about = '';
                        $editorNew->super_editor = 1;
                        $editorNew->save();
                    }
                } else {
                    $editorTable->delete(array('user_id = ?' => $payload->getIdentity()));
                    $editor_id = $editorTable->getHighestLevelEditorId();
                    $editor = Engine_Api::_()->getItem('siteevent_editor', $editor_id);

                    //IF EDITOR IS NOT EXIST
                    $isExist = $editorTable->isEditor($editor->user_id);
                    if (empty($isExist)) {
                        $editorNew = $editorTable->createRow();
                        $editorNew->user_id = $editor->user_id;
                        $editorNew->designation = $editor->designation;
                        $editorNew->details = $editor->details;
                        $editorNew->about = $editor->about;
                        $editorNew->super_editor = 1;
                        $editorNew->save();
                    }
                }
            }

            $super_editor_user_id = $editorTable->getSuperEditor('user_id');

            //GET REVIEW TABLE
            $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');
            $reviewTable->update(array('owner_id' => $super_editor_user_id), array('type = ?' => 'editor', 'owner_id = ?' => $payload->getIdentity()));
            Engine_Api::_()->getDbTable('ratings', 'siteevent')->update(array('user_id' => $super_editor_user_id), array('user_id = ?' => $payload->getIdentity(), 'type' => 'editor'));

//      //UPDATE HOST NAME IN TABLE IF HOST IS DELEATED
//      Engine_Api::_()->getDbTable('events', 'siteevent')->update(array('host' => $payload->getTitle()), array('host = ?' => $payload->getIdentity()));

            $listItemTable = Engine_Api::_()->getDbtable('ListItems', 'siteevent');
            $listItemSelect = $listItemTable->select()->where('child_id = ?', $payload->getIdentity());
            foreach ($listItemTable->fetchAll($listItemSelect) as $listitem) {
                $list = Engine_Api::_()->getItem('siteevent_list', $listitem->list_id);
                if (!$list) {
                    $listitem->delete();
                    continue;
                }
                if ($list->has($payload)) {
                    $list->remove($payload);
                }
            }

            // Delete memberships
            $membershipApi = Engine_Api::_()->getDbtable('membership', 'siteevent');
            foreach ($membershipApi->getMembershipsOf($payload) as $event) {
                $membershipApi->removeMember($event, $payload);
            }
            
            //DELETE WAITLIST ENTRIES
            Engine_Api::_()->getDbTable('waitlists', 'siteevent')->delete(array('user_id = ?' => $payload->getIdentity()));
        }
    }

    public function onActivityActionCreateAfter($event) {
        $payload = $event->getPayload();

        if ($payload->object_type == 'siteevent_event' && ($payload->getTypeInfo()->type == 'siteevent_post' || $payload->getTypeInfo()->type == 'siteevent_post_parent') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
            $viewer_id = $viewer->getIdentity();
            $event_id = $payload->getObject()->event_id;
            $user_id = $payload->getSubject()->user_id;
            $subject = Engine_Api::_()->getItem('siteevent_event', $event_id);
            Engine_Api::_()->siteevent()->sendNotificationEmail($subject, $subject, 'siteevent_notificationpost', 'SITEEVENT_POSTNOTIFICATION_EMAIL', null, null, 'posted', $viewer);
            $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject);
            if (!empty($isChildIdLeader)) {
                Engine_Api::_()->siteevent()->sendNotificationToFollowers($subject, 'siteevent_notificationpost');
            }
        }
    }

    public function addActivity($event) {
        $payload = $event->getPayload();
        $subject = $payload['subject'];
        $object = $payload['object'];

        // Only for object=event
        if ($object instanceof Siteevent_Model_Event &&
                Engine_Api::_()->authorization()->context->isAllowed($object, 'member', 'view')) {
            $event->addResponse(array(
                'type' => 'siteevent_event',
                'identity' => $object->getIdentity()
            ));
        }
    }

    public function getActivity($event) {
        // Detect viewer and subject
        $payload = $event->getPayload();
        $user = null;
        $subject = null;
        if ($payload instanceof User_Model_User) {
            $user = $payload;
        } else if (is_array($payload)) {
            if (isset($payload['for']) && $payload['for'] instanceof User_Model_User) {
                $user = $payload['for'];
            }
            if (isset($payload['about']) && $payload['about'] instanceof Core_Model_Item_Abstract) {
                $subject = $payload['about'];
            }
        }
        if (null === $user) {
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($viewer->getIdentity()) {
                $user = $viewer;
            }
        }
        if (null === $subject && Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject();
        }

        // Get event memberships
        if ($user) {
            $data = Engine_Api::_()->getDbtable('membership', 'siteevent')->getMembershipsOfIds($user);
            if (!empty($data) && is_array($data)) {
                $event->addResponse(array(
                    'type' => 'siteevent_event',
                    'data' => $data,
                ));
            }
        }
    }

    public function onSitereviewListingtypeCreateAfter($event) {
        $payload = $event->getPayload();
        $db = Engine_Db_Table::getDefaultAdapter();
        $title_singular = ucfirst($payload->title_singular);
        $db->query("INSERT IGNORE INTO `engine4_siteevent_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitereview_listing_$payload->listingtype_id', 'listing_id', 'sitereview', '0', '0', '$title_singular Events', 'a:1:{i:0;s:18:\"contentlikemembers\";}')");
        $db->query("INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( 'siteevent.event.leader.owner.sitereview.listing.$payload->listingtype_id', '0');");
                
    }

}