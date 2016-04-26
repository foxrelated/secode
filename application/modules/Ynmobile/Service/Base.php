<?php

/**
 * Class Ynmobile_Service_Base
 */
class Ynmobile_Service_Base extends Core_Api_Abstract {

    protected $module = 'core';
    protected $mainItemType = 'user';


    protected $availablePrivacyOptions  = array(
        'everyone'              => 'Everyone',
        'registered'            => 'All Registered Members',
        'owner_network'         => 'Friends and Networks',
        'owner_member_member'   => 'Friends of Friends',
        'owner_member'          => 'Friends Only',
        'owner'                 => 'Just Me'
    );

    static $pageNameMap = array(
        'advalbum_album'    =>  'album_album_view',
        'album'             =>  'album_album_view',
        'advalbum_photo'    =>  'album_photo_view',
        'photo'             =>  'album_photo_view',
        'blog'              =>  'blog_index_view',
        'ynblog'            =>  'ynblog_index_view',
        'music_playlist'    =>  'music_playlist_view',
        'mp3music_album'    =>  'mp3music_album_album',
        'mp3music_playlist' =>  'mp3music_playlist_playlist',
        'classified'        =>  'classified_index_view',
        'poll'              =>  'poll_poll_view',
        'video'             =>  'video_index_view',
        'ynvideo'           =>  'ynvideo_index_view',
    );

    static $activityPageMap = array(
        'activity'                  =>  'user_index_home',
        'ynfeed'                  =>  'user_index_home',
        'user'                      =>  'user_profile_index',
        'event'                     =>  'event_profile_index',
        'ynevent'                   =>  'ynevent_profile_index',
        'group'                     =>  'group_profile_index',
        'advgroup'                  =>  'advgroup_profile_index',
        'ynlistings_listing'        =>  'ynlistings_index_view',
        'ynbusinesspages_business'  => 'ynbusinesspages_profile_index',
        'ynjobposting_company'      => 'ynjobposting_company_detail',
    );

    static $workingTypes = array();

    function getViewer(){
        return Engine_Api::_()->user()->getViewer();
    }

    function getWorkingItem($sType, $iId){
        return Engine_Api::_()->getItem($this->getWorkingType($sType), $iId);
    }

    function getWorkingItemTable($sType){
        return Engine_Api::_()->getItemTable($this->getWorkingType($sType));
    }

    function finalizeUrl($url){
        return Ynmobile_Helper_Base::finalizeUrl($url);
    }

    /**
     * get working item type.
     */
    function getWorkingType($type){

        if(isset(self::$workingTypes[$type])){
            return self::$workingTypes[$type];
        }

        if(Engine_Api::_()->hasItemType($type)){
            return self::$workingTypes[$type] =  $type;
        }

        $parts = explode('_', $type,2);
        if(count($parts) > 1){
            $module =    $parts[0];
            $track  =    $parts[1];
        }else{
            $module = $type;
            $track  = $type;
        }

        $module = $this->getWorkingModule($module);
        return self::$workingTypes[$type] =  $module .'_'. $track;
    }

    function getActivityType($type){
        $parts = explode('_', $type,2);

        if(count($parts) > 1){
            return $this->getWorkingModule($module) . '_'. $parts[1];
        }else{
            return $type;
        }
    }


    function getWorkingModule($module = null) {
        return Ynmobile_AppMeta::getWorkingModule($module?$module:$this->module);
    }

    function getWorkingApi($api = 'core', $module = null) {

        $module = $this->getWorkingModule($module);

        return Engine_Api::_()->getApi($api, $module);
    }

    function getWorkingTable($table, $module=null){

        $module = $this->getWorkingModule($module);

        return Engine_Api::_()->getDbTable($table, $module);
    }

    protected $categoryAssoc = array();

    function getCategoryName($id){
        $assoc  = $this->loadCategoryAssoc();

        return isset($assoc[$id])?$assoc[$id]:'';
    }

    function loadCategoryAssoc(){

        if($this->categoryAssoc){
            return $this->categoryAssoc;
        }

        $table = $this -> getWorkingTable('categories', $this->module);

        $named   = array_pop(array_intersect(array(
            'category_name',
            'title'
        ),array_values($table->info('cols'))));

        $select = $table -> select()->order($named);

        foreach ($table->fetchAll($select) as $cate) {
            $this->categoryAssoc[$cate->getIdentity()] = $cate->{$named};
        }

        return $this->categoryAssoc;
    }

