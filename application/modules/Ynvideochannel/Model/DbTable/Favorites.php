<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Model_DbTable_Favorites extends Engine_Db_Table {

    protected $_rowClass = 'Ynvideochannel_Model_Favorite';
    protected $_name = 'ynvideochannel_favorites';

    /**
    * @param $videoId
    * @param $userId
    * @return bool
    * determine if the video is in user's favorite list
     */
    public function isAdded($videoId, $userId) {

        if (isset($userId) && isset($videoId)) {
            $row = $this -> fetchRow(array(
                "video_id = $videoId",
                "user_id = $userId"
            ));
            if ($row) return true;
        }

        return false;
    }

    /**
     * @param $videoId
     * @param $userId
     * @return bool
     */
    public function addVideoToFavorite($videoId, $userId)
    {
        $row = $this -> fetchRow(array(
            "video_id = $videoId",
            "user_id = $userId"
        ));
        if ($row == null)
        {
            $favorite = $this -> createRow();
            $favorite -> video_id = $videoId;
            $favorite -> user_id = $userId;
            $favorite -> save();

            $video = Engine_Api::_() -> getItem('ynvideochannel_video', $videoId);
            $video -> favorite_count = new Zend_Db_Expr('favorite_count + 1');
            $video -> save();
            return $favorite;
        }
        else
        {
            return false;
        }
    }

    /**
     * @param $videoId
     * @param $userId
     * @return bool
     */
    public function removeVideoFromFavorite($videoId, $userId)
    {
        $favorite = Engine_Api::_() -> getDbTable('favorites', 'ynvideochannel') -> fetchRow(array(
            "video_id = $videoId",
            "user_id = $userId"
        ));
        if ($favorite)
        {
            return $favorite -> delete();
        }
        return false;
    }
}