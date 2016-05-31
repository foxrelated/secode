<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: AdminSettingsController.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_AdminAutobackupsettingsController extends Core_Controller_Action_Admin {

  public function indexAction() {
		if( !empty($_POST['dbbackup_controllersettings']) ) { $_POST['dbbackup_controllersettings'] = trim($_POST['dbbackup_controllersettings']); }
		$backup_form_content = array('dbbackup_deleteoptions', 'dbbackup_deletelimit', 'dbbackup_deleteoptions', 'dbbackup_deletecodeoptions', 'dbbackup_deletecodelimit', 'dbbackup_backupoptions', 'dbbackup_autofilename', 'dbbackup_destinations', 'dbbackup_dropdowntime', 'dbbackup_mailoption', 'dbbackup_mailsender', 'submit');
    include_once APPLICATION_PATH . '/application/modules/Dbbackup/Api/Core.php';
    include_once(APPLICATION_PATH ."/application/modules/Dbbackup/controllers/license/license1.php");   
  }

}
?>