    /**
     * get get category options
     */
    function categories() {

        foreach ($this->loadCategoryAssoc() as $id=>$title) {
            $options[] = array(
                'id' => intval($id),
                'title' => $title,
            );
        }

        return $options;
    }

    function getPrivacyValue($entry, $list, $action){
        $viewer =  Engine_Api::_()->user()->getViewer();

        $type =  $this->getWorkingType($entry->getType());
        $availableLabels  = $this->availablePrivacyOptions;
        $auth = Engine_Api::_()->authorization()->context;

        if(!$viewer){
            return array();
        }

        // Element: auth_view
        $options = (array) Engine_Api::_()
            ->authorization()
            ->getAdapter('levels')
            ->getAllowed($type, $viewer, $list);

        $options = array_reverse(array_intersect_key($availableLabels, array_flip($options)));
        $result = array();

        foreach($options as $role=>$label){
            if($auth->isAllowed($entry, $role, $action)){
                $result = array(
                    'id'=>$role,
                    'title'=>Zend_Registry::get('Zend_Translate')->_($label)
                );
            }
        }

        /**
         * init if empty values.
         */
        if(empty($result) && count($options)){
            foreach($options as $role=>$label){
                $result = array(
                    'id'=>$role,
                    'title'=>Zend_Registry::get('Zend_Translate')->_($label)
                );
            }
        }

        return $result;
    }

    /**
     * @return array[{id: int, title: label}] // options
     * etc: [{id: everyone, title: "everyone"}, ... ]
     */
    function getPrivacyOptions($itemType, $action){

        $viewer =  Engine_Api::_()->user()->getViewer();

        $type =  $this->getWorkingType($itemType);
        $availableLabels  = $this->availablePrivacyOptions;

        if(!$viewer){
            return array();
        }


        // Element: auth_view
        $options = (array) Engine_Api::_()
            ->authorization()
            ->getAdapter('levels')
            ->getAllowed($type, $viewer, $action);

        $options = array_intersect_key($availableLabels, array_flip($options));
        $result = array();
        foreach($options as $key=>$title){
            $result[] = array(
                'id'=>$key,
                'title'=>Zend_Registry::get('Zend_Translate')->_($title),
            );
        }

        return $result;
    }

    function viewOptions() {
        return $this->getPrivacyOptions($this->mainItemType, 'auth_view');
    }

    function photoOptions() {
        return $this->getPrivacyOptions($this->mainItemType, 'auth_photo');
    }

    function eventOptions() {
        return $this->getPrivacyOptions($this->mainItemType, 'auth_event');
    }

    function commentOptions() {
        return $this->getPrivacyOptions($this->mainItemType, 'auth_comment');
    }


    function inviteOptions() {
        return $this->getPrivacyOptions($this->mainItemType, 'auth_invite');
    }

    /**
     * form add
     */
    public function formadd($aData) {

        $response = array(
            'viewOptions' => $this -> viewOptions(),
            'commentOptions' => $this -> commentOptions(),
            'categoryOptions' => $this -> categories(),
        );

        return $response;
    }

