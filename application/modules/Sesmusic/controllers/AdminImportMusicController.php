<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminImportMusicController.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_AdminImportMusicController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmusic_admin_main', array(), 'sesmusic_admin_main_importmusic');
    $setting = Engine_Api::_()->getApi('settings', 'core');

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('music') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesmusic') && $setting->getSetting('sesmusic.pluginactivated')) {

      $playlistTable = Engine_Api::_()->getDbTable('playlists', 'music');
      $playlistTableName = $playlistTable->info('name');

      $coreLikeTable = Engine_Api::_()->getDbTable('likes', 'core');
      $coreLikeTableName = $coreLikeTable->info('name');

      $coreCommentsTable = Engine_Api::_()->getDbTable('comments', 'core');
      $coreCommentsTableName = $coreCommentsTable->info('name');

      $albumTable = Engine_Api::_()->getDbTable('albums', 'sesmusic');
      $albumTableName = $albumTable->info('name');

      $albumsongsTable = Engine_Api::_()->getDbTable('albumsongs', 'sesmusic');
      $albumsongsTableName = $albumsongsTable->info('name');

      $storageTable = Engine_Api::_()->getDbtable('files', 'storage');

      $selectmusicplaylists = $playlistTable->select()
              ->from($playlistTableName)
              ->where('musicimport = ?', 0)
              ->order('playlist_id ASC');
      $this->view->playlistresults = $playlistsResults = $playlistTable->fetchAll($selectmusicplaylists);
      if ($playlistsResults && isset($_GET['is_ajax']) && $_GET['is_ajax']) {
        try {
          foreach ($playlistsResults as $playlistsResult) {

            $music_playlistId = $playlistsResult->playlist_id;
            if ($music_playlistId) {

              $musicItem = Engine_Api::_()->getItem('music_playlist', $music_playlistId);
              $album = $albumTable->createRow();
              $album->title = $musicItem->title;
              $album->description = $musicItem->description;
              $album->owner_type = $musicItem->owner_type;
              $album->owner_id = $musicItem->owner_id;
              $album->search = $musicItem->search;
              $album->profile = $musicItem->profile;
              $album->special = $musicItem->special;
              $album->view_count = $musicItem->view_count;
              $album->comment_count = $musicItem->comment_count;
              $album->creation_date = $musicItem->creation_date;
              $album->modified_date = $musicItem->modified_date;
              $album->save();

              //sesmusic album id.
              $albumId = $album->album_id;

              //Core like table data
              $selectPlaylistLike = $coreLikeTable->select()
                      ->from($coreLikeTableName)
                      ->where('resource_id = ?', $music_playlistId)
                      ->where('resource_type = ?', 'music_playlist');
              $playlistLikeResults = $coreLikeTable->fetchAll($selectPlaylistLike);
              foreach ($playlistLikeResults as $playlistLikeResult) {
                $like = Engine_Api::_()->getItem('core_like', $playlistLikeResult->like_id);
                $coreLikeMusicAlbum = $coreLikeTable->createRow();
                $coreLikeMusicAlbum->resource_type = 'sesmusic_album';
                $coreLikeMusicAlbum->resource_id = $albumId;
                $coreLikeMusicAlbum->poster_type = 'user';
                $coreLikeMusicAlbum->poster_id = $like->poster_id;
                $coreLikeMusicAlbum->creation_date = $like->creation_date;
                $coreLikeMusicAlbum->save();
              }

              //Core comments table data
              $selectPlaylistComments = $coreCommentsTable->select()
                      ->from($coreCommentsTableName)
                      ->where('resource_id = ?', $music_playlistId)
                      ->where('resource_type = ?', 'music_playlist');
              $playlistCommentsResults = $coreCommentsTable->fetchAll($selectPlaylistComments);
              foreach ($playlistCommentsResults as $playlistCommentsResult) {
                $comment = Engine_Api::_()->getItem('core_comment', $playlistCommentsResult->comment_id);

                $coreCommentMusicAlbum = $coreCommentsTable->createRow();
                $coreCommentMusicAlbum->resource_type = 'sesmusic_album';
                $coreCommentMusicAlbum->resource_id = $albumId;
                $coreCommentMusicAlbum->poster_type = 'user';
                $coreCommentMusicAlbum->poster_id = $comment->poster_id;
                $coreCommentMusicAlbum->body = $comment->body;
                $coreCommentMusicAlbum->creation_date = $comment->creation_date;
                $coreCommentMusicAlbum->like_count = $comment->like_count;
                $coreCommentMusicAlbum->save();
              }

              //Fetch data from playlist song table.
              $playlistSongs = $musicItem->getSongs();
              foreach ($playlistSongs as $playlistSong) {
                $playlistSong = Engine_Api::_()->getItem('music_playlist_song', $playlistSong->song_id);

                $playlistSongFileId = $playlistSong->file_id;
                if ($playlistSongFileId) {
                  $storageData = $storageTable->fetchRow(array('file_id = ?' => $playlistSongFileId));
                }

                //New song create for music album.
                if ($storageData) {
                  $albumSongsResults = Engine_Api::_()->sesmusic()->createSong($storageData);
                  $storageTable->update(array('name' => $playlistSong->title, 'parent_id' => $album->owner_id, 'user_id' => $album->owner_id, 'mime_major' => 'application', 'mime_minor' => 'octet-stream'), array('file_id = ?' => $albumSongsResults->file_id));
                }

                //Insert data in to album songs table.
                $albumSongs = $albumsongsTable->createRow();
                $albumSongs->album_id = $albumId;
                $albumSongs->title = $playlistSong->title;
                $albumSongs->play_count = $playlistSong->play_count;
                if ($albumSongsResults)
                  $albumSongs->file_id = $albumSongsResults->file_id;
                $albumSongs->creation_date = $album->creation_date;
                $albumSongs->modified_date = $album->modified_date;
                $albumSongs->save();
                $album->song_count++;
                $album->save();
              }

              if ($musicItem->photo_id) {
                $photoId = $musicItem->photo_id;
                $storageDataPhoto = $storageTable->fetchRow(array('file_id = ?' => $photoId));
                $musicalbumphotoData = $album->setPhoto($storageDataPhoto);
                $storageTable->update(array('user_id' => $album->owner_id), array('parent_id = ?' => $album->album_id));
              }

              //Privacy work: playlist from music plugin into music album plguin. 
              $auth = Engine_Api::_()->authorization()->context;
              $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

              foreach ($roles as $role) {
                if ($auth->isAllowed($musicItem, $role, 'view')) {
                  $values['auth_view'] = $role;
                }
              }
              foreach ($roles as $role) {
                if ($auth->isAllowed($musicItem, $role, 'comment')) {
                  $values['auth_comment'] = $role;
                }
              }

              $viewMax = array_search($values['auth_view'], $roles);
              $commentMax = array_search($values['auth_comment'], $roles);
              foreach ($roles as $i => $role) {
                $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
              }
              $musicItem->musicimport = 1;
              $musicItem->save();
            }
          }
        } catch (Exception $e) {
          //$db->rollBack();
          throw $e;
        }
      }
    }
  }

}