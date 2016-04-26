<?php
class Ynidea_Widget_ListCategoriesController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') . 
                'application/modules/Ynidea/externals/scripts/collapsible.js');
        
        $categories = Engine_Api::_() -> getDbTable('categories', 'ynidea') -> getCategories();
        unset($categories[0]);
        $this->view->categories = $categories;
        if (count($categories) == 0) {
            $this->setNoRender(true);
        }
    }
}