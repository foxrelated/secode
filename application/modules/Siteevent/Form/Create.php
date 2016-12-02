<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Create extends Engine_Form {

    public $_error = array();
    protected $_defaultProfileId;
    protected $_parent_type;
    protected $_parent_id;
    protected $_host;
    protected $_quick;

//  protected $_organizer;

    public function getDefaultProfileId() {
        return $this->_defaultProfileId;
    }

    public function getHost() {
        return $this->_host;
    }

//  public function getOrganizer() {
//    return $this->_organizer;
//  }

    public function setQuick($flage) {
        $this->_quick = $flage;
        return $this;
    }

    public function getQuick() {
        return $this->_quick;
    }

//  public function getOrganizer() {
//    return $this->_organizer;
//  }

    public function setHost($host) {
        $this->_host = $host;
        return $this;
    }

//  public function setOrganizer($organizer) {
//    $this->_organizer = $organizer;
//    return $this;
//  }
    public function setDefaultProfileId($default_profile_id) {
        $this->_defaultProfileId = $default_profile_id;
        return $this;
    }

    public function setParent_type($value) {
        $this->_parent_type = $value;
    }

    public function setParent_id($value) {
        $this->_parent_id = $value;
    }

    public function init() {
        $user = Engine_Api::_()->user()->getViewer();
        $viewer_id = $user->getIdentity();
        $this->loadDefaultDecorators();
        //PACKAGE ID
        $package_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);

        $note = '';
        $seaocoreCalenderDayStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.calendar.daystart', 1);
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $reviewApi = Engine_Api::_()->siteevent();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        //PACKAGE BASED CHECKS
        $hasPackageEnable = Engine_Api::_()->siteevent()->hasPackageEnable();
        if (!$hasPackageEnable) {
            $this->setTitle("Create New Event");
        }
        $this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Compose your new event below, then click 'Create' to publish the event.")))
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'siteevents_create')
                ->getDecorator('Description')->setOption('escape', false);

        if ($this->getQuick()) {
            $this->setAttrib('id', 'siteevents_create_quick');
            $this->setDescription('');
        } else {
            $this->setAttrib('id', 'siteevents_create_form');
        }

        $siteeventRepeatEventsTypeInfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventrepeatevents.type.info', null);
        $siteeventrepeatLsettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventrepeat.lsettings', null);
        $siteeventRepeatGetShowViewType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventrepeat.getshow.viewtype', null);
        $expertTipsContent = strip_tags(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.experttips'));
        $expertTipsContent = str_replace('&nbsp;', '', $expertTipsContent);
        $eventRepeatHostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $tempEventRepeatTypeInfo = @md5($eventRepeatHostType . $siteeventrepeatLsettings);
        $expertTipsContent = trim($expertTipsContent);
        if ($expertTipsContent) {
            $this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_('Compose your new event below, then click "Create" to publish the event. %1$sExpert Tips%2$s'), "<span class='siteevent_link_wrap'><i class='siteevent_icon icon_siteevent_tip mright5'></i><a href='javascript:void(0)' onclick='expertTips()'>", "</a></span>"));
        }

        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);

        $createFormFields = array(
            'venue',
            'location',
            'tags',
            'photo',
            'description',
            'overview',
            'price',
            'host',
            'viewPrivacy',
            'commentPrivacy',
            'postPrivacy',
            'discussionPrivacy',
            'photoPrivacy',
            'videoPrivacy',
            'rsvp',
            'invite',
            'status',
            'search',
            'guestLists'
        );

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument') || (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => "siteevent_event", 'item_module' => 'siteevent')))) {
            $createFormFields = array_merge($createFormFields, array('document'));
        }

        if (empty($event_id) && Engine_Api::_()->getApi('settings', 'core')->hasSetting('siteevent.createFormFields')) {

            $createFormFields = $settings->getSetting('siteevent.createFormFields', $createFormFields);
        }

        if (Engine_Api::_()->siteevent()->isTicketBasedEvent() && in_array('rsvp', $createFormFields)) {
            $indexRSVP = array_search('rsvp', $createFormFields);
            unset($createFormFields[$indexRSVP]);
        }

        if (Engine_Api::_()->siteevent()->isTicketBasedEvent() && in_array('invite', $createFormFields)) {
            $indexInvite = array_search('invite', $createFormFields);
            unset($createFormFields[$indexInvite]);
        }

        $this->addElement('Text', 'title', array(
            'label' => "Event Title",
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            //new Engine_Filter_StringLength(array('max' => '63')),
        )));

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $onChangeEvent = "showFields($(this).value, 1); subcategories(this.value, '', '');";
            $categoryFiles = 'application/modules/Siteevent/views/scripts/_formSubcategory.tpl';
            $locationJs = "en4.siteevent.create.is_online(false)";
        } else {

            $onChangeEvent = "showSMFields(this.value, 1);sm4.core.category.set(this.value, 'subcategory');";
            $categoryFiles = 'application/modules/Siteevent/views/sitemobile/scripts/_subCategory.tpl';

            $locationJs = "siteeventCreateIsOnline(false)";
        }

        if (!empty($createFormFields) && in_array('venue', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.veneuname', 1)) {
            $this->addElement('Text', 'venue_name', array(
                'label' => "Venue Name",
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
                //new Engine_Filter_StringLength(array('max' => '63')),
            )));

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.onlineevent.allow', 1) || ($this->_item && $this->_item->is_online == 1)) {
                if (!$this->_item)
                    $text_online = 'This event will run as an online event, so venue name will not appear for this event. %1$s+ Add a location%2$s';
                else
                    $text_online = 'This event will run as an online event, so venue name will not appear for this event. %1$s+ Add a venue%2$s';
                $this->addElement('Dummy', 'online_events', array(
                    'label' => (!$this->_item) ? "Location" : 'Venue Name',
                    'content' => sprintf(Zend_Registry::get('Zend_Translate')->_($text_online), '<a href="javascript:void(0);" onclick=' . "$locationJs" . '>', '</a>')
                ));
            }
        }
        $this->addElement('Hidden', 'is_online', array('value' => 0, 'order' => 1000));
        if (!empty($createFormFields) && in_array('location', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)) {
            $this->addElement('Text', 'location', array(
                'label' => 'Location',
                'description' => 'Eg: Fairview Park, Berkeley, CA',
                'placeholder' => $view->translate('Enter a location'),
                'autocomplete' => 'off',
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
            )));
            $this->location->getDecorator('Description')->setOption('placement', 'append');
            $this->addElement('Hidden', 'locationParams', array('order' => 800000));


            include_once APPLICATION_PATH . '/application/modules/Seaocore/Form/specificLocationElement.php';
        }

        $user = Engine_Api::_()->user()->getViewer();
        $user_level = Engine_Api::_()->user()->getViewer()->level_id;

        $defaultProfileId = "0_0_" . $this->getDefaultProfileId();
        $translate = Zend_Registry::get('Zend_Translate');
        $editFullEventDate = true;
        if ($this->_item)
            $editFullEventDate = $this->getFullEventDate();

        if (!$this->_item || (isset($this->_item->category_id) && empty($this->_item->category_id)) || ($this->_item && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.categoryedit', 1))) {
            $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);
            if (count($categories) != 0) {
                $categories_prepared[0] = "";
                foreach ($categories as $category) {
                    $categories_prepared[$category->category_id] = $translate->translate($category->category_name);
                }

                $this->addElement('Select', 'category_id', array(
                    'label' => 'Category',
                    'allowEmpty' => false,
                    'required' => true,
                    'multiOptions' => $categories_prepared,
                    'onchange' => $onChangeEvent,
                ));

                $this->addElement('Select', 'subcategory_id', array(
                    'RegisterInArrayValidator' => false,
                    'allowEmpty' => true,
                    'required' => false,
                ));

                $this->addElement('Select', 'subsubcategory_id', array(
                    'RegisterInArrayValidator' => false,
                    'allowEmpty' => true,
                    'required' => false,
                ));

                $this->addDisplayGroup(array(
                    'subcategory_id',
                    'subsubcategory_id',
                        ), 'Select', array(
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => $categoryFiles,
                                'class' => 'form element')))
                ));
            }
        }

        if (!$this->_item) {
            $customFields = new Siteevent_Form_Custom_Standard(array(
                'item' => 'siteevent_event',
                'decorators' => array(
                    'FormElements'
            )));
        } else {
            $customFields = new Siteevent_Form_Custom_Standard(array(
                'item' => $this->getItem(),
                'decorators' => array(
                    'FormElements'
            )));
        }

        //START PACKAGE WORK
        if ($hasPackageEnable) {
            $packageId = Zend_Controller_Front::getInstance()->getRequest()->getParam('id');
            $packageObject = Engine_Api::_()->getItem('siteeventpaid_package', $packageId);
            if (!empty($packageObject))
                $profileField_level = $packageObject->profile;
            else
                $profileField_level = 1;

            if ($profileField_level == 2) {
                $fieldsProfile = array("0_0_1", "submit");

                $field_id = array();
                $fieldsProfile_2 = Engine_Api::_()->siteeventpaid()->getProfileFields();
                $fieldsProfile = array_merge($fieldsProfile, $fieldsProfile_2);

                foreach ($fieldsProfile_2 as $k => $v) {
                    $explodeField = explode("_", $v);
                    $field_id[] = $explodeField['2'];
                }

                $elements = $customFields->getElements();
                foreach ($elements as $key => $value) {
                    $explode = explode("_", $key);
                    if ($explode['0'] != "1" && $explode['0'] != "submit") {
                        if (in_array($explode['0'], $field_id)) {
                            $field_id[] = $explode['2'];
                            $fieldsProfile[] = $key;
                            continue;
                        }
                    }

                    if (!in_array($key, $fieldsProfile)) {
                        $customFields->removeElement($key);
                        $customFields->addElement('Hidden', $key, array(
                            "value" => "",
                        ));
                    }
                }
            } elseif ($profileField_level == 0) {
                $elements = $customFields->getElements();
                foreach ($elements as $key => $value) {
                    $customFields->removeElement($key);
                    $customFields->addElement('Hidden', $key, array(
                        "value" => "",
                    ));
                }
            }
        }

        //END PACKAGE WORK

        $customFields->removeElement('submit');
        if ($customFields->getElement($defaultProfileId)) {
            $customFields->getElement($defaultProfileId)
                    ->clearValidators()
                    ->setRequired(false)
                    ->setAllowEmpty(true);
        }

        $this->addSubForms(array(
            'fields' => $customFields
        ));

        if (!empty($createFormFields) && in_array('tags', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.tags', 1)) {
            $this->addElement('Text', 'tags', array(
                'label' => 'Tags (Keywords)',
                'autocomplete' => 'off',
                'description' => Zend_Registry::get('Zend_Translate')->_('Separate tags with commas.'),
                'filters' => array(
                    new Engine_Filter_Censor(),
                ),
            ));
            $this->tags->getDecorator("Description")->setOption("placement", "append");
        }
        //put the start and end date here.
        // Start time
        if ($editFullEventDate) {
            $start = new Engine_Form_Element_CalendarDateTime('starttime');
            $start->setLabel("Start Time");
            //$start->setRequired(false);
            if ($this->getQuick()) {
                $start->setAttrib('loadedbyAjax', 'TRUE');
            }
            $start->setAllowEmpty(false);
            $this->addElement($start);
            if (!$this->_item) {
                $starttime = time();
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($user->timezone);
                $start->setValue(date("Y-m-d H:i:s", ($starttime + 3600)));
                date_default_timezone_set($oldTz);
            }
        }
        // End time
        $end = new Engine_Form_Element_CalendarDateTime('endtime');
        $end->setLabel("End Time");
        $end->setAllowEmpty(false);
        if ($this->getQuick()) {
            $end->setAttrib('loadedbyAjax', 'TRUE');
        }
        $this->addElement($end);
        if (!$this->_item) {
            $endtime = (time() + 4 * 3600);
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($user->timezone);
            $end->setValue(date("Y-m-d H:i:s", $endtime));
            date_default_timezone_set($oldTz);
        }

        $userTimezone = array(
            'US/Pacific' => '(UTC-8) Pacific Time (US & Canada)',
            'US/Mountain' => '(UTC-7) Mountain Time (US & Canada)',
            'US/Central' => '(UTC-6) Central Time (US & Canada)',
            'US/Eastern' => '(UTC-5) Eastern Time (US & Canada)',
            'America/Halifax' => '(UTC-4)  Atlantic Time (Canada)',
            'America/Anchorage' => '(UTC-9)  Alaska (US & Canada)',
            'Pacific/Honolulu' => '(UTC-10) Hawaii (US)',
            'Pacific/Samoa' => '(UTC-11) Midway Island, Samoa',
            'Etc/GMT-12' => '(UTC-12) Eniwetok, Kwajalein',
            'Canada/Newfoundland' => '(UTC-3:30) Canada/Newfoundland',
            'America/Buenos_Aires' => '(UTC-3) Brasilia, Buenos Aires, Georgetown',
            'Atlantic/South_Georgia' => '(UTC-2) Mid-Atlantic',
            'Atlantic/Azores' => '(UTC-1) Azores, Cape Verde Is.',
            'Europe/London' => 'Greenwich Mean Time (Lisbon, London)',
            'Europe/Berlin' => '(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid',
            'Europe/Athens' => '(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe',
            'Europe/Moscow' => '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow',
            'Iran' => '(UTC+3:30) Tehran',
            'Asia/Dubai' => '(UTC+4) Abu Dhabi, Kazan, Muscat',
            'Asia/Kabul' => '(UTC+4:30) Kabul',
            'Asia/Yekaterinburg' => '(UTC+5) Islamabad, Karachi, Tashkent',
            'Asia/Calcutta' => '(UTC+5:30) Bombay, Calcutta, New Delhi',
            'Asia/Katmandu' => '(UTC+5:45) Nepal',
            'Asia/Omsk' => '(UTC+6) Almaty, Dhaka',
            'India/Cocos' => '(UTC+6:30) Cocos Islands, Yangon',
            'Asia/Krasnoyarsk' => '(UTC+7) Bangkok, Jakarta, Hanoi',
            'Asia/Hong_Kong' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei',
            'Asia/Tokyo' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk',
            'Australia/Adelaide' => '(UTC+9:30) Adelaide, Darwin',
            'Australia/Sydney' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam',
            'Asia/Magadan' => '(UTC+11) Magadan, Soloman Is., New Caledonia',
            'Pacific/Auckland' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',
        );
        $timezone = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity()) {
            $timezone = $viewer->timezone;
        }
        $this->addElement('Dummy', 'showtimezone', array(
            'label' => '',
            'content' => $userTimezone[$timezone]
        ));

        //CHECK IF SITEEVENT REPEAT MODULE EXIST AND ENABLE ON THE SITE ONLY THEN WE WILL ACTIVATE THE REPEATING EVENT FEATURE.
        $siteeventrepeatEventRepeatsPrivacy = Zend_Registry::isRegistered('siteeventrepeatEventRepeatsPrivacy') ? Zend_Registry::get('siteeventrepeatEventRepeatsPrivacy') : null;


        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat') && !empty($siteeventrepeatEventRepeatsPrivacy) && (!empty($siteeventRepeatGetShowViewType) || empty($siteeventRepeatEventsTypeInfo) || ($siteeventRepeatEventsTypeInfo == $tempEventRepeatTypeInfo))) {
            $eventrepeat_prepared = array('never' => 'Never', 'daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'custom' => 'Other (be specific)');
//            if ($this->_item && (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.repeat', 1) && empty($this->_item->repeat_params) || !$editFullEventDate)) {
//                if (!empty($this->_item->repeat_params)) {
//                    $repeatEventInfo = json_decode($this->_item->repeat_params, true);
//                    $eventrepeat_prepared = array($repeatEventInfo['eventrepeat_type'] => $repeatEventInfo['eventrepeat_type']);
//                } else {
//                    $eventrepeat_prepared = array('never' => 'Never');
//                }
//            }

            $this->addElement('Select', 'eventrepeat_id', array(
                'label' => 'Event Repeats',
                'multiOptions' => $eventrepeat_prepared,
                'onchange' => "en4.siteevent.create._repeatEvent($(this), '$seaocoreCalenderDayStart');",
                'value' => 'never',
                'attribs' => array('class' => 'se_quick_advanced'),
            ));


            //Repeat event work:
            //GET THE REPEAT EVENT INFO
            if ($this->_item && !empty($this->_item->repeat_params) && empty($_POST)) {

                $repeatEventInfo = json_decode($this->_item->repeat_params, true);
                //CHECK IF THE LOCAL TIME FORMAT IS DMY OR MDY.

                $dateFormat = $view->locale()->useDateLocaleFormat();
                if ($dateFormat == 'dmy' && $repeatEventInfo['eventrepeat_type'] != 'custom') {
                    $date = explode("/", $repeatEventInfo['endtime']['date']);
                    if (count($date) == 3) {
                        $repeatEventInfo['endtime']['date'] = $date[1] . '/' . $date[0] . '/' . $date[2];
                    }
                }
                if (!empty($repeatEventInfo) && $repeatEventInfo['eventrepeat_type'] == 'custom') {

                    $customEventType = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getCustomEventInfo($event_id)->toarray();
                    if (!empty($_POST)) {

                        $j = 0;
                        for ($i = 0; $i <= $_POST['countcustom_dates']; $i++) {
                            if (isset($_POST['customdate_' . $i])) {

                                $startenddate = explode("-", $_POST['customdate_' . $i]);
                                if ($editFullEventDate) {
                                    $customEventType[$j]['starttime'] = $startenddate[0];
                                    $customEventType[$j]['endtime'] = $startenddate[1];
                                    $j++;
                                } else {
                                    $customEventType[$i]['starttime'] = $startenddate[0];
                                    $customEventType[$i]['endtime'] = $startenddate[1];
                                }
                            }
                        }
                    }

                    $repeatEventInfo = array_merge($repeatEventInfo, $customEventType);
                }
            } elseif (!empty($_POST) && isset($_POST['eventrepeat_id']) && $_POST['eventrepeat_id'] != 'never') {

                $repeatEventInfo = Engine_Api::_()->siteevent()->getRepeatEventInfo($_POST, $event_id, $editFullEventDate, 'display');
            } else {

                $repeatEventInfo = '';
                $editFullEventDate = true;
            }

            $this->addElement('dummy', 'eventrepeat', array(
                'decorators' => array(array('ViewScript', array(
                            'viewScript' => 'application/modules/Siteeventrepeat/views/scripts/_repeatEvent.tpl',
                            'repeatEventInfo' => $repeatEventInfo,
                            'editFullEventDate' => $editFullEventDate,
                            'class' => 'form element'
                        )))
            ));
        } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat') && !empty($siteeventrepeatEventRepeatsPrivacy) && (!empty($siteeventRepeatGetShowViewType) || empty($siteeventRepeatEventsTypeInfo) || ($siteeventRepeatEventsTypeInfo == $tempEventRepeatTypeInfo))) {
            $this->addElement('dummy', 'eventrepeat', array(
                'decorators' => array(array('ViewScript', array(
                            'viewScript' => 'application/modules/Siteeventrepeat/views/sitemobile/scripts/_repeatEvent.tpl',
                            'class' => 'form element'
                        )))
            ));
        }

        $allowed_upload = Engine_Api::_()->authorization()->getPermission($user_level, 'siteevent_event', "photo");
        if (!empty($createFormFields) && in_array('photo', $createFormFields) && $allowed_upload) {
            $this->addElement('File', 'photo', array(
                'label' => 'Main Photo',
                    //'attribs' => array('class' => 'se_quick_advanced'),
            ));
            $this->photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        }

        //PACKAGE BASED CHECKS
        if ($hasPackageEnable) {
            if (Engine_Api::_()->siteeventpaid()->allowPackageContent($package_id, "overview")) {
                $allowOverview = 1;
            } else {
                $allowOverview = 0;
            }
        } else {//AUTHORIZATION CHECKS
            $allowOverview = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteevent_event', "overview");
        }
        //PACKAGE BASED CHECKS   
        $allowEdit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteevent_event', "edit");

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1) && (!empty($createFormFields) && in_array('overview', $createFormFields)) && $allowOverview && $allowEdit && !$this->_item) {
            $description = 'Short Description';
        } else {
            $description = 'Description';
        }

        if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.bodyallow', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.bodyrequired', 1)) || (!empty($createFormFields) && in_array('description', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.bodyallow', 1))) {

            $this->addElement('textarea', 'body', array(
                'label' => $description,
                'required' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.bodyrequired', 1) ? true : false,
                'allowEmpty' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.bodyrequired', 1) ? false : true,
                'attribs' => $this->getQuick() ? array('rows' => 2, 'cols' => 180, 'style' => 'width:300px; max-width:400px;min-height:20px;') : array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
                'filters' => array(
                    'StripTags',
                    //new Engine_Filter_HtmlSpecialChars(),
                    new Engine_Filter_EnableLinks(),
                    new Engine_Filter_Censor(),
                ),
            ));
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1) && (!empty($createFormFields) && in_array('overview', $createFormFields)) && $allowOverview && $allowEdit && !$this->_item) {
//      $upload_url = "";
//      $viewer = Engine_Api::_()->user()->getViewer();
//      $albumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
//      if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') && $albumEnabled) {
//        $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'siteevent_general', true);
//      }
//
//      $editorOptions = Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url);
//      $editorOptions['mode'] = "exact";
//      $editorOptions['elements'] = "overview";
            $this->addElement('textarea', 'overview', array(
                'label' => 'Overview',
                'description' => 'Create a rich, attractive overview for your event. Switch the editor to Fullscreen mode by clicking on its icon below to comfortably create the overview.',
                'allowEmpty' => false,
                'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;', 'class' => 'se_quick_advanced'),
                //   'editorOptions' => $editorOptions,
                'filters' => array(new Engine_Filter_Censor()),
            ));
        }

        if (!empty($createFormFields) && in_array('price', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) {
            $localeObject = Zend_Registry::get('Locale');
            $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
            $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
            $this->addElement('Text', 'price', array(
                'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Price (%s)'), $currencyName),
                'attribs' => array('class' => 'se_quick_advanced'),
                'validators' => array(
                // array('Float', true),
                //array('GreaterThan', false, array(0))
                ),
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
            )));
        }

        if (!empty($createFormFields) && in_array('host', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.host', 1) && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $hostOptionsAlow = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.hostOptions', array('sitepage_page', 'sitebusiness_business', 'user', 'sitegroup_group', 'sitestore_store', 'siteevent_organizer'));
            $hostOptions = array(
                "user" => "Member",
                'siteevent_organizer' => "Other Individual or Organization",
                'sitepage_page' => 'Page',
                'sitebusiness_business' => 'Business',
                'sitegroup_group' => 'Group',
                'sitestore_store' => 'Store',
            );
            foreach ($hostOptions as $k => $v) {
                if (!Engine_Api::_()->hasItemType($k) || !in_array($k, $hostOptionsAlow)) {
                    unset($hostOptions[$k]);
                }
            }
            if (count($hostOptions)) {
                $this->addElement('select', 'host_type', array(
                    'label' => 'Host',
                    'multiOptions' => $hostOptions,
                    'value' => $this->getHost()->getType(),
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => '_hostDetails.tpl',
                                'hostOptions' => $hostOptions,
                                'fieldType' => 'host_type',
                                'host' => $this->getHost(),
                                'class' => 'form element'
                            ))),
                ));
                $this->addElement('Hidden', 'host_id', array(
                    'value' => $this->getHost()->getIdentity(),
                    'order' => 801,
                ));
                $this->addElement('Hidden', 'add_new_host', array('value' => 0, 'order' => 800));

                $hostFields = new Siteevent_Form_Organizer_Create(array(
//            'item' => $this->getOrganizer(),
                    'elementsBelongTo' => 'host',
                    'decorators' => array(
                        'FormElements'
                )));

                $this->addSubForm($hostFields, 'organizer');
            }
        }

        $orderPrivacyHiddenFields = 786590;
        $availableLabels = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'member' => 'Event Guests Only',
            'leader' => 'Owner and Leaders Only'
        );

        $parentItem = Engine_Api::_()->getItem($this->_parent_type, $this->_parent_id);

        $explodeParentType = explode('_', $this->_parent_type);

        if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($this->_parent_type == 'sitepage_page' || $this->_parent_type == 'sitebusiness_business' || $this->_parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $this->_parent_type, 'item_module' => $explodeParentType[0])))) {
                $shortTypeName = ucfirst($explodeParentType[1]);
                $availableLabels = array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'parent_member' => $shortTypeName . ' Members Only',
                    'member' => 'Event Guests Only',
                    'leader' => 'Just Me'
                );
            } elseif (($this->_parent_type == 'sitepage_page' || $this->_parent_type == 'sitebusiness_business' || $this->_parent_type == 'sitegroup_group' || $this->_parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $this->_parent_type, 'item_module' => $explodeParentType[0])))) {
                $availableLabels = array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'member' => 'Event Guests Only',
                    'leader' => 'Just Me'
                );
            } elseif (($this->_parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentItem->listingtype_id, 'item_module' => 'sitereview')))) {
                $availableLabels = array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'member' => 'Event Guests Only',
                    'leader' => 'Just Me'
                );
            }
        }

        if (Engine_Api::_()->siteevent()->isTicketBasedEvent() && isset($availableLabels['member'])) {
            unset($availableLabels['member']);
        }

        $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_event', $user, "auth_view");
        $view_options = array_intersect_key($availableLabels, array_flip($view_options));

        if (!empty($createFormFields) && in_array('viewPrivacy', $createFormFields) && count($view_options) > 1) {
            $this->addElement('Select', 'auth_view', array(
                'label' => 'View Privacy',
                'description' => Zend_Registry::get('Zend_Translate')->_("Who may see this event?"),
                // 'attribs' => array('class' => 'se_quick_advanced'),
                'multiOptions' => $view_options,
                'value' => key($view_options),
            ));
            $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
        } elseif (count($view_options) == 1) {
            $this->addElement('Hidden', 'auth_view', array(
                'value' => key($view_options),
                'order' => ++$orderPrivacyHiddenFields
            ));
        } else {
            $this->addElement('Hidden', 'auth_view', array(
                'value' => "everyone",
                'order' => ++$orderPrivacyHiddenFields
            ));
        }

        $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_event', $user, "auth_comment");
        $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

        if (!empty($createFormFields) && in_array('commentPrivacy', $createFormFields) && count($comment_options) > 1) {
            $this->addElement('Select', 'auth_comment', array(
                'label' => 'Comment Privacy',
                'description' => Zend_Registry::get('Zend_Translate')->_("Who may comment on this event?"),
                'multiOptions' => $comment_options,
                'value' => key($comment_options),
                'attribs' => array('class' => 'se_quick_advanced'),
            ));
            $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
        } elseif (count($comment_options) == 1) {
            $this->addElement('Hidden', 'auth_comment', array('value' => key($comment_options),
                'order' => ++$orderPrivacyHiddenFields));
        } else {
            $this->addElement('Hidden', 'auth_comment', array('value' => "registered",
                'order' => ++$orderPrivacyHiddenFields));
        }

        $availableLabels = array(
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'member' => 'Event Guests Only',
            'leader' => 'Owner and Leaders Only'
        );

        if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($this->_parent_type == 'sitepage_page' || $this->_parent_type == 'sitebusiness_business' || $this->_parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $this->_parent_type, 'item_module' => $explodeParentType[0])))) {
                $shortTypeName = ucfirst($explodeParentType[1]);
                $availableLabels = array(
                    'registered' => 'All Registered Members',
                    'parent_member' => $shortTypeName . ' Members Only',
                    'like_member' => 'Who liked this ' . $shortTypeName,
                    'member' => 'Event Guests Only',
                    'leader' => 'Just Me'
                );
            } elseif (($this->_parent_type == 'sitepage_page' || $this->_parent_type == 'sitebusiness_business' || $this->_parent_type == 'sitegroup_group' || $this->_parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $this->_parent_type, 'item_module' => $explodeParentType[0])))) {
                $shortTypeName = ucfirst($explodeParentType[1]);
                $availableLabels = array(
                    'registered' => 'All Registered Members',
                    'like_member' => 'Who liked this ' . $shortTypeName,
                    'member' => 'Event Guests Only',
                    'leader' => 'Just Me'
                );
            } elseif (($this->_parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentItem->listingtype_id, 'item_module' => 'sitereview')))) {
                $availableLabels = array(
                    'registered' => 'All Registered Members',
                    'member' => 'Event Guests Only',
                    'leader' => 'Just Me'
                );
            }
        }

        if (Engine_Api::_()->siteevent()->isTicketBasedEvent() && isset($availableLabels['member'])) {
            unset($availableLabels['member']);
        }

        if (Engine_Api::_()->hasModuleBootstrap('advancedactivity')) {
            $post_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_event', $user, "auth_post");
            $post_options = array_intersect_key($availableLabels, array_flip($post_options));

            if (!empty($createFormFields) && in_array('postPrivacy', $createFormFields) && count($post_options) > 1) {
                $this->addElement('Select', 'auth_post', array(
                    'label' => 'Posting Updates Privacy',
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may post updates on this event?"),
                    'multiOptions' => $post_options,
                    'value' => key($post_options),
                    'attribs' => array('class' => 'se_quick_advanced'),
                ));
                $this->auth_post->getDecorator('Description')->setOption('placement', 'append');
            } elseif (count($post_options) == 1) {
                $this->addElement('Hidden', 'auth_post', array('value' => key($post_options),
                    'order' => ++$orderPrivacyHiddenFields));
            } else {
                $this->addElement('Hidden', 'auth_post', array(
                    'value' => 'member',
                    'order' => ++$orderPrivacyHiddenFields
                ));
            }
        }

        $topic_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_event', $user, "auth_topic");
        $topic_options = array_intersect_key($availableLabels, array_flip($topic_options));

        if (!empty($createFormFields) && in_array('discussionPrivacy', $createFormFields) && count($topic_options) > 1) {
            $this->addElement('Select', 'auth_topic', array(
                'label' => 'Discussion Topic Privacy',
                'description' => Zend_Registry::get('Zend_Translate')->_("Who may post discussion topics for this event?"),
                'multiOptions' => $topic_options,
                'value' => 'member',
                'attribs' => array('class' => 'se_quick_advanced'),
            ));
            $this->auth_topic->getDecorator('Description')->setOption('placement', 'append');
        } elseif (count($topic_options) == 1) {
            $this->addElement('Hidden', 'auth_topic', array('value' => key($topic_options),
                'order' => ++$orderPrivacyHiddenFields));
        } else {
            $this->addElement('Hidden', 'auth_topic', array(
                'value' => 'member',
                'order' => ++$orderPrivacyHiddenFields
            ));
        }

        $photo_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_event', $user, "auth_photo");
        $photo_options = array_intersect_key($availableLabels, array_flip($photo_options));

        //PACKAGE BASED CHECKS
        $can_show_photo_list = true;
        if ($hasPackageEnable && !Engine_Api::_()->siteeventpaid()->allowPackageContent($package_id, "photo")) {
            $can_show_photo_list = false;
        }

        if (!empty($createFormFields) && in_array('photoPrivacy', $createFormFields) && count($photo_options) > 1 && $can_show_photo_list) {
            $this->addElement('Select', 'auth_photo', array(
                'label' => 'Photo Privacy',
                'description' => Zend_Registry::get('Zend_Translate')->_("Who may upload photos for this event?"),
                'multiOptions' => $photo_options,
                'value' => 'member',
                'attribs' => array('class' => 'se_quick_advanced'),
            ));
            $this->auth_photo->getDecorator('Description')->setOption('placement', 'append');
        } elseif (count($photo_options) == 1 && $can_show_photo_list) {
            $this->addElement('Hidden', 'auth_photo', array('value' => key($photo_options),
                'order' => ++$orderPrivacyHiddenFields));
        } else {
            $this->addElement('Hidden', 'auth_photo', array(
                'value' => 'member',
                'order' => ++$orderPrivacyHiddenFields
            ));
        }

        //START SITEEVENTDOCUMENT PLUGIN WORK
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument') || (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document') &&Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => "siteevent_event", 'item_module' => 'siteevent')))) {

            $document_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_event', $user, 'auth_document');
            $document_options = array_intersect_key($availableLabels, array_flip($document_options));

            if (!empty($createFormFields) && in_array('document', $createFormFields) && count($document_options) > 1) {
                $this->addElement('Select', 'auth_document', array(
                    'label' => 'Documents Creation Privacy',
                    'description' => 'Who may create documents in this event?',
                    'multiOptions' => $document_options,
                    'value' => 'member',
                    'attribs' => array('class' => 'se_quick_advanced'),
                ));
                $this->auth_document->getDecorator('Description')->setOption('placement', 'append');
            } elseif (count($document_options) == 1) {
                $this->addElement('Hidden', 'auth_document', array('value' => key($document_options),
                    'order' => ++$orderPrivacyHiddenFields));
            } else {
                $this->addElement('Hidden', 'auth_document', array(
                    'value' => 'member',
                    'order' => ++$orderPrivacyHiddenFields
                ));
            }
        }
        //END SITEEVENTDOCUMENT PLUGIN WORK    

        $videoEnable = Engine_Api::_()->siteevent()->enableVideoPlugin();
        if ($videoEnable) {

            $video_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_event', $user, "auth_video");
            $video_options = array_intersect_key($availableLabels, array_flip($video_options));

            //PACKAGE BASED CHECKS
            $can_show_video_list = true;
            if ($hasPackageEnable && !Engine_Api::_()->siteeventpaid()->allowPackageContent($package_id, "video")) {
                $can_show_video_list = false;
            }
            if (!empty($createFormFields) && in_array('videoPrivacy', $createFormFields) && count($video_options) > 1 && $can_show_video_list) {
                $this->addElement('Select', 'auth_video', array(
                    'label' => 'Video Privacy',
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may add videos for this event?"),
                    'multiOptions' => $video_options,
                    'value' => 'member',
                    'attribs' => array('class' => 'se_quick_advanced'),
                ));
                $this->auth_video->getDecorator('Description')->setOption('placement', 'append');
            } elseif (count($video_options) == 1 && $can_show_video_list) {
                $this->addElement('Hidden', 'auth_video', array('value' => key($video_options),
                    'order' => ++$orderPrivacyHiddenFields));
            } else {
                $this->addElement('Hidden', 'auth_video', array(
                    'value' => 'member',
                    'order' => ++$orderPrivacyHiddenFields
                ));
            }
        }

        //NETWORK BASE PAGE VIEW PRIVACY
        if (Engine_Api::_()->siteevent()->listBaseNetworkEnable()) {
            // Make Network List
            $table = Engine_Api::_()->getDbtable('networks', 'network');
            $select = $table->select()
                    ->from($table->info('name'), array('network_id', 'title'))
                    ->order('title');
            $result = $table->fetchAll($select);

            $networksOptions = array('0' => 'Everyone');
            foreach ($result as $value) {
                $networksOptions[$value->network_id] = $value->title;
            }

            if (count($networksOptions) > 0) {
                $this->addElement('Multiselect', 'networks_privacy', array(
                    'label' => 'Networks Selection',
                    'description' => Zend_Registry::get('Zend_Translate')->_("Select the networks, members of which should be able to see your event. (Press Ctrl and click to select multiple networks. You can also choose to make your event viewable to everyone.)"),
//            'attribs' => array('style' => 'max-height:150px; '),
                    'multiOptions' => $networksOptions,
                    'value' => array(0),
                    'attribs' => array('class' => 'se_quick_advanced'),
                ));
            } else {
                
            }
        }

        if (!empty($createFormFields) && in_array('status', $createFormFields)) {
            $this->addElement('Select', 'draft', array(
                'label' => 'Status',
                'multiOptions' => array("0" => "Published", "1" => "Saved As Draft"),
                'description' => 'If this event is published, it cannot be switched back to draft mode.',
                'onchange' => 'checkDraft();',
                'attribs' => array('class' => 'se_quick_advanced'),
            ));
            $this->draft->getDecorator('Description')->setOption('placement', 'append');
        }

        $this->addElement('Hidden', 'event_info', array(
            'value' => 'set'
        ));

        if (!empty($createFormFields) && in_array('rsvp', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.rsvp.option', 1))
            $this->addElement('Radio', 'approval', array(
                'label' => 'Approve members?',
                'description' => 'When people try to join this event, should they be allowed to join immediately, or should they be forced to wait for approval?',
                'multiOptions' => array(
                    '0' => 'New members can join immediately this event.',
                    '1' => 'New members must be approved to join this event.',
                ),
                'value' => '1',
                'onclick' => 'showGuestLists(this.value)'
            ));
        if (!empty($createFormFields) && in_array('invite', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.other.guests', 1)) {
            $memberModuleName = '';
            if ($this->_parent_type == 'sitepage_page') {
                $moduleName = 'sitepage';
                $memberModuleName = 'sitepagemember';
                $core_setting = 'pagemember.pageasgroup';
                $label = "Invite all Page Members.";
                $id = 'page_id';
            } elseif ($this->_parent_type == 'sitebusiness_business') {
                $moduleName = 'sitebusiness';
                $memberModuleName = 'sitebusinessmember';
                $core_setting = 'businessmember.businessasgroup';
                $label = "Invite all Business Members.";
                $id = 'business_id';
            } elseif ($this->_parent_type == 'sitegroup_group') {
                $moduleName = 'sitegroup';
                $memberModuleName = 'sitegroupmember';
                $core_setting = 'groupmember.groupasgroup';
                $label = "Invite all Group Members.";
                $id = 'group_id';
            }
            $pagemember = '';
            $pageasgroup = '';
            if ($memberModuleName) {
                $pagemember = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($memberModuleName);
                if (!empty($pagemember)) {
                    $select = Engine_Api::_()->getDbTable('membership', $moduleName)->hasMembers($viewer_id, $parentItem->$id);
                    $pageasgroup = Engine_Api::_()->getApi('settings', 'core')->getSetting($core_setting);
                }
            }
            if (empty($pageasgroup) && empty($pagemember)) {
                // Invite
                $this->addElement('Checkbox', 'auth_invite', array(
                    'label' => 'Invited guests can invite other people as well.',
                    'value' => True,
                    'attribs' => array('class' => 'se_quick_advanced'),
                ));
            } elseif (!empty($select)) {
                $this->addElement('Checkbox', 'all_members', array(
                    'label' => $label,
                    'value' => True,
                    'attribs' => array('class' => 'se_quick_advanced'),
                ));
            }
        }

        if (!empty($createFormFields) && in_array('search', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.browse', 1)) {
            $this->addElement('Checkbox', 'search', array(
                //'label' => "Show this event in search results",
                'label' => "Show this event on browse page and in various blocks.",
                'value' => 1,
                'attribs' => array('class' => 'se_quick_advanced'),
            ));
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.guestconfimation', 0) && !empty($createFormFields) && in_array('guestLists', $createFormFields)) {
            $this->addElement('Radio', 'guest_lists', array(
                // 'label' => 'Approve members?',
                // 'description' => 'When people try to join this event, should they be allowed ' .
                // 'to join immediately, or should they be forced to wait for approval?',
                'multiOptions' => array(
                    '1' => 'Unconfirmed members can see the names of the ones that have been confirmed.',
                    '0' => 'Unconfirmed members cannot see the names of the ones that have been confirmed.',
                ),
                'value' => '0',
            ));
        }

        $this->addElement('Hidden', 'return_url', array(
            'order' => 10000000000
        ));
        $this->addElement('Button', 'execute', array(
            'label' => 'Create',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'onclick' => $this->getQuick() ? 'SmoothboxSEAO.close()' : '',
            'prependText' => ' or ',
            'href' => $this->getQuick() ? "javascript:void(0)" : Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), "siteevent_general", true),
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array(
            'execute',
            'cancel',
                ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}
