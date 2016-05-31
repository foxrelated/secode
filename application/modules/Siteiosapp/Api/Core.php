<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteiosapp_Api_Core extends Core_Api_Abstract {

    // Disabled previous APPs module if exist on plugin install or update.
    public function validatePreviousMobileAPP() {
        $db = Engine_Db_Table::getDefaultAdapter();

        $select = new Zend_Db_Select($db);
        $isPreviousAndroidMobileAPPExist = $select
                ->from('engine4_core_modules', 'enabled')
                ->where('name = ?', 'sitemobileiosapp')
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
            array_map('unlink', glob(APPLICATION_PATH . "/application/packages/module-sitemobileiosapp*.json"));
            unlink(APPLICATION_PATH . "/application/packages/sitemobileiosapp.csv");

            // Delete all db dependent code.
            $db->query('DELETE FROM `engine4_core_modules` WHERE `engine4_core_modules`.`name` = "sitemobileiosapp" LIMIT 1');
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

}

?>