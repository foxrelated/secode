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
class Siteevent_Widget_SearchSiteeventController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $siteeventtable = Engine_Api::_()->getDbtable('events', 'siteevent');
        $siteeventName = $siteeventtable->info('name');

        $categoryTable = Engine_Api::_()->getDbTable('categories', 'siteevent');

        $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();

        $this->view->siteevent_post = true;

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        if (!isset($params['category_id']))
            $params['category_id'] = 0;
        if (!isset($params['subcategory_id']))
            $params['subcategory_id'] = 0;
        if (!isset($params['subsubcategory_id']))
            $params['subsubcategory_id'] = 0;
        $this->view->category_id = $category_id = $params['category_id'];
        $this->view->subcategory_id = $subcategory_id = $params['subcategory_id'];
        $this->view->subsubcategory_id = $subsubcategory_id = $params['subsubcategory_id'];
        $this->view->siteeventSearchCategoryType = $siteeventSearchCategoryType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventsearch.categorytype', 1);
        $this->view->categoryInSearchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'category_id');
        if (empty($siteeventSearchCategoryType))
            return $this->setNoRender();
        
        if(!isset($params['profile_type']) && !empty($this->view->category_id) && !empty($this->view->categoryInSearchForm)) {
            $categoryIds = array();
            $categoryIds[] = $this->view->category_id;
            $categoryIds[] = $this->view->subcategory_id;
            $categoryIds[] = $this->view->subsubcategory_id;

            $profile_type = Engine_Api::_()->getDbTable('categories', 'siteevent')->getProfileType($categoryIds, 0, 'profile_type');
            if(!empty($profile_type)) {
                $params['profile_type'] = $profile_type;
            }    
        }        

        //FORM CREATION
        $this->view->locationDetection = $this->_getParam('locationDetection', 1);
        $this->view->viewType = $this->_getParam('viewType', 'vertical');
        $this->view->showAllCategories = $this->_getParam('showAllCategories', 1);
        $this->view->whatWhereWithinmile = $this->_getParam('whatWhereWithinmile', 0);
        $this->view->advancedSearch = $this->_getParam('advancedSearch', 0);
        $this->view->widgetSettings = $widgetSettings = array(
            'priceFieldType' => $this->_getParam('priceFieldType', 'slider'),
            'minPrice' => $this->_getParam('minPrice', 0),
            'maxPrice' => $this->_getParam('maxPrice', 999),
            'viewType' => $this->view->viewType,
            'showAllCategories' => $this->view->showAllCategories,
            'resultsAction' => $this->_getParam('resultsAction', 'index'),
            'whatWhereWithinmile' => $this->view->whatWhereWithinmile,
            'advancedSearch' => $this->view->advancedSearch,
            'locationDetection' => $this->view->locationDetection,
        );
        $this->view->form = $form = new Siteevent_Form_Search(array('type' => 'siteevent_event', 'widgetSettings' => $widgetSettings));
        $this->view->viewType = $this->_getParam('viewType', 'vertical');

        $orderBy = $request->getParam('orderby', null);
        if (empty($orderBy)) {
            $order = Engine_Api::_()->siteevent()->showSelectedBrowseBy($this->view->identity);
            if($order == 'viewcount') {
                $order = 'view_count';
            }
            $form->orderby->setValue("$order");
        }

        if (isset($params['tag']) && !empty($params['tag'])) {
            $tag = $params['tag'];
            if (isset($params['tag_id']) && !empty($params['tag_id'])) {
                $tag_id = $params['tag_id'];
            }
            $page = 1;
            if (isset($params['page']) && !empty($params['page'])) {
                $page = $params['page'];
            }

            $params['search'] = $params['tag'] = $tag;
            if (isset($params['tag_id']) && !empty($params['tag_id'])) {
                $params['tag_id'] = $tag_id;
            }
            $params['page'] = $page;
        }

        $orderBy = $request->getParam('orderby', null);

        if (!empty($orderBy)) {
            $params['orderby'] = $orderBy;
        }

        if (!empty($params))
            $form->populate($params);

        if (!$viewer) {
            $form->removeElement('show');
        }

        //SHOW PROFILE FIELDS ON DOME READY
        if (!empty($this->view->categoryInSearchForm) && !empty($this->view->categoryInSearchForm->display) && !empty($category_id)) {

            $categoryIds = array();
            $categoryIds[] = $category_id;
            $categoryIds[] = $subcategory_id;
            $categoryIds[] = $subsubcategory_id;

            //GET PROFILE MAPPING ID
            $this->view->profileType = Engine_Api::_()->getDbTable('categories', 'siteevent')->getProfileType($categoryIds, 0, 'profile_type');
        }

        $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name', 'category_slug'), null, 0, 0, 1);
        $categories_slug[0] = "";
        if (count($categories) != 0) {
            foreach ($categories as $category) {
                $categories_slug[$category->category_id] = $category->getCategorySlug();
            }
        }
        $this->view->categories_slug = $categories_slug;
    }

}