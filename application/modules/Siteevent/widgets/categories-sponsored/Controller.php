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
class Siteevent_Widget_CategoriesSponsoredController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $itemCount = $this->_getParam('itemCount', 0);
        $this->view->showIcon = $this->_getParam('showIcon', 1);

        //GET CATEGORY TABLE
        $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'siteevent');

        //GET SPONSORED CATEGORIES
        $this->view->categories = $categories = $tableCategory->getCategories(array('category_id', 'category_name', 'cat_order', 'file_id', 'category_slug', 'cat_dependency', 'subcat_dependency'), null, 0, 1, 0, $itemCount);

        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();

        //GET SPONSORED CATEGORIES COUNT
        $this->view->totalCategories = Count($categories);

        if ($this->view->totalCategories <= 0) {
            return $this->setNoRender();
        }
    }

}