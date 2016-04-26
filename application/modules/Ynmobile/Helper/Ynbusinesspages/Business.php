<?php
/**
 * Created by IntelliJ IDEA.
 * User: macpro
 * Date: 7/21/15
 * Time: 5:06 PM
 */
class Ynmobile_Helper_Ynbusinesspages_Business extends Ynmobile_Helper_Base
{
    /**
    [business_id] => 2
    [user_id] => 1
    [package_id] => 1
    [theme] => theme1
    [name] => test require approve
    [short_description] => <p>asdasd</p>
    [description] => <p>adasdasdas</p>
    [search] => 1
    [approval] => 1
    [rating] => 0
    [size] => 123
    [phone] => ["123123123","234235345"]
    [fax] => ["1231234564","345643"]
    [email] => blabal@bolob.com
    [country] => Andorra
    [city] => Cocoa
    [province] => Zed
    [zip_code] => 90038
    [web_address] => ["www.bl.com","www.test.net"]
    [facebook_link] => bloblo
    [twitter_link] => twittwes
    [timezone] => (UTC-8) Pacific Time (US & Canada)
    [photo_id] => 26
    [creation_date] => 2015-07-23 08:52:02
    [modified_date] => 2015-07-23 10:57:11
    [deleted] => 0
    [status] => published
    [approved] => 1
    [featured] => 0
    [is_claimed] => 0
    [last_payment_date] => 2015-07-23 08:52:06
    [approved_date] => 2015-07-23 08:52:06
    [expiration_date] => 2042-12-07 08:52:06
    [like_count] => 0
    [view_count] => 7
    [follow_count] => 0
    [comment_count] => 0
    [review_count] => 0
    [checkin_count] => 0
    [topic_count] => 0
    [never_expire] => 0
     */

    /*
     *
     * 0: "ynbusinesspages_album"
        1: "poll"
        2: "music_playlist"
        3: "mp3music_album"
        4: "ynbusinesspages_topic"
        5: "classified"
        6: "ynlistings_listing"
        7: "ynjobposting_job"
     */

    static $removed_supported_items = array(
        'music_playlist'=>'mp3music',
//        'video'=>'ynvideo',
//        'event'=>'ynevent',
    );

    public function getYnmobileApi()
    {
        return Engine_Api::_()->getApi('ynbusinesspages', 'ynmobile');
    }

    function field_id(){
        return $this->data['iBusinessId'] =  $this->entry->getIdentity();
    }

    public function field_listing() {
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_rate();
        $this->field_locations();
        $this->field_stats();
        $this->field_imgFull();
        $this->field_imgNormal();
        $this->field_user();
        $this->field_permissions();
        $this->field_member_status();

        $subject = $business = $this->entry;
        $businessId = $business->getIdentity();
        $viewer = $this->getViewer();
        $viewerId = $this->getViewerId();

        $this->data['iSize'] = $business->size;
        $this->data['iCategoryId'] = $business->getMainCategoryId();
        $mainCategory = $business->getMainCategory();
        $this->data['sCategory'] = $mainCategory? $mainCategory->getTitle() : '';
        $this->data['sLocation'] = $business->getMainLocation();
        $this->data['sPackageName'] = '';
        $this->data['bIsRequireApproval'] = $business->approval;
        $this->data['iPackageId'] = $business->package_id;
        $this->data['sStatus'] = $business->status;
        $this->data['bIsApproved'] = $business->approved;
        $this->data['bIsClaimed'] = $business->is_claimed;
        $this->data['bIsNeverExpire'] = $business->never_expire;
        $this->data['sExpireDate'] = $business->expiration_date?strtotime($business->expiration_date) : '';
        $this->data['sApprovedDate'] = $business->approved_date?strtotime($business->approved_date) : '';
        $this->data['sLastPaymentDate'] = $business->last_payment_date?strtotime($business->last_payment_date) : '';
        $this->data['bIsFeatured'] = $business->featured;
        $this->data['sFeatureExpirationDate'] = 0;
        $checkInTbl = Engine_Api::_()->getDbTable('checkin', 'ynbusinesspages');
        $this->data['bIsCheckedIn'] = $checkInTbl->isCheckedIn($viewer, $business)?1:0;
        $this->data['iTotalFollow'] = $business->getFollowerCount();
        $this->data['iTotalMember'] = $business->getMemberCount();
        // claiming status
        $claimTable = Engine_Api::_() -> getDbTable('claimrequests', 'ynbusinesspages');
        $request = $claimTable -> getClaimRequest($viewerId, $businessId);
        if ($request) {
            $this->data['sClaimingStatus'] = $request->status;
        } else {
            $this->data['sClaimingStatus'] = '';
        }

        // retrieve package name and max cover photos number
        $package = $business -> getPackage();
        if($package -> getIdentity())
        {
            $this->data['sPackageName'] = $package->title;
            if (isset($package->max_cover)){
                $this->data['iMaxCover'] = $package->max_cover;
            }
        }

        // featured and feature expiration date
        if ($business->featured) {
            $featureRow = Engine_Api::_() -> getDbTable('features', 'ynbusinesspages') -> getFeatureRowByBusinessId($businessId);
            if ($featureRow){
                $featureDateObj = null;
                if (!is_null($featureRow->expiration_date) && !empty($featureRow->expiration_date) && $featureRow->expiration_date) {
                    {
                        $featureDateObj = new Zend_Date(strtotime($featureRow->expiration_date));
                    }
                    if( $viewer && $viewerId ) {
                        $tz = $viewer->timezone;
                        if (!is_null($featureDateObj))
                        {
                            $featureDateObj->setTimezone($tz);
                        }
                    }
                }
                if ($featureDateObj) {
                    $this->data['sFeatureExpirationDate'] = $featureDateObj->getTimeStamp();
                }
            }
        }

        // status
        if ($viewer){
            $tableFollow = Engine_Api::_()->getDbTable('follows', 'ynbusinesspages');
            $isFollowing = $tableFollow->getFollowBusiness($businessId, $viewerId);
            $this->data['bIsFollowing'] = $isFollowing?1:0;
            $tableFavourite = Engine_Api::_()->getDbTable('favourites', 'ynbusinesspages');
            $isFavourite = $tableFavourite->getFavouriteBusiness($businessId, $viewerId);
            $this->data['bIsFavourite'] = $isFavourite?1:0;
        }
    }

