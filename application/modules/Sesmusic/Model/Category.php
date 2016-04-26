<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Category.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Model_Category extends Core_Model_Item_Abstract {

  protected $_searchTriggers = false;

  public function getTitle() {
    return $this->category_name;
  }

}
