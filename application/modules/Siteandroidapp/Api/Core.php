<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteandroidapp_Api_Core extends Core_Api_Abstract {

    /*
     * Set listing types in the array
     */
    protected $_listingTypeArray = array();
    
    // Disabled previous APPs module if exist on plugin install or update.
    public function validatePreviousMobileAPP() {
        $db = Engine_Db_Table::getDefaultAdapter();

        $select = new Zend_Db_Select($db);
        $isPreviousAndroidMobileAPPExist = $select
                ->from('engine4_core_modules', 'enabled')
                ->where('name = ?', 'sitemobileandroidapp')
                ->where('enabled = ?', 1)
                ->query()
                ->fetchColumn();

        $select = new Zend_Db_Select($db);
        $isPreviousiOSMobileAPPExist = $select
                ->from('engine4_core_modules', 'enabled')
                ->where('name = ?', 'sitemobileiosapp')
                ->where('enabled = ?', 1)
                ->query()
                ->fetchColumn();

        $select = new Zend_Db_Select($db);
        $isPreviousMobileAPPExist = $select
                ->from('engine4_core_modules', 'enabled')
                ->where('name = ?', 'sitemobileapp')
                ->where('enabled = ?', 1)
                ->query()
                ->fetchColumn();

        if (!empty($isPreviousAndroidMobileAPPExist)) {
            // Delete all exist package files.  
            array_map('unlink', glob(APPLICATION_PATH . "/application/packages/module-sitemobileandroidapp*.json"));
            unlink(APPLICATION_PATH . "/application/languages/en/sitemobileandroidapp.csv");

            // Delete all db dependent code.
            $db->query('DELETE FROM `engine4_core_modules` WHERE `engine4_core_modules`.`name` = "sitemobileandroidapp" LIMIT 1');
        }

        if (empty($isPreviousiOSMobileAPPExist) && !empty($isPreviousMobileAPPExist)) {
            // Delete all exist package files.  
            array_map('unlink', glob(APPLICATION_PATH . "/application/packages/module-sitemobileapp*.json"));
            unlink(APPLICATION_PATH . "/application/packages/sitemobileapp.csv");

            // Delete all db dependent code.
            $db->query('DELETE FROM `engine4_core_modules` WHERE `engine4_core_modules`.`name` = "sitemobileapp" LIMIT 1');
        }

        return;
    }

    /*
     * Synchronised review listing types to android menus.
     * 
     * @return empty
     */

    public function synchroniseDashboardMenus() {
        if(!_ANDROID_VERSION || (_ANDROID_VERSION < '1.6.2'))
            return;
        
        $availableListingTypesArray = array();
        $listingTypes = Engine_Api::_()->getDbTable('listingtypes', 'sitereview')->getListingTypes(-1, array('visible' => 1));

        // Get all rows of review to insert / delete listing type.
        $table = Engine_Api::_()->getDbtable('menus', 'siteandroidapp');
        $select = $table->getSelect();
        $paginator = $table->fetchAll($select);
        foreach ($paginator as $menu) {
            if (isset($menu->params) && !empty($menu->params)) {
                $params = @unserialize($menu->params);
                if (isset($params['listingtype_id']) && !empty($params['listingtype_id']))
                    $availableListingTypesArray[$menu->menu_id] = $params['listingtype_id'];
            }
        }

        // Add listing type in menu table, If not exist.
        $this->_addListingTypeInMenu($listingTypes, $availableListingTypesArray);
        
        // Remove listing type in menu table, If not exist.
        $this->_removeListingTypeInMenu($availableListingTypesArray);
        
        return;
    }

    /*
     * Set the listing type in menu table
     * 
     * @param $listingTypes object
     * @param $availableListingTypesArray array
     * @return empty
     */

    private function _addListingTypeInMenu($listingTypes, $availableListingTypesArray) {
        if (!empty($listingTypes)) {
            $table = Engine_Api::_()->getDbtable('menus', 'siteandroidapp');
            foreach ($listingTypes as $listingType) {
                $this->_listingTypeArray[] = $listingType->listingtype_id;
                if (!in_array($listingType->listingtype_id, $availableListingTypesArray)) {             
                    $row = $table->createRow();
                    $row->name = 'sitereview_listing';
                    $row->dashboard_label = $listingType->title_plural;
                    $row->header_label = $listingType->title_plural;
                    $row->module = 'sitereview';
                    $row->show = 'both';
                    $row->type = 'menu';
                    $row->status = 1;
                    $row->default = 1;
                    $row->default = 1;
                    $row->params = @serialize(array("listingtype_id" => $listingType->listingtype_id));
                    $row->save();
                }
            }
        }
        
        return;
    }
    
    /*
     * Remove the listing type from menu table
     * 
     * @param $availableListingTypesArray array
     * @param $listingTypes object
     * @return empty
     */

    private function _removeListingTypeInMenu($availableListingTypesArray) {
        if (!empty($availableListingTypesArray)) {
            foreach($availableListingTypesArray as $menu_id => $listingtype_id) {
                if(!in_array($listingtype_id, $this->_listingTypeArray))
                    Engine_Api::_()->getItem('siteandroidapp_menus', $menu_id)->delete();
            }
        }
        
        return;
    }
}

?>