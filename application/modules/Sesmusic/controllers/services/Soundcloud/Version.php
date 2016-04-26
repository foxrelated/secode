<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Version.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Services_Soundcloud_Version {

  const MAJOR = 2;
  const MINOR = 3;
  const PATCH = 2;

  /**
   * Magic to string method
   *
   * @return string
   *
   * @access public
   */
  function __toString() {
    return implode('.', array(self::MAJOR, self::MINOR, self::PATCH));
  }

}
