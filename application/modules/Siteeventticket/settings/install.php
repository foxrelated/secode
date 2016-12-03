<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Installer extends Engine_Package_Installer_Module {

    function onPreinstall() {

        $db = $this->getDb();

        //CHECK THAT SITEEVENT PLUGIN IS ACTIVATED OR NOT
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'siteevent.isActivate')
            ->limit(1);
        $siteevent_settings = $select->query()->fetchAll();
        $siteevent_is_active = !empty($siteevent_settings) ? $siteevent_settings[0]['value'] : 0;

        $select = new Zend_Db_Select($db);
        $issiteeventEnabled = $select
            ->from('engine4_core_modules', 'enabled')
            ->where('name = ?', 'siteevent')
            ->where('enabled = ?', 1)
            ->query()
            ->fetchColumn()
        ;

        if (empty($issiteeventEnabled)) {
            $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

            return $this->_error("<span style='color:red'>Note: You have not installed the '<a href='http://www.socialengineaddons.com/socialengine-advanced-events-plugin' target='_blank'>Advanced Events Plugin</a>' on your site yet. Please install it first before installing the 'Advanced Events - Paid Extension'. <a href='" . $base_url . "/manage'>Click here</a> to go to Manage Packages.</span>");
        } else {
            if (empty($siteevent_is_active)) {
                $newVar = !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://';
                $core_final_url = '';
                $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
                $explode_base_url = explode("/", $baseUrl);
                foreach ($explode_base_url as $url_key) {
                    if ($url_key != 'install') {
                        $core_final_url .= $url_key . '/';
                    }
                }
                return $this->_error("<span style='color:red'>Note: You have installed the 'Advanced Events Plugin' but not activated it on your site yet. Please activate it first before installing the 'Advanced Events - Paid Extension'.</span> <a href='" . $newVar . $core_final_url . "admin/siteevent/settings/readme'>Click here</a> to activate the 'Advanced Events Plugin'.");
            }

            $getErrorMsg = $this->_getVersion();
            if (!empty($getErrorMsg)) {
                return $this->_error($getErrorMsg);
            }

            $PRODUCT_TYPE = 'siteeventticket';
            $PLUGIN_TITLE = 'Siteeventticket';
            $PLUGIN_VERSION = '4.8.10p2';
            $PLUGIN_CATEGORY = 'plugin';
            $PRODUCT_DESCRIPTION = 'Advanced Events - Paid Event and Ticket Selling Extension';
            $_PRODUCT_FINAL_FILE = 0;
            $SocialEngineAddOns_version = '4.8.9p12';
            $PRODUCT_TITLE = 'Advanced Events - Paid Event and Ticket Selling Extension';

            $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
            $is_file = @file_exists($file_path);
            if (empty($is_file)) {
                include APPLICATION_PATH . "/application/modules/Siteevent/controllers/license/license3.php";
            } else {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
                $is_Mod = $select->query()->fetchObject();
                if (empty($is_Mod)) {
                    include_once $file_path;
                }
            }
            parent::onPreinstall();
        }
    }

    function onInstall() {
        
        $db = $this->getDb();
        
        //START SITEGATEWAY RELATED WORK
        $sitegatewayEnabled = $db->select()
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitegateway')
            ->where('enabled = ?', 1)                
            ->limit(1)
            ->query()
            ->fetchColumn();        
        
        if (!empty($sitegatewayEnabled)) {     
            
            $orderTable = $db->query('SHOW TABLES LIKE \'engine4_siteeventticket_orders\'')->fetch();
            if (!empty($orderTable)) {
                $payment_split_column = $db->query("SHOW COLUMNS FROM engine4_siteeventticket_orders LIKE 'payment_split'")->fetch();
                if (empty($payment_split_column)) {
                    $db->query("ALTER TABLE `engine4_siteeventticket_orders` ADD `payment_split` TINYINT(1) NOT NULL DEFAULT '0';");
                }
            }            
            
            $eventGatewayTable = $db->query('SHOW TABLES LIKE \'engine4_siteeventticket_gateways\'')->fetch();
            if (!empty($eventGatewayTable)) {
                $eventIdIndex = $db->query("SHOW INDEX FROM `engine4_siteeventticket_gateways` WHERE Key_name = 'event_id'")->fetch();
                if (!empty($eventIdIndex)) {
                    $db->query("ALTER TABLE `engine4_siteeventticket_gateways` DROP INDEX event_id");
                    $db->query("ALTER TABLE `engine4_siteeventticket_gateways` ADD INDEX (`event_id`)");
                }
            }            
            
            $eventBillTable = $db->query('SHOW TABLES LIKE \'engine4_siteeventticket_eventbills\'')->fetch();
            if (!empty($eventBillTable)) {
                $eventIdIndex = $db->query("SHOW INDEX FROM `engine4_siteeventticket_eventbills` WHERE Key_name = 'event_id'")->fetch();
                if (!empty($eventIdIndex)) {
                    $db->query("ALTER TABLE `engine4_siteeventticket_eventbills` DROP INDEX event_id");
                    $db->query("ALTER TABLE `engine4_siteeventticket_eventbills` ADD INDEX (`event_id`)");
                }
                
                $gateway_id_column = $db->query("SHOW COLUMNS FROM engine4_siteeventticket_eventbills LIKE 'gateway_id'")->fetch();
                if (empty($gateway_id_column)) {
                    $db->query("ALTER TABLE `engine4_siteeventticket_eventbills` ADD `gateway_id` INT(11) NOT NULL AFTER `status`;");
                }
           
            }        
        }
        //END SITEGATEWAY RELATED WORK
        
        $db->update('engine4_core_menuitems', array('label' => 'Coupons'), array('name = ?' => 'siteevent_dashboard_coupons'));        
        $db->update('engine4_core_menuitems', array('label' => 'Commissions Bill'), array('name = ?' => 'siteevent_dashboard_yourbill'));
        $db->update('engine4_activity_actiontypes', array('body' => '{item:$subject} added a new ticket {item:$ticket} for the event:'), array('type = ?' => 'siteeventticket_new'));
        $db->update('engine4_activity_actiontypes', array('body' => '{item:$subject} has purchased {var:$count} ticket(s) for the event:'), array('type = ?' => 'siteeventticket_order_place'));
        
        $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('SITEEVENTTICKET_THRESHOLD_COMMISSION_ADMIN', 'siteeventticket', '[event_title],[event_title_with_link],[dashboard_commission_bills]'), ('SITEEVENTTICKET_THRESHOLD_COMMISSION_OWNER', 'siteeventticket', '[event_title],[event_title_with_link],[dashboard_commission_bills]');");        

        $siteeventticketEnabled = $db->select()
            ->from('engine4_core_modules')
            ->where('name = ?', 'siteeventticket')
            ->where('enabled = ?', 1)                
            ->limit(1)
            ->query()
            ->fetchColumn();        
        
        if (empty($siteeventticketEnabled)) {        
            $this->runWidgetSettings();
        }
                
        $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES ('core_mini_siteeventticketmytickets', 'siteeventticket', 'My Tickets', 'Siteeventticket_Plugin_Menus', '', 'core_mini', '', 4);");
        $db->delete('engine4_core_mailtemplates', array('module = ?' => 'siteeventticket', 'type =?' => 'siteeventticket_order_invoice'));

        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='siteeventticket';");

        //PAID PLUGIN WORK
        //START: UPGRADE QUERIES - MOBILE COMPATIBLE
        $select = new Zend_Db_Select($db);

        $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitemobile')
            ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();

        $select = new Zend_Db_Select($db);

        $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitemobileapp')
            ->where('enabled = ?', 1);
        $is_sitemobileapp_object = $select->query()->fetchObject();
        if ($is_sitemobile_object) {
            $this->packagePageCreate('engine4_sitemobile_pages', 'engine4_sitemobile_content');
            $this->packagePageCreate('engine4_sitemobile_tablet_pages', 'engine4_sitemobile_tablet_content');
        }
        if ($is_sitemobileapp_object) {
            $this->packagePageCreate('engine4_sitemobileapp_pages', 'engine4_sitemobileapp_content');
            $this->packagePageCreate('engine4_sitemobileapp_tablet_pages', 'engine4_sitemobileapp_tablet_content');
        }
        
        $page_id_1 = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteevent_index_view")
            ->limit(1)
            ->query()
            ->fetchColumn();
        if ($page_id_1) {

            $content_id = $db->select()
                ->from('engine4_core_content', 'content_id')
                ->where('page_id = ?', $page_id_1)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'siteeventticket.members-bought-ticket')
                ->limit(1)
                ->query()
                ->fetchColumn();

            if (empty($content_id)) {
                $right_container_id = $db->select()
                    ->from('engine4_core_content', 'content_id')
                    ->where('page_id = ?', $page_id_1)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'right')->limit(1)
                    ->query()
                    ->fetchColumn();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id_1,
                    'type' => 'widget',
                    'name' => 'siteeventticket.members-bought-ticket',
                    'parent_content_id' => $right_container_id,
                    'order' => 4,
                    'params' => '{"title":"Event Attendees"}'
                ));
            }
       }        
        
       parent::onInstall();
    }

    function onPostInstall() {
        $db = $this->getDb();

        $packageCreation = 1;
        $this->_setPackageId($packageCreation);
    }

    function onEnable() {
        $db = $this->getDb();

        $db->query('UPDATE `engine4_core_modules` SET `enabled` = "1" WHERE `engine4_core_modules`.`name` = "siteeventpaid" LIMIT 1');

        $db->delete('engine4_activity_notificationtypes', array('module = ?' => 'siteevent', 'type IN (?)' => array('siteevent_accepted', 'siteevent_confirm_guests', 'siteevent_join', 'siteevent_member', 'siteevent_reject_guests', 'siteevent_request_disapprove', 'siteevent_rsvp_change')));

        $packageCreation = 0;
        $this->_setPackageId($packageCreation);
        
        $db->update('engine4_core_menuitems', array('menu' => 'siteevent_dashboard_ticket', 'order' => 145), array('name = ?' => 'siteevent_dashboard_waitlist'));        

        parent::onEnable();
    }

    function onDisable() {

        $db = $this->getDb();

        $db->query('UPDATE `engine4_core_modules` SET `enabled` = "0" WHERE `engine4_core_modules`.`name` = "siteeventpaid" LIMIT 1');

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
("siteevent_accepted", "siteevent", "Your request to join the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id} has been approved.", 0, "", 1),
("siteevent_confirm_guests", "siteevent", "{item:$subject} has confirmed you for the event {item:$object}.", 0, "", 1),
("siteevent_join", "siteevent", "{item:$subject} has joined the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.", 0, "", 1),
("siteevent_member", "siteevent", "You are now a guest of the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.", 0, "", 1),
("siteevent_reject_guests", "siteevent", "{item:$subject} has not confirmed you for the event {item:$object}.", 0, "", 1),
("siteevent_request_disapprove", "siteevent", "Your request to join the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id} has been rejected.", 0, "", 1),
("siteevent_rsvp_change", "siteevent", "{item:$subject} has changed RSVP for the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id} to {var:$status}.", 0, "", 1);');

        $db->update('engine4_activity_actiontypes', array('enabled' => 1), array('module = ?' => 'siteevent', 'type IN (?)' => array('siteevent_join', 'siteevent_leave', 'siteevent_maybe_join', 'siteevent_mid_join', 'siteevent_mid_leave', 'siteevent_mid_maybe')));
        
        $db->update('engine4_core_menuitems', array('menu' => 'siteevent_dashboard_content', 'order' => 30), array('name = ?' => 'siteevent_dashboard_waitlist'));        

        parent::onDisable();
    }

    private function _getVersion() {

        $db = $this->getDb();

        $errorMsg = '';
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'siteevent' => '4.8.8p5',
            'sitemailtemplates' => '4.8.8p1',
            'sitepage' => '4.8.8p1',
            'sitegroup' => '4.8.8p1',
            'sitebusiness' => '4.8.8p1',
            'sitestore' => '4.8.6p10',
            'sitemenu' => '4.8.8p3',
        );

        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')
                ->where('name = ?', "$key")
                ->where('enabled = ?', 1);
            $isModEnabled = $select->query()->fetchObject();
            if (!empty($isModEnabled)) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_modules', array('title', 'version'))
                    ->where('name = ?', "$key")
                    ->where('enabled = ?', 1);
                $getModVersion = $select->query()->fetchObject();

                $isModSupport = $this->checkVersion($getModVersion->version, $value);
                if (empty($isModSupport)) {
                    $finalModules[$key] = $getModVersion->title;
                }
            }
        }

        foreach ($finalModules as $modArray) {
            $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "' . $modArray . '".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
        }

        return $errorMsg;
    }

    private function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }
    
    protected function _setPackageId($packageCreation) {

        //START WORK FOR DEFAULT FREE PACKAGE INTO WHICH IS ALREADY CREATED
        $db = $this->getDb();

        $siteevent_package_id_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_events LIKE 'package_id'")->fetch();
        if (empty($siteevent_package_id_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_events` ADD `package_id` INT( 11 ) NOT NULL DEFAULT '1' AFTER `event_id`");
        }

        $select = new Zend_Db_Select($db);

        if (!empty($packageCreation)) {
            $isFreePackageExist = $db->select()
                ->from('engine4_siteeventpaid_packages')
                ->limit(1)
                ->query()
                ->fetchColumn();
            if (empty($isFreePackageExist)) {
                $db->query("INSERT IGNORE INTO `engine4_siteeventpaid_packages` (`title`, `description`, `level_id`, `price`, `recurrence`, `recurrence_type`, `duration`, `duration_type`, `sponsored`, `featured`, `overview`, `video`, `video_count`, `photo`, `photo_count`, `approved`, `enabled`, `defaultpackage`, `renew`, `renew_before`, `profile`, `profilefields`, `order`, `update_list`) VALUES
('Free Event Package', 'This is a free event package. One does not need to pay for creating an event of this package.', '0', '0.00', 0, 'forever', 0, 'forever', 0, 0, 1, 1, 0, 1, 0, 1, 1, 1, 1, 0, 1, NULL, 0, 1);
");
            }
        }

        $select = new Zend_Db_Select($db);
        $select->from('engine4_siteevent_events', array('package_id', 'event_id', 'approved'));
        $events = $select->query()->fetchAll();
        foreach ($events as $event) {
            $package_id = $event['package_id'];
            $approved_id = $event['approved'];
            $event_id = $event['event_id'];
            $currentPackageId = $db->select()
                ->from('engine4_siteeventpaid_packages', 'package_id')
                ->order('package_id ASC')
                ->limit(1)
                ->query()
                ->fetchColumn();

            if (empty($package_id)) {
                $db->query("UPDATE `engine4_siteevent_events` SET `package_id` = '$currentPackageId' WHERE `engine4_siteevent_events`.`event_id` = '$event_id' LIMIT 1;
");
                if (!empty($approved_id)) {
                    $db->query("UPDATE `engine4_siteevent_events` SET `pending` = 0 WHERE `engine4_siteevent_events`.`event_id` = '$event_id' LIMIT 1;
");
                }
            }
        }
        //END DEFAULT PACKAGE WORK
    }

    public function packagePageCreate($pageTable, $contentTable) {

        //CREATE PACKAGE BASED EVENT 
        $db = $this->getDb();
        $page_id = $db->select()
            ->from($pageTable, 'page_id')
            ->where('name = ?', "siteeventpaid_package_index")
            ->limit(1)
            ->query()
            ->fetchColumn();
        if (empty($page_id)) {

            $containerCount = 0;

            //CREATE PAGE
            $db->insert($pageTable, array(
                'name' => "siteeventpaid_package_index",
                'displayname' => 'Advanced Events - Packages for Events',
                'title' => 'Packages for Events',
                'description' => 'This is the Packages page for Event.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            //TOP CONTAINER
            $db->insert($contentTable, array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert($contentTable, array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert($contentTable, array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN-MIDDLE CONTAINER
            $db->insert($contentTable, array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.navigation-siteevent',
                'parent_content_id' => $top_middle_id,
                'params' => '',
            ));

            $db->insert($contentTable, array(
                'type' => 'widget',
                'name' => 'siteeventpaid.list-packages',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'params' => '',
                'order' => 1,
            ));
        }
    }

    public function runWidgetSettings() {

//GET DB
        $db = $this->getDb();

//TICKET QUERIES
        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='siteevntticket';");

        //ADDING COLUMNS TO ADVANCED EVENTS TABLES FOR TICKET EXTENSION
        $siteevent_occurrences_ticket_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_occurrences LIKE 'ticket_id_sold'")->fetch();
        if (empty($siteevent_occurrences_ticket_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_occurrences` ADD `ticket_id_sold` tinytext COLLATE utf8_unicode_ci DEFAULT NULL");
        }

        //ADDING `event_gateway` COLUMNS TO ADVANCED EVENTS TABLES FOR TICKET EXTENSION
        $siteevent_otherinfo_gateway_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_otherinfo LIKE 'event_gateway'")->fetch();
        if (empty($siteevent_otherinfo_gateway_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_otherinfo` ADD `event_gateway` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL");
        }

        //ADDING `is_tax_allow` COLUMNS TO ADVANCED EVENTS OTHERINFO TABLES FOR TICKET EXTENSION
        $siteevent_otherinfo_tax_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_otherinfo LIKE 'is_tax_allow'")->fetch();
        if (empty($siteevent_otherinfo_tax_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_otherinfo` ADD `is_tax_allow` tinyint(1) DEFAULT 0");
        }

        //ADDING `tax_rate` COLUMNS TO ADVANCED EVENTS OTHERINFO TABLES FOR TICKET EXTENSION
        $siteevent_otherinfo_tax_rate_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_otherinfo LIKE 'tax_rate'")->fetch();
        if (empty($siteevent_otherinfo_tax_rate_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_otherinfo` ADD `tax_rate` DECIMAL(16,2) NOT NULL DEFAULT '0.00'");
        }

        //ADDING 'tax_id_no' COLUMNS TO ADVANCED EVENTS OTHERINFO TABLES FOR TICKET EXTENSION
        $siteevent_otherinfo_tax_id_no_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_otherinfo LIKE 'tax_id_no'")->fetch();
        if (empty($siteevent_otherinfo_tax_id_no_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_otherinfo` ADD `tax_id_no` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL");
        }

        $siteevent_otherinfo_terms_of_use_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_otherinfo LIKE 'terms_of_use'")->fetch();
        if (empty($siteevent_otherinfo_terms_of_use_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_otherinfo` ADD `terms_of_use` text COLLATE utf8_unicode_ci DEFAULT NULL");
        }

        //PRICE FIELD DISABLE 
        $db->query("UPDATE `engine4_core_settings` SET `value` = '0' WHERE `engine4_core_settings`.`name` = 'siteevent.price' LIMIT 1;");

        $db->query("UPDATE `engine4_core_settings` SET `value` = '0' WHERE `engine4_core_settings`.`name` = 'siteevent.guestconfimation' LIMIT 1;");

        $content = '<div style="width: 650px; padding-bottom: 15px; border-color: #ccc; border-style: solid; border-width: 12px 2px 2px; font-family: arial; margin:0 auto;"><table style="width:100%;"><tr><td><div style="border-bottom: 2px dotted #ccc; margin: 0 15px 0 15px; padding: 15px 0; text-align: left;"><span style="color: #535353;font-size: 22px; line-height: 22px; letter-spacing: -1px;">[Free]! [event_name] [event_date_time]</span></div><div style="border-bottom: 2px dotted #ccc; margin: 0 15px 0 15px; padding: 15px 0; text-align: left;"><table style="width:100%;"><tr><td style="text-align: left; width: 40%;"><div style="color: #999; font-size: 16px; letter-spacing: 0; line-height: 16px; margin-top: 10px;"> ORDERED ON </div><div style="color: #535353; font-size: 14px; line-height: 21px;"><strong>[ticket_date_time]</strong></div></td><td style="text-align: right;vertical-align: top; width: 60%;"><div style="text-align: right; color: #535353; font-size: 14px; line-height: 21px;width:84%;"> [event_location], </div><div style="text-align: right; color: #535353; font-size: 14px; line-height: 21px;width:84%;">[event_venue]</div></td></tr></table></div><div style="margin: 0 15px 0 15px; text-align: left;padding: 15px 0 0;"><table><tr><td style="border-right: 2px dashed #ccc; padding-top: 4px;text-align: left; vertical-align: top; width: 150px;"><div style="color: #999; font-size: 16px; letter-spacing: 0; line-height: 16px;"> TICKET # </div><div style="color: #535353; font-size: 14px; line-height: 21px;"><strong> [buyer_ticket_id] </strong></div><div style="color: #999; font-size: 16px; letter-spacing: 0; line-height: 16px; margin-top: 10px;"> PRICE </div><div style="color: #535353; font-size: 14px; line-height: 21px;"><strong> [ticket_price] </strong></div></td><td style="padding: 4px 0 0 32px;text-align: left;width: 300px;vertical-align: top;"><div style="color: #999; font-size: 16px; letter-spacing: 0; line-height: 16px;">NAME</div><div style="color: #535353; font-size: 14px; line-height: 21px;"><strong> [user_name] </strong></div><div style="color: #999; font-size: 16px; letter-spacing: 0; line-height: 16px; margin-top: 10px;"> TICKET TYPE </div><div style="color: #535353; font-size: 14px; line-height: 21px;"> <strong>[ticket_title]</strong> </div></td><td style="text-align: right;width: 100px;">[QR_code_image]</td></tr></table></div></td></tr></table></div>';

        $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('siteeventticket.format.bodyhtml.default', '$content')");

        /* START MAKING WIDGETIZE PAGE 
         * INSERT TICKET WIDGET ON EVENT PROFILE PAGE IN RIGHT CONTAINER
         */
        $page_id_1 = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteevent_index_view")
            ->limit(1)
            ->query()
            ->fetchColumn();
        if ($page_id_1) {
            $content_id = $db->select()
                ->from('engine4_core_content', 'content_id')
                ->where('page_id = ?', $page_id_1)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'siteeventticket.event-tickets')
                ->limit(1)
                ->query()
                ->fetchColumn();

            if (empty($content_id)) {
                $right_container_id = $db->select()
                    ->from('engine4_core_content', 'content_id')
                    ->where('page_id = ?', $page_id_1)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'right')->limit(1)
                    ->query()
                    ->fetchColumn();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id_1,
                    'type' => 'widget',
                    'name' => 'siteeventticket.event-tickets',
                    'parent_content_id' => $right_container_id,
                    'order' => 1
                ));
            }
            
            $content_id = $db->select()
                ->from('engine4_core_content', 'content_id')
                ->where('page_id = ?', $page_id_1)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'siteeventticket.members-bought-ticket')
                ->limit(1)
                ->query()
                ->fetchColumn();

            if (empty($content_id)) {
                $right_container_id = $db->select()
                    ->from('engine4_core_content', 'content_id')
                    ->where('page_id = ?', $page_id_1)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'right')->limit(1)
                    ->query()
                    ->fetchColumn();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id_1,
                    'type' => 'widget',
                    'name' => 'siteeventticket.members-bought-ticket',
                    'parent_content_id' => $right_container_id,
                    'order' => 4,
                    'params' => '{"title":"Event Attendees"}'
                ));
            }            

            $select = new Zend_Db_Select($db);

            $selectContent = $select
                ->from('engine4_core_content')
                ->where('page_id = ?', $page_id_1)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'siteevent.event-status');
            $contentItem = $selectContent->query()->fetchObject();

            if ($contentItem->params == '[""]' || $contentItem->params == '') {
                $contentItem->params = '{"title":"","titleCount":true,"showButton":"1","showEventFullStatus":"0","nomobile":"0","name":"siteevent.event-status"}';
            } else {
                $contentItemData = json_decode($contentItem->params);
                $contentItemData->showEventFullStatus = 0;
                $contentItem->params = json_encode($contentItemData);
            }

            $db->query("UPDATE `engine4_core_content` SET `params` = '$contentItem->params' WHERE `content_id` = $contentItem->content_id LIMIT 1;");
        }

        //TICKET BUY PAGE
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteeventticket_ticket_buy")
            ->limit(1)
            ->query()
            ->fetchColumn();
        if (empty($page_id)) {

            $containerCount = 0;

            //CREATE PAGE
            $db->insert('engine4_core_pages', array(
                'name' => "siteeventticket_ticket_buy",
                'displayname' => 'Advanced Events - Events Booking & Tickets Selling Page',
                'title' => 'Buy Tickets for Events',
                'description' => 'Buy Tickets for Events',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN-MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.list-profile-breadcrumb',
                'parent_content_id' => $top_middle_id,
                'params' => '',
                'order' => 1
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.list-information-profile',
                'parent_content_id' => $top_middle_id,
                'params' => '{"title":"","showContent":["title","hostName","photo","featuredLabel","sponsoredLabel","newLabel","description","venueName","showrepeatinfo","startDate","endDate","location","directionLink"],"actionLinks":"0","like_button":"0","truncationDescription":"300","nomobile":"0","name":"siteevent.list-information-profile"}',
                'order' => 2
            ));

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'siteeventticket.tickets-buy',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'params' => '',
                'order' => 3,
            ));
        }

        /*
         * DASHBOARD PAGE CREATED FOR SALES REPORT
         */
        $page_id_2 = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteeventticket_report_sales-statistics")
            ->limit(1)
            ->query()
            ->fetchColumn();
        if (empty($page_id_2)) {

            $containerCount = 0;
            $widgetCount = 0;

            //CREATE PAGE
            $db->insert('engine4_core_pages', array(
                'name' => "siteeventticket_report_sales-statistics",
                'displayname' => 'Advanced Events - Event Statistics',
                'title' => 'Advanced Events - Event Statistics',
                'description' => 'This is the event sales report dashboard page.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            //INSERT MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //RIGHT CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $right_container_id = $db->lastInsertId();

            //MAIN-MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            //INSERT "STATISTICS BOX" WIDGET
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteeventticket.sales-figures',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '',
            ));

            //INSERT "LATEST ORDER" WIDGET
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteeventticket.latest-orders',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"itemCount":"5","title":"Recent Orders"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteeventticket.ticket-statistics',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Ticket Statistics"}',
            ));
        }

        //MY TICKETS PAGE
        $page_id_3 = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteeventticket_order_my-tickets")
            ->limit(1)
            ->query()
            ->fetchColumn();
        if (empty($page_id_3)) {

            $containerCount = 0;

            //CREATE PAGE
            $db->insert('engine4_core_pages', array(
                'name' => "siteeventticket_order_my-tickets",
                'displayname' => 'Advanced Events - My Tickets Page',
                'title' => 'My Tickets',
                'description' => 'My Tickets',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN-MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'siteeventticket.my-tickets',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'params' => '',
                'order' => 1,
            ));
        }

        //BUYER DETAILS PAGE
        $page_id_4 = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteeventticket_order_buyer-details")
            ->limit(1)
            ->query()
            ->fetchColumn();
        if (empty($page_id_4)) {

            $containerCount = 0;

            //CREATE PAGE
            $db->insert('engine4_core_pages', array(
                'name' => "siteeventticket_order_buyer-details",
                'displayname' => 'Advanced Events - Buyer Details Page',
                'title' => 'Buyer Details',
                'description' => 'Buyer Details',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN-MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'params' => '',
                'order' => 1,
            ));
        }

        //CHECKOUT PAGE
        $page_id_5 = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteeventticket_order_checkout")
            ->limit(1)
            ->query()
            ->fetchColumn();
        if (empty($page_id_5)) {

            $containerCount = 0;

            //CREATE PAGE
            $db->insert('engine4_core_pages', array(
                'name' => "siteeventticket_order_checkout",
                'displayname' => 'Advanced Events - Checkout Page',
                'title' => 'Checkout Page',
                'description' => 'Checkout Page',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));

            //MAIN-MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'params' => '',
                'order' => 1,
            ));
        }
        //END THE WORK FOR MAKE WIDGETIZE PAGE
        //PAID PLUGIN WORK
        //START: UPGRADE QUERIES - MOBILE COMPATIBLE 
        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='siteeventpaid';");

        //ADDING COLUMNS TO ADVANCED EVENTS TABLES FOR PAID EXTENSION
        $siteevent_status_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_events LIKE 'status'")->fetch();
        if (empty($siteevent_status_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_events` ADD `status` ENUM( 'initial', 'trial', 'pending', 'active', 'cancelled', 'expired', 'overdue', 'refunded' ) NOT NULL DEFAULT 'initial'");
        }

        $siteevent_pending_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_events LIKE 'pending'")->fetch();
        if (empty($siteevent_pending_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_events` ADD `pending` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `status`");
        }

        $siteevent_package_id_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_events LIKE 'package_id'")->fetch();
        if (empty($siteevent_package_id_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_events` ADD `package_id` INT( 11 ) NOT NULL DEFAULT '1' AFTER `event_id`");
        }

        $siteevent_expiration_date_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_events LIKE 'expiration_date'")->fetch();
        if (empty($siteevent_expiration_date_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_events` ADD `expiration_date` DATETIME NULL DEFAULT '2250-01-01 00:00:00' AFTER `modified_date`");
        }

        $siteevent_payment_date_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_events LIKE 'payment_date'")->fetch();
        if (empty($siteevent_payment_date_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_events` ADD `payment_date` DATETIME NULL DEFAULT NULL AFTER `expiration_date`");
        }

        $siteevent_gateway_id_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_otherinfo LIKE 'gateway_id'")->fetch();
        if (empty($siteevent_gateway_id_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_otherinfo` ADD `gateway_id` INT( 10 ) NULL DEFAULT NULL");
        }

        $siteevent_gateway_profile_id_column = $db->query("SHOW COLUMNS FROM engine4_siteevent_otherinfo LIKE 'gateway_profile_id'")->fetch();
        if (empty($siteevent_gateway_profile_id_column)) {
            $db->query("ALTER TABLE `engine4_siteevent_otherinfo` ADD `gateway_profile_id` VARCHAR( 128 ) NULL DEFAULT NULL");
        }

        //START MAKING WIDGETIZE PAGE 
        //CREATE PACKAGE BASED EVENT 
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteeventpaid_package_index")
            ->limit(1)
            ->query()
            ->fetchColumn();
        if (empty($page_id)) {

            $containerCount = 0;

            //CREATE PAGE
            $db->insert('engine4_core_pages', array(
                'name' => "siteeventpaid_package_index",
                'displayname' => 'Advanced Events - Packages for Events',
                'title' => 'Packages for Events',
                'description' => 'This is the Packages page for Event.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN-MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.navigation-siteevent',
                'parent_content_id' => $top_middle_id,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'siteeventpaid.list-packages',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'params' => '',
                'order' => 1,
            ));
        }

//END

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteeventticket_coupon_view")
            ->limit(1)
            ->query()
            ->fetchColumn();

        if (!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'siteeventticket_coupon_view',
                'displayname' => 'Advanced Events - Event Coupon View Page',
                'title' => 'Event Coupon View Page',
                'description' => 'This is the view page of event coupon.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '',
            ));
            $right_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 3,
                'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteeventticket.coupon-content',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '',
            ));
        }

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteeventticket_coupon_create")
            ->limit(1)
            ->query()
            ->fetchColumn();

        if (!$page_id) {

            $containerCount = 0;
            $widgetCount = 0;

            $db->insert('engine4_core_pages', array(
                'name' => "siteeventticket_coupon_create",
                'displayname' => "Advanced Events - Coupon Creation Page",
                'title' => 'Event Coupon Creation Page',
                'description' => 'This is the creation page of event coupon.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN-MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.content',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
            ));
        }

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteeventticket_coupon_edit")
            ->limit(1)
            ->query()
            ->fetchColumn();

        if (!$page_id) {

            $containerCount = 0;
            $widgetCount = 0;

            $db->insert('engine4_core_pages', array(
                'name' => "siteeventticket_coupon_edit",
                'displayname' => "Advanced Events - Coupon Edit Page",
                'title' => 'Event Coupon Edit Page',
                'description' => 'This is the edit page of event coupon.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN-MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.content',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
            ));
        }

//BROWSE COUPONS PAGE
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteeventticket_coupon_index")
            ->limit(1)
            ->query()
            ->fetchColumn();

        if (!$page_id) {

            $containerCount = 0;
            $widgetCount = 0;

            $db->insert('engine4_core_pages', array(
                'name' => "siteeventticket_coupon_index",
                'displayname' => 'Advanced Events - Browse Event Coupons',
                'title' => 'Advanced Events - Browse Event Coupons',
                'description' => "This is the event coupon's browse page.",
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //LEFT CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $left_container_id = $db->lastInsertId();

            //MAIN-MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.navigation-siteevent',
                'parent_content_id' => $top_middle_id,
                'order' => $widgetCount++,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteeventticket.search-coupons',
                'parent_content_id' => $left_container_id,
                'order' => $widgetCount++,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteeventticket.browse-coupons',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '',
            ));
        }

//START: PUT COUPONS TAB AUTOMATICALLY AT SITEEVENT PROFILE PAGE
        $select = new Zend_Db_Select($db);

        $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'siteevent_index_view')
            ->limit(1);
        $page_id = $select->query()->fetchObject()->page_id;
        if (!empty($page_id)) {

            $select = new Zend_Db_Select($db);
            $select_content = $select
                ->from('engine4_core_content')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'siteeventticket.event-profile-coupons')
                ->limit(1);
            $content = $select_content->query()->fetchObject();
            $content_id = !empty($content) ? $content->content_id : 0;
            if (empty($content_id)) {
                $select = new Zend_Db_Select($db);
                $select_container = $select
                    ->from('engine4_core_content')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
                $container_id = $select_container->query()->fetchObject()->content_id;
                if (!empty($container_id)) {
                    $select = new Zend_Db_Select($db);
                    $select_middle = $select
                        ->from('engine4_core_content')
                        ->where('parent_content_id = ?', $container_id)
                        ->where('type = ?', 'container')
                        ->where('name = ?', 'middle')
                        ->limit(1);
                    $middle_id = $select_middle->query()->fetchObject()->content_id;
                    if (!empty($middle_id)) {
                        $select = new Zend_Db_Select($db);
                        $select_tab = $select
                            ->from('engine4_core_content')
                            ->where('type = ?', 'widget')
                            ->where('name = ?', 'core.container-tabs')
                            ->where('page_id = ?', $page_id)
                            ->limit(1);
                        $tab_id = $select_tab->query()->fetchObject()->content_id;

                        $db->insert('engine4_core_content', array(
                            'page_id' => $page_id,
                            'type' => 'widget',
                            'name' => 'siteeventticket.event-profile-coupons',
                            'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
                            'order' => 999,
                            'params' => '{"title":"Coupons","titleCount":true,"loaded_by_ajax":"1","statistics":["startdate","enddate","couponcode","discount","expire"],"itemCount":"10","truncation":"64","nomobile":"0","name":"siteeventticket.event-profile-coupons"}',
                        ));
                    }
                }
            }

            $select = new Zend_Db_Select($db);
            $select_content = $select
                ->from('engine4_core_content')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'siteeventticket.terms-of-use')
                ->limit(1);
            $content = $select_content->query()->fetchObject();
            $content_id = !empty($content) ? $content->content_id : 0;
            if (empty($content_id)) {
                $select = new Zend_Db_Select($db);
                $select_container = $select
                    ->from('engine4_core_content')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
                $container_id = $select_container->query()->fetchObject()->content_id;
                if (!empty($container_id)) {
                    $select = new Zend_Db_Select($db);
                    $select_middle = $select
                        ->from('engine4_core_content')
                        ->where('parent_content_id = ?', $container_id)
                        ->where('type = ?', 'container')
                        ->where('name = ?', 'middle')
                        ->limit(1);
                    $middle_id = $select_middle->query()->fetchObject()->content_id;
                    if (!empty($middle_id)) {
                        $select = new Zend_Db_Select($db);
                        $select_tab = $select
                            ->from('engine4_core_content')
                            ->where('type = ?', 'widget')
                            ->where('name = ?', 'core.container-tabs')
                            ->where('page_id = ?', $page_id)
                            ->limit(1);
                        $tab_id = $select_tab->query()->fetchObject()->content_id;

                        $db->insert('engine4_core_content', array(
                            'page_id' => $page_id,
                            'type' => 'widget',
                            'name' => 'siteeventticket.terms-of-use',
                            'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
                            'order' => 999,
                            'params' => '{"title":"Terms & Conditions","titleCount":true}',
                        ));
                    }
                }
            }
        }
//END: PUT COUPONS TAB AUTOMATICALLY AT SITEEVENT PROFILE PAGE

        $db->update('engine4_siteevent_event_fields_meta', array('label' => 'Tickets (Custom Field)'), array('field_id = ?' => 22, 'label = ?' => 'Tickets'));

        $db->delete('engine4_activity_notificationtypes', array('module = ?' => 'siteevent', 'type IN (?)' => array('siteevent_accepted', 'siteevent_confirm_guests', 'siteevent_join', 'siteevent_member', 'siteevent_reject_guests', 'siteevent_request_disapprove', 'siteevent_rsvp_change')));

        $db->update('engine4_activity_actiontypes', array('enabled' => 0), array('module = ?' => 'siteevent', 'type IN (?)' => array('siteevent_join', 'siteevent_leave', 'siteevent_maybe_join', 'siteevent_mid_join', 'siteevent_mid_leave', 'siteevent_mid_maybe')));

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteeventticket_order_view")
            ->limit(1)
            ->query()
            ->fetchColumn();

        if (!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'siteeventticket_order_view',
                'displayname' => 'Advanced Events - View Ticket Orders',
                'title' => 'Orders View Page',
                'description' => 'This is events tickets order view page.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.content',
                'parent_content_id' => $middle_id,
                'order' => 3,
                'params' => '',
            ));
        }

//START MIGRATION RELATED QURIES
        $max_event_id = $db->query("SELECT MAX(engine4_siteevent_events.event_id) as event_id FROM `engine4_siteevent_events` WHERE draft = 0 LIMIT 1")->fetchColumn();
        if (!empty($max_event_id)) {
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("siteeventticket_admin_main_migrate", "siteeventticket", "Migrate RSVP Events", "", \'{"route":"admin_default","module":"siteeventticket","controller":"migrate","action":"index"}\', "siteeventticket_admin_main_ticket", "", 1, 0, 999);');
            $db->query("DELETE FROM `engine4_core_settings` WHERE `engine4_core_settings`.`name` = 'siteeventticket.lasteventid'");
            $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('siteeventticket.lasteventid', $max_event_id)");
        }
//END MIGRATION RELATED QURIES

        $name = $db->select()
            ->from('engine4_core_menuitems', 'name')
            ->where('name = ?', "siteeventticket_admin_main_ticketfaqs")
            ->limit(1)
            ->query()
            ->fetchColumn();
        if(!$name) {
            $db->insert('engine4_core_menuitems', array(
                'name' => 'siteeventticket_admin_main_ticketfaqs',
                'module' => 'siteeventticket',
                'label' => 'Tickets FAQs',
                'plugin' => 'Siteeventticket_Plugin_Menus::ticketFaqs',
                'params' => '{"route":"admin_default","module":"siteevent","controller":"settings","action":"faq"}',
                'menu' => 'siteeventticket_admin_main_ticket',
                'submenu' => '',
                'enabled' => 1,
                'custom' => 0,
                'order' => 1111
            ));
        }
        $siteevent_otherinfo_ticket_type_column = $db->query("SHOW COLUMNS FROM engine4_siteeventpaid_packages LIKE 'ticket_type'")->fetch();
        if (empty($siteevent_otherinfo_ticket_type_column)) {
        $db->query("ALTER TABLE `engine4_siteeventpaid_packages` ADD `ticket_type` TINYINT(1) NOT NULL DEFAULT '0';");
        }

//ORDER SUCCESS PAGE
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteeventticket_order_success")
            ->limit(1)
            ->query()
            ->fetchColumn();
        if (empty($page_id)) {

            $containerCount = 0;

            //CREATE PAGE
            $db->insert('engine4_core_pages', array(
                'name' => "siteeventticket_order_success",
                'displayname' => 'Advanced Events - Order Success Page',
                'title' => 'Order Success Page',
                'description' => 'Order Success Page',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN-MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'params' => '',
                'order' => 1,
            ));
        }
        
        //UPDATE MENU
        $db->update('engine4_core_menuitems', array('params' => '{"route":"siteevent_package"}'), array('name = ?' => 'siteevent_main_create', 'module = ?' => "siteevent", "menu = ?" => "siteevent_main"));
        $db->update('engine4_core_menuitems', array('params' => '{"route":"siteevent_package"}'), array('name = ?' => 'siteevent_quick_create', 'module = ?' => "siteevent", "menu = ?" => "siteevent_quick"));       
        
        $db->update('engine4_core_menuitems', array('menu' => 'siteevent_dashboard_ticket', 'order' => 145), array('name = ?' => 'siteevent_dashboard_waitlist'));        
        
        $page_id_1 = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "siteevent_index_view")
            ->limit(1)
            ->query()
            ->fetchColumn();
        if ($page_id_1) {
            $db->delete('engine4_core_content', array('name = ?' => 'siteeventrepeat.occurrences', 'page_id = ?' => $page_id_1));
            $db->delete('engine4_core_content', array('name = ?' => 'siteevent.profile-members', 'page_id = ?' => $page_id_1));
        }
        
    }

}
