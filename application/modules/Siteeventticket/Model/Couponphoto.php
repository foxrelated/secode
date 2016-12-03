<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Couponphoto.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_Couponphoto extends Core_Model_Item_Collectible {

    protected $_parent_type = 'siteeventticket_couponalbum';
    protected $_owner_type = 'user';
    protected $_collection_type = 'siteeventticket_couponalbum';

    public function getMediaType() {

        return 'photo';
    }

    /**
     * Get photo url
     *
     * @param string $type
     * @return photo url
     */
    public function getPhotoUrl($type = null) {

        if (empty($this->file_id)) {

            return null;
        }

        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, $type);
        if (!$file) {
            return null;
        }

        return $file->map();
    }

}