    public function field_permissions() {
        $subject = $business = $this->entry;
        $businessId = $business->getIdentity();
        $viewer = $this->getViewer();
        $viewerId = $this->getViewerId();

        // permission
        $this->data['bCanEdit'] = $bCanEdit = 0;
        $this->data['bCanDelete'] = $bCanDelete = 0;
        $this->data['bCanShare'] = $bCanShare = 0;
        $this->data['bCanOpenClose'] = $bCanOpenClose = 0;
        $this->data['bCanFollow'] = $bCanFollow = 0;
        $this->data['bCanFavourite'] = $bCanFavourite = 0;
        $this->data['bCanCheckin'] = $bCanCheckin = 0;
        $this->data['bCanInvite'] = $bCanInvite = 0;
        $this->data['bCanClaim'] = $bCanClaim = 0;
        $this->data['bCanReport'] = $bCanReport = 0;
        $this->data['bCanMessageOwner'] = $bCanMessageOwner = 0;
//        $isShowMore = true;

        $claimTable = Engine_Api::_() -> getDbTable('claimrequests', 'ynbusinesspages');
        $request = $claimTable -> getClaimRequest($viewer -> getIdentity(), $subject -> getIdentity());
        $this->data['bIsClaiming'] = (!empty($request) && $request->status != 'approved')? 1: 0;
        // return all zeros if the business is unclaimed
        if(!$business -> isClaimedByUser() && $business->status == 'unclaimed') {
            $bCanClaim = 1;
            $this->data['bCanClaim'] = $bCanClaim;
            return;
        }

        if(!$business->is_claimed){
            $bCanFollow = 1;
            $bCanFavourite = 1;
            if (!$business -> isCheckedIn($viewer)){
                $bCanCheckin = 1;
            }
        }

        // check for detail permission
        if(!in_array($business -> status, array('claimed', 'unclaimed', 'deleted'))) {
            if ($business->isAllowed('edit')) {
                $bCanEdit = 1;
            }
            if ($business->isAllowed('delete')) {
                $bCanDelete = 1;
            }
        }

        if (in_array($business -> status, array('closed', 'published'))) {
            if ($business->isAllowed('edit')) {
                $bCanOpenClose = 1;
            }
        }

        $package = $business -> getPackage();

        if($package -> getIdentity())
        {
            if ($package->allow_user_share_business) {
                $bCanShare = 1;
            }
            if($package -> allow_user_invite_friend) {
                if ($business->isAllowed('invite'))
                {
                    $bCanInvite = 1;
                }
            }
        }

        if (($business->user_id != $viewer->getIdentity()) && !$business->is_claimed){
            $this->data['bCanReport'] = 1;
            $this->data['bCanMessageOwner'] = 1;
        }

        $this->data['bCanEdit'] = $bCanEdit;
        $this->data['bCanDelete'] = $bCanDelete;
        $this->data['bCanShare'] = $bCanShare;
        $this->data['bCanOpenClose'] = $bCanOpenClose;
        $this->data['bCanFollow'] = $bCanFollow;
        $this->data['bCanFavourite'] = $bCanFavourite;
        $this->data['bCanCheckin'] = $bCanCheckin;
        $this->data['bCanInvite'] = $bCanInvite;
        $this->data['bCanClaim'] = $bCanClaim;
    }

