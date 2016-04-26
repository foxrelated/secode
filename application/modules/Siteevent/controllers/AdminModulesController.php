<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminModulesController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_AdminModulesController extends Core_Controller_Action_Admin {

    protected $_enabledModuleNames;

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_integrated');
        include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license2.php';
    }

    public function addAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_integrated');

        $this->view->form = $form = new Siteevent_Form_Admin_Module_Add();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $integratedTable = Engine_Api::_()->getDbtable('modules', 'siteevent');
                $resourceTypeTable = Engine_Api::_()->getItemTable($values['item_type']);
                $primaryId = current($resourceTypeTable->info("primary"));
                if (!empty($primaryId))
                    $values['item_id'] = $primaryId;
                $row = $integratedTable->createRow();
                $row->setFromArray($values);
                $row->save();

                foreach ($values as $key => $value) {
                    Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_redirect('admin/siteevent/modules');
        }
    }

    public function editAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_integrated');

        $id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);
        $integratedTable = Engine_Api::_()->getDbTable('modules', 'siteevent');
        $integratedTableResult = $integratedTable->fetchRow(array('module_id = ?' => $id));
        $integratedRowValues = $integratedTableResult->toArray();
        $item_module = Zend_Controller_Front::getInstance()->getRequest()->getParam('item_module', null);
        $this->view->form = $form = new Siteevent_Form_Admin_Module_Edit();
        if (isset($integratedRowValues['item_membertype']) && !empty($integratedRowValues['item_membertype']))
            $integratedRowValues['item_membertype'] = unserialize($integratedRowValues['item_membertype']);
        $form->populate($integratedRowValues);

        if ($item_module == 'sitereview') {
            $form->item_type->setValue('sitereview_listing');
        }

        if ($this->getRequest()->isPost() && !$form->isValid($this->getRequest()->getPost())) {
            if ($item_module == 'sitereview') {
                $form->item_module->setValue('sitereview');
                $form->item_type->setValue('sitereview_listing');
            } else {
                $form->item_module->setValue($item_module);
                $form->item_type->setValue($integratedRowValues->item_type);
            }
            $form->item_module->setAttrib('disable', true);
            $form->item_type->setAttrib('disable', true);
            return;
        }

        $form->item_module->setAttrib('disable', true);
        $form->item_type->setAttrib('disable', true);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $item_type = $integratedTableResult->item_type;
            $item_module = $integratedTableResult->item_module;
            $item_id = $integratedTableResult->item_id;
            $type = $item_module . 'event_event';
            unset($values['item_module']);
            unset($values['item_type']);
            if (isset($values['item_membertype']) && !empty($values['item_membertype']))
                $values['item_membertype'] = serialize($values['item_membertype']);


            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $integratedTableResult->setFromArray($values);
                $integratedTableResult->save();

                foreach ($values as $key => $value) {
                    Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_redirect('admin/siteevent/modules');
        }
    }

    public function deleteAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->id = $id = $this->_getParam('id');
        $integratedTable = Engine_Api::_()->getDbtable('modules', 'siteevent');
        $integratedTableSelect = $integratedTable->fetchRow(array('module_id = ?' => $id));
        $this->view->module = $integratedTableSelect->item_module;
        if ($this->getRequest()->isPost()) {
            $integratedTableSelect->delete();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
    }

    public function enabledDisabledAction() {
        $id = $this->_getParam('id');
        $coreModuleTable = Engine_Api::_()->getDbtable('modules', 'core');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $table = Engine_Api::_()->getDbtable('modules', 'siteevent');
        $select = $table->select()->where('module_id =?', $id);
        $content = $table->fetchRow($select);
        $enabled = $content->enabled;
        $integrated = $content->integrated;

        if ($content->enabled == 0 && $content->integrated == 0) {
            $item_type = $content->item_type;
            $item_module = $content->item_module;
            $item_id = $content->item_id;
            $event_id = false;
            if ($item_module == 'sitepage' || $item_module == 'sitebusiness' || $item_module == 'sitegroup' || $item_module == 'sitestore') {
                $type = $item_module . 'event_event';
                $explodedArray = explode('_', $item_id);
                $itemShortType = 'getWidgetized' . ucfirst($explodedArray[0]);
                if (Engine_Api::_()->$item_module()->$itemShortType()) {
                    $page_id = Engine_Api::_()->$item_module()->$itemShortType()->page_id;
                }
                $db = Engine_Db_Table::getDefaultAdapter();

                Engine_Api::_()->getDbtable('admincontent', $item_module)->setAdminDefaultInfo('siteevent.contenttype-events', $page_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');

                if (Engine_Api::_()->hasModuleBootstrap('sitemobile')) {
                    $itemMobileShortType = 'getMobileWidgetized' . ucfirst($explodedArray[0]);

                    $mobilepage_id = Engine_Api::_()->$item_module()->$itemMobileShortType()->page_id;
                    if ($mobilepage_id) {
                        Engine_Api::_()->getDbtable('mobileadmincontent', $item_module)->setAdminDefaultInfo('siteevent.contenttype-events', $mobilepage_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');

                        Engine_Api::_()->getApi('mobilelayoutcore', $item_module)->setContentDefaultInfo('siteevent.contenttype-events', $mobilepage_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
                    }
                }

                Engine_Api::_()->getApi('layoutcore', $item_module)->setContentDefaultInfo('siteevent.contenttype-events', $page_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');

                if ($item_module == 'sitepage') {

                    //GET SITEPAGE CONTENT TABLE
                    $sitepagecontentTable = Engine_Api::_()->getDbtable('content', 'sitepage');
                    //GET SITEPAGE CONTENT PAGES TABLE
                    $sitepagepageTable = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
                    $selectsitepagePage = $sitepagepageTable->select()
                            ->from($sitepagepageTable->info('name'), array('contentpage_id'))
                            ->where('name =?', 'sitepage_index_view');
                    $contentpages_id = $selectsitepagePage->query()->fetchAll();
                    foreach ($contentpages_id as $key => $value) {
                        $sitepagecontentTable->setDefaultInfo('siteevent.contenttype-events', $value['contentpage_id'], 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
                    }

                    if (Engine_Api::_()->hasModuleBootstrap('sitemobile') && Engine_Api::_()->$item_module()->getMobileWidgetizedPage()) {

                        //GET SITEPAGE CONTENT TABLE
                        $sitepagemobilecontentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitepage');
                        //GET SITEPAGE CONTENT PAGES TABLE
                        $sitepagemobilecontentpagesTable = Engine_Api::_()->getDbtable('mobileContentpages', 'sitepage');
                        $selectsitepagemobilePage = $sitepagemobilecontentpagesTable->select()
                                ->from($sitepagemobilecontentpagesTable->info('name'), array('mobilecontentpage_id'))
                                ->where('name =?', 'sitepage_index_view');
                        $contentmobilepages_id = $selectsitepagemobilePage->query()->fetchAll();
                        foreach ($contentmobilepages_id as $key => $value) {
                            $sitepagemobilecontentTable->setDefaultInfo('siteevent.contenttype-events', $value['mobilecontentpage_id'], 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
                        }
                    }

                    $db->query("INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( 'siteevent.event.leader.owner.sitepage.page', '1');");
                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                        Engine_Api::_()->sitepage()->oninstallPackageEnableSubMOdules('sitepageevent');
                    }
                    $sitepageeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');

                    if ($sitepageeventEnabled) {
                        $sitepageeventTable = Engine_Api::_()->getDbTable('events', 'sitepageevent');
                        $sitepageeventTableName = $sitepageeventTable->info('name');
                        $event_id = $sitepageeventTable->select()
                                ->from($sitepageeventTableName, 'event_id')
                                ->query()
                                ->fetchColumn();
                        if (!$event_id) {
                            $coreModuleTable->update(array('enabled' => 0), array('name = ?' => 'sitepageevent'));
                            Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitepageevent.profile-sitepageevents'));
                            Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitepageevent.profile-events'));
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->delete(array('name =?' => 'sitepageevent.profile-sitepageevents'));
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->delete(array('name =?' => 'sitepageevent.profile-events'));
                            Engine_Api::_()->getDbtable('content', 'sitepage')->delete(array('name =?' => 'sitepageevent.profile-sitepageevents'));
                            Engine_Api::_()->getDbtable('content', 'sitepage')->delete(array('name =?' => 'sitepageevent.profile-events'));
                        }
                    }
                } elseif ($item_module == 'sitebusiness') {
                    //GET SITEBUSINESS CONTENT TABLE
                    $sitebusinesscontentTable = Engine_Api::_()->getDbtable('content', 'sitebusiness');
                    //GET SITEBUSINESS CONTENT PAGES TABLE
                    $sitebusinessbusinessTable = Engine_Api::_()->getDbtable('contentbusinesses', 'sitebusiness');
                    $selectsitebusinessBusiness = $sitebusinessbusinessTable->select()
                            ->from($sitebusinessbusinessTable->info('name'), array('contentbusiness_id'))
                            ->where('name =?', 'sitebusiness_index_view');
                    $contentbusinesses_id = $selectsitebusinessBusiness->query()->fetchAll();
                    foreach ($contentbusinesses_id as $key => $value) {
                        $sitebusinesscontentTable->setDefaultInfo('siteevent.contenttype-events', $value['contentbusiness_id'], 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
                    }

                    if (Engine_Api::_()->hasModuleBootstrap('sitemobile') && Engine_Api::_()->$item_module()->getMobileWidgetizedBusiness()) {
                        //GET SITEBUSINESS CONTENT TABLE
                        $sitebusinesscontentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitebusiness');
                        //GET SITEBUSINESS CONTENT PAGES TABLE
                        $sitebusinessbusinessTable = Engine_Api::_()->getDbtable('mobileContentbusinesses', 'sitebusiness');
                        $selectsitebusinessBusiness = $sitebusinessbusinessTable->select()
                                ->from($sitebusinessbusinessTable->info('name'), array('mobilecontentbusiness_id'))
                                ->where('name =?', 'sitebusiness_index_view');
                        $contentbusinesses_id = $selectsitebusinessBusiness->query()->fetchAll();
                        foreach ($contentbusinesses_id as $key => $value) {
                            $sitebusinesscontentTable->setDefaultInfo('siteevent.contenttype-events', $value['mobilecontentbusiness_id'], 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
                        }
                    }


                    $db->query("INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( 'siteevent.event.leader.owner.sitebusiness.business', '1');");
                    if (Engine_Api::_()->sitebusiness()->hasPackageEnable()) {
                        Engine_Api::_()->sitebusiness()->oninstallPackageEnableSubMOdules('sitebusinessevent');
                    }
                    $sitebusinesseventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessevent');

                    if ($sitebusinesseventEnabled) {
                        $sitebusinesseventTable = Engine_Api::_()->getDbTable('events', 'sitebusinessevent');
                        $sitebusinesseventTableName = $sitebusinesseventTable->info('name');
                        $event_id = $sitebusinesseventTable->select()
                                ->from($sitebusinesseventTableName, 'event_id')
                                ->query()
                                ->fetchColumn();
                        if (!$event_id) {
                            $coreModuleTable->update(array('enabled' => 0), array('name = ?' => 'sitebusinessevent'));
                            Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitebusinessevent.profile-sitebusinessevents'));
                            Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitebusinessevent.profile-events'));
                            Engine_Api::_()->getDbtable('admincontent', 'sitebusiness')->delete(array('name =?' => 'sitebusinessevent.profile-sitebusinessevents'));
                            Engine_Api::_()->getDbtable('admincontent', 'sitebusiness')->delete(array('name =?' => 'sitebusinessevent.profile-events'));
                            Engine_Api::_()->getDbtable('content', 'sitebusiness')->delete(array('name =?' => 'sitebusinessevent.profile-sitebusinessevents'));
                            Engine_Api::_()->getDbtable('content', 'sitebusiness')->delete(array('name =?' => 'sitebusinessevent.profile-events'));
                        }
                    }
                } elseif ($item_module == 'sitegroup') {
                    //GET SITEGROUP CONTENT TABLE
                    $sitegroupcontentTable = Engine_Api::_()->getDbtable('content', 'sitegroup');
                    //GET SITEGROUP CONTENT PAGES TABLE
                    $sitegroupgroupTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
                    $selectsitegroupGroup = $sitegroupgroupTable->select()
                            ->from($sitegroupgroupTable->info('name'), array('contentgroup_id'))
                            ->where('name =?', 'sitegroup_index_view');
                    $contentgroups_id = $selectsitegroupGroup->query()->fetchAll();
                    foreach ($contentgroups_id as $key => $value) {
                        $sitegroupcontentTable->setDefaultInfo('siteevent.contenttype-events', $value['contentgroup_id'], 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
                    }
                    $db->query("INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( 'siteevent.event.leader.owner.sitegroup.group', '0');");
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        Engine_Api::_()->sitegroup()->oninstallPackageEnableSubMOdules('sitegroupevent');
                    }

                    $sitegroupeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');

                    if ($sitegroupeventEnabled) {
                        $sitegroupeventTable = Engine_Api::_()->getDbTable('events', 'sitegroupevent');
                        $sitegroupeventTableName = $sitegroupeventTable->info('name');
                        $event_id = $sitegroupeventTable->select()
                                ->from($sitegroupeventTableName, 'event_id')
                                ->query()
                                ->fetchColumn();
                        if (!$event_id) {
                            $coreModuleTable->update(array('enabled' => 0), array('name = ?' => 'sitegroupevent'));
                            Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitegroupevent.profile-sitegroupevents'));
                            Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitegroupevent.profile-events'));
                            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->delete(array('name =?' => 'sitegroupevent.profile-sitegroupevents'));
                            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->delete(array('name =?' => 'sitegroupevent.profile-events'));
                            Engine_Api::_()->getDbtable('content', 'sitegroup')->delete(array('name =?' => 'sitegroupevent.profile-sitegroupevents'));
                            Engine_Api::_()->getDbtable('content', 'sitegroup')->delete(array('name =?' => 'sitegroupevent.profile-events'));
                        }
                    }

                    if (Engine_Api::_()->hasModuleBootstrap('sitemobile') && Engine_Api::_()->$item_module()->getMobileWidgetizedGroup()) {
                        //GET SITEGROUP CONTENT TABLE
                        $sitegroupcontentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitegroup');
                        //GET SITEGROUP CONTENT PAGES TABLE
                        $sitegroupgroupTable = Engine_Api::_()->getDbtable('mobileContentgroups', 'sitegroup');
                        $selectsitegroupGroup = $sitegroupgroupTable->select()
                                ->from($sitegroupgroupTable->info('name'), array('mobilecontentgroup_id'))
                                ->where('name =?', 'sitegroup_index_view');
                        $contentgroups_id = $selectsitegroupGroup->query()->fetchAll();
                        foreach ($contentgroups_id as $key => $value) {
                            $sitegroupcontentTable->setDefaultInfo('siteevent.contenttype-events', $value['mobilecontentgroup_id'], 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
                        }
                    }
                } elseif ($item_module == 'sitestore') {
                    //GET SITESTORE CONTENT TABLE
                    $sitestorecontentTable = Engine_Api::_()->getDbtable('content', 'sitestore');
                    //GET SITESTORE CONTENT STORES TABLE
                    $sitestorestoreTable = Engine_Api::_()->getDbtable('contentstores', 'sitestore');
                    $selectsitestoreStore = $sitestorestoreTable->select()
                            ->from($sitestorestoreTable->info('name'), array('contentstore_id'))
                            ->where('name =?', 'sitestore_index_view');
                    $contentstores_id = $selectsitestoreStore->query()->fetchAll();
                    foreach ($contentstores_id as $key => $value) {
                        $sitestorecontentTable->setDefaultInfo('siteevent.contenttype-events', $value['contentstore_id'], 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
                    }
                    $db->query("INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( 'siteevent.event.leader.owner.sitestore.store', '1');");
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        Engine_Api::_()->sitestore()->oninstallPackageEnableSubMOdules('sitestoreevent');
                    }

                    $sitestoreeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent');

                    if ($sitestoreeventEnabled) {
                        $sitestoreeventTable = Engine_Api::_()->getDbTable('events', 'sitestoreevent');
                        $sitestoreeventTableName = $sitestoreeventTable->info('name');
                        $event_id = $sitestoreeventTable->select()
                                ->from($sitestoreeventTableName, 'event_id')
                                ->query()
                                ->fetchColumn();
                        if (!$event_id) {
                            $coreModuleTable->update(array('enabled' => 0), array('name = ?' => 'sitestoreevent'));
                            Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitestoreevent.profile-sitestoreevents'));
                            Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitestoreevent.profile-events'));
                            Engine_Api::_()->getDbtable('admincontent', 'sitestore')->delete(array('name =?' => 'sitestoreevent.profile-sitestoreevents'));
                            Engine_Api::_()->getDbtable('admincontent', 'sitestore')->delete(array('name =?' => 'sitestoreevent.profile-events'));
                            Engine_Api::_()->getDbtable('content', 'sitestore')->delete(array('name =?' => 'sitestoreevent.profile-sitestoreevents'));
                            Engine_Api::_()->getDbtable('content', 'sitestore')->delete(array('name =?' => 'sitestoreevent.profile-events'));
                        }
                    }

                    if (Engine_Api::_()->hasModuleBootstrap('sitemobile') && Engine_Api::_()->$item_module()->getMobileWidgetizedStore()) {
                        //GET SITESTORE CONTENT TABLE
                        $sitestorecontentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitestore');
                        //GET SITESTORE CONTENT PAGES TABLE
                        $sitestorestoreTable = Engine_Api::_()->getDbtable('mobileContentstores', 'sitestore');
                        $selectsitestoreStore = $sitestorestoreTable->select()
                                ->from($sitestorestoreTable->info('name'), array('mobilecontentstore_id'))
                                ->where('name =?', 'sitestore_index_view');
                        $contentstores_id = $selectsitestoreStore->query()->fetchAll();
                        foreach ($contentstores_id as $key => $value) {
                            $sitestorecontentTable->setDefaultInfo('siteevent.contenttype-events', $value['mobilecontentstore_id'], 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
                        }
                    }
                }

                $db->query('
						INSERT IGNORE INTO `engine4_authorization_permissions` 
						SELECT level_id as `level_id`, 
							"' . $item_type . '" as `type`, 
							"auth_secreate" as `name`, 
							5 as `value`, 
							\'["registered","owner_network","owner_member_member","owner_member","owner","member","like_member"]\' as `params` 
						FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");
					');
                $db->query('
						INSERT IGNORE INTO `engine4_authorization_permissions` 
						SELECT level_id as `level_id`, 
							"' . $item_type . '" as `type`, 
							"secreate" as `name`, 
							1 as `value`, 
							NULL as `params`
						FROM `engine4_authorization_levels`;
					');

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'view' as `name`,
						2 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'view' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('user');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'comment' as `name`,
						2 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'comment' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('user');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'invite' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'invite' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('user');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES ('5', '$type', 'view', '1', NULL);");

                $feedType = $item_module . 'event_new';
                $adminfeedType = $item_module . 'event_admin_new';
                $db->query("UPDATE `engine4_activity_actiontypes` SET  `module` =  'siteevent' WHERE  `engine4_activity_actiontypes`.`type` =  '$feedType' LIMIT 1;");
                $db->query("UPDATE `engine4_activity_actiontypes` SET  `module` =  'siteevent' WHERE  `engine4_activity_actiontypes`.`type` =  '$adminfeedType' LIMIT 1;");
                $body = '{item:$object} created a new event:';
                $db->query("INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ('$adminfeedType', 'siteevent', '$body', '1', '6', '2', '1', '1', '1');");

                $body = '{item:$subject} created a new event in the ' . $explodedArray[0] . '{item:$object}:';
                $db->query("INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ('$feedType', 'siteevent', '$body', '1', '3', '2', '1', '1', '1');");
            } else if ($item_module == 'sitereview') {
                $type = $item_module . 'event_event';
                $explodedArray = explode('_', $item_id);
                $explodedItemTypeArray = explode('_', $item_type);
                $listingTypeId = $explodedItemTypeArray[2];

                $db = Engine_Db_Table::getDefaultAdapter();

                $value = "auth_event_listtype_$listingTypeId";

                $db->query('
						INSERT IGNORE INTO `engine4_authorization_permissions` 
						SELECT level_id as `level_id`, 
							"sitereview_listing" as `type`, 
							"' . $value . '" as `name`, 
							5 as `value`, 
							\'["registered","owner_network","owner_member_member","owner_member","owner"]\' as `params` 
						FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");
					');

                $value = "event_listtype_$listingTypeId";

                $db->query("
						INSERT IGNORE INTO `engine4_authorization_permissions` 
						SELECT 
									level_id as `level_id`, 
									'sitereview_listing' as `type`, 
									'$value' as `name`, 
									1 as `value`, 
									NULL as `params` 
						FROM `engine4_authorization_levels` WHERE `type` IN('moderator','admin','user');
					");
                $listingType = Engine_Api::_()->getItem('sitereview_listingtype', $listingTypeId);
                $titleSinLc = strtolower($listingType->title_singular);
                $body = '{item:$subject} added a new event in the ' . $titleSinLc . ' listing {item:$object}:';
                $actionType = 'sitereview_event_new_listtype_' . $listingTypeId;
                $db->query("INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ('$actionType', 'siteevent','$body', '1', '3', '2', '1', '1', '1');");
                $db->query("INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( 'siteevent.event.leader.owner.sitereview.listing. $listingTypeId', '0');");
                //MEMBER PROFILE PAGE WIDGETS
                $page_id = $db->select()
                        ->from('engine4_core_pages', array('page_id'))
                        ->where('name =?', 'sitereview_index_view_listtype_' . $listingTypeId)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();

                if (!empty($page_id)) {

                    $tab_id = $db->select()
                            ->from('engine4_core_content', array('content_id'))
                            ->where('page_id =?', $page_id)
                            ->where('type = ?', 'widget')
                            ->where('name = ?', 'core.container-tabs')
                            ->limit(1)
                            ->query()
                            ->fetchColumn();

                    if (!empty($tab_id)) {

                        $content_id = $db->select()
                                ->from('engine4_core_content', array('content_id'))
                                ->where('page_id =?', $page_id)
                                ->where('type = ?', 'widget')
                                ->where('name = ?', 'siteevent.contenttype-events')
                                ->limit(1)
                                ->query()
                                ->fetchColumn();

                        if (empty($content_id)) {
                            $db->insert('engine4_core_content', array(
                                'page_id' => $page_id,
                                'type' => 'widget',
                                'name' => 'siteevent.contenttype-events',
                                'parent_content_id' => $tab_id,
                                'order' => 999,
                                'params' => '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","memberCount","reviewCount","ratingStar"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}',
                            ));
                        }
                    }
                }
            }
        }

        try {
            $content->enabled = !$content->enabled;
            $content->integrated = 1;
            $content->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        if ($enabled == 0 && $integrated == 0 && $event_id) {
            $this->_redirect('admin/siteevent/importevent');
        }

        $this->_redirect('admin/siteevent/modules');
    }

}