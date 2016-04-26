<?php

class Ynmobile_AppMeta {

    protected $metas = array();
    
    static $modules = array(
    	'activity'=>array('ynfeed','activity'),
        'blog'=>array('ynblog','advblog','blog'),
        'video'=>array('ynvideo','video'),
        'group'=>array('advgroup','group'),
        'album'=>array('advalbum','album'),
        'event'=>array('ynevent','event'),
        'forum'=>array('ynforum','forum'),
        'classified'=>array('classified'),
        'messages'=>array('messages'),
        'poll'=>array('poll',),
        'music'=>array('mp3music','music'),
        'ynmobile'=>array('ynmobile'),
        'user'=>array('user'),
    );
    
    static $inst;
    
    static $workingModules = array();
    
    static private function _getWorkingModule($key){
        $engine = Engine_Api::_();
        foreach(self::$modules[$key] as $module){
            if($engine->hasModuleBootstrap($module)){
                return $module;
            }
        }
        
        return $key;
    }
    
    static function getWorkingModule($key){
        if(!isset(self::$workingModules[$key])){
            self::$workingModules[$key] =  self::_getWorkingModule($key);
        }
        return self::$workingModules[$key];
    }

    function __construct() {
        $this -> supportUser();
        $this -> supportMusic();
        $this -> supportAlbum();
        $this -> supportEvent();
        $this -> supportClassified();
        $this -> supportGroup();
        $this -> supportVideo();
        $this -> supportPoll();
        $this -> supportFeed();
        $this -> supportBlog();
        $this -> supportForum();
        $this -> supportNetwork();
		$this -> supportYnJobPosting();
		$this -> supportYnlisting();
		$this -> supportUltimatenews();
        $this -> supportYnbusinesspages();
        $this -> supportYnresume();
    }
    
    static function getInstance(){
        if(null == self::$inst){
            self::$inst = new self;
        }
        return self::$inst;
    }
    
    function getModelHelper($entry, $opts = null, $params =  array()){
        
        $type = $entry->getType();
        
        
        $options = $this->getMeta('model', $type);
        
        if($opts){
            $options = array_merge($options, $opts);    
        }
        
        
        $def = @$options['def']?$options['def']:'Ynmobile_Helper_Base';
        
        return new $def($entry, $options, $params);
    }
    
    function addMeta($category, $key, $specs) {
        if(is_array($key)){
            foreach($key as $k){
                $this -> metas[$category][$k] = $specs;        
            }
        }else{
            $this -> metas[$category][$key] = $specs;
        }
        
    }

