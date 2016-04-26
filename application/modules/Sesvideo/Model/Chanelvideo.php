<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Chanelvideo.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Model_Chanelvideo extends Core_Model_Item_Abstract {

  protected $_parent_type = 'user';
  protected $_owner_type = 'user';
  protected $_parent_is_owner = true;
  protected $_type = 'video';

  public function getHref($params = array()) {
    $params = array_merge(array(
        'route' => 'sesvideo_view',
        'reset' => true,
        'user_id' => $this->owner_id,
        'video_id' => $this->video_id,
        'slug' => $this->getSlug(),
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  /**
   * Gets a url to the current video representing this item. Return null if none
   * set
   *
   * @param string The video type;
   * @return string The video photo url
   * */
  public function getPhotoUrl($type = null) {
    $photo_id = $this->photo_id;
    if (!$photo_id && !$this->is_locked)
      return 'application/modules/Sesvideo/externals/images/video.png';
    if ($this->is_locked)
      return 'application/modules/Sesvideo/externals/images/locked-video.jpg';
    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo_id, $type);
    if (!$file)
      return 'application/modules/Sesvideo/externals/images/video.png';
    return $file->map();
  }

}
