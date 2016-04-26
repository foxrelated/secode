<?php
class Mp3music_AdminImportController extends Core_Controller_Action_Admin
{
   protected $_paginate_params = array();
   public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mp3music_admin_main', array(), 'mp3music_admin_main_import');
  }
  public function indexAction()
  {
      if ($_POST)
         {
             $configFile = APPLICATION_PATH . '/application/settings/database.php';
            $options = include $configFile;
            $db =  $options['params'];
            $connection = mysql_connect($db['host'], $db['username'], $db['password']);
            if (!$connection)
                die("can't connect server");
            $db_selected = mysql_select_db($db['dbname']);
            if (!$db_selected)
                die ("have not database");
            $sql = "ALTER TABLE `engine4_mp3music_albums` ADD `price` DECIMAL( 11, 2 ) NOT NULL DEFAULT '0.00' AFTER `is_download` ,
                ADD `is_delete` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `price`, 
                ADD `type` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `is_delete`; ";
            mysql_query($sql,$connection);          
            $sql ="ALTER TABLE `engine4_mp3music_album_songs` ADD `price` DECIMAL( 11, 2 ) NOT NULL DEFAULT '0.00' AFTER `download_count` ,
                ADD `is_delete` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `price`;";
            mysql_query($sql,$connection);          
            $sql ="CREATE TABLE IF NOT EXISTS `engine4_mp3music_bills` (
                  `bill_id` int(11) NOT NULL AUTO_INCREMENT,
                  `invoice` varchar(70) NOT NULL,
                  `sercurity` varchar(100) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  `finance_account_id` int(11) DEFAULT NULL,
                  `emal_receiver` varchar(255) NOT NULL,
                  `payment_receiver_id` int(11) NOT NULL,
                  `date_bill` int(11) NOT NULL,
                  `bill_status` int(3) NOT NULL DEFAULT '0',
                  `params` text CHARACTER SET utf8 NOT NULL,
                  PRIMARY KEY (`bill_id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=214 ;";
                mysql_query($sql,$connection);          
            $sql ="CREATE TABLE IF NOT EXISTS `engine4_mp3music_coupons` (
                  `coupon_id` int(11) NOT NULL AUTO_INCREMENT,
                  `coupon_code` varchar(55) NOT NULL,
                  `coupon_value` float NOT NULL,
                  `start_date` int(11) DEFAULT NULL,
                  `end_date` int(11) DEFAULT NULL,
                  `coupon_status` int(11) NOT NULL DEFAULT '1',
                  PRIMARY KEY (`coupon_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

                mysql_query($sql,$connection);          
            $sql ="CREATE TABLE IF NOT EXISTS `engine4_mp3music_gateways` (
                  `gateway_id` int(11) NOT NULL AUTO_INCREMENT,
                  `gateway_name` varchar(70) NOT NULL,
                  `admin_account` varchar(255) DEFAULT NULL,
                  `is_active` int(11) NOT NULL DEFAULT '0',
                  `params` text NOT NULL,
                  PRIMARY KEY (`gateway_id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;";

                mysql_query($sql,$connection);          
            $sql ="CREATE TABLE IF NOT EXISTS `engine4_mp3music_lists` (
                  `list_id` int(11) NOT NULL AUTO_INCREMENT,
                  `dl_song_id` int(11) NOT NULL,
                  `dl_album_id` int(11) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  PRIMARY KEY (`list_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=88 ;";
                mysql_query($sql,$connection);          
            $sql ="CREATE TABLE IF NOT EXISTS `engine4_mp3music_payment_accounts` (
                  `paymentaccount_id` int(11) NOT NULL AUTO_INCREMENT,
                  `account_username` varchar(255) DEFAULT NULL,
                  `account_password` varchar(255) DEFAULT NULL,
                  `user_id` int(6) DEFAULT NULL,
                  `payment_type` int(11) NOT NULL,
                  `is_save_password` tinyint(4) DEFAULT '0',
                  `total_amount` decimal(11,2) DEFAULT NULL,
                  `last_check_out` bigint(11) DEFAULT NULL,
                  `account_status` int(11) NOT NULL DEFAULT '1',
                  PRIMARY KEY (`paymentaccount_id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Store account payment.' AUTO_INCREMENT=7 ;";

                mysql_query($sql,$connection);          
            $sql ="CREATE TABLE IF NOT EXISTS `engine4_mp3music_payment_requests` (
                  `paymentrequest_id` int(11) NOT NULL AUTO_INCREMENT,
                  `request_user_id` int(11) DEFAULT NULL,
                  `request_payment_acount_id` int(11) DEFAULT NULL,
                  `request_amount` decimal(11,2) DEFAULT NULL,
                  `request_status` int(11) DEFAULT NULL,
                  `request_reason` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                  `request_answer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                  `request_date` int(11) NOT NULL,
                  PRIMARY KEY (`paymentrequest_id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='engine4_mp3music_payment_request' AUTO_INCREMENT=8 ;";
                mysql_query($sql,$connection);          
            $sql ="CREATE TABLE IF NOT EXISTS `engine4_mp3music_transaction_trackings` (
                  `transactiontracking_id` int(11) NOT NULL AUTO_INCREMENT,
                  `transaction_date` bigint(11) DEFAULT NULL,
                  `user_seller` int(11) DEFAULT NULL,
                  `user_buyer` int(11) DEFAULT NULL,
                  `item_id` int(11) DEFAULT NULL,
                  `item_type` varchar(45) DEFAULT NULL,
                  `amount` decimal(11,2) DEFAULT NULL,
                  `account_seller_id` int(11) DEFAULT NULL,
                  `account_buyer_id` int(11) DEFAULT NULL,
                  `transaction_status` int(11) DEFAULT NULL,
                  `params` text NOT NULL,
                  PRIMARY KEY (`transactiontracking_id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='engine4_mp3music_transaction_tracking' AUTO_INCREMENT=77 ;";

                mysql_query($sql,$connection);          
            $sql ="CREATE TABLE IF NOT EXISTS `engine4_mp3music_selling_historys` (
                  `sellinghistory_id` int(11) NOT NULL AUTO_INCREMENT,
                  `selling_datetime` bigint(11) DEFAULT NULL,
                  `selling_total_upload_songs` int(11) DEFAULT NULL,
                  `selling_total_download_songs` int(11) DEFAULT NULL,
                  `selling_sold_songs` int(11) DEFAULT NULL,
                  `selling_sold_albums` int(11) DEFAULT NULL,
                  `selling_final_new_account` int(11) DEFAULT NULL,
                  `selling_transaction_succ` int(11) DEFAULT NULL,
                  `selling_transaction_fail` int(11) DEFAULT NULL,
                  `selling_total_amount` decimal(11,2) DEFAULT NULL,
                  `params` text NOT NULL,
                  PRIMARY KEY (`sellinghistory_id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='engine4_mp3music_selling_history' AUTO_INCREMENT=4 ;";


                mysql_query($sql,$connection);          
            $sql ="CREATE TABLE IF NOT EXISTS `engine4_mp3music_selling_settings` (
                  `sellingsetting_id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_group_id` int(11) NOT NULL,
                  `module_id` varchar(25) NOT NULL,
                  `name` varchar(33) NOT NULL,
                  `default_value` text NOT NULL,
                  `params` text,
                  PRIMARY KEY (`sellingsetting_id`,`user_group_id`,`name`,`module_id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=370 ; ";

                mysql_query($sql,$connection);          
            $sql ="INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
                ('mp3music_admin_main_ssettings', 'mp3music', 'Selling Settings', '', '{\"route\":\"admin_default\",\"module\":\"mp3-music\",\"controller\":\"ssettings\"}', 'mp3music_admin_main', '', 8),
                ('mp3music_admin_main_sstatistics', 'mp3music', 'Selling Statistics', '', '{\"route\":\"admin_default\",\"module\":\"mp3-music\",\"controller\":\"sstatistics\"}', 'mp3music_admin_main', '', 9),
                ('mp3music_admin_main_managefinance', 'mp3music', 'Manage Finance', '', '{\"route\":\"admin_default\",\"module\":\"mp3-music\",\"controller\":\"managefinance\"}', 'mp3music_admin_main', '', 10),
                ('mp3music_admin_main_manageaccounts', 'mp3music', 'Manage Accounts', '', '{\"route\":\"admin_default\",\"module\":\"mp3-music\",\"controller\":\"manageaccounts\"}', 'mp3music_admin_main', '', 11),
                ('mp3music_admin_main_managegateway', 'mp3music', 'Manage Gateway', '', '{\"route\":\"admin_default\",\"module\":\"mp3-music\",\"controller\":\"managegateway\"}', 'mp3music_admin_main', '', 12);";
                mysql_query($sql,$connection);          
            $sql ="INSERT INTO `engine4_mp3music_selling_settings` (`sellingsetting_id`,`user_group_id`, `module_id`, `name`, `default_value`, `params`) VALUES
                ( 1,5, 'mp3music', 'min_payout', '30', NULL),
                ( 2,5, 'mp3music', 'max_payout', '100', NULL),
                ( 3,5, 'mp3music', 'can_buy_song', '0', NULL),
                ( 4,5, 'mp3music', 'can_sell_song', '0', NULL),
                ( 5,5, 'mp3music', 'comission_fee', '0', NULL),
                ( 6,4, 'mp3music', 'who_payment', '3', NULL),
                ( 7,4, 'mp3music', 'comission_fee', '0', NULL),
                ( 8,4, 'mp3music', 'min_price_song', '0', NULL),
                ( 9,4, 'mp3music', 'min_price_album', '0', NULL),
                ( 10,4, 'mp3music', 'min_payout', '30', NULL),
                ( 11,4, 'mp3music', 'max_payout', '-1', NULL),
                ( 12,4, 'mp3music', 'can_buy_song', '1', NULL),
                ( 13,4, 'mp3music', 'can_sell_song', '1', NULL),
                ( 14,3, 'mp3music', 'min_price_song', '0', NULL),
                ( 15,3, 'mp3music', 'min_price_album', '0', NULL),
                ( 16,3, 'mp3music', 'min_payout', '300', NULL),
                ( 17,3, 'mp3music', 'max_payout', '1000', NULL),
                ( 18,3, 'mp3music', 'can_buy_song', '0', NULL),
                ( 19,3, 'mp3music', 'can_sell_song', '1', NULL),
                ( 20,3, 'mp3music', 'method_payment', '1', NULL),
                ( 21,3, 'mp3music', 'comission_fee', '0', NULL),
                ( 22,3, 'mp3music', 'who_payment', '3', NULL),
                ( 23,2, 'mp3music', 'who_payment', '1', NULL),
                ( 24,2, 'mp3music', 'comission_fee', '11', NULL),
                ( 25,2, 'mp3music', 'min_price_song', '0', NULL),
                ( 26,2, 'mp3music', 'min_price_album', '0', NULL),
                ( 27,2, 'mp3music', 'max_payout', '-1', NULL),
                ( 28,2, 'mp3music', 'can_sell_song', '1', NULL),
                ( 29,2, 'mp3music', 'min_payout', '11', NULL),
                ( 30,2, 'mp3music', 'can_buy_song', '1', NULL),
                ( 31,2, 'mp3music', 'method_payment', '3', NULL),
                ( 32,1, 'mp3music', 'who_payment', '3', NULL),
                ( 33,1, 'mp3music', 'comission_fee', '6.66', NULL),
                ( 34,1, 'mp3music', 'min_price_song', '3', NULL),
                ( 35,1, 'mp3music', 'min_price_album', '3', NULL),
                ( 36,1, 'mp3music', 'min_payout', '6', NULL),
                ( 37,1, 'mp3music', 'max_payout', '-1', NULL),
                ( 38,1, 'mp3music', 'can_buy_song', '1', NULL),
                ( 39,1, 'mp3music', 'can_sell_song', '1', NULL),
                ( 40,1, 'mp3music', 'method_payment', '1', NULL),
                ( 41,0, 'mp3music', 'is_test_mode', '1', NULL),
                ( 42,0, 'mp3music', 'policy_message', '', NULL),
                ( 43,0, 'mp3music', 'policy_message_request', '', NULL),
                ( 44,0, 'mp3music', 'upload_message', '', NULL);";
                
                mysql_query($sql,$connection);
                
                $sql = "ALTER TABLE `engine4_mp3music_album_songs` ADD `artist_id` INT( 11 ) NOT NULL DEFAULT '0' AFTER `other_singer_title_url`";
                mysql_query($sql,$connection); 
                
                mysql_close($connection);
               $this->view->message = 'Import database successfull, please refresh page!';
         }
  }
}