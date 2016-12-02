<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Dbbackup.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_Plugin_Task_Dbbackup extends Core_Plugin_Task_Abstract {

  protected $ftp, $database;

  public function execute() {
  	
  	$autobackup = Engine_Api::_()->getApi('settings', 'core')->dbbackup_backupoptions;
  	if(empty($autobackup)) {
	  	// fetch that time stamp when the reminder mail was last sent
	    $taskstable = Engine_Api::_()->getDbtable('tasks', 'core');
	    $rtasksName = $taskstable->info('name');
	    $taskstable_result = $taskstable->select()
					    ->from($rtasksName, array('started_last'))
					    ->where('title = ?', 'Background Automatic Backup')
					    ->where('plugin = ?', 'Dbbackup_Plugin_Task_Dbbackup')
					    ->limit(1);
					   
	    $value = $taskstable->fetchRow($taskstable_result);
	    $old_started_last = Engine_Api::_()->getApi('settings', 'core')->getSetting('dbbackup.startedlast', 0);
	    
			$coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
	    $coreversion = $coremodule->version;
	    if(!($coreversion < '4.1.0')) {
				if(!Engine_Api::_()->dbbackup()->canRunTask("dbbackup","Dbbackup_Plugin_Task_Dbbackup", $old_started_last)){
					return;
				}
	  	}
	  	Engine_Api::_()->getApi('settings', 'core')->setSetting('dbbackup_startedlast', $value['started_last']);
	  	
	  	
	  	
	    $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
	    $backup_file = APPLICATION_PATH . '/public/' . $dir_name_temp;
	
			$fileAdapter = APPLICATION_PATH . '/application/settings/database.php';
			$fileAdapterinfo = include $fileAdapter;
	
			//Setting the selected table into the session.
			$session = new Zend_Session_Namespace();
	
			$session->fileadapter = $fileAdapterinfo['adapter']; 
	    $code_destination = '';
	    if (!is_dir($backup_file) && mkdir($backup_file, 0777, true)) {
	
	      $table = Engine_Api::_()->getDbtable('backupauthentications', 'dbbackup');
	      $select = $table->select();
	      $row = $table->fetchRow($select);
	      $htusername = $row->htpassword_username;
	      $htpassword = $row->htpassword_password;
	      $backup_enable = $row->htpasswd_enable;
	      $htpasswd_text = "$htusername:" . crypt($htpassword) . "";
	      $backup_file1 = APPLICATION_PATH . '/public/' . $dir_name_temp . '/.htpasswd';
	      $fp = fopen($backup_file1, 'w');
	      fwrite($fp, $htpasswd_text);
	      fclose($fp);
	
	      if ($backup_enable == 1) {
	        $authtication_name_store = $dir_name_temp;
	        $authtication_path = APPLICATION_PATH . '/public/' . $dir_name_temp . '/.htpasswd';
	        $format = "AuthType Basic \n AuthName  $authtication_name_store \n AuthUserFile $authtication_path \n Require valid-user";
	        $backupfilepath = APPLICATION_PATH . '/public/' . $dir_name_temp . '/.htaccess';
	        $fp = fopen($backupfilepath, 'w');
	        fwrite($fp, $format);
	        fclose($fp);
	      }
	
	      $password_check_format = "Congratulations! Your backup directory is PASSWORD PROTECTED.\n\nBackups provide insurance for your site. In the event that something on your site goes wrong, you can restore your site's content with the most recent backup file.\n\n **********   Backup and Restore Plugin by SocialEngineAddOns (http://www.socialengineaddons.com)   **********";
	      $password_check_path = APPLICATION_PATH . '/public/' . $dir_name_temp . '/password_check.txt';
	      $fp = fopen($password_check_path, 'w');
	      fwrite($fp, $password_check_format);
	      fclose($fp);
	    }
	    $mailFlage = 0;
	
	
	    $files = array();
	    $tmpPath_files = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR;
	    $files = array($tmpPath_files . 'log' . DIRECTORY_SEPARATOR . 'index.html',
	            $tmpPath_files . 'log' . DIRECTORY_SEPARATOR . 'scaffold' . DIRECTORY_SEPARATOR . 'index.html',
	            $tmpPath_files . 'scaffold' . DIRECTORY_SEPARATOR . 'index.html',
	            $tmpPath_files . 'cache' . DIRECTORY_SEPARATOR . 'index.html',
	            $tmpPath_files . 'session' . DIRECTORY_SEPARATOR . 'index.html',
	            $tmpPath_files . 'package' . DIRECTORY_SEPARATOR . 'sdk' . DIRECTORY_SEPARATOR . 'index.html',
	            $tmpPath_files . 'package' . DIRECTORY_SEPARATOR . 'archives' . DIRECTORY_SEPARATOR . 'index.html',
	            $tmpPath_files . 'package' . DIRECTORY_SEPARATOR . 'compare' . DIRECTORY_SEPARATOR . 'index.html',
	            $tmpPath_files . 'package' . DIRECTORY_SEPARATOR . 'repositories' . DIRECTORY_SEPARATOR . 'index.html',
	            $tmpPath_files . 'package' . DIRECTORY_SEPARATOR . 'manifests' . DIRECTORY_SEPARATOR . 'index.html',
	            $tmpPath_files . 'package' . DIRECTORY_SEPARATOR . 'index.html',
	            $tmpPath_files . 'package' . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . 'index.html',
	            $tmpPath_files . 'backup'  . DIRECTORY_SEPARATOR . 'index.html',
	    );
	    $dbbackup_filename = Engine_Api::_()->getApi('settings', 'core')->dbbackup_autofilename;
	    $databaseflage = 1;
	
	    $backupLog_Table = Engine_Api::_()->getDbtable('backuplogs', 'dbbackup');
	    $log_values = array();
	
	    $lockoption = Engine_Api::_()->getApi('settings', 'core')->dbbackup_lockoptions;
	    $destination_id = Engine_Api::_()->getApi('settings', 'core')->dbbackup_destinations;
	    $destinationlocation_options = Engine_Api::_()->getApi('settings', 'core')->dbbackup_locationoptions;
	    $result = Engine_Api::_()->getItem('destinations', $destination_id);
	    if ($destination_id != 0) {
	
	      if ($result->destination_mode == 3) {
	
	            $log_values = array();
	          $method = 'Database';
	          $log_values = array_merge($log_values, array(
	                      'type' => 'Database',
	                     'method' => 'Automatic',
	                      'destination_name' => $result->destinationname,
	                      'destination_method' => 'Database',
	                      'filename' => 'N.A.',
	                      'start_time' => date('Y-m-d H:i:s'),
	                      'status' => 'Fail'
	              ));
	          $backuplog_id = $backupLog_Table->setLog($log_values);
	
	        if ($lockoption) {
	          $this->lockDatabse();
	        }
	        $databaseList = $result->toarray();
	        $link = mysql_connect($databaseList['dbhost'], $databaseList['dbuser'], $databaseList['dbpassword']);
	        if (!$link) {
	          $subject = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
	          $message = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
	          $reason = 'Reason: Could not connect to database:' . mysql_error();
	
	          if (Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailoption) {
	            $mails = Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailsender;
	            $mail = explode(',', $mails);
	
	            foreach ($mail as $mail_id) {
	              $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
	              Engine_Api::_()->getApi('mail', 'core')->sendSystem($mail_id, 'Backup_Failure_Email_Notification', array(
	                      'subject' => $subject,
	                      'message' => $message,
	                      'reason' => $reason,
	                      'backup_type' => 'Database',
	                      'moremessage' => '------',
	                      'email' => $email,
	                      'queue' => false
	              ));
	            }
	          }
	          die('Could not connect to database: ' . mysql_error());
	        }
	        $databaseList['con'] = $link;
	        // make foo the current db
	        $db_selected = mysql_select_db($databaseList['dbname'], $link);
	        if (!$db_selected) {
	          $subject = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
	          $message = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
	          $reason = 'Reason: Could not connect to database:' . mysql_error();
	
	          if (Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailoption) {
	            $mails = Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailsender;
	            $mail = explode(',', $mails);
	
	            foreach ($mail as $mail_id) {
	              $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
	              Engine_Api::_()->getApi('mail', 'core')->sendSystem($mail_id, 'Backup_Failure_Email_Notification', array(
	                      'subject' => $subject,
	                      'message' => $message,
	                      'backup_type' => 'Database',
	                      'reason' => $reason,
	                      'moremessage' => '---------',
	                      'email' => $email,
	                      'queue' => false
	              ));
	            }
	          }
	          die('Could not connect to database: ' . mysql_error());
	        }
	
	        set_time_limit(0);
	        $initial_code = 0;
	        $table_selected = $this->fetchtables();
	        $num_selected_table = count($table_selected);
	        try {
	          $string = '';
	          $dbinfo = Engine_Db_Table::getDefaultAdapter()->getConfig();
	          $dbname = $dbinfo['dbname'];
	          while (($num_selected_table - 1 >= $initial_code)) {
	            $table_name = $table_selected[$initial_code]['Tables_in_' . $dbname];
	            $dropformat = $this->database_dropformat($table_name);
	            mysql_query($dropformat);
	            $create_format = $this->database_fetchTablecreateformat($table_name);
	            mysql_query($create_format);
	            $dataresult = $this->database_fetchTableData($table_name);
	            $initial_code++;
	          }
	
	          $mailFlage = 1;
	        } catch (Exception $e) {
	          /*  $table = Engine_Api::_()->getDbtable('tasks', 'core');
	            $table->update(array(
	            'state' => 'dormant',
	            'data' => '',
	            'executing' => 0,
	            'executing_id' => 0,
	            ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
	
	          $subject = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
	          $message = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
	          $reason = "Reason: Database to database backup failed.";
	
	          if (Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailoption) {
	            $mails = Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailsender;
	            $mail = explode(',', $mails);
	            $backup_type = 'Database';
	            foreach ($mail as $mail_id) {
	              $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
	              Engine_Api::_()->getApi('mail', 'core')->sendSystem($mail_id, 'Backup_Failure_Email_Notification', array(
	                      'subject' => $subject,
	                      'message' => $message,
	                      'backup_type' => $backup_type,
	                      'reason' => $reason,
	                      'moremessage' => '-------',
	                      'email' => $email,
	                      'queue' => false
	              ));
	            }
	          }
	        }
	
	        $databaseflage = 0;
	      }
	    }
	
	    if ($databaseflage) {
	      if ($lockoption) {
	        $this->lockDatabse();
	      }
	
	      $dbinfo = Engine_Db_Table::getDefaultAdapter()->getConfig();
	      $dbname = $dbinfo['dbname'];
	      $dbhost = $dbinfo['host'];
	      set_time_limit(0);
	      $initial_code = 0;
	      $string = '--';
	      $generated_date = new Zend_Date();
	      $filename_compressed_form = $dbbackup_filename . '_database_' . date("Y_m_d_H_i_s", time()) . '.sql.gz';
	
	
	         $log_values = array();
	
	          if (!empty($destination_id)) {
	
	            if ($result->destination_mode == 1) {
	              $method = 'Email';
	              $destination_name = $result->destinationname;
	            } else if ($result->destination_mode == 2) {
	              $method = 'FTP';
	
	              $destination_name = $result->destinationname;
	            } else if ($result->destination_mode == 0) {
	
	              $method = 'Server Backup Directory & Download';
	
	              $destination_name = $result->destinationname;
	            }
	          } else {
	            $method = 'Download';
	            $destination_name = 'Download to computer';
	          }
	
	
	
	          $log_values = array_merge($log_values, array(
	                      'type' => 'Database',
	                     'method' => 'Automatic',
	                      'destination_name' => $destination_name,
	                      'destination_method' => $method,
	                      'filename' => $filename_compressed_form,
	                      'start_time' => date('Y-m-d H:i:s'),
	                      'status' => 'Fail'
	              ));
	          $backuplog_id = $backupLog_Table->setLog($log_values);
	
	
	      $format = "$string SQL Dump \n$string filename: $filename_compressed_form \n$string Host:$dbhost \n$string Generation Time: $generated_date\n$string Backup execution by: Backup and Restore Plugin by SocialEngineAddOns (http://www.socialengineaddons.com)\n\n$string\n$string Database Name: $dbname\n$string\n";
	      $backup_data['backup_data'] = $format;
	      $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
	      $backup_filepath = APPLICATION_PATH . '/public/' . $dir_name_temp . '/' . $filename_compressed_form;
	      $archiveFileName = $backup_filepath;
	      Engine_Api::_()->dbbackup()->writeintobackupfile($backup_data['backup_data'], $backup_filepath);
	      $table_selected = $this->fetchtables();
	      $num_selected_table = count($table_selected);
	      $string = '';
	      try {
	        while (($num_selected_table - 1 >= $initial_code)) {
	          $table_name = $table_selected[$initial_code]['Tables_in_' . $dbname];
	          $backup_filepath = APPLICATION_PATH . '/public/' . $dir_name_temp . '/' . $filename_compressed_form;
	          $format = $this->fetchtableheader($table_name);
	          $create_format = $this->fetchTablecreateformat($table_name);
	          $resultant_format = "$format\n$create_format";
	          $backup_data['backup_data'] = '';
	          $backup_data['backup_data'] = $resultant_format;
	          Engine_Api::_()->dbbackup()->writeintobackupfile($resultant_format, $backup_filepath);
	          Engine_Api::_()->dbbackup()->writeintobackupfile($this->_fetchTableData1($table_name, $backup_filepath), $backup_filepath);
	          $initial_code++;
	        }
	        $mailFlage = 1;
	      } catch (Exception $e) {
	        /* $table = Engine_Api::_()->getDbtable('tasks', 'core');
	          $table->update(array(
	          'state' => 'dormant',
	          'data' => '',
	          'executing' => 0,
	          'executing_id' => 0,
	          ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
	        /*  $subject = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
	          $message = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
	          $reason = "Reason: Backup could not be saved on the Server Backup Directory.";
	
	          if (Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailoption) {
	          $mails = Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailsender;
	          $mail = explode(',', $mails);
	
	          foreach ($mail as $mail_id) {
	          $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
	          Engine_Api::_()->getApi('mail', 'core')->sendSystem($mail_id, 'Backup_Failure_Email_Notification', array(
	          'subject' => $subject,
	          'message' => $message,
	          'reason' => $reason,
	          'backup_type' => '  ',
	          'moremessage' => '  ',
	          'email' => $email,
	          'queue' => false
	          ));
	          }
	          } */
	      }
	    }
	
	    //*--------------------------------------------
	    //
	    //Here we inserting into the database value of code related row.
	    //if (!$databaseflage) {
	    if (!empty($destination_id)) {
		    if ($result->destination_mode != 3) {
		      $filesize_temp = filesize($backup_filepath);
		      $filesize = round($filesize_temp / 1048576, 3) . ' Mb';      
		    } else {
		      $filesize = '-';
		    }
		    try {
		      $db = Engine_Db_Table::getDefaultAdapter();
		      $db->beginTransaction();
		      $table = Engine_Api::_()->getDbTable('dbbackups', 'dbbackup');
		      $backup_time = time();
		      $row = $table->createRow();
		      $database_filesize = $row->backup_filesize = $filesize;
		      $row->backup_time = $backup_time;
		
		      if (!empty($destination_id)) {
		        if ($result->destination_mode == 1) {
		          $row->backup_method = 'Email';
		          $row->destination_name = $result->destinationname;
		        } else if ($result->destination_mode == 2) {
		          $row->backup_method = 'FTP';
		          $row->destination_name = $result->destinationname;
		        } else if ($result->destination_mode == 3) {
		          $row->backup_method = 'Database';
		          $row->destination_name = $result->destinationname;
		        } else if ($result->destination_mode == 0) {
		          $row->backup_method = 'Server Backup Directory & Download';
		          $row->destination_name = $result->destinationname;
		        }
		        $database_destination = $row->destination_name;
		      }
		
		      $row->backup_timedescription = date('r');
		      if ($result->destination_mode != 3) {
		        $row->backup_filename = $filename_compressed_form;
		      } else {
		        $row->backup_filename = '-';
		      }
		      $database_filename = $row->backup_filename;
		      $row->backup_status = 1;
		      $row->backup_codemethod = 1;
		      $row->backup_auto = 1;
		      $id = $row->save();
		      $db->commit();
		
		
	        $log_values=array();
	          if($result->destination_mode !==3){
	          $log_values = array_merge($log_values, array(
	                      
	                        'filename'=>$filename_compressed_form,
	                        'size' => $filesize,
	                        'end_time' => date('Y-m-d H:i:s'),
	                        'status' => 'Success',
	                ));
	
	            $backupLog_Table->updateLog($log_values);
	            }else{
	               $log_values = array_merge($log_values, array(
	                      
	                        'backuplog_id'=>$backuplog_id,
	                        'size' => 0,
	                        'end_time' => date('Y-m-d H:i:s'),
	                        'status' => 'Success',
	                ));
	
	            $backupLog_Table->updateLog($log_values);
	            }
		    } catch (Exception $e) {
		
		      /* $table = Engine_Api::_()->getDbtable('tasks', 'core');
		        $table->update(array(
		        'state' => 'dormant',
		        'data' => '',
		        'executing' => 0,
		        'executing_id' => 0,
		        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
		
		      $db->rollBack();
		      throw $e;
		    }
		
		    if ($lockoption) {
		      $this->unlockDatabse();
		    }
	
	    }
	
	
	
	    //*--------------------------------------------
	    //Taking code backup if condition matches.
	
	    if (empty(Engine_Api::_()->getApi('settings', 'core')->dbbackup_backuptype)) {
	      try {
	        if (!empty($destinationlocation_options)) {
	          $coderesult = Engine_Api::_()->getItem('destinations', $destinationlocation_options);
	          if ($coderesult->destination_mode == 2) {
	            $destinationlocation_options = 2;
	          } else if ($coderesult->destination_mode == 0) {
	            $destinationlocation_options = 1;
	          }
	        }
	        require_once 'PEAR.php';
	        require_once 'Archive/Tar.php';
	        $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
	        $this->_outputPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public'
	            . DIRECTORY_SEPARATOR . $dir_name_temp;
	        set_time_limit(0);
	        // Make filename
	        $archiveFileName = $dbbackup_filename . '_code_' . date("Y_m_d_H_i_s", time()) . '.tar';
	        $filename_code = $archiveFileName;
	        $archiveFileName = preg_replace('/[^a-zA-Z0-9_.-]/', '', $archiveFileName);
	        if (strtolower(substr($archiveFileName, -4)) != '.tar') {
	          $archiveFileName .= '.tar';
	        }
	
	             if (!empty($backup_options)) {
	              $coderesult = Engine_Api::_()->getItem('destinations', $backup_options);
	              if ($coderesult->destination_mode == 2) {
	                $method = 'FTP';
	                $destination_name = $coderesult->destinationname;
	              } else if ($coderesult->destination_mode == 0) {
	                $method = 'Server Backup Directory & Download';
	
	                $destination_name = $coderesult->destinationname;
	              }
	            } else {
	              $method = 'Download';
	              $destination_name = 'Download to computer';
	            }
	
	
	
	            $log_values = array_merge($log_values, array(
	                        'type' => 'File',
	                       'method' => 'Automatic',
	                        'destination_name' => $destination_name,
	                        'destination_method' => $method,
	                        'filename' => $archiveFileName,
	                        'start_time' => date('Y-m-d H:i:s'),
	                        'status' => 'Fail'
	                ));
	            $code_backuplog_id = $backupLog_Table->setLog($log_values);
	
	        $archiveFileName = $this->_outputPath . DIRECTORY_SEPARATOR . $archiveFileName;
	
	        // setup paths
	        $archiveSourcePath = APPLICATION_PATH;
	        $tmpPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
	        $excludedirectory = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $dir_name_temp;
	        // Make archive
	        $archive = new Archive_Tar($archiveFileName);
	        // Add files
	        // $includedirectory = Engine_Api::_()->getApi('settings', 'core')->dbbackup_includedirectory;
	        $path = $archiveSourcePath;
	
	        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
	        foreach ($it as $file) {
	          $pathname = $file->getPathname();
	          if ($file->isFile()) {
	            if (substr($pathname, 0, strlen($tmpPath)) == $tmpPath || substr($pathname, 0, strlen($excludedirectory)) == $excludedirectory) {
	              continue;
	            } else {
	              $files[] = $pathname;
	            }
	          }
	        }
	        //}
	        $ret = $archive->addModify($files, '', $path);
	        if (PEAR::isError($ret)) {
	          throw new Engine_Exception($ret->getMessage());
	        }
	        $filesize1 = filesize($archiveFileName);
	        $db = Engine_Db_Table::getDefaultAdapter();
	        $db->beginTransaction();
	        $table = Engine_Api::_()->getDbTable('dbbackups', 'dbbackup');
	        $backup_time = time();
	        $row = $table->createRow();
	        $filesize1 = round($filesize1 / 1048576, 3) . ' Mb';
	        $row->backup_filesize1 = $filesize1;
	        $code_filesize = $row->backup_filesize = $filesize1;
	        $row->backup_time = $backup_time;
	
	        if ($destinationlocation_options == 1) {
	          $row->backup_method = 'Server Backup Directory & Download';
	          $row->destination_name = $code_destination = $coderesult->destinationname;
	        } elseif ($destinationlocation_options == 2) {
	          $row->backup_method = 'FTP';
	          $row->destination_name = $code_destination = $coderesult->destinationname;
	        }
	
	
	        $row->backup_filename1 = $filename_code;
	        $row->backup_status = 1;
	        $row->backup_timedescription = date('r');
	        $row->backup_codemethod = 2;
	        $row->backup_auto = 1;
	        $id = $row->save();
	        $db->commit();
	
	        if ($destinationlocation_options == 2)
	          {
	          set_time_limit(0);
	          $download = 0;
	          $this->ftp = $coderesult->toarray();
	          if (!isset($this->ftp['conn']))
	            $this->backup_ftp_connect($archiveFileName);
	          else {
	            $this->backup_ftp_connected($archiveFileName);
	          }
	          $mailFlage = 0;
	          if ($this->backup_file_transfer($archiveFileName, $filename_code, $id)) {
	            $mailFlage = 1;
	          }
	        }
	
	         $log_values=array();
	            $log_values = array_merge($log_values, array(
	                        'backuplog_id' => $code_backuplog_id,
	                        'size' => $filesize1,
	                        'end_time' => date('Y-m-d H:i:s'),
	                        'status' => 'Success',
	                ));
	
	            $backupLog_Table->updateLog($log_values);
	      } catch (Exception $e) {
	
	        /*   $table = Engine_Api::_()->getDbtable('tasks', 'core');
	          $table->update(array(
	          'state' => 'dormant',
	          'data' => '',
	          'executing' => 0,
	          'executing_id' => 0,
	          ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
	
	        $db->rollBack();
	        throw $e;
	      }
	    }
	
	
	
	
	
	
	    if ($destination_id != 0) {
	      if ($result->destination_mode == 1) {
	
	        set_time_limit(0);
	        $download = 0;
	        $link = $backup_filepath;
	        $size = filesize($link);
	        $max = 20971520;
	        if ($size > $max) {
	          $subject = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
	          $message = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
	          $reason = "Reason: The backup file could not be emailed as attachment because it is larger than 20 MB in size.";
	          $moremessage = "This backup file has been saved in the Backup Directory on your server. You may download it from Database Backups listing page.";
	
	          if (Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailoption) {
	            $mails = Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailsender;
	            $mail = explode(',', $mails);
	            $backup_type = 'Backup Type: Database';
	            foreach ($mail as $mail_id) {
	              $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
	              Engine_Api::_()->getApi('mail', 'core')->sendSystem($mail_id, 'Backup_Failure_Email_Notification', array(
	                      'subject' => $subject,
	                      'message' => $message,
	                      'backup_type' => $backup_type,
	                      'reason' => $reason,
	                      'moremessage' => $moremessage,
	                      'email' => $email,
	                      'queue' => false
	              ));
	            }
	          }
	
	          return;
	        }
	        $date = date('r');
	        $str = str_replace("+0000", "", $date);
	        $subject = "Automatic Database backup of your site on $str";
	        $translate = Zend_Registry::get('Zend_Translate');
	        $user_message = $translate->_('_EMAIL_HEADER_BODY');
	        $user_message .= "\n\nThe automatic database backup of your site was successfully completed on $str.\n\nPlease find the backup file attached with this email.";
	        $to = $result->email;
	        $fileatt_type = 'application/x-gzip';
	        $fileatt_name = $filename_compressed_form;
	        $from = 'Site Admin' . '<' . Engine_Api::_()->getApi('settings', 'core')->core_mail_from . '>';
	        $headers = "From: " . $from;
	        $data = @file_get_contents($link);
	        $semi_rand = md5(time());
	        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
	
	
	        $headers .= "\nMIME-Version: 1.0\n" .
	            "Content-Type: multipart/mixed;\n" .
	            " boundary=\"{$mime_boundary}\"";
	
	        $email_message = "This is a multi-part message in MIME format.\n\n" .
	            "--{$mime_boundary}\n" .
	            "Content-Type:text/html; charset=\"iso-8859-1\"\n" .
	            "Content-Transfer-Encoding: 7bit\n\n" . $user_message . "\n\n";
	
	        $data = chunk_split(base64_encode($data));
	
	        $email_message .= "--{$mime_boundary}\n" .
	            "Content-Type: {$fileatt_type};\n" .
	            " name=\"{$fileatt_name}\"\n" .
	            "Content-Transfer-Encoding: base64\n\n" .
	            $data . "\n\n" .
	            "--{$mime_boundary}--\n";
	
	
	        $mail_sent = mail($to, $subject, $email_message, $headers);
	
	        if ($mail_sent) {
	          $mailFlage = 1;
	          unlink($link);
	
	          $view_msg = 'Email has been sent successfully.';
	          $time_out = 7000;
	          $no_form = 1;
	        } else {
	          $mailFlage = 0;
	          $is_error = 1;
	
	
	          $table = Engine_Api::_()->getDbTable('dbbackups', 'dbbackup');
	          $table->update(array('backup_status' => 0), array('dbbackup_id = ?' => $id));
	          $subject = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
	          $message = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
	          $reason = "Reason: There was an error in sending the email with backup attachment.";
	          $moremessage = "This backup file has been saved in the Backup Directory on your server. You may download it from Database Backups listing page.";
	          $backup_type = 'Backup Type: Database';
	          if (Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailoption) {
	            $mails = Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailsender;
	            $mail = explode(',', $mails);
	            $backup_type = 'Backup Type: Database';
	            foreach ($mail as $mail_id) {
	              $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
	              Engine_Api::_()->getApi('mail', 'core')->sendSystem($mail_id, 'Backup_Failure_Email_Notification', array(
	                      'subject' => $subject,
	                      'message' => $message,
	                      'backup_type' => $backup_type,
	                      'reason' => $reason,
	                      'moremessage' => $moremessage,
	                      'email' => $email,
	                      'queue' => false
	              ));
	            }
	          }
	          $error_array[] = 'There was an error in sending your email. Please try again later.';
	          $time_out = 50000;
	          $no_form = 1;
	          $table = Engine_Api::_()->getDbTable('dbbackups', 'dbbackup');
	          $table->update(array('backup_status' => 0), array('dbbackup_id = ?' => $id));
	        }
	      } else if ($result->destination_mode == 2) {
	        set_time_limit(0);
	        $mailFlage = 0;
	        $download = 0;
	        $this->ftp = $result->toarray();
	        $file = $backup_filepath;
	        if (!isset($this->ftp['conn']))
	          $this->backup_ftp_connect($file);
	        else {
	          $this->backup_ftp_connected($file);
	        }
	
	        if ($this->backup_file_transfer($file, $filename_compressed_form, $id)) {
	          $mailFlage = 1;
	        }
	      }
	    }
	
	
	    if ($mailFlage) {
	
	      if (!empty(Engine_Api::_()->getApi('settings', 'core')->dbbackup_backuptype)) {
	
	        $date = date('r');
	        $array = (explode(" ", $date));
	        $array[3] = $array[3] . ',';
	        $date1 = implode(" ", $array);
	        $date1 = str_replace("+0000", "", $date1);
	        $subject = "Automatic backup of your site on $date1";
	        $message = '';
	        $message .= "The automatic backup of your site's database has completed. Please find below its details:";
	        $message .= "\n\nDatabase backup information:";
	        $status = 'Successful';
	
	        if (Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailoption) {
	          $mails = Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailsender;
	          $mail = explode(',', $mails);
	
	          foreach ($mail as $mail_id) {
	            $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
	            Engine_Api::_()->getApi('mail', 'core')->sendSystem($mail_id, 'Database_Backup_Email_Notification', array(
	                    'subject' => $subject,
	                    'message' => $message,
	                    'status' => $status,
	                    'completion_time' => $date1,
	                    'destination_name' => $database_destination,
	                    'backup_filesize' => $database_filesize,
	                    'backup_filename' => $database_filename,
	                    'email' => $email,
	                    'queue' => false
	            ));
	          }
	        }
	      } else {
	
	        $date = date('r');
	        $array = (explode(" ", $date));
	        $array[3] = $array[3] . ',';
	        $date1 = implode(" ", $array);
	        $date1 = str_replace("+0000", "", $date1);
	        $subject = "Automatic backup of your site on $date1";
	        $message = '';
	        $message .= "The automatic backup of your site's database and files has completed. Please find below its details:";
	        $message .= "\n\nDatabase backup information:";
	        $status = 'Successful';
	        $date2 = date('r');
	        $array2 = (explode(" ", $date2));
	        $array2[3] = $array2[3] . ',';
	        $date2 = implode(" ", $array2);
	        $date2 = str_replace("+0000", "", $date2);
	        $message2 = "Files backup information:";
	
	
	        if (Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailoption) {
	
	          $mails = Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailsender;
	          $mail = explode(',', $mails);
	          foreach ($mail as $mail_id) {
	            $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
	            $resultf = Engine_Api::_()->getApi('mail', 'core')->sendSystem($mail_id, 'Database_and_Files_Backup_Email_Notification', array(
	                        'subject' => $subject,
	                        'message' => $message,
	                        'status' => $status,
	                        'completion_time' => $date1,
	                        'destination_name' => $database_destination,
	                        'backup_filesize' => $database_filesize,
	                        'backup_filename' => $database_filename,
	                        'message2' => $message2,
	                        'status' => $status,
	                        'completion_time2' => $date2,
	                        'destination_name2' => $code_destination,
	                        'backup_filesize2' => $code_filesize,
	                        'backup_filename2' => $filename_code,
	                        'email' => $email,
	                        'queue' => false
	                ));
	          }
	        }
	      }
	    }
  	}
  }

  //This function return the create statement for a table start.
  public function fetchTablecreateformat($table) {
    try {
      $db = Engine_Db_Table::getDefaultAdapter();
      $export = Engine_Db_Export::factory($db);
      $adapter = $export->getAdapter();
      $quotedTable = $export->getAdapter()->quoteIdentifier($table);
      $result = $this->fetchrow('SHOW CREATE TABLE ' . $quotedTable);
      $result = $result[0]['Create Table'];
      $output = '';
      if ($export->getParam('dropTable', true)) {
        $output = 'SET FOREIGN_KEY_CHECKS=0;';
        $output .= 'DROP TABLE IF EXISTS ' . $quotedTable . ';';
      }
      $output .= PHP_EOL . $result . ';';
      $output .= PHP_EOL . PHP_EOL;
      return $output;
    } catch (Exception $e) {
      /* $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
    }
  }

  //This function return the header of the table start.
  public function fetchtableheader($table) {
    try {
      $db = Engine_Db_Table::getDefaultAdapter();
      $export = Engine_Db_Export::factory($db);
      return
      $this->fetchcomments('--------------------------------------------------------') . PHP_EOL .
      PHP_EOL .
      $this->fetchcomments() . PHP_EOL .
      $this->fetchcomments('Table structure for ' . $export->getAdapter()->quoteIdentifier($table)) . PHP_EOL .
      $this->fetchcomments() . PHP_EOL .
      PHP_EOL
      ;
    } catch (Exception $e) {
      /* $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
    }
  }

  //This function return the comments for the file start .
  public function fetchcomments($comment = '') {
    try {
      $db = Engine_Db_Table::getDefaultAdapter();
      $export = Engine_Db_Export::factory($db);

      if (empty($comment)) {
        return '--';
      }
      if (strpos($comment, "\n") === false && strpos($comment, "\r") === false) {
        return '-- ' . $comment;
      }
      if (strpos($comment, '/*') !== false) {
        $comment = str_replace('/*', '', $comment);
      }
      if (strpos($comment, '*/') !== false) {
        $comment = str_replace('*/', '', $comment);
      }
      return '/*' . $comment . '*/';
    } catch (Exception $e) {
      /* $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
    }
  }

  //This function return the row of the tables start.
  public function fetchrow($sql) {
    try {
    	$session = new Zend_Session_Namespace();
			$fileAdapterinfo['adapter'] = $session->fileadapter;
      $db = Engine_Db_Table::getDefaultAdapter();
      $export = Engine_Db_Export::factory($db);
      $connection = $export->getAdapter()->getConnection();
			if ($fileAdapterinfo['adapter'] == 'mysql') {
				if (!($result = mysql_query($sql))) {
					throw new Engine_Db_Export_Exception('Unable to execute raw query.');
				}
				$data = array();
				while (false != ($row = mysql_fetch_array($result))) {
					$data[] = $row;
				}
			} elseif ($fileAdapterinfo['adapter'] == 'pdo_mysql') {
				if (!($result = $connection->query($sql))) {
					throw new Engine_Db_Export_Exception('Unable to execute query.');
				}
				$data = array();
				while (false != ($row = $result->fetch())) {
					$data[] = $row;
				}
			} else {
				if (!($result = $connection->query($sql))) {
					throw new Engine_Db_Export_Exception('Unable to execute raw query.');
				}
				$data = array();
				while (false != ($row = $result->fetch_assoc())) {
					$data[] = $row;
				}
			}
      return $data;      	
    } catch (Exception $e) {
      /* $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
      throw $e;
    }
  }

  //This function return the tables of the database start.
  public function fetchtables() {
    try {
      $db = Engine_Db_Table::getDefaultAdapter();
      $export = Engine_Db_Export::factory($db);
      return array_values($export->getAdapter()->fetchAll('SHOW TABLES'));
    } catch (Exception $e) {
      /*  $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
      throw $e;
    }
  }

  //This function return the data of the tables start.
  public function _fetchTableData1($table, $backup_filepath) {
    try {

      $db = Engine_Db_Table::getDefaultAdapter();
      $export = Engine_Db_Export::factory($db);
      $adapter = $export->getAdapter();
      $quotedTable = $export->getAdapter()->quoteIdentifier($table);
      $output = '';
      $output .= "LOCK TABLES $quotedTable WRITE;\n";

      // Get the totall number of rows in tables
      $sql = "SELECT COUNT(*) AS row_table FROM $quotedTable";
      $stmt = $adapter->query($sql);
      $row_count = $stmt->fetch();
      // Counter for while loop base on tables rows
      $counter = $row_count['row_table'] / 60000;
      if ($row_count['row_table'] % 60000)
        $counter++;
      $i = 1;
      // Staring index of tabled where to fetch
      $start_limit = 0;

      while ($i <= $counter) {

        if ($i != 1) {
          $start_limit+=60000;
        }

        // Get data
        $sql = "SELECT * FROM  $quotedTable  LIMIT $start_limit,60000";
        $stmt = $adapter->query($sql);
        $first = true;
        $columns = null;

        while (false != ($row = $stmt->fetch())) {
          if (!$export->getParam('insertExtended', true) || $first) {
            $output .= 'INSERT ';
            if ($export->getParam('insertIgnore', false)) {
              $output .= 'IGNORE ';
            }
            $output .= 'INTO ' . $quotedTable . ' ';
            // Complete
            if ($export->getParam('insertComplete', true)) {
              if (empty($columns)) {
                $columns = implode(', ', array_map(array($adapter, 'quoteIdentifier'), array_keys($row)));
              }
              $output .= '(' . $columns . ') ';
              $output .= 'VALUES ';
            }
          }
          // Other wise we are continuing a previous query
          else {
            $output .= ',';
            $output .= PHP_EOL;
          }

          // Add data
          $data = array();

          foreach ($row as $key => $value) {
            if (null === $value) {
              $data[$key] = 'NULL';
            } else {
              $data[$key] = $adapter->quote($value);
            }
          }
          $output .= '(' . implode(', ', $data) . ')' . ';';
          $output .= "\n";
          // Write into file if output size greather than 50000
          if (strlen($output) > 50000) {
            Engine_Api::_()->dbbackup()->writeintobackupfile($output, $backup_filepath);
            $output = '';
          }
        }
        $i++;
      }
      $output .= "\nUNLOCK TABLES;\n";
      $output .= "\nSET FOREIGN_KEY_CHECKS=1;";
      //$output_array['data'] = $output;

      return $output;
    } catch (Exception $e) {
      /*  $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
      throw $e;
    }
  }

  protected function _createTemporaryFile() {
    try {
      $file = tempnam('/tmp', 'en4_install_backup');
      if (!$file) {
        throw new Engine_Exception('Unable to create temp file');
      }
      return $file;
    } catch (Exception $e) {
      /*  $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
      throw $e;
    }
  }

  public function lockDatabse() {
    try {
    	$session = new Zend_Session_Namespace();
			$fileAdapterinfo['adapter'] = $session->fileadapter;    	
      $table_selected = $this->fetchtables();
      $num_selected_table = count($table_selected);
      $initial_code = 0;
      if ($num_selected_table > 0) {
        $table_name = array();
        $dbinfo = Engine_Db_Table::getDefaultAdapter()->getConfig();
        $dbname = $dbinfo['dbname'];
        while (($num_selected_table - 1 >= $initial_code)) {
          $table_name[] = '`' . $table_selected[$initial_code]['Tables_in_' . $dbname] . '`  WRITE';

          $initial_code++;
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $export = Engine_Db_Export::factory($db);
        $connection = $export->getAdapter()->getConnection();
        if($fileAdapterinfo['adapter'] != 'mysql') {
	        if (!($result = $connection->query('LOCK TABLES ' . implode(', ', $table_name)))) {
	          throw new Engine_Db_Export_Exception('Unable to execute lock query.');
	        }
        } else {
	 	        if (!($result = mysql_query('LOCK TABLES ' . implode(', ', $table_name)))) {
		          throw new Engine_Db_Export_Exception('Unable to execute lock query.');
		        }       	
        }
      }
    } catch (Exception $e) {
      /*   $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
      throw $e;
    }
  }

  public function unlockDatabse() {
    try {
    	$session = new Zend_Session_Namespace();
			$fileAdapterinfo['adapter'] = $session->fileadapter; 
      $db = Engine_Db_Table::getDefaultAdapter();
      $export = Engine_Db_Export::factory($db);
      $connection = $export->getAdapter()->getConnection();
      if($fileAdapterinfo['adapter'] != 'mysql') {
	      if (!($result = $connection->query('UNLOCK TABLES'))) {
	        throw new Engine_Db_Export_Exception('Unable to execute unlock query.');
	      }
      } else {
 					if (!($result = mysql_query('UNLOCK TABLES'))) {
	        	throw new Engine_Db_Export_Exception('Unable to execute unlock query.');
	      }     	
      }
    } catch (Exception $e) {

      /* $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
      throw $e;
    }
  }

  function backup_file_transfer($file, $dest_filename, $id) {
    try {
      $allowFileExtensions = array('tar');
      if (in_array(end(explode(".", $file)), $allowFileExtensions)) {
        $backup_type = "Backup Type: File";
      } else {
        $backup_type = "Backup Type: Database";
      }
      set_time_limit(0);
      if (!(@ftp_chdir($this->ftp['conn'], $this->ftp['ftppath'] . "/" . $this->ftp['ftpdirectoryname']))) {
        @ftp_mkdir($this->ftp['conn'], $this->ftp['ftppath'] . "/" . $this->ftp['ftpdirectoryname']);
      }

      $source = $file;


      $dest_complete_path = $this->ftp['ftppath'] . "/" . $this->ftp['ftpdirectoryname'] . "/" . $dest_filename;

      if (file_exists($source)) {
        $put_file = @ftp_put($this->ftp['conn'], $dest_complete_path, $source, FTP_BINARY);
        if (!$put_file) {
          // turn passive mode on
          @ftp_pasv($this->ftp['conn'], true);
          $put_file = @ftp_put($this->ftp['conn'], $dest_complete_path, $source, FTP_BINARY);
        }

        if (!$put_file) {
          $subject = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
          $message = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
          $reason = "Reason: FTP Error: Could not save the backup file - $file on the FTP server.";
          $moremessage = "This backup file has been saved in the Backup Directory on your server. You may download it from Database Backups listing page.";

          if (Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailoption) {
            $mails = Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailsender;
            $mail = explode(',', $mails);

            foreach ($mail as $mail_id) {
              $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
              Engine_Api::_()->getApi('mail', 'core')->sendSystem($mail_id, 'Backup_Failure_Email_Notification', array(
                      'subject' => $subject,
                      'message' => $message,
                      'backup_type' => $backup_type,
                      'reason' => $reason,
                      'moremessage' => $moremessage,
                      'email' => $email,
                      'queue' => false
              ));
            }
          }

          $table = Engine_Api::_()->getDbTable('dbbackups', 'dbbackup');

          $table->update(array('backup_status' => 0), array('dbbackup_id = ?' => $id));
          return FALSE;
        } else {
          @unlink($source);
        }

        // Everything worked OK
        return TRUE;
      } else {
        $subject = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
        $message = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
        $reason = "FTP Error: Could not find the backup file on your site\'s server.";


        if (Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailoption) {
          $mails = Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailsender;
          $mail = explode(',', $mails);

          foreach ($mail as $mail_id) {
            $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($mail_id, 'Backup_Failure_Email_Notification', array(
                    'subject' => $subject,
                    'message' => $message,
                    'backup_type' => $backup_type,
                    'reason' => $reason,
                    'moremessage' => '  ',
                    'email' => $email,
                    'queue' => false
            ));
          }
        }


        $table = Engine_Api::_()->getDbTable('dbbackups', 'dbbackup');

        $table->update(array('backup_status' => 0), array('dbbackup_id = ?' => $id));
        return FALSE;
      }
    } catch (Exception $e) {

      /* $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */

      throw $e;
    }
  }

  function backup_ftp_connect($filename) {
    try {
      $allowFileExtensions = array('tar');
      if (in_array(end(explode(".", $filename)), $allowFileExtensions)) {
        $backup_type = "Backup Type: File";
      } else {
        $backup_type = "Backup Type: Database";
      }

      set_time_limit(0);

      //Connect to theserver
      $this->ftp['conn'] = @ftp_connect($this->ftp['ftphost'], $this->ftp['ftpportno']);

      if (!$this->ftp['conn']) {

        $subject = "The automatic backup scheduled for your site failed at: " . date('Y-m-d H:i:s') . '.';
        $message = "The automatic backup scheduled for your site failed at: " . date('Y-m-d H:i:s') . '.';
        $reason = "Reason: FTP Error: Could not connect to FTP server.";
        $moremessage = "This backup file has been saved in the Backup Directory on your server. You may download it from Database Backups listing page.";

        if (Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailoption) {
          $mails = Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailsender;
          $mail = explode(',', $mails);

          foreach ($mail as $mail_id) {
            $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($mail_id, 'Backup_Failure_Email_Notification', array(
                    'subject' => $subject,
                    'message' => $message,
                    'backup_type' => $backup_type,
                    'reason' => $reason,
                    'moremessage' => $moremessage,
                    'email' => $email,
                    'queue' => false
            ));
          }
        }
        $table = Engine_Api::_()->getDbTable('dbbackups', 'dbbackup');
        $table->update(array('backup_status' => 0), array('dbbackup_id = ?' => $id));
        return FALSE;
      }
      // Login to the server
      $this->ftp['login'] = ftp_login($this->ftp['conn'], $this->ftp['ftpuser'], $this->ftp['ftppassword']);

      if (!$this->ftp['login']) {

        $subject = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
        $message = "The automatic backup scheduled for your site failed at:" . date('Y-m-d H:i:s') . '.';
        $reason = "Reason: FTP Error: Could not login as user FTP_USER on the FTP server.";
        $moremessage = "This backup file has been saved in the Backup Directory on your server. You may download it from Database Backups listing page.";

        if (Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailoption) {
          $mails = Engine_Api::_()->getApi('settings', 'core')->dbbackup_mailsender;
          $mail = explode(',', $mails);

          foreach ($mail as $mail_id) {
            $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($mail_id, 'Backup_Failure_Email_Notification', array(
                    'subject' => $subject,
                    'messgae' => $message,
                    'backup_type' => $backup_type,
                    'reason' => $reason,
                    'moremessage' => $moremessage,
                    'email' => $email,
                    'queue' => false
            ));
          }
        }
        $table = Engine_Api::_()->getDbTable('dbbackups', 'dbbackup');
        $table->update(array('backup_status' => 0), array('dbbackup_id = ?' => $id));
        return FALSE;
      }
    } catch (Exception $e) {

      /*   $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */

      throw $e;
    }
  }

  function backup_ftp_connected($file) {
    try {
      set_time_limit(0);

      if (!@ftp_systype($this->ftp['conn'])) {
        return $this->ftp['conn'] = $this->backup_ftp_connect($file);
      } else {
        return $this->ftp['conn'];
      }
    } catch (Exception $e) {

      /* $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */

      throw $e;
    }
  }

  //This is a function for droping the table.
  public function database_dropformat($table) {
    try {
      $db = Engine_Db_Table::getDefaultAdapter();
      $export = Engine_Db_Export::factory($db);
      $adapter = $export->getAdapter();
      $quotedTable = $export->getAdapter()->quoteIdentifier($table);
      $outputdrop = '';
      if ($export->getParam('dropTable', true)) {
        $outputdrop = 'DROP TABLE IF EXISTS ' . $quotedTable . ';';
      }
      return $outputdrop;
    } catch (Exception $e) {

      /* $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */

      throw $e;
    }
  }

  //This is a function for creating the table.
  public function database_fetchTablecreateformat($table) {
    try {
      $db = Engine_Db_Table::getDefaultAdapter();
      $export = Engine_Db_Export::factory($db);
      $adapter = $export->getAdapter();
      $quotedTable = $export->getAdapter()->quoteIdentifier($table);
      $result = $this->database_fetchrow('SHOW CREATE TABLE ' . $quotedTable);
      $result = $result[0]['Create Table'];
      $output = '';
      $output .= PHP_EOL . $result . ';';
      $output .= PHP_EOL . PHP_EOL;
      return $output;
    } catch (Exception $e) {
      /* $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
      throw $e;
    }
  }

  //This function return the row of the tables start.
  public function database_fetchrow($sql) {
    try {
    	$session = new Zend_Session_Namespace();
			$fileAdapterinfo['adapter'] = $session->fileadapter;     	
      $db = Engine_Db_Table::getDefaultAdapter();
      $export = Engine_Db_Export::factory($db);
      $connection = $export->getAdapter()->getConnection();
			if ($fileAdapterinfo['adapter'] == 'mysql') {
				if (!($result = mysql_query($sql))) {
					throw new Engine_Db_Export_Exception('Unable to execute raw query.');
				}
				$data = array();
				while (false != ($row = mysql_fetch_array($result))) {
					$data[] = $row;
				}
				return $data;
			} elseif ($fileAdapterinfo['adapter'] == 'pdo_mysql') {
				if (!($result = $connection->query($sql))) {
					throw new Engine_Db_Export_Exception('Unable to execute query.');
				}
				$data = array();
				while (false != ($row = $result->fetch())) {
					$data[] = $row;
				}
			} else {
				if (!($result = $connection->query($sql))) {
					throw new Engine_Db_Export_Exception('Unable to execute raw query.');
				}
				$data = array();
				while (false != ($row = $result->fetch_assoc())) {
					$data[] = $row;
				}
				return $data;
			}
      
      return $data;
    } catch (Exception $e) {
      /* $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
      throw $e;
    }
  }

  //This function return the data of the tables start.
  public function database_fetchTableData($table) {
    try {
      $db = Engine_Db_Table::getDefaultAdapter();
      $export = Engine_Db_Export::factory($db);
      $adapter = $export->getAdapter();
      $quotedTable = $export->getAdapter()->quoteIdentifier($table);
      $output = '';

      // Get the totall number of rows in tables
      $sql = "SELECT COUNT(*) AS row_table FROM $quotedTable";
      $stmt = $adapter->query($sql);
      $row_count = $stmt->fetch();
      // Counter for while loop base on tables rows
      $counter = $row_count['row_table'] / 60000;
      if ($row_count['row_table'] % 60000)
        $counter++;
      $i = 1;
      // Staring index of tabled where to fetch
      $start_limit = 0;

      while ($i <= $counter) {

        if ($i != 1) {
          $start_limit+=60000;
        }

        // Get data
        $sql = "SELECT * FROM  $quotedTable  LIMIT $start_limit,60000";
        $stmt = $adapter->query($sql);
        $first = true;
        $columns = null;

        while (false != ($row = $stmt->fetch())) {
          if (!$export->getParam('insertExtended', true) || $first) {
            $output .= 'INSERT ';
            if ($export->getParam('insertIgnore', false)) {
              $output .= 'IGNORE ';
            }
            $output .= 'INTO ' . $quotedTable . ' ';
            // Complete
            if ($export->getParam('insertComplete', true)) {
              if (empty($columns)) {
                $columns = implode(', ', array_map(array($adapter, 'quoteIdentifier'), array_keys($row)));
              }
              $output .= '(' . $columns . ') ';
              $output .= 'VALUES ';
            }
          }
          // Other wise we are continuing a previous query
          else {
            $output .= ',';
            $output .= PHP_EOL;
          }

          // Add data
          $data = array();

          foreach ($row as $key => $value) {
            if (null === $value) {
              $data[$key] = 'NULL';
            } else {
              $data[$key] = $adapter->quote($value);
            }
          }
          $output .= '(' . implode(', ', $data) . ')' . ';';
          mysql_query($output);
          $output = '';
        }
        $i++;
      }
      return $output;
    } catch (Exception $e) {

      /* $table = Engine_Api::_()->getDbtable('tasks', 'core');
        $table->update(array(
        'state' => 'dormant',
        'data' => '',
        'executing' => 0,
        'executing_id' => 0,
        ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup')); */
      throw $e;
    }
  }

}
?>
