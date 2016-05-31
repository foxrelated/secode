<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Upload.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_Form_Admin_Upload extends Engine_Form {

  public function init() 
  {
	    $filename = Zend_Controller_Front::getInstance()->getRequest()->getParam('filename', null);
	    //HERE WE SETTING THE TITLE AND DESCRIPTION OF DATABASE BACKUP
	    $this
	            ->setTitle('Restore Database')
	            ->setDescription("Here, you can restore the database of your site from one of the database backup files created using this SocialEngineAddOns Backup and Restore Plugin. This restore will not work with database backups taken from other tools.");
	            
			$this->addElement('Dummy', 'Message', array(
      'ignore' => true,
      'decorators' => array(array('ViewScript', array(
        'viewScript' => '_formMessageRestore.tpl',
        'class'      => 'form element'
      )))
    ));
	    
	    $this->addElement('File', 'filename', array( 
	        'label' => 'Upload a Backup File',
	        'required' => true,
	        'description' => "Browse and choose a backup file generated only by this plugin. Max file size allowed :  ". (int)ini_get('upload_max_filesize') ." MB."
	    ));
	    $this->filename->getDecorator('Description')->setOption('placement', 'append');
	
	    $this->addElement('Button', 'continue', array(
	        'label' => 'Continue >>',
	        'type' => 'submit',
	    ));
  	}
}