<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Cleanup.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Plugin_Task_Cleanup extends Core_Plugin_Task_Abstract {

  public function execute() {

    $albumSongsTable = Engine_Api::_()->getDbTable('albumsongs', 'sesmusic');
    $albumSongsTableName = $albumSongsTable->info('name');
    $tbl_files = Engine_Api::_()->getDbTable('files', 'storage');
    $tbl_files_name = $tbl_files->info('name');

    //Find songs in the storage_files table that don't exist in music_playlist_songs
    $select = $tbl_files->select()
            ->setIntegrityCheck(false)
            ->from($tbl_files_name, 'file_id')
            ->joinLeft($albumSongsTableName, "$tbl_files_name.file_id = $albumSongsTableName.file_id", '')
            ->where('type = ?', 'song')
            ->where('parent_type = ?', 'music_song')
            ->where('albumsong_id IS NULL')
            ->limit(50);

    $rows = $tbl_files->fetchAll($select);
    if ($rows) {
      foreach ($rows as $row) {
        $db = $albumSongsTable->getAdapter();
        $db->beginTransaction();
        try {
          Engine_Api::_()->getItem('storage_file', $row->file_id)->remove();
          $db->commit();
        } catch (Exception $e) {
          $db->rollback();
          throw $e;
        }
      }
    }
  }

}