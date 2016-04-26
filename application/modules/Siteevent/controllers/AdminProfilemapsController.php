<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminProfilemapsControllerController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_AdminProfilemapsController extends Core_Controller_Action_Admin {

    //ACTION FOR MANAGING THE PROFILE-CATEGORY MAPPING
    public function manageAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_profilemaps');




        //GET FIELD OPTION TABLE NAME
        $tableFieldOptions = Engine_Api::_()->getDbtable('options', 'siteevent');

        //GET TOTAL PROFILES
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('siteevent_event');
        $this->view->totalProfileTypes = 1;
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();
            $this->view->totalProfileTypes = Count($options);
        }

        //GET REVIEW PARAMETER TABLE NAME
        $tableReviewCats = Engine_Api::_()->getDbtable('ratingparams', 'siteevent');
        $tableReviewCatsName = $tableReviewCats->info('name');

        $tableCategory = Engine_Api::_()->getDbtable('categories', 'siteevent');
        $categories = array();
        $category_info = $tableCategory->getCategories(array('category_id', 'category_name', 'cat_order', 'profile_type'), null, 0, 0, 1);
        foreach ($category_info as $value) {

            $sub_cat_array = array();
            $category_info2 = $tableCategory->getSubCategories($value->category_id, array('category_id',  'category_name','profile_type', 'cat_order'));
            foreach ($category_info2 as $subresults) {

                $treesubarray = array();
                $subcategory_info2 = $tableCategory->getSubCategories($subresults->category_id, array('category_id',  'category_name','profile_type', 'cat_order'));
                $treesubarrays[$subresults->category_id] = array();
                foreach ($subcategory_info2 as $subvalues) {

                    $tree_profile_type_label = '---';
                    if (!empty($subvalues->profile_type)) {
                        $tree_profile_type_label = $tableFieldOptions->getProfileTypeLabel($subvalues->profile_type);
                    }

                    $treesubarrays[$subresults->category_id][] = $treesubarray = array(
                        'tree_sub_cat_id' => $subvalues->category_id,
                        'tree_sub_cat_name' => $subvalues->category_name,
                        'order' => $subvalues->cat_order,
                        'tree_profile_type_id' => $subvalues->profile_type,
                        'tree_profile_type_label' => $tree_profile_type_label
                    );
                }

                $subcat_profile_type_label = '---';
                if (!empty($subresults->profile_type)) {
                    $subcat_profile_type_label = $tableFieldOptions->getProfileTypeLabel($subresults->profile_type);
                }

                $tmp_array = array('sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                    'order' => $subresults->cat_order,
                    'subcat_profile_type_id' => $subresults->profile_type,
                    'subcat_profile_type_label' => $subcat_profile_type_label
                );
                $sub_cat_array[] = $tmp_array;
            }

            $cat_profile_type_label = '---';
            if (!empty($value->profile_type)) {
                $cat_profile_type_label = $tableFieldOptions->getProfileTypeLabel($value->profile_type);
            }

            $categories[] = $category_array = array(
                'category_id' => $value->category_id,
                'category_name' => $value->category_name,
                'order' => $value->cat_order,
                'sub_categories' => $sub_cat_array,
                'cat_profile_type_id' => $value->profile_type,
                'cat_profile_type_label' => $cat_profile_type_label,
            );
        }

        include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license2.php';
    }

    //ACTION FOR MAP THE PROFILE WITH CATEGORY
    public function mapAction() {

        //DEFAULT LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET CATEGORY ID
        $this->view->category_id = $category_id = $this->_getParam('category_id');

        //GET CHIELD MAPPING
        $chieldMapping = Engine_Api::_()->getDbTable('categories', 'siteevent')->getChildMapping($category_id, 'profile_type');
        $countChieldMapping = Count($chieldMapping);

        //GENERATE THE FORM
        $this->view->form = $form = new Siteevent_Form_Admin_Profilemaps_Map(array('countChieldMapping' => $countChieldMapping));

        //GET MAPPING ITEM
        $category = Engine_Api::_()->getItem('siteevent_category', $category_id);

        //POST DATA
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $tempCatFlag = false;
            //GET DATA
            $values = $form->getValues();

            //GET EVENT TABLE
            $eventTable = Engine_Api::_()->getDbTable('events', 'siteevent');

            //BEGIN TRANSCATION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                $category->profile_type = $values['profile_type'];
                $category->save();
                $tempCatFlag = true;

                if (empty($tempCatFlag)) {
                    return;
                }
                //IF YES BUTTON IS CLICKED THEN CHANGE MAPPING OF ALL EVENTS
                if (isset($_POST['yes_button'])) {

                    if (!empty($countChieldMapping)) {

                        $chieldMappingArray = array();
                        foreach ($chieldMapping as $chieldMappingItem) {
                            $chieldMappingArray[] = $chieldMappingItem->category_id;
                        }

                        //HAVE TO UPDATE PREVIOUS MAPPED EVENTS
                        $select = $eventTable->select()
                                ->from($eventTable->info('name'), array('event_id', 'profile_type', 'category_id', 'subcategory_id', 'subsubcategory_id'))
                                ->where("category_id = $category_id OR category_id IN (?) OR subcategory_id IN (?) OR subsubcategory_id IN (?)", $chieldMappingArray)
                                ->where("profile_type != ?", $values['profile_type']);
                        $eventsIds = $eventTable->fetchAll($select);

                        $chieldMappingArrayStr = "(" . join(",", $chieldMappingArray) . ")";
                        Zend_Db_Table_Abstract::getDefaultAdapter()->query("UPDATE `engine4_siteevent_categories` SET `profile_type` = 0 WHERE category_id IN $chieldMappingArrayStr");
                    } else {
                        //SELECT EVENTS WHICH HAVE THIS CATEGORY AND THIS PROFILE TYPE
                        $eventsIds = $eventTable->getMappedSiteevent($category_id);
                    }

                    if (!empty($eventsIds)) {
                        foreach ($eventsIds as $event) {
                            $event_id = $event['event_id'];

                            //GET FIELD VALUE TABLE
                            $fieldvalueTable = Engine_Api::_()->fields()->getTable('siteevent_event', 'values');

                            //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                            Engine_Api::_()->fields()->getTable('siteevent_event', 'values')->delete(array('item_id = ?' => $event_id));
                            Engine_Api::_()->fields()->getTable('siteevent_event', 'search')->delete(array('item_id = ?' => $event_id));

                            //PUT NEW PROFILE TYPE
                            $fieldvalueTable->insert(array(
                                'item_id' => $event_id,
                                'field_id' => Engine_Api::_()->getDbTable('metas', 'siteevent')->defaultProfileId(),
                                'index' => 0,
                                'value' => $category->profile_type,
                            ));

                            $eventTable->update(array('profile_type' => $category->profile_type), array('event_id = ?' => $event_id));
                        }
                    }
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping has been done successfully.'))
            ));
        }

        $this->renderScript('admin-profilemaps/map.tpl');
    }

    public function editAction() {

        //DEFAULT LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET CATEGORY ID
        $this->view->category_id = $category_id = $this->_getParam('category_id');

        //GET PROFILE TYPE
        $old_profile_type_id = $this->_getParam('profile_type');

        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('siteevent_event');
        $this->view->totalProfileTypes = 1;
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();
            $this->view->totalProfileTypes = Count($options);
        }

        //GENERATE THE FORM
        $this->view->form = $form = new Siteevent_Form_Admin_Profilemaps_Edit();

        //POST DATA
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET DATA
            $values = $form->getValues();
            $new_profile_type_id = $values['profile_type'];

            if ($old_profile_type_id != $new_profile_type_id) {

                //BEGIN TRANSCATION
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();

                try {

                    //GET MAPPING ITEM
                    $category = Engine_Api::_()->getItem('siteevent_category', $category_id);

                    //GET EVENT TABLE
                    $siteeventTable = Engine_Api::_()->getDbTable('events', 'siteevent');

                    //FOR CATEGORY
                    if ($category->cat_dependency == 0 && $category->subcat_dependency == 0) {
                        $select = $siteeventTable->select()
                                ->from($siteeventTable->info('name'), array('event_id'))
                                ->where('category_id = ?', $category->category_id)
                                ->where('subcategory_id = ?', 0)
                        ;
                    }
                    //FOR SUBCATEGORY
                    elseif ($category->cat_dependency != 0 && $category->subcat_dependency == 0) {
                        $select = $siteeventTable->select()
                                ->from($siteeventTable->info('name'), array('event_id'))
                                ->where('subcategory_id = ?', $category->category_id)
                                ->where('subsubcategory_id = ?', 0)
                        ;
                    } elseif ($category->cat_dependency != 0 && $category->subcat_dependency != 0) {
                        $select = $siteeventTable->select()
                                ->from($siteeventTable->info('name'), array('event_id'))
                                ->where('subsubcategory_id = ?', $category->category_id)
                        ;
                    }

                    $eventIds = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

                    if (!empty($eventIds)) {

                        $fieldMapsTable = Engine_Api::_()->getDbTable('maps', 'siteevent');
                        $fieldValuesTable = Engine_Api::_()->fields()->getTable('siteevent_event', 'values');

                        $old_meta_ids = $fieldMapsTable->getMappingIds($old_profile_type_id);
                        $new_meta_ids = $fieldMapsTable->getMappingIds($new_profile_type_id);
                        $array_diff = array_diff($old_meta_ids, $new_meta_ids);

                        $fieldValuesTable->update(array('value' => $new_profile_type_id), array('item_id IN (?)' => "'" . join("',", $eventIds) . "'", 'field_id = ?' => 1, 'value = ?' => $old_profile_type_id));

                        //DELETE UN-COMMON VALUES FROM CUSTOM TABLES
                        if (!empty($array_diff)) {
                            $fieldValuesTable->delete(array('item_id IN (?)' => "'" . join("',", $eventIds) . "'", 'field_id IN (?)' => "'" . join("',", $array_diff) . "'"));
                        }

                        //UPDATE PROFILE-TYPE VALUE IN EVENT TABLE
                        $siteeventTable->update(array('profile_type' => $new_profile_type_id), array('event_id IN (?)' => (array) $eventIds));
                    }

                    $category->profile_type = $new_profile_type_id;
                    $category->save();

                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping has been edited successfully.'))
            ));
        }
    }

    //ACTION FOR DELETE MAPPING 
    public function removeAction() {

        //DEFAULT LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET MAPPING ID
        $this->view->category_id = $category_id = $this->_getParam('category_id');

        //GET CHILD CATEGORIES
        $categoryTable = Engine_Api::_()->getDbTable('categories', 'siteevent');
        $getChilds = $categoryTable->getChilds($category_id);
        $this->view->countChilds = Count($getChilds);

        //GET MAPPING ITEM
        $this->view->category = $category = Engine_Api::_()->getItem('siteevent_category', $category_id);

        //POST DATA
        if ($this->getRequest()->isPost()) {

            //BEGIN TRANSCATION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                //GET EVENT TABLE
                $siteeventTable = Engine_Api::_()->getDbTable('events', 'siteevent');

                if (!isset($_POST['import_profile'])) {

                    //SELECT EVENTS WHICH HAVE THIS CATEGORY
                    $siteevents = $siteeventTable->getMappedSiteevent($category_id);
                } else {

                    foreach ($getChilds as $getChild) {
                        $child = Engine_Api::_()->getItem('siteevent_category', $getChild->category_id);
                        $child->profile_type = $category->profile_type;
                        $child->save();
                    }

                    //FOR CATEGORY
                    if ($category->cat_dependency == 0 && $category->subcat_dependency == 0) {
                        $select = $siteeventTable->select()
                                ->from($siteeventTable->info('name'), array('event_id'))
                                ->where('category_id = ?', $category->category_id)
                                ->where('subcategory_id = ?', 0)
                        ;
                    }
                    //FOR SUBCATEGORY
                    elseif ($category->cat_dependency != 0 && $category->subcat_dependency == 0) {
                        $select = $siteeventTable->select()
                                ->from($siteeventTable->info('name'), array('event_id'))
                                ->where('subcategory_id = ?', $category->category_id)
                                ->where('subsubcategory_id = ?', 0)
                        ;
                    }

                    $siteevents = $siteeventTable->fetchAll($select);

                    if (!empty($siteevents)) {
                        $siteevents = $siteevents->toArray();
                    }
                }

                foreach ($siteevents as $siteevent) {

                    //GET EVENT ID
                    $event_id = $siteevent['event_id'];

                    //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                    Engine_Api::_()->fields()->getTable('siteevent_event', 'values')->delete(array('item_id = ?' => $event_id));
                    Engine_Api::_()->fields()->getTable('siteevent_event', 'search')->delete(array('item_id = ?' => $event_id));

                    //UPDATE THE PROFILE TYPE OF ALREADY CREATED EVENTS
                    $siteeventTable->update(array('profile_type' => 0), array('event_id = ?' => $event_id));
                }

                //DELETE MAPPING
                $category->profile_type = 0;
                $category->save();


                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping has been deleted successfully!'))
            ));
        }
        $this->renderScript('admin-profilemaps/remove.tpl');
    }

}