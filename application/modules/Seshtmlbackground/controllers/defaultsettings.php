<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Seshtmlbackground
 * @package    Seshtmlbackground
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: defaultsettings.php 2015-02-20 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
$db = Zend_Db_Table_Abstract::getDefaultAdapter();
$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`,`params`, `menu`, `submenu`, `order`) VALUES ("seshtmkbackground_admin_main_utility", "seshtmlbackground", "Utilities", "", \'{"route":"admin_default","module":"seshtmlbackground","controller":"settings","action":"utility"}\', "seshtmlbackground_admin_main", "", 2);');
$db->query('INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `enabled`, `multi`, `priority`) VALUES ("Ses Html Background Encode", "seshtmlbackground_video_encode", "seshtmlbackground", "Seshtmlbackground_Plugin_Job_Encode", 1, 2, 85);');
$db->query('ALTER TABLE `engine4_seshtmlbackground_slides`  ADD `status` ENUM("1","2","3") NOT NULL DEFAULT "1";');
$db->query('ALTER TABLE `engine4_seshtmlbackground_slides` ADD `extra_button_linkopen` TINYINT(1) NOT NULL DEFAULT "0";');



