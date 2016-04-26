<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Playlistvideos.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Model_DbTable_Playlistvideos extends Engine_Db_Table {

  protected $_name = 'sesvideo_playlistvideos';
  protected $_rowClass = 'Sesvideo_Model_Playlistvideo';

  public function getPlaylistVideos($params = array()) {
    return $this->select()
                    ->from($this->info('name'), $params['column_name'])
                    ->where('file_id = ?', $params['file_id'])
                    ->query()
                    ->fetchAll();
  }

  public function playlistVideosCount($params = array()) {

    $row = $this->select()
            ->from($this->info('name'))
            ->where('playlist_id = ?', $params['playlist_id'])
            ->query()
            ->fetchAll();
    $total = count($row);
    return $total;
  }

  public function checkVideosAlready($params = array()) {

    return $this->select()
                    ->from($this->info('name'), $params['column_name'])
                    ->where('playlist_id = ?', $params['playlist_id'])
                    ->where('file_id = ?', $params['file_id'])
                    ->where('playlistvideo_id = ?', $params['playlistvideo_id'])
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }

}
