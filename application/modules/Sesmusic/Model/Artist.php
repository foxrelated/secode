<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Artist.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Model_Artist extends Core_Model_Item_Abstract {

  protected $_searchTriggers = false;

  public function getTitle() {
    return $this->name;
  }

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {

    $params = array_merge(array(
        'route' => 'sesmusic_artist',
        'reset' => true,
        'artist_id' => $this->artist_id,
        'slug' => $this->getSlug(),
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
  }

  public function getPhotoUrl() {

    $photo_id = $this->artist_photo;
    if ($photo_id) {
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo_id, '');
      return $file->map();
    }
  }

}