    function getMeta($category, $key) {
        return $this -> metas[$category][$key];
    }
    function supportNetwork() {
        
        $this -> addMeta('model', array(
            'activity_notification'
        ), array(
            'def'=>'Ynmobile_Helper_Notification',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
        
        $this -> addMeta('model', array(
            'network'
        ), array(
            'def'=>'Ynmobile_Helper_Network',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
        
        $this -> addMeta('model', array(
            'core_link',
            'link'
        ), array(
            'def'=>'Ynmobile_Helper_Link',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
        
        $this -> addMeta('model', array(
            'core_like',
            'activity_like'
        ), array(
            'def'=>'Ynmobile_Helper_Like',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));

        $this -> addMeta('model', array(
            'yncomment_dislike',
        ), array(
            'def'=>'Ynmobile_Helper_Dislike',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
    }
    
    function supportBlog() {
        $this -> addMeta('model', array(
            'blog'
        ), array(
            'def'=>'Ynmobile_Helper_Blog',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
    }
	
	protected function supportYnJobPosting(){
		$this -> addMeta('model', array(
            'ynjobposting_job'
        ), array(
            'def'=>'Ynmobile_Helper_Ynjobposting_Job',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
		
		$this -> addMeta('model', array(
            'ynjobposting_jobapply'
        ), array(
            'def'=>'Ynmobile_Helper_Ynjobposting_Jobapply',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
		
		
		$this -> addMeta('model', array(
            'ynjobposting_company'
        ), array(
            'def'=>'Ynmobile_Helper_Ynjobposting_Company',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            	'' => '/application/modules/Ynmobile/externals/images/nophoto_company_thumb_main.png',
            	'cover' => '/application/modules/Ynmobile/externals/images/company_default_cover.png',
            	'thumb.icon' => '/application/modules/Ynmobile/externals/images/nophoto_company_thumb_icon.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/nophoto_company_thumb_normal.png',
                'thumb.profile' => '/application/modules/Ynmobile/externals/images/nophoto_company_thumb_profile.png',
            ),
        ));
		
	}
	
	protected function supportYnlisting(){
		$this -> addMeta('model', array(
            'ynlistings_listing'
        ), array(
            'def'=>'Ynmobile_Helper_Ynlisting_Listing',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.profile' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/image-default.png',
            ),
        ));
		
		$this -> addMeta('model', array(
            'ynlistings_topic'
        ), array(
            'def'=>'Ynmobile_Helper_Ynlisting_Topic',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
		
		$this -> addMeta('model', array(
            'ynlistings_review'
        ), array(
            'def'=>'Ynmobile_Helper_Ynlisting_Review',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
		
		$this -> addMeta('model', array(
            'ynlistings_post'
        ), array(
            'def'=>'Ynmobile_Helper_Ynlisting_Post',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
		
		
		$this -> addMeta('model', array(
            'ynlistings_album',
        ), array(
            'def'=>'Ynmobile_Helper_Ynlisting_Album',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/image-default.png',
            ),
        ));
        
        $this -> addMeta('model', array(
            'ynlistings_photo'
        ), array(
            'def'=>'Ynmobile_Helper_Ynlisting_Photo',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/image-default.png',
            ),
        ));
	}
	
	protected function supportUltimatenews(){
		$this -> addMeta('model', array(
            'ultimatenews_content'
        ), array(
            'def'=>'Ynmobile_Helper_Ultimatenews_Content',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            	'' => '/application/modules/Ynmobile/externals/images/ultimatenews-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/ultimatenews-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/ultimatenews-default.png',
                'thumb.profile' => '/application/modules/Ynmobile/externals/images/ultimatenews-default.png',
            ),
        ));
		
		$this -> addMeta('model', array(
            'ultimatenews_category'
        ), array(
            'def'=>'Ynmobile_Helper_Ultimatenews_Feed',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
		
		
	}
    
     function supportPoll() {
        $this -> addMeta('model', array(
            'poll'
        ), array(
            'def'=>'Ynmobile_Helper_Poll',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
                'thumb.icon' => '/application/modules/User/externals/images/nophoto_user_thumb_icon.png',
                'thumb.normal' => '/application/modules/User/externals/images/nophoto_user_thumb_profile.png',
                'thumb.profile' => '/application/modules/User/externals/images/nophoto_user_thumb_profile.png',
            ),
        ));
    }
    
    
    function supportForum(){
        $this -> addMeta('model', array(
            'forum'
        ), array(
            'def'=>'Ynmobile_Helper_Forum',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
               
            ),
        ));
        
        $this -> addMeta('model', array(
            'forum_topic'
        ), array(
            'def'=>'Ynmobile_Helper_ForumTopic',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
        
        $this -> addMeta('model', array(
            'forum_post'
        ), array(
            'def'=>'Ynmobile_Helper_ForumPost',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
        
        
    }

    function supportUser() {
        $this -> addMeta('model', array(
            'user'
        ), array(
            'def'=>'Ynmobile_Helper_User',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/avatar-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/avatar-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/avatar-default.png',
                'thumb.profile' => '/application/modules/Ynmobile/externals/images/avatar-default.png',
            ),
        ));
        
        $this->addMeta('model',array(
            'ynmobile_map'
        ),array(
        'def'=>'Ynmobile_Helper_Checkin'));
		
		$this->addMeta('model',array(
            'ynfeed_map'
        ),array(
        'def'=>'Ynmobile_Helper_YnfeedMap'));
        
        $this->addMeta('model',array(
            'activity_comment',
            'core_comment',
        ),array(
        'def'=>'Ynmobile_Helper_Comment'));
    }
    
    
    function supportVideo() {
        $this -> addMeta('model', array(
            'video','ynvideo',
        ), array(
            'def'=>'Ynmobile_Helper_Video',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                ''=>'/application/modules/Ynmobile/externals/images/video-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/video-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/video-default.png',
            ),
        ));
    }

    function supportEvent() {
        $this -> addMeta('model', array(
            'event',
            'ynevent'
        ), array(
            'def'=>'Ynmobile_Helper_Event',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/event-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/event-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/event-default.png',
            ),
        ));
        
        $this -> addMeta('model', array(
            'event_photo',
            'ynevent_photo',
        ), array(
            'def'=>'Ynmobile_Helper_EventPhoto',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/image-default.png',
            ),
        ));
    }

    function supportMusic() {
        
        $this -> addMeta('model', array(
            'music_playlist',
        ), array(
            'def'=>'Ynmobile_Helper_MusicPlaylist',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/music-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/music-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/music-default.png',
            ),
        ));
        
        $this -> addMeta('model', array(
            'mp3music_album',
        ), array(
            'def'=>'Ynmobile_Helper_Mp3MusicAlbum',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/music-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/music-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/music-default.png',
            ),
        ));
        
        $this->addMeta('model', array(
            'music_playlist_song',
            'mp3music_album_song',
        ), array(
            'def'=>'Ynmobile_Helper_MusicPlaylistSong',
            'simple_img'=>'thumb.icon',
            'no_imgs'=>array(
                '' => '/application/modules/Ynmobile/externals/images/music-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/music-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/music-default.png',
            )
        ));
    }

    function supportFeed(){
        $this -> addMeta('model', array(
            'activity_action',
        ), array(
            'def'=>'Ynmobile_Helper_Feed',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/image-default.png',
            ),
        ));
    }
    function supportAlbum() {
        $this -> addMeta('model', array(
            'ynalbum',
            'advalbum_album',
            'album_album',
            'album',
        ), array(
            'def'=>'Ynmobile_Helper_Album',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/image-default.png',
            ),
        ));
        
        $this -> addMeta('model', array(
            'album_photo',
            'advalbum_photo',
        ), array(
            'def'=>'Ynmobile_Helper_AlbumPhoto',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/image-default.png',
            ),
        ));
    }

    function supportGroup() {
        $this -> addMeta('model', array(
            'group','advgroup'
        ), array(
            'def'=>'Ynmobile_Helper_Group',
            ''=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Group/externals/images/nophoto_group_thumb_icon.png',
                'thumb.icon' => '/application/modules/Group/externals/images/nophoto_group_thumb_icon.png',
                'thumb.normal' => '/application/modules/Group/externals/images/nophoto_group_thumb_normal.png',
                'thumb.profile' => '/application/modules/Group/externals/images/nophoto_group_thumb_profile.png'
            ),
        ));
        
        $this -> addMeta('model', array(
            'group_photo',
            'advgroup_photo',
        ), array(
            'def'=>'Ynmobile_Helper_GroupPhoto',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/image-default.png',
            ),
        ));
    }

    function supportClassified() {
        $this -> addMeta('model', 'classified', array(
            'def'=>'Ynmobile_Helper_Classified',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Classified/externals/images/nophoto_classified_thumb_profile.png',
                'thumb.icon' => '/application/modules/Classified/externals/images/nophoto_classified_thumb_normal.png',
                'thumb.normal' => '/application/modules/Classified/externals/images/nophoto_classified_thumb_normal.png',
                'thumb.profile' => '/application/modules/Classified/externals/images/nophoto_classified_thumb_profile.png'
            ),
        ));
        
        
        $this -> addMeta('model', array(
            'classified_photo',
        ), array(
            'def'=>'Ynmobile_Helper_ClassifiedPhoto',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/image-default.png',
            ),
        ));
    }

    function supportYnbusinesspages(){
        $this -> addMeta('model', array(
            'ynbusinesspages_business',
        ), array(
            'def'=>'Ynmobile_Helper_Ynbusinesspages_Business',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/nophoto_business_thumb_main.png',
                'cover' => '/application/modules/Ynmobile/externals/images/business_default_cover.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/nophoto_business_thumb_icon.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/nophoto_business_thumb_normal.png',
                'thumb.profile' => '/application/modules/Ynmobile/externals/images/nophoto_business_thumb_profile.png',
            ),
        ));

        $this -> addMeta('model', array(
            'ynbusinesspages_review'
        ), array(
            'def'=>'Ynmobile_Helper_Ynbusinesspages_Review',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));

        $this -> addMeta('model', array(
            'ynbusinesspages_post'
        ), array(
            'def'=>'Ynmobile_Helper_Ynbusinesspages_Post',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));

        $this -> addMeta('model', array(
            'ynbusinesspages_topic'
        ), array(
            'def'=>'Ynmobile_Helper_Ynbusinesspages_Topic',
            'simple_img'=>'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));

        $this -> addMeta('model', array(
            'ynbusinesspages_album',
        ), array(
            'def'=>'Ynmobile_Helper_Ynbusinesspages_Album',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/image-default.png',
            ),
        ));

        $this -> addMeta('model', array(
            'ynbusinesspages_photo'
        ), array(
            'def'=>'Ynmobile_Helper_Ynbusinesspages_Photo',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/image-default.png',
            ),
        ));

//      use simple array for locations now, may use location helper in the future
//        $this -> addMeta('model', array(
//            'ynbusinesspages_location',
//        ), array(
//            'def'=>'Ynmobile_Helper_Ynbusinesspages_Location',
//        ));
    }

    function supportYnresume(){
        $this -> addMeta('model', array(
            'ynresume_resume',
        ), array(
            'def'=>'Ynmobile_Helper_Ynresume_Resume',
            'simple_img'=>'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynmobile/externals/images/nophoto_resume_thumb_profile.png',
                'thumb.icon' => '/application/modules/Ynmobile/externals/images/nophoto_resume_thumb_icon.png',
                'thumb.normal' => '/application/modules/Ynmobile/externals/images/nophoto_resume_thumb_profile.png',
                'thumb.profile' => '/application/modules/Ynmobile/externals/images/nophoto_resume_thumb_profile.png',
            ),
        ));

        $this -> addMeta('model', array(
            'ynresume_award',
        ), array(
            'def'=>'Ynmobile_Helper_Ynresume_Award',
            'simple_img'=>'thumb.icon',
            'no_imgs' => array(
            ),
        ));

        $this -> addMeta('model', array(
            'ynresume_certification',
        ), array(
            'def'=>'Ynmobile_Helper_Ynresume_Certification',
            'simple_img'=>'thumb.icon',
            'no_imgs' => array(
            ),
        ));

        $this -> addMeta('model', array(
            'ynresume_course',
        ), array(
            'def'=>'Ynmobile_Helper_Ynresume_Course',
            'simple_img'=>'thumb.icon',
            'no_imgs' => array(
            ),
        ));

        $this -> addMeta('model', array(
            'ynresume_education',
        ), array(
            'def'=>'Ynmobile_Helper_Ynresume_Education',
            'simple_img'=>'thumb.icon',
            'no_imgs' => array(
            ),
        ));

        $this -> addMeta('model', array(
            'ynresume_experience',
        ), array(
            'def'=>'Ynmobile_Helper_Ynresume_Experience',
            'simple_img'=>'thumb.icon',
            'no_imgs' => array(
            ),
        ));

        $this -> addMeta('model', array(
            'ynresume_language',
        ), array(
            'def'=>'Ynmobile_Helper_Ynresume_Language',
            'simple_img'=>'thumb.icon',
            'no_imgs' => array(
            ),
        ));

        $this -> addMeta('model', array(
            'ynresume_project',
        ), array(
            'def'=>'Ynmobile_Helper_Ynresume_Project',
            'simple_img'=>'thumb.icon',
            'no_imgs' => array(
            ),
        ));

        $this -> addMeta('model', array(
            'ynresume_publication',
        ), array(
            'def'=>'Ynmobile_Helper_Ynresume_Publication',
            'simple_img'=>'thumb.icon',
            'no_imgs' => array(
            ),
        ));

        $this -> addMeta('model', array(
            'ynresume_skill',
        ), array(
            'def'=>'Ynmobile_Helper_Ynresume_Skill',
            'simple_img'=>'thumb.icon',
            'no_imgs' => array(
            ),
        ));

        $this -> addMeta('model', array(
            'ynresume_recommendation',
        ), array(
            'def'=>'Ynmobile_Helper_Ynresume_Recommendation',
            'simple_img'=>'thumb.icon',
            'no_imgs' => array(
            ),
        ));
    }
    
    static function _exports_by_page($select, $iPage, $iLimit, $fields, $params  = array()) {
        if(!$select){
            return array();
        }
        else if($select instanceof Zend_Paginator){
            $paginator  = $select;
        }else{
            $paginator = Zend_Paginator::factory($select);    
        }
        
        $paginator -> setCurrentPageNumber($iPage);

        $paginator -> setItemCountPerPage($iLimit);
		
		if($iPage < 1){
			$iPage =  1;
		}
		
		if(!$iLimit){
			$iLimit =  10;
		}

        if ($iPage > $paginator -> count()) {
            return array();
        }

        $return = array();

        foreach ($paginator as $entry) {
            $return[] = self::getInstance() -> getModelHelper($entry, array(), $params) -> toArray($fields);
        }

        return $return;
    }
    
    static function _export_one($entry, $fields, $params = array()){
            
        return self::getInstance()->getModelHelper($entry, array(), $params)->toArray($fields);
    }

    static function _export_all($select, $fields, $params =  array()) {
         
        if(!$select){
            return array();
        }else
        if($select instanceof Zend_Paginator){
            $paginator  = $select;
        }else{
            $paginator = Zend_Paginator::factory($select);
            $paginator->setItemCountPerPage(1000);    
        }
        
        $return = array();
        
        foreach ($paginator as $entry) {
            try{
                $return[] = Ynmobile_AppMeta::getInstance() -> getModelHelper($entry, array(), $params) -> toArray($fields);    
            }catch(Exception $e){
                
            }
            
        }

        return $return;
    }
    
}
