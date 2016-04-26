<?php
/**
 * Created by IntelliJ IDEA.
 * User: macpro
 * Date: 8/18/15
 * Time: 9:39 AM
 */

class Ynmobile_Service_Ynresume extends Ynmobile_Service_Base {

    /**
     * main module name.
     *
     * @var string
     */
    protected $module = 'ynresume';

    /**
     * @main item type
     */
    protected $mainItemType = 'ynresume_resume';

    /**
     * @param $aData
     * @return array
     *  This function map search criteria from mobile api to ynresume fetch interface
     */
    public function mapSearchFields($aData)
    {

        if (empty($aData['iIndustryId']) || $aData['iIndustryId'] == 'null') {
            $aData['iIndustryId'] = 'all';
        }

        $maps = array(
            'sSearch' => array(
                'def' => '',
                'key' => 'title'
            ),
            'sHeadline' => array(
                'def' => '',
                'key' => 'headline'
            ),
            'iIndustryId' => array(
                'def' => 'all',
                'key' => 'industry_id'
            ),
            'sLat' => array(
                'def' => '',
                'key' => 'lat',
            ),
            'sLong' => array(
                'def' => '',
                'key' => 'long',
            ),
            'sWithin' => array(
                'def' => '',
                'key' => 'within',
            ),
            'sJobTitle' => array(
                'def' => '',
                'key' => 'job_title',
            ),
            'sCompany' => array(
                'def' => '',
                'key' => 'company',
            ),
            'sSchool' => array(
                'def' => '',
                'key' => 'school',
            ),
//            'sOrder'         => array(
//                'def' => 'resume.resume_id',
//                'key' => 'order',
//            ),
        );

        $result = array();

        foreach ($maps as $k => $opt) {
            if (isset($aData[ $k ])) {
                $result[ $opt['key'] ] = $aData[ $k ];
            } else {
                $result[ $opt['key'] ] = $opt['def'];
            }
        }

//         sView : favourite, my_claiming, my_favorite, my_following
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $viewerId = $viewer -> getIdentity();

        switch ($aData['sView']) {
            case 'favourite':
                $result['favourite'] = 1;
                $result['favouriter_id'] = $viewerId;
                break;
            case 'save':
                $result['save'] = 1;
                $result['saver_id'] = $viewerId;
                break;
            default:
        }

        return $result;
    }

    /**
     * @param $aData
     * @return array
     */
    public function fetch($aData)
    {
        extract($aData);

//        search params
        $searchParams = $this->mapSearchFields($aData);

//        who viewed me data, this take other approach than resume select
        if ($aData['sView'] == 'who-viewed-me') {
            return $this->_getWhoViewedMe($aData);
        }

        $tableResume = Engine_Api::_()->getItemTable('ynresume_resume');
        $paginator = $tableResume->getResumesPaginator($searchParams);

        return Ynmobile_AppMeta::_exports_by_page($paginator, $iPage, $iLimit, $fields = array('listing'));
    }

    /**
     * @param $aData
     * @return array : including mix of resume and user
     */
    public function _getWhoViewedMe($aData) {

        extract($aData);

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $resumeTable = Engine_Api::_() -> getItemTable('ynresume_resume');
        $resume = $resumeTable -> getResume($viewer -> getIdentity());

        if (!$resume){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You haven't created your resume yet."),
            );
        }

        $viewTable = Engine_Api::_() -> getDbTable('views', 'ynresume');
        $totalView = $viewTable -> getCountViewer($resume);

        // get viewers paginator
        $paginator = $viewTable -> getViewersPaginator($resume, false);

        if($iPage < 1){
            $iPage =  1;
        }
        if(!$iLimit){
            $iLimit =  10;
        }

        $paginator -> setCurrentPageNumber($iPage);
        $paginator -> setItemCountPerPage($iLimit);

        if ($iPage > $paginator -> count()) {
            return array();
        }

        $result = array();

        foreach ($paginator as $entry) {

            $user = Engine_Api::_() -> getItem('user', $entry -> user_id);
            if($user -> getIdentity()) {
                $viewedResume = Engine_Api::_() -> ynresume() -> getUserResume($user -> getIdentity());

                // there is a resume of this user
                if(!empty($viewedResume)) {

                    $result[] = Ynmobile_AppMeta::_export_one($viewedResume, array('listing'));
                } else {
                    $result[] = Ynmobile_AppMeta::_export_one($user, array('listing'));
                }
            }
        }

        // get view more
        if (!$resume -> serviced) {
            if ($totalView > 2) {
                $view = Zend_Registry::get('Zend_View');
                $baseUrl = Ynmobile_Helper_Base::getBaseUrl();
                $photoUrl = '/application/modules/Ynresume/externals/images/register-who-viewed-me.png';
                $finalPhotoUrl = $baseUrl.$photoUrl;
                $additionalView = array(
                    'sModelType' => 'ynresume_more_viewer',
                    'sPhotoUrl' => $finalPhotoUrl,
                    'sFullPhotoUrl' => $finalPhotoUrl,
                    'text1' => $totalView - 2 . ' ' . $view -> translate("more person viewed you"),
                    'text2' => $view -> translate('See the full list of %1s people who viewed your resume by using "Who Viewed Me" service', $totalView)
                );
                $result[] = $additionalView;
            }
        }

