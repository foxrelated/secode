<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getMutualFriend.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_View_Helper_GetMutualFriend extends Zend_View_Helper_Abstract {

  /**
   * Function which return the "Display Information" of the content, which will display on widgets and on pages.
   */
  public function getMutualFriend($friend_id) {

    $getMutualFriend = false;
    $isFriend = Engine_Api::_()->getApi('coreFun', 'suggestion')->isMember($friend_id);
    if (!empty($isFriend)) {
      $getMutualFriend = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMutualFriend($friend_id);
    }
    return $getMutualFriend;
  }

}