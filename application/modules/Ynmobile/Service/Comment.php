<?php

class Ynmobile_Service_Comment extends Ynmobile_Service_Base{

    protected $module = 'activity';
    protected $mainItemTyp = 'activity_comment';

    /**
     * Input data:
     * + iItemId: int, required.
     * + sItemType: string, required.
     * + iCommentId: int, required.
     *
     * Output data:
     * + error_code: int.
     * + error_message: string.
     * + result: int.
     *
     * @see Mobile - API SE/Api V1.0
     * @see comment/delete
     *
     * @global string $token
     * @param array $aData
     * @return array
     */
    public function remove($aData)
    {
        extract($aData);


        $viewer = $this -> getViewer();
        $iItemId = intval(@$iItemId);
        $sItemType = @$sItemType;

        $oItem = $this -> getWorkingItem($sItemType, $iItemId);

        if (!$oItem){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _('No comment or wrong parent'),
            );

        }

        try
        {
            $resource_type = 'activity_action';
            if(isset($oItem->resource_type)){
                $resource_type= $oItem->resource_type;
            }
            $oParent = Engine_Api::_()->getItem($resource_type, $oItem->resource_id);

            if(!$oParent){
                return array(
                    'error_code' => 1,
                    'error_message' => Zend_Registry::get('Zend_Translate') -> _('No Item or wrong parent'),
                );
            }

            $oParent->comments()->removeComment($iItemId);

            return array(
                'error_code' => 0,
                'error_message' => '',
            );
        }

