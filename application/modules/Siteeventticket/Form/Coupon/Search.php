<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Coupon_Search extends Engine_Form {

    protected $_searchForm;

    public function init() {

        $this
                ->setAttribs(array(
                    'id' => 'filter_form',
                    'class' => 'global_form_box',
                ))
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setMethod('GET');

        $this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

        $this->getAdditionalOptionsElement();

        parent::init();

        $this->loadDefaultDecorators();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $this->setAction($view->url(array(), 'siteeventticket_coupon', true))->getDecorator('HtmlTag')->setOption('class', '');
    }

    public function getAdditionalOptionsElement() {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $search_column = array();
        $contentTable = Engine_Api::_()->getDbtable('content', 'core');
        $contentTableName = $contentTable->info('name');

        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageTableName = $pageTable->info('name');

        $select = $contentTable->select()
                ->setIntegrityCheck(false)
                ->from($contentTableName, 'params')
                ->join($pageTableName, "$pageTableName.page_id = $contentTableName.page_id", null)
                ->where($contentTableName . '.name = ?', 'siteeventticket.search-coupons')
                ->where($pageTableName . '.name = ?', 'siteeventticket_coupon_index');

        $params = $contentTable->fetchAll($select);

        foreach ($params as $widget) {
            if (isset($widget['params']['search_column'])) {
                $search_column = $widget['params']['search_column'];
            }
        }

        $showTabArray = Zend_Controller_Front::getInstance()->getRequest()->getParam("search_column", array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => "5"));

        $enabledColumns = array_intersect($search_column, $showTabArray);
        if (empty($enabledColumns)) {
            $enabledColumns = $showTabArray;
        }

        $i = -5000;

        if (in_array("2", $enabledColumns)) {
            $this->addElement('Text', 'event_title', array(
                'label' => 'Event Ticket Title',
            ));
        }

        if (in_array("3", $enabledColumns)) {
            $this->addElement('Text', 'title', array(
                'label' => 'Coupon Title',
            ));
        }

        if (in_array("1", $enabledColumns)) {
            $show_multiOptions = array();
            $show_multiOptions[0] = '';
            $show_multiOptions["end_week"] = 'Ending this Week';
            $show_multiOptions["end_month"] = 'Ending this Month';
            $show_multiOptions["creation_date"] = 'Recently Posted Coupons';
            $show_multiOptions["like_count"] = 'Most Liked Coupons';
            $show_multiOptions["view_count"] = 'Most Viewed Coupons';
            $show_multiOptions["comment_count"] = 'Most Commented Coupons';
            $show_multiOptions["end_offer"] = 'Expired Coupons';

            $this->addElement('Select', 'orderby', array(
                'label' => 'Browse By',
                'multiOptions' => $show_multiOptions,
            ));
        }

        if (in_array("4", $enabledColumns)) {

            $translate = Zend_Registry::get('Zend_Translate');

            $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);

            if (count($categories) != 0) {
                $categories_prepared[0] = "";
                foreach ($categories as $category) {
                    $categories_prepared[$category->category_id] = $translate->translate($category->category_name);
                }

                $onChangeEvent = "addOptions(this.value, 'cat_dependency', 'subcategory_id', 0);";
                $categoryFiles = 'application/modules/Siteevent/views/scripts/_subCategory.tpl';

                $this->addElement('Select', 'category_id', array(
                    'label' => 'Category',
                    //'order' => $row->order,
                    'multiOptions' => $categories_prepared,
                    'onchange' => $onChangeEvent,
                    'decorators' => array(
                        'ViewHelper',
                        array('Label', array('tag' => 'div')),
                        array('HtmlTag', array('tag' => 'div'))),
                ));

                $this->addElement('Select', 'subcategory_id', array(
                    'RegisterInArrayValidator' => false,
                    //'order' => $row->order + 1,
                    'decorators' => array(array('ViewScript', array(
                                'showAllCategories' => 1,
                                'viewScript' => $categoryFiles,
                                'class' => 'form element')))
                ));
            }
        }

        $this->addElement('Hidden', 'page', array(
            'order' => $i--,
        ));

        $this->addElement('Hidden', 'category', array(
            'order' => $i--,
        ));

        $this->addElement('Hidden', 'subcategory', array(
            'order' => $i--,
        ));

        $this->addElement('Hidden', 'subsubcategory', array(
            'order' => $i--,
        ));

        $this->addElement('Hidden', 'categoryname', array(
            'order' => $i--,
        ));

        $this->addElement('Hidden', 'subcategoryname', array(
            'order' => $i--,
        ));

        $this->addElement('Hidden', 'subsubcategoryname', array(
            'order' => $i--,
        ));

        $this->addElement('Button', 'done', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));

        return $this;
    }

}
