<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Likes.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_Model_DbTable_Likes extends Core_Model_DbTable_Likes {

    protected $_rowClass = 'Nestedcomment_Model_Like';
    protected $_custom = false;
    protected $_name = 'core_likes';

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes($subject) {
        return new Engine_ProxyObject($subject, Engine_Api::_()->getDbtable('likes', 'core'));
    }

}