        return $result;
    }

    /**
     * @return array
     * for search form
     */
    public function form_search() {

        $categoryOptions  = $this->getIndustriesOptions();

        // remove all cat options
        array_shift($categoryOptions);

        return array(
            'industryOptions'=>$categoryOptions
        );
    }

    /**
     * @return array
     */
    public function getIndustriesOptions(){
        $categoryOptions =  array();

        foreach($this->__getIndustriesOptions() as $row){
            $categoryOptions[] = array(
                'id'=>$row['industry_id'],
                'title'=>str_repeat("-", $row['level'] - 1).$row['title'],
            );
        }

        return $categoryOptions;
    }

    /**
     * @return mixed
     */
    function __getIndustriesOptions(){
        return Engine_Api::_() -> getDbTable('industries', 'ynresume')->getIndustries();
    }

    public function save($aData){
        return $this->saveResume($aData);
    }

    public function un_save($aData){
        return $this->saveResume($aData);
    }

    /**
     * @param $aData
     * @return array
     * using same function for save and un-save
     */
    function saveResume($aData) {

        if (!isset($aData['iResumeId']))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing Resume Id!")
            );
        }

        extract($aData);

        $resumeId = intval($iResumeId);

        $resume = Engine_Api::_() -> getItem('ynresume_resume', $resumeId);

        if (!$resume){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Resume not found."),
            );
        }

        // check self resume
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if ($viewer->isSelf($resume->getOwner())) {
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to save this resume."),
            );
        }
        $tableSave = Engine_Api::_() -> getDbTable('saves', 'ynresume');
        $saveRow = $tableSave -> getSaveRow($viewer -> getIdentity(), $resume -> getIdentity());

        // now switch save status
        if(!empty($saveRow))
        {
            //un-save action
            $saveRow -> delete();
            $saveStatus = 0;
        }
        else
        {
            //save action
            $saveRow = $tableSave -> createRow();
            $saveRow -> user_id = $viewer -> getIdentity();
            $saveRow -> resume_id = $resume -> getIdentity();
            $saveRow -> creation_date = $now =  date("Y-m-d H:i:s");
            $saveRow -> save();
            $saveStatus = 1;
        }

        $resume = Engine_Api::_() -> getItem('ynresume_resume', $resumeId);

        $message = ($saveStatus)?Zend_Registry::get('Zend_Translate') -> _("Resume is successfully saved"):Zend_Registry::get('Zend_Translate') -> _("Resume is unsaved");

        return array(
            'error_code' => 0,
            'message' => $message,
            'aItem' => Ynmobile_AppMeta::_export_one($resume, array('listing'))
        );
    }

    public function favourite($aData){
        return $this->favouriteResume($aData);
    }

    public function un_favourite($aData){
        return $this->favouriteResume($aData);
    }

    // using same function for both actions
    function favouriteResume($aData) {

        if (!isset($aData['iResumeId']))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing Resume Id!")
            );
        }

        extract($aData);

        $resumeId = intval($iResumeId);

        $resume = Engine_Api::_() -> getItem('ynresume_resume', $resumeId);

        if (!$resume){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Resume not found."),
            );
        }

        // check self resume
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if ($viewer->isSelf($resume->getOwner())) {
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to add this resume to your favourite list."),
            );
        }
        $tableFavourite = Engine_Api::_() -> getDbTable('favourites', 'ynresume');
        $favouriteRow = $tableFavourite -> getFavouriteResume($resume -> getIdentity(), $viewer -> getIdentity());

        // now switch save status
        if(!empty($favouriteRow))
        {
            //un-save action
            $favouriteRow -> delete();
            $favouriteStatus = 0;
        }
        else
        {
            //save action
            $favouriteRow = $tableFavourite -> createRow();
            $favouriteRow -> user_id = $viewer -> getIdentity();
            $favouriteRow -> resume_id = $resume -> getIdentity();
            $favouriteRow -> creation_date = $now =  date("Y-m-d H:i:s");
            $favouriteRow -> save();
            $favouriteStatus = 1;
        }

        $resume = Engine_Api::_() -> getItem('ynresume_resume', $resumeId);

        $message = ($favouriteStatus)?Zend_Registry::get('Zend_Translate') -> _("Resume successfully added to favourite list"):Zend_Registry::get('Zend_Translate') -> _("Resume removed from favourite list");

        return array(
            'error_code' => 0,
            'message' => $message,
            'aItem' => Ynmobile_AppMeta::_export_one($resume, array('listing'))
        );
    }

    /**
     * @param $aData
     * @return array
     *  Resume detail
     */
    public function detail($aData){

        if (!isset($aData['iResumeId']))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing Resume Id!")
            );
        }

        extract($aData);

        if(empty($fields)) $fields  = 'detail';

        $fields = explode(',', $fields);

        $resumeId = intval($iResumeId);

        if ($resumeId == 0) {
            $resume = Engine_Api::_()->ynresume()->getUserResume();
        } else {
            $resume = Engine_Api::_() -> getItem('ynresume_resume', $resumeId);
        }

        if (!$resume){
            if ($resumeId == 0) {
                return array(
                    'error_code' => 0,
                    'bNoSelfResume' => 1,
                    'message' => ''
                );
            } else {
                return array(
                    'error_code' => 1,
                    'error_message' => Zend_Registry::get('Zend_Translate') -> _("Resume not found.")
                );
            }
        }

        return Ynmobile_AppMeta::_export_one($resume, $fields);
    }

    /**
     * @param $aData
     * @return array
     */
    function endorse($aData) {
        if ( !( isset($aData['iResumeId']) && isset($aData['iSkillId'])) )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing Skill and Resume Id!")
            );
        }

        return $this->_endorse($aData, 1);
    }

    function unendorse($aData) {
        if ( !( isset($aData['iResumeId']) && isset($aData['iSkillId'])) )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing Skill and Resume Id!")
            );
        }

        return $this->_endorse($aData, 0);
    }

    /**
     * @param $aData
     * @param int $endorse
     * @return array
     *  endorse state switcher
     *  @TODO may implement a check for endorse status and return proper error
     */
    function _endorse($aData, $endorseState = 0) {
        extract($aData);

        $resumeId = intval($iResumeId);
        $resume = Engine_Api::_()->getItem('ynresume_resume', $resumeId);

        if (!$resume){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Resume not found."),
            );
        }

        $skillId = intval($iSkillId);
        $skill = Engine_Api::_()->getItem('ynresume_skill', $skillId);
        if (!$resume){
            return array(
                'error_code'=>1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Skill not found."),
            );
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        if ($endorseState) {
            $resume -> skills() -> addSkillMap($viewer, $skill);
        } else {
            $resume -> skills() -> removeSkillMap($viewer, $skill);
        }

        // get returned skill
        $result = array();
        $viewerId = $viewer -> getIdentity();
        $result['iSkillId'] = $skill->skill_id;
        $result['sTitle'] = $skill->text;
        $endorsedUsers = $skill->getEndorsedUsers($resume, false);
        $endorsedUserIds = array();
        foreach ($endorsedUsers as $endorse)
        {
            $endorsedUserIds[] = $endorse -> user_id;
        }
        $result['iTotalEndorses'] = count($endorsedUsers);
        $result['bIsEndorsed'] = (in_array($viewerId, $endorsedUserIds)) ? 1: 0;
        $result['aEndorsesUsers'] = array();
        foreach ($endorsedUserIds as $user_id) {
            $user = Engine_Api::_() -> getItem('user', $user_id);
            $result['aEndorsesUsers'][] = Ynmobile_AppMeta::_export_one($user, $fields = array('simple_array'));
        }

        $message = ($endorseState)?Zend_Registry::get('Zend_Translate') -> _("You have endorsed this skill."):Zend_Registry::get('Zend_Translate') -> _("You have un-endorsed this skill.");
        return array(
            'error_code' => 0,
            'message' => $message,
            'aItem' => $result
        );
    }

    /**
     * @param $aData
     * @return array
     * fetching faqs
     */
    public function fetch_faqs($aData) {

        extract($aData);
        $result = array();

        $table = Engine_Api::_()->getDbTable('faqs', 'ynresume');
        $select = $table->select()->where("status = 'show'")->order('order ASC');
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

    public function who_viewed_me_status() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $resumeTable = Engine_Api::_() -> getItemTable('ynresume_resume');
        $resume = $resumeTable -> getResume($viewer -> getIdentity());

        if (!$resume){
            return '';
        }

        $view = Zend_Registry::get('Zend_View');
        $viewTable = Engine_Api::_() -> getDbTable('views', 'ynresume');
        $totalView = $viewTable -> getCountViewer($resume);
        if ($resume->serviced) {

            $serviceDateObj = new Zend_Date(strtotime($resume -> service_expiration_date));
            $tz = $viewer->timezone;
            $dateText = '';
            if (!is_null($serviceDateObj))
            {
                $serviceDateObj->setTimezone($tz);
                $dateText = date('M d Y', $serviceDateObj->getTimestamp());
            }
            $text = $view -> translate("You are using \"Who Viewed Me\" service and it is valid until %s", $dateText);

        } else {
            $text = $view->translate('See the full list of %1s people who viewed your resume by using \"Who Viewed Me\" service', $totalView);
        }

        return $text;
    }
}