    public function field_member_status() {
        $business = $subject = $this->entry;
        $viewer = $this->getViewer();
        $this->data['bCanRequest'] = 0;
        $this->data['bIsSentRequest'] = 0;
        $this->data['bIsInvited'] = 0;
        $this->data['bCanJoin'] = 0;
        $this->data['bCanLeave'] = 0;

        $package = $business -> getPackage();
        if(!$package->getIdentity() || !$package->allow_user_join_business)
        {
            return false;
        }
        $row = $business->membership()->getRow($viewer);

        if( null === $row ) {
            if( $business->membership()->isResourceApprovalRequired() ) {
                $this->data['bCanRequest'] = 1;
            } else {
                $this->data['bCanJoin'] = 1;
            }
        } else if( $row->active ) {
            if( !$business->isOwner($viewer) )
            {
                $this->data['bCanLeave'] = 1;
            } else {
                return false;
            }
        } else if( !$row->resource_approved && $row->user_approved ) {
            $this->data['bIsSentRequest'] = 1;
        } else if( !$row->user_approved && $row->resource_approved ) {
            $this->data['bIsInvited'] = 1;
        }
    }

    public function field_infos() {
        $this->field_listing();
        $this->field_full_category();
        $this->field_cover_photos();
        $this->field_cover_photos_edit();
        $this->field_founders();
        $this->field_operating_hours();
        $this->field_available_modules();
        $this->field_sub_pages();


        // more detail for detail and edit pages
        $subject = $business = $this->entry;
        $followTable = Engine_Api::_() -> getDbTable('follows', 'ynbusinesspages');
        $followers = $followTable -> getUsersFollow($subject->getIdentity());
        $this->data['iTotalFollow'] = count($followers);
        $this->data['iTotalMember'] = $business->getMemberCount();
        $this->data['sShortDesc'] = $business->short_description;
        $this->data['sDescription'] = $business->description;
        $this->data['aPhone'] = !empty($business->phone)? $business->phone : array();
        $this->data['aFax'] = !empty($business->fax)? $business->fax : array();
        $this->data['sEmail'] = $business->email;
        $this->data['sCountry'] = $business->country;
        $this->data['sCity'] = $business->city;
        $this->data['sProvince'] = $business->province;
        $this->data['sZipCode'] = $business->zip_code;
        $this->data['aWebAddress'] = !empty($business->web_address)? $business->web_address : array();
        $this->data['sFacebook'] = $business->facebook_link;
        $this->data['sTwitter'] = $business->twitter_link;
        $this->data['bIsSearch'] = $business->search;

        // additional detail info
    }

    public function field_sub_pages() {
        $subject = $business = $this->entry;
        $this->data['iTotalPhotos'] = $business->getAlbumPhotosCount();
        $this->data['iTotalVideos'] = $business->countItemMapping(array('video'));
        $this->data['iTotalEvents'] = $business->countItemMapping(array('event'));
        $this->data['iTotalMusicPlaylist'] = $business->countItemMapping(array('music_playlist'));
        $this->data['iTotalTopic'] = $business->getDiscussionsCount();

        $this->data['bCanUploadPhoto'] = $subject -> isAllowed('album_create');
        $this->data['bCanCreateVideo'] = $subject -> isAllowed('video_create');
        $this->data['bCanCreateTopic'] = $subject -> isAllowed('discussion_create');
        $this->data['bCanCreateEvent'] = $subject -> isAllowed('event_create');
    }

    public function field_available_modules() {

        $business = $this->entry;
        $package = $business -> getPackage();
        $this->data['aAvailableModules'] = array();
        if($package -> getIdentity())
        {
            $modules = $package->getAvailableModules();
            foreach ($modules as $module) {
                // filter normal modules
                if (!Engine_Api::_()->hasModuleBootstrap(self::$removed_supported_items[$module->item_type])) {
                    $aAvailableModules[] = $module->item_type;
                }
            }
        }

        $this->data['aAvailableModules'] = $aAvailableModules;
    }

    public function field_cover_photos() {
        $subject = $business = $this->entry;
        $businessId = $business->getIdentity();
        $aCoverPhotos = array();

        $coverTbl = Engine_Api::_()->getDbTable('covers', 'ynbusinesspages');
        $covers = $coverTbl -> getCoverByBusiness($business);

        foreach ($covers as $key=>$photo) {
            $url = $photo->getPhotoUrl();
            $aCoverPhotos[$key]['sPhotoUrl'] = $this->finalizeUrl($url);
            $aCoverPhotos[$key]['iCoverId'] = $photo->cover_id;
            $aCoverPhotos[$key]['iPhotoId'] = $photo->photo_id;
        }

        if (!count($covers)){
            $aCoverPhotos[] = array(
                'sPhotoUrl' => $this->getNoImg('cover')
            );
        }

        $this->data['aCoverPhotos'] = $aCoverPhotos;
    }