        catch( Exception $e )
        {
            return array(
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Can not remove this comment!"),
                'debug_mesasge'=> $e->getMessage(),
                'oItem'=> $oItem->toArray(),
                'error_code' => 1,
            );
        }
    }


    /**
     * Input data:
     * + sItemType: string, required.
     * + iItemId: int, required.
     * + sText: string, optional
     *
     * Output data:
     * + iLastId: int
     * + error_code: int.
     * + error_message: string.
     * + result: int.
     *
     * @see Mobile - API SE/Api V1.0
     * @see comment/add
     *
     * @global string $token
     * @param array $aData
     * @return array
     */
    public function add($aData)
    {
        extract($aData);

        $sItemType = @$sItemType;
        $iItemId  = intval($iItemId);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $sText = trim(@$sText);
        $attachmentData = $aAttachment;
        $iParentCommentId = intval($iParentCommentId);
        $oItem = $this -> getWorkingItem($sItemType, $iItemId);

        if(!$oItem){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing comment target"),
            );
        }

        if (!$viewer -> getIdentity() )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to comment on this item!")
            );
        }

        if($sItemType != 'activity_action' && !Engine_Api::_() -> authorization() -> isAllowed($oItem, null, 'comment'))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to comment on this item!")
            );
        }

        if (!method_exists($oItem, 'comments')){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("This object does not support to comment!"),
            );
        }

        if (!$sText){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Add some text to your comment!"),
            );
        }

        if (Engine_Api::_()->hasModuleBootstrap('yncomment')) {

            $arrTags = array();

            $sText = html_entity_decode($sText, ENT_QUOTES, 'UTF-8');

            $replacements = array();

            $sText = preg_replace("/#\[x=/", "# [", preg_replace("/#(\w+)\[x=/", "#$1 [x=", $sText));

            // Tags
            // $pattern = '/#tags@\w+@\d+@/';
            $friend_tag_reg = "/(\[x\=(\w+)\@(\d+)\])([^\[]+)(\[\/x\])/mi";
            $matches = array();
            $count_matched = preg_match_all($friend_tag_reg, $sText, $matches);

            if ($count_matched) {
                for ($index = 0; $index < $count_matched; ++$index) {

                    $type = $matches[2][ $index ];
                    $item_id = $matches[3][ $index ];
                    $title = $matches[4][ $index ];

                    $item = Engine_Api::_()->getItem($type, $item_id);

                    if ($item) {
                        $arrTags[] = array('item_type' => $type, 'item_id' => $item_id);
                        $replacements[ $matches[0][ $index ] ] = sprintf('<a ng-url="#/app/%s/%s" href="%s">%s</a>', $type, $item_id, $item->getHref(), $title);
                    } else {
                        $replacements[ $matches[0][ $index ] ] = $title;
                    }
                }
            }

            if ($replacements) {
                $sText = strtr($sText, $replacements);
            }

            $sText = urldecode($sText);
            $sText = strip_tags($sText, '<a><br>');
        }

        $comments = $oItem->comments();

        // create filter to censor content
        $filter = new Zend_Filter();
        $filter -> addFilter(new Engine_Filter_Censor());
        $filter -> addFilter(new Engine_Filter_HtmlSpecialChars());

        try
        {
            $baseUrl = Ynmobile_Helper_Base::getBaseUrl();

            // replace smiley with emoticon image
            if (Engine_Api::_()->hasModuleBootstrap('yncomment')) {
                foreach (Engine_Api::_()->yncomment()->getEmoticons() as $emoticon) {
                    $sText = str_replace($emoticon->text, "<img src='{$baseUrl}/application/modules/Yncomment/externals/images/emoticons/{$emoticon -> image}'/>", $sText);
                }
            }
            $comment = $comments -> addComment($viewer, $sText);

            // add parent id
            if (Engine_Api::_()->hasModuleBootstrap('yncomment')) {
                $comment -> parent_comment_id = $iParentCommentId;
                $comment -> save();
            }

            $iLastId = $comment -> comment_id;
            $activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');

            if($sItemType == 'activity_action')
            {
                $oItemOwner = Engine_Api::_() -> getItemByGuid($oItem -> subject_type . "_" . $oItem -> subject_id);
            }
            else
            {
                $oItemOwner = $oItem -> getOwner('user');
            }

            // get attachment
            $attachment = null;

            if (!empty($attachmentData) && !empty($attachmentData['type'])) {
                if (isset($attachmentData['type']) && $attachmentData['type'] == 'photo' && isset($attachmentData['photo_id']))
                {
                    if (Engine_Api::_()->hasModuleBootstrap('advalbum'))
                        $attachment = Engine_Api::_()->getItem('advalbum_photo', $attachmentData['photo_id']);
                    else
                        $attachment = Engine_Api::_()->getItem('album_photo', $attachmentData['photo_id']);
                }

                if (isset($attachmentData['type']) && $attachmentData['type'] == 'link')
                {
                    if (Engine_Api::_() -> core() -> hasSubject()) {
                        $subject = Engine_Api::_() -> core() -> getSubject();
                        if ($subject -> getType() != 'user')
                        {
                            $attachmentData['parent_type'] = $subject -> getType();
                            $attachmentData['parent_id'] = $subject -> getIdentity();
                        }
                    }
                    if (!empty($attachmentData['title'])) {
                        $attachmentData['title'] = $filter -> filter($attachmentData['title']);
                    }
                    if (!empty($attachmentData['description'])) {
                        $attachmentData['description'] = $filter -> filter($attachmentData['description']);
                    }
                    $attachment = Engine_Api::_() -> getApi('links', 'core') -> createLink($viewer, $attachmentData);
                }
            }

            // Add Activity
            // Check if advanced comment and is a reply
            if(Engine_Api::_()->hasModuleBootstrap('yncomment') && !empty($comment -> parent_comment_id)) {

                $action = $activityApi->addActivity($viewer, $oItem, 'comment_' . $oItem->getType(), '', array(
                    'owner' => $oItemOwner->getGuid(),
                    'body' => $sText
                ));
            } else {
                $action = $activityApi -> addActivity($viewer, $oItem, 'yncomment_' . $oItem -> getType(), '', array(
                    'owner' => $oItemOwner -> getGuid(),
                    'body' => $sText));
            }

            // Add attachment if action is done
            if ($action && $attachment)
            {
                $activityApi -> attachActivity($action, $attachment);
            }

            if ($attachment) {
                if (isset($comment -> attachment_type))
                    $comment -> attachment_type = ($attachment ? $attachment -> getType() : '');
                if (isset($comment -> attachment_id))
                    $comment -> attachment_id = ($attachment ? $attachment -> getIdentity() : 0);
                $comment -> save();
            }

            // Add notification for owner (if user and not viewer)
            if ($oItemOwner -> getType() == 'user' && $oItemOwner -> getIdentity() != $viewer -> getIdentity())
            {
                $notifyApi -> addNotification($oItemOwner, $viewer, $oItem, 'commented', array('label' => $oItem -> getShortType()));
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            $commentedUserNotifications = array();
            $notifyUsers = (method_exists($oItem, 'comments')) ? ($oItem -> comments() -> getAllCommentsUsers()) : (Engine_Api::_() -> getDbtable('comments', 'core') -> getAllCommentsUsers($oItem));

            foreach ($notifyUsers as $notifyUser)
            {
                if ($notifyUser -> getIdentity() == $viewer -> getIdentity() || $notifyUser -> getIdentity() == $oItemOwner -> getIdentity())
                    continue;

                // Don't send a notification if the user both commented and liked this
                $commentedUserNotifications[] = $notifyUser -> getIdentity();

                $notifyApi -> addNotification($notifyUser, $viewer, $oItem, 'commented_commented', array('label' => $oItem -> getShortType()));
            }

            // Add a notification for all users that liked
            // @todo we should probably limit this
            $allLikeUsers = (method_exists($oItem, 'likes')) ? ($oItem -> likes() -> getAllLikesUsers()) : (Engine_Api::_() -> getDbtable('likes', 'core') -> getAllLikesUsers($oItem));

            foreach ($allLikeUsers as $notifyUser)
            {
                // Skip viewer and owner
                if ($notifyUser -> getIdentity() == $viewer -> getIdentity() || $notifyUser -> getIdentity() == $oItemOwner -> getIdentity())
                    continue;

                // Don't send a notification if the user both commented and liked this
                if (in_array($notifyUser -> getIdentity(), $commentedUserNotifications))
                    continue;

                $notifyApi -> addNotification($notifyUser, $viewer, $oItem, 'liked_commented', array('label' => $oItem -> getShortType()));
            }

            // Increment comment count
            Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('core.comments');

            $commentResult = Ynmobile_AppMeta::_export_one($comment, array('listing'));
            $commentResult = array_merge($commentResult, $this->getAdvancedCommentOptions($sItemType));
            return $commentResult;
        }

        catch( Exception $e )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Oops, Fail!"),
                'result' => 0,
                'error_debug'=> $e->getMessage()
            );
        }
    }

    /**
     * Input data:
     * + sItemType: string, required.
     * + iItemId: int, required.
     * + sText: string, optional
     *
     * Output data:
     * + iLastId: int
     * + error_code: int.
     * + error_message: string.
     * + result: int.
     *
     * @see Mobile - API SE/Api V1.0
     * @see comment/add
     *
     * @global string $token
     * @param array $aData
     * @return array
     */
    public function edit($aData)
    {
        extract($aData);

        $iItemId  = intval($iItemId);
        $sItemType = @$sItemType;
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $sText = trim(@$sText);
        $attachmentData = $aAttachment;
        $comment = $this -> getWorkingItem($sItemType, $iItemId);
        $currentAttachmentId = $comment->attachment_id;

        if(!$comment){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing comment target"),
            );
        }

        if (!$viewer -> getIdentity() )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this comment!")
            );
        }

        // check comment edit permission based on comment permission or not?
