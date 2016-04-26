<?php

class Ynmobile_Service_Like extends Ynmobile_Service_Base{
    public function like($aData){

        extract($aData);
        $iItemId = intval($iItemId);

        $oItem = $this -> getWorkingItem($sItemType, $iItemId);

        $likes  = $oItem-> likes();

        $viewer = Engine_Api::_() -> user() -> getViewer();

        if(!$viewer){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Permission denied"),
            );
        }

        if(!$oItem){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Subject not found"),
            );
        }

        $bIsDisliked = 0;
        $iTotalDislike = 0;

        try
        {
            if(Engine_Api::_()->hasModuleBootstrap('yncomment')){

                $dislikes = Engine_Api::_() -> getDbtable('dislikes', 'yncomment');

                if ($dislikes->isDislike($oItem, $viewer)) {
                    $dislikes->removeDislike($oItem, $viewer);
                    $bIsDisliked = $dislikes -> isDislike($oItem, $viewer)?1:0;
                    $iTotalDislike = intval($dislikes -> getDislikeCount($oItem));
                }
            }

            $oItem -> likes() -> addLike($viewer);

            // Add notification
            $owner = $oItem -> getOwner();

            if ($owner -> getType() == 'user' && $owner -> getIdentity() != $viewer -> getIdentity())
            {
                $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                $notifyApi -> addNotification($owner, $viewer, $oItem, 'liked', array('label' => $oItem -> getShortType()));
            }

            // Stats
            Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('core.likes');

            // $db -> commit();
            return array(
                'sItemType'=> $sItemType,
                'iItemId'=>$iItemId,
                'iTotalLike' => intval($likes -> getLikeCount()),
                'bIsLiked'=> $likes -> isLike($viewer)?1:0,
                'bIsDisliked'=> $bIsDisliked,
                'iTotalDislike'=> $iTotalDislike,
                'message' => Zend_Registry::get('Zend_Translate') -> _("You liked successful!")
            );
        }
        catch( Exception $e )
        {
            // $db -> rollBack();
            return array(
                'error_code' => 1,
                'sItemType'=> $sItemType,
                'iItemId'=>$iItemId,
                'iTotalLike' => intval($likes -> getLikeCount()),
                'bIsLiked'=> $likes -> isLike($viewer)?1:0,
                'bIsDisliked'=> $bIsDisliked,
                'iTotalDislike'=> $iTotalDislike,
                'error_message' => $e->getMessage()
            );
        }
    }


    public function unlike($aData)
    {
        extract($aData);
        $iItemId = intval($iItemId);

        $oItem = $this -> getWorkingItem($sItemType, $iItemId);

        $likes  = $oItem-> likes();

        $viewer = Engine_Api::_() -> user() -> getViewer();

        if(!$viewer){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Permission denied"),
            );
        }

        if(!$oItem){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Subject not found"),
            );
        }

        try
        {
            $oItem -> likes() -> removeLike($viewer);

            // Add notification
            $owner = $oItem -> getOwner();

            if ($owner -> getType() == 'user' && $owner -> getIdentity() != $viewer -> getIdentity())
            {
                $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                $notifyApi -> addNotification($owner, $viewer, $oItem, 'liked', array('label' => $oItem -> getShortType()));
            }

            // Stats
            Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('core.likes');

            // $db -> commit();

        }
        catch( Exception $e )
        {
            // $db -> rollBack();
            return array(
                'error_code' => 1,
                'iTotalLike' => intval($likes -> getLikeCount()),
                'bIsLiked'=> $likes -> isLike($viewer)?1:0,
                'error_message' => $e->getMessage()
            );
        }

        return array(
            'error_code' => 0,
            'iTotalLike' => intval($likes -> getLikeCount()),
            'bIsLiked'=> $likes -> isLike($viewer)?1:0,
            'message' => Zend_Registry::get('Zend_Translate') -> _("You unliked successful!")
        );
    }

    /**
     * Input data:
     * + sItemType: string, required.
     * + iItemId: int, required.
     * + iLastLikeIdViewed: int, optional.
     * + iLimit: int, optional.
     *
     * Output data:
     * + iLikeId: int
     * + iUserId: int
     * + sFullName: string
     * + sImage: string
     *
     * @see Mobile - API phpFox/Api V1.0
     * @see like/listalllikes
     *
     * @param array $aData
     * @return array
     */
    public function listalllikes($aData)
    {
        $sItemType = isset($aData['sItemType']) ? $aData['sItemType'] : '';
        $iItemId = isset($aData['iItemId']) ? (int)$aData['iItemId'] : 0;
        $viewer = Engine_Api::_() -> user() -> getViewer();

        $lastLikeIdViewed = isset($aData['iLastLikeIdViewed']) ? (int)$aData['iLastLikeIdViewed'] : 0;
        $amountOfLike = isset($aData['iLimit']) ? (int)$aData['iLimit'] : 20;

        if (empty($sItemType) || $iItemId < 1)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }
        $oItem = $this -> getWorkingItem($sItemType, $iItemId);

        $likes = $oItem->likes();
        $table = $likes->getReceiver();
        $sender =  $likes->getSender();

        $select = $table->getLikeSelect($sender);

        if ($lastLikeIdViewed){
            $select -> where('like_id > ?', $lastLikeIdViewed);
        }

        if ($amountOfLike){
            $select -> limit($amountOfLike);
        }

        return Ynmobile_AppMeta::_export_all($select, $fields = array('listing'));
    }

    /**
     * Input data:
     * + sItemType: string, required.
     * + iItemId: int, required.
     * + iLastLikeIdViewed: int, optional.
     * + iLimit: int, optional.
     *
     * Output data:
     * + iLikeId: int
     * + iUserId: int
     * + sFullName: string
     * + sImage: string
     *
     * @see Mobile - API phpFox/Api V1.0
     * @see like/listalllikes
     *
     * @param array $aData
     * @return array
     */
    public function listalldislikes($aData, $iItemId)
    {
        $sItemType = isset($aData['sItemType']) ? $aData['sItemType'] : '';
        $iItemId = isset($aData['iItemId']) ? (int)$aData['iItemId'] : 0;
        $viewer = Engine_Api::_() -> user() -> getViewer();

        $lastLikeIdViewed = isset($aData['iLastLikeIdViewed']) ? (int)$aData['iLastLikeIdViewed'] : 0;
        $amountOfLike = isset($aData['iLimit']) ? (int)$aData['iLimit'] : 20;

        if (empty($sItemType) || $iItemId < 1)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }

        $oItem = $this -> getWorkingItem($sItemType, $iItemId);

        $dislikeTable = Engine_Api::_()->getItemTable('yncomment_dislike');

        $select = $dislikeTable->getDislikeSelect($oItem);

