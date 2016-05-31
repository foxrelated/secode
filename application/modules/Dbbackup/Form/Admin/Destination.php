<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Destination.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_Form_Admin_Destination extends Engine_Form {

  public function init() {
    //HERE WE SETTING THE TITLE AND DESCRIPTION OF DATABASE BACKUP
    $this
        ->setTitle('Create a new Backup Destination')
        ->setDescription('Create your new backup destination by filling the form below.');

    $destination_prepared = array('1' => 'Email', '2' => 'FTP Directory', '3' => 'MySQL Database');


    // category field
    $this->addElement('Select', 'destination_mode', array(
            'label' => 'Destination Type',
            'description' => 'Email destination type can be used for database backups (manual and automatic).',
            'multiOptions' => $destination_prepared,
            'onchange' => 'javascript:fetchDestinationSettings(this.value);'
    ));


    $this->addElement('Text', 'destinationname', array(
            'label' => 'Destination Name *',
    ));


    $this->addElement('Text', 'email', array(
            'label' => 'Email *',
            'description' => 'Specify the email address to which the database backup files should be sent as attachment to. The database backups can be sent as email attachment only if their filesize is less than or equal to 20 MB. Also, please ensure that the email server and email account can handle large file attachments.'
    ));

    $this->addElement('Text', 'ftphost', array(
            'label' => 'Host *',
    ));

    $this->addElement('Text', 'ftpportno', array(
            'label' => 'Port Number *',
            'value' => 21,
    ));

    $this->addElement('Text', 'ftppath', array(
            'label' => 'Path *',
            'description' => 'Directory path on your FTP server.'
    ));

    $this->ftppath->getDecorator('Description')->setOption('placement', 'APPEND');

    $this->addElement('Text', 'ftpdirectoryname', array(
            'label' => 'Backup Directory *',
            'description' => 'If this directory does not already exist on the FTP server at the above path, then it will be created. Please ensure that this location has enough space for storing backups.',
    ));
    $this->ftpdirectoryname->getDecorator('Description')->setOption('placement', 'APPEND');

    $this->addElement('Text', 'ftpuser', array(
            'label' => 'Username *',
            'description' => 'Please ensure that this user has write permission for the above backup directory.'
    ));
    $this->ftpuser->getDecorator('Description')->setOption('placement', 'APPEND');

    $this->addElement('Text', 'ftppassword', array(
            'label' => 'Password ',
    ));

    $this->addElement('Dummy', 'ftpmsg', array(
            'label' => 'Backup Types',
            'description' =>'Select the backup types for which you want to use this destination. (Automatic backups on FTP Directory are only recommended in case of good site and FTP servers, and for sites having good and regular activity on them.)',
    ));

     // ftp Manual database
    $this->addElement('Checkbox', 'ftpmdb', array(
            'label' => 'Manual Database backups',
            'value' => 1,
    ));
     // ftp Manual file
    $this->addElement('Checkbox', 'ftpmfile', array(
            'label' => 'Manual Files backups',
            'value' => 1,
    ));
      // ftp auto batabase
    $this->addElement('Checkbox', 'ftpadb', array(
            'label' => 'Automatic Database backups',
            'value' => 0,
    ));
    // ftp auto file
 /*   $this->addElement('Checkbox', 'ftpafile', array(
            'label' => 'Automatic Files backups',
            'value' => 0,
    ));*/



    $this->addElement('Text', 'dbhost', array(
            'label' => 'Host *',
            'value' => 'localhost',
    ));

    $this->addElement('Text', 'dbname', array(
            'label' => 'Database Name *',
            'description' => 'Specify the name of the database. This database must exist, and will not be created.'
    ));

    $this->addElement('Text', 'dbuser', array(
            'label' => 'Username *',
            'description' => 'Enter the username which has write access to the database.'
    ));

    $this->addElement('Text', 'dbpassword', array(
            'label' => 'Password ',
    ));


    $this->addElement('Button', 'submit', array(
            'label' => 'Create',
            'type' => 'submit',
    ));
  }

}