<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Autobackupsettings.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_Form_Admin_Autobackupsettings extends Engine_Form {

  public function init() {
    //HERE WE SETTING THE TITLE AND DESCRIPTION OF DATABASE BACKUP
    $this
        ->setTitle('Backup Settings')
        ->setDescription("Here, you can configure the settings for automatic backup of your site's database. You can also configure settings for the number of backup files to be kept on your server.");

   	$this->addElement('Text', 'dbbackup_controllersettings', array(
      'label' => 'Enter License key',
      'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('dbbackup.controllersettings'),
    ));

      if( APPLICATION_ENV == 'production' ) {
			$this->addElement('Checkbox', 'environment_mode', array(
				'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
				'description' => 'System Mode',
				'value' => 1,
			)); 
		}else {
			$this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
		}

		$this->addElement('Button', 'submit_lsetting', array(
			'label' => 'Activate Your Plugin Now',
			'type' => 'submit',
			'ignore' => true
		));


    //HERE WE CREATE THE AUTOMATIC  DROPDOWN OF DELETE FILE
    $this->addElement('Radio', 'dbbackup_deleteoptions', array(
            'label' => 'Automatic Delete',
            'description' => "Please choose options you want to automatic delete backup files.",
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_deleteoptions,
            'onclick' => 'showdeleteblock(this.value);',
    ));


    $this->addElement('Text', 'dbbackup_deletelimit', array(
            'label' => 'Maximum Database Backups saved on Server',
            'description' => "Specify the maximum number of database backup files to be kept in your server's backup directory before deleting old files. (The recent backups will be kept, and the older ones will be deleted. This prevents unnecessary space utilization on your server. The minimum value for this is 1.)",
            'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_deletelimit,
            'required' => true,
    ));
    //HERE WE CREATE THE AUTOMATIC  DROPDOWN OF DELETE FILE
    $this->addElement('Radio', 'dbbackup_deleteoptions', array(
            'label' => 'Automatically Delete Database Backups',
            'description' => "Do you want database backup files to be automatically deleted from the backup directory on your server ? (Choosing yes will allow you to specify the maximum number of database backups that should be saved on your server. The recent backups will be kept, and the older ones will be deleted. This prevents unnecessary space utilization on your server.)",
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_deleteoptions,
            'onclick' => 'showdeleteblock(this.value);',
    ));


    //HERE WE CREATE THE AUTOMATIC  DROPDOWN OF DELETE FILE
    $this->addElement('Radio', 'dbbackup_deletecodeoptions', array(
            'label' => "Automatically Delete Site's Files' Backups",
            'description' => "Do you want your site's file backups to be automatically deleted from the backup directory on your server ? (Choosing yes will allow you to specify the maximum number of file backups that should be saved on your server. The recent backups will be kept, and the older ones will be deleted. This prevents unnecessary space utilization on your server.)",
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_deletecodeoptions,
            'onclick' => 'showdeletecodeblock(this.value);',
    ));

    $this->addElement('Text', 'dbbackup_deletecodelimit', array(
            'label' => 'Maximum File Backups saved on Server',
            'description' => "Specify the maximum number of site's file backups to be kept in your server's backup directory before deleting old files. (The recent backups will be kept, and the older ones will be deleted. This prevents unnecessary space utilization on your server. The minimum value for this is 1.)",
            'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_deletecodelimit,
            'required' => true,
    ));


    //HERE WE CREATE THE AUTOMATIC  DROPDOWN OF DATABASE BACKUP FILE OR SIMPLE FILE
    $this->addElement('Radio', 'dbbackup_backupoptions', array(
            'label' => 'Automatic Database Backup',
            'description' => "Do you want Automatic Database Backup to be activated on your site ? (If yes, then you will be able to choose more settings below. Note that automatic database backup is dependent on the activity on your site. If your site is dormant, with no activity, then the backup for your site will not occur till there is activity on it.)",
            'multiOptions' => array(
                    0 => 'Yes',
                    1 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_backupoptions,
            'onclick' => 'showautomaticblock(this.value);',
    ));

    //HERE WE CREATE THE OPTIONS WHERE YOU WANT TO STORE THE BACKUP FILE
    /*    $this->addElement('Radio', 'dbbackup_backuptype', array(
      'label' => 'Automatic Backup Type',
      'multiOptions' => array(
      0 => "Backup both database, and site's files.",
      1 => 'Backup only the database.'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_backuptype,
      'onclick' => 'showdatabaseblock(this.value);',
      )); */

    //Here we attaching the filename with the backup genereted file.
    $this
        ->addElement('Text', 'dbbackup_autofilename', array(
                'label' => 'Automatic Database Backup Filename Prefix',
                'description' => 'Specify the prefix for the name of the automatic database backup file. (The prefix specified by you will be appended by the backup timestamp.)',
                'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_autofilename,
                'required' => true,
        ));
        
		$destination_prepared = array();
    $table = Engine_Api::_()->getDbtable('destinations', 'dbbackup');
    $select_query = $table->select()
            ->where('destination_mode <> ?', 2)
            ->orWhere('destination_mode = 2 and ftpadb= 1')
            ->order('destinations_id DESC');
    $results = $table->fetchAll($select_query);
    foreach ($results as $item) {
      $destination_prepared[$item->destinations_id] = $item->destinationname;
    }
    //Destinations
    $this->addElement('Select', 'dbbackup_destinations', array(
            'label' => 'Destination for Automatic Database Backups',
            'description' => 'Select a destination for the automatic database backups of your site. (You may create a new destination from Destinations section.)',
            'multiOptions' => $destination_prepared,
            'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_destinations,
    ));


    /*    $table = Engine_Api::_()->getDbtable('destinations', 'dbbackup');
      $select_query_ftp = $table->select()
      ->where('destination_mode = ?', 0)
      ->orWhere('destination_mode = 2 and ftpafile= 1')
      ->order('destinations_id DESC');
      $results_ftp = $table->fetchAll($select_query_ftp);
      foreach ($results_ftp as $itemftp) {
      $destination_ftp[$itemftp->destinations_id] = $itemftp->destinationname;
      }
      //Destinations
      $this->addElement('Select', 'dbbackup_locationoptions', array(
      'label' => 'Destination for Automatic File Backups',
      'multiOptions' => $destination_ftp,
      'description' => 'Select a destination for the automatic file backups of your site. (You may create a new destination from Destinations section.)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_locationoptions,
      ));

     */
    //HERE WE CREATE THE AUTOMATIC  DROPDOWN FOR DATABASE BACKUP FILE
    $this->addElement('Select', 'dbbackup_dropdowntime', array(
            'label' => 'Time Interval between Database Backups',
            'description' => " Select the time intervals between successive database backups. A new backup process will start after this time interval from the previous backup. (Note: The time interval selected by you should be more than 5 times the duration taken for a manual backup on your site. You may take a manual backup in Take Backup section for finding its time duration. For example, if your site's manual backup takes 4 hours to execute, then the time interval chosen by you here must be greater than 20 hours. Please refer to the FAQ section for a better understanding.)",
            'multiOptions' => array(
                    21600 => '6 Hours',
                    43200 => '12 Hours',
                    64800 => '18 Hours',
                    86400 => '1 Day',
                    172800 => '2 Days',
                    259200 => '3 Days',
                    608400 => '1 Week',
                    1216800 => '2 Weeks',
                    1825200 => '3 Weeks',
                    2520000 => '1 Month',
                    7560000 => '3 Months',
                    15120000 => '6 Months',
                    30240000 => '1 Year',
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_dropdowntime,
    ));

    /* 	    $this->addElement('Checkbox', 'dbbackup_includedirectory', array(
      'label' => 'If you want to include the all backup files of backup server directory into the new generated backup file then select this option.',
      'description' => 'Include server backup directory in case of automatic backup',
      'multiOptions' => array(
      'yes' => 'Yes',
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_includedirectory
      )); */

    //******
    //HERE WE SEND MAIL  FOR AUTOMATIC  BACKUP OPTION
    $this->addElement('Radio', 'dbbackup_mailoption', array(
            'label' => 'Email Notification on completion of Automatic Database Backups',
            'description' => "Do you want email notifications to be sent to you for the completion of automatic database backups ?",
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailoption,
            'onclick' => 'showmailblock(this.value);',
    ));


    $this->addElement('Text', 'dbbackup_mailsender', array(
            'label' => 'Email(s) for Notifications',
            'description' => 'Specify the email(s) for receiving automatic backup notifications. (Separate multiple emails by commas.)',
            'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailsender,
        // 'required' => true,
    ));

    //******
    // Add submit button
    $this->addElement('Button', 'submit', array(
            'label' => 'Submit',
            'type' => 'submit',
            'value' => 'submit',
            'ignore' => true,
    ));
  }

}