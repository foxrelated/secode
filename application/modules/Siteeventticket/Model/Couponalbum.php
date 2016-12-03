<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Couponalbum.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_Couponalbum extends Core_Model_Item_Collection {

    protected $_parent_type = 'siteeventticket_coupon';
    protected $_owner_type = 'siteeventticket_coupon';
    protected $_children_types = array('siteeventticket_couponphoto');
    protected $_collectible_type = 'siteeventticket_couponphoto';

    /**
     * Get authorization
     *
     * @return authorization value
     */
    public function getAuthorizationItem() {

        return $this->getParent('siteeventticket_coupon');
    }

    /**
     * Delete child things
     *
     */
    protected function _delete() {

        $photoTable = Engine_Api::_()->getItemTable('siteeventticket_couponphoto');
        $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());

        foreach ($photoTable->fetchAll($photoSelect) as $siteEventTicketCouponPhoto) {
            $siteEventTicketCouponPhoto->delete();
        }

        parent::_delete();
    }

}
