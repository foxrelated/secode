<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: List.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_List extends Core_Model_List {

    protected $_owner_type = 'siteevent_event';
    protected $_child_type = 'user';
    public $ignorePermCheck = true;

    public function getListItemTable() {
        return Engine_Api::_()->getItemTable('siteevent_list_item');
    }

}