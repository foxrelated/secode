<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Directorysettings.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_Form_Admin_Directorysettings extends Engine_Form {

  public function init() 
  { 	
	    $table = Engine_Api::_()->getDbtable('backupauthentications', 'dbbackup');
	  	$select = $table->select()
	     ->where('backupauthentication_id = ?', 1)
	     ->limit();
	
	  	$row = $table->fetchRow($select);
	  
	  	$htusername = $row['htpassword_username'];
	  	$htpassword = $row['htpassword_password'];
			$backup_enable = $row['htpasswd_enable'];
				
				
	    //HERE WE SETTING THE TITLE AND DESCRIPTION OF DATABASE BACKUP
	    $this
	            ->setTitle('Backup Directory on your Server')
	            ->setDescription('This backup directory will be used for both database and file backups on your server. Please ensure that this location has enough space to store your backups. Below, you can also enable password protection for this backup directory.');
	
			    //Add Dummy element for using the tables
	    $this->addElement('Dummy', 'directory_path', array(
					'label' =>'Current Backup Directory Path',
					'description' => APPLICATION_PATH. '/public/'. Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname,
					'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname,
	    ));
	    
	    $this->addElement('Text', 'dbbackup_directoryname', array(
	        'label' => 'Edit Backup Directory Name',
	        'description' => 'You can rename the backup directory on your server below. (This name must be alphanumeric, and must contain both alphabets and numbers.)',
	        'value' => Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname,
	        'required' => true,
	        
	    ));    
	 	    //HERE WE CREATE THE AUTOMATIC  DROPDOWN OF DATABASE BACKUP FILE OR SIMPLE FILE
		    $this->addElement('Radio', 'backup_enable', array(
		      'label' => 'Enable Password Protection',
		      'description' => "Do you want to enable directory level password protection for the backup directory ?",
		      'multiOptions' => array(
		        1 => 'Yes, enable password protection on the backup directory.',
		        0 => 'No, do not enable password protection on the backup directory.',
		      ),
		      'value' => $backup_enable,
		      'onclick' => 'showhide(this.value);',
		     
		    ));  
			  
		    
		    $this->addElement('Text', 'htusername', array(
		      'label' => 'Username',
		      'value' => $htusername,
		      'ignore' => true,
					 //'required' =>true,
		    ));
	
	   
		 		$this->addElement('Text', 'htpassword', array(
		    'label' => 'Password',
		    'value' => $htpassword,
		    'ignore' => true,
	      //'required' =>true,
		    ));   
	    
	   
				// Add submit button
		    $this->addElement('Button', 'submit', array(
		      'label' => 'Save',
		      'type' => 'submit',
		      'value' =>'submit',
		      'ignore' => true
		    ));
  		}
	}