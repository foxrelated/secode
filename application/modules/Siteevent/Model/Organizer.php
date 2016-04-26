<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Event.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_Organizer extends Core_Model_Item_Abstract {

    protected $_searchTriggers = array();
    protected $_parent_is_owner = true;
    protected $_parent_type = 'user';
    protected $_owner_type = 'user';

    public function getOwner($recurseType = null) {
        return Engine_Api::_()->getItem('user', $this->creator_id);
    }

    public function getDescription() {
        // @todo decide how we want to handle multibyte string functions
        $tmpBody = strip_tags($this->description);
        return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
    }

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {


        $params = array_merge(array(
            'route' => "siteevent_organizer_profile",
            'reset' => true,
            'organizer_id' => $this->organizer_id,
            'slug' => $this->getSlug()
                ), $params);

        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);

        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    /**
     * Return slug
     * */
    public function getSlug($str = null) {

        if (null === $str) {
            $str = $this->title;
        }

        return Engine_Api::_()->seaocore()->getSlug($str, 225);
    }

    /**
     * Set event photo
     *
     * */
    public function setPhoto($photo) {

        if (is_string($photo) && (strstr($photo, 'http')) || (strstr($photo, 'https'))) {
            $file_exists = 1;
        } else {
            $file_exists = 0;
        }

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo) && empty($file_exists)) {
            $file = $photo;
        } elseif (is_string($photo) && fopen($photo, "r") && !empty($file_exists)) {
            $file = $photo;
        } else {
            throw new Engine_Exception('invalid argument passed to setPhoto');
        }
        if (empty($file))
            return;
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => $this->getType(),
            'parent_id' => $this->getIdentity()
        );

        // Add autorotation for uploded images. It will work only for SocialEngine-4.8.9 Or more then.
        $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
        if(!empty($hasVersion)) {
          $image = Engine_Image::factory();
          $image->open($file)
                  ->resize(720, 720)
                  ->write($path . '/m_' . $name)
                  ->destroy();

          //RESIZE IMAGE (PROFILE)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->resize(300, 500)
                  ->write($path . '/p_' . $name)
                  ->destroy();

          //RESIZE IMAGE (NORMAL)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->resize(140, 160)
                  ->write($path . '/in_' . $name)
                  ->destroy();
        }else {
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
                  ->resize(300, 500)
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

//    //RESIZE IMAGE (Midum)
//    $image = Engine_Image::factory();
//    $image->open($file)
//            ->resize(200, 200)
//            ->write($path . '/im_' . $name)
//            ->destroy();
        //RESIZE IMAGE (ICON)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $name)
                ->destroy();

        //STORE
        $storage = Engine_Api::_()->storage();
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
//    $iIconNormalMidum = $storage->create($path . '/im_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
//    $iMain->bridge($iIconNormalMidum, 'thumb.midum');
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

        return $this;
    }

    public function countOrganizedEvent() {
        return Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator(array('host_type' => $this->getType(), 'host_id' => $this->organizer_id, 'action' => 'all'))->getTotalItemCount();
    }

    public function delete($params = array()) {
        if ($this->countOrganizedEvent()) {
            $moveInto = array();
            if (isset($params['move_host_type']) && isset($params['move_host_id']) && $params['move_host_type'] && $params['move_host_id']) {
                $moveInto['host_type'] = $params['move_host_type'];
                $moveInto['host_id'] = $params['move_host_id'];

                Engine_Api::_()->getDbTable('events', 'siteevent')->update($moveInto, array('host_type =?' => $this->getType(), 'host_id =?' => $this->getIdentity()));
            }
        }

        parent::delete();
    }

}