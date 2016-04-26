<?php

class Ynmusic_Plugin_Composer extends Core_Plugin_Abstract {
  	public function onAttachYnmusic($data) {
    	if( !is_array($data) || empty($data['song_id']) ) 
      		return;

    	$song = Engine_Api::_()->getItem('ynmusic_song', $data['song_id']);
    	if( !($song instanceof Core_Model_Item_Abstract) || !$song->getIdentity() )
      		return;
    
    	return $song;
  	}
}