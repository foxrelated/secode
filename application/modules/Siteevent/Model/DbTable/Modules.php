<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Seaocore
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Integrated.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
class Siteevent_Model_DbTable_Modules extends Engine_Db_Table {

    protected $_name = 'siteevent_modules';
    protected $_rowClass = 'Siteevent_Model_Module';

    public function getModuleTitle($name) {
        $coreTable = Engine_Api::_()->getDbtable('modules', 'core');
        $coreTableName = $coreTable->info('name');
        $select = $coreTable->select()
                ->from($coreTableName, array('title'))
                ->where('name =?', $name);
        $title = $select->query()->fetchColumn();
        return $title;
    }

    public function getIntegratedModules($params = array()) {

        $coreModuleTable = Engine_Api::_()->getDbtable('modules', 'core');
        $moduleTableName = $this->info('name');
        $coreModuleTableName = $coreModuleTable->info('name');
        $select = $this->select()->setIntegrityCheck(false)
                ->from($moduleTableName, array('*'))
                ->join($coreModuleTableName, $coreModuleTableName . '.name = ' . $moduleTableName . '.item_module', array())
                ->where($coreModuleTableName . '.enabled =?', 1);


        if (isset($params['enabled']) && !empty($params['enabled'])) {
            $select->where($moduleTableName . '.enabled =?', 1);
        }

        if (isset($params['item_module']) && !empty($params['item_module'])) {
            $select->where($moduleTableName . '.item_module =?', $params['item_module']);
        }

        if (isset($params['item_type']) && !empty($params['item_type'])) {
            $select->where($moduleTableName . '.item_type =?', $params['item_type']);
        }

        $results = $this->fetchAll($select)->toArray();

        if(empty($results) && isset($params['checked']) && !empty($params['checked'])) {
            return false;
        }
        if (empty($results) && (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent'))) {
            return true;
        } elseif (empty($results) && (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessevent'))) {
            return true;
        } elseif (empty($results) && (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent'))) {
            return true;
        }

        if (empty($results)) {
            return false;
        }

        if (($results || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) && (isset($params['item_module']) && $params['item_module'] == 'sitepage')) {
            return true;
        } elseif (($results || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessevent')) && (isset($params['item_module']) && $params['item_module'] == 'sitebusiness')) {
            return true;
        } elseif (($results || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) && (isset($params['item_module']) && $params['item_module'] == 'sitegroup')) {
            return true;
        } elseif (($results || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore')) && (isset($params['item_module']) && $params['item_module'] == 'sitestore')) {
            return true;
        } elseif (($results || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview')) && (isset($params['item_module']) && $params['item_module'] == 'sitereview')) {
            return true;
        }
        return $results;
    }

    public function getIntegratedItemTitle($item_module, $item_type) {

        $title = $this->select()
                        ->from($this->info('name'), 'item_title')
                        ->where('item_module =?', $item_module)
                        ->where('item_type =?', $item_type)
                        ->query()->fetchColumn();

        if (empty($title))
            $title = $this->getModuleTitle($item_module);
        return $title;
    }

}