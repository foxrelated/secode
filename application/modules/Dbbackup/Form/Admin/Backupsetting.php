<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Backupsetting.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_Form_Admin_Backupsetting extends Engine_Form {

  public function init() 
  {
			$base = Zend_Controller_Front::getInstance()->getBaseUrl();

	    $table = Engine_Api::_()->getDbtable('settings', 'dbbackup');
	    $select = $table->select();
	    $row = $table->fetchAll($select);
	    
			if(!empty($row)) {
		    foreach ($row as $key => $value) {
		      $field_name[$value->name] = $value->value;
		    }
		    $backup_completecode = $field_name['backup_completecode'];
		    $dbbackup_filename = $field_name['dbbackup_filename'];
		    $dbbackupmaintenance_mode = $field_name['dbbackupmaintenance_mode'];
		    $dbbackup_tablelock = $field_name['dbbackup_tablelock'];
		    $backup_options = $field_name['backup_options'];
		    $destination_id = $field_name['destination_id'];
		    $backup_tables = $field_name['backup_tables'];
		    $backup_optionsettings = $field_name['backup_optionsettings'];
		    $backupfiles = $field_name['backup_files'];
		    $backuprootfiles = $field_name['backup_rootfiles'];
		    $backupmodulefiles = $field_name['backup_modulesfiles'];;
			} 
			else {
		    $backup_completecode = 2;
		    $dbbackup_filename = 'backup';
		    $dbbackupmaintenance_mode = 0;
		    $dbbackup_tablelock = 0;
		    $backup_options = 0;
		    $backup_tables = 1;
		    $destination_id = 0;
		    $backup_optionsettings = 0;
		    $backupfiles = 1;
		    $backuprootfiles = 1;
		    $backupmodulefiles = 1;
			}
			// Get settings
			$global_settings_file = APPLICATION_PATH . '/application/settings/general.php';
			if (file_exists($global_settings_file)) {
				$generalConfig = include $global_settings_file;
			} else {
				$generalConfig = array();
			}
      if (!empty($generalConfig['maintenance']['enabled']) && !empty($generalConfig['maintenance']['code'])) {
        $maintenance_mode = 0;
      } else {
        $maintenance_mode = 1;
      }
    //GET DECORATORS
    $this->loadDefaultDecorators();

    //GET DESCRIPTION
    $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Take a backup of your site's database and files after filling the form below.<br/>NOTE: Below are the settings for the Manual Back-Up taken by you (site admin) of the files or database of the site after clicking on the
\"Backup Now!\" button. While if you need to to activate the \"Automatic Database Backup\" (for Database Only), you can do so from the \"Back-Up Settings\" section."));

	    //HERE WE SETTING THE TITLE AND DESCRIPTION OF DATABASE BACKU
	    $this
        ->setTitle('Take Backup')
        ->setDescription("$description");

			$this->getDecorator('Description')->setOption('escape', false);

	    //HERE WE CREATE THE OPTIONS FOR BACKUP OF (DATABASE and CODE)
	    $this
	    	->addElement('Radio', 'backup_completecode', array(
	        'label' => 'Backup Type',
	        'description' => "Select the type of backup that you want to take of your site.",
	        'multiOptions' => array(
	            2 => 'Backup both database and files.',
	            1 => 'Backup database only.',
	            0 => 'Backup files only.',
	        ),
	        'value' => $backup_completecode,
	        'onclick' => 'showlocation(this.value)'
	    ));
			
	    //Here we attaching the filename with the backup genereted file.
	    $this
	    	->addElement('Text', 'dbbackup_filename', array(
	        'label' => 'Backup Filename Prefix',
	        'description' => 'Specify the prefix for the name of the backup file. (The prefix specified by you will be appended by the backup timestamp.)',
	        'value' => $dbbackup_filename,
	        'required' => true,
	    ));
	
	    //HERE WE CREATE THE OPTIONS WHERE YOU WANT TO STORE THE BACKUP FILE
      if(!empty($maintenance_mode))
			$this
				->addElement('Radio', 'dbbackupmaintenance_mode', array(
	        'label' => 'Activate Maintenance Mode',
	        'description' => 'Do you want the site to be in Maintenance Mode during the backup ? (Putting the site in Maintenance Mode will prevent site visitors from accessing your website. You can customize the maintenance mode page by manually editing the file "/application/maintenance.html". The site will be taken back online once backup is complete.)',
	        'required' => true,
	        'multiOptions' => array(
	            1 => 'Yes, take the site offline (Activate Maintenance Mode).',
	            0 => 'No, keep the site online.',
	        ),
	        'value' => $dbbackupmaintenance_mode
	    ));
	
			//HERE WE CREATE THE OPTIONS WHERE YOU WANT TO STORE THE BACKUP FILE
	    $this
	      ->addElement('Radio', 'dbbackup_tablelock', array(
	        'label' => 'Lock Tables during Database Backup',
	        'description' => "Do you want to lock your site's database tables during the backup? (Note: Locking your site's database tables during backup can help in keeping data consistency, but it will also make your site unresponsive during the backup process.)",
	        'multiOptions' => array(
	            1 => 'Yes',
	            0 => 'No'
	        ),
	        'value' => $dbbackup_tablelock,
	    ));
	
	
			$destination_ftp = array('0' => 'Download');
		  $table = Engine_Api::_()->getDbtable('destinations', 'dbbackup');
			$select_query_ftp = $table->select()
										->where('destination_mode = 2 and ftpmfile= 1')
										->orWhere('destination_mode = ?', 0)
			              ->order('destinations_id DESC');
			$results_ftp = $table->fetchAll($select_query_ftp);
	
	    foreach ($results_ftp as $itemftp) {
	      $destination_ftp[$itemftp->destinations_id] = $itemftp->destinationname;
	    }
	    
	    $this
	    	->addElement('Select', 'backup_options', array(
	        'label' => 'Destination for Files Backup',
	        'multiOptions' => $destination_ftp,
	        'description' => 'Select a destination for the file backups of your site. (You may create a new destination from Destinations section.)',	        
	        'value' =>$backup_options
	    ));
	
			$destination_prepared = array('0' => 'Download');
			$table = Engine_Api::_()->getDbtable('destinations', 'dbbackup');
			$select_query = $table->select()
                      	->where('destination_mode <> ?', 2)
	    								->orWhere('destination_mode = 2 and ftpmdb= 1')
	                    ->order('destinations_id DESC');
			$results = $table->fetchAll($select_query);
			foreach ($results as $item) {
	      $destination_prepared[$item->destinations_id] = $item->destinationname;
	    }
			$this->addElement('Select', 'destination_id', array(
	        'label' => 'Destination for Database Backup',
	        'multiOptions' => $destination_prepared,
	        'description' => 'Select a destination for the database backups of your site. (You may create a new destination from Destinations section.)',	        
	        'value' =>$destination_id
	    ));
	