    public function getCommentOptions($itemType) {

        $options = array(
            'bShowComments' => 0,
        );

        if (!isset($itemType) || !Engine_Api::_()->hasModuleBootstrap('yncomment')) {
            return $options;
        }

        // core.comments, yncomment.comments
        $pageName = self::$pageNameMap[$this->getWorkingModule($itemType)];

        if (!$pageName) {
            // return if there is no map page
            return $options;
        }

        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $contentTable = Engine_Api::_()->getDbTable('content', 'core');
        $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $pageName));
        // get content row of yncomment content

        if (!$pageObject) {
            // return if there is no map page
            return $options;
        }

        $contentRow = $contentTable->fetchRow(array(
            'page_id = ?' => $pageObject->page_id,
            'name IN (?)' => array('core.comments','yncomment.comments')
        ));

        if (!$contentRow) {
            // return if there is no map page
            return $options;
        }

        $options['bShowComments'] = 1;

        return $options;
    }

    /**
     * @param $itemType
     * @return array(
     * enabled int,
     *
     *
     * )
     */

    public function getAdvancedCommentOptions($itemType) {

        // return enable for now
        $options = array(
            'bEnabled' => 0,
            'bIsAdvanced' => 0
        );

        if (!isset($itemType) || !Engine_Api::_()->hasModuleBootstrap('yncomment')) {
            return $options;
        }

        if ($itemType == 'activity_action'){
            // get adv comment setting for activity action
            $modulesTable = Engine_Api::_()->getDbtable('modules', 'yncomment');
            $modules = $modulesTable -> fetchRow(array('resource_type = ?' => 'ynfeed'));
            $val = $modules -> toArray();
        } else {
            // get comment setting for other item type
            // $pageName = self::$pageNameMap[$itemType];
            $pageName = self::$pageNameMap[$this->getWorkingModule($itemType)];

            if (!$pageName) {
                // return if there is no map page
                return $options;
            }
            $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
            $contentTable = Engine_Api::_()->getDbTable('content', 'core');
            $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $pageName));
            // get content row of yncomment content

            if (!$pageObject) {
                // return if there is no map page
                return $options;
            }

            $contentRow = $contentTable->fetchRow(array(
                'page_id = ?' => $pageObject->page_id,
                'name = ?' => 'yncomment.comments'
            ));

            if (!$contentRow) {
                // return if there is no map page
                return $options;
            }

            $val = $contentRow -> toArray();
        }
        if ($val['params']) {

            // return corresponding data
            if (is_array($val['params'])) {
                $aParams = $val['params'];
            } else {
                $aParams = Zend_Json_Decoder::decode($val['params']);
            }

            // enable setting for activity feed, enable if content present for other item
            $enabled = isset($aParams['enabled']) ? intval($aParams['enabled']) : 1;
            $taggingContent = !empty($aParams['taggingContent']) ? $aParams['taggingContent'] : array();
            $showComposerOptions = !empty($aParams['showComposerOptions']) ? $aParams['showComposerOptions'] : array();
            // feed always show reply (called show as nested)
            // showAsNested = 1 : show replys on both comment and reply, 0 not show
            $showAsNested = isset($aParams['showAsNested']) ? intval($aParams['showAsNested']) : 1;
            // showAsLike : 1 like only, 0 : both like and disklike
            $showAsLike = isset($aParams['showAsLike']) ? intval($aParams['showAsLike']) : 1;
            $showDislikeUsers = isset($aParams['showDislikeUsers']) ? intval($aParams['showDislikeUsers']) : 0;
            $commentsorder = isset($aParams['commentsorder']) ? intval($aParams['commentsorder']) : 0;
            $showLikeWithoutIconInReplies = isset($aParams['showLikeWithoutIconInReplies']) ? intval($aParams['showLikeWithoutIconInReplies']) : 0;

            // parse options
            $options['bEnabled'] = $enabled;
            $options['bIsAdvanced'] = $enabled;
            $options['aTaggingContent'] = $taggingContent;
            $options['aAttachmentTypes'] = $showComposerOptions;
            $options['bCanReply'] = $showAsNested;
            $options['bCanDislike'] = $showAsLike ? 0 : 1;
            $options['bShowDislikeUsers'] = $showDislikeUsers;
            $options['iCommentsOrder'] = $commentsorder;
            $options['bShowVote'] = ($showLikeWithoutIconInReplies == 3 && !$showAsLike) ? 1 : 0;
        }

        return $options;
    }

    /**
     * Check if Advanced feed widget is added to the item's activity page
     * @param null $itemType
     * @return bool
     */
    public function hasAdvancedFeed($itemType = null) {

        if (!$itemType){
            $itemType = 'activity';
        }

        // false if Advanced feed module is not installed and enabled, obviously
        if (!Engine_Api::_()->hasModuleBootstrap('ynfeed')) {
            return false;
        }

        // get page name from map table between object type and page name
        // this requires default page's names is not changed
        $pageName = self::$activityPageMap[$this->getWorkingModule($itemType)];

        // there is no map page implemented such as new module added
        if (!$pageName) {
            return false;
        }

        // get core page and core content table
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $contentTable = Engine_Api::_()->getDbTable('content', 'core');

        // get the page object
        $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $pageName));

        // cannot find page
        if (!$pageObject) {
            return false;
        }

        // is ynfeed.feed added in this page ?
        $contentRow = $contentTable->fetchRow(array(
            'page_id = ?' => $pageObject->page_id,
            'name = ?' => 'ynfeed.feed'
        ));

        if ($contentRow) {
            return true;
        } else {
            return false;
        }
    }
}
