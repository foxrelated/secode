<?php
/**
 * Created by IntelliJ IDEA.
 * User: phuongnv
 * Date: 7/21/15
 * Time: 4:45 PM
 */

class Ynmobile_Service_Ynbusinesspages extends Ynmobile_Service_Base {

    /**
     * main module name.
     *
     * @var string
     */
    protected $module = 'ynbusinesspages';

    /**
     * @main item type
     */
    protected $mainItemType = 'ynbusinesspages_business';

    public function mapSearchFields($aData)
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $maps = array(
            'sSearch' => array(
                'def' => '',
                'key' => 'title'
            ),
            'iCategoryId' => array(
                'def' => 'all',
                'key' => 'category'
            ),
            'sStatus' => array(
                'def' => 'all',
                'key' => 'status'
            ),
            'sStatusClaimed' => array(
                'def' => 'all',
                'key' => 'status_claimed'
            ),
            'sLocationAddress' => array(
                'def' => '',
                'key' => 'location',
            ),
            'sLat'             => array(
                'def' => '',
                'key' => 'lat',
            ),
            'sLong'            => array(
                'def' => '',
                'key' => 'long',
            ),
            'sWithin'          => array(
                'def' => '',
                'key' => 'within',
            ),
            'sOrder'         => array(
                'def' => 'business.business_id',
                'key' => 'order',
            ),
        );

        $result = array();

        foreach ($maps as $k => $opt) {
            if (isset($aData[ $k ])) {
                $result[ $opt['key'] ] = $aData[ $k ];
            } else {
                $result[ $opt['key'] ] = $opt['def'];
            }
        }

        // sView : my, my_claiming, my_favorite, my_following
        $viewerId = $viewer -> getIdentity();
        switch ($aData['sView']) {
            case 'my':
                $result['user_id'] = $viewerId;
                break;
            case 'my_claiming':
                $result['claim'] = 1;
                $result['claimer_id'] = $viewerId;
                break;
            case 'my_favourite':
                $result['favourite'] = 1;
                $result['favouriter_id'] = $viewerId;
                break;
            case 'my_following':
                $result['follow'] = 1;
                $result['follower_id'] = $viewerId;
                break;
            case 'all':
                unset($result['status']);
            default:
        }

        return $result;
    }

    public function fetch($aData)
    {
        extract($aData);

        //         search params
        $searchParams = $this->mapSearchFields($aData);
        //        $searchParams['direction'] = 'DESC';

        $tableBusiness = Engine_Api::_()->getItemTable('ynbusinesspages_business');
        $paginator = $tableBusiness->getBusinessesPaginator($searchParams);


        return Ynmobile_AppMeta::_exports_by_page($paginator, $iPage, $iLimit, $fields = array('listing'));
    }

    public function getCategories(){
        $categoryOptions =  array();

        foreach($this->__getCategoryOptions() as $row){
            $categoryOptions[] = array(
                'id'=>$row['category_id'],
                'title'=>str_repeat("-- ", $row['level'] - 1).$row['title'],
            );
        }

        return $categoryOptions;
    }

    public function form_search() {

        $categoryOptions  = $this->getCategories();

        // remove all cat options
        array_shift($categoryOptions);

        return array(
            'categoryOptions'=>$categoryOptions
        );
    }

    public function fetch_faqs($aData) {

        extract($aData);
        $result = array();

        $table = Engine_Api::_()->getDbTable('faqs', 'ynbusinesspages');
        $select = $table->select()->where("status = 'show'")->order('order ASC');
//        $paginator = Zend_Paginator::factory($select);
//        $paginator->setItemCountPerPage($iLimit);
//        $paginator->setCurrentPageNumber($iPage);
        $paginator=$table->fetchAll($select);

        foreach ($paginator as $item) {
            $result[] = array(
                'iFaqId'=>$item->faq_id,
                'sTitle'=>$item->title,
                'sAnswer'=>$item->answer,
            );
        }

        return $result;
    }

    public function fetch_review($aData){
        extract($aData);

        $iPage =  @$iPage?intval($iPage):1;
        $iLimit = @$iLimit?intval($iLimit): 10;
        $iOffset = @($iPage -1 )* $iLimit;
        $iBusinessId =  intval($iBusinessId);
        $viewer = $this->getViewer();

        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $iBusinessId);

        if (!$business || $business->status == 'deleted') {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _('Business not found.'),
            );
        }

        $table = Engine_Api::_()->getItemTable('ynbusinesspages_review');

