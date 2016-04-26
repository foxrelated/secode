<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Playlistvideo.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Model_Playlistvideo extends Core_Model_Item_Abstract {

  public function getParent($recurseType = NULL) {
    return Engine_Api::_()->getItem('sesvideo_playlist', $this->playlist_id);
  }

}