//        if($sItemType != 'activity_action' && !Engine_Api::_() -> authorization() -> isAllowed($oItem, null, 'comment'))
//        {
//            return array(
//                'error_code' => 1,
//                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to comment on this item!")
//            );
//        }

        if (!$sText){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Add some text to your comment!"),
            );
        }

        // parse tagged
        if (Engine_Api::_()->hasModuleBootstrap('yncomment')) {

            $arrTags = array();

            $sText = html_entity_decode($sText, ENT_QUOTES, 'UTF-8');

            $replacements = array();

            $sText = preg_replace("/#\[x=/", "# [", preg_replace("/#(\w+)\[x=/", "#$1 [x=", $sText));

            // Tags
            // $pattern = '/#tags@\w+@\d+@/';
            $friend_tag_reg = "/(\[x\=(\w+)\@(\d+)\])([^\[]+)(\[\/x\])/mi";
            $matches = array();
            $count_matched = preg_match_all($friend_tag_reg, $sText, $matches);

            if ($count_matched) {
                for ($index = 0; $index < $count_matched; ++$index) {

                    $type = $matches[2][ $index ];
                    $item_id = $matches[3][ $index ];
                    $title = $matches[4][ $index ];

                    $item = Engine_Api::_()->getItem($type, $item_id);

                    if ($item) {
                        $arrTags[] = array('item_type' => $type, 'item_id' => $item_id);
                        $replacements[ $matches[0][ $index ] ] = sprintf('<a ng-url="#/app/%s/%s" href="%s">%s</a>', $type, $item_id, $item->getHref(), $title);
                    } else {
                        $replacements[ $matches[0][ $index ] ] = $title;
                    }
                }
            }

            if ($replacements) {
                $sText = strtr($sText, $replacements);
            }

            $sText = urldecode($sText);
            $sText = strip_tags($sText, '<a><br>');
        }

        $values = array();
        $values['body'] = $sText;

        if (empty($attachmentData)){
            $values['attachment_id'] = 0;
            $values['attachment_type'] = '';
        }

        $commentTable = $comment->getTable();
        $db=$commentTable->getAdapter();

        // create filter to censor content
        $filter = new Zend_Filter();
        $filter -> addFilter(new Engine_Filter_Censor());
        $filter -> addFilter(new Engine_Filter_HtmlSpecialChars());

        try
        {
            $baseUrl = Ynmobile_Helper_Base::getBaseUrl();
            foreach (Engine_Api::_()->yncomment()->getEmoticons() as $emoticon) {
                $sText = str_replace($emoticon->text, "<img src='{$baseUrl}/application/modules/Yncomment/externals/images/emoticons/{$emoticon -> image}'/>", $sText);
            }
            $values['body'] = $sText;
            $comment -> setFromArray($values);
            $comment -> save();

            // get attachment

            $attachment = null;

            if (!empty($attachmentData) && !empty($attachmentData['type']) && empty($attachmentData['iId'])) {
                if (isset($attachmentData['type']) && $attachmentData['type'] == 'photo' && isset($attachmentData['photo_id']))
                {
                    if (Engine_Api::_()->hasModuleBootstrap('advalbum'))
                        $attachment = Engine_Api::_()->getItem('advalbum_photo', $attachmentData['photo_id']);
                    else
                        $attachment = Engine_Api::_()->getItem('album_photo', $attachmentData['photo_id']);
                }

                if (isset($attachmentData['type']) && $attachmentData['type'] == 'link')
                {
                    if (Engine_Api::_() -> core() -> hasSubject()) {
                        $subject = Engine_Api::_() -> core() -> getSubject();
                        if ($subject -> getType() != 'user')
                        {
                            $attachmentData['parent_type'] = $subject -> getType();
                            $attachmentData['parent_id'] = $subject -> getIdentity();
                        }
                    }
                    if (!empty($attachmentData['title'])) {
                        $attachmentData['title'] = $filter -> filter($attachmentData['title']);
                    }
                    if (!empty($attachmentData['description'])) {
                        $attachmentData['description'] = $filter -> filter($attachmentData['description']);
                    }
                    $attachment = Engine_Api::_() -> getApi('links', 'core') -> createLink($viewer, $attachmentData);
                }
            }
            // =================

            if ($attachment) {
                if (isset($comment -> attachment_type))
                    $comment -> attachment_type = ($attachment ? $attachment -> getType() : '');
                if (isset($comment -> attachment_id))
                    $comment -> attachment_id = ($attachment ? $attachment -> getIdentity() : 0);
                $comment -> save();
            }

            return Ynmobile_AppMeta::_export_one($comment, array('listing'));
        }

        catch( Exception $e )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Oops, Fail!"),
                'result' => 0,
                'error_debug'=> $e->getMessage()
            );
        }
    }

    /**
     * Input data:
     * + sItemType: string, required.
     * + iItemId: int, required.
     * + iLastCommentIdViewed: int, optional.
     * + iLimit: int, optional.
     *
     * Output data:
     * + iLikeId: int
     * + iUserId: int
     * + sFullName: string
     * + sImage: string
     *
     * @see Mobile - API phpFox/Api V1.0
     * @see comment/listallcomments
     *
     * @param array $aData
     * @return array
     */
    public function listallcomments($aData)
    {
        extract($aData);

        $amountOfComment  = $amountOfComment?intval($amountOfComment):3;
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $sItemType = isset($aData['sItemType']) ? $aData['sItemType'] : '';
        $iItemId = isset($aData['iItemId']) ? (int)$aData['iItemId'] : 0;

        // get advanced comment options
        $options = $this->getAdvancedCommentOptions($sItemType);
        $isAdvancedComment = $options['bEnabled'];

        if (!$sItemType || !$iItemId)
        {
            return array(
                'error_code' => 1,
                'error_elements' => 'sItemType or iItemId',
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }

        if (empty($sItemType) || $iItemId < 1)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }

        // get last comment id to load more
        $lastCommentIdViewed = isset($aData['iLastCommentIdViewed']) ? (int)$aData['iLastCommentIdViewed'] : 0;
        $amountOfComment = isset($aData['iLimit']) ? (int)$aData['iLimit'] : 20;

        // main item
        $oItem = Engine_Api::_() -> getItem($sItemType, $iItemId);

        // return empty list if there is no item
        if(!$oItem) return array();

        $proxy = $oItem->comments();
        if ($sItemType == 'activity_action') {
            $table = Engine_Api::_()->getDbTable('comments', 'activity');
        } else {
            $table = Engine_Api::_()->getDbTable('comments', 'core');
        }
        $select = $proxy->getCommentSelect();

        // only select comment for pagination
        if ($isAdvancedComment) {
            $select -> where('parent_comment_id =?', isset($iParentCommentId)?$iParentCommentId:0);
        }

        // get all hidden comments of this user
        $aHiddenItems = $this->getHideItemByMember();

        if (!empty($aHiddenItems)) {
            $select -> where('comment_id NOT IN (?)', $aHiddenItems);
            // show count of visible replies only or not?
            // $selectReplies -> where('comment_id NOT IN (?)', $aHiddenItems);
        }

        // sort by setting
        if ($options['iCommentsOrder']) {
            $select -> order ("comment_id DESC");
        } else {
            $select -> order ("comment_id ASC");
        }

        if ($lastCommentIdViewed)
        {
            if ($options['iCommentsOrder']) {
                $select -> where('comment_id < ?', $lastCommentIdViewed);
            } else {
                $select -> where('comment_id > ?', $lastCommentIdViewed);
            }
        }
        $select -> limit($amountOfComment);

        if(empty($fields)) $fields = 'listing';

        $fields = explode(',', $fields);

        $commentList = Ynmobile_AppMeta::_export_all($select, $fields);

        // merge option to reply of advance reply and can comment
        $bCanComment  = (Engine_Api::_() -> authorization() -> isAllowed($oItem, null, 'comment')) ? 1 : 0;
        $options['bCanReply'] = $options['bCanReply'] && $bCanComment;

        // recursive create replies tree
        if ($isAdvancedComment) {
            // get all comment to recursive arrange to replies
            $selectReplies = $proxy->getCommentSelect();
            $replyList = Ynmobile_AppMeta::_export_all($selectReplies, $fields);

            foreach ($replyList as $key => $comment) {
                $replyList[$key] = array_merge($replyList[$key], $options);
            }
            foreach ($commentList as $key=>$comment) {
                $commentList[$key]['aReplies'] = $this->_loadReplies($comment['iCommentId'], $replyList);
                $commentList[$key]['iTotalReply'] = count($commentList[$key]['aReplies']);
            }
        }

        // append option to replies
        foreach ($commentList as $key => $comment) {
            $commentList[$key] = array_merge($commentList[$key], $options);
        }

        return $commentList;
    }

    /**
     * @param array $replyList
     * @return array [Collection Of replies by parent Id ]
     * <code>
     * array {1: [reply1, reply2, ]} reply can contain children reply.
     * </code>
     */
    public function _loadReplies($iCommentId, $replyList)
    {
        $newReplyList = array();

        foreach ($replyList as $key=>$reply) {

            if ($reply['iParentCommentId'] == $iCommentId) {
                $newReplyList[] = $reply;
                unset($replyList[$key]);
            }
        }

        foreach($newReplyList as $key=>$reply) {
            $newReplyList[$key]['aReplies'] = $this->_loadReplies($newReplyList[$key]['iCommentId'], $replyList);
            $newReplyList[$key]['iTotalReply'] = count($newReplyList[$key]['aReplies']);
        }

        return $newReplyList;
    }

    public function getHideItemByMember()
    {
        $hideItems = array();

        $viewer = Engine_Api::_() -> user() -> getViewer();

        if (Engine_Api::_()->hasModuleBootstrap('yncomment') || !$viewer) {

            $hideTable = Engine_Api::_()->getDbtable('hide', 'yncomment');

            $select = $hideTable->select()
                ->where('user_id  = ?', $viewer->getIdentity());

            $results = $select->query()->fetchAll();
            foreach ($results as $result)
            {
                $hideItems[] = $result['hide_resource_id'];
            }
        }

        return $hideItems;
    }


    public function emoticons(){

        if (!Engine_Api::_()->hasModuleBootstrap('yncomment')) {
            return array();
        }

        $data = array();

        $emoticons = Engine_Api::_()->yncomment()->getEmoticons();
        $baseUrl = Ynmobile_Helper_Base::getBaseUrl();

        foreach ($emoticons as $emoticon) {
            $data[] = array(
                'text'  => $emoticon->text,
                'title' => $emoticon->title,
                'image' => $this->finalizeUrl($baseUrl . "/application/modules/Yncomment/externals/images/emoticons/{$emoticon -> image}"),
            );
        }

        return $data;
    }
}