//        if(!$viewer || ! $business->getOwner()){
//            return array();
//        }


        $result = array();

        // put my review first
        if($iPage == 1 && $viewer != null){

            $select = $table->select()
                ->where('business_id = ?', $business->getIdentity())
                ->where('user_id = '.$viewer->getIdentity())
            ;
            foreach($table->fetchAll($select) as $row){
                $result[] = Ynmobile_AppMeta::_export_one($row, array('listing'));
            }
        }

        $select = $table->select()
            ->where('business_id = ?', $business->getIdentity())
            ->where('user_id <> '.$viewer->getIdentity())
            ->limit($iLimit, $iOffset)
            ->order('modified_date');

        foreach($table->fetchAll($select) as $row){
            $result[] =  	Ynmobile_AppMeta::_export_one($row, array('listing'));
        }

        return $result;
    }

    public function detail($aData) {

        extract($aData);

        $iBusinessId = intval($iBusinessId);

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        if (!$business || $business->status == 'deleted') {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _('Business not found.'),
            );
        }

        return Ynmobile_AppMeta::_export_one($business, array('infos'));
    }

    public function follow($aData) {

        extract($aData);

        $iBusinessId = intval($iBusinessId);
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        if (!$business) {

            return array(
                'error_code' => 1,
                'message'=>Zend_Registry::get('Zend_Translate') -> _("Business Not Found"),
            );
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $followTable = Engine_Api::_()->getDbTable('follows', 'ynbusinesspages');
        $row = $followTable->getFollowBusiness($business->getIdentity(), $viewer->getIdentity());
        if(!$row)
        {
            $row = $followTable->createRow();
            $row->business_id = $business->getIdentity();
            $row->user_id = $viewer->getIdentity();
            $row->creation_date = date('Y-m-d H:i:s');
            $row -> save();
            $business -> follow_count = $business -> follow_count + 1;
            $business -> save();
        } else {
            return array(
                'error_code' => 1,
                'message'=>Zend_Registry::get('Zend_Translate') -> _("You've already followed this business"),
            );
        }

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("You've successfully followed this business"),
            'aItem'      => Ynmobile_AppMeta::_export_one($business, array('infos'))
        );
    }

    public function unfollow($aData) {

        extract($aData);

        $iBusinessId = intval($iBusinessId);
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        $result = array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("Follow Business"),
        );

        if (!$business) {
            return array(
                'error_code' => 1,
                'message'=>Zend_Registry::get('Zend_Translate') -> _("Business Not Found"),
            );
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $followTable = Engine_Api::_()->getDbTable('follows', 'ynbusinesspages');
        $row = $followTable->getFollowBusiness($business->getIdentity(), $viewer->getIdentity());
        if($row)
        {
            $row -> delete();
            $business -> follow_count = $business -> follow_count - 1;
            $business -> save();
        } else {
            return array(
                'error_code' => 1,
                'message'=>Zend_Registry::get('Zend_Translate') -> _("You haven't followed this business"),
            );
        }

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("You've un-followed this business"),
            'aItem'      => Ynmobile_AppMeta::_export_one($business, array('infos'))
        );
    }

    public function favourite($aData) {

        extract($aData);

        $iBusinessId = intval($iBusinessId);
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        if (!$business) {

            return array(
                'error_code' => 1,
                'message'=>Zend_Registry::get('Zend_Translate') -> _("Business Not Found"),
            );
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $favouriteTable = Engine_Api::_()->getDbTable('favourites', 'ynbusinesspages');
        $row = $favouriteTable->getFavouriteBusiness($business->getIdentity(), $viewer->getIdentity());
        if(!$row)
        {
            $row = $favouriteTable->createRow();
            $row->business_id = $business->getIdentity();
            $row->user_id = $viewer->getIdentity();
            $row -> save();
        } else {
            return array(
                'error_code' => 1,
                'message'=>Zend_Registry::get('Zend_Translate') -> _("You've already favoured this business"),
            );
        }

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("You've successfully favoured this business"),
            'aItem'      => Ynmobile_AppMeta::_export_one($business, array('infos'))
        );
    }

    public function unfavourite($aData) {

        extract($aData);

        $iBusinessId = intval($iBusinessId);
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        if (!$business) {

            return array(
                'error_code' => 1,
                'message'=>Zend_Registry::get('Zend_Translate') -> _("Business Not Found"),
            );
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $favouriteTable = Engine_Api::_()->getDbTable('favourites', 'ynbusinesspages');
        $row = $favouriteTable->getFavouriteBusiness($business->getIdentity(), $viewer->getIdentity());
        if($row)
        {
            $row -> delete();
        } else {
            return array(
                'error_code' => 1,
                'message'=>Zend_Registry::get('Zend_Translate') -> _("You haven't favoured this business"),
            );
        }

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("You've un-favoured this business"),
            'aItem'      => Ynmobile_AppMeta::_export_one($business, array('infos'))
        );
    }

    public function open($aData) {

        extract($aData);
        $iBusinessId = intval($iBusinessId);
        if (!$iBusinessId) {
            return array(
                'error_code' => 1,
                'message'=>Zend_Registry::get('Zend_Translate') -> _("Business not found"),
            );
        } else {
            return $this->_open($iBusinessId, 'published');
        }
    }

    public function close($aData) {

        extract($aData);
        $iBusinessId = intval($iBusinessId);
        if (!$iBusinessId) {
            return array(
                'error_code' => 1,
                'message'=>Zend_Registry::get('Zend_Translate') -> _("Business not found"),
            );
        } else {
            return $this->_open($iBusinessId, 'closed');
        }
    }

    public function _open($iBusinessId, $status) {

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        if(!$business || in_array($business -> status, array('claimed', 'unclaimed', 'deleted')))
        {
            return array(
                'error_code' => 1,
                'message'=>Zend_Registry::get('Zend_Translate') -> _("This Business cannot be changed"),
            );
        }

        $db = $business -> getTable() -> getAdapter();
        $db -> beginTransaction();

        try
        {
            $business -> status = $status;
            $business -> save();
            $db -> commit();
        }
        catch( Exception $e )
        {
            $db -> rollBack();
            throw $e;
        }

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        $messagge = ($status == 'closed')?Zend_Registry::get('Zend_Translate') -> _("Business is successfully closed"):Zend_Registry::get('Zend_Translate') -> _("Business is successfully opened");

        return array(
            'error_code' => 0,
            'message' => $messagge,
            'aItem' => Ynmobile_AppMeta::_export_one($business, array('infos'))
        );
    }

    public function claim($aData) {

        extract($aData);

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);
        $viewer = Engine_Api::_() -> user() -> getViewer();

        $auth =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');

        $canClaim  =  $auth ->setAuthParams('ynbusinesspages_business', null, 'claim')->checkRequire();

        if(!$canClaim){
            return array(
                'error_code'=>1,
                'error_message'=> Zend_Registry::get('Zend_Translate')->_("You can not claim this business"),
            );
        }

        $claimTable = Engine_Api::_() -> getItemTable('ynbusinesspages_claimrequest');

        $db = $claimTable -> getAdapter();
        $db -> beginTransaction();

        try
        {
            $claimRequest = $claimTable -> getClaimRequest($viewer -> getIdentity(), $business -> getIdentity());
            if(empty($claimRequest))
            {
                $claimRequest = $claimTable -> createRow();
                $claimRequest -> business_id = $business -> getIdentity();
                $claimRequest -> user_id = $viewer -> getIdentity();
                $claimRequest -> status = 'pending';
                $claimRequest -> save();
            }
            else
            {
                $claimRequest -> business_id = $business -> getIdentity();
                $claimRequest -> user_id = $viewer -> getIdentity();
                $claimRequest -> status = 'pending';
                $claimRequest -> save();
            }

            //send notice
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $notifyApi -> addNotification($viewer, $business, $business, 'ynbusinesspages_claim_success');

            //send email
            $params['website_name'] = Engine_Api::_()->getApi('settings','core')->getSetting('core.site.title','');
            $params['website_link'] =  'http://'.@$_SERVER['HTTP_HOST'];
            $href =
                'http://'. @$_SERVER['HTTP_HOST'].
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $business -> getIdentity(), 'slug' => $business -> getSlug()),'ynbusinesspages_profile',true);
            $params['business_link'] = $href;
            $params['business_name'] = $business -> getTitle();
            if(!empty($viewer))
            {
                try{
                    Engine_Api::_()->getApi('mail','ynbusinesspages')->send($viewer -> email, 'ynbusinesspages_claim_success',$params);
                }
                catch(exception $e)
                {

                }
            }

            $db -> commit();
        }
        catch( Exception $e )
        {
            $db -> rollBack();
            throw $e;
        }

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        $messagge = Zend_Registry::get('Zend_Translate') -> _("You've successfully claimed this business");

        return array(
            'error_code' => 0,
            'message' => $messagge,
            'aItem' => Ynmobile_AppMeta::_export_one($business, array('infos'))
        );
    }

    public function delete_claim($aData) {

        extract($aData);

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);
        $viewer = Engine_Api::_() -> user() -> getViewer();

        $auth =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');

        $canClaim  =  $auth ->setAuthParams('ynbusinesspages_business', null, 'claim')->checkRequire();

        if(!$canClaim){
            return array(
                'error_code'=>1,
                'error_message'=> Zend_Registry::get('Zend_Translate')->_("You can not claim this business"),
            );
        }

        $claimTable = Engine_Api::_() -> getItemTable('ynbusinesspages_claimrequest');

        $db = $claimTable -> getAdapter();
        $db -> beginTransaction();

        try
        {
            $claimRequest = $claimTable -> getClaimRequest($viewer -> getIdentity(), $business -> getIdentity());
            if(!empty($claimRequest))
            {
                $claimRequest -> delete();
            }

            $db -> commit();
        }
        catch( Exception $e )
        {
            $db -> rollBack();
            throw $e;
        }

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        $messagge = Zend_Registry::get('Zend_Translate') -> _("You've successfully deleted your claiming of this business");

        return array(
            'error_code' => 0,
            'message' => $messagge,
            'aItem' => Ynmobile_AppMeta::_export_one($business, array('infos'))
        );
    }

    public function checkin($aData) {

        extract($aData);
        $iBusinessId = intval($iBusinessId);
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$business) {
            return array(
                'error_code' => 1,
                'message' => Zend_Registry::get('Zend_Translate')->_("Business not found"),
            );
        }

        $db = $business -> getTable() -> getAdapter();
        $db -> beginTransaction();

        try
        {
            $business -> checkin($viewer);
            $db -> commit();
        }
        catch( Exception $e )
        {
            $db -> rollBack();
            throw $e;
        }

        $messagge = Zend_Registry::get('Zend_Translate') -> _("You've successfully checked in this business");

        return array(
            'error_code' => 0,
            'message' => $messagge,
            'aItem' => Ynmobile_AppMeta::_export_one($business, array('infos'))
        );
    }

    public function fetch_followers($aData) {

        extract($aData);
        $iBusinessId = intval($iBusinessId);
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$business) {
            return array(
                'error_code' => 1,
                'message' => Zend_Registry::get('Zend_Translate')->_("Business not found"),
            );
        }

        $followTable = Engine_Api::_() -> getDbTable('follows', 'ynbusinesspages');
        $members = $followTable -> getUsersFollow($iBusinessId);

        if (!count($members))
        {
            return array();
        }
        $aMembers = array();
        foreach ($members as $member)
        {
            $sProfileImage = $member -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
            $sBigProfileImage = $member -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE);
            if ($sProfileImage)
            {
                $sProfileImage = Engine_Api::_() -> ynmobile() ->finalizeUrl($sProfileImage);
                $sBigProfileImage = Engine_Api::_() -> ynmobile() ->finalizeUrl($sBigProfileImage);
            }
            else
            {
                $sProfileImage = NO_USER_ICON;
                $sBigProfileImage = NO_USER_NORMAL;
            }
            $aMembers[] = array(
                'iUserId' => $member -> getIdentity(),
                'sUserName' => $member -> getTitle(),
                'sUserImage' => $sProfileImage,
                'sBigUserImage' => $sBigProfileImage,
            );
        }
        return $aMembers;
    }

    public function fetch_members($aData) {
        extract($aData);

        $iBusinessId = intval($iBusinessId);

        if (!isset($aData['iBusinessId']))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iBusinessId!")
            );
        }

        $iPage  = $iPage?intval($iPage):1;
        $iLimit = $iLimit?intval($iLimit):10;

        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $iBusinessId);

        if (!$business){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists."),
            );
        }

        // Get subject and check auth
        $listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
        $listTblName = $listTbl->info('name');
        $select = $business->membership()->getMembersObjectSelect();

        $select
            -> setIntegrityCheck(false)
            -> join($listTblName, "engine4_ynbusinesspages_membership.list_id = {$listTblName}.list_id", array('role_name' => "{$listTblName}.name"));

        $fields = array(
            'id','title','imgIcon','type','ynbusinesspagesRole',
        );

        $result = Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields);

        return $result;
    }

    public function add_review($aData) {
        extract($aData);

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $iBusinessId =  intval($iBusinessId);

        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $iBusinessId);

        if (!$business){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists."),
            );
        }

        $auth =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');

        if($business -> is_claimed || !$auth -> setAuthParams('ynbusinesspages_business', null, 'rate')->checkRequire()){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You do not have permission to add review to this business."),
            );
        }

        $rated = $business->checkRated();
        if ($rated) {
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You have added a review already.")
            );
        }

        $values  = array(
            'review_title'=> (string)$sTitle,
            'review_body'=>  (string)$sContent,
            'review_rating'=> intval($iRateValue),
        );

        $db = Engine_Api::_()->getDbtable('reviews', 'ynbusinesspages')->getAdapter();

        $db->beginTransaction();
        try {
            $table = Engine_Api::_()->getDbtable('reviews', 'ynbusinesspages');
            $review = $table->createRow();
            $review->business_id = $business->getIdentity();
            $review->user_id = $viewer->getIdentity();
            $review->title = strip_tags($values['review_title']);
            $review->body = strip_tags($values['review_body']);
            $review->rate_number = $values['review_rating'];
            $review->save();

            $business -> review_count += 1;
            $business -> rating  = $business -> getRating();
            $business -> save();

            // Add activity and notification
            $activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
            $action = $activityApi -> addActivity($viewer, $business, 'ynbusinesspages_review_create');
            if ($action) {
                $action -> attach($review);
            }

            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $notifyApi -> addNotification($business->getOwner(), $viewer, $business, 'ynbusinesspages_business_add_review');
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }

        $db->commit();

        return array(
            'error_code' => 0,
            'message' => Zend_Registry::get('Zend_Translate') -> _('Your review has been created.'),
            'aItem' => Ynmobile_AppMeta::_export_one($review, array('infos')),
            'iReviewId' => $review->getIdentity(),
        );
    }

    public function delete_review($aData){
        extract($aData);

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $iReviewId =  intval($iReviewId);

        $review = Engine_Api::_() -> getItem('ynbusinesspages_review', $iReviewId);

        if(!$review){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Review doesn't exists."),
            );
        }

        $business =  Engine_Api::_() -> getItem('ynbusinesspages_business', intval($review->business_id));

        if (!$viewer -> isSelf($review -> getOwner()) && !$review->isDeletable()) {
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this review."),
            );
        }
        if (!$business){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists."),
            );
        }

        $db = $review -> getTable() -> getAdapter();
        $db -> beginTransaction();

        try {
            $review -> delete();
            $business -> review_count -= 1;
            $business -> rating  = $business -> getRating();
            $business -> save();
            $db -> commit();
        }
        catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        $canReview = 0;
        $can_review = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynbusinesspages_business', null, 'rate') -> checkRequire();
        if (!$business->hasReviewed() && $can_review && !($business -> is_claimed)) {
            $canReview = 1;
        }

        return array(
            'error_code' => 0,
            'message' => Zend_Registry::get('Zend_Translate') -> _('This review has been deleted.'),
            'bCanReview' => $canReview
        );
    }

    public function form_edit_review($aData){
        extract($aData);

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $iReviewId =  intval($iReviewId);

        $review = Engine_Api::_() -> getItem('ynbusinesspages_review', $iReviewId);

        if(!$review){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Review doesn't exists."),
            );
        }

        if (!$viewer -> isSelf($review -> getOwner()) && !$review->isEditable()) {
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this review."),
            );
        }

        return array(
            'aItem'=>Ynmobile_AppMeta::_export_one($review, array('infos')),
        );
    }

    public function edit_review($aData){
        extract($aData);

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $iReviewId =  intval($iReviewId);

        $review = Engine_Api::_() -> getItem('ynbusinesspages_review', $iReviewId);

        if(!$review){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Review doesn't exists."),
            );
        }

        $business =  Engine_Api::_() -> getItem('ynbusinesspages_business', intval($review->business_id));

        if (!$viewer -> isSelf($review -> getOwner()) && !$review->isEditable()) {
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this review."),
            );
        }
        if (!$business){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists."),
            );
        }

        $values  = array(
            'review_title'=>  (string)$sTitle,
            'review_body'=>  (string)$sContent,
            'review_rating'=> intval($iRateValue),
        );

        $db = Engine_Api::_()->getDbtable('reviews', 'ynbusinesspages')->getAdapter();
        $db->beginTransaction();

        try {
            $review->title = strip_tags($values['review_title']);
            $review->body = $values['review_body'];
            $review->rate_number = $values['review_rating'];
            $review->save();
        }
        catch( Exception $e ) {
            $db->rollBack();
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Could not complete your request."),
                'error_debug'=> $e->getMessage(),
            );
        }

        $db->commit();

        return array(
            'error_code' => 0,
            'message' => Zend_Registry::get('Zend_Translate') -> _('This review has been edited.'),
            'aItem' => Ynmobile_AppMeta::_export_one($review, array('infos')),
        );
    }

    public function getinvitepeople($aData)
    {
        extract($aData);
        $iBusinessId = intval($iBusinessId);

        if (!isset($aData['iBusinessId']))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iBusinessId!")
            );
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $iBusinessId);

        $friends = $viewer->membership()->getMembers();
        $result = array();
        foreach( $friends as $friend )
        {
            if( $business->membership()->isMember($friend) )
                continue;

            $sProfileImage = $friend -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
            $sBigProfileImage = $friend -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE);
            if ($sProfileImage) {
                $sProfileImage = Engine_Api::_() -> ynmobile() ->finalizeUrl($sProfileImage);
                $sBigProfileImage = Engine_Api::_() -> ynmobile() ->finalizeUrl($sBigProfileImage);
            }        else {
                $sProfileImage = NO_USER_ICON;
                $sBigProfileImage = NO_USER_NORMAL;
            }
            $result[] = array(
                'iUserId' => $friend->getIdentity(),
                'sFullName' => $friend->getTitle(),
                'sUserImageUrl' => $sProfileImage,
                'sBigUserImageUrl' => $sBigProfileImage
            );
        }
        return $result;
    }

    public function invite($aData)
    {

        extract($aData);
        $iBusinessId = intval($iBusinessId);

        if (!isset($iBusinessId))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iBusinessId!")
            );
        }

        $aUserIds = explode(",", $sUserIds);

        if (!is_array($aUserIds) || count($aUserIds) == 0)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("No users have been invited!")
            );
        }
        // Verify business
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        $package = $business -> getPackage();
        if(!$package -> getIdentity() || !$package -> allow_user_invite_friend)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to invite.")
            );
        }

        // Prepare friends
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $friends = Engine_Api::_()->user()->getUserMulti($aUserIds);

        $message = $sMessage;

        $table = $business -> getTable();
        $db = $table -> getAdapter();
        $db -> beginTransaction();

        try
        {
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            foreach ($friends as $friend)
            {
                $business -> membership() -> addMember($friend) -> setResourceApproved($friend);
                $memberList = $business -> getMemberList();
                $row = $business->membership()->getRow($friend);
                $row -> list_id = $memberList->getIdentity();
                $row -> save();

                if (isset($message) && !empty($message))
                {
                    $notifyApi -> addNotification($friend, $viewer, $business, 'ynbusinesspages_invite_message', array('message' => $message));
                }
                else
                {
                    $notifyApi -> addNotification($friend, $viewer, $business, 'ynbusinesspages_invite');
                }
            }

            $db -> commit();
        }
        catch (Exception $e)
        {
            $db -> rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage(),
            );
        }

        //Invite people via email
        $recipients = $aRecipients;
        if (is_array($recipients) && count($recipients) != 0) {

            if (isset($message) && !empty($message)) {
                $sent = $this->InviteViaEmail($recipients, $message, $business, "ynbusinesspages_invite_message");
            } else {
                $sent = $this->InviteViaEmail($recipients, $message, $business, "ynbusinesspages_invite");
            }
        }

        return array(
            'error_code' => 0,
            'result' => 1,
            'message' => Zend_Registry::get('Zend_Translate') -> _("Guests invited successfully!")
        );
    }

    public function InviteViaEmail($recipients, $message = NULL, $object, $type)
    {
        $settings = Engine_Api::_() -> getApi('settings', 'core');
        $user = Engine_Api::_() -> user() -> getViewer();
        // Check recipients
        if (is_string($recipients))
        {
            $recipients = preg_split("/[\s,]+/", $recipients);
        }
        if (is_array($recipients))
        {
            $recipients = array_map('strtolower', array_unique(array_filter(array_map('trim', $recipients))));
        }
        if (!is_array($recipients) || empty($recipients))
        {
            return 0;
        }

        // Only allow a certain number for now
        $max = $settings -> getSetting('invite.max', 10);
        if (count($recipients) > $max)
        {
            $recipients = array_slice($recipients, 0, $max);
        }

        // Check message
        $message = trim($message);
        $emailsSent = 0;
        foreach ($recipients as $recipient)
        {
            try
            {
                $defaultParams = array(
                    'host' => $_SERVER['HTTP_HOST'],
                    'email' => $recipient,
                    'date' => time(),
                    'recipient_title' => "Guest",
                    'sender_title' => $user -> getTitle(),
                    'sender_link' => $user -> getHref(),
                    'object_title' => $object -> getTitle(),
                    'object_link' => $object -> getHref(),
                    'object_photo' => $object -> getPhotoUrl('thumb.icon'),
                    'object_description' => $object -> getDescription(),
                    'message' => $message,
                );
                Engine_Api::_() -> getApi('mail', 'core') -> sendSystem($recipient, 'notify_' . $type, $defaultParams);
            }
            catch (Exception $e)
            {
                // Silence
                if (APPLICATION_ENV == 'development')
                {
                    throw $e;
                }
                continue;
            }
            $emailsSent++;
        }
        return $emailsSent;
    }

    public function join($aData) {
        extract($aData);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $iBusinessId =  intval($iBusinessId);
        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $iBusinessId);
        if (!$business){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists."),
            );
        }
        $package = $business -> getPackage();
        //==============
        if ((!$package -> getIdentity()) || (!$package -> allow_user_join_business)) {
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You cannot join this business."),
            );
        }
        // business require member approval
        if( $business->membership()->isResourceApprovalRequired() ) {
            $row = $business->membership()->getReceiver()
                ->select()
                ->where('resource_id = ?', $business->getIdentity())
                ->where('user_id = ?', $viewer->getIdentity())
                ->query()
                ->fetch(Zend_Db::FETCH_ASSOC, 0);
            ;
            if (empty($row)) {
                return array(
                    'error_code'=>1,
                    'error_message'=>Zend_Registry::get('Zend_Translate') -> _("This business requires member approval. Please submit for one."),
                );
            } else {
                return array(
                    'error_code'=>1,
                    'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You had submit a join request. Please wait for approval."),
                );
            }
        }

        $db = $business->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $membership_status = $business->membership()->getRow($viewer)->active;
            $business->membership()
                ->addMember($viewer)
                ->setUserApproved($viewer)
            ;
            $memberList = $business -> getMemberList();
            $memberList -> add($viewer);
            $row = $business->membership()->getRow($viewer);
            $row -> list_id = $memberList->getIdentity();
            $row -> save();

            // Add activity if membership status was not valid from before
            if (!$membership_status)
            {
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_join');
            }
            $user = $business -> getOwner();
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $business, 'ynbusinesspages_joined');
            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage(),
            );
        }

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        return array(
            'error_code' => 0,
            'message' => Zend_Registry::get('Zend_Translate') -> _("Business joined."),
            'aItem' => Ynmobile_AppMeta::_export_one($business, array('infos'))
        );
    }

    public function leave($aData) {
        extract($aData);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $iBusinessId =  intval($iBusinessId);
        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $iBusinessId);
        if (!$business){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists."),
            );
        }

        if ($business->isOwner($viewer)){
            return array(
                'error_code' => 0,
                'message'=>Zend_Registry::get('Zend_Translate') -> _("You can not leave your own business."),
            );
        }

        $listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
        $db = $business->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $business->membership()->removeMember($viewer);
            $list = $listTbl -> getListByUser($viewer, $business);
            $list -> remove($viewer);
            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage(),
            );
        }

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("Business left."),
            'aItem' => Ynmobile_AppMeta::_export_one($business, array('infos'))
        );
    }

    public function request_invite($aData) {
        extract($aData);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $iBusinessId =  intval($iBusinessId);
        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $iBusinessId);
        if (!$business){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists."),
            );
        }

        $db = $business->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $business->membership()
                ->addMember($viewer)
                ->setUserApproved($viewer)
            ;
            $memberList = $business -> getMemberList();
            $row = $business->membership()->getRow($viewer);
            $row -> list_id = $memberList->getIdentity();
            $row -> save();

            // Add activity if membership status was not valid from before
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notifyApi->addNotification($business->getOwner(), $viewer, $business, 'ynbusinesspages_approve');

            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage(),
            );
        }

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("Your invite request has been sent."),
            'aItem' => Ynmobile_AppMeta::_export_one($business, array('infos'))
        );
    }

    public function cancel_request($aData) {
        extract($aData);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $iBusinessId =  intval($iBusinessId);
        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $iBusinessId);
        if (!$business){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists."),
            );
        }

        $db = $business->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $business->membership()
                ->removeMember($viewer)
            ;
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                ->getNotificationByObjectAndType($business->getOwner(), $business, 'ynbusinesspages_approve');
            if ($notification) {
                $notification->delete();
            }

            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage(),
            );
        }

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("Your invite request has been cancelled."),
            'aItem' => Ynmobile_AppMeta::_export_one($business, array('listing'))
        );
    }

    public function form_add_step1($aData) {
        extract($aData);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $auth = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');
        $canCreate = $auth ->setAuthParams('ynbusinesspages_business', null, 'create')->checkRequire();

        if(!$canCreate){
            return array(
                'error_code'=>1,
                'error_message'=> Zend_Registry::get('Zend_Translate')->_("You don't have permission to create business"),
            );
        }
        //check max businesses user can create
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max = $permissionsTable->getAllowed('ynbusinesspages_business', $viewer->level_id, 'max');
        if ($max == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynbusinesspages_business')
                ->where('name = ?', 'max'));
            if ($row) {
                $max = $row->value;
            }
        }
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_business');
        $select = $table->select()
            -> where('user_id = ?', $viewer->getIdentity())
            -> where('is_claimed <> ?', 1)
            -> where('deleted <> ?', 1);

        $raw_data = $table->fetchAll($select);
        if (($max != 0) && (sizeof($raw_data) >= $max)) {
            return array(
                'error_code'=>1,
                'error_message'=> Zend_Registry::get('Zend_Translate')->_("Your businesses are reach limit. Plese delete some businesses for creating new."),
            );
        }

        $result = array();

        // check if user can create business for claiming
        $isCreator = Engine_Api::_() -> getDbTable('creators', 'ynbusinesspages') -> checkIsCreator($viewer);
        $result['bIsCreator'] = $isCreator?1:0;

        // get package list
        $table = Engine_Api::_() -> getItemTable('ynbusinesspages_package');
        $select = $table -> select() -> where('`show` = 1') -> where('`deleted` = 0') -> where('`current` = 1') -> order('order ASC');
        $packages = $table -> fetchAll($select);
        $aPackages = array();
        foreach ($packages as $package) {
            $features = $package->getAvailableFeatures();
            $modules = $package->getAvailableTitleModules();
            $aPackages[] = array(
                'iPackageId'=>$package->package_id,
                'sTitle'=>$package->title,
                'fPrice'=>$package->price,
                'sCurrency'=>$package->currency,
                'sCurrencySymbol'=>Zend_Registry::get("Zend_View")->locale()->toCurrency($package->price),
                'sDescription'=>$package->description,
                'iValidAmount'=>$package->valid_amount,
                'sValidPeriod'=>$package->valid_period,
                'aFeatures'=>$features,
                'sFeatures'=>implode(', ', $features),
                'aModules'=>$modules,
                'sModules'=>implode(', ',$modules)
            );
        }
        $result['aPackages'] = $aPackages;

        return $result;
    }

    public function form_add($aData) {

        extract($aData);
        $package_id = intval($iPackageId);
        if ($package_id) {

            $package = Engine_Api::_() -> getItem('ynbusinesspages_package', $package_id);
            $aPackageCategory = $package->category_id;
        }
        $categoryOptions = array();

        // get available categories

        $tableCategory = Engine_Api::_() -> getItemTable('ynbusinesspages_category');
        $categories = $tableCategory -> getCategories();
        unset($categories[0]);

        if ($package_id) {
            foreach ($categories as $item) {
//                if ((in_array($item['category_id'], $aPackageCategory)) && ($item['level'] == 1))
                // now get all category as new changes from BA
                if (in_array($item['category_id'], $aPackageCategory))
                    $categoryOptions[] = array(
                        'id' => $item -> category_id,
                        'title' => str_repeat('-', $item['level'] - 1).' '.$item -> title,
                    );
            }
        } else {
            foreach ($categories as $item) {
                $categoryOptions[] = array(
                    'id' => $item -> category_id,
                    'title' => str_repeat('-', $item['level'] - 1).' '.$item -> title,
                );
            }
        }

        return array(
            'categoryOptions'=>$categoryOptions,
        );
    }

    public function form_edit($aData) {


        extract($aData);
        $iBusinessId = intval($iBusinessId);

        if (!isset($aData['iBusinessId']))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iBusinessId!")
            );
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $iBusinessId);

        if (!$business){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists."),
            );
        }
        if(!empty($this->package_id)) {

            $package_id = $business->package_id;
        }
        $categoryOptions = array();

        // get available categories
        $package = $business->getPackage();

        $tableCategory = Engine_Api::_() -> getItemTable('ynbusinesspages_category');
        $categories = $tableCategory -> getCategories();
        unset($categories[0]);
        foreach ($categories as $item) {
            if (in_array($item['category_id'], $package->category_id)) {
                $categoryOptions[] = array(
                    'id' => $item -> category_id,
                    'title' => str_repeat("-", $item['level'] - 1).' '.$item -> title,
                );
            }
        }

        return array(
            'categoryOptions'=>$categoryOptions,
            'aItem'=>Ynmobile_AppMeta::_export_one($business, array('infos')),
        );
    }

    public function add($aData) {
        extract($aData);
        $viewer =  $this->getViewer();

        $auth =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');

        $canCreate  =  $auth ->setAuthParams('ynbusinesspages_business', null, 'create')->checkRequire();

        if(!$canCreate){
            return array(
                'error_code'=>1,
                'error_message'=> Zend_Registry::get('Zend_Translate')->_("You don't have permission to create business"),
            );
        }

        //check max businesses user can create
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max = $permissionsTable->getAllowed('ynbusinesspages_business', $viewer->level_id, 'max');
        if ($max == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynbusinesspages_business')
                ->where('name = ?', 'max'));
            if ($row) {
                $max = $row->value;
            }
        }
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_business');
        $select = $table->select()
            -> where('user_id = ?', $viewer->getIdentity())
            -> where('is_claimed <> ?', 1)
            -> where('deleted <> ?', 1);

        $raw_data = $table->fetchAll($select);
        if (($max != 0) && (sizeof($raw_data) >= $max)) {
            return array(
                'error_code'=>1,
                'error_message'=> Zend_Registry::get('Zend_Translate')->_("Your businesses are reach limit. Please delete some businesses for creating new ones."),
            );
        }

        $values = $this->mapAddBusinessFields($aData);
        $values['user_id'] = $viewer -> getIdentity();
        $values['status'] = 'draft';
        $values['approved'] = false;
        // may implement
        $values['phone'] = explode(',', $aData['sPhone']);
        $values['fax'] = explode(',', $aData['sFax']);
        $values['web_address'] = explode(',', $aData['sWebAddress']);
        $values['theme'] = 'theme1';

        $businessTable = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
        $checkExist = $businessTable -> getBusinessByNameEmail($values['name'], $values['email']);

        if(!empty($checkExist))
        {
            return array(
                'error_code'=>1,
                'error_message'=> Zend_Registry::get('Zend_Translate')->_("Your business name and email are already existing!"),
            );
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db -> beginTransaction();

        //=======================
        try {
            //save business
            $business = $businessTable -> createRow();
            $business->setFromArray($values);
            $business -> save();

            // Set Location
            $tableLocation = Engine_Api::_() -> getDbTable('locations', 'ynbusinesspages');
            $location_title = $aData['sLocationAddress'];
            $location = $aData['sLocationAddress'];
            $latitude = $aData['sLat'];
            $longitude = $aData['sLong'];
            if(!empty($location) && !empty($latitude) && !empty($longitude))
            {
                $locationRow = $tableLocation -> createRow();
                $locationRow -> business_id = $business -> getIdentity();
                $locationRow -> title = $location_title;
                $locationRow -> location = $location;
                $locationRow -> latitude = $latitude;
                $locationRow -> longitude = $longitude;
                $locationRow -> main = true;
                $locationRow -> save();
            }

            //insert category to mapping table
            if (!empty($values['category_id'])) {
                $tableCategoryMap = Engine_Api::_() -> getDbTable('categorymaps', 'ynbusinesspages');
                $checkCategory = $tableCategoryMap -> checkExistCategoryByBusiness($values['category_id'], $business -> getIdentity());
                if (empty($checkCategory)) {
                    $rowCategoryMap = $tableCategoryMap -> createRow();
                    $rowCategoryMap -> business_id = $business -> getIdentity();
                    $rowCategoryMap -> category_id = $values['category_id'];
                    $rowCategoryMap -> main = true;
                    $rowCategoryMap -> save();
                }
            }

            if (!empty($values['photo'])) {
                if ($file = Engine_Api::_()->ynmobile()->saveUploadPhotoAsDataUrlToFile($values['photo'])) {
                    $business->setPhoto($file);
                }
            }

            //set auth
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
            $auth_arr = array('video', 'view', 'comment');
            foreach ($auth_arr as $elem) {
                $auth_role = 'everyone';
                if ($auth_role) {
                    $roleMax = array_search($auth_role, $roles);
                    foreach ($roles as $i=>$role) {
                        $auth->setAllowed($business, $role, $elem, ($i <= $roleMax));
                    }
                }
            }
            //send email
            $params['website_name'] = Engine_Api::_()->getApi('settings','core')->getSetting('core.site.title','');
            $params['website_link'] =  'http://'.@$_SERVER['HTTP_HOST'];
            $href =
                'http://'. @$_SERVER['HTTP_HOST'].
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $business -> getIdentity(), 'slug' => $business -> getSlug()),'ynbusinesspages_profile',true);
            $params['business_link'] = $href;
            $params['business_name'] = $business -> getTitle();
            try{
                Engine_Api::_()->getApi('mail','ynbusinesspages')->send($viewer -> email, 'ynbusinesspages_business_created',$params);
            }
            catch(exception $e){

            }
            /**
             * Insert 2 default roles: ADMIN and MEMBER
             */
            $business -> insertSampleList();
            // Commit

            if (Engine_Api::_() -> hasModuleBootstrap("yncredit"))
            {
                $user = $business -> getOwner();
                if($user -> getIdentity())
                    Engine_Api::_()->yncredit()-> hookCustomEarnCredits($user, $user -> getTitle(), 'ynbusinesspages_new', $user);
            }

            $db -> commit();

        } catch (Exception $e) {
            $db -> rollBack();
            return array(
                'error_code'=>1,
                'error_message'=>$e->getMessage()
            );
        }

        return array(
            'error_code'=>0,
            'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business created successfully."),
            'iBusinessId'=>$business->getIdentity(),
        );
    }

    public function add_claim($aData) {
        extract($aData);
        $viewer =  $this->getViewer();

        $auth =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');

        $canCreate  =  $auth ->setAuthParams('ynbusinesspages_business', null, 'create')->checkRequire();

        if(!$canCreate){
            return array(
                'error_code'=>1,
                'error_message'=> Zend_Registry::get('Zend_Translate')->_("You don't have permission to create business"),
            );
        }

        $values = $this->mapAddBusinessFields($aData);

        //status
        $values['status'] = 'unclaimed';
        $superAdmins = Engine_Api::_() -> user() -> getSuperAdmins();
        foreach($superAdmins as $superAdmin)
        {
            $values['user_id'] = $superAdmin -> getIdentity();
            break;
        }
        $values['is_claimed'] = true;
        // may implement
        $values['size'] = 1;
        $values['phone'] = explode(',', $aData['sPhone']);
        $values['fax'] = explode(',', $aData['sFax']);
        $values['web_address'] = explode(',', $aData['sWebAddress']);
        $values['theme'] = 'theme1';


        $db = Engine_Db_Table::getDefaultAdapter();
        $db -> beginTransaction();

        //=======================
        try {
            //save business
            $businessTable = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
            $business = $businessTable -> createRow();
            $business->setFromArray($values);
            $business -> save();

            //insert category to mapping table
            if (!empty($values['category_id'])) {
                $tableCategoryMap = Engine_Api::_() -> getDbTable('categorymaps', 'ynbusinesspages');
                $checkCategory = $tableCategoryMap -> checkExistCategoryByBusiness($values['category_id'], $business -> getIdentity());
                if (empty($checkCategory)) {
                    $rowCategoryMap = $tableCategoryMap -> createRow();
                    $rowCategoryMap -> business_id = $business -> getIdentity();
                    $rowCategoryMap -> category_id = $values['category_id'];
                    $rowCategoryMap -> main = true;
                    $rowCategoryMap -> save();
                }
            }

            //set auth
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
            $auth_arr = array('video', 'view', 'comment');
            foreach ($auth_arr as $elem) {
                $auth_role = 'everyone';
                if ($auth_role) {
                    $roleMax = array_search($auth_role, $roles);
                    foreach ($roles as $i=>$role) {
                        $auth->setAllowed($business, $role, $elem, ($i <= $roleMax));
                    }
                }
            }
            //send email
            $params['website_name'] = Engine_Api::_()->getApi('settings','core')->getSetting('core.site.title','');
            $params['website_link'] =  'http://'.@$_SERVER['HTTP_HOST'];
            $href =
                'http://'. @$_SERVER['HTTP_HOST'].
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $business -> getIdentity(), 'slug' => $business -> getSlug()),'ynbusinesspages_profile',true);
            $params['business_link'] = $href;
            $params['business_name'] = $business -> getTitle();
            try{
                Engine_Api::_()->getApi('mail','ynbusinesspages')->send($viewer -> email, 'ynbusinesspages_business_created',$params);
            }
            catch(exception $e){

            }
            /**
             * Insert 2 default roles: ADMIN and MEMBER
             */
            $business -> insertSampleList();
            // Commit

            $db -> commit();

        } catch (Exception $e) {
            $db -> rollBack();
            return array(
                'error_code'=>1,
                'error_message'=>$e->getMessage()
            );
        }

        return array(
            'error_code'=>0,
            'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business created successfully."),
            'iBusimessId'=>$business->getIdentity(),
        );
    }

    public function mapAddBusinessFields($aData) {
        $keys =  array(
            'iPackageId'=>'package_id',
            'iCategoryId'=>'category_id',
            'sShortDesc'=>'short_description',
            'sDescription'=>'description',
            'sTitle'=> 'name',
            'bIsSearch' => 'search',
            'bIsRequireApproval' => 'approval',
            'iSize' => 'size',
            'sEmail'=> 'email',
            'sWebAddress'=>'web_address',
            'sFacebook'=>'facebook_link',
            'sTwitter'=>'twitter_link',
            'sProfilePhoto'=>'photo',
        );

        $values = array();
        foreach($keys as $from=>$to){
            if(isset($aData[$from])){
                $values[$to] =  $aData[$from];
            }else{
                $values[$to] = "";
            }
        }

        $values['description'] =  html_entity_decode($values['description']);
        $values['short_description'] =  html_entity_decode($values['short_description']);

        return $values;
    }

    public function edit($aData) {
        extract($aData);

        $auth =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');

        $viewer = Engine_Api::_() -> user() -> getViewer();

        // Check authorization to edit.
        if (!$auth -> setAuthParams('ynbusinesspages_business', null, 'edit')){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You do not have permission to edit this business."),
            );
        }

        $business_id = $iBusinessId =  intval($iBusinessId);

        $values =  $this->mapAddBusinessFields($aData);

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        $package = $business -> getPackage();

        //get main
        $tableCategory = Engine_Api::_() -> getItemTable('ynbusinesspages_category');
        $tableCategoryMap = Engine_Api::_() -> getDbTable('categorymaps', 'ynbusinesspages');
        $main_category = $tableCategoryMap -> getMainCategoryByBusinessId($business_id);
        $category_id = $values['category_id'];
        $category = Engine_Api::_() -> getItem('ynbusinesspages_category', $category_id);

        // get location
        $businessTable = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
        $checkExist = $businessTable -> getBusinessByNameEmail($values['name'], $values['email']);

        //check when changing email or name
        if(($values['name'] != $business -> name) || ($values['email'] != $business -> email))
        {
            if(!empty($checkExist))
            {
                return array(
                    'error_code'=>1,
                    'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Your business name and email are already existing!"),
                );
            }
        }

        // get phone, fax, web address
        $values['phone'] = explode(',', $aData['sPhone']);
        $values['fax'] = explode(',', $aData['sFax']);
        $values['web_address'] = explode(',', $aData['sWebAddress']);

        $db = Engine_Db_Table::getDefaultAdapter();
        $db -> beginTransaction();

        try {
            //save business
            $business->setFromArray($values);
            $business -> save();

            // Set Location
            $tableLocation = Engine_Api::_() -> getDbTable('locations', 'ynbusinesspages');
            //delete all before insert
            $tableLocation -> deleteAllLocationsByBusinessId($business -> getIdentity());
            //for location in form
            $location_title = $aData['sLocationAddress'];
            $location = $aData['sLocationAddress'];
            $latitude = $aData['sLat'];
            $longitude = $aData['sLong'];
            if(!empty($location) && !empty($latitude) && !empty($longitude))
            {
                $locationRow = $tableLocation -> createRow();
                $locationRow -> business_id = $business -> getIdentity();
                $locationRow -> title = $location_title;
                $locationRow -> location = $location;
                $locationRow -> latitude = $latitude;
                $locationRow -> longitude = $longitude;
                $locationRow -> main = true;
                $locationRow -> save();
            }

            if (!empty($values['photo'])) {
                if ($file = Engine_Api::_()->ynmobile()->saveUploadPhotoAsDataUrlToFile($values['photo'])) {
                    $business->setPhoto($file);
                }
            }

            //insert category to mapping table
            if (!empty($values['category_id'])) {
                $tableCategoryMap = Engine_Api::_() -> getDbTable('categorymaps', 'ynbusinesspages');
                //delete all before insert
                $tableCategoryMap -> deleteCategoriesByBusinessId($business -> getIdentity());
                $checkCategory = $tableCategoryMap -> checkExistCategoryByBusiness($values['category_id'], $business -> getIdentity());
                if (empty($checkCategory)) {
                    $rowCategoryMap = $tableCategoryMap -> createRow();
                    $rowCategoryMap -> business_id = $business -> getIdentity();
                    $rowCategoryMap -> category_id = $values['category_id'];
                    $rowCategoryMap -> main = true;
                    $rowCategoryMap -> save();
                }
            }

            // Commit
            $db -> commit();

            //send notice to followers
            $tableFollow = Engine_Api::_() -> getDbTable('follows', 'ynbusinesspages');
            $followers = $tableFollow -> getUsersFollow($business -> getIdentity());
            if($followers)
            {
                foreach($followers as $follower)
                {
                    Engine_Api::_() -> getDbTable('notifications', 'activity') -> addNotification($follower, $business, $business, 'ynbusinesspages_edited');
                }
            }

        } catch (Exception $e) {
            $db -> rollBack();
            return array(
                'error_code'=>1,
                'error_message'=>$e->getMessage()
            );
        }

        return array(
            'error_code'=>0,
            'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business edited successfully."),
            'iBusimessId'=>$business->getIdentity(),
        );
    }

    public function accept_invite($aData) {
        extract($aData);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $iBusinessId =  intval($iBusinessId);
        $subject = $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $iBusinessId);
        if (!$business){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists."),
            );
        }

        $db = $business->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $membership_status = $subject->membership()->getRow($viewer)->active;
            $subject->membership()
                ->setUserApproved($viewer);

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                $viewer, $subject, 'ynbusinesspages_invite');
            if( $notification )
            {
                $notification->mitigated = true;
                $notification->save();
            }

            // Add activity
            if (!$membership_status){
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $action = $activityApi->addActivity($viewer, $subject, 'ynbusinesspages_join');
            }
            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage(),
            );
        }

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("You have accepted the invite to this business."),
            'aItem' => Ynmobile_AppMeta::_export_one($business, array('infos'))
        );
    }

    public function ignore_invite($aData) {
        extract($aData);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $iBusinessId =  intval($iBusinessId);
        $subject = $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $iBusinessId);
        if (!$business){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists."),
            );
        }

        $db = $business->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $subject->membership()->removeMember($viewer);

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                $viewer, $subject, 'ynbusinesspages_invite');
            if( $notification )
            {
                $notification->mitigated = true;
                $notification->save();
            }

            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage(),
            );
        }

        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("You have ignored the invite to the business."),
            'aItem' => Ynmobile_AppMeta::_export_one($business, array('infos'))
        );
    }

    public function delete($aData) {
        extract($aData);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $iBusinessId =  intval($iBusinessId);
        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $iBusinessId);
        if (!$business || !$business->isDeletable()) {
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Business cannot be deleted."),
            );
        }

        $db = $business -> getTable() -> getAdapter();
        $db -> beginTransaction();

        try
        {
            $business -> delete();
            $db -> commit();
        }
        catch( Exception $e )
        {
            $db -> rollBack();
            return array(
                'error_code'=>1,
                'error_message' => $e->getMessage(),
            );
        }

        return array(
            'error_code'=>0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("Business has been deleted."),
        );
    }

    /**
     * @param $aData
     * @return array
     *
     * upload single cover photo
     */
    public function upload_cover($aData) {
        extract($aData);

        if ( !isset($iBusinessId) || empty($iBusinessId) )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing iBusinessId.")
            );
        }
        $iBusinessId = intval($iBusinessId);
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);
        if( !$business )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Business doesn't exist!")
            );
        }

        // check to see if upload file is exist
        if( !isset($_FILES['image']) )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("No files uploaded.")
            );
        }

        $table = Engine_Api::_() -> getItemTable('ynbusinesspages_photo');
        $coverTbl = Engine_Api::_()->getDbTable('covers', 'ynbusinesspages');
        $db = $table -> getAdapter();
        $db -> beginTransaction();

        try
        {
            // create new cover item
            $photo = $coverTbl -> createRow();
            $photo -> setFromArray(array(
                'business_id' => $iBusinessId,
                // set oder of this cover photo
                'order' => (int)($coverTbl -> getMaxOrderByBusiness($iBusinessId)) + 1
            ));
            $photo -> save();
            // call set photo from dbtable ynbusinesspages covers
            $photo -> setPhoto($_FILES['image']);
            $db -> commit();
        }

        catch( Exception $e )
        {
            $db -> rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }
    }

    public function upload_photo($aData) {
        extract($aData);

        if ( !isset($iBusinessId) || empty($iBusinessId) )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing iBusinessId.")
            );
        }
        $iBusinessId = intval($iBusinessId);
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);
        if( !$business )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Business doesn't exist!")
            );
        }

        // check to see if upload file is exist
        if( !isset($_FILES['image']) )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("No files uploaded.")
            );
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $album = $business->getSingletonAlbum();

        $photoTable = Engine_Api::_() -> getItemTable('ynbusinesspages_photo');
        $db = $photoTable -> getAdapter();
        $db -> beginTransaction();

        try
        {
            $params = array(
                'business_id' => $business -> getIdentity(),
                'user_id' => $viewer -> getIdentity(),
                'collection_id' => $album -> getIdentity(),
                'album_id' => $album -> getIdentity()
            );
            // create new cover item
            $photo = $photoTable -> createRow();
            $photo -> setFromArray($params);
            $photo -> save();
            // call set photo from dbtable ynbusinesspages covers
            $photo -> setPhoto($_FILES['image']);
            $db -> commit();
            return array(
                'result' => 1,
                'message' => Zend_Registry::get('Zend_Translate') -> _("Photo successfully uploaded."),
                'iPhotoId' => $photo -> getIdentity(),
                'sPhotoTitle' => $photo -> getTitle(),
                'sType' => $photo->getType(),
            );
        }

        catch( Exception $e )
        {
            $db -> rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }
    }

    /**
     * @param $aData = array(
     *      iBusinessId
     *      aCoverIds
     * )
     *
     * @return array
     */
    public function delete_cover($aData) {

        extract($aData);

        if (!isset($aCoverIds)) {
            return array(
                'error_code' => 0,
                'message' => ''
            );
        }

        if ( !isset($iBusinessId) || empty($iBusinessId) )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing iBusinessId.")
            );
        }
        $iBusinessId = intval($iBusinessId);
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);
        if( !$business )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Business doesn't exist!")
            );
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $coverTbl = Engine_Api::_()->getDbTable('covers', 'ynbusinesspages');
        $db = $coverTbl -> getAdapter();
        $db->beginTransaction();

        try {

            foreach ($aCoverIds as $coverId) {
                $cover = Engine_Api::_()->getItem('ynbusinesspages_cover', $coverId);
                if (!$cover) {
                    continue;
                } else {
                    $cover->delete();
                }
            }
            $db->commit();
        }
        catch ( Exception $e ) {
            $db -> rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }

        return array(
            'error_code' => 0,
            'message' => ''
        );
    }

    public function discussions($aData)
    {
        extract($aData);

        $iBusinessId = intval($iBusinessId);

        $viewer = Engine_Api::_()->user()->getViewer();
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);

        if (!$business){
            return array();
        }

        // Get paginator
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_topic');

        $select = $table->select()
            ->where('business_id = ?', $iBusinessId)
            ->order('sticky DESC')
            ->order('modified_date DESC');

        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, array('listing'));
    }

    public function create_topic($aData)
    {
        extract($aData);

        $iBusinessId =  intval($iBusinessId);

        $table = Engine_Api::_()->getItemTable('ynbusinesspages_business');
        $business = $table->findRow($iBusinessId);

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!isset($sTitle) || empty($sTitle)){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing topic title!")
            );
        }

        if (!isset($sBody) || empty($sBody)){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing topic body!")
            );
        }

        // Process
        $values = array(
            'title' => $sTitle,
            'body' => $sBody,
            'watch' => (isset($iWatch) && ($iWatch == '0' || $iWatch == '1')) ? $iWatch : 1,
        );

        $values['user_id'] = $viewer->getIdentity();
        $values['business_id'] = $business->getIdentity();

        $topicTable = Engine_Api::_()->getItemTable('ynbusinesspages_topic');
        $topicWatchesTable = $this->getWorkingTable('topicWatches','ynbusinesspages');
        $postTable = $this->getWorkingTable('posts','ynbusinesspages');


        $db = $business->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            // Create topic
            $topic = $topicTable->createRow();
            $topic->setFromArray($values);
            $topic->save();

            // Create post
            $values['topic_id'] = $topic->topic_id;

            $post = $postTable->createRow();
            $post->setFromArray($values);
            $post->save();

            // Create topic watch
            $topicWatchesTable->insert(array(
                'resource_id' => $business->getIdentity(),
                'topic_id' => $topic->getIdentity(),
                'user_id' => $viewer->getIdentity(),
                'watch' => (bool) $values['watch'],
            ));

            // Add activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($viewer, $business, $this->getActivityType('ynbusinesspages_topic_create'), null, array('child_id' => $topic->getIdentity()));
            if( $action ) {
                $action->attach($topic, Activity_Model_Action::ATTACH_DESCRIPTION);
            }

            $db->commit();
            return array(
                'error_code' => 0,
                'error_message' => '',
                'message' => Zend_Registry::get("Zend_Translate")->_("Created topic successfully!"),
                'iTopicId' => $topic->getIdentity()
            );
        }

        catch( Exception $e )
        {
            $db->rollBack();
            return array(
                'error_code' => 3,
                'error_message' => $e->getMessage(),
            );
        }
    }

    public function topic_info($aData)
    {
        extract($aData);

        $iTopicId = intval($iTopicId);

        $topic = Engine_Api::_()->getItem('ynbusinesspages_topic', $iTopicId);

        if (!$topic){
            return array(
                'error_code' => 0,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing Topic identity!")
            );
        }

        return Ynmobile_AppMeta::_export_one($topic, array('infos'));
    }

    public function view_topic($aData)
    {
        extract($aData);

        $viewer = Engine_Api::_()->user()->getViewer();

        $table = Engine_Api::_()->getItemTable('ynbusinesspages_post');

        $select = $table->select()
            ->where('topic_id = ?', $iTopicId)
            ->order('creation_date ASC');

        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, array('listing'));
    }

    public function post_info($aData)
    {
        extract($aData);

        $iPostId =  intval($iPostId);

        $post  =  $this->getWorkingItem('ynbusinesspages_post',$iPostId);

        if (!$post){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPostId!")
            );
        }

        return Ynmobile_AppMeta::_export_one($post, array('infos'));
    }

    public function post_reply($aData)
    {
        extract($aData);

        $iTopicId  = intval($iTopicId);

        $topic = Engine_Api::_()->getItem('ynbusinesspages_topic', $iTopicId);

        if (!$topic){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
            );
        }

        if (!isset($sBody)){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("sBody is required and can't be empty")
            );
        }

        $business = $topic->getParentBusiness();

        if( $topic->closed ) {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("This has been closed for posting.")
            );
        }

        if( isset($iQuoteId) && !empty($iQuoteId) ) {
            $quote = $this->getWorkingItem('ynbusinesspages_post', $iQuoteId);

            if($quote->user_id == 0) {
                $owner_name = Zend_Registry::get('Zend_Translate')->_('Deleted Member');
            } else {
                $owner_name = $quote->getOwner()->__toString();
            }
            $sBody = "[blockquote][b]" . "[i]{$owner_name}[/i] said:" . "[/b]\r\n" . htmlspecialchars_decode($quote->body, ENT_COMPAT) . "[/blockquote]\r\n" . $sBody;

        }

        if ($sBody == ''){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Post content is invalid!")
            );
        }

        // Process
        $viewer = Engine_Api::_()->user()->getViewer();
        $topicOwner = $topic->getOwner();

        $postTable = $this->getWorkingTable('posts','ynbusinesspages');
        $topicWatchesTable = $this->getWorkingTable('topicWatches','ynbusinesspages');

        $userTable = Engine_Api::_()->getItemTable('user');
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

        $values['body'] = html_entity_decode($sBody, ENT_QUOTES, 'UTF-8');
        $values['user_id'] = $viewer->getIdentity();
        $values['business_id'] = $business->getIdentity();
        $values['topic_id'] = $topic->getIdentity();
        $values['watch'] =  (isset($iWatch) && $iWatch == '1') ? 1 : 0;

        $watch = (bool) $values['watch'];
        $isWatching = $topicWatchesTable
            ->select()
            ->from($topicWatchesTable->info('name'), 'watch')
            ->where('resource_id = ?', $business->getIdentity())
            ->where('topic_id = ?', $topic->getIdentity())
            ->where('user_id = ?', $viewer->getIdentity())
            ->limit(1)
            ->query()
            ->fetchColumn(0)
        ;

        $db = $business->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            // Create post
            $post = $postTable->createRow();
            $post->setFromArray($values);
            $post->save();

            // Watch
            if( false === $isWatching ) {
                $topicWatchesTable->insert(array(
                    'resource_id' => $business->getIdentity(),
                    'topic_id' => $topic->getIdentity(),
                    'user_id' => $viewer->getIdentity(),
                    'watch' => (bool) $watch,
                ));
            } else if( $watch != $isWatching ) {
                $topicWatchesTable->update(array(
                    'watch' => (bool) $watch,
                ), array(
                    'resource_id = ?' => $business->getIdentity(),
                    'topic_id = ?' => $topic->getIdentity(),
                    'user_id = ?' => $viewer->getIdentity(),
                ));
            }

            // Activity
            $action = $activityApi->addActivity($viewer, $business, $this->getActivityType('ynbusinesspages_topic_reply'), null, array('child_id' => $topic->getIdentity()));
            if( $action )
            {
                $action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
            }


            // Notifications
            $notifyUserIds = $topicWatchesTable->select()
                ->from($topicWatchesTable->info('name'), 'user_id')
                ->where('resource_id = ?', $business->getIdentity())
                ->where('topic_id = ?', $topic->getIdentity())
                ->where('watch = ?', 1)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN)
            ;

            $view = Zend_Registry::get("Zend_View");

            foreach( $userTable->find($notifyUserIds) as $notifyUser )
            {
                if( $notifyUser->isSelf($viewer) )
                {
                    continue;
                }
                if( $notifyUser->isSelf($topicOwner) )
                {
                    $type = 'ynbusinesspages_discussion_response';
                } else
                {
                    $type = 'ynbusinesspages_discussion_reply';
                }
                $notifyApi->addNotification($notifyUser, $viewer, $topic, $this->getActivityType($type), array(
                    'message' => $view->BBCode($post->body),
                ));
            }

            $db->commit();
            return array(
                'error_code' => 0,
                'error_message' => '',
                'message' => Zend_Registry::get('Zend_Translate') -> _("Posted reply successfully!"),
                'iPostId' => $post->getIdentity(),
                'iTopicId' => $iTopicId,
            );
        }

        catch( Exception $e )
        {
            $db->rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }
    }

    public function topic_watch($aData)
    {
        extract($aData);

        $iTopicId = intval($iTopicId);

        $topic = $this->getWorkingItem('ynbusinesspages_topic', $iTopicId);

        if (!$topic){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
            );
        }

        if (!isset($iWatch)){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iWatch!")
            );
        }

        $business = $topic->getParentBusiness();

        $viewer = Engine_Api::_()->user()->getViewer();

        $watch = intval($iWatch);

        $topicWatchesTable = $this->getWorkingTable('topicWatches','ynbusinesspages');

        $db = $topicWatchesTable->getAdapter();
        $db->beginTransaction();

        try
        {
            $isWatching = $topicWatchesTable
                ->select()
                ->from($topicWatchesTable->info('name'), 'watch')
                ->where('resource_id = ?', $business->getIdentity())
                ->where('topic_id = ?', $topic->getIdentity())
                ->where('user_id = ?', $viewer->getIdentity())
                ->limit(1)
                ->query()
                ->fetchColumn(0)
            ;

            if( false === $isWatching ) {
                $topicWatchesTable->insert(array(
                    'resource_id' => $business->getIdentity(),
                    'topic_id' => $topic->getIdentity(),
                    'user_id' => $viewer->getIdentity(),
                    'watch' => (bool) $watch,
                ));
            } else if( $watch != $isWatching ) {
                $topicWatchesTable->update(array(
                    'watch' => (bool) $watch,
                ), array(
                    'resource_id = ?' => $business->getIdentity(),
                    'topic_id = ?' => $topic->getIdentity(),
                    'user_id = ?' => $viewer->getIdentity(),
                ));
            }

            $db->commit();
            return array(
                'error_code' => 0,
                'message' => ($watch)
                    ? Zend_Registry::get('Zend_Translate')->_("Set watching successfully")
                    : Zend_Registry::get('Zend_Translate')->_("Unset watching successfully")
            );
        }

        catch( Exception $e )
        {
            $db->rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }

    }

    public function topic_delete($aData)
    {
        extract($aData);
        $iTopicId = intval($iTopicId);

        $topic = $this->getWorkingItem('ynbusinesspages_topic', $iTopicId);
        if (!$topic){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
            );
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        try{
            $topic->delete();
            return array(
                'error_code' => 0,
                'error_message' => Zend_Registry::get('Zend_Translate')->_("Deleted topic successfully")
            );
        }

        catch( Exception $e ){
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }

    }

    public function edit_post($aData)
    {
        extract($aData);

        $iPostId = intval($iPostId);

        $post = $this->getWorkingItem('ynbusinesspages_post', $iPostId);

        if (!$post){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPostId!")
            );
        }

        if (!isset($sBody) || $sBody == ""){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("sBody is required and can't be empty")
            );
        }



        $business = $post->getParentBusiness();
        $viewer = Engine_Api::_()->user()->getViewer();

        if( !$business->isOwner($viewer) && !$post->isOwner($viewer) && !$business->isAllowed('discussion_delete', null, $post) )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this post")
            );
        }

        // Process
        $table = $post->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try
        {
            $post->modified_date = date('Y-m-d H:i:s');
            $post->body = html_entity_decode($sBody, ENT_QUOTES, 'UTF-8');
            $post->save();

            $db->commit();
            return array(
                'error_code' => 0,
                'error_message' => '',
                'message' => Zend_Registry::get('Zend_Translate') -> _("Edited post successfully!"),
                'iPostId' => $post->getIdentity(),
            );
        }

        catch( Exception $e )
        {
            $db->rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }
    }

    public function delete_post($aData)
    {
        extract($aData);

        $iPostId = intval($iPostId);

        $post = $this->getWorkingItem('ynbusinesspages_post', $iPostId);

        if (!$post){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPostId!")
            );
        }

        $business = $post->getParentBusiness();
        $viewer = Engine_Api::_()->user()->getViewer();

        if( !$business->isOwner($viewer) && !$post->isOwner($viewer) && !$business->isAllowed('discussion_delete', null, $post) ){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this post")
            );
        }

        // Process
        $table = $post->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        $topic_id = $post->topic_id;

        try
        {
            $post->delete();
            $db->commit();
            return array(
                'error_code' => 0,
                'error_message' => '',
                'message' => Zend_Registry::get('Zend_Translate') -> _("Deleted post successfully!"),
                'iTopicId' => $topic_id,
            );
        }

        catch( Exception $e )
        {
            $db->rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }
    }

    public function fetch_events($aData) {

        extract($aData);
        $iBusinessId = intval($iBusinessId);
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$business) {
            return array(
                'error_code' => 1,
                'message' => Zend_Registry::get('Zend_Translate')->_("Business not found"),
            );
        }

        $select = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages') -> getEventsPaginator(array('business_id' => $iBusinessId));
        if(!$select) return array();

        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields = array('listing'));
    }

    public function fetch_playlists($aData) {

        extract($aData);
        $iBusinessId = intval($iBusinessId);
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $iBusinessId);
        if (!$business) {
            return array(
                'error_code' => 1,
                'message' => Zend_Registry::get('Zend_Translate')->_("Business not found"),
            );
        }

        $params = array();
        $params['business_id'] = $iBusinessId;
        $params['order'] = 'recent';
        if (Engine_Api::_()->hasModuleBootstrap('mp3music')) {
            $params['ItemTable'] = 'mp3music_album';
        } else {
            $params['ItemTable'] = 'music_playlist';
        }

        $select = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getAlbumsPaginator($params);
        if(!$select) return array();

        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields = array('listing'));
    }

    public function fetch_video($aData){
        extract($aData);

        // Get paginator
        $tableMapping = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages');
        $select = $tableMapping -> getVideosPaginator(array(
            'business_id'=>intval($iBusinessId)
        ));

        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, array('listing'));
    }

    function __getCategoryOptions(){
        return Engine_Api::_() -> getDbTable('categories', 'ynbusinesspages')->getCategories();
    }
}