<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LayoutController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_LayoutController extends Core_Controller_Action_Standard {

    //SET THE VALUE FOR ALL ACTION DEFAULT
    public function init() {

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
                ->addActionContext('rate', 'json')
                ->addActionContext('validation', 'html')
                ->initContext();

        $id = $this->_getParam('group_id', $this->_getParam('id', null));
        if ($id) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $id);
            if ($sitegroup) {
                Engine_Api::_()->core()->setSubject($sitegroup);
                //START MANAGE-ADMIN CHECK
                $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');

                if (empty($isManageAdmin)) {
                    return $this->_forward('requireauth', 'error', 'core');
                }
                //END MANAGE-ADMIN CHECK
            }
        }
    }

    public function layoutAction() {

        if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
            return false;
        }

        $edit_layout_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
        if (empty($edit_layout_setting)) {
            $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
        }

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

        //GET NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');

        //FINDING THE LAYOUT ID OF THIS GROUP
        $contentTable = Engine_Api::_()->getDbtable('content', 'sitegroup');
        $contentTableName = $contentTable->info('name');
        $contentGroupTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
        $contentGroupTableName = $contentGroupTable->info('name');
        $contentgroup_id = $contentGroupTable->select()
                ->from($contentGroupTableName, array('contentgroup_id'))
                ->where('group_id =?', $sitegroup->group_id)
                ->query()
                ->fetchColumn();
        //GET GROUP PARAM
        $group = $contentgroup_id;
        $contentTable = Engine_Api::_()->getDbtable('content', 'sitegroup');
        $this->view->adminDriven = 0;
        if (empty($group)) {
            $coregroupinfo = Engine_Api::_()->sitegroup()->getWidgetizedGroup();
            $coreGroupsTable = Engine_Api::_()->getDbtable('pages', 'core');
            $coreGroupsTableName = $coreGroupsTable->info('name');
            $contentCoreTable = Engine_Api::_()->getDbtable('content', 'core');
            $contentCoreTableName = $contentCoreTable->info('name');
            $adminContentTable = Engine_Api::_()->getDbtable('admincontent', 'sitegroup');
            $adminContentTableName = $adminContentTable->info('name');
            $contentgroup_id = $adminContentTable->select()
                    ->from($adminContentTableName, array('group_id'))
                    ->where('group_id =?', $coregroupinfo->page_id)
                    ->query()
                    ->fetchColumn();
            //GET CURRENT GROUP
            $this->view->groupObject = $groupObject = $coreGroupsTable->fetchRow($coreGroupsTable->select()->where('name = ?', 'sitegroup_index_view')->orWhere('page_id = ?', $contentgroup_id));
            if (null === $groupObject) {
                $group = 'core_index_index';
                $groupObject = $coreGroupsTable->fetchRow($coreGroupsTable->select()->where('name = ?', 'core_index_index'));
            }

            //GET REGISTERED CONTENT AREAS
            if (!empty($groupObject)) {
                $contentRowset = $adminContentTable->fetchAll($adminContentTable->select()->where('group_id = ?', $groupObject->page_id)->order('order ASC'));
                $contentStructure = $adminContentTable->prepareContentArea($contentRowset);
            }
            $this->view->adminDriven = 1;
        } else {
            //GET CURRENT GROUP
            $this->view->groupObject = $groupObject = $contentGroupTable->fetchRow($contentGroupTable->select()->where('user_id = ?', $viewer_id)->where('name = ?', $group)->orWhere('contentgroup_id = ?', $group));
            if (null === $groupObject) {
                $group = 'core_index_index';
                $groupObject = $groupTable->fetchRow($contentGroupTable->select()->where('name = ?', $group)->where('user_id = ?', $viewer_id));
            }
            //GET REGISTERED CONTENT AREAS
            if (!empty($groupObject)) {
                $contentRowset = $contentTable->fetchAll($contentTable->select()->where('contentgroup_id = ?', $groupObject->contentgroup_id)->order('order ASC'));
                $contentStructure = $contentGroupTable->prepareContentArea($contentRowset);
            }
        }
        $this->view->group = $group;
        $this->view->groupObject = $groupObject;

        //GET AVAILABLE CONTENT BLOCKS
        $this->view->contentAreas = $contentAreas = $this->buildCategorizedContentAreas($this->getContentAreas());

        $rows = Engine_Api::_()->getDbtable('hideprofilewidgets', 'sitegroup')->hideWidgets();
        $hideWidgets = array();
        foreach ($rows as $value)
            $hideWidgets[] = $value->widgetname;
        $this->view->hideWidgets = $hideWidgets;
        $contentByName = array();
        foreach ($contentAreas as $category => $categoryAreas) {
            foreach ($categoryAreas as $info) {
                $contentByName[$info['name']] = $info;
            }
        }
        $this->view->contentByName = $contentByName;

        //MAKE GROUP FORM
        $this->view->groupForm = $groupForm = new Sitegroup_Form_Layout_Content_Group();
        if (!empty($groupObject)) {
            $groupForm->populate($groupObject->toArray());
        } else {
            //return;
        }

        //VALIDATE STRUCTURE
        //NOTE: DO NOT VALIDATE FOR HEADER OR FOOTER
        $error = false;
        if ($groupObject->name !== 'header' && $groupObject->name !== 'footer') {
            foreach ($contentStructure as &$info1) {
                if (!in_array($info1['name'], array('top', 'bottom', 'main')) || $info1['type'] != 'container') {
                    $error = true;
                    break;
                }
                foreach ($info1['elements'] as &$info2) {
                    if (!in_array($info2['name'], array('left', 'middle', 'right')) || $info1['type'] != 'container') {
                        $error = true;
                        break;
                    }
                }
                //RE ORDER SECOND-LEVEL ELEMENTS
                usort($info1['elements'], array($this, '_reorderContentStructure'));
            }
        }

        if ($error) {
            $error_msg = Zend_Registry::get('Zend_Translate')->_('group failed validation check');
            throw new Exception($error_msg);
        }

        $this->view->showeditinwidget = array('seaocore.feed', 'activity.feed', 'sitegroup.info-sitegroup', 'sitegroup.overview-sitegroup', 'sitegroup.location-sitegroup', 'core.profile-links', 'sitegroup.discussion-sitegroup', 'sitegrouppoll.profile-sitegrouppolls', 'sitegroupevent.profile-sitegroupevents', 'sitegroupoffer.profile-sitegroupoffers', 'sitegroupdocument.profile-sitegroupdocuments', 'sitegroupform.sitegroup-viewform', 'sitegroupreview.profile-sitegroupreviews', 'sitegroupnote.profile-sitegroupnotes', 'sitegroupvideo.profile-sitegroupvideos', 'sitegroup.photos-sitegroup', 'sitegroupmusic.profile-sitegroupmusic', 'sitegroupintegration.profile-items', 'sitegrouptwitter.feeds-sitegrouptwitter', 'advancedactivity.home-feeds', 'sitegroupmember.profile-sitegroupmembers', 'siteevent.contenttype-events', 'sitevideo.contenttype-videos');

        //ASSIGN STRUCTURE
        $this->view->contentRowset = $contentRowset;
        $this->view->contentStructure = $contentStructure;

        $isSupport = null;
        $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
        /*
          return < 0 : when running version is lessthen 4.2.1
          return 0 : If running version is equal to 4.2.1
          return > 0 : when running version is greaterthen 4.2.1
         */
        if (!empty($coreVersion)) {
            $coreVersion = $coreVersion->version;
            $isPluginSupport = strcasecmp($coreVersion, '4.2.1');
            if ($isPluginSupport >= 0) {
                $isSupport = 1;
            }
        }
        if (!empty($isSupport)) {
            $this->renderScript('layout/layout.tpl');
        } else {
            $this->renderScript('layout/layout_default.tpl');
        }
    }

    public function updateAction() {

        $groupTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
        $contentTable = Engine_Api::_()->getDbtable('content', 'sitegroup');
        $db = $groupTable->getAdapter();
        $db->beginTransaction();

        try {
            //GET GROUP
            $group = $this->_getParam('group');
            $groupObject = $groupTable->fetchRow($groupTable->select()->where('name = ?', $group)->orWhere('contentgroup_id = ?', $group));
            if (null === $groupObject) {
                $message1 = Zend_Registry::get('Zend_Translate')->_('Group is missing');
                throw new Engine_Exception($message1);
            }

            //UPDATE LAYOUT
            if (null !== ($newLayout = $this->_getParam('layout'))) {
                $groupObject->layout = $newLayout;
                $groupObject->save();
            }

            //GET REGISTERED CONTENT AREAS
            $contentRowset = $contentTable->fetchAll($contentTable->select()->where('contentgroup_id = ?', $groupObject->contentgroup_id));

            //GET STRUCTURE
            $structure = Zend_Json::decode($this->_getParam('structure'));

            //DIFF
            $orderIndex = 1;
            $newRowsByTmpId = array();
            $existingRowsByContentId = array();

            foreach ($structure as $element) {
                //GET INFO
                $content_id = @$element['identity'];
                $tmp_content_id = @$element['tmp_identity'];
                $parent_id = @$element['parent_identity'];
                $tmp_parent_id = @$element['parent_tmp_identity'];

                $newOrder = $orderIndex++;

                //SANITY
                if (empty($content_id) && empty($tmp_content_id)) {
                    $message2 = Zend_Registry::get('Zend_Translate')->_('content id and tmp content id both empty');
                    throw new Exception($message2);
                }

                //GET EXISTING CONTENT ROW (IF ANY)
                $contentRow = null;
                if (!empty($content_id)) {
                    $contentRow = $contentRowset->getRowMatching('content_id', $content_id);
                    if (null === $contentRow) {
                        $message3 = Zend_Registry::get('Zend_Translate')->_('content row missing');
                        throw new Exception($message3);
                    }
                }

                //GET EXISTING PARENT ROW (IF ANY)
                $parentContentRow = null;
                if (!empty($parent_id)) {
                    $parentContentRow = $contentRowset->getRowMatching('content_id', $parent_id);
                } else if (!empty($tmp_parent_id)) {
                    $parentContentRow = @$newRowsByTmpId[$tmp_parent_id];
                }

                //EXISTING ROW
                if (!empty($contentRow) && is_object($contentRow)) {
                    $existingRowsByContentId[$content_id] = $contentRow;

                    //UPDATE ROW
                    if (!empty($parentContentRow)) {
                        $contentRow->parent_content_id = $parentContentRow->content_id;
                    }
                    if (empty($contentRow->parent_content_id)) {
                        $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
                    }
                    $session = new Zend_Session_Namespace();

                    //SET PARAMS
                    if (isset($session->setSomething) && in_array($element['name'], $session->setSomething)) {
                        $contentRow->params = json_encode($element['params']);
                        $contentRow->widget_admin = 0;
                    }
                    if ($contentRow->type == 'container') {
                        $newOrder = array_search($contentRow->name, array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
                    }

                    $contentRow->order = $newOrder;
                    $contentRow->save();
                }
                //NEW ROW
                else {
                    if (empty($element['type']) || empty($element['name'])) {
                        $message4 = Zend_Registry::get('Zend_Translate')->_('missing name and/or type info');
                        throw new Exception($message4);
                    }

                    if ($element['type'] == 'container') {
                        $newOrder = array_search($element['name'], array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
                    }

                    $contentRow = $contentTable->createRow();
                    $contentRow->contentgroup_id = $groupObject->contentgroup_id;
                    $contentRow->order = $newOrder;
                    $contentRow->type = $element['type'];
                    $contentRow->name = $element['name'];
                    $contentRow->widget_admin = 0;

                    //SET PARENT CONTENT
                    if (!empty($parentContentRow)) {
                        $contentRow->parent_content_id = $parentContentRow->content_id;
                    }
                    if (empty($contentRow->parent_content_id)) {
                        $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
                    }

                    $contentRow->params = json_encode($element['params']);
                    $contentRow->save();
                    $newRowsByTmpId[$tmp_content_id] = $contentRow;
                }
            }
            //DELETE ROWS THAT WERE NOT PRESENT IN DATA SENT BACK
            $deletedRowIds = array();
            foreach ($contentRowset as $contentRow) {
                if (empty($existingRowsByContentId[$contentRow->content_id])) {
                    $deletedRowIds[] = $contentRow->content_id;
                    $contentRow->delete();
                }
            }
            $this->view->deleted = $deletedRowIds;

            //SEND BACK NEW CONTENT INFO
            $newData = array();
            foreach ($newRowsByTmpId as $tmp_id => $newRow) {
                $newData[$tmp_id] = $groupTable->createElementParams($newRow);
            }
            $this->view->newIds = $newData;

            $this->view->status = true;
            $this->view->error = false;

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = true;
        }
        if (isset($session->setSomething))
            unset($session->setSomething);
    }

    public function createAction() {

        //GET GROUP PARAM
        $group = $this->_getParam('group');
        $groupTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
        $contentTable = Engine_Api::_()->getDbtable('content', 'sitegroup');

        //MAKE NEW GROUP
        if (($group == 'new' || $group === null) && $this->getRequest()->isPost()) {
            $groupObject = $groupTable->createRow();
            $groupObject->displayname = ( null !== ($name = $this->_getParam('name')) ? $name : 'Untitled' );
            $groupObject->save();

            //CREATE A CONTENT ROW FOR THIS GROUP
            $contentRow = $contentTable->createRow();
            $contentRow->type = 'container';
            $contentRow->name = 'main';
            $contentRow->contentgroup_id = $groupObject->contentgroup_id;
            $contentRow->save();

            $contentRow2 = $contentTable->createRow();
            $contentRow2->type = 'container';
            $contentRow2->name = 'middle';
            $contentRow2->contentgroup_id = $groupObject->contentgroup_id;
            $contentRow2->parent_content_id = $contentRow->content_id;
            $contentRow2->save();
        }

        if ($groupObject) {
            return $this->_redirectCustom($this->view->url(array('action' => 'index')) . '?group=' . $groupObject->contentgroup_id);
        } else {
            return $this->_redirectCustom($this->view->url(array('action' => 'index')));
        }
    }

    public function saveAction() {

        $form = new Sitegroup_Form_Layout_Content_Group();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $group_id = $values['contentgroup_id'];
            unset($values['contentgroup_id']);

            if (empty($values['url'])) {
                $values['url'] = new Zend_Db_Expr('NULL');
            }

            $groupTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
            $groupObject = $groupTable->fetchRow($groupTable->select()->where('name = ?', $group_id)->orWhere('contentgroup_id = ?', $group_id));
            $groupObject->setFromArray($values)->save();
            $form->addNotice($this->view->translate('Your changes have been saved.'));
        }

        $this->getResponse()->setBody($form->render($this->view));
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        return;
    }

    public function deleteAction() {

        $group_id = $this->_getParam('group');
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
            return;
        }

        $group = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup')->find($group_id)->current();
        if (null === $group) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Group not found');
            return;
        }

        if (!$group->custom) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Cannot delete non-custom groups');
            return;
        }

        $groupTable->deleteGroup($group);
        $this->view->status = true;
    }

    public function widgetAction() {

        $group_id = $this->_getParam('group_id');
        $mod = $this->_getParam('mod');
        //RENDER BY WIDGET NAME    
        $name = $this->_getParam('name');
        if (null === $name) {
            $message5 = Zend_Registry::get('Zend_Translate')->_('no widget found with name:');
            throw new Exception($message5 . $name);
        }
        if (null !== $mod) {
            $name = $mod . '.' . $name;
        }

        $contentInfoRaw = $this->getContentAreas();
        $contentInfo = array();
        foreach ($contentInfoRaw as $info) {
            $contentInfo[$info['name']] = $info;
        }

        //IT HAS A FORM SPECIFIED IN CONTENT MANIFEST
        if (!empty($contentInfo[$name]['adminForm'])) {
            if (is_string($contentInfo[$name]['adminForm'])) {
                $formClass = $contentInfo[$name]['adminForm'];
                Engine_Loader::loadClass($formClass);
                $this->view->form = $form = new $formClass();
            } else if (is_array($contentInfo[$name]['adminForm'])) {
                $this->view->form = $form = new Engine_Form($contentInfo[$name]['adminForm']);
            } else {
                throw new Core_Model_Exception('Unable to load admin form class');
            }

            //TRY TO SET TITLE IF MISSING
            if (!$form->getTitle()) {
                $form->setTitle('Editing: ' . $contentInfo[$name]['title']);
            }

            //TRY TO SET DESCRIPTION IF MISSING
            if (!$form->getDescription()) {
                $form->setDescription('placeholder');
            }

            $form->setAttrib('class', 'global_form_popup ' . $form->getAttrib('class'));

            //ADD TITLE ELEMENT
            if (!$form->getElement('title')) {
                $form->addElement('Text', 'title', array(
                    'label' => 'Title',
                    'order' => -100,
                ));
            }
            //ADD SUBMIT BUTTON
            if (!$form->getElement('submit') && !$form->getElement('execute')) {
                $form->addElement('Button', 'execute', array(
                    'label' => 'Save Changes',
                    'type' => 'submit',
                    'ignore' => true,
                    'decorators' => array(
                        'ViewHelper',
                    ),
                ));
            }

            //ADD NAME
            $form->addElement('Hidden', 'name', array(
                'value' => $name
            ));

            if (!$form->getElement('cancel')) {
                $form->addElement('Cancel', 'cancel', array(
                    'label' => 'cancel',
                    'link' => true,
                    'prependText' => ' or ',
                    'onclick' => 'parent.Smoothbox.close();',
                    'ignore' => true,
                    'decorators' => array(
                        'ViewHelper',
                    ),
                ));
            }

            if (!$form->getDisplayGroup('buttons')) {
                $submitName = ( $form->getElement('execute') ? 'execute' : 'submit' );
                $form->addDisplayGroup(array(
                    $submitName,
                    'cancel',
                        ), 'buttons', array(
                ));
            }

            //FORCE METHOD AND ACTION
            $form->setMethod('post')
                    ->setAction($_SERVER['REQUEST_URI']);

            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $this->view->values = $form->getValues();
                $this->view->form = null;
                $session = new Zend_Session_Namespace();
                if (isset($session->setSomething))
                    unset($session->setSomething);

                $session = new Zend_Session_Namespace();
                $session->setSomething[] = $name;
            }

            return;
        }

        //TRY TO RENDER ADMIN GROUP
        if (!empty($contentInfo[$name])) {
            try {
                $structure = array(
                    'type' => 'widget',
                    'name' => $name,
                    'request' => $this->getRequest(),
                    'action' => 'admin',
                    'throwExceptions' => true,
                );

                //CREATE ELEMENT (WITH STRUCTURE)
                $element = new Engine_Content_Element_Container(array(
                    'elements' => array($structure),
                    'decorators' => array(
                        'Children'
                    )
                ));

                $content = $element->render();
                $this->getResponse()->setBody($content);
                $this->_helper->viewRenderer->setNoRender(true);
                return;
            } catch (Exception $e) {
                
            }
        }

        //JUST RENDER DEFAULT EDITING FORM
        $this->view->form = $form = new Engine_Form(array(
            'title' => $contentInfo[$name]['title'],
            'description' => 'placeholder',
            'method' => 'post',
            'action' => $_SERVER['REQUEST_URI'],
            'class' => 'global_form_popup',
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                    )
                ),
                array(
                    'Button',
                    'submit',
                    array(
                        'label' => 'Save',
                        'type' => 'submit',
                        'decorators' => array('ViewHelper'),
                        'ignore' => true,
                        'order' => 1501,
                    )
                ),
                array(
                    'Hidden',
                    'name',
                    array(
                        'value' => $name,
                    )
                ),
                array(
                    'Cancel',
                    'cancel',
                    array(
                        'label' => 'cancel',
                        'link' => true,
                        'prependText' => ' or ',
                        'onclick' => 'parent.Smoothbox.close();',
                        'ignore' => true,
                        'decorators' => array('ViewHelper'),
                        'order' => 1502,
                    )
                )
            ),
            'displaygroups' => array(
                'buttons' => array(
                    'name' => 'buttons',
                    'elements' => array(
                        'submit',
                        'cancel',
                    ),
                    'options' => array(
                        'order' => 1500,
                    )
                )
            )
        ));

        if (!empty($contentInfo[$name]['isPaginated'])) {
            $form->addElement('Text', 'itemCountPerGroup', array(
                'label' => 'Count',
                'description' => 'Number of items to show.',
                'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
                ),
                'order' => 1000000 - 1,
            ));
        }

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $this->view->values = $form->getValues();
            $this->view->form = null;
            $session = new Zend_Session_Namespace();
            if (isset($session->setSomething))
                unset($session->setSomething);
            $session->setSomething[] = $name;
        } else {
            $form->populate($this->_getAllParams());
        }
    }

    public function getContentAreas() {

        $contentAreas = array();
        $levelModules = array("offer" => "sitegroupoffer", "form" => "sitegroupform", "invite" => "sitegroupinvite", "sdcreate" => "sitegroupdocument", "sncreate" => "sitegroupnote", "splcreate" => "sitegrouppoll", "secreate" => "sitegroupevent", "svcreate" => "sitegroupvideo", "spcreate" => "sitegroupalbum", "sdicreate" => "sitegroupdiscussion", "smcreate" => "sitegroupmusic", "smecreate" => "sitegroupmusic", "twitter" => "sitegrouptwitter");
        //$sitegroupintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration');
        //FROM MODULES
        $modules = Zend_Controller_Front::getInstance()->getControllerDirectory();
        $flag = 0;
        $integrated_module = '';
        foreach ($modules as $module => $path) {
            if ($module == 'sitegroup' || $module == 'core' || $module == 'sitegroupnote' || $module == 'activity' || $module == 'sitegroupdocument' || $module == 'sitegroupevent' || $module == 'sitegroupreview' || $module == 'sitegrouppoll' || $module == 'sitegroupvideo' || $module == 'sitegroupform' || $module == 'sitegroupdiscussion' || $module == 'sitegroupalbum' || $module == 'sitegroupoffer' || $module == 'sitegroupbadge' || $module == 'facebookse' || $module == 'sitelike' || $module == 'suggestion' || $module == 'sitegroupmusic' || $flag || $module == 'sitegrouptwitter' || $module == 'sitegroupmember' || $module == 'sitecontentcoverphoto' || $module == 'siteusercoverphoto' || $module == 'siteevent' || $module == 'sitevideo') {
                if ($module == 'activity' || $module == 'core' || $module == 'facebookse' || $module == 'sitelike' || $module == 'suggestion' || $module == 'sitecontentcoverphoto' || $module == 'siteusercoverphoto') {
                    $contentManifestFile = dirname($path) . '/settings/content.php';
                } else {
                    $addFile = true;
                    $subject = Engine_Api::_()->core()->getSubject('sitegroup_group');

                    if ($flag == 0) {
                        if ($module != 'sitegroup' && $module != 'sitegroupreview' && $module != 'sitegroupbadge' && $module != 'sitegrouptwitter') {
                            if ($module == 'siteevent') {
                                $module = 'sitegroupevent';
                            } else if ($module == 'sitevideo') {
                                $module = 'sitegroupvideo';
                            }
                            //PACKAGE BASE PRIYACY START
                            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                                if (!Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", $module)) {
                                    $addFile = false;
                                }
                            } else {
                                //non sub modules
                                $search_Key = array_search($module, $levelModules);
                                if (!empty($search_Key))
                                    $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, $search_Key);
                                if (empty($isGroupOwnerAllow)) {
                                    $addFile = false;
                                }
                            }
                            //PACKAGE BASE PRIYACY END
                        }
                    } elseif ($module == 'sitegrouptwitter') {
                        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'twitter');
                        if (empty($isManageAdmin)) {
                            $addFile = false;
                        }
                    }

                    $contentManifestFile = '';
                    if ($addFile)
                        $contentManifestFile = dirname($path) . '/settings/content_user.php';
                }
                if (!file_exists($contentManifestFile))
                    continue;
                $ret = include $contentManifestFile;
                $contentAreas = array_merge($contentAreas, (array) $ret);
            }
        }
        $grouplayout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layout.setting', 1);
        //$layoutBlockTable = Engine_Api::_()->getDbtable('layoutblocks', 'sitegroup');
        foreach ($contentAreas as $key => $item) {
            if ($grouplayout) {
                if ($item['name'] == 'core.content' || $item['name'] == 'core.theme-choose' || $item['name'] == 'core.menu-footer' || $item['name'] == 'core.menu-generic' || $item['name'] == 'core.menu-main' || $item['name'] == 'core.menu-mini' || $item['name'] == 'core.menu-logo' || $item['name'] == 'core.statistics' || $item['name'] == 'activity.list-requests' || $item['name'] == 'sitegroupevent.profile-photo' || $item['name'] == 'sitegroupevent.profile-options' || $item['name'] == 'sitegroupevent.profile-info' || $item['name'] == 'sitegroupevent.profile-rsvp' || $item['name'] == 'sitegroupevent.profile-members' || $item['name'] == 'sitegroupevent.profile-status' || $item['name'] == 'Facebookse.facebookse-recommendation' || $item['name'] == 'Facebookse.facebookse-activity' || $item['name'] == 'Facebookse.facebookse-facepile' || $item['name'] == 'Facebookse.facebookse-likebox' || $item['name'] == 'Facebookse.facebookse-websitelike' || $item['name'] == 'Facebookse.facebookse-groupprofilelike' ||
                        $item['name'] == '
Facebookse.facebookse-eventprofilelike' || $item['name'] == 'Facebookse.facebookse-userprofilelike' || $item['name'] == 'Facebookse.facebookse-listprofilelike' || $item['name'] == 'Facebookse.facebookse-sitegroupeventprofilelike' || $item['name'] == 'Suggestion.suggestion-classified' || $item['name'] == 'Suggestion.explore-friend' || $item['name'] == 'Suggestion.suggestion-album' || $item['name'] == 'Suggestion.suggestion-blog' || $item['name'] == 'Suggestion.suggestion-document' || $item['name'] == 'Suggestion.suggestion-event' || $item['name'] == 'Suggestion.suggestion-forum' || $item['name'] == 'Suggestion.suggestion-friend' || $item['name'] == 'Suggestion.suggestion-mix' || $item['name'] == 'Suggestion.suggestion-list' || $item['name'] == 'Suggestion.suggestion-music' || $item['name'] == 'Suggestion.suggestion-poll' || $item['name'] == 'Suggestion.suggestion-video' || $item['name'] == 'Suggestion.suggestion-group' || $item['name'] == 'sitelike.event-friend-like' || $item['name'] == 'sitelike.event-like' || $item['name'] == 'sitelike.event-like-button' || $item['name'] == 'sitelike.group-friend-like' || $item['name'] == 'sitelike.group-like' || $item['name'] == 'sitelike.group-like-button' || $item['name'] == '
sitelike.list-browse-mixlikes' || $item['name'] == 'sitelike.list-friend-like' || $item['name'] == 'sitelike.list-like' || $item['name'] == 'sitelike.list-like-album' || $item['name'] == 'sitelike.list-like-albumphoto' || $item['name'] == 'sitelike.list-like-blogs' || $item['name'] == 'sitelike.list-like-eventphotos' || $item['name'] == 'sitelike.list-like-events' || $item['name'] == 'sitelike.list-like-forum' || $item['name'] == 'sitelike.list-like-groupphotos' || $item['name'] == 'sitelike.list-like-groups' || $item['name'] == 'sitelike.list-like-listings' || $item['name'] == 'sitelike.list-like-members' || $item['name'] == 'sitelike.list-like-musics' || $item['name'] == 'sitelike.list-like-groups' || $item['name'] == 'sitelike.list-like-button' || $item['name'] == 'sitelike.list-like-classifieds' || $item['name'] == 'sitelike.list-like-document' || $item['name'] == 'sitelike.list-like-videos' || $item['name'] == 'sitelike.member-friend-like' || $item['name'] == 'sitelike.member-like' || $item['name'] == '
sitelike.mix-like' || $item['name'] == 'sitelike.navigation-like' || $item['name'] == 'sitelike.group-like' || $item['name'] == 'sitelike.profile-like-button' || $item['name'] == 'sitelike.profile-user-likes' || $item['name'] == 'sitelike.list-like-groupalbumphotos' || $item['name'] == 'sitelike.list-like-groupalbums' || $item['name'] == 'sitelike.list-like-groupdocuments' || $item['name'] == 'sitelike.list-like-groupevent' || $item['name'] == 'sitelike.list-like-groupnotes' || $item['name'] == 'sitelike.list-like-grouppolls' || $item['name'] == 'sitelike.list-like-groupreviews' || $item['name'] == 'sitelike.list-like-groupvideos' || $item['name'] == 'sitelike.list-like-recipe' || $item['name'] == 'sitelike.groupevent-friend-like' || $item['name'] == 'sitelike.groupevent-like' || $item['name'] == 'sitelike.groupevent-like-button' || $item['name'] == 'sitelike.recipe-friend-like' || $item['name'] == 'sitelike.recipe-like' || $item['name'] == 'sitelike.recipe-like-button' ||
                        $item['name'] == 'sitelike.sitegroupevent-like-
button' || $item['name'] == 'sitelike.list-like-polls' || $item['name'] == 'Facebookse.facebookse-comments' || $item['name'] == 'Facebookse.facebookse-commonlike'
                ) {
                    unset($contentAreas[$key]);
                }
            } else {
                if ($item['name'] == 'Suggestion.common-suggestion' || $item['name'] == 'core.content' || $item['name'] == 'core.theme-choose' || $item['name'] == 'core.menu-footer' || $item['name'] == 'core.menu-generic' || $item['name'] == 'core.menu-main' || $item['name'] == 'core.menu-mini' || $item['name'] == 'core.menu-logo' || $item['name'] == 'core.statistics' || $item['name'] == 'activity.list-requests' || $item['name'] == 'sitegroupevent.profile-photo' || $item['name'] == 'sitegroupevent.profile-options' || $item['name'] == 'sitegroupevent.profile-info' || $item['name'] == 'sitegroupevent.profile-rsvp' || $item['name'] == 'sitegroupevent.profile-members' || $item['name'] == 'sitegroupevent.profile-status' || $item['name'] == 'core.container-tabs' || $item['name'] == 'Facebookse.facebookse-recommendation' || $item['name'] == 'Facebookse.facebookse-activity' || $item['name'] == 'Facebookse.facebookse-facepile' || $item['name'] == 'Facebookse.facebookse-likebox' || $item['name'] == '
Facebookse.facebookse-
websitelike' || $item['name'] == 'Facebookse.facebookse-groupprofilelike' || $item['name'] == 'Facebookse.facebookse-eventprofilelike' || $item['name'] == 'Facebookse.facebookse-userprofilelike' || $item['name'] == 'Facebookse.facebookse-listprofilelike' || $item['name'] == 'Facebookse.facebookse-sitegroupeventprofilelike' || $item['name'] == 'Suggestion.suggestion-classified' || $item['name'] == 'Suggestion.explore-friend' || $item['name'] == 'Suggestion.suggestion-album' || $item['name'] == 'Suggestion.suggestion-blog' || $item['name'] == 'Suggestion.suggestion-document' || $item['name'] == 'Suggestion.suggestion-event' || $item['name'] == 'Suggestion.suggestion-forum' || $item['name'] == 'Suggestion.suggestion-friend' || $item['name'] == 'Suggestion.suggestion-mix' || $item['name'] == 'Suggestion.suggestion-list' || $item['name'] == 'Suggestion.suggestion-music' || $item['name'] == 'Suggestion.suggestion-poll' || $item['name'] == 'Suggestion.suggestion-video' || $item['name'] == 'Suggestion.suggestion-
group' || $item['name'] == 'sitelike.event-friend-like' || $item['name'] == 'sitelike.event-like' || $item['name'] == 'sitelike.event-like-button' || $item['name'] == 'sitelike.group-friend-like' || $item['name'] == 'sitelike.group-like' || $item['name'] == 'sitelike.group-like-button' || $item['name'] == 'sitelike.list-browse-mixlikes' || $item['name'] == 'sitelike.list-friend-like' || $item['name'] == 'sitelike.list-like' || $item['name'] == 'sitelike.list-like-album' || $item['name'] == 'sitelike.list-like-albumphoto' || $item['name'] == 'sitelike.list-like-blogs' || $item['name'] == 'sitelike.list-like-eventphotos' || $item['name'] == 'sitelike.list-like-events' || $item['name'] == 'sitelike.list-like-forum' || $item['name'] == 'sitelike.list-like-groupphotos' || $item['name'] == 'sitelike.list-like-groups' || $item['name'] == 'sitelike.list-like-listings' || $item['name'] == 'sitelike.list-like-members' || $item['name'] == 'sitelike.list-like-musics' || $item['name'] == 'sitelike.list-like-groups' ||
                        $item['name'] == 'sitelike.list-like-button' || $item['name'] == 'sitelike.list-like-classifieds' || $item['name'] == 'sitelike.list-like-document' || $item['name'] == 'sitelike.list-like-videos' || $item['name'] == 'sitelike.member-friend-like' || $item['name'] == 'sitelike.member-like' || $item['name'] == 'sitelike.mix-like' || $item['name'] == 'sitelike.navigation-like' || $item['name'] == 'sitelike.group-like' || $item['name'] == 'sitelike.profile-like-button' || $item['name'] == 'sitelike.profile-user-likes' || $item['name'] == 'sitelike.list-like-groupalbumphotos' || $item['name'] == 'sitelike.list-like-groupalbums' || $item['name'] == 'sitelike.list-like-groupdocuments' || $item['name'] == 'sitelike.list-like-groupevent' || $item['name'] == 'sitelike.list-like-groupnotes' || $item['name'] == 'sitelike.list-like-grouppolls' || $item['name'] == 'sitelike.list-like-groupreviews' || $item['name'] == 'sitelike.list-like-groupvideos' || $item['name'] == 'sitelike.list-like-recipe' || $item['name'] == 'sitelike.
groupevent-friend-like' || $item['name'] == 'sitelike.groupevent-like' || $item['name'] == 'sitelike.groupevent-like-button' || $item['name'] == 'sitelike.recipe-friend-like' || $item['name'] == 'sitelike.recipe-like' || $item['name'] == 'sitelike.recipe-like-button' || $item['name'] == 'sitelike.sitegroupevent-like-button' || $item['name'] == 'sitelike.list-like-polls'
                ) {
                    unset($contentAreas[$key]);
                }
            }
        }

        // From widgets
        $it = new DirectoryIterator(APPLICATION_PATH . '/application/widgets');
        foreach ($it as $dir) {
            if (!$dir->isDir() || $dir->isDot())
                continue;
            $path = $dir->getPathname();
            $contentManifestFile = $path . '/' . 'manifest.php';
            if (!file_exists($contentManifestFile))
                continue;
            $ret = include $contentManifestFile;
            if (!is_array($ret))
                continue;
            foreach ($ret as $key => $value) {
                if ((isset($value['name']) && $value['name'] == 'rss')) {
                    array_push($contentAreas, $ret);
                }
            }
        }

        return $contentAreas;
    }

    public function buildCategorizedContentAreas($contentAreas) {

        $categorized = array();
        foreach ($contentAreas as $config) {
            //CHECK SOME STUFF
            if (!empty($config['requireItemType'])) {
                if (is_string($config['requireItemType']) && !Engine_Api::_()->hasItemType($config['requireItemType'])) {
                    $config['disabled'] = true;
                } else if (is_array($config['requireItemType'])) {
                    $tmp = array_map(array(Engine_Api::_(), 'hasItemType'), $config['requireItemType']);
                    $config['disabled'] = !(array_sum($tmp) == count($config['requireItemType']));
                }
            }

            //ADD TO CATEGORY
            $category = ( isset($config['category']) ? $config['category'] : 'Uncategorized' );
            $categorized[$category][] = $config;
        }

        //SORT CATEGORIES
        uksort($categorized, array($this, '_sortCategories'));

        //SORT ITEMS IN CATEGORIES
        foreach ($categorized as $category => &$items) {
            usort($items, array($this, '_sortCategoryItems'));
        }

        return $categorized;
    }

    protected function _sortCategories($a, $b) {

        if ($a == 'Core')
            return -1;
        if ($b == 'Core')
            return 1;
        return strcmp($a, $b);
    }

    protected function _sortCategoryItems($a, $b) {

        if (!empty($a['special']))
            return -1;
        if (!empty($b['special']))
            return 1;
        return strcmp($a['title'], $b['title']);
    }

    protected function _reorderContentStructure($a, $b) {

        $sample = array('left', 'middle', 'right');
        $av = $a['name'];
        $bv = $b['name'];
        $ai = array_search($av, $sample);
        $bi = array_search($bv, $sample);
        if ($ai === false && $bi === false)
            return 0;
        if ($ai === false)
            return -1;
        if ($bi === false)
            return 1;
        $r = ( $ai == $bi ? 0 : ($ai < $bi ? -1 : 1) );
        return $r;
    }

    public function setUserDrivenLayoutAction() {
        $this->view->group_id = $this->_getParam('group_id', null);
        $this->_helper->layout->setLayout('default-simple');
    }

    public function saveUserDrivenLayoutAction() {
        $group_id = $this->_getParam('group_id', null);
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        //EXTRACTING CURRENT ADMIN SETTINGS FOR THIS VIEW GROUP.
        $groupAdminTable = Engine_Api::_()->getDbtable('pages', 'core');
        $groupAdminTableName = $groupAdminTable->info('name');
        $selectGroupAdmin = $groupAdminTable->select()
                ->setIntegrityCheck(false)
                ->from($groupAdminTableName)
                ->where('name = ?', 'sitegroup_index_view');
        $groupAdminresult = $groupAdminTable->fetchRow($selectGroupAdmin);
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        //NOW INSERTING THE ROW IN GROUP TABLE
        $groupTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');

        //CREATE NEW GROUP
        $groupObject = $groupTable->createRow();
        $groupObject->displayname = $sitegroup->title;
        $groupObject->title = $sitegroup->title;
        $groupObject->description = $sitegroup->body;
        $groupObject->name = "sitegroup_index_view";
        $groupObject->url = $groupAdminresult->url;
        $groupObject->custom = $groupAdminresult->custom;
        $groupObject->fragment = $groupAdminresult->fragment;
        $groupObject->keywords = $groupAdminresult->keywords;
        $groupObject->layout = $groupAdminresult->layout;
        $groupObject->view_count = $groupAdminresult->view_count;
        $groupObject->user_id = $viewer_id;
        $groupObject->group_id = $group_id;
        $contentGroupId = $groupObject->save();

        //NOW FETCHING GROUP CONTENT DEFAULT SETTING INFORMATION FROM CORE CONTENT TABLE FOR THIS GROUP.
        //NOW INSERTING DEFAULT GROUP CONTENT SETTINGS IN OUR CONTENT TABLE
        $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
        $sitegroup_layout_cover_photo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layout.cover.photo', 1);
        if (!$layout) {
            Engine_Api::_()->getDbtable('content', 'sitegroup')->setContentDefault($contentGroupId, $sitegroup_layout_cover_photo);
        } else {
            Engine_Api::_()->getApi('layoutcore', 'sitegroup')->setContentDefaultLayout($contentGroupId, $sitegroup_layout_cover_photo);
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefreshTime' => '60',
            'parentRefresh' => 'true',
            'format' => 'smoothbox',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Layout for this group has been changed successfully.'))
        ));
    }

}

?>