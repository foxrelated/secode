<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Playlistsongs.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Model_DbTable_Playlistsongs extends Engine_Db_Table {

  protected $_name = 'sesmusic_playlistsongs';
  protected $_rowClass = 'Sesmusic_Model_Playlistsong';

  public function checkSongsAlready($params = array()) {

    return $this->select()
                    ->from($this->info('name'), $params['column_name'])
                    ->where('playlist_id = ?', $params['playlist_id'])
                    ->where('file_id = ?', $params['file_id'])
                    ->where('albumsong_id = ?', $params['albumsong_id'])
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }

  public function playlistSongsCount($params = array()) {

    $row = $this->select()
            ->from($this->info('name'))
            ->where('playlist_id = ?', $params['playlist_id'])
            ->query()
            ->fetchAll();
    $total = count($row);
    return $total;
  }

  public function getPlaylistSongs($params = array()) {

    return $this->select()
                    ->from($this->info('name'), $params['column_name'])
                    ->where('albumsong_id = ?', $params['albumsong_id'])
                    ->query()
                    ->fetchAll();
  }

}