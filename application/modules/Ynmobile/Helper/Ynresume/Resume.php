<?php
/**
 * Created by IntelliJ IDEA.
 * User: macpro
 * Date: 8/10/15
 * Time: 12:02 PM
 */
class Ynmobile_Helper_Ynresume_Resume extends Ynmobile_Helper_Base
{

    /*
     *
    [1] => user_id
    [2] => photo_id
    [3] => name
    [4] => summary
    [5] => headline
    [6] => title
    [7] => company
    [8] => industry_id
    [9] => birth_day
    [10] => marial_status
    [11] => gender
    [12] => nationality
    [13] => phone
    [14] => email
    [15] => location
    [16] => longitude
    [17] => latitude
    [18] => contact_location
    [19] => contact_longitude
    [20] => contact_latitude
    [21] => theme
    [22] => search
    [23] => featured
    [24] => serviced
    [25] => feature_expiration_date
    [26] => service_expiration_date
    [27] => view_count
    [28] => endorse_count
    [29] => favourite_count
    [30] => active
    [31] => creation_date
    [32] => modified_date
     */

    public function getYnmobileApi()
    {
        return Engine_Api::_()->getApi('ynresume', 'ynmobile');
    }

    function field_id(){
        return $this->data['iResumeId'] =  $this->entry->getIdentity();
    }

    function field_listing() {
        $this->field_id();
        $this->field_type();
        $this->field_imgFull();
        $this->field_imgNormal();
        $this->field_user();
        $this->field_permissions();

        $subject = $resume = $this->entry;

        $this->data['sName'] = $resume->name;
        $this->data['sSummary'] = $resume->summary;
        $this->data['sTitle'] = $resume->title;
        $this->data['sCompany'] = $resume->company;
        $this->data['sLocation'] = $resume->location;
        $this->data['bIsSaved'] = $resume->hasSaved()?1:0;
        $this->data['bIsFavourited'] = $resume->hasFavourited()?1:0;

        $industry = $resume->getIndustry();
        $this->data['sIndustry'] = $industry ? $industry->getTitle() : 'Unknown Industry';

    }

    function field_permissions() {

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $resume = $this->entry;

        $this->data['bCanSave'] = $this->data['bCanFavourite'] = $viewer->isSelf($resume->getOwner())?0:1;
    }

    function field_currentExperiences() {

        $subject = $resume = $this->entry;
        $tableExperiences = Engine_Api::_() -> getDbTable('experiences', 'ynresume');

        // get current experience
        $currentExperiences = $tableExperiences -> getExperiencesByResumeId($resume -> getIdentity(), true, 3);
        if(count($currentExperiences) > 0)
        {
            $business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
            $experiences = array();
            foreach ($currentExperiences as $experience){
                $business = null;
                if ($experience->business_id) {
                    $business = ($business_enable) ? Engine_Api::_()->getItem('ynbusinesspages_business', $experience->business_id) : null;
                }
                if ($business && !$business->deleted) {
                    $experiences[] = $business->getTitle();
                }else{
                    $experiences[] = $experience -> company;
                }
            }
            $this->data['sCurrentExperiences'] = implode(", ", $experiences);
        }
    }

    function field_previousExperiences() {

        $subject = $resume = $this->entry;
        $tableExperiences = Engine_Api::_() -> getDbTable('experiences', 'ynresume');

        // get previous experience
        $previousExperiences = $tableExperiences -> getExperiencesByResumeId($resume -> getIdentity(), false, 3);
        if(count($previousExperiences) > 0)
        {
            $business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
            $experiences_arr = array();
            foreach ($previousExperiences as $experience){
                $business = null;
                if ($experience->business_id) {
                    $business = ($business_enable) ? Engine_Api::_()->getItem('ynbusinesspages_business', $experience->business_id) : null;
                }
                if ($business && !$business->deleted) {
                    $experiences_arr[] = $business->getTitle();
                }else{
                    $experiences_arr[] = $experience -> company;
                }
            }
            $this->data['sPreviousExperiences'] = implode(", ", $experiences_arr);
        }
    }

    function field_educationTitle() {

        $subject = $resume = $this->entry;
        // get education
        $tableEducations = Engine_Api::_() -> getDbTable('educations', 'ynresume');
        $educations = $tableEducations -> getEducationsByResumeId($resume -> getIdentity(), 3);
        if(count($educations) > 0)
        {
            $educations_arr = array();
            foreach ($educations as $education){
                $educations_arr[] = $education -> title;
            }
            $this->data['sEducation'] = implode(", ", $educations_arr);
        }
    }

    function field_detail() {

        $this->field_listing();
        $this->field_currentExperiences();
        $this->field_previousExperiences();
        $this->field_educationTitle();
        $this->field_contact_info();
        $this->field_awards();
        $this->field_skills();
        $this->field_experiences();
        $this->field_education();
        $this->field_publication();
        $this->field_languages();
        $this->field_projects();
        $this->field_certifications();
        $this->field_courses();
        $this->field_recommendations();
        $this->field_section_permissions();
    }

