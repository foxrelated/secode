<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Playlistsong.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Model_Playlistsong extends Core_Model_Item_Abstract {

  public function getParent() {
    return Engine_Api::_()->getItem('sesmusic_playlist', $this->playlist_id);
  }

}