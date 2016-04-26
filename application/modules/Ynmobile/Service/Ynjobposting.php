<?php

/**
 * @since  4.10
 * @author Nam Nguyen <namnv@younetco.com>
 */
class Ynmobile_Service_Ynjobposting extends Ynmobile_Service_Base
{

    /**
     * main module name.
     *
     * @var string
     */
    protected $module = 'ynjobposting';


    /**
     * @main item type
     */
    protected $mainItemType = 'ynjobposting_job';

    public function mapSearchFields($aData)
    {
        $maps = array(
            'sStatus'          => array(
                'def' => 'all',
                'key' => 'status',
            ),
            'sSearch'          => array(
                'def' => '',
                'key' => 'job_title',
            ),
            'sCompanyName'     => array(
                'def' => '',
                'key' => 'company_name',
            ),
            'iIndustryId'      => array(
                'def' => '',
                'key' => 'industry_id',
            ),
            'sLevel'           => array(
                'def' => 'all',
                'key' => 'level',
            ),
            'sType'            => array(
                'def' => 'all',
                'key' => 'type',
            ),
            'sSalaryFrom'      => array(
                'def' => '',
                'key' => 'salary_from',
            ),
            'sSalaryCurrency'  => array(
                'def' => '',
                'key' => 'salary_currency',
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
        );

        $result = array();

        foreach ($maps as $k => $opt) {
            if (isset($aData[ $k ])) {
                $result[ $opt['key'] ] = $aData[ $k ];
            } else {
                $result[ $opt['key'] ] = $opt['def'];
            }
        }

        return $result;
    }


    /**
     * input: array []
     *    iPage,
     * iLimit,
     * sSearch,
     * sCompanyName,
     * iIndustryId,
     * sLevel,
     * sType,
     * sSalaryFrom,
     * sSalaryCurrency,
     * sLocation,
     * sWithin
     */
    public function fetch_jobs($aData)
    {

        extract($aData);

        // search params
        $params = $this->mapSearchFields($aData);


        $tableJob = Engine_Api::_()->getItemTable('ynjobposting_job');
        $paginator = $tableJob->getJobsPaginator($params);

        return Ynmobile_AppMeta::_exports_by_page($paginator, $iPage, $iLimit, $fields = array('listing'));

    }

    /**
     * iCompanyId,
     * iPage,
     * iLimit,
     * sSearch,
     * sStatus
     */
    public function fetch_posted_jobs($aData)
    {
        extract($aData);

        if (empty($sStatus)) {
            $sStatus = 'all';
        }

        $params = array(
            'company_id' => $iCompanyId,
            'tags'       => $sSearch,
            'job_title'  => $sSearch,
            'status'     => $sStatus,
            'user_id'    => $this->getViewer()->getIdentity(),
        );

        $table = Engine_Api::_()->getItemTable('ynjobposting_job');
        $select = $table->getJobsPaginator($params);

        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields = array('listing'));
    }

    public function job_info($aData)
    {
        extract($aData);
        $iJobId = intval($iJobId);

        $job = Engine_Api::_()->getItem('ynjobposting_job', $iJobId);


        if (!$job || $job->status == 'deleted') {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Job not found")
            );
        }