    function field_section_permissions(){

        $resume =$this->entry;
        $allSections = Engine_Api::_()->ynresume()->getAllSectionsAndGroups();
        if (isset($allSections['general_info'])) unset($allSections['general_info']);
        if (isset($allSections['photo'])) unset($allSections['photo']);
        $aPermissions = array();

        foreach ($allSections as $key => $section) {

            $can_view = $resume->authorization()->isAllowed(null, $key);
            if (strpos($key, 'field_') !== FALSE) {
                $can_view = true;
            }

            $aPermissions[$key] = $can_view;
        }
        $this->data['aPermissions'] = $aPermissions;
    }

    public function field_contact_info() {

        $resume = $this->entry;
        $view = Zend_Registry::get('Zend_View');
        // convert birthday to text
        $birthDayObject = null;
        $date = '';
        if (!is_null($resume -> birth_day) && !empty($resume -> birth_day) && $resume -> birth_day)
        {
            $birthDayObject = new Zend_Date(strtotime($resume -> birth_day));
        }
        if(!is_null($birthDayObject))
        {
            $date = date('F d, Y', $birthDayObject -> getTimestamp());
        }

        $this->data['sBirthday'] = $date;
        $this->data['sGender'] = ($resume->gender)? $view -> translate("Male") : $view -> translate("Female");
        $this->data['sMaritalStatus'] = ($resume->marial_status)? $view -> translate("Single") : $view -> translate("Married");
        $this->data['sNationality'] = $resume->nationality;
        $this->data['sEmail'] = $resume->email;
        $this->data['sPhone'] = $resume->phone;
        $this->data['sAddress'] = $resume->contact_location;
    }

    function field_experiences(){
        $result = array();
        $resume = $this->entry;
        $items = $resume->getAllExperience();
        foreach ($items as $item) {
            $result[] = Ynmobile_AppMeta::_export_one($item, array('listing'));
        }

        $this->data['aExperiences'] = $result;
    }

    function field_skills(){

        $result = array();
        $resume = $this->entry;
        $skills = $resume->getAllSkills(false);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $viewerId = $viewer -> getIdentity();
        foreach ($skills as $key=>$skill) {
            $result[$key]['iSkillId'] = $skill['skill_id'];
            $result[$key]['sTitle'] = $skill['text'];
            $result[$key]['iTotalEndorses'] = count($skill['endorses']);
            $result[$key]['bIsEndorsed'] = (in_array($viewerId, $skill['endorsed_user_ids'])) ? 1: 0;
            $result[$key]['aEndorsesUsers'] = array();
            foreach ($skill['endorsed_user_ids'] as $user_id) {

                $user = Engine_Api::_() -> getItem('user', $user_id);

                if($user -> getIdentity()) {
                    $userResume = Engine_Api::_() -> ynresume() -> getUserResume($user -> getIdentity());

                    // there is a resume of this user
                    if(!empty($userResume)) {

                        $result[$key]['aEndorsesUsers'][] = Ynmobile_AppMeta::_export_one($userResume, array('simple_array'));
                    } else {
                        $result[$key]['aEndorsesUsers'][] = Ynmobile_AppMeta::_export_one($user, array('simple_array'));
                    }
                }
            }
        }

        $this->data['aSkills'] = $result;
    }

    function field_education(){

        $result = array();
        $resume = $this->entry;
        $items = $resume->getAllEducation();
        foreach ($items as $item) {
            $result[] = Ynmobile_AppMeta::_export_one($item, array('listing'));
        }

        $this->data['aEducation'] = $result;
    }

    function field_publication() {
        $result = array();
        $resume = $this->entry;
        $items = $resume->getAllPublication();
        foreach ($items as $item) {
            $result[] = Ynmobile_AppMeta::_export_one($item, array('listing'));
        }

        $this->data['aPublication'] = $result;
    }

    function field_languages() {
        $result = array();
        $resume = $this->entry;
        $items = $resume->getAllLanguage();
        foreach ($items as $item) {
            $result[] = Ynmobile_AppMeta::_export_one($item, array('listing'));
        }

        $this->data['aLanguages'] = $result;
    }

    function field_projects() {
        $result = array();
        $resume = $this->entry;
        $items = $resume->getAllProject();
        foreach ($items as $item) {
            $result[] = Ynmobile_AppMeta::_export_one($item, array('listing'));
        }

        $this->data['aProjects'] = $result;
    }

    function field_certifications() {
        $result = array();
        $resume = $this->entry;
        $items = $resume->getAllCertification();
        foreach ($items as $item) {
            $result[] = Ynmobile_AppMeta::_export_one($item, array('listing'));
        }

        $this->data['aCertifications'] = $result;
    }

