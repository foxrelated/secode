<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Composer.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Plugin_Composer extends Core_Plugin_Abstract {

  public function onAttachSesmusic($data) {

    if (!is_array($data) || empty($data['albumsong_id']))
      return;

    $song = Engine_Api::_()->getItem('sesmusic_albumsong', $data['albumsong_id']);
    if (!($song instanceof Core_Model_Item_Abstract) || !$song->getIdentity())
      return;

    return $song;
  }

}