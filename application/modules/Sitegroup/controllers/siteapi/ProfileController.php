<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    ProfileController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_ProfileController extends Siteapi_Controller_Action_Standard {

    /**
     * Auth checkup and creating the subject.
     * 
     */
    public function init() {
        // Authorization check
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireAuth()->setAuthParams('sitegroup_group', $viewer, "view")->isValid())
            $this->respondWithError('unauthorized');
        if ($this->getRequestParam("group_id") && (0 !== ($group_id = (int) $this->getRequestParam("group_id")) &&
                null !== ($group = Engine_Api::_()->getItem('sitegroup_group', $group_id)))) {
            Engine_Api::_()->core()->setSubject($group);
        }
    }

    /**
     * Returns the profile listing of the particular Directory Page
     * 
     * 
     */
    public function viewAction() {
        $this->validateRequestMethod();
        // Get subject
        if (Engine_Api::_()->core()->hasSubject('sitegroup_group'))
            $sitegroup = $subject = Engine_Api::_()->core()->getSubject('sitegroup_group');

        // Return if no subject available.
        if (empty($subject))
            $this->respondWithError('no_record');

        // viewer information
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $bodyParams = $tagArray = array();

        $bodyParams['response'] = $subject->toArray();

        // Set the price & currency 
        if (isset($bodyParams['response']['price']) && $bodyParams['response']['price'] > 0) {
            $bodyParams['response']['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
            $bodyParams['response']['price'] = (int) $bodyParams['response']['price'];
        } else if (isset($bodyParams['response']['price']))
            unset($bodyParams['response']['price']);

        $contentURL = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($subject);

        if (isset($contentURL) && !empty($contentURL))
            $bodyParams['response'] = array_merge($bodyParams['response'], $contentURL);
        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject);
        $getOwnerImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject, true);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
        $bodyParams['response'] = array_merge($bodyParams['response'], $getOwnerImages);
        $bodyParams['response']["owner_title"] = $subject->getOwner()->getTitle();

        // Getting viewer like or not to content.
        $bodyParams['response']["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($subject);

        // Getting like count.
        $bodyParams['response']["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($subject);

        // Get tags
        $PageTags = $subject->tags()->getTagMaps();
        if (isset($PageTags) && !empty($PageTags)) {
            foreach ($PageTags as $tag) {
                $tagArray[$tag->getTag()->tag_id] = $tag->getTag()->text;
            }
        }

        // Page View Count Increment
        if (!$subject->isOwner($viewer)) {
            Engine_Api::_()->getDbtable('groups', 'sitegroup')->update(array(
                'view_count' => new Zend_Db_Expr('view_count + 1'),
                    ), array(
                'group_id = ?' => $subject->getIdentity(),
            ));
        }

        $bodyParams['response']['tags'] = $tagArray;

        $categoryObj = Engine_Api::_()->getItem('sitegroup_category', $bodyParams['response']['category_id']);
        if (!empty($categoryObj))
            $bodyParams['response']['category_title'] = $categoryObj->getTitle();

        if (isset($sitegroup->profile_type) && !empty($sitegroup->profile_type))
            $bodyParams['response']['profile_fields'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitegroup')->getInformation($sitegroup, true);

        if (!empty($categoryObj))
            $bodyParams['response']['profile_fields']['Category'] = $categoryObj->getTitle();

        if (isset($bodyParams['response']['location']) && !empty($bodyParams['response']['location']))
            $bodyParams['response']['profile_fields']['Location'] = $bodyParams['response']['location'];

        $bodyParams['response']['body'] = @str_replace('src="/', 'src="' . $this->getHost . '/', $bodyParams['response']['body']);
        $followsData = Engine_Api::_()->getDbTable('follows', 'seaocore')->getFollow($subject, $viewer);
        $bodyParams['isGroupFollowed'] = 0;
        if ($followsData)
            $bodyParams['response']['isGroupFollowed'] = 1;

        // Getting the gutter-menus.
        if ($this->getRequestParam('gutter_menu', true))
            $bodyParams['gutterMenu'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitegroup')->gutterMenus($subject);

        if ($this->getRequestParam('tabs_menu', true))
            $bodyParams['profile_tabs'] = $this->_tabsMenus($subject);
        $this->respondWithSuccess($bodyParams, true);
    }

    /**
     * Deletes the directory Page
     * 
     * 
     */
    public function deleteAction() {

        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        // Get group id and object
        $group_id = $this->_getParam('group_id');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        // Return if no subject available.
        if (empty($sitegroup))
            $this->respondWithError('no_record');

        // Start manage-admin check
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'delete');
        if (empty($isManageAdmin)) {
            $this->respondWithError('unauthorized');
        }
        // End manage-admin check

        $getSubGroupids = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getsubGroupids($group_id);

        try {
            foreach ($getSubGroupids as $getSubGroupid) {
                Engine_Api::_()->sitegroup()->onPageDelete($getSubGroupid['group_id']);
            }
            // End sub-group work
            Engine_Api::_()->sitegroup()->onGroupDelete($group_id);
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Edits the Directory group
     * 
     */
    public function editAction() {

        // Get the group id and object
        $group_id = $this->_getParam('group_id');
        $sitegroup = $subject = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        if (!$subject)
            $this->respondWithError('no_record');

        // Start manage admin check
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            $this->respondWithError('unauthorized');
        }

        $previous_category_id = $sitegroup->category_id;
        $PageTags = $subject->tags()->getTagMaps();
        $tagString = '';

        foreach ($PageTags as $tagmap) {

            if ($tagString !== '')
                $tagString .= ', ';
            $tagString .= $tagmap->getTag()->getTitle();
        }
        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();

        $viewer_id = $viewer->getIdentity();

        if ($this->getRequest()->isGet()) {
            $form_fields = Engine_Api::_()->getApi('Siteapi_Core', 'sitegroup')->getForm($sitegroup);
            $form_fields['formValues'] = array();
            $form_fields['formValues'] = $subject->toArray();
            $form_fields['formValues']['tags'] = $tagString;

            // Custom field work
            $categoryIds = array();
            $categoryIds[] = $sitegroup->category_id;
            $categoryIds[] = $sitegroup->subcategory_id;
            $categoryIds[] = $sitegroup->subsubcategory_id;

            $profile_type = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getProfileType($categoryIds, 0, 'profile_type');

            $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('sitegroup_group');
            $fieldValues = Engine_Api::_()->fields()->getFieldsValues($sitegroup);
            $fieldValuesArray = array();

            if (!empty($fieldValues))
                foreach ($fieldValues as $value)
                    $fieldValuesArray[$value->field_id] = $value->value;

            // Getting profile fields
            $getRowsMatching = $mapData->getRowsMatching('option_id', $profile_type);
            $fieldValuesResponse = array();
            if (!empty($getRowsMatching)) {
                foreach ($getRowsMatching as $value) {
                    if (array_key_exists($value->child_id, $fieldValuesArray)) {
                        $key = $value->field_id . '_' . $value->option_id . '_' . $value->child_id . '_field_' . $value->child_id;
                        $fieldValuesResponse[$key] = $fieldValuesArray[$value->child_id];
                    }
                }
                if (!empty($fieldValuesResponse))
                    $form_fields['formValues'] = array_merge($form_fields['formValues'], $fieldValuesResponse);
            }

            $this->respondWithSuccess($form_fields, true);
        }
        else if ($this->getRequest()->isPost() || $this->getRequest()->isPut()) {

            $package = Engine_Api::_()->getItem('sitegroup_package', $sitegroup->package_id);
            $sitegroupUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupurl');
            $table = Engine_Api::_()->getItemTable('sitegroup_group');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {

                // Create sitegroup
                $values = $this->getAllParams();

                // Start form validation
                $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitegroup')->getFormValidators($sitegroup, $values);
                $values['validators'] = $validators;
                $validationMessage = $this->isValid($values);

                // Response validation error
                if (!empty($validationMessage) && @is_array($validationMessage)) {
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }

                $values = array_merge($values, array(
                    'owner_id' => $viewer->getIdentity(),
                ));

                $is_error = 0;
                if (isset($values['category_id']) && empty($values['category_id'])) {
                    $is_error = 1;
                }
                if (empty($values['subcategory_id'])) {
                    $values['subcategory_id'] = 0;
                }
                if (empty($values['subsubcategory_id'])) {
                    $values['subsubcategory_id'] = 0;
                }

                $values['profile_type'] = $data['profile_type'] = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getProfileType($values, 0, 'profile_type');
                if (isset($values['profile_type']) && !empty($values['profile_type'])) {
                    // START FORM VALIDATION
                    $profileFieldsValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitegroup')->getFieldsFormValidations($values);
                    $values['validators'] = $profileFieldsValidators;
                    $profileFieldsValidationMessage = $this->isValid($values);
                }

                if (is_array($eventValidationMessage) && is_array($profileFieldsValidationMessage))
                    $validationMessage = array_merge($eventValidationMessage, $profileFieldsValidationMessage);
                else if (is_array($eventValidationMessage))
                    $validationMessage = $eventValidationMessage;
                else if (is_array($profileFieldsValidationMessage))
                    $validationMessage = $profileFieldsValidationMessage;
                else
                    $validationMessage = 1;

                if (!empty($validationMessage) && @is_array($validationMessage)) {
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }

                if (Engine_Api::_()->getApi('subCore', 'sitegroup')->groupBaseNetworkEnable()) {
                    if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                        if (in_array(0, $values['networks_privacy'])) {
                            $values['networks_privacy'] = new Zend_Db_Expr('NULL');
                        } else {
                            $values['networks_privacy'] = (string) ( is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds );
                        }
                    }
                }

                $sitegroup->setFromArray($values);

                $user_level = $viewer->level_id;

                if (!empty($sitegroup->approved)) {
                    $sitegroup->pending = 0;
                    $sitegroup->aprrove_date = date('Y-m-d H:i:s');

                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        $expirationDate = $package->getExpirationDate();
                        if (!empty($expirationDate))
                            $sitegroup->expiration_date = date('Y-m-d H:i:s', $expirationDate);
                        else
                            $sitegroup->expiration_date = '2250-01-01 00:00:00';
                    }
                    else {
                        $sitegroup->expiration_date = '2250-01-01 00:00:00';
                    }
                }

                $sitegroup->save();
                $group_id = $sitegroup->group_id;

                if (!empty($sitegroup->approved)) {
                    Engine_Api::_()->sitegroup()->sendMail("ACTIVE", $sitegroup->group_id);
                } else {
                    Engine_Api::_()->sitegroup()->sendMail("APPROVAL_PENDING", $sitegroup->group_id);
                }

                $group_id = $sitegroup->group_id;
                if (!empty($sitegroupUrlEnabled)) {
                    $values['group_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
                    if (!empty($count_index) || !empty($count_index_url)) {
                        $values['group_url'] = $values['group_url'] . '-' . $group_id;
                        $table->update(array('group_url' => $values['group_url']), array('group_id = ?' => $group_id));
                    } else {
                        $values['group_url'] = $values['group_url'];
                        $table->update(array('group_url' => $values['group_url']), array('group_id = ?' => $group_id));
                    }
                }

                $sitegroupFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform');
                if ($sitegroupFormEnabled) {
                    $tablecontent = Engine_Api::_()->getDbtable('content', 'core');
                    $params = $tablecontent->select()
                                    ->from($tablecontent->info('name'), 'params')
                                    ->where('name = ?', 'sitegroupform.sitegroup-viewform')
                                    ->query()->fetchColumn();
                    $decodedParam = Zend_Json::decode($params);
                    $tabName = $decodedParam['title'];
                    if (empty($tabName))
                        $tabName = 'Form';
                    $sitegroupformtable = Engine_Api::_()->getDbtable('sitegroupforms', 'sitegroupform');
                    $optionid = Engine_Api::_()->getDbtable('groupquetions', 'sitegroupform');
                    $table_option = Engine_Api::_()->fields()->getTable('sitegroupform', 'options');
                    $sitegroupform = $table_option->createRow();
                    $sitegroupform->setFromArray($values);
                    $sitegroupform->label = $values['title'];
                    $sitegroupform->field_id = 1;
                    $option_id = $sitegroupform->save();
                    $optionids = $optionid->createRow();
                    $optionids->option_id = $option_id;
                    $optionids->group_id = $group_id;
                    $optionids->save();
                    $sitegroupforms = $sitegroupformtable->createRow();
                    if (isset($sitegroupforms->offer_tab_name))
                        $sitegroupforms->offer_tab_name = $tabName;
                    $sitegroupforms->description = 'Please leave your feedback below and enter your contact details.';
                    $sitegroupforms->group_id = $group_id;
                    $sitegroupforms->save();
                }

                // Set photo
                if (!empty($_FILES)) {
                    Engine_Api::_()->getApi('Siteapi_Core', 'sitegroup')->setPhoto($_FILES['photo'], $sitegroup);
                    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitegroup');
                    $album_id = $albumTable->update(array('photo_id' => $sitegroup->photo_id), array('group_id = ?' => $sitegroup->group_id));
                }


                // Add tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $tags = array_filter(array_map("trim", $tags));
                $sitegroup->tags()->addTagMaps($viewer, $tags);

                if (!empty($group_id)) {
                    $sitegroup->setLocation();
                }

                // Set privacy
                $auth = Engine_Api::_()->authorization()->context;

                // Get the group admin list.
                $ownerList = $sitegroup->getGroupOwnerList();

                $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
                if (!empty($sitegroupmemberEnabled)) {
                    $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }

                if (!isset($values['auth_view']) || empty($values['auth_view'])) {
                    $values['auth_view'] = "everyone";
                }

                if (!isset($values['auth_comment']) || empty($values['auth_comment'])) {
                    $values['auth_comment'] = "everyone";
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitegroup, $role, 'view', ($i <= $viewMax));
                    $auth->setAllowed($sitegroup, $role, 'comment', ($i <= $commentMax));
                    $auth->setAllowed($sitegroup, $role, 'print', 1);
                    $auth->setAllowed($sitegroup, $role, 'tfriend', 1);
                    $auth->setAllowed($sitegroup, $role, 'overview', 1);
                    $auth->setAllowed($sitegroup, $role, 'map', 1);
                    $auth->setAllowed($sitegroup, $role, 'insight', 1);
                    $auth->setAllowed($sitegroup, $role, 'layout', 1);
                    $auth->setAllowed($sitegroup, $role, 'contact', 1);
                    $auth->setAllowed($sitegroup, $role, 'form', 1);
                    $auth->setAllowed($sitegroup, $role, 'offer', 1);
                    $auth->setAllowed($sitegroup, $role, 'invite', 1);
                }

                $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
                if (!empty($sitegroupmemberEnabled)) {
                    $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }

                // Start work on sub-group
                if (empty($values['sspcreate'])) {
                    $values['sspcreate'] = "owner";
                    $values['auth_sspcreate'] = "owner";
                }

                $createMax = array_search($values['auth_sspcreate'], $roles);
                foreach ($roles as $i => $role) {
                    if ($role === 'like_member') {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitegroup, $role, 'sspcreate', ($i <= $createMax));
                }
                // End work on sub-group
                // Start sitegroupdiscussion plugin work
                $sitegroupdiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion');
                if ($sitegroupdiscussionEnabled) {

                    // Start discussion privacy work
                    if (empty($values['sdicreate'])) {
                        $values['sdicreate'] = "registered";
                    }

                    $createMax = array_search($values['sdicreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'sdicreate', ($i <= $createMax));
                    }
                    // End discussion privacy work
                }
                // End sitegroupdiscussion plugin work
                // Start sitegroupalbum plugin work
                $sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
                if ($sitegroupalbumEnabled) {

                    // Start photo privacy work
                    if (empty($values['spcreate'])) {
                        $values['spcreate'] = "registered";
                    }

                    $createMax = array_search($values['spcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'spcreate', ($i <= $createMax));
                    }
                    // End photo privacy work
                }
                // End sitegroupalbum plugin work
                // Start sitegroupdocument plugin work
                $sitegroupDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument');
                if ($sitegroupDocumentEnabled) {
                    $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
                    if (!empty($sitegroupmemberEnabled)) {
                        $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                    } else {
                        $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                    }

                    if (empty($values['sdcreate'])) {
                        $values['sdcreate'] = "registered";
                    }

                    $createMax = array_search($values['sdcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'sdcreate', ($i <= $createMax));
                    }
                }
                // End sitegroupdocument plugin work 
                // Start sitegroupvideo plugin work
                $sitegroupVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo');
                if ($sitegroupVideoEnabled || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
                    if (empty($values['svcreate'])) {
                        $values['svcreate'] = "registered";
                    }

                    $createMax = array_search($values['svcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'svcreate', ($i <= $createMax));
                    }
                }
                // End sitegroupvideo plugin work
                // Start sitegrouppoll plugin work
                $sitegroupPollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll');
                if ($sitegroupPollEnabled) {
                    if (empty($values['splcreate'])) {
                        $values['splcreate'] = "registered";
                    }

                    $createMax = array_search($values['splcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'splcreate', ($i <= $createMax));
                    }
                }
                // End sitegrouppoll plugin work
                // Start sitegroupnote plugin work
                $sitegroupNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote');
                if ($sitegroupNoteEnabled) {
                    if (empty($values['sncreate'])) {
                        $values['sncreate'] = "registered";
                    }

                    $createMax = array_search($values['sncreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'sncreate', ($i <= $createMax));
                    }
                }


                // End sitegroupnote plugin work
                // Start sitegroupmusic plugin work
                $sitegroupMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic');
                if ($sitegroupMusicEnabled) {
                    if (empty($values['smcreate'])) {
                        $values['smcreate'] = "registered";
                    }

                    $createMax = array_search($values['smcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'smcreate', ($i <= $createMax));
                    }
                }
                // End sitegroupmusic plugin work
                // Start sitegroupevent plugin work
                $sitegroupeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
                if ($sitegroupeventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
                    if (empty($values['secreate'])) {
                        $values['secreate'] = "registered";
                    }

                    $createMax = array_search($values['secreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'secreate', ($i <= $createMax));
                    }
                }
                // End sitegroupevent plugin work
                // Start sitegroupmember plugin work
                $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
                if ($sitegroupmemberEnabled && $previous_category_id != $sitegroup->category_id) {
                    $db->query("UPDATE `engine4_sitegroup_membership` SET `role_id` = '0' WHERE `engine4_sitegroup_membership`.`group_id` = " . $sitegroup->group_id . ";");
                }
                $memberInvite = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.invite.option', 1);
                $member_approval = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.approval.option', 1);
                if (empty($memberInvite)) {
                    $memberInviteOption = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.invite.automatically', 1);
                    $sitegroup->member_invite = $memberInviteOption;
                    $sitegroup->save();
                }
                if (empty($member_approval)) {
                    $member_approvalOption = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.approval.automatically', 1);
                    $sitegroup->member_approval = $member_approvalOption;
                    $sitegroup->save();
                }

                // End store integration work
                // Start sub-groups work
                $parent_id = $this->_getParam('parent_id');
                if (!empty($parent_id)) {
                    $sitegroup->subgroup = 1;
                    $sitegroup->parent_id = $parent_id;
                    $sitegroup->save();
                }
                // End sub-groups work
                // Custom field work
                $categoryIds = array();
                $categoryIds[] = $values['category_id'];
                $categoryIds[] = $values['subcategory_id'];
                $categoryIds[] = $values['subsubcategory_id'];

                $values['profile_type'] = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getProfileType($categoryIds, 0, 'profile_type');

                // Update the profile field on profile field values
                $fieldValues = Engine_Api::_()->fields()->getFieldsValues($sitegroup);
                $fieldvalue = $fieldValues->getRowsMatching(array(
                    'item_id' => $sitegroup->group_id,
                    'field_id' => 1,
                ));

                if (!empty($fieldvalue) && count($fieldvalue) == 1) {
                    $fieldvalue[0]->value = $values['profile_type'];
                    $fieldvalue[0]->save();
                }

                if (isset($values['profile_type']) && !empty($values['profile_type'])) {

                    // Start form validation
                    $profileFieldsValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitegroup')->getFieldsFormValidations($values);
                    $values['validators'] = $profileFieldsValidators;
                    $profileFieldsValidationMessage = $this->isValid($values);
                }

                if (is_array($eventValidationMessage) && is_array($profileFieldsValidationMessage))
                    $validationMessage = array_merge($eventValidationMessage, $profileFieldsValidationMessage);

                else if (is_array($eventValidationMessage))
                    $validationMessage = $eventValidationMessage;
                else if (is_array($profileFieldsValidationMessage))
                    $validationMessage = $profileFieldsValidationMessage;
                else
                    $validationMessage = 1;

                if (!empty($validationMessage) && @is_array($validationMessage)) {
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }

                // Custom field work
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.profile.fields', 1)) {

                    $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('sitegroup_group');

                    // Getting profile fields
                    $getRowsMatching = $mapData->getRowsMatching('option_id', $values['profile_type']);
                    $fieldValues = Engine_Api::_()->fields()->getFieldsValues($sitegroup);

                    // Looking for data in form post and inserting in field values
                    if (!empty($getRowsMatching)) {
                        foreach ($getRowsMatching as $field) {
                            $key = $field->field_id . '_' . $field->option_id . '_' . $field->child_id . '_field_' . $field->child_id;
                            if (isset($values[$key]) && !empty($values[$key])) {
                                $fieldvalue = $fieldValues->getRowsMatching(array(
                                    'field_id' => $field->child_id,
                                    'item_id' => $sitegroup->group_id,
                                ));

                                if (!empty($fieldvalue)) {
                                    $fieldvalue[0]->value = $values[$key];
                                    $fieldvalue[0]->save();
                                } else {
                                    $valuesRow = $fieldValues->createRow();
                                    $valuesRow->field_id = $field->child_id;
                                    $valuesRow->item_id = $sitegroup->group_id;
                                    $valuesRow->index = 0;
                                    $valuesRow->value = $values[$key];
                                    $valuesRow->save();
                                }
                            }
                        }
                    }
                }

                // Commit
                $db->commit();
                $this->successResponseNoContent('no_content');
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Close/Open the directory group
     * 
     *
     */
    public function closeAction() {
        // Check method
        $this->validateRequestMethod('POST');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $this->_getParam('group_id'));

        // Get Viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Start manage-admin check
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            $this->respondWithError('unauthorized');
        }
        // End manage-admin check

        $db = Engine_Api::_()->getDbtable('groups', 'sitegroup')->getAdapter();
        $db->beginTransaction();
        try {
            $sitegroup->closed = !$sitegroup->closed;
            $sitegroup->save();
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $db->rollback();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Publish the directory group
     * 
     *
     */
    public function publishAction() {

        // Check method
        $this->validateRequestMethod("POST");

        // Check user validation
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $group_id = $this->_getParam('group_id');

        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Start manage-admin check
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            $this->respondWithError('unauthorized');
        }
        // End manage-admin check

        $db = Engine_Api::_()->getDbtable('groups', 'sitegroup')->getAdapter();
        $db->beginTransaction();
        $search = $this->_getParam('search');
        $search = (isset($search) && !empty($search)) ? $search : 0;

        try {
            $sitegroup->modified_date = new Zend_Db_Expr('NOW()');
            $sitegroup->draft = 1;
            $sitegroup->search = $search;
            $sitegroup->save();
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollback();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     *  Claim a Directory Page
     */
    public function claimAction() {

        // Get logged in user information   
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $group_id = $this->_getParam('group_id');

        $sitegroup = $subject = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        if (empty($sitegroup))
            $this->respondWithError('no_record');

        // Get level id
        $level_id = 0;
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $authorizationTable = Engine_Api::_()->getItemTable('authorization_level');
            $authorization = $authorizationTable->fetchRow(array('type = ?' => 'public', 'flag = ?' => 'public'));
            if (isset($authorization) && !empty($authorization))
                $level_id = $authorization->level_id;
        }


        // check user have to allow claim of not
        $allow_claim = Engine_Api::_()->authorization()->getPermission($level_id, 'sitegroup_group', 'claim');
        $getPackageClaim = Engine_Api::_()->sitegroup()->getPackageAuthInfo('sitegroup');
        if (empty(Engine_Api::_()->getApi('settings', 'core')->sitegroup_claimlink) || empty($allow_claim)) {
            $this->respondWithError('unauthorized');
        }

        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Sitegroup')->getClaimForm();
            $this->respondWithSuccess($response, true);
        } elseif ($this->getRequest()->isPost()) {
            $value = $this->_getAllParams();
            if (empty($value['email']))
                $value['email'] = $viewer->email;

            if (empty($value['nickname']))
                $value['nickname'] = $viewer->displayname;

            $items = array();
            $items['group_id'] = $group_id;
            $items['viewer_id'] = $viewer_id;

            $claimgroups = Engine_Api::_()->getDbtable('claims', 'sitegroup')->getClaimStatus($items);
            if (!empty($claimgroups)) {
                if ($claimgroups->status == 3 || $claimgroups->status == 4) {
                    $error = "You have already filed a claim for the group: $sitegroup->title, which is either on hold or is awaiting action by administration.";
                    $this->respondWithError('already_claimed', $error);
                } elseif ($claimgroups->status == 2) {
                    $error = "You have already filed a claim for the group: $sitegroup->title , which has been declined by the site admin.";
                    $this->respondWithError('already_claimed', $error);
                }
            }

            $email = $values['email'];

            // Check email validation
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);
            if (!$validator->isValid($email)) {
                $this->respondWithValidationError('validation_fail', 'Invalid email address value');
            }

            // Get admin email
            $coreApiSettings = Engine_Api::_()->getApi('settings', 'core');
            $adminEmail = $coreApiSettings->getSetting('core.mail.contact', $coreApiSettings->getSetting('core.mail.from', "email@domain.com"));
            if (!$adminEmail)
                $adminEmail = $coreApiSettings->getSetting('core.mail.from', "email@domain.com");

            // Get claim table
            $tableClaim = Engine_Api::_()->getDbTable('claims', 'sitegroup');
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                if (!empty($getPackageClaim)) {

                    // Get sitegroup item
                    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

                    // Get group url
                    $group_url = Engine_Api::_()->sitegroup()->getGroupUrl($group_id);
                    $group_title = $sitegroup->title;

                    // Send claim email
                    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.claim.email', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup_claimlink', 1)) {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminEmail, 'SITEPAGE_CLAIMOWNER_EMAIL', array(
                            'group_title' => $group_title,
                            'group_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true) . '"  >' . $group_title . ' </a>',
                            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => $group_url), 'sitegroup_entry_view', true),
                            'email' => $coreApiSettings->getSetting('core.mail.from', "email@domain.com"),
                            'queue' => true
                        ));
                    }

                    $row = $tableClaim->createRow();
                    $row->group_id = $group_id;
                    $row->user_id = $viewer_id;
                    $row->about = $values['about'];
                    $row->nickname = $values['nickname'];
                    $row->email = $email;
                    $row->contactno = $values['contactno'];
                    $row->usercomments = $values['usercomments'];
                    $row->status = 3;
                    $row->save();
                }
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     *  Send a message to group owner
     */
    public function messageownerAction() {

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // Get viewer detail
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Get group id and group object
        $group_id = $this->_getParam("group_id");
        $sitegroup = $subject = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        if (empty($sitegroup))
            $this->respondWithError('no_record');

        // Page owner can't send message to himself
        if ($viewer_id == $sitegroup->owner_id)
            $this->respondWithError('unauthorized');

        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Sitegroup')->getMessageOwnerForm();
            $this->respondWithSuccess($response, true);
        } elseif ($this->getRequest()->isPost()) {

            // Get admins id for sending message
            $manageAdminData = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdmin($group_id);
            $manageAdminData = $manageAdminData->toArray();

            $recipients = array();
            if (!empty($manageAdminData)) {
                foreach ($manageAdminData as $key => $user_ids) {
                    $user_id = $user_ids['user_id'];
                    if ($viewer_id != $user_id) {
                        $recipients[] = $user_id;
                    }
                }
            }

            $values = $this->getAllParams();
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitegroup')->getMessageOwnerFormValidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
            $db->beginTransaction();

            try {

                // Limit recipients if it is not a special list of members
                $recipients = array_slice($recipients, 0, 1000);
                $recipients = array_unique($recipients);
                $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
                $sitegroup_title = $sitegroup->title;
                $group_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id)), 'sitegroup_entry_view') . ">$sitegroup_title</a>";
                $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
                        $viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . $this->translate("This message corresponds to the Page:") . $group_title_with_link
                );

                foreach ($recipientsUsers as $user) {
                    if ($user->getIdentity() == $viewer->getIdentity()) {
                        continue;
                    }

                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                            $user, $viewer, $conversation, 'message_new'
                    );
                }

                // Increment message counter
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     *  Message friends about this group
     */
    public function tellafriendAction() {
        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Get form
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Sitegroup')->getTellAFriendForm();
            if (isset($viewer_id) && !empty($viewer_id)) {
                $response['formValues']['sender_name'] = $viewer->displayname;
                $response['formValues']['sender_email'] = $viewer->email;
            }

            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {

            // Form validation
            // Get group id and object
            $group_id = $this->_getParam('group_id', $this->_getParam('group_id', null));
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

            if (empty($sitegroup))
                $this->respondWithError('no_record');


            // Get form values
            $values = $this->_getAllParams();

            // Start form validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitegroup')->tellaFriendFormValidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }
            // Explode email ids
            $reciver_ids = explode(',', $values['receiver_emails']);
            if (!empty($values['send_me'])) {
                $reciver_ids[] = $values['sender_email'];
            }
            $sender_email = $values['sender_email'];

            $heading = $sitegroup->title;

            // Check valid email id format
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);

            if (!$validator->isValid($sender_email)) {
                $this->respondWithValidationError('validation_fail', 'Invalid sender email address value');
            }

            if (!empty($reciver_ids)) {
                foreach ($reciver_ids as $receiver_id) {
                    $receiver_id = trim($receiver_id, ' ');
                    ($reciver_ids);
                    if (!$validator->isValid($receiver_id)) {
                        $this->respondWithValidationError('validation_fail', 'Please enter correct email address of the receiver(s).');
                    }
                }
            }

            $sender = $values['sender_name'];
            $message = $values['message'];
            try {
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITEGROUP_TELLAFRIEND_EMAIL', array(
                    'host' => $_SERVER['HTTP_HOST'],
                    'sender_name' => $sender,
                    'sender_email' => $sender_email,
                    'group_title' => $heading,
                    'message' => '<div>' . $message . '</div>',
                    'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()),
                    'queue' => true
                ));
            } catch (Exception $ex) {
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
            $this->successResponseNoContent('no_content', true);
        }
    }

    /**
     * Follow and unfollow a group
     */
    public function followAction() {
// Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!$viewer_id)
            $this->respondWithError('unauthorized');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
// Validate request method
        $this->validateRequestMethod("POST");
// Get page id and object
        $group_id = $this->_getParam('group_id');
        $sitegroup = $subject = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        if (empty($sitegroup) && !isset($sitegroup))
            $this->respondWithError('no_record');

        //ADD ACTIVITY FEED
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $resource_type = $sitegroup->getType();
        $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
        $follow = $followTable->getFollow($sitegroup, $viewer);
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            if ($follow) {
                $follow->delete();
                $sitegroup->follow_count = $sitegroup->follow_count - 1;
                $sitegroup->save();
                if ($viewer_id != $sitegroup->getOwner()->getIdentity()) {
                    //DELETE ACTIVITY FEED
                    $action_id = Engine_Api::_()->getDbtable('actions', 'activity')
                            ->select()
                            ->from('engine4_activity_actions', 'action_id')
                            ->where('type = ?', "follow_$resource_type")
                            ->where('subject_id = ?', $viewer_id)
                            ->where('subject_type = ?', 'user')
                            ->where('object_type = ?', $resource_type)
                            ->where('object_id = ?', $sitegroup->getIdentity())
                            ->query()
                            ->fetchColumn();

                    if (!empty($action_id)) {
                        $activity = Engine_Api::_()->getItem('activity_action', $action_id);
                        if (!empty($activity)) {
                            $activity->delete();
                        }
                    }
                }
            } else {
                $newrow = $followTable->createRow();
                $newrow->resource_type = $sitegroup->getType();
                $newrow->resource_id = $sitegroup->getIdentity();
                $newrow->poster_type = $viewer->getType();
                $newrow->poster_id = $viewer->getIdentity();
                $newrow->creation_date = date("Y-m-d H:i:s");
                $newrow->save();
                $sitegroup->follow_count = $sitegroup->follow_count + 1;
                $sitegroup->save();

                if ($viewer_id != $resource->getOwner()->getIdentity()) {

                    $action = $activityApi->addActivity($viewer, $sitegroup, 'follow_' . $resource_type, '', array(
                        'owner' => $sitegroup->getOwner()->getGuid(),
                    ));
                    if (!empty($action))
                        $activityApi->attachActivity($action, $sitegroup);
                }
            }
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $db->rollBack();
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Returns the tabs menu of the Directory Page
     * 
     * @return array
     */
    public function _tabsMenus() {
        if (!Engine_Api::_()->core()->hasSubject('group'))
            $sitegroup = $subject = Engine_Api::_()->core()->getSubject('sitegroup_group');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        $tabsMenu = array();

        // Prepare updated count
        $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
        $updates_count = $streamTable->select()
                        ->from($streamTable->info('name'), 'count(*) as count')
                        ->where('object_id = ?', $subject->group_id)
                        ->where('object_type = ?', "sitegroup_group")
                        ->where('target_type = ?', "sitegroup_group")
                        ->where('type like ?', "%post%")
                        ->query()->fetchColumn();

        $tabsMenu[] = array(
            'totalItemCount' => $updates_count,
            'name' => 'update',
            'label' => $this->translate('Updates'),
        );

        $tabsMenu[] = array(
            'name' => 'information',
            'label' => $this->translate('Info'),
            'url' => 'advancedgroup/information/' . $subject->getIdentity()
        );

        if ($sitegroup->overview) {
            $tabsMenu[] = array(
                'name' => 'overview',
                'label' => $this->translate('Overview'),
                'url' => 'advancedgroup/overview/' . $sitegroup->getIdentity()
            );
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
            $photos_count = Engine_Api::_()->getDbtable('photos', 'sitegroup')->countTotalPhotos(array('group_id' => $sitegroup->group_id));
            if ($photos_count > 0) {
                $tabsMenu[] = array(
                    'totalItemCount' => $photos_count,
                    'name' => 'photos',
                    'label' => $this->translate('Photos'),
                    'url' => 'advancedgroups/photos/index/' . $subject->getIdentity(),
                );
            }
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {

            $review_count = Engine_Api::_()->getDbtable('reviews', 'sitegroupreview')->totalReviews($sitegroup->group_id);
            if ($review_count > 0)
                $tabsMenu[] = array(
                    'totalItemCount' => $review_count,
                    'name' => 'reviews',
                    'label' => $this->translate('Reviews'),
                    'url' => 'advancedgroups/reviews/' . $subject->getIdentity(),
                );
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
            $videos_count = Engine_Api::_()->getDbtable('videos', 'sitegroupvideo')->getGroupVideoCount($sitegroup->group_id);
            if ($videos_count > 0)
                $tabsMenu[] = array(
                    'totalItemCount' => $videos_count,
                    'name' => 'videos',
                    'label' => $this->translate('Videos'),
                    'url' => 'advancedgroups/videos/index/' . $subject->getIdentity(),
                );
        }

        $values['group_id'] = $subject->group_id;
        $paginator = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getSitegroupmembersPaginator($values);
        $memberCount = $paginator->getTotalItemCount();
        if ($memberCount > 0) {
            $tabsMenu[] = array(
                'totalItemCount' => $memberCount,
                'name' => 'members',
                'label' => (empty($subject->member_title)) ? $this->translate('Members') : $this->translate($subject->member_title),
                'url' => 'advancedgroups/members/browse/' . $subject->getIdentity(),
            );
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
            $canBrowseOffer = 1;
            // PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupoffer")) {
                    $canBrowseOffer = 0;
                }
            } else {
                $groupOwnerBase = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'offer');
                if (empty($groupOwnerBase)) {
                    $canBrowseOffer = 0;
                }
            }

            $offerCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroupoffer', 'offers');
            if (empty($offerCount)) {
                $canBrowseOffer = 0;
            }
            if (!empty($canBrowseOffer)) {
                $tabsMenu[] = array(
                    'totalItemCount' => $offerCount,
                    'name' => 'offer',
                    'label' => $this->translate('Offers'),
                    'url' => 'advancedgroups/offers/' . $subject->getIdentity(),
                );
            }
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
            if ((Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $subject->getType(), 'item_module' => strtolower($subject->getModuleName()), 'checked' => 'enabled')))) {
                $eventCount = Engine_Api::_()->sitegroup()->getTotalCount($subject->getIdentity(), 'siteevent', 'events');
                if (!empty($eventCount)) {
                    $tabsMenu[] = array(
                        'totalItemCount' => $eventCount,
                        'name' => 'advevents',
                        'label' => $this->translate('Events'),
                        'url' => 'advancedevents/',
                        'urlParams' => array(
                            'parent_type' => 'sitegroup_group',
                            'parent_id' => $subject->getIdentity()
                        )
                    );
                }
            }
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview')) {
            $mixSettingsResults = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getIntegrationItems();
            foreach ($mixSettingsResults as $modNameValue) {
                if ($modNameValue['resource_type'] == 'sitereview_listing') {

                    //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY START
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", 'sitereview_listing' . '_' . $modNameValue['listingtype_id'])) {
                            continue;
                        }
                    } else {
                        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'sitereview_listing' . '_' . $modNameValue['listingtype_id']);
                        if (empty($isGroupOwnerAllow)) {
                            continue;
                        }
                    }

                    $params['resource_type'] = 'sitereview_listing';
                    $params['listingtype_id'] = $modNameValue['listingtype_id'];
                    $params['group_id'] = $subject->group_id;
                    $paginator = Engine_Api::_()->getDbtable('contents', 'sitegroupintegration')->getResults($params);
                    // Do not render if nothing to show
                    if ($paginator->getTotalItemCount() > 0) {
                        $tabsMenu[] = array(
                            'totalItemCount' => $paginator->getTotalItemCount(),
                            'name' => 'sitereview_listing',
                            'label' => $this->translate($modNameValue['item_title']),
                            'url' => 'advancedgrouplistings/' . $subject->getIdentity(),
                            'urlParams' => array(
                                'listingtype_id' => $modNameValue['listingtype_id'],
                            )
                        );
                    }
                }
            }
        }

        return $tabsMenu;
    }

    /**
     * Returns the basic information and profile fields of the Directory Page
     */
    public function informationAction() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // Validate request method
        $this->validateRequestMethod();

        // Get group id and object
        $group_id = $this->_getParam('group_id');
        $sitegroup = $subject = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        if (empty($sitegroup) && !isset($sitegroup))
            $this->respondWithError('no_record');
        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'sitegroup')->getInformation($sitegroup), true);
    }

    /*
     * Overview
     *
     */

    public function overviewAction() {
        // Validate request method
        $this->validateRequestMethod();

        // Get group id and object
        $group_id = $this->_getParam('group_id');
        $sitegroup = $subject = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        if (empty($sitegroup) && !isset($sitegroup))
            $this->respondWithError('no_record');

        $this->respondWithSuccess($sitegroup->overview, true);
    }

}
