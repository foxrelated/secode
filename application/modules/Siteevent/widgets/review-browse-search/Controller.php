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
class Siteevent_Widget_ReviewBrowseSearchController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $searchForm = $this->view->searchForm = new Siteevent_Form_Review_Search(array('type' => 'siteevent_review'));
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->requestParams = $requestParams = $request->getParams();

        if (isset($requestParams['page'])) {
            unset($requestParams['page']);
        }

        $searchForm
                ->setMethod('get')
                ->populate($requestParams)
        ;

        $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name', 'category_slug'), null, 0, 0, 1);
        $categories_slug[0] = "";
        if (count($categories) != 0) {
            foreach ($categories as $category) {
                $categories_slug[$category->category_id] = $category->getCategorySlug();
            }
        }
        $this->view->categories_slug = $categories_slug;

        $this->view->searchField = 'search';
        $this->view->widgetParams = $widgetParams = $this->_getAllParams();
    }

}