/*	    $this->addElement('Checkbox', 'backup_includedirectory', array(
	        'label' => 'If you want to include the all backup files of backup server directory into the new generated backup file then select this option.',
	        'description' => 'Include server backup directory',
	        'multiOptions' => array(
	            'yes' => 'Yes',
	        ),
	        'value' => $backup_includedirectory
	    ));	 */
	    
	    //HERE WE CREATE THE OPTIONS WHERE YOU WANT TO STORE THE BACKUP FILE
	    $this->addElement('Radio', 'backup_tables', array(
	        'label' => 'Database Tables to Backup',
	        'description' => "Select the database tables that you want to be backed-up. (Note: We recommend you to select all the tables. Excluding some tables may lead to data inconsistency. You should exclude tables only when you are sure of what you are doing.)",
	        'multiOptions' => array(
	            1 => 'Select all tables.',
	            0 => 'Exclude some tables.'
	        ),
	        'value' => $backup_tables,
	        'onclick' => 'advanceOption(this.value)',
	    ));
	    
	    //Add Dummy element for using the tables
	    $this->addElement('Dummy', 'tables', array(
	        'ignore' => true,
	        'decorators' => array(array('ViewScript', array(
	                    'viewScript' => '_formCheckbox.tpl',
	                    'class' => 'form element'
	            )))
	    ));
	    
	    $this->addElement('Radio', 'backup_rootfiles', array(
	        'label' => "File Directories to Backup in $base/ (ROOT_DIRECTORY)",
	        'description' => "Select the file directories in $base/ directory that you want to be backed-up.",
	        'multiOptions' => array(
	            1 => 'Select all directories.',
	            0 => 'Exclude some directories.'
	        ),
	        'value' => $backuprootfiles,
	        'onclick' => 'showrootfilesOption(this.value)',
	    ));
	    

	    
	    //Add Dummy element for using the tables
	    $this->addElement('Dummy', 'rootfiles', array(
	        'ignore' => true,
	        'decorators' => array(array('ViewScript', array(
	                    'viewScript' => '_formRootFilesCheckbox.tpl',
	                    'class' => 'form element'
	            )))
	    ));
	    
	    $this->addElement('Radio', 'backup_files', array(
	        'label' => "File Directories to Backup in $base/public/",
	        'description' => "Select the file directories in $base/public/ directory that you want to be backed-up. (Note : The $base/public/directory contains user uploaded files of your site like photos, music, etc in respective sub-directories. Thus, these sub-directories can be very large in size. You may thus consider not to back-up heavy directories.)",
	        'multiOptions' => array(
	            1 => 'Select all directories.',
	            0 => 'Exclude some directories.'
	        ),
	        'value' => $backupfiles,
	        'onclick' => 'showfilesOption(this.value)',
	    ));
	    

	    
	    //Add Dummy element for using the tables
	    $this->addElement('Dummy', 'files', array(
	        'ignore' => true,
	        'decorators' => array(array('ViewScript', array(
	                    'viewScript' => '_formFilesCheckbox.tpl',
	                    'class' => 'form element'
	            )))
	    ));
	    

	    if(file_exists(APPLICATION_PATH.'/application/modules/Arcade/')){
		    $this->addElement('Radio', 'backup_modulesfiles', array(
		        'label' => "Files Directories to Backup in $base/application/modules/",
		        'description' => "Select the files directories in $base/application/modules/ that you want to be backed-up.(Note : The '/oldplugin/application/modules/' directory contains the files of your site's modules/plugins like Blogs, Classified, Documents, Community Ads, Arcade, etc in their respective module directories. Some of these modules can be very large in size. You may thus consider not to back-up heavy modules. We recommend that if your site has Arcade module then this should be excluded from files backup of your site.)",
		        'multiOptions' => array(
		            1 => 'Select all directories.',
		            0 => 'Exclude some directories.'
		        ),
		        'value' => $backupmodulefiles,
		        'onclick' => 'showmodulesfilesOption(this.value)',
		    ));
		    
	    
		    //Add Dummy element for using the tables
		    
			    $this->addElement('Dummy', 'modulesfiles', array(
			        'ignore' => true,
			        'decorators' => array(array('ViewScript', array(
			                    'viewScript' => '_formmodulesfilesCheckbox.tpl',
			                    'class' => 'form element'
			            )))
			    ));	 
	    }   
	    
	
	    $this->addElement('Checkbox', 'backup_optionsettings', array(
	        'label' => 'Save these settings for future use.',
	        'description' => 'Save these Settings',
	        'multiOptions' => array(
	            'yes' => 'Yes',
	        ),
	        
	    ));
	
	    
  		// Add submit button
	    $this->addElement('Button', 'submit', array(
	        'label' => 'Backup Now!',
	        'type' => 'submit',
	        'value' => 'submit',
	        'ignore' => true,
	    ));
  	}
}