    public function field_cover_photos_edit() {
        $subject = $business = $this->entry;
        $businessId = $business->getIdentity();
        $aCoverPhotos = array();

        $coverTbl = Engine_Api::_()->getDbTable('covers', 'ynbusinesspages');
        $covers = $coverTbl -> getCoverByBusiness($business);

        foreach ($covers as $key=>$photo) {
            $url = $photo->getPhotoUrl();
            $aCoverPhotos[$key]['sPhotoUrl'] = $this->finalizeUrl($url);
            $aCoverPhotos[$key]['iCoverId'] = $photo->cover_id;
            $aCoverPhotos[$key]['iPhotoId'] = $photo->photo_id;
        }

        $this->data['aCoverPhotosEdit'] = $aCoverPhotos;
    }

    public function field_rate() {
        $business = $this->entry;
        $this->data['fRating'] = $business->rating;
        $this->data['bIsRated'] = $business->checkRated();
        $this->data['bIsReviewed'] = $business->checkRated();
        $this->data['iTotalRate'] = $business->getReviewCount();
        $this->data['iTotalReview'] = $business->getReviewCount();
        $this->data['bCanReview'] = 0;

        $can_review = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynbusinesspages_business', null, 'rate') -> checkRequire();
        if (!$business->hasReviewed() && $can_review && !($business -> is_claimed)) {
            $this->data['bCanReview'] = 1;
        }
    }

    public function field_locations() {
        $business = $this->entry;
        $tableLocation = Engine_Api::_()->getDbTable('locations', 'ynbusinesspages');
        $locations = $tableLocation->getLocationsByBusinessId($business->getIdentity());
        $aLocations = array();
        foreach ($locations as $location) {
            $aLocations[] = array(
                'iLocationId'=>$location->getIdentity(),
                'sLocation'=>$location->location,
                'iBusinessId'=>$location->business_id,
                'fLatitude'=>$location->latitude,
                'fLongitude'=>$location->longitude,
                'sLocationTitle'=>$location->title
            );
        }
        $this->data['aLocations'] = $aLocations;
    }

    public function field_founders() {

        $subject = $this->entry;
        $this->data['aFounders'] = array();


        $tableFounder = Engine_Api::_() -> getDbTable('founders', 'ynbusinesspages');
        $founders = $tableFounder -> getFoundersByBusinessId($subject -> getIdentity());

        $fields =  array('simple_array');
        $aFounders = array();

        foreach ($founders as $founder) {
            if ($founder -> user_id) {
                $user = Engine_Api::_() -> getItem('user', $founder -> user_id);
                $helper = Ynmobile_AppMeta::getInstance()
                    ->getModelHelper($user);
                $aFounders[] = $helper ->toArray($fields);
            } else {
                $aFounders[] = array(
                    'id' => 0,
                    'title' => $founder->name
                );
            }
        }
        $this->data['aFounders'] = $aFounders;
    }

    public function field_operating_hours() {
        $subject = $this->entry;
        $tableHours = Engine_Api::_() -> getDbTable('operatinghours', 'ynbusinesspages');
        $operatingHours = $tableHours -> getHoursByBusinessId($subject->getIdentity());

        $aOperatingHours = array();
        foreach($operatingHours as $hour)
        {
            $aOperatingHours[] = array(
                'sDay'=>ucfirst($hour -> day),
                'sFrom'=>$hour -> from,
                'sTo'=>$hour -> to,
            );
        }

        $this->data['aOperatingHours'] = $aOperatingHours;
    }

    public function field_full_category() {
        $aFullCategory = array();
        $tableCategoryMap = Engine_Api::_() -> getDbTable('categorymaps', 'ynbusinesspages');
        $categoryMaps = $tableCategoryMap -> getCategoriesByBusinessId($this->entry->getIdentity());
        $table = Engine_Api::_() -> getDbTable('categories', 'ynbusinesspages');
        foreach($categoryMaps  as $categoryMap) {
            $aCategoryTree = array();
            $category = $table -> getNode($categoryMap -> category_id);
            if ($category) {
                foreach ($category->getBreadCrumNode() as $node) {
                    if ($node -> category_id != 1) {
                        $aCategoryTree[] = $node->shortTitle();
                    }
                }
                $aCategoryTree[] = $category->getTitle();
            }
            $aFullCategory[] = $aCategoryTree;
        }
        $this->data['aFullCategory'] = $aFullCategory;
    }
}