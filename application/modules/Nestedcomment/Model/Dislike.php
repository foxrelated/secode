<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Dislike.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_Model_Dislike extends Core_Model_Item_Abstract {

    protected $_searchTriggers = false;

    public function getOwner($type = null) {
        $poster = $this->getPoster();
        if (null === $type && $type !== $poster->getType()) {
            return $poster->getOwner($type);
        }
        return $poster;
    }

    public function getPoster() {
        return Engine_Api::_()->getItem($this->poster_type, $this->poster_id);
    }

    public function __toString() {
        return $this->getPoster()->__toString();
    }

}
