<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Create extends Engine_Form {

    public $_error = array();
    protected $_packageId;
    protected $_owner;
    protected $_quick;

    public function getOwner() {
        return $this->_owner;
    }

    public function setOwner($owner) {
        $this->_owner = $owner;
        return $this;
    }

    public function getPackageId() {
        return $this->_packageId;
    }

    public function setPackageId($package_id) {
        $this->_packageId = $package_id;
        return $this;
    }

    public function setQuick($flage) {
        $this->_quick = $flage;
        return $this;
    }

    public function getQuick() {
        return $this->_quick;
    }

    public function init() {
        $this->loadDefaultDecorators();
        parent::init();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $createFormFields = array(
            'location',
            'tags',
            'photo',
            'description',
            'overview',
            'price',
            'viewPrivacy',
            'commentPrivacy',
            'showHideAdvancedOptions' => 'Advanced Show / Hide Options',
            'allPostPrivacy'
        );

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
            $createFormFields = array_merge($createFormFields, array('discussionPrivacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
            $createFormFields = array_merge($createFormFields, array('photoPrivacy'));
        }

        if ((Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
            $createFormFields = array_merge($createFormFields, array('videoPrivacy'));
        } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
            $createFormFields = array_merge($createFormFields, array('videoPrivacy'));
        }
        
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
            $createFormFields = array_merge($createFormFields, array('documentPrivacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
            $createFormFields = array_merge($createFormFields, array('pollPrivacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
            $createFormFields = array_merge($createFormFields, array('notePrivacy'));
        }

        if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
            $createFormFields = array_merge($createFormFields, array('eventPrivacy'));
        } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
            $createFormFields = array_merge($createFormFields, array('eventPrivacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
            $createFormFields = array_merge($createFormFields, array('musicPrivacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
            $createFormFields = array_merge($createFormFields, array('memberTitle'));
            $createFormFields = array_merge($createFormFields, array('memberInvite'));
            $createFormFields = array_merge($createFormFields, array('memberApproval'));
        }

        $createFormFields = array_merge($createFormFields, array(
            'subGroupPrivacy',
            'claimThisGroup',
            'status',
            'search'
        ));

        $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
        if (empty($group_id) && Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitegroup.createFormFields')) {
            $createFormFields = $settings->getSetting('sitegroup.createFormFields', $createFormFields);
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $user = $this->getOwner();
        $viewer_id = $viewer->getIdentity();
        $userlevel_id = $user->level_id;
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $sitegroupMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
        $this->setTitle('Create New Group')
                ->setDescription('Configure your group to showcase your offerings and connect to your customers.')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'sitegroups_create');
        $this->getDecorator('Description')->setOption('escape', false);
        if ($this->getQuick()) {
            $this->setAttrib('id', 'sitegroups_create_quick');
            // $this->setDescription('');
        }

        // TITLE
        $this->addElement('Text', 'title', array(
            'label' => 'Title',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '128')),
        )));

        // Element: group_url
        $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
        $parent_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_id', null);
        $sitegroupUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupurl');
        $show_url = $coreSettings->getSetting('sitegroup.showurl.column', 1);
        $change_url = $coreSettings->getSetting('sitegroup.change.url', 1);
        $edit_url = $coreSettings->getSetting('sitegroup.edit.url', 0);
        //if (empty($group_id)) {
// // This will be the end of your group URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your group will be:
//  <br /> <span id="group_url_address">http://%s</span>
//       $description = Zend_Registry::get('Zend_Translate')->_('This will be the end of your group URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your group will be:');
        //$description = sprintf($description, $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => Zend_Registry::get('Zend_Translate')->_('GROUP-NAME')), 'sitegroup_entry_view')).'<br />';

        $link = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => Zend_Registry::get('Zend_Translate')->_('GROUP-NAME')), 'sitegroup_entry_view');

        if (!empty($sitegroupUrlEnabled) && !empty($change_url)) {

            $front = Zend_Controller_Front::getInstance();
            $baseUrl = $front->getBaseUrl();
            $GROUP_NAME = Zend_Registry::get('Zend_Translate')->_("GROUP-NAME");
            $link2 = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $GROUP_NAME;
            $limit = $coreSettings->getSetting('sitegroup.likelimit.forurlblock', 5);
            if (empty($limit)) {
                $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This will be the end of your group URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your group will be: %s"), "<span id='short_group_url_address'>http://$link2</span>");
            } else {
                $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This will be the end of your group URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your group will be: %s"), "<span id='group_url_address'>http://$link</span>");
                $description = $description . sprintf(Zend_Registry::get('Zend_Translate')->_('<br />and if your group has %1$s or more likes URL will be: <br />%2$s'), "$limit", "<span id='short_group_url_address'>http://$link2</span>");
            }
        } else {
            $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This will be the end of your group URL. It should be indicative of the title and can contain alphabets, numbers, underscores and  dashes only. Its length should be in the range of 3-255 characters. The complete URL of your group will be: %s"), "<span id='group_url_address'>http://$link</span>");
        }

        if (!empty($sitegroupUrlEnabled) && !empty($group_id) && !empty($show_url) && !empty($edit_url)) {
            $this->addElement('Text', 'group_url', array(
                'label' => 'URL',
                'description' => $description,
                'autocomplete' => 'off',
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('NotEmpty', true),
                    // array('Alnum', true),
                    array('StringLength', true, array(3, 255)),
                    array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
                ),
            ));
            $this->group_url->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
            $this->group_url->getValidator('NotEmpty')->setMessage('Please enter a valid group url.', 'isEmpty');
            $this->group_url->getValidator('Regex')->setMessage('Profile addresses must start with a letter.', 'regexNotMatch');
            $this->addElement('dummy', 'group_url_msg', array('value' => 0));
        } elseif (empty($group_id)) {
            $this->addElement('Text', 'group_url', array(
                'label' => 'URL',
                'description' => $description,
                'autocomplete' => 'off',
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('NotEmpty', true),
                    // array('Alnum', true),
                    array('StringLength', true, array(3, 255)),
                    array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
                    array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'sitegroup_groups', 'group_url'), array('field' => 'group_id', 'value != ?' => 1))
                ),
                    //'onblur' => 'var el = this; en4.user.checkgroup_urlTaken(this.value, function(taken){ el.style.marginBottom = taken * 100 + "px" });'
            ));
            $this->group_url->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
            $this->group_url->getValidator('NotEmpty')->setMessage('Please enter a valid group url.', 'isEmpty');
            $this->group_url->getValidator('Db_NoRecordExists')->setMessage('Someone has already picked this group url, please use another one.', 'recordFound');
            $this->group_url->getValidator('Regex')->setMessage('Profile addresses must start with a letter.', 'regexNotMatch');
            //$this->group_url->getValidator('Alnum')->setMessage('Profile addresses must be alphanumeric.', 'notAlnum');
            $this->addElement('dummy', 'group_url_msg', array('value' => 0));
        }
        //}
        // init to
        if (!empty($createFormFields) && in_array('tags', $createFormFields)) {
            $this->addElement('Text', 'tags', array(
                'label' => 'Tags (Keywords)',
                'autocomplete' => 'off',
                'description' => 'Separate tags with commas.',
                'filters' => array(
                    new Engine_Filter_Censor(),
                ),
            ));

            $this->tags->getDecorator("Description")->setOption("placement", "append");
        }

        // prepare categories
        $categories = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategories();
        if (count($categories) != 0) {
            $categories_prepared[0] = "";
            foreach ($categories as $category) {
                $categories_prepared[$category->category_id] = $category->category_name;
            }

            //category field
            if (!$this->_item && $coreSettings->getSetting('sitegroup.profile.fields', 1)) {
                $this->addElement('Select', 'category_id', array(
                    'label' => 'Category',
                    'allowEmpty' => false,
                    'required' => true,
                    'multiOptions' => $categories_prepared,
                    'onchange' => " var profile_type = getProfileType($(this).value); 
														if(profile_type == 0) profile_type = '';
														$('0_0_1').value = profile_type;
														changeFields($('0_0_1'));
														subcategory(this.value, '', '');",
                ));
            } else {
                $this->addElement('Select', 'category_id', array(
                    'label' => 'Category',
                    'allowEmpty' => false,
                    'required' => true,
                    'multiOptions' => $categories_prepared,
                    'onchange' => "subcategory(this.value, '', '');",
                ));
            }
        }

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
                        'viewScript' => 'application/modules/Sitegroup/views/scripts/_formSubcategory.tpl',
                        'class' => 'form element')))
        ));

        if (!$this->_item && $coreSettings->getSetting('sitegroup.profile.fields', 1)) {
            $customFields = new Sitegroup_Form_Custom_Standard(array(
                'item' => 'sitegroup_group',
                'decorators' => array(
                    'FormElements'
            )));

            $customFields->removeElement('submit');

            $customFields->getElement("0_0_1")
                    ->clearValidators()
                    ->setRequired(false)
                    ->setAllowEmpty(true);

            $this->addSubForms(array(
                'fields' => $customFields
            ));
        }


        if ($coreSettings->getSetting('sitegroup.description.allow', 1)) {
            if ($coreSettings->getSetting('sitegroup.requried.description', 1)) {
                // body
                $this->addElement('textarea', 'body', array(
                    'label' => 'Description',
                    'required' => true,
                    'allowEmpty' => false,
                    'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
                    'filters' => array(
                        'StripTags',
                        //new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                        new Engine_Filter_Censor(),
                    ),
                ));
            } else {
                if (!empty($createFormFields) && in_array('description', $createFormFields)) {
                    $this->addElement('textarea', 'body', array(
                        'label' => 'Description',
                        'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
                        'filters' => array(
                            'StripTags',
                            //new Engine_Filter_HtmlSpecialChars(),
                            new Engine_Filter_EnableLinks(),
                            new Engine_Filter_Censor(),
                        ),
                    ));
                }
            }
        }

        //$allowed_upload = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup', 'photo');
        //if ($allowed_upload) {

        if ($coreSettings->getSetting('sitegroup.requried.photo', 1)) {
            $this->addElement('File', 'photo', array(
                'label' => 'Main Photo',
                'required' => true,
                'allowEmpty' => false,
            ));
            $this->photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        } else {
            if (!empty($createFormFields) && in_array('photo', $createFormFields)) {
                $this->addElement('File', 'photo', array(
                    'label' => 'Main Photo',
                ));
                $this->photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');
            }
        }

        //}
        // PRICE
        if (!empty($createFormFields) && in_array('price', $createFormFields)) {
            if ($coreSettings->getSetting('sitegroup.price.field', 1)) {
                $localeObject = Zend_Registry::get('Locale');
                $currencyCode = $coreSettings->getSetting('payment.currency', 'USD');
                $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
                $this->addElement('Text', 'price', array(
                    'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Price (%s)'), $currencyName),
                    // 'description' => '(Zero will make this a free group.)',
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                )));
                //$this->price->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        // LOCATION
        if (!empty($createFormFields) && in_array('location', $createFormFields) && $coreSettings->getSetting('sitegroup.locationfield', 1)) {
            $this->addElement('Text', 'location', array(
                'label' => 'Location',
                'description' => 'Eg: Fairview Park, Berkeley, CA',
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
            )));
            $this->location->getDecorator('Description')->setOption('placement', 'append');
            $this->addElement('Hidden', 'locationParams', array('order' => 800000));


            include_once APPLICATION_PATH . '/application/modules/Seaocore/Form/specificLocationElement.php';
        }

        // Privacy
        // Privacy
        $groupadminsetting = $coreSettings->getSetting('sitegroup.manageadmin', 1);
        if (!empty($groupadminsetting)) {
            $ownerTitle = "Group Admins";
        } else {
            $ownerTitle = "Just Me";
        }


        //START SITEGROUPMEMBER PLUGIN WORK
        $allowMemberInLevel = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitegroup_group', 'smecreate');
        $allowMemberInthisPackage = false;
        $allowMemberInthisPackage = Engine_Api::_()->sitegroup()->allowPackageContent($this->getPackageId(), "modules", "sitegroupmember");
        if (!empty($createFormFields) && (in_array('memberTitle', $createFormFields) || in_array('memberInvite', $createFormFields) || in_array('memberApproval', $createFormFields))) {
            $member_approval = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.approval.option', 1);
            if ($sitegroupMemberEnabled && in_array('memberApproval', $createFormFields) && !empty($member_approval)) {
                if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                    if ($allowMemberInthisPackage) {
                        $this->addElement('Radio', 'member_approval', array(
                            'label' => 'Approve members?',
                            'description' => 'When people try to join this group, should they be allowed ' .
                            'to join immediately, or should they be forced to wait for approval?',
                            'multiOptions' => array(
                                '1' => 'New members can join immediately.',
                                '0' => 'New members must be approved.',
                            ),
                            'value' => '1',
                        ));
                    }
                } else if (!empty($allowMemberInLevel)) {

                    $this->addElement('Radio', 'member_approval', array(
                        'label' => 'Approve members?',
                        'description' => ' When people try to join this group, should they be allowed ' .
                        'to join immediately, or should they be forced to wait for approval?',
                        'multiOptions' => array(
                            '1' => 'New members can join immediately.',
                            '0' => 'New members must be approved.',
                        ),
                        'value' => '1',
                    ));
                }
            }
        }

        $availableLabels = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
        );
        if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
            $availableLabels['member'] = 'Group Members Only';
        } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
            $availableLabels['member'] = 'Group Members Only';
        }
        $availableLabels['owner'] = $ownerTitle;

        if (!empty($createFormFields) && in_array('viewPrivacy', $createFormFields)) {
            // View
            $orderPrivacyHiddenFields = 786590;
            $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_view');
            $view_options = array_intersect_key($availableLabels, array_flip($view_options));

            if (count($view_options) > 1) {
                $this->addElement('Select', 'auth_view', array(
                    'label' => 'View Privacy',
                    'description' => 'Who may see this group? (Note: Group information will always be displayed to everyone.)',
                    'multiOptions' => $view_options,
                    'value' => key($view_options),
                ));
                $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
            } elseif (count($view_options) == 1) {
                $this->addElement('Hidden', 'auth_view', array(
                    'value' => key($view_options),
                    'order' => ++$orderPrivacyHiddenFields,
                ));
            } else {
                $this->addElement('Hidden', 'auth_view', array(
                    'value' => "everyone",
                    'order' => ++$orderPrivacyHiddenFields,
                ));
            }
        }

        //START SITEGROUPEVENT PLUGIN WORK
        if (!empty($createFormFields) && in_array('eventPrivacy', $createFormFields)) {
            if ((Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) || ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup'))))) {
                $availableLabels = array(
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'like_member' => 'Who Liked This Group',
                );
                if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                    $availableLabels['member'] = 'Group Members Only';
                } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                    $availableLabels['member'] = 'Group Members Only';
                }
                $availableLabels['owner'] = $ownerTitle;

                $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_secreate');
                $options_create = array_intersect_key($availableLabels, array_flip($options));

                if (!empty($options_create)) {
                    $can_show_list = true;
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($this->getPackageId(), "modules", "sitegroupevent")) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'secreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    } else {
                        $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitegroup_group', 'secreate');
                        if (!$can_create) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'secreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    }
                    if ($can_show_list) {
                        if (count($options_create) > 1) {
                            $this->addElement('Select', 'secreate', array(
                                'label' => 'Event Creation Privacy',
                                'description' => 'Who may create events for this group?',
                                'multiOptions' => $options_create,
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                            $this->secreate->getDecorator('Description')->setOption('placement', 'append');
                        } elseif (count($options_create) == 1) {
                            $this->addElement('Hidden', 'secreate', array(
                                'value' => key($options_create),
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        } else {
                            $this->addElement('Hidden', 'secreate', array(
                                'value' => 'registered',
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        }
                    }
                } else {
                    $this->addElement('Hidden', 'secreate', array(
                        'value' => 'registered',
                        'order' => ++$orderPrivacyHiddenFields,
                    ));
                }
            }
        }
        //END SITEGROUPEVENT PLUGIN WORK        

        if (!empty($createFormFields) && in_array('allPostPrivacy', $createFormFields)) {
            $this->addElement('Select', 'all_post', array(
                'label' => 'Post in Updates Tab',
                'multiOptions' => array("1" => "All Registered Members", "0" => "Group Admins"),
                'description' => 'Who is allowed to post in this group?',
                'attribs' => array('class' => 'sg_quick_advanced')
            ));
            $this->all_post->getDecorator('Description')->setOption('placement', 'append');
        }
        //END GROUP MEMBER WORK
        //START SITEGROUPMEMBER PLUGIN WORK
        $allowMemberInLevel = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitegroup_group', 'smecreate');
        $allowMemberInthisPackage = false;
        $allowMemberInthisPackage = Engine_Api::_()->sitegroup()->allowPackageContent($this->getPackageId(), "modules", "sitegroupmember");
        if (!empty($createFormFields) && (in_array('memberTitle', $createFormFields) || in_array('memberInvite', $createFormFields) || in_array('memberApproval', $createFormFields))) {
            if ($sitegroupMemberEnabled) {
                if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                    if ($allowMemberInthisPackage) {

                        $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.title', 1);
                        if (in_array('memberTitle', $createFormFields) && !empty($memberTitle)) {
                            $this->addElement('Text', 'member_title', array(
                                'label' => 'What will members be called?',
                                'description' => 'Ex: Dance Lovers, Hikers, Innovators, Music Lovers, etc.',
                                'filters' => array(
                                    'StripTags',
                                    new Engine_Filter_Censor(),
                            )));
                            $this->member_title->getDecorator('Description')->setOption('placement', 'append');
                        }

                        $memberInvite = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.invite.option', 1);
                        if (in_array('memberInvite', $createFormFields) && !empty($memberInvite)) {
                            $this->addElement('Radio', 'member_invite', array(
                                'label' => 'Invite member',
                                //'description' => 'Do you want group members to invite other people to this group?',
                                'multiOptions' => array(
                                    '0' => 'Yes, members can invite other people.',
                                    '1' => 'No, only group admins can invite other people.',
                                ),
                                'value' => '1',
                                'attribs' => array('class' => 'sg_quick_advanced')
                            ));
                        }
                    }
                } else if (!empty($allowMemberInLevel)) {

                    $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.title', 1);
                    if (in_array('memberTitle', $createFormFields) && !empty($memberTitle)) {
                        $this->addElement('Text', 'member_title', array(
                            'label' => 'What will members be called?',
                            'description' => 'Ex: Dance Lovers, Hikers, Innovators, Music Lovers, etc.',
                            'filters' => array(
                                'StripTags',
                                new Engine_Filter_Censor(),
                        )));
                        $this->member_title->getDecorator('Description')->setOption('placement', 'append');
                    }

                    if (in_array('memberInvite', $createFormFields)) {
                        $this->addElement('Radio', 'member_invite', array(
                            'label' => 'Invite member',
                            'multiOptions' => array(
                                '0' => 'Yes, members can invite other people.',
                                '1' => 'No, only group admins can invite other people.',
                            ),
                            'value' => '1',
                            'attribs' => array('class' => 'sg_quick_advanced')
                        ));
                    }
                }
            }
        }

        //NETWORK BASE GROUP VIEW PRIVACY
        if (Engine_Api::_()->getApi('subCore', 'sitegroup')->groupBaseNetworkEnable()) {
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
                $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.networkprofile.privacy', 0);
                if ($viewPricavyEnable) {
                    $desc = 'Select the networks, members of which should be able to see your group. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
                } else {
                    $desc = 'Select the networks, members of which should be able to see your Group in browse and search groups. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
                }
                $this->addElement('Multiselect', 'networks_privacy', array(
                    'label' => 'Networks Selection',
                    'description' => $desc,
//            'attribs' => array('style' => 'max-height:150px; '),
                    'multiOptions' => $networksOptions,
                    'value' => array(0)
                ));
            }
        }

        
        // Comment
        if (!empty($createFormFields) && in_array('commentPrivacy', $createFormFields)) {
            $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_comment');
            $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

            if (count($comment_options) > 1) {
                $this->addElement('Select', 'auth_comment', array(
                    'label' => 'Comment Privacy',
                    'description' => 'Who may post comments on this group?',
                    'multiOptions' => $comment_options,
                    'value' => key($comment_options),
                    'attribs' => array('class' => 'sg_quick_advanced')
                ));
                $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
            } elseif (count($comment_options) == 1) {
                $this->addElement('Hidden', 'auth_comment', array(
                    'value' => key($comment_options),
                    'order' => ++$orderPrivacyHiddenFields,
                ));
            } else {
                $this->addElement('Hidden', 'auth_comment', array(
                    'value' => "everyone",
                    'order' => ++$orderPrivacyHiddenFields,
                ));
            }
        }
        //START DISCUSSION PRIVACY WORK
        if (!empty($createFormFields) && in_array('discussionPrivacy', $createFormFields)) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
                $availableLabels = array(
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'like_member' => 'Who Liked This Group',
                );
                if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                    $availableLabels['member'] = 'Group Members Only';
                } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                    $availableLabels['member'] = 'Group Members Only';
                }
                $availableLabels['owner'] = $ownerTitle;

                $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_sdicreate');
                $options_create = array_intersect_key($availableLabels, array_flip($options));

                if (!empty($options_create)) {
                    $can_show_list = true;
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($this->getPackageId(), "modules", "sitegroupdiscussion")) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'sdicreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    } else {
                        $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitegroup_group', 'sdicreate');
                        if (!$can_create) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'sdicreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    }

                    if ($can_show_list) {
                        if (count($options_create) > 1) {
                            $this->addElement('Select', 'sdicreate', array(
                                'label' => 'Discussion Topic Post Privacy',
                                'description' => 'Who may post discussion topics for this group?',
                                'multiOptions' => $options_create,
                                'value' => key($options_create),
                                'attribs' => array('class' => 'sg_quick_advanced')
                            ));
                            $this->sdicreate->getDecorator('Description')->setOption('placement', 'append');
                        } elseif (count($options_create) == 1) {
                            $this->addElement('Hidden', 'sdicreate', array(
                                'value' => key($options_create),
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        } else {
                            $this->addElement('Hidden', 'sdicreate', array(
                                'value' => 'registered',
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        }
                    }
                } else {
                    $this->addElement('Hidden', 'sdicreate', array(
                        'value' => 'registered',
                        'order' => ++$orderPrivacyHiddenFields,
                    ));
                }
            }
        }
        //END DISCUSSION PRIVACY WORK  
        //START PHOTO PRIVACY WORK
        if (!empty($createFormFields) && in_array('photoPrivacy', $createFormFields)) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
                $availableLabels = array(
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'like_member' => 'Who Liked This Group',
                );
                if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                    $availableLabels['member'] = 'Group Members Only';
                } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                    $availableLabels['member'] = 'Group Members Only';
                }
                $availableLabels['owner'] = $ownerTitle;

                $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_spcreate');
                $options_create = array_intersect_key($availableLabels, array_flip($options));

                if (!empty($options_create)) {
                    $can_show_list = true;
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($this->getPackageId(), "modules", "sitegroupalbum")) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'spcreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    } else {
                        $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitegroup_group', 'spcreate');
                        if (!$can_create) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'spcreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    }

                    if ($can_show_list) {
                        if (count($options_create) > 1) {
                            $this->addElement('Select', 'spcreate', array(
                                'label' => 'Photo Creation Privacy',
                                'description' => 'Who may upload photos for this group?',
                                'multiOptions' => $options_create,
                                'value' => @array_search(@end($options_create), $options_create),
                                'attribs' => array('class' => 'sg_quick_advanced')
                            ));
                            $this->spcreate->getDecorator('Description')->setOption('placement', 'append');
                        } elseif (count($options_create) == 1) {
                            $this->addElement('Hidden', 'spcreate', array(
                                'value' => key($options_create),
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        } else {
                            $this->addElement('Hidden', 'spcreate', array(
                                'value' => 'registered',
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        }
                    }
                } else {
                    $this->addElement('Hidden', 'spcreate', array(
                        'value' => 'registered',
                        'order' => ++$orderPrivacyHiddenFields,
                    ));
                }
            }
        }
        //END PHOTO PRIVACY WORK
        //START SITEGROUPDOCUMENT PLUGIN WORK
        if (!empty($createFormFields) && in_array('documentPrivacy', $createFormFields)) {
            $sitegroupDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument');
            if ($sitegroupDocumentEnabled) {
                $availableLabels = array(
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'like_member' => 'Who Liked This Group',
                );
                if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                    $availableLabels['member'] = 'Group Members Only';
                } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                    $availableLabels['member'] = 'Group Members Only';
                }
                $availableLabels['owner'] = $ownerTitle;

                $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_sdcreate');
                $options_create = array_intersect_key($availableLabels, array_flip($options));

                if (!empty($options_create)) {
                    $can_show_list = true;
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($this->getPackageId(), "modules", "sitegroupdocument")) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'sdcreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    } else {
                        $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitegroup_group', 'sdcreate');
                        if (!$can_create) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'sdcreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    }
                    if ($can_show_list) {
                        if (count($options_create) > 1) {
                            $this->addElement('Select', 'sdcreate', array(
                                'label' => 'Documents Creation Privacy',
                                'description' => 'Who may create documents for this group?',
                                'multiOptions' => $options_create,
                                'value' => @array_search(@end($options_create), $options_create),
                                'attribs' => array('class' => 'sg_quick_advanced')
                            ));
                            $this->sdcreate->getDecorator('Description')->setOption('placement', 'append');
                        } elseif (count($options_create) == 1) {
                            $this->addElement('Hidden', 'sdcreate', array(
                                'value' => key($options_create),
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        } else {
                            $this->addElement('Hidden', 'sdcreate', array(
                                'value' => 'registered',
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        }
                    }
                } else {
                    $this->addElement('Hidden', 'sdcreate', array(
                        'value' => 'registered',
                        'order' => ++$orderPrivacyHiddenFields,
                    ));
                }
            }
        }
        //END SITEGROUPDOCUMENT PLUGIN WORK
        //START SITEGROUPVIDEO PLUGIN WORK
        if (!empty($createFormFields) && in_array('videoPrivacy', $createFormFields)) {
            $sitegroupVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo');
            if (($sitegroupVideoEnabled) || ((Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup'))))) {
                $availableLabels = array(
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'like_member' => 'Who Liked This Group',
                );
                if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                    $availableLabels['member'] = 'Group Members Only';
                } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                    $availableLabels['member'] = 'Group Members Only';
                }
                $availableLabels['owner'] = $ownerTitle;

                $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_svcreate');
                $options_create = array_intersect_key($availableLabels, array_flip($options));

                if (!empty($options_create)) {
                    $can_show_list = true;
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($this->getPackageId(), "modules", "sitegroupvideo")) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'svcreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    } else {
                        $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitegroup_group', 'svcreate');
                        if (!$can_create) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'svcreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    }
                    if ($can_show_list) {

                        if (count($options_create) > 1) {
                            $this->addElement('Select', 'svcreate', array(
                                'label' => 'Videos Creation Privacy',
                                'description' => 'Who may create videos for this group?',
                                'multiOptions' => $options_create,
                                'value' => @array_search(@end($options_create), $options_create),
                                'attribs' => array('class' => 'sg_quick_advanced'),
                            ));
                            $this->svcreate->getDecorator('Description')->setOption('placement', 'append');
                        } elseif (count($options_create) == 1) {
                            $this->addElement('Hidden', 'svcreate', array(
                                'value' => key($options_create),
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        } else {
                            $this->addElement('Hidden', 'svcreate', array(
                                'value' => 'registered',
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        }
                    }
                } else {
                    $this->addElement('Hidden', 'svcreate', array(
                        'value' => 'registered',
                        'order' => ++$orderPrivacyHiddenFields,
                    ));
                }
            }
        }
        //END SITEGROUPVIDEO PLUGIN WORK
        //START SITEGROUPPOLL PLUGIN WORK
        if (!empty($createFormFields) && in_array('pollPrivacy', $createFormFields)) {
            $sitegroupPollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll');
            if ($sitegroupPollEnabled) {

                $availableLabels = array(
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'like_member' => 'Who Liked This Group',
                );
                if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                    $availableLabels['member'] = 'Group Members Only';
                } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                    $availableLabels['member'] = 'Group Members Only';
                }
                $availableLabels['owner'] = $ownerTitle;

                $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_splcreate');
                $options_create = array_intersect_key($availableLabels, array_flip($options));

                if (!empty($options_create)) {
                    $can_show_list = true;
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($this->getPackageId(), "modules", "sitegrouppoll")) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'splcreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    } else {
                        $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitegroup_group', 'splcreate');
                        if (!$can_create) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'splcreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    }
                    if ($can_show_list) {
                        if (count($options_create) > 1) {
                            $this->addElement('Select', 'splcreate', array(
                                'label' => 'Polls Creation Privacy',
                                'description' => 'Who may create polls for this group?',
                                'multiOptions' => $options_create,
                                'value' => @array_search(@end($options_create), $options_create),
                                'attribs' => array('class' => 'sg_quick_advanced'),
                            ));
                            $this->splcreate->getDecorator('Description')->setOption('placement', 'append');
                        } elseif (count($options_create) == 1) {
                            $this->addElement('Hidden', 'splcreate', array(
                                'value' => key($options_create),
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        } else {
                            $this->addElement('Hidden', 'splcreate', array(
                                'value' => 'registered',
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        }
                    }
                } else {
                    $this->addElement('Hidden', 'splcreate', array(
                        'value' => 'registered',
                        'order' => ++$orderPrivacyHiddenFields,
                    ));
                }
            }
        }
        //END SITEGROUPPOLL PLUGIN WORK

        if (!empty($createFormFields) && in_array('notePrivacy', $createFormFields)) {
            //START SITEGROUPNOTE PLUGIN WORK
            $sitegroupNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote');
            if ($sitegroupNoteEnabled) {
                $availableLabels = array(
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'like_member' => 'Who Liked This Group',
                );
                if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                    $availableLabels['member'] = 'Group Members Only';
                } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                    $availableLabels['member'] = 'Group Members Only';
                }
                $availableLabels['owner'] = $ownerTitle;

                $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_sncreate');
                $options_create = array_intersect_key($availableLabels, array_flip($options));

                if (!empty($options_create)) {
                    $can_show_list = true;
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($this->getPackageId(), "modules", "sitegroupnote")) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'sncreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    } else {
                        $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitegroup_group', 'sncreate');
                        if (!$can_create) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'sncreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    }
                    if ($can_show_list) {
                        if (count($options_create) > 1) {
                            $this->addElement('Select', 'sncreate', array(
                                'label' => 'Notes Creation Privacy',
                                'description' => 'Who may create notes for this group?',
                                'multiOptions' => $options_create,
                                'value' => @array_search(@end($options_create), $options_create),
                                'attribs' => array('class' => 'sg_quick_advanced'),
                            ));
                            $this->sncreate->getDecorator('Description')->setOption('placement', 'append');
                        } elseif (count($options_create) == 1) {
                            $this->addElement('Hidden', 'sncreate', array(
                                'value' => key($options_create),
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        } else {
                            $this->addElement('Hidden', 'sncreate', array(
                                'value' => 'registered',
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        }
                    }
                } else {
                    $this->addElement('Hidden', 'sncreate', array(
                        'value' => 'registered',
                        'order' => ++$orderPrivacyHiddenFields,
                    ));
                }
            }
        }
        //END SITEGROUPNOTE PLUGIN WORK


        if (!empty($createFormFields) && in_array('musicPrivacy', $createFormFields)) {
            //START SITEGROUPMUSIC PLUGIN WORK
            $sitegroupMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic');
            if ($sitegroupMusicEnabled) {
                $availableLabels = array(
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'like_member' => 'Who Liked This Group',
                );
                if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                    $availableLabels['member'] = 'Group Members Only';
                } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                    $availableLabels['member'] = 'Group Members Only';
                }
                $availableLabels['owner'] = $ownerTitle;

                $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_smcreate');
                $options_create = array_intersect_key($availableLabels, array_flip($options));

                if (!empty($options_create)) {
                    $can_show_list = true;
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($this->getPackageId(), "modules", "sitegroupmusic")) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'smcreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    } else {
                        $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitegroup_group', 'smcreate');
                        if (!$can_create) {
                            $can_show_list = false;
                            $this->addElement('Hidden', 'smcreate', array(
                                'value' => @array_search(@end($options_create), $options_create)
                            ));
                        }
                    }
                    if ($can_show_list) {
                        if (count($options_create) > 1) {
                            $this->addElement('Select', 'smcreate', array(
                                'label' => 'Music Creation Privacy',
                                'description' => 'Who may upload music for this group?',
                                'multiOptions' => $options_create,
                                'value' => @array_search(@end($options_create), $options_create),
                                'attribs' => array('class' => 'sg_quick_advanced'),
                            ));
                            $this->smcreate->getDecorator('Description')->setOption('placement', 'append');
                        } elseif (count($options_create) == 1) {
                            $this->addElement('Hidden', 'smcreate', array(
                                'value' => key($options_create),
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        } else {
                            $this->addElement('Hidden', 'smcreate', array(
                                'value' => 'registered',
                                'order' => ++$orderPrivacyHiddenFields,
                            ));
                        }
                    }
                } else {
                    $this->addElement('Hidden', 'smcreate', array(
                        'value' => 'registered',
                        'order' => ++$orderPrivacyHiddenFields,
                    ));
                }
            }
        }
        //END SITEGROUPMUSIC PLUGIN WORK

        if (!empty($createFormFields) && in_array('subGroupPrivacy', $createFormFields)) {
            //START SUB GROUP WORK
            if (empty($parent_id)) {
                $available_Labels = array(
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'like_member' => 'Who Liked This Group',
                );
                if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                    $available_Labels['member'] = 'Group Members Only';
                } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                    $available_Labels['member'] = 'Group Members Only';
                }
                $available_Labels['owner'] = $ownerTitle;

                $subgroupcreate_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_sspcreate');
                $subgroupcreate_options = array_intersect_key($available_Labels, array_flip($subgroupcreate_options));

                $can_create = Engine_Api::_()->authorization()->getPermission($userlevel_id, 'sitegroup_group', 'sspcreate');
                $can_show_list = true;
                if (!$can_create) {
                    $can_show_list = false;
                    $this->addElement('Hidden', 'sspcreate', array(
                        'value' => @array_search(@end($subgroupcreate_options), $subgroupcreate_options)
                    ));
                }

                if (count($subgroupcreate_options) > 1 && !empty($can_show_list)) {
                    $this->addElement('Select', 'auth_sspcreate', array(
                        'label' => 'Sub Groups Creation Privacy',
                        'description' => 'Who may create sub groups in this group?',
                        'multiOptions' => $subgroupcreate_options,
                        'value' => @array_search(@end($subgroupcreate_options), $subgroupcreate_options),
                        'attribs' => array('class' => 'sg_quick_advanced'),
                    ));
                    $this->auth_sspcreate->getDecorator('Description')->setOption('placement', 'append');
                } elseif (count($subgroupcreate_options) == 1 && !empty($can_show_list)) {
                    $this->addElement('Hidden', 'auth_sspcreate', array(
                        'value' => key($subgroupcreate_options),
                        'order' => ++$orderPrivacyHiddenFields,
                    ));
                } elseif (!empty($can_show_list)) {
                    $this->addElement('Hidden', 'auth_sspcreate', array(
                        'value' => 'owner',
                        'order' => ++$orderPrivacyHiddenFields,
                    ));
                }
            }
        }
        //END WORK FOR SUBGROUP WORK.

        if (!empty($createFormFields) && in_array('claimThisGroup', $createFormFields)) {
            $table = Engine_Api::_()->getDbtable('listmemberclaims', 'sitegroup');
            $select = $table->select()
                    ->where('user_id = ?', $viewer_id)
                    ->limit(1);

            $row = $table->fetchRow($select);
            if ($row !== null) {
                $this->addElement('Checkbox', 'userclaim', array(
                    'label' => 'Show "Claim this Group" link on this group.',
                    'value' => 1,
                    'attribs' => array('class' => 'sg_quick_advanced'),
                ));
            }
        }

        if (!empty($createFormFields) && in_array('status', $createFormFields)) {
            $this->addElement('Select', 'draft', array(
                'label' => 'Status',
                'multiOptions' => array("1" => "Published", "0" => "Saved As Draft"),
                'description' => 'If this entry is published, it cannot be switched back to draft mode.',
                'onchange' => 'checkDraft();',
                'attribs' => array('class' => 'sg_quick_advanced'),
            ));
            $this->draft->getDecorator('Description')->setOption('placement', 'append');
        }

        $searchText = "Show this group in search results.";
        if (!empty($createFormFields) && in_array('search', $createFormFields)) {
            $this->addElement('Checkbox', 'search', array(
                'label' => $searchText,
                'value' => 1,
                'attribs' => array('class' => 'sg_quick_advanced'),
            ));
        }

        // Element: execute
        $this->addElement('Button', 'execute', array(
            'label' => 'Create',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            // 'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitegroup_general', true),
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // DisplayGroup: buttons
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
