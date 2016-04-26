<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_CategoriesMiddleSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->show3rdlevelCategory = $show3rdlevelCategory = $this->_getParam('show3rdlevelCategory', 1);
        $this->view->show2ndlevelCategory = $show2ndlevelCategory = $this->_getParam('show2ndlevelCategory', 1);
        $showEventType = $this->view->showEventType = $this->_getParam('showEventType', 'upcoming');
        $showCount = $this->_getParam('showCount', 0);
        $showAllCategories = $this->_getParam('showAllCategories', 0);

        $siteeventCategoriesMiddEvents = Zend_Registry::isRegistered('siteeventCategoriesMiddEvents') ? Zend_Registry::get('siteeventCategoriesMiddEvents') : null;
        $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'siteevent');

        $this->view->categories = $categories = array();

        //GET EVENT TABLE
        $tableSiteevent = Engine_Api::_()->getDbtable('events', 'siteevent');

        if ($showAllCategories) {

            $category_info = $tableCategory->getCategories(array('category_id', 'category_name', 'cat_order'), null, 0, 0, 1);
            foreach ($category_info as $value) {

                $sub_cat_array = array();

                if (!empty($show2ndlevelCategory)) {

                    $category_info2 = $tableCategory->getSubcategories($value->category_id, array('category_id', 'category_name', 'cat_order'));

                    foreach ($category_info2 as $subresults) {

                        if (!empty($show3rdlevelCategory)) {

                            $subcategory_info2 = $tableCategory->getSubcategories($subresults->category_id, array('category_id', 'category_name', 'cat_order'));
                            $treesubarrays[$subresults->category_id] = array();
                            foreach ($subcategory_info2 as $subvalues) {
                                if ($showCount) {
                                    $treesubarrays[$subresults['category_id']][] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                                        'tree_sub_cat_name' => $subvalues->category_name,
                                        'count' => $tableSiteevent->getEventsCount($subvalues->category_id, 'subsubcategory_id', 1),
                                        'order' => $subvalues->cat_order,
                                    );
                                } else {
                                    $treesubarrays[$subresults['category_id']][] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                                        'tree_sub_cat_name' => $subvalues->category_name,
                                        'order' => $subvalues->cat_order,
                                    );
                                }
                            }

                            if ($showCount) {
                                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                    'sub_cat_name' => $subresults->category_name,
                                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                                    'count' => $tableSiteevent->getEventsCount($subresults->category_id, 'subcategory_id'),
                                    'order' => $subresults->cat_order);
                            } else {
                                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                    'sub_cat_name' => $subresults->category_name,
                                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                                    'order' => $subresults->cat_order);
                            }
                        } else {
                            if ($showCount) {
                                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                    'sub_cat_name' => $subresults->category_name,
                                    'count' => $tableSiteevent->getEventsCount($subresults->category_id, 'subcategory_id', 1),
                                    'order' => $subresults->cat_order);
                            } else {
                                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                    'sub_cat_name' => $subresults->category_name,
                                    'order' => $subresults->cat_order);
                            }
                        }
                    }
                }

                if ($showCount) {
                    $categories[] = $category_array = array('category_id' => $value->category_id,
                        'category_name' => $value->category_name,
                        'order' => $value->cat_order,
                        'count' => $tableSiteevent->getEventsCount($value->category_id, 'category_id', 1),
                        'sub_categories' => $sub_cat_array,
                    );
                } else {
                    $categories[] = $category_array = array('category_id' => $value->category_id,
                        'category_name' => $value->category_name,
                        'order' => $value->cat_order,
                        'sub_categories' => $sub_cat_array,
                    );
                }
            }
        } else {
            $category_info = $tableCategory->getCategorieshasevents(0, 'category_id', '', array('showEventType' => $showEventType), array('category_id', 'category_name', 'cat_order'));
            foreach ($category_info as $value) {

                $sub_cat_array = array();

                if (!empty($show2ndlevelCategory)) {

                    $category_info2 = $tableCategory->getCategorieshasevents($value->category_id, 'subcategory_id', '', array('eventType' => $showEventType), array('category_id', 'category_name', 'cat_order'));

                    foreach ($category_info2 as $subresults) {

                        if (!empty($show3rdlevelCategory)) {

                            $subcategory_info2 = $tableCategory->getCategorieshasevents($subresults->category_id, 'subsubcategory_id', '', array('eventType' => $showEventType), array('category_id', 'category_name', 'cat_order'));
                            $treesubarrays[$subresults->category_id] = array();
                            foreach ($subcategory_info2 as $subvalues) {
                                if ($showCount) {
                                    $treesubarrays[$subresults['category_id']][] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                                        'tree_sub_cat_name' => $subvalues->category_name,
                                        'order' => $subvalues->cat_order,
                                        'count' => $tableSiteevent->getEventsCount($subvalues->category_id, 'subsubcategory_id', 1),
                                    );
                                } else {
                                    $treesubarrays[$subresults['category_id']][] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                                        'tree_sub_cat_name' => $subvalues->category_name,
                                        'order' => $subvalues->cat_order
                                    );
                                }
                            }

                            if ($showCount) {
                                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                    'sub_cat_name' => $subresults->category_name,
                                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                                    'count' => $tableSiteevent->getEventsCount($subresults->category_id, 'subcategory_id', 1),
                                    'order' => $subresults->cat_order);
                            } else {
                                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                    'sub_cat_name' => $subresults->category_name,
                                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                                    'order' => $subresults->cat_order);
                            }
                        } else {
                            if ($showCount) {
                                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                    'sub_cat_name' => $subresults->category_name,
                                    'count' => $tableSiteevent->getEventsCount($subresults->category_id, 'subcategory_id', 1),
                                    'order' => $subresults->cat_order);
                            } else {
                                $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                    'sub_cat_name' => $subresults->category_name,
                                    'order' => $subresults->cat_order);
                            }
                        }
                    }
                }

                if ($showCount) {
                    $categories[] = $category_array = array('category_id' => $value->category_id,
                        'category_name' => $value->category_name,
                        'order' => $value->cat_order,
                        'sub_categories' => $sub_cat_array,
                        'count' => $tableSiteevent->getEventsCount($value->category_id, 'category_id', 1),
                    );
                } else {
                    $categories[] = $category_array = array('category_id' => $value->category_id,
                        'category_name' => $value->category_name,
                        'order' => $value->cat_order,
                        'sub_categories' => $sub_cat_array,
                    );
                }
            }
        }

        $this->view->categories = $categories;

        if (empty($siteeventCategoriesMiddEvents))
            return $this->setNoRender();

        //SET NO RENDER
        if (!(count($this->view->categories) > 0)) {
            return $this->setNoRender();
        }
    }

}