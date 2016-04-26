<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Model_Video extends Core_Model_Item_Abstract
{
    protected $_owner_type = 'user';
    protected $_type = 'ynvideochannel_video';

    /**
     * @param array $params
     * @return string
     * @throws User_Model_Exception
     */
    public function getHref($params = array())
    {
        $params = array_merge(array(
            'route' => 'ynvideochannel_video_detail',
            'reset' => true,
            'video_id' => $this -> getIdentity(),
            'slug' => $this -> getSlug(),
        ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
    }

    /**
     * @param bool $view
     * @param array $params
     * @return string
     * @throws Zend_Exception
     */
    public function getRichContent()
    {
        return Zend_Registry::get('Zend_View') -> partial('_video_feed.tpl', 'ynvideochannel', array('item' => $this));
    }

    /**
     * @param $photo
     * @param bool $cronJob
     * @return $this
     * @throws Engine_Image_Exception
     * @throws User_Model_Exception
     */
    public function setPhoto($photo)
    {
        if ($photo instanceof Zend_Form_Element_File)
        {
            $file = $photo -> getFileName();
            $name = basename($file);
        }
        else if( $photo instanceof Storage_Model_File ) {
            $file = $photo->temporary();
            $name = $photo->name;
        }
        else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $name = $photo['name'];
        }
        else if (is_string($photo)) {
            $pathInfo = @pathinfo($photo);
            $parts = explode('?', preg_replace("/#!/", "?", $pathInfo['extension']));
            $ext = $parts[0];
            $photo_parsed = @parse_url($photo);
            if ($ext && $photo_parsed) {
                $file = APPLICATION_PATH . '/temporary/ynvideochannel_' . md5($photo) . '.' . $ext;
                file_put_contents($file, file_get_contents($photo));
                $name = basename($file);

            } else
                throw new User_Model_Exception('can not get get thumbnail image from youtube video');
        } else {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }

        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'ynvideochannel_video',
            'parent_id' => $this -> getIdentity(),
            'user_id' => $this -> owner_id
        );

        // Save
        $storage = Engine_Api::_() -> storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image -> open($file) -> resize(854, 480) -> write($path . '/m_' . $name) -> destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image -> open($file) -> resize(640, 360) -> write($path . '/p_' . $name) -> destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image -> open($file) -> resize(420, 236) -> write($path . '/in_' . $name) -> destroy();

        // Store
        $iMain = $storage -> create($path . '/m_' . $name, $params);
        $iProfile = $storage -> create($path . '/p_' . $name, $params);
        $iIconNormal = $storage -> create($path . '/in_' . $name, $params);

        $iMain -> bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');

        // Remove temp files
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path.'/in_'.$name);
        @unlink($file);

        // Update row
        $this -> modified_date = date('Y-m-d H:i:s');
        $this -> photo_id = $iMain -> file_id;
        $this -> save();

        return $this;
    }
    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     **/
    public function comments()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }


    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     **/
    public function likes()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

    /**
     * Gets a proxy object for the tags handler
     *
     * @return Engine_ProxyObject
     **/
    public function tags()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
    }

    /**
     * @throws Zend_Db_Table_Row_Exception
     */

    protected function _delete()
    {
        if( $this->_disableHooks ) return;
        Engine_Api::_() -> getDbtable('favorites', 'ynvideochannel') -> delete(array('video_id = ?' => $this -> video_id));
        Engine_Api::_() -> getDbtable('ratings', 'ynvideochannel') -> delete(array('video_id = ?' => $this -> video_id));
        parent::_delete();
    }

    public function getChannel()
    {
        return Engine_Api::_()->getItem('ynvideochannel_channel', $this -> channel_id);
    }

    public function getCategory() {
        $category = Engine_Api::_()->getItem('ynvideochannel_category', $this->category_id);
        if ($category) {
            return $category;
        }
    }

    public function getVideoIframe($width = 560, $height = 315)
    {
        return '<iframe width="'.$width.'" height="'.$height.'" src="//www.youtube.com/embed/'.$this -> code.'" frameborder="0" allowfullscreen></iframe>';
    }

    function isViewable() {
        return $this->authorization()->isAllowed(null, 'view');
    }

    function isEditable() {
        return $this->authorization()->isAllowed(null, 'edit');
    }

    function isDeletable() {
        return $this->authorization()->isAllowed(null, 'delete');
    }

    public function getEmbedCode(array $options = null)
    {
        $options = array_merge(array(
            'height' => '315',
            'width' => '560',
        ), (array)$options);
        $view = Zend_Registry::get('Zend_View');
        $url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                'module' => 'ynvideochannel',
                'controller' => 'video',
                'action' => 'external',
                'video_id' => $this -> getIdentity(),

            ), 'default', true) . '?format=frame';
        return '<iframe ' . 'src="' . $view -> escape($url) . '" ' . 'width="' . sprintf("%d", $options['width']) . '" ' . 'height="' . sprintf("%d", $options['height']) . '" ' . 'style="overflow:hidden;"' . '>' . '</iframe>';
    }

    public function getPlayerDOM()
    {
        $video_id = $this->getIdentity();
        $code = $this->code;
        $videoDom = '<video id="player_' . $video_id . '" class="ynvideochannel-player" data-type="1" width="764" height="426">
                <source type="video/youtube" src="http://www.youtube.com/watch?v='. $code .'" />
            </video>';
        return $videoDom;
    }

    public function isRated($user_id)
    {
        $table = Engine_Api::_() -> getDbTable('ratings', 'ynvideochannel');
        $select = $table -> select() -> where('video_id = ?', $this -> getIdentity()) -> where('user_id = ?', $user_id) -> limit(1);
        return $table -> fetchRow($select)?true:false;
    }
}