    function field_courses() {
        $result = array();
        $resume = $this->entry;
        $courseTbl = Engine_Api::_()->getItemTable('ynresume_course');

        // get courses that are not belong to any education or experience
        $courses = $resume->getAllCourse();
        $orphanCourses = array();
        foreach($courses as $c)
        {
            if ($c->associated_id == '0')
            {
                $orphanCourses[] = $c;
            }
        }

        $education = $resume -> getAllEducation();
        $experience = $resume -> getAllExperience();

        foreach ($education as $edu) {
            $resultItem = array();
            $courses = $courseTbl -> getCoursesByEducation($edu);
            if (count($courses)) {
                $resultItem['sAssociate'] = $edu->title;
                $resultItem['aCourses'] = array();
                foreach ($courses as $item) {
                    $resultItem['aCourses'][] = Ynmobile_AppMeta::_export_one($item, array('listing'));
                }
                $result[] = $resultItem;
            }
        }

        foreach ($experience as $exp) {
            $resultItem = array();
            $courses = $courseTbl -> getCoursesByExperience($exp);
            if (count($courses)) {
                $resultItem['sAssociate'] = $exp->title;
                $resultItem['aCourses'] = array();
                foreach ($courses as $item) {
                    $resultItem['aCourses'][] = Ynmobile_AppMeta::_export_one($item, array('listing'));
                }
                $result[] = $resultItem;
            }
        }

        if (count($orphanCourses)) {
            $view = Zend_Registry::get('Zend_View');
            $resultItem = array();
            $resultItem['sAssociate'] = $view->translate("Others");
            $resultItem['aCourses'] = array();
            foreach ($orphanCourses as $item) {
                $resultItem['aCourses'][] = Ynmobile_AppMeta::_export_one($item, array('listing'));
            }
            $result[] = $resultItem;
        }

        $this->data['aAssociates'] = $result;
    }

    function field_recommendations() {
        $result = array();

        $item = $receiver = $resume = $this->entry;
        $receivedRecommendations = Engine_Api::_()->getDbTable('recommendations','ynresume')->getReceivedRecommendations($receiver->user_id);
        $view = Zend_Registry::get("Zend_View");

        if (count($receivedRecommendations)) {
            $occupations = Engine_Api::_()->ynresume()->getOccupations($receiver->user_id);
            foreach ($occupations as $occupation) {
                $recommendations = Engine_Api::_()->ynresume()->getShowRecommendationsOfOccupation($occupation['type'], $occupation['item_id'], $receiver->user_id);
                if (count($recommendations)) {
                    foreach ($recommendations as $recommendation) {
                        $giver = $recommendation->getGiver();
                        $resultItem = Ynmobile_AppMeta::_export_one($recommendation, array('listing'));
                        $resultItem['oGiver'] = Ynmobile_AppMeta::_export_one($giver, array('listing'));
                        $giverLink = '<a href="#/app/' . $giver->getType() . '/' . $giver->getIdentity() . '">' . $giver->getTitle() . '</a>';
                        $receiverLink = '<a href="#/app/' . $receiver->getType() . '/' . $receiver->getIdentity() . '">' . $receiver->getTitle() . '</a>';
                        $place = Engine_Api::_()->ynresume()->getPlace($recommendation->receiver_position_type, $recommendation->receiver_position_id);
                        $occupationLink = '<a>' . $place . '</a>';
                        if ($recommendation->relationship != 'senior_to') {
                            $relationshipText = $view->translate('YNRESUME_RELATIONSHIP_SHOW_'.strtoupper($recommendation->relationship), $giverLink, $receiverLink, $occupationLink);
                        } else {
                            $relationshipText = $view->translate('YNRESUME_RELATIONSHIP_SHOW_'.strtoupper($recommendation->relationship), $receiverLink, $giverLink, $occupationLink);
                        }
                        $resultItem['sRelation'] = $relationshipText;
                        $result[] = $resultItem;
                    }
                }
            }
        }

        $this->data['aRecommendations'] = $result;
    }

    function field_awards() {
        $result = array();
        $resume = $this->entry;
        $items = $resume->getAllAwards();
        foreach ($items as $item) {
            $result[] = Ynmobile_AppMeta::_export_one($item, array('listing'));
        }

        $this->data['aAwards'] = $result;
    }

    public function field_as_attachment(){
        $this->field_listing();
        $this->field_currentExperiences();
        $this->field_previousExperiences();
        $this->field_educationTitle();
    }

    function field_simple_array() {
        $this->data['id'] =  $this->entry->getIdentity();
        $this->data['type'] =  $this->entry->getType();
        $this->data['title'] =  $this->entry->getTitle();

        $type  = 'thumb.profile';

        $url = $this->entry->getPhotoUrl($type);

        $this->data['img'] =  $url?$this->finalizeUrl($url):$this->getNoImg($type);
    }
}