//        $data =$dislikeTable->fetchAll($select);
//        print_r($data);die;

        return Ynmobile_AppMeta::_export_all($select, $fields = array('listing'));
    }

    /**
     * get User Like
     * @param object
     *
     * @return Array
     *
     */
    public function getUserLike($oItem)
    {
        $aUserLike = array();
        // friend of mine in the list of people like
        $likes = $oItem -> likes();


        $table =  $likes->getReceiver();
        $sender   = $likes->getSender();

        $select = $table -> select();
        $select -> from($table -> info('name'), array(
            'poster_type',
            'poster_id'
        ));

        if ($likes -> getLikeCount() > 0)
        {

            $custom =  false;

            if(get_class($table) == 'Core_Model_DbTable_Likes'){
                $select -> where('resource_id = ?', $sender -> getIdentity());
                $select -> where('resource_type = ?', $sender -> getType());

            }else{
                $select -> where('resource_id = ?', $sender -> getIdentity());
                $custom =  true;
            }

            $oViewer = $this->getViewer();

            if($oViewer){

                $select->where('poster_id <>?', $oViewer->getIdentity());
                $friends = $oViewer -> membership() -> getMembersInfo(true);

                if (count($friends) > 0)
                {
                    $ids = array();
                    foreach ($friends as $row)
                    {
                        $ids[] = $row -> user_id;
                    }
                    $select -> where("poster_id IN (?)", $ids);
                }
            }
            $select -> limit(1);
            $userLike = $table -> fetchRow($select);
            if (!$userLike)
            {
                $select = $table -> select();
                $select -> from($table -> info('name'), array(
                    'poster_type',
                    'poster_id'
                ));
                if (!$custom)
                {
                    $select -> where('resource_id = ?', $sender -> getIdentity());
                    $select -> where('resource_type = ?', $sender -> getType());

                }else{
                    $select -> where('resource_id = ?', $sender -> getIdentity());
                }

                $select -> limit(1);
                $userLike = $table -> fetchRow($select);
            }

            $oUser = Engine_Api::_() -> user() -> getUser($userLike -> poster_id);

            if ($oUser -> getIdentity())
            {
                $aUserLike[] = array(
                    'iUserId' => $oUser -> getIdentity(),
                    'sTitle' => $oUser -> getTitle()
                );
            }
        }
        return $aUserLike;
    }

    public function getUserDislike($resource)
    {
        if(!Engine_Api::_()->hasModuleBootstrap('yncomment')) {
            return array();
        }

        $table =  Engine_Api::_() -> getDbtable('dislikes', 'yncomment');
        $select = new Zend_Db_Select($table->getAdapter());

        $select->from($table->info('name'), array('poster_id'));
        $select->where('resource_type = ?', $resource->getType());
        $select->where('resource_id = ?', $resource->getIdentity());

        $aUserDislike = array();
        $users = $select->query()->fetchAll();
        foreach ($users as $userDislike) {
            $oUser = Engine_Api::_() -> user() -> getUser($userDislike['poster_id']);
            $aUserDislike[] = array(
                'iUserId' => $oUser -> getIdentity(),
                'sTitle' => $oUser -> getTitle()
            );
        }
        return $aUserDislike;
    }

    public function dislike($aData){

        extract($aData);
        $iItemId = intval($iItemId);

        $oItem = $this -> getWorkingItem($sItemType, $iItemId);

        $likes  = $oItem-> likes();

        $viewer = Engine_Api::_() -> user() -> getViewer();

        if(!$viewer){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Permission denied"),
            );
        }

        if(!$oItem){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Subject not found"),
            );
        }

        try
        {
            $dislikes = Engine_Api::_() -> getDbtable('dislikes', 'yncomment');

            $dislikes -> addDislike($oItem, $viewer);

            if ($likes -> isLike($viewer)){

                $likes -> removeLike($viewer);
            }

            return array(
                'error_code' => 0,
                'sItemType' => $sItemType,
                'iItemId' => $iItemId,
                'iTotalLike' => intval($likes -> getLikeCount()),
                'bIsLiked'=> $likes -> isLike($viewer)?1:0,
                'bIsDisliked'=> $dislikes -> isDislike($oItem, $viewer)?1:0,
                'iTotalDislike' => intval($dislikes -> getDislikeCount($oItem)),
                'message' => Zend_Registry::get('Zend_Translate') -> _("You disliked successful!")
            );
        }
        catch( Exception $e )
        {
            // $db -> rollBack();
            return array(
                'error_code' => 1,
                'sItemType'=> $sItemType,
                'iItemId'=>$iItemId,
                'bIsLiked'=> $likes -> isLike($viewer)?1:0,
                'iTotalLike' => intval($likes -> getLikeCount()),
                'bIsDisliked'=> $dislikes -> isDislike($oItem, $viewer)?1:0,
                'iTotalDislike' => intval($dislikes -> getDislikeCount($oItem)),
                'error_message' => $e->getMessage()
            );
        }
    }
}
