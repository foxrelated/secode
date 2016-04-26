<?php
class Ynmusic_Plugin_Task_ImportFromMp3music extends Core_Plugin_Task_Abstract {
	public function execute() {
		if (!Engine_Api::_()->hasModuleBootstrap('mp3music')) {
			return;
		}
		
		$albums = Engine_Api::_()->getItemTable('mp3music_album')->fetchAll();
		foreach ($albums as $album) {
			if (!Engine_Api::_()->ynmusic()->hasImported($album)) {
				Engine_Api::_()->getItemTable('ynmusic_album')->importItem($album);
			}
		}
		
		$songs = Engine_Api::_()->getItemTable('mp3music_album_song')->fetchAll();
		foreach ($songs as $song) {
			if (!Engine_Api::_()->ynmusic()->hasImported($song)) {
				$oldAlbum = $song->getParent();
				$album = Engine_Api::_()->getItemTable('ynmusic_album')->getImportedItem($oldAlbum);
				if ($album) {
					Engine_Api::_()->getItemTable('ynmusic_song')->importItem($song, $album);
				}
				else {
					Engine_Api::_()->getItemTable('ynmusic_album')->importItem($album);
				}
			}
		}
		
		$playlists = Engine_Api::_()->getItemTable('mp3music_playlist')->fetchAll();
		foreach ($playlists as $playlist) {
			Engine_Api::_()->getItemTable('ynmusic_playlist')->importItem($playlist);
		}		
	}
}