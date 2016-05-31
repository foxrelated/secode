<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Restore.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_Form_Admin_Restore extends Engine_Form {
	
  public function init() 
  {
	  	$table = Engine_Api::_()->getDbtable('settings', 'dbbackup');
	    $select = $table->select();
	    $row = $table->fetchAll($select);
			foreach ($row->toarray() as $key => $value) {
	      $field_name[$key] = $value;
	    }
	
	    $dbbackupmaintenance_mode = $field_name[2]['value'];
	  	$filename = Zend_Controller_Front::getInstance()->getRequest()->getParam('filename', null);
			//HERE WE SETTING THE TITLE AND DESCRIPTION OF DATABASE BACKUP
	  	$this
	    ->setTitle('Restore from Backup')
	    ->setDescription("Are you sure you want to restore the site database from the backup file: $filename ? Note: Restoring will delete some of your site's data that has been created after the backup date. This cannot be undone.");
	    
			$this->addElement('Dummy', 'Message', array(
	      'ignore' => true,
	      'decorators' => array(array('ViewScript', array(
	        'viewScript' => '_formMessageRestore.tpl',
	        'class'      => 'form element'
	      )))
	    ));
	    
	    $this->addElement('Radio', 'dbbackupmaintenance_mode', array(
	        'label' => 'Activate Maintenance Mode',
	        'description' => 'Do you want the site to be in Maintenance Mode during the restore ? (Putting the site in Maintenance Mode will prevent site visitors from accessing your website. You can customize the maintenance mode page by manually editing the file "/application/maintenance.html". The site will be taken back online once restore is complete.)',
	        'required' => true,
	        'multiOptions' => array(
	           	1 => 'Yes, take the site offline (Activate Maintenance Mode).',
	            0 => 'No, keep the site online.',
	        ),
	        'value' => $dbbackupmaintenance_mode
	    ));
	    
	     $this->addElement('Button', 'submit', array(
	      'type' => 'submit',
	      'decorators' => array(array('ViewScript', array(
	      	'viewScript' => '_formButtonSubmit.tpl',
	        'class'      => 'form element')))
	    ));
  	}
}