        return Ynmobile_AppMeta::_export_one($job, array('infos'));

    }


    /**
     * @return array
     */
    public function form_search_jobs()
    {

        return array(
            'industry_options' => $this->industryOptions(),
            'level_options'    => $this->levelOptions(),
            'type_options'     => $this->typeOptions(),
            'currency_options' => $this->currencyOptions(),
        );
    }

    //keyword=&industry_id=0&location=&within=50&size=&Search=&lat=0&long=0
    //iPage,
    //iLimit,
    //sSearch,
    //iIndustryId,
    //sLocation,
    //sWithin,
    //iSize
    public function mapCompanySearchFields($aData)
    {
        $maps = array(
            'sSearch'          => array(
                'def' => '',
                'key' => 'keyword',
            ),
            'iIndustryId'      => array(
                'def' => '',
                'key' => 'industry_id',
            ),
            'sLocationAddress' => array(
                'def' => '',
                'key' => 'location',
            ),
            // 'sStatus'=>array(
            // 'def'=>'published',
            // 'key'=>'status',
            // ),
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
            'iSize'            => array(
                'def' => '',
                'key' => 'size',
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

        if ($aData['sView'] == 'my') {
            $result['user_id'] = $this->getViewer()->getIdentity();
        }

        return $result;
    }

    public function fetch_companies($aData)
    {
        extract($aData);

        $searchParams = $this->mapCompanySearchFields($aData);

        $searchParams['status'] = 'published';

        $select = $this->getCompaniesSelect($searchParams);

        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields = array('listing'));
    }


    public function fetch_my_companies($aData)
    {
        extract($aData);

        $searchParams = $this->mapCompanySearchFields($aData);

        $searchParams['status'] = 'all';

        $select = $this->getCompaniesSelect($searchParams);

        $select->where("company.user_id=?", $this->getViewer()->getIdentity());

        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields = array('infos'));
    }

    public function fetch_following_companies($aData)
    {
        extract($aData);

        $viewer = Engine_Api::_()->user()->getViewer();

        $table = Engine_Api::_()->getItemTable('ynjobposting_company');
        $tableName = $table->info('name');
        $select = $table->select();
        $select->setIntegrityCheck(false);
        $joinTbl = Engine_Api::_()->getItemTable('ynjobposting_follow');

        $joinTblName = $joinTbl->info('name');

        $select->from("$tableName as company", "company.*");
        $viewer = Engine_Api::_()->user()->getViewer();

        $select->joinLeft("$joinTblName", "$joinTblName.company_id = company.company_id", "");

        $select->where("$joinTblName.active=?", 1);

        $select->where("$joinTblName.user_id = ?", $viewer->getIdentity());


        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields = array('listing'));
    }

    public function getCompaniesSelect($params = array())
    {
        $companyTbl = Engine_Api::_()->getItemTable('ynjobposting_company');
        $companyTblName = $companyTbl->info('name');

        $userTbl = Engine_Api::_()->getDbtable('users', 'user');
        $userTblName = $userTbl->info('name');

        $industrymapsTbl = Engine_Api::_()->getDbTable('industrymaps', 'ynjobposting');
        $industrymapsTblName = $industrymapsTbl->info('name');

        $industryTbl = Engine_Api::_()->getItemTable('ynjobposting_industry');
        $indsutryTblName = $industryTbl->info('name');

        $select = $companyTbl->select();
        $select->setIntegrityCheck(false);

        //Get your location
        $target_distance = $base_lat = $base_lng = "";
        if (isset($params['lat']))
            $base_lat = $params['lat'];
        if (isset($params['long']))
            $base_lng = $params['long'];

        //Get target distance in miles
        if (isset($params['within']))
            $target_distance = $params['within'];
        else {
            $target_distance = 50;
        }
        if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
            $select->from("$companyTblName as company", array("company.*", "( 3959 * acos( cos( radians('$base_lat')) * cos( radians( company.latitude ) ) * cos( radians( company.longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( company.latitude ) ) ) ) AS distance"));
            $select->where("company.latitude <> ''");
            $select->where("company.longitude <> ''");
        } else {
            $select->from("$companyTblName as company", "company.*");
        }

        $select->joinLeft("$userTblName as user", "user.user_id = company.user_id", null)
            ->joinLeft("$industrymapsTblName as industrymap", "industrymap.company_id = company.company_id", null);

        if (isset($params['name']) && $params['name'] != '') {
            $select->where('company.name LIKE ?', '%' . $params['name'] . '%');
        }

        if (isset($params['keyword']) && $params['keyword'] != '') {
            $keyword = $params['keyword'];
            $select->where("company.name LIKE '%{$keyword}%' OR company.description LIKE '%{$keyword}%'");
        }

        if (isset($params['owner']) && $params['owner'] != '') {
            $select->where('user.displayname LIKE ?', '%' . $params['owner'] . '%');
        }

        if (isset($params['industry_id']) && $params['industry_id'] != 'all' && $params['industry_id']) {
            $industrySelect = $industryTbl->select()->where('industry_id = ?', $params['industry_id']);
            $industry = $industryTbl->fetchRow($industrySelect);
            if ($industry) {
                $tree = array();
                $node = $industryTbl->getNode($industry->getIdentity());
                $industryTbl->appendChildToTree($node, $tree);
                $industries = array();
                foreach ($tree as $node) {
                    array_push($industries, $node->industry_id);
                }
                $select->where('industrymap.industry_id IN (?)', $industries);
            }
        }

        if (isset($params['status']) && $params['status'] == 'all') {
            $select->where('company.status <> ?', 'deleted');
        } elseif (isset($params['status']) && $params['status'] != 'all') {
            $select->where('company.status = ?', $params['status']);
        }

        if (isset($params['sponsored']) && $params['sponsored'] != 'all') {
            $select->where('company.sponsored = ?', $params['sponsored']);
        }

        if (isset($params['size']) && $params['size']) {
            $size = (int)$params['size'];
            $select->where('company.from_employee < ?', $params['size']);
            $select->where('company.to_employee > ?', $params['size']);
        }

        if (isset($params['order'])) {
            if (empty($params['direction'])) {
                $params['direction'] = ($params['order'] == 'company.title') ? 'ASC' : 'DESC';
            }
            $select->order($params['order'] . ' ' . $params['direction']);
        } else {
            if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
                $select->having("distance <= $target_distance");
                $select->order("distance ASC");
            } else if (!empty($params['direction'])) {
                $select->order('company.company_id' . ' ' . $params['direction']);
            }
        }
        $select->group('company.company_id');

        return $select;
    }

    public function fetch_company_jobs($aData)
    {
        extract($aData);

        $viewer = Engine_Api::_()->user()->getViewer();
        $iCompanyId = intval($iCompanyId);

        $company = Engine_Api::_()->getItem('ynjobposting_company', $iCompanyId);

        if (!$company) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Company not found"),
            );
        }

        $jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');
        $select = $jobTbl->select()->where("company_id = ?", $company->getIdentity());

        if ($company->user_id == $viewer->user_id) {
            $select->where("status <> ?", 'deleted');
        } else {
            $select->where("status = ?", 'published');
        }

        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, array('listing'));
    }

    public function unfollow_company($aData)
    {
        extract($aData);

        $viewer = Engine_Api::_()->user()->getViewer();
        $iCompanyId = intval($iCompanyId);

        $company = Engine_Api::_()->getItem('ynjobposting_company', $iCompanyId);

        $result = array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("Un-follow Company"),
        );

        if (!$company) {
            return $result;
        }

        $tableFollow = Engine_Api::_()->getItemTable('ynjobposting_follow');
        $followRow = $tableFollow->getFollowBy($company->getIdentity(), $viewer->getIdentity());

        if (isset($followRow)) {
            if ($followRow->active == 1) {
                $followRow->active = 0;
            }
            $followRow->save();
        }

        $company = Engine_Api::_()->getItem('ynjobposting_company', $iCompanyId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("Un-follow Company"),
            'aItem'      => Ynmobile_AppMeta::_export_one($company, array('infos'))
        );
    }

    public function follow_company($aData)
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        extract($aData);
        $iCompanyId = intval($iCompanyId);

        $company = Engine_Api::_()->getItem('ynjobposting_company', $iCompanyId);

        $result = array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("Follow Company"),
        );

        if (!$company) {
            return $result;
        }
        $tableFollow = Engine_Api::_()->getItemTable('ynjobposting_follow');
        $followRow = $tableFollow->getFollowBy($company->getIdentity(), $viewer->getIdentity());
        if (isset($followRow)) {
            $followRow->active = 1;
            $followRow->save();
        } else {
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $owner = $company->getOwner();
            $notifyApi->addNotification($owner, $viewer, $company, 'ynjobposting_company_follow');
            $followRow = $tableFollow->createRow();
            $followRow->user_id = $viewer->getIdentity();
            $followRow->company_id = $company->getIdentity();
            $followRow->active = 1;
            $followRow->save();
        }

        $company = Engine_Api::_()->getItem('ynjobposting_company', $iCompanyId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("Follow Company"),
            'aItem'      => Ynmobile_AppMeta::_export_one($company, array('infos'))
        );
    }

    public function close_company($aData)
    {
        extract($aData);
        $status = 'closed';
        $iCompanyId = intval($iCompanyId);

        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();

        $company = Engine_Api::_()->getItem('ynjobposting_company', $iCompanyId);
        $job_status = $label = "";

        if (!$company) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Company not found."),
            );
        }

        if (!$company->isClosable()) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You do not have permission to close this company."),
            );
        }

        $job_status = 'ended';
        $notificationType = 'ynjobposting_job_ended';
        $notificationTypeCompany = "ynjobposting_company_closed";

        //save company status
        $company->status = $status;
        $company->save();

        $company_owner = $company->getOwner();
        $notifyApi->addNotification($company_owner, $company, $company, $notificationTypeCompany);

        $jobs = $company->getJobs();
        foreach ($jobs as $job) {
            $job->status = $job_status;
            $job->save();
            $owner = $job->getOwner();
            //send notice to job
            $notifyApi->addNotification($owner, $job, $job, $notificationType);
        }

        $company = Engine_Api::_()->getItem('ynjobposting_company', $iCompanyId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("The selected company is closed."),
            'aItem'      => Ynmobile_AppMeta::_export_one($company, array('infos'))
        );

    }

    public function company_info($aData)
    {
        extract($aData);

        $iCompanyId = intval($iCompanyId);

        $company = Engine_Api::_()->getItem('ynjobposting_company', $iCompanyId);

        if (!$company || $company->status == 'deleted') {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Company not found."),
            );
        }

        return Ynmobile_AppMeta::_export_one($company, array('infos'));
    }

    public function form_add_company($aData)
    {

        $auth = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');

        $viewer = Engine_Api::_()->user()->getViewer();

        $auth = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');
        $canCreate = $auth->setAuthParams('ynjobposting_company', null, 'create')->checkRequire();

        if (!$canCreate) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_("You don't have permission to create company"),
            );
        }

        //get max company user can create
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max_companies = $permissionsTable->getAllowed('ynjobposting', $viewer->level_id, 'max_company');
        if ($max_companies == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynjobposting')
                ->where('name = ?', 'max_company'));
            if ($row) {
                $max_companies = $row->value;
            }
        }
        $companyTbl = Engine_Api::_()->getItemTable('ynjobposting_company');
        $select = $companyTbl->select()
            ->where('user_id = ?', $viewer->getIdentity())
            ->where('deleted = ?', 0);

        $raw_data = $companyTbl->fetchAll($select);
        if (($max_companies != 0) && (sizeof($raw_data) >= $max_companies)) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Your companies are reach limit. Plese delete some companies for creating new.'),
            );
        }

        $industries = Engine_Api::_()->getItemTable('ynjobposting_industry')->getIndustries();
        unset($industries[0]);

        if (empty($industries)) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Create company require at least one industry. Please contact admin for more details.'),
            );
        }

        return array(
            'industry_options' => $this->industryOptions(),
            'view_options'     => $this->viewOptions(),
            // 'comment_options'=>$this->commentOptions(),
        );
    }

    public function mapAddCompanyFields($aData)
    {
        $keys = array(
            'iIndustryId'      => 'industry_id',
            'sTitle'           => 'name',
            'sDescription'     => 'description',
            'sLocationAddress' => 'location_address',
            'sLat'             => 'latitude',
            'sLong'            => 'longitude',
            'sWebsite'         => 'website',
            'sSizeFrom'        => 'from_employee',
            'sSizeTo'          => 'to_employee',
            'sContactName'     => 'contact_name',
            'sContactEmail'    => 'contact_email',
            'sContactPhone'    => 'contact_phone',
            'sContactFax'      => 'contact_fax',
            'sAuthView'        => 'view',
            'sProfilePhoto'    => 'profile_photo', // data URI
            'sCoverPhoto'      => 'cover_photo'// data URI
        );

        $values = array();
        foreach ($keys as $from => $to) {
            if (isset($aData[ $from ])) {
                $values[ $to ] = $aData[ $from ];
            } else {
                $values[ $to ] = "";
            }
        }

        $values['description'] = html_entity_decode($values['description']);

        return $values;
    }

    public function add_company($aData)
    {
        extract($aData);

        $values = $this->mapAddCompanyFields($aData);
        $viewer = Engine_Api::_()->user()->getViewer();

        $auth = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');
        $canCreate = $auth->setAuthParams('ynjobposting_company', null, 'create')->checkRequire();

        if (!$canCreate) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_("You don't have permission to create company"),
            );
        }

        //get max company user can create
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max_companies = $permissionsTable->getAllowed('ynjobposting', $viewer->level_id, 'max_company');
        if ($max_companies == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynjobposting')
                ->where('name = ?', 'max_company'));
            if ($row) {
                $max_companies = $row->value;
            }
        }
        $companyTbl = Engine_Api::_()->getItemTable('ynjobposting_company');
        $select = $companyTbl->select()
            ->where('user_id = ?', $viewer->getIdentity())
            ->where('deleted = ?', 0);

        $raw_data = $companyTbl->fetchAll($select);
        if (($max_companies != 0) && (sizeof($raw_data) >= $max_companies)) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Your companies are reach limit. Plese delete some companies for creating new.'),
            );
        }

        $industries = Engine_Api::_()->getItemTable('ynjobposting_industry')->getIndustries();
        unset($industries[0]);

        if (empty($industries)) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Create company require at least one industry. Please contact admin for more details.'),
            );
        }

        if ($values['to_employee'] < $values['from_employee']) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Please input valid from employee & to employee value.'),
            );
        }

        $regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
        if (!preg_match($regexp, $values['contact_email'])) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Please enter valid email!'),
            );
        }


        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            //save company
            $companyTable = Engine_Api::_()->getItemTable('ynjobposting_company');
            $company = $companyTable->createRow();
            $company->user_id = $viewer->getIdentity();
            $company->name = $values['name'];
            $company->description = $values['description'];
            $company->location = $values['location_address'];
            $company->longitude = $values['longitude'];
            $company->latitude = $values['latitude'];
            $company->website = $values['website'];
            $company->from_employee = $values['from_employee'];
            $company->to_employee = $values['to_employee'];
            $company->contact_name = $values['contact_name'];
            $company->contact_email = $values['contact_email'];
            $company->contact_phone = $values['contact_phone'];
            $company->contact_fax = $values['contact_fax'];
            $company->save();

            // Set photo
            if (!empty($values['profile_photo'])) {
                if ($file = Engine_Api::_()->ynmobile()->saveUploadPhotoAsDataUrlToFile($values['profile_photo'])) {
                    $company->setPhoto($file);
                }
            }

            // Add Cover photo
            if (!empty($values['cover_photo'])) {
                if ($file = Engine_Api::_()->ynmobile()->saveUploadPhotoAsDataUrlToFile($values['cover_photo'])) {
                    $company->setCoverPhoto($file);
                }
            }

            //insert industry to mapping table
            if (!empty($values['industry_id'])) {
                $tableIndustryMap = Engine_Api::_()->getDbTable('industrymaps', 'ynjobposting');
                $checkIndustry = $tableIndustryMap->checkExistIndustryByCompany($values['industry_id'], $company->getIdentity());
                if (empty($checkIndustry)) {
                    $rowIndustryMap = $tableIndustryMap->createRow();
                    $rowIndustryMap->company_id = $company->getIdentity();
                    $rowIndustryMap->industry_id = $values['industry_id'];
                    $rowIndustryMap->main = true;
                    $rowIndustryMap->save();
                }
            }

            //set auth for view, comment
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
            $auth_arr = array('view', 'comment');
            foreach ($auth_arr as $elem) {
                $auth_role = $values[ $elem ];
                if (!$auth_role) {
                    $auth_role = 'everyone';
                }
                $roleMax = array_search($auth_role, $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($company, $role, $elem, ($i <= $roleMax));
                }
            }

            //send notice to admin
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $list_admin = Engine_Api::_()->user()->getSuperAdmins();
            foreach ($list_admin as $admin) {
                $notifyApi->addNotification($admin, $company, $company, 'ynjobposting_company_create');
            }

            //add activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($company->getOwner(), $company, 'ynjobposting_company_create');
            if ($action) {
                $activityApi->attachActivity($action, $company);
            }
            $company->addDefaultSubmissionForm();
            // Commit
            $db->commit();

            if (Engine_Api::_()->hasModuleBootstrap("yncredit")) {
                Engine_Api::_()->yncredit()->hookCustomEarnCredits($company->getOwner(), $company->name, 'ynjobposting_company', $company);
            }

            return array(
                'error_code' => 0,
                'message'=>Zend_Registry::get('Zend_Translate') -> _("Saved company successfully."),
                'iCompanyId' => $company->getIdentity(),
            );
        } catch (Exception $e) {
            $db->rollBack();

            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Could not create company."),
                'error_debug'   => $e->getMessage(),
                'aData'         => $aData,
                'values'        => $values
            );
        }

    }

    public function edit_company($aData)
    {
        extract($aData);
        $iCompanyId = intval($iCompanyId);

        $company = Engine_Api::_()->getItem('ynjobposting_company', $iCompanyId);
        $values = $this->mapAddCompanyFields($aData);

        if (!$company) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Company not found"),
            );
        }

        if (!$company->isEditable()) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You do not have permission to edit this company."),
            );
        }

        if ($values['to_employee'] < $values['from_employee']) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Please input valid from employee & to employee value.'),
            );
        }

        $regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
        if (!preg_match($regexp, $values['contact_email'])) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Please enter valid email!'),
            );
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            //Check if it edit main category
            if ($main_industry->industry_id != $values['industry_id']) {
                $old_industry = Engine_Api::_()->getItem('ynjobposting_industry', $main_industry->industry_id);
                $isEditIndustry = true;
            }

            //save company
            $company->name = $values['name'];
            $company->description = $values['description'];
            $company->location = $values['location_address'];
            $company->longitude = $values['long'];
            $company->latitude = $values['lat'];
            $company->website = $values['website'];
            $company->from_employee = $values['from_employee'];
            $company->to_employee = $values['to_employee'];
            $company->contact_name = $values['contact_name'];
            $company->contact_email = $values['contact_email'];
            $company->contact_phone = $values['contact_phone'];
            $company->contact_fax = $values['contact_fax'];
            $company->save();

            // Set photo
            if (!empty($values['profile_photo'])) {
                if ($file = Engine_Api::_()->ynmobile()->saveUploadPhotoAsDataUrlToFile($values['profile_photo'])) {
                    $company->setPhoto($file);
                }
            }

            // Add Cover photo
            if (!empty($values['cover_photo'])) {
                if ($file = Engine_Api::_()->ynmobile()->saveUploadPhotoAsDataUrlToFile($values['cover_photo'])) {
                    $company->setCoverPhoto($file);
                }
            }

            //delete old industry
            $tableIndustryMap = Engine_Api::_()->getDbTable('industrymaps', 'ynjobposting');
            $tableIndustryMap->deleteIndustriesByCompanyId($company->getIdentity());

            //insert industry to mapping table
            if (!empty($values['industry_id'])) {
                $tableIndustryMap = Engine_Api::_()->getDbTable('industrymaps', 'ynjobposting');
                $checkIndustry = $tableIndustryMap->checkExistIndustryByCompany($values['industry_id'], $company->getIdentity());
                if (empty($checkIndustry)) {
                    $rowIndustryMap = $tableIndustryMap->createRow();
                    $rowIndustryMap->company_id = $company->getIdentity();
                    $rowIndustryMap->industry_id = $values['industry_id'];
                    $rowIndustryMap->main = true;
                    $rowIndustryMap->save();
                }
            }

            // Remove old data custom fields if edit industry
            if ($isEditIndustry) {
                $tableMaps = Engine_Api::_()->getDbTable('maps', 'ynjobposting');
                $tableValues = Engine_Api::_()->getDbTable('values', 'ynjobposting');
                $tableSearch = Engine_Api::_()->getDbTable('search', 'ynjobposting');
                if ($old_industry) {
                    $fieldIds = $tableMaps->fetchAll($tableMaps->select()->where('option_id = ?', $old_industry->option_id));
                    $arr_ids = array();
                    if (count($fieldIds) > 0) {
                        //clear values in search table
                        $searchItem = $tableSearch->fetchRow($tableSearch->select()->where('item_id = ?', $company->getIdentity())->limit(1));
                        foreach ($fieldIds as $id) {
                            try {
                                $column_name = 'field_' . $id->child_id;
                                $searchItem->$column_name = null;
                                $arr_ids[] = $id->child_id;
                            } catch (exception $e) {
                                continue;
                            }
                        }
                        $searchItem->save();
                        //delele in values table
                        if (count($arr_ids) > 0) {
                            $valueItems = $tableValues->fetchAll($tableValues->select()->where('item_id = ?', $company->getIdentity())->where('field_id IN (?)', $arr_ids));
                            foreach ($valueItems as $item) {
                                $item->delete();
                            }
                        }
                    }
                }
            }

            //set auth for view, comment
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
            $auth_arr = array('view');
            foreach ($auth_arr as $elem) {
                $auth_role = $values[ $elem ];
                if (!$auth_role) {
                    $auth_role = 'everyone';
                }
                $roleMax = array_search($auth_role, $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($company, $role, $elem, ($i <= $roleMax));
                }
            }

            // Commit
            $db->commit();

        } catch (Exception $e) {
            $db->rollBack();

            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Could not edit company."),
                'error_debug'   => $e->getMessage(),
                'aData'         => $aData,
                'values'        => $values
            );
        }

        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $owner = $company->getOwner();
        $notifyApi->addNotification($owner, $company, $company, 'ynjobposting_company_edited');

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("Saved company successfully."),
            'iCompanyId' => $company->getIdentity(),
        );
    }

    public function form_edit_company($aData)
    {

        extract($aData);
        $iCompanyId = intval($iCompanyId);

        $company = Engine_Api::_()->getItem('ynjobposting_company', $iCompanyId);

        if (!$company) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Company not found"),
            );
        }

        if (!$company->isEditable()) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You do not have permission to edit this company."),
            );
        }

        return array(
            'aItem'            => Ynmobile_AppMeta::_export_one($company, array('edit')),
            'industry_options' => $this->industryOptions(),
            'view_options'     => $this->viewOptions(),
        );
    }

    public function delete_company($aData)
    {
        extract($aData);
        $status = 'deleted';
        $iCompanyId = intval($iCompanyId);

        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();

        $company = Engine_Api::_()->getItem('ynjobposting_company', $iCompanyId);
        $job_status = $label = "";

        if (!$company) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Company not found"),
            );
        }

        if (!$company->isDeletable()) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You do not have permission to delete this company."),
            );
        }

        $notificationType = 'ynjobposting_job_deleted';
        $notificationTypeCompany = "ynjobposting_company_deleted";
        $job_status = 'deleted';

        //save company status
        $company->status = $status;
        $company->save();

        $company_owner = $company->getOwner();
        $notifyApi->addNotification($company_owner, $company, $company, $notificationTypeCompany);

        $jobs = $company->getJobs();
        foreach ($jobs as $job) {
            $job->delete();
            $owner = $job->getOwner();
            //send notice to job
            $notifyApi->addNotification($owner, $job, $job, $notificationType);
        }
        $company->delete();

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("The selected company is deleted"),
        );
    }

    public function form_search_companies($aData)
    {
        return array(
            'industry_options' => $this->industryOptions(),
        );
    }

    public function fetch_saved_jobs($aData)
    {
        extract($aData);

        $table = Engine_Api::_()->getItemTable('ynjobposting_job');
        $tableName = $table->info('name');
        $select = $table->select();
        $select->setIntegrityCheck(false);
        $joinTbl = Engine_Api::_()->getDbTable('savejobs', 'ynjobposting');

        $joinTblName = $joinTbl->info('name');

        $select->from("$tableName as job", "job.*");
        $select->where('job.status <> ?', 'deleted');
        $viewer = Engine_Api::_()->user()->getViewer();
        $select->joinLeft("$joinTblName", "$joinTblName.job_id = job.job_id", "");

        $select->where("$joinTblName.user_id = ?", $viewer->getIdentity());

        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields = array('listing'));
    }

    public function fetch_applied_jobs($aData)
    {

        extract($aData);

        $applyTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
        $applyTblName = $applyTbl->info('name');

        $jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');
        $jobTblName = $jobTbl->info('name');

        $user = $this->getViewer();
        $iViewerId = $user->getIdentity();

        $select = $jobTbl->select()->setIntegrityCheck(false)
            ->from($jobTblName)
            ->join($applyTblName, "{$jobTblName}.job_id = {$applyTblName}.job_id", "$applyTblName.creation_date as applied_date")
            ->where(" {$applyTblName}.user_id = ?", $iViewerId)
            ->where(" {$applyTblName}.owner_deleted=?", 0);


        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, array('listing', 'apply'));

    }

    public function save_job($aData)
    {
        extract($aData);
        $viewer = Engine_Api::_()->user()->getViewer();
        $iJobId = intval($iJobId);
        $job = Engine_Api::_()->getItem('ynjobposting_job', $iJobId);

        if (!$job) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Job not found.')
            );
        }

        if ($job->isOwner()) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('You could not save your own job.')
            );
        }

        //check job is published
        if (!$job->isPublished()) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('This job is not published now.')
            );
        }

        if ($job->hasApplied()) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('You have applied this job.')
            );
        }

        if ($job->hasSaved()) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('You have saved this job.')
            );
        }

        $table = Engine_Api::_()->getDbTable('savejobs', 'ynjobposting');

        try {
            $saveJob = $table->createRow();
            $saveJob->user_id = $viewer->getIdentity();
            $saveJob->job_id = $iJobId;
            $saveJob->save();
        } catch (Exception $e) {
            return array(
                'error_code'    => 1,
                'error_message' => '',
            );
        }


        $job = Engine_Api::_()->getItem('ynjobposting_job', $iJobId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("You have saved this job successfully."),
            'aItem'      => Ynmobile_AppMeta::_export_one($job, array('infos')),
        );
    }

    public function apply_job($aData)
    {
        extract($aData);
        $viewer = Engine_Api::_()->user()->getViewer();
        $iJobId = intval($iJobId);
        $job = Engine_Api::_()->getItem('ynjobposting_job', $iJobId);

        if (!$job) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Job not found.')
            );
        }

        if ($job->isOwner()) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('You could not save your own job.')
            );
        }

        $submissionForm = $job->getSubmissionForm();
        if (!$submissionForm) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Can not apply job. Please contact with its company for more infomation.')
            );
        }

        if ($job->hasApplied()) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('You have applied this job.')
            );
        }

        //check job is published
        if (!$job->isPublished()) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('This job is not published now.')
            );
        }

        if ($job->hasSaved()) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('You have saved this job.')
            );
        }

        $table = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
        $jobApply = $table->createRow();
        $jobApply->job_id = $iJobId;
        $jobApply->user_id = $viewer->getIdentity();

        $jobApply->save();

        //count candidate
        $job->refresh();
        $job->candidate_count = $job->candidate_count + 1;
        $job->save();

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("You have saved this job successfully."),
        );
    }

    public function end_job($aData)
    {
        extract($aData);

        $iJobId = intval($iJobId);


        $job = Engine_Api::_()->getItem('ynjobposting_job', $iJobId);

        if (!$job) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Job not found.')
            );
        }

        if (!$job->isEndable()) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('You do not have permission to end this job.')
            );
        }

        if (!$job->isPublished()) {

            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Your request is invalid.')
            );
        }

        $job->changeStatus('ended');
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $owner = $job->getOwner();
        $notifyApi->addNotification($owner, $owner, $job, 'ynjobposting_job_ended');


        $job = Engine_Api::_()->getItem('ynjobposting_job', $iJobId);

        return array(
            'error_code' => 0,
            'message'    => Zend_Registry::get('Zend_Translate')->_('End job successful.'),
            'aItem'      => Ynmobile_AppMeta::_export_one($job, array('infos')),
        );

    }

    public function delete_job($aData)
    {
        extract($aData);

        $iJobId = intval($iJobId);


        $job = Engine_Api::_()->getItem('ynjobposting_job', $iJobId);

        if (!$job) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Job not found.')
            );
        }

        if (!$job->isDeletable()) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('You do not have permission to end this job.')
            );
        }


        $job->delete();

        //send notification
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notifyApi->addNotification($job->getOwner(), $job, $job, 'ynjobposting_job_deleted');

        return array(
            'error_code'    => 0,
            'error_message' => Zend_Registry::get('Zend_Translate')->_('Delete job successful.'),
        );
    }

    public function form_add_job($aData)
    {

        $companyOptions = $this->companyOptions();

        $viewer = Engine_Api::_()->user()->getViewer();

        if (empty($companyOptions)) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('You can not create a job. You have to create the company first, in order to create a job.'),
            );
        }

        $jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');

        //get max jobs user can create
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max_jobs = $permissionsTable->getAllowed('ynjobposting', $viewer->level_id, 'max_job');
        if ($max_jobs == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynjobposting')
                ->where('name = ?', 'max_job'));
            if ($row) {
                $max_jobs = $row->value;
            }
        }

        $select = $jobTbl->select()
            ->where('user_id = ?', $viewer->getIdentity())
            ->where('status <> ?', 'deleted');

        $raw_data = $jobTbl->fetchAll($select);

        if (($max_jobs != 0) && (sizeof($raw_data) >= $max_jobs)) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Your jobs are reach limit. Plese delete some jobs for creating new.')
            );
        }

        return array(
            'company_options'   => $companyOptions,
            'education_options' => $this->educationOptions(),
            'industry_options'  => $this->industryOptions(),
            'level_options'     => $this->levelOptions(),
            'type_options'      => $this->typeOptions(),
            'currency_options'  => $this->currencyOptions(),
            'view_options'      => $this->viewOptions(),
            'comment_options'   => $this->commentOptions(),
        );
    }

    public function form_edit_job($aData)
    {
        extract($aData);
        $iJobId = intval($iJobId);
        $job = Engine_Api::_()->getItem('ynjobposting_job', $iJobId);

        if (!$job) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Job not found."),
            );
        }


        if (!$job->isEditable()) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this job."),
            );
        }


        $companyOptions = $this->companyOptions();

        if (empty($companyOptions)) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('You can not edit this job. You have to create the company first, in order to create a job.'),
            );
        }

        return array(
            'aItem'             => Ynmobile_AppMeta::_export_one($job, array('infos', 'edit')),
            'company_options'   => $companyOptions,
            'education_options' => $this->educationOptions(),
            'industry_options'  => $this->industryOptions(),
            'level_options'     => $this->levelOptions(),
            'type_options'      => $this->typeOptions(),
            'currency_options'  => $this->currencyOptions(),
            'view_options'      => $this->viewOptions(),
            'comment_options'   => $this->commentOptions(),
        );

    }

    public function mapAddJobFields($aData)
    {
        $keys = array(
            'iCompanyId'       => 'company_id',
            'sTitle'           => 'title',
            'sDescription'     => 'description',
            'sDesiredSkills'   => 'skill_experience',
            'iIndustryId'      => 'industry_id',
            'sLevelId'         => 'level',
            'sTypeId'          => 'type',
            'sLanguage'        => 'language_prefer',
            'sEducationId'     => 'education_prefer',
            'sSalaryFrom'      => 'salary_from',
            'sSalaryTo'        => 'salary_to',
            'sCurrencyId'      => 'salary_currency',
            'sLocationAddress' => 'working_place',
            'sAuthView'        => 'view',
            'sAuthComment'     => 'comment',
            'sTags'            => 'tags',
            'sLat'             => 'latitude',
            'sLong'            => 'longitude',
            'iNegotiable'      => 'negotiable'
        );

        $values = array();
        foreach ($keys as $from => $to) {
            if (isset($aData[ $from ])) {
                $values[ $to ] = $aData[ $from ];
            } else {
                $values[ $to ] = "";
            }
        }

        $values['description'] = html_entity_decode($values['description']);
        $values['skill_experience'] = html_entity_decode($values['skill_experience']);


        return $values;

    }

    public function add_job($aData)
    {
        extract($aData);

        $auth = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');

        $canCreate = $auth->setAuthParams('ynjobposting_job', null, 'create')->checkRequire();

        if (!$canCreate) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_("You don't have permission to create post"),
            );
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $values = $this->mapAddJobFields($aData);


        $jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');

        //get max jobs user can create
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max_jobs = $permissionsTable->getAllowed('ynjobposting', $viewer->level_id, 'max_job');
        if ($max_jobs == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynjobposting')
                ->where('name = ?', 'max_job'));
            if ($row) {
                $max_jobs = $row->value;
            }
        }

        $select = $jobTbl->select()
            ->where('user_id = ?', $viewer->getIdentity())
            ->where('status <> ?', 'deleted');

        $raw_data = $jobTbl->fetchAll($select);

        if (($max_jobs != 0) && (sizeof($raw_data) >= $max_jobs)) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Your jobs are reach limit. Plese delete some jobs for creating new.')
            );
        }

        //popuplate company
        $companies = Engine_Api::_()->getItemTable('ynjobposting_company')->getMyCompanies();

        if (sizeof($companies) <= 0) {

            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('You can not create a job. You have to create the company first, in order to create a job.')
            );
        }

        //populate industry
        $industries = Engine_Api::_()->getItemTable('ynjobposting_industry')->getIndustries();
        unset($industries[0]);

        if (sizeof($industries) <= 0) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Create job require at least one industry. Please contact admin for more details.')
            );
        }


        $table = Engine_Api::_()->getItemTable('ynjobposting_job');
        $values['user_id'] = $viewer->getIdentity();

        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            if (empty($values['salary_from']) && empty($values['salary_to'])) {
                $values['negotiable'] = 1;
            }
            if ($values['negotiable']) {
                unset($values['salary_from']);
                unset($values['salary_to']);
            }

            $job = $table->createRow();
            $job->setFromArray($values);
            $job->save();


            $feature_period = 0;
            if ($values['feature_period'])
                $feature_period = $values['feature_period'];
            $db->commit();

            //set auth for view, comment
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
            $auth_arr = array('view', 'comment');
            foreach ($auth_arr as $elem) {
                $auth_role = $values[ $elem ];
                if (!$auth_role) {
                    $auth_role = 'everyone';
                }
                $roleMax = array_search($auth_role, $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($job, $role, $elem, ($i <= $roleMax));
                }
            }

            if (isset($values['tags']) && $values['tags']) {
                $tags = preg_split('/[,]+/', $values['tags']);
                $job->tags()->addTagMaps($viewer, $tags);
            }

            $search_table = Engine_Api::_()->getDbTable('search', 'core');
            $select = $search_table->select()->where('type = ?', 'ynjobposting_job')->where('id = ?', $job->getIdentity());
            $row = $search_table->fetchRow($select);
            if ($row) {
                $row->keywords = $values['tags'];
                $row->save();
            } else {
                $row = $search_table->createRow();
                $row->type = 'ynjobposting_job';
                $row->id = $job->getIdentity();
                $row->title = $job->title;
                $row->description = $job->description;
                $row->keywords = $values['tags'];
                $row->save();
            }

            if (Engine_Api::_()->hasModuleBootstrap("yncredit")) {
                Engine_Api::_()->yncredit()->hookCustomEarnCredits($job->getOwner(), $job->title, 'ynjobposting_job', $job);
            }

        } catch (Exception $e) {
            $db->rollBack();

            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Could not create job"),
                'error_debug'   => $e->getMessage(),
                'aData'         => $aData,
                'values'        => $values
            );
        }

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("Create job successfully."),
            'iJobId'     => $job->getIdentity(),
        );

    }


    public function edit_job($aData)
    {
        extract($aData);
        $iJobId = intval($iJobId);
        $job = Engine_Api::_()->getItem('ynjobposting_job', $iJobId);

        if (!$job) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Job not found."),
            );
        }


        if (!$job->isEditable()) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this job."),
            );
        }


        $companyOptions = $this->companyOptions();

        if (empty($companyOptions)) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('You can not edit this job. You have to create the company first, in order to create a job.'),
            );
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');

        //populate industry
        $industries = Engine_Api::_()->getItemTable('ynjobposting_industry')->getIndustries();
        unset($industries[0]);

        if (sizeof($industries) <= 0) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Create job require at least one industry. Please contact admin for more details.')
            );
        }
        $values = $this->mapAddJobFields($aData);

        $table = Engine_Api::_()->getItemTable('ynjobposting_job');

        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            if ($values['negotiable']) {
                $values['salary_from'] = null;
                $values['salary_to'] = null;
            }

            $job->setFromArray($values);

            if (isset($values['end'])) {
                if ($values['end']) {
                    $job->status = 'ended';
                } else {
                    if ($job->status == 'ended') $job->status = 'published';
                }
            }
            $job->save();

            //set auth for view, comment
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
            $auth_arr = array('view', 'comment');
            foreach ($auth_arr as $elem) {
                $auth_role = $values[ $elem ];
                if (!$auth_role) {
                    $auth_role = 'everyone';
                }
                $roleMax = array_search($auth_role, $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($job, $role, $elem, ($i <= $roleMax));
                }
            }

            // Add tags
            if (isset($values['tags']) && $values['tags']) {
                $tags = preg_split('/[,]+/', $values['tags']);
                $job->tags()->setTagMaps($viewer, $tags);
            }

            $search_table = Engine_Api::_()->getDbTable('search', 'core');
            $select = $search_table->select()->where('type = ?', 'ynjobposting_job')->where('id = ?', $job->getIdentity());
            $row = $search_table->fetchRow($select);
            if ($row) {
                $row->keywords = $values['tags'];
                $row->save();
            } else {
                $row = $search_table->createRow();
                $row->type = 'ynjobposting_job';
                $row->id = $job->getIdentity();
                $row->title = $job->title;
                $row->description = $job->description;
                $row->keywords = $values['tags'];
                $row->save();
            }

            //send notification
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notifyApi->addNotification($job->getOwner(), $job, $job, 'ynjobposting_job_edited');

            $savedList = $job->getAllSaved();
            foreach ($savedList as $saved) {
                $user = Engine_Api::_()->user()->getUser($saved->user_id);
                if ($user) {
                    $notifyApi->addNotification($user, $job, $job, 'ynjobposting_job_edited');
                }
            }

            $appliedList = $job->getAllApplied();
            foreach ($appliedList as $applied) {
                $user = Engine_Api::_()->user()->getUser($applied->user_id);
                if ($user) {
                    $notifyApi->addNotification($user, $job, $job, 'ynjobposting_job_edited');
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();

            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Could not save job"),
                'error_debug'   => $e->getMessage(),
                'aData'         => $aData,
                'values'        => $values
            );
        }

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("Saved job successfully."),
            'iJobId'     => $job->getIdentity(),
        );
    }

    public function getEducationOptions()
    {
        return array(
            'highschool' => 'Highschool Diploma',
            'associated' => 'Associated Degree',
            'bachelor'   => 'Bachelor Degree',
            'master'     => 'Master Degree',
            'doctorate'  => 'Doctorate Degree'
        );
    }

    public function educationOptions()
    {
        $educationOptions = array();
        foreach ($this->getEducationOptions() as $k => $v) {
            $educationOptions[] = array(
                'id'    => $k,
                'title' => $v,
            );
        }

        return $educationOptions;
    }

    public function levelOptions()
    {
        $levelOptions = array();
        $tableLevel = Engine_Api::_() -> getDbTable('joblevels', 'ynjobposting');
        $jobLevels = $tableLevel -> getJobLevelArray();
        foreach ($jobLevels as $k => $v) {
            $levelOptions[] = array(
                'id'    => $k,
                'title' => $v,
            );
        }

        return $levelOptions;
    }

    public function typeOptions()
    {
        $typeOptions = array();
        $tableType = Engine_Api::_() -> getDbTable('jobtypes', 'ynjobposting');
        $jobTypes = $tableType -> getJobTypeArray();
        foreach ($jobTypes as $k => $v) {
            $typeOptions[] = array(
                'id'    => $k,
                'title' => $v,
            );
        }

        return $typeOptions;
    }

    public function currencyOptions()
    {
        $currencyOptions = array();
        foreach ($this->getCurrencyOptions() as $k => $v) {
            $currencyOptions[] = array(
                'id'         => $k,
                'title'      => $v,
                'is_default' => ($k == 'USD') ? 1 : 0,
            );
        }

        return $currencyOptions;
    }

    public function industryOptions()
    {
        $industryOptions = array();
        foreach ($this->getIndustryOptions() as $industry) {
            $industryOptions[] = array(
                'id'    => $industry['industry_id'],
                'title' => str_repeat("-- ", $industry['level'] - 1) . $industry['title'],
            );
        }

        return $industryOptions;
    }

    public function companyOptions()
    {
        $options = array();
        $companies = Engine_Api::_()->getItemTable('ynjobposting_company')->getMyCompanies();
        foreach ($companies as $k => $v) {
            $options[] = array(
                'id'    => $k,
                'title' => $v
            );
        }

        return $options;
    }

    public function remove_applied_job($aData)
    {
        extract($aData);

        $iJobId = intval($iJobId);


        $job = Engine_Api::_()->getItem('ynjobposting_job', $iJobId);

        if (!$job) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Job not found.')
            );
        }

        $table = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
        $viewer = Engine_Api::_()->user()->getViewer();

        $select = $table->select()->where('job_id = ?', $iJobId)->where('user_id = ?', $viewer->getIdentity());

        $results = $table->fetchAll($select);
        foreach ($results as $row) {
            $row->owner_deleted = 1;
            $row->save();
        }

        $view = Zend_Registry::get('Zend_View');

        return array(
            'error_code' => 0,
            'message'    => $view->translate('Removed job successfully'),
        );
    }


    public function remove_saved_job($aData)
    {
        extract($aData);

        $iJobId = intval($iJobId);


        $job = Engine_Api::_()->getItem('ynjobposting_job', $iJobId);

        if (!$job) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_('Job not found.')
            );
        }

        $table = Engine_Api::_()->getDbTable('savejobs', 'ynjobposting');
        $viewer = Engine_Api::_()->user()->getViewer();

        $select = $table->select()->where('job_id = ?', $iJobId)->where('user_id = ?', $viewer->getIdentity());

        $results = $table->fetchAll($select);
        foreach ($results as $row) {
            $row->delete();
        }

        $view = Zend_Registry::get('Zend_View');

        return array(
            'error_code' => 0,
            'message'    => $view->translate('Removed job successfully'),
        );
    }

    public function form_search_applications($aData)
    {
        extract($aData);


        return array(
            'job_options' => $this->jobOptions($aData['iCompanyId']),
        );
    }

    public function fetch_applications($aData)
    {
        extract($aData);

        $iCompanyId = intval($iCompanyId);
        $iJobId = intval($iJobId);

        $company = Engine_Api::_()->getItem('ynjobposting_company', $iCompanyId);
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$company) {
            return array();
        }

        if (!$company->isEditable()) {
            return array();
        }


        $applyTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
        $applyTblName = $applyTbl->info('name');
        $companyTbl = Engine_Api::_()->getItemTable('ynjobposting_company');
        $jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');


        $select = $applyTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('apply' => $applyTbl->info('name')), 'apply.*')
            ->join(array('job' => $jobTbl->info('name')), 'apply.job_id= job.job_id', '')
            ->join(array('company' => $companyTbl->info('name')), 'company.company_id=job.company_id', '');

        if ($iCompanyId) {
            $select->where('company.company_id=?', $iCompanyId);
        }

        if ($iJobId) {
            $select->where('job.job_id=?', $iJobId);
        }

        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields = array('listing'));
    }

    public function application_info($aData)
    {
        extract($aData);

        $iApplicationId = intval($iApplicationId);

        $applyTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');

        $apply = $applyTbl->fetchRow(array(
            'jobapply_id = ?' => $iApplicationId
        ));

        if (!$apply) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Application not found."),
            );
        }

        $job = $apply->getJob();

        if (!$job) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Job not found."),
            );
        }

        $company = $job->getCompany();

        if (!$company) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Company not found."),
            );
        }

        if (!$company->isEditable()) {
            array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("You don't have permission to view this application"),
            );
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        return Ynmobile_AppMeta::_export_one($apply, array('infos'));
    }

    /**
     * $iApplicationId
     */
    public function getApplicationById($iApplicationId)
    {
        $applyTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');

        return $applyTbl->fetchRow(array(
            'jobapply_id = ?' => $iApplicationId
        ));
    }

    public function reject_application($aData)
    {
        extract($aData);

        $viewer = Engine_Api::_()->user()->getViewer();
        $iApplicationId = intval($iApplicationId);

        $apply = $this->getApplicationById($iApplicationId);

        if (!$apply) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_("This application doesn't exist.")
            );
        }

        try {
            $apply->status = 'rejected';
            $apply->save();

        } catch (Exception $e) {

            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Your request could not be complete"),
            );
        }

        $apply = $this->getApplicationById($iApplicationId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("The application has been rejected."),
            'aItem'      => Ynmobile_AppMeta::_export_one($apply, array('infos')),
        );
    }

    public function pass_application($aData)
    {
        extract($aData);

        $viewer = Engine_Api::_()->user()->getViewer();
        $iApplicationId = intval($iApplicationId);

        $apply = $this->getApplicationById($iApplicationId);

        if (!$apply) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_("This application doesn't exist.")
            );
        }

        try {
            $apply->status = 'passed';
            $apply->save();

        } catch (Exception $e) {

            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Your request could not be complete"),
            );
        }

        $apply = $this->getApplicationById($iApplicationId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("The application has been set passed."),
            'aItem'      => Ynmobile_AppMeta::_export_one($apply, array('infos')),
        );
    }

    public function delete_application($aData)
    {
        extract($aData);

        $viewer = Engine_Api::_()->user()->getViewer();
        $iApplicationId = intval($iApplicationId);

        $apply = $this->getApplicationById($iApplicationId);

        if (!$apply) {
            return array(
                'error_code'    => 1,
                'error_message' => Zend_Registry::get('Zend_Translate')->_("This application doesn't exist.")
            );
        }

        try {
            $apply->delete();

        } catch (Exception $e) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Your request could not be complete"),
            );
        }

        $apply = $this->getApplicationById($iApplicationId);

        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("The application has been deleted."),
        );
    }

    public function subscribe($aData)
    {
        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("The feature is coming soon."),
        );
    }

    public function unsubscribe($aData)
    {
        return array(
            'error_code' => 0,
            'message'=>Zend_Registry::get('Zend_Translate') -> _("The feature is coming soon."),
        );
    }

    public function jobOptions($iCompanyId)
    {
        $table = Engine_Api::_()->getItemTable('ynjobposting_job');
        $select = $table->select();
        $select->where('company_id=?', intval($iCompanyId));
        $select->where('status <> ?', 'deleted');

        $options = array();

        foreach ($table->fetchAll($select) as $row) {
            $options[] = array(
                'id'    => $row->getIdentity(),
                'title' => $row->getTitle(),
            );
        }

        return $options;
    }


    public function getCurrencyOptions()
    {
        //populate currency
        $supportedCurrencies = array();


        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');

        $select = $gatewaysTable->select()->where('enabled=?',1);


        foreach ($gatewaysTable->fetchAll() as $gateway) {
            $gateways[ $gateway->gateway_id ] = $gateway->title;
            $gatewayObject = $gateway->getGateway();

            $currencies = $gatewayObject->getSupportedCurrencies();


            if (empty($currencies)) {
                continue;
            }
            $supportedCurrencyIndex[ $gateway->title ] = $currencies;
            if (empty($fullySupportedCurrencies)) {
                $fullySupportedCurrencies = $currencies;
            } else {
                $fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
            }
            $supportedCurrencies = array_merge($supportedCurrencies, $currencies);
        }
        $supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);

        return array_merge(array_combine($fullySupportedCurrencies, $fullySupportedCurrencies), array_combine($supportedCurrencies, $supportedCurrencies));
    }

    public function getIndustryOptions()
    {
        $industries = Engine_Api::_()->getItemTable('ynjobposting_industry')->getIndustries();
        unset($industries[0]);

        return $industries;
    }


    public function getLevelOptions()
    {
        return array(
            'entry_level'    => 'New Grad/Entry Level',
            'experienced'    => 'Experienced (Non-Manager)',
            'supervisor'     => 'Team Leader/Supervisor',
            'manager'        => 'Manager',
            'vice_director'  => 'Vice Director',
            'director'       => 'Director',
            'CEO'            => 'CEO',
            'vice_president' => 'Vice President',
            'president'      => 'President');
    }

    public function getTypeOptions()
    {
        return array(
            'full_time'  => 'Full-time',
            'part_time'  => 'Part-time',
            'unpaid'     => 'Unpaid',
            'internship' => 'Internship',
            'contractor' => 'Contractor',
            'freelancer' => 'Freelancer'
        );
    }


    public function viewOptions()
    {

        $id = Engine_Api::_()->user()->getViewer()->level_id;

        // privacy
        $availableLabels = array(
            'everyone'     => 'Everyone',
            'registered'   => 'All Registered',
            'network'      => 'My Network',
            'owner_member' => 'My Friends',
            'owner'        => 'Only Me'
        );

        // comment
        $commentOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynjobposting_job', $id, 'auth_view');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

        $options = array();

        foreach ($commentOptions as $k => $v) {
            $options[] = array('id' => $k, 'title' => $v);
        }

        return $options;

    }

    public function commentOptions()
    {

        $id = Engine_Api::_()->user()->getViewer()->level_id;

        // privacy
        $availableLabels = array(
            'everyone'     => 'Everyone',
            'registered'   => 'All Registered',
            'network'      => 'My Network',
            'owner_member' => 'My Friends',
            'owner'        => 'Only Me'
        );

        // comment
        $commentOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynjobposting_job', $id, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

        $options = array();

        foreach ($commentOptions as $k => $v) {
            $options[] = array('id' => $k, 'title' => $v);
        }

        return $options;

    }

    public function delete_note($aData)
    {
        extract($aData);

        $iNoteId = intval($iNoteId);


        $noteTbl = Engine_Api::_()->getDbTable('applynotes', 'ynjobposting');
        $note = $noteTbl->fetchRow(array(
                'applynote_id=?' => $iNoteId,
            )
        );

        if ($note) {
            $note->delete();
        }

        return array(
            'error_code' => 0,
            'iNoteId'    => $iNoteId,
        );
    }


    public function add_note($aData)
    {
        extract($aData);
        $iApplicationId = intval($iApplicationId);
        $sContent = (string)$sContent;
        $viewer = Engine_Api::_()->user()->getViewer();

        $apply = $this->getApplicationById($iApplicationId);

        if (!$sContent || !$apply) {
            return array(
                'error_code'    => 1,
                'error_message'=>Zend_Registry::get('Zend_Translate') -> _("Missing data"),
            );
        }


        $noteTbl = Engine_Api::_()->getDbTable('applynotes', 'ynjobposting');
        $note = $noteTbl->createRow();
        $note->setFromArray(array(
            'creation_date' => date('Y-m-d H:i:s'),
            'jobapply_id'   => $apply->getIdentity(),
            'user_id'       => $viewer->getIdentity(),
            'content'       => $sContent
        ));
        $note->save();

        return array(
            'error_code' => 0,
            'iNoteId'    => $note->getIdentity(),
        );
    }
}
