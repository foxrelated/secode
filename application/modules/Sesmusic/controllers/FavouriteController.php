<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: FavouriteController.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_FavouriteController extends Core_Controller_Action_Standard {

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if (empty($viewer_id))
      return;

    $resource_id = $this->_getParam('resource_id');
    $resource_type = $this->_getParam('resource_type');
    $favourite_id = $this->_getParam('favourite_id');

    $item = Engine_Api::_()->getItem($resource_type, $resource_id);

    $favouriteTable = Engine_Api::_()->getDbTable('favourites', 'sesmusic');
    $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $activityStrameTable = Engine_Api::_()->getDbtable('stream', 'activity');

    if (empty($favourite_id)) {
      $isFavourite = $favouriteTable->isFavourite($resource_id, $resource_type);
      if (empty($isFavourite)) {
        $db = $favouriteTable->getAdapter();
        $db->beginTransaction();
        try {
          if (!empty($item))
            $favourite_id = $favouriteTable->addFavourite($item, $viewer)->favourite_id;
          $this->view->favourite_id = $favourite_id;

          if ($resource_type == 'sesmusic_album') {            
            $owner = $item->getOwner();
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $item, 'sesmusic_favourite_musicalbum');
            $action = $activityTable->addActivity($viewer, $item, 'sesmusic_favouritealbum');
            if ($action)
              $activityTable->attachActivity($action, $item);
          } elseif ($resource_type == 'sesmusic_albumsong') {            
            $owner = $item->getOwner();
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $item, 'sesmusic_favourite_song');            
            $action = $activityTable->addActivity($viewer, $item, 'sesmusic_favouritealbumsong');
            if ($action) {
              $activityStrameTable->delete(array('action_id =?' => $action->action_id));
              $db->query("INSERT INTO `engine4_activity_stream` (`target_type`, `target_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`, `action_id`) VALUES
('everyone', 0, 'user', $viewer_id, 'sesmusic_albumsong', $resource_id, 'sesmusic_favouritealbumsong', $action->action_id),
('members', $viewer_id, 'user', $viewer_id, 'sesmusic_albumsong', $resource_id, 'sesmusic_favouritealbumsong', $action->action_id),
('owner', $viewer_id, 'user', $viewer_id, 'sesmusic_albumsong', $resource_id, 'sesmusic_favouritealbumsong', $action->action_id),
('parent', $viewer_id, 'user', $viewer_id, 'sesmusic_albumsong', $resource_id, 'sesmusic_favouritealbumsong', $action->action_id),
('registered', 0, 'user', $viewer_id, 'sesmusic_albumsong', $resource_id, 'sesmusic_favouritealbumsong', $action->action_id);");
              $activityTable->attachActivity($action, $item);
            }
          } elseif ($resource_type == 'sesmusic_artist') {            
            $action = $activityTable->addActivity($viewer, $item, 'sesmusic_favouriteartist');
            if ($action) {
              $activityStrameTable->delete(array('action_id =?' => $action->action_id));
              $db->query("INSERT INTO `engine4_activity_stream` (`target_type`, `target_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`, `action_id`) VALUES
('everyone', 0, 'user', $viewer_id, 'sesmusic_artist', $resource_id, 'sesmusic_favouriteartist', $action->action_id),
('members', $viewer_id, 'user', $viewer_id, 'sesmusic_artist', $resource_id, 'sesmusic_favouriteartist', $action->action_id),
('owner', $viewer_id, 'user', $viewer_id, 'sesmusic_artist', $resource_id, 'sesmusic_favouriteartist', $action->action_id),
('parent', $viewer_id, 'user', $viewer_id, 'sesmusic_artist', $resource_id, 'sesmusic_favouriteartist', $action->action_id),
('registered', 0, 'user', $viewer_id, 'sesmusic_artist', $resource_id, 'sesmusic_favouriteartist', $action->action_id);");
              $activityTable->attachActivity($action, $item);
            }
          } elseif ($resource_type == 'sesmusic_playlist') {
            
            $owner = $item->getOwner();
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $item, 'sesmusic_favourite_playlist');
            
            $action = $activityTable->addActivity($viewer, $item, 'sesmusic_favouriteplaylist');
            if ($action) {
              $activityStrameTable->delete(array('action_id =?' => $action->action_id));
              $db->query("INSERT INTO `engine4_activity_stream` (`target_type`, `target_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`, `action_id`) VALUES
('everyone', 0, 'user', $viewer_id, 'sesmusic_playlist', $resource_id, 'sesmusic_favouriteplaylist', $action->action_id),
('members', $viewer_id, 'user', $viewer_id, 'sesmusic_playlist', $resource_id, 'sesmusic_favouriteplaylist', $action->action_id),
('owner', $viewer_id, 'user', $viewer_id, 'sesmusic_playlist', $resource_id, 'sesmusic_favouriteplaylist', $action->action_id),
('parent', $viewer_id, 'user', $viewer_id, 'sesmusic_playlist', $resource_id, 'sesmusic_favouriteplaylist', $action->action_id),
('registered', 0, 'user', $viewer_id, 'sesmusic_playlist', $resource_id, 'sesmusic_favouriteplaylist', $action->action_id);");
              $activityTable->attachActivity($action, $item);
            }
          }

          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      } else {
        $this->view->favourite_id = $isFavourite;
      }
    } else {
      if ($resource_type == 'sesmusic_album') {
        
        Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => "sesmusic_favourite_musicalbum", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));
        
        $action = $activityTable->fetchRow(array('type =?' => "sesmusic_favouritealbum", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));
      } elseif ($resource_type == 'sesmusic_albumsong') {
        Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => "sesmusic_favourite_song", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));
        $action = $activityTable->fetchRow(array('type =?' => "sesmusic_favouritealbumsong", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));
      } elseif ($resource_type == 'sesmusic_artist') {
        $action = $activityTable->fetchRow(array('type =?' => "sesmusic_favouriteartist", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));
      } elseif ($resource_type == 'sesmusic_playlist') {
        Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => "sesmusic_favourite_playlist", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));
        $action = $activityTable->fetchRow(array('type =?' => "sesmusic_favouriteplaylist", "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));
      }

      if (!empty($action)) {
        $action->deleteItem();
        $action->delete();
      }

      $favouriteTable->removeFavourite($item, $viewer);
    }
  }

}