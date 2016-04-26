<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_Album extends Core_Model_Item_Collection {

    protected $_searchTriggers = false;
    protected $_modifiedTriggers = false;
    protected $_parent_type = 'siteevent_event';
    protected $_owner_type = 'user';
    protected $_children_types = array('siteevent_photo');
    protected $_collectible_type = 'siteevent_photo';

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {

        return $this->getOwner()->getHref($params);
    }

    public function getAuthorizationItem() {

        return $this->getParent('siteevent_event');
    }

    protected function _delete() {

        //DELTE ALL CHILD POST
        $photoTable = Engine_Api::_()->getItemTable('siteevent_photo');
        $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
        foreach ($photoTable->fetchAll($photoSelect) as $siteeventPhoto) {
            $siteeventPhoto->delete();
        }
        parent::_delete();
    }

}