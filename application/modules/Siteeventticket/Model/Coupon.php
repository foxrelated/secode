<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Coupon.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_Coupon extends Core_Model_Item_Abstract {

    protected $_searchTriggers = false;
    protected $_parent_type = 'siteevent_event';
    protected $_owner_type = 'user';
    protected $_parent_is_owner = false;

    /**
     * Gets an absolute URL to the event to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {

        $params = array_merge(array(
            'route' => 'siteeventticketcoupon_view',
            'reset' => true,
            'user_id' => $this->owner_id,
            'coupon_id' => $this->coupon_id,
            'slug' => $this->getCouponSlug(),
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);

        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    /**
     * Create photo
     *
     * @param array $photo
     * @return photo object
     */
    public function setPhoto($photo) {

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            $error_msg1 = Zend_Registry::get('Zend_Translate')->_('invalid argument passed to setPhoto');
            throw new Event_Model_Exception($error_msg1);
        }

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_id' => $this->getIdentity(),
            'parent_type' => 'siteeventticket_coupon'
        );

        $storage = Engine_Api::_()->storage();

        
        // Add autorotation for uploded images. It will work only for SocialEngine-4.8.9 Or more then.
        $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
        if(!empty($hasVersion)) {
          //RESIZE IMAGE (MAIN)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->resize(720, 720)
                  ->write($path . '/m_' . $name)
                  ->destroy();

          //RESIZE IMAGE (PROFILE)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->resize(200, 400)
                  ->write($path . '/p_' . $name)
                  ->destroy();

          //RESIZE IMAGE (NORMAL)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->resize(140, 160)
                  ->write($path . '/in_' . $name)
                  ->destroy();
        }else {
          //RESIZE IMAGE (MAIN)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->autoRotate()
                  ->resize(720, 720)
                  ->write($path . '/m_' . $name)
                  ->destroy();

          //RESIZE IMAGE (PROFILE)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->autoRotate()
                  ->resize(200, 400)
                  ->write($path . '/p_' . $name)
                  ->destroy();

          //RESIZE IMAGE (NORMAL)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->autoRotate()
                  ->resize(140, 160)
                  ->write($path . '/in_' . $name)
                  ->destroy();
        }

        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $name)
                ->destroy();

        //EVENT
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        //REMOVE TEMP FILES
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);

        //UPDATE ROW
        $this->photo_id = $iMain->file_id;
        $this->save();

        //ADD TO ALBUM
        $photoTable = Engine_Api::_()->getItemTable('siteeventticket_couponphoto');
        $siteeventticketAlbum = $this->getSingletonAlbum();
        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'coupon_id' => $this->getIdentity(),
            'couponalbum_id' => $siteeventticketAlbum->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $siteeventticketAlbum->getIdentity(),
        ));
        $photoItem->save();

        return $this;
    }

    /**
     * Get album
     *
     * @return album object
     */
    public function getSingletonAlbum() {

        $table = Engine_Api::_()->getItemTable('siteeventticket_couponalbum');
        $select = $table->select()
                ->where('coupon_id = ?', $this->getIdentity())
                ->order('couponalbum_id ASC')
                ->limit(1);

        $album = $table->fetchRow($select);

        if (null === $album) {
            $album = $table->createRow();
            $album->setFromArray(array(
                'coupon_id' => $this->getIdentity()
            ));
            $album->save();
        }

        return $album;
    }

    /**
     * Get photo
     *
     * @param int $photo_id
     * @return photo object
     */
    public function getPhoto($photo_id) {

        $photoTable = Engine_Api::_()->getItemTable('siteeventticket_couponphoto');
        $select = $photoTable->select()
                ->where('file_id = ?', $photo_id)
                ->limit(1);

        return $photoTable->fetchRow($select);
    }

    protected function _delete() {

        $tablePhoto = Engine_Api::_()->getItemTable('siteeventticket_couponphoto');
        $select = $tablePhoto->select()->where('coupon_id = ?', $this->coupon_id);
        foreach ($tablePhoto->fetchAll($select) as $photo) {
            $photo->delete();
        }

        $tableAlbum = Engine_Api::_()->getItemTable('siteeventticket_couponalbum');
        $select = $tableAlbum->select()->where('coupon_id = ?', $this->coupon_id);
        foreach ($tableAlbum->fetchAll($select) as $album) {
            $album->delete();
        }

        parent::_delete();
    }

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

    /**
     * Return slug corrosponding to coupon title
     *
     * @return coupontitle
     */
    public function getCouponSlug() {

        $string = $this->title;

        setlocale(LC_CTYPE, 'pl_PL.utf8');
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $string = strtolower($string);
        $string = strtr($string, array('&' => '-', '"' => '-', '&' . '#039;' => '-', '<' => '-', '>' => '-', '\'' => '-'));
        $string = preg_replace('/^[^a-z0-9]{0,}(.*?)[^a-z0-9]{0,}$/si', '\\1', $string);
        $string = preg_replace('/[^a-z0-9\-]/', '-', $string);
        $string = preg_replace('/[\-]{2,}/', '-', $string);
        return $string;
    }

}
