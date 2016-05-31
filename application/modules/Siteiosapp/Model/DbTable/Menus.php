<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Menus.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteiosapp_Model_DbTable_Menus extends Engine_Db_Table {

    protected $_name = 'siteiosapp_menus';
    protected $_rowClass = "Siteiosapp_Model_Menu";
    

    public function getPaginator($params = array()) {
        $paginator = Zend_Paginator::factory($this->getSelect($params));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        if (empty($params['limit'])) {
            $paginator->setItemCountPerPage(10);
        }

        return $paginator;
    }

    public function getSelect($params = array()) {
        $name = $this->info('name');

        $select = $this->select()
                ->order('.order ASC');

//        if (isset($params['user_id']) && !empty($params['user_id'])) {
//            $select->where($name . ".user_id =?", $params['user_id']);
//        }
//
//        if (isset($params['title']) && !empty($params['title'])) {
//            $select->where($name . ".title LIKE ?", '%' . $params['title'] . '%');
//        }
//
//        if (isset($params['key']) && !empty($params['key'])) {
//            $select->where($name . ".key LIKE ?", $params['key']);
//        }
//
//        if (isset($params['secret']) && !empty($params['secret'])) {
//            $select->where($name . ".secret LIKE ?", $params['secret']);
//        }
//
        if (isset($params['status']))
            $select->where("status =?", $params['status']);        

        return $select;
    }
}
