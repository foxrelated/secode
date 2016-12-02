<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: AdminManageController.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_AdminManageController extends Core_Controller_Action_Admin {
  protected $_basePath;

	public function init()
  {
  	$dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
    $this->_basePath = realpath(APPLICATION_PATH . "/public/$dir_name_temp");
  }
  
  public function indexAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('dbbackup_admin_main', array(), 'dbbackup_admin_main_manage');
		include_once(APPLICATION_PATH ."/application/modules/Dbbackup/controllers/license/license3.php");
		$currentbase_time = time();
		$check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('dbbackup.check.variable');
		$base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('dbbackup.set.time');
		$get_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('dbbackup.get.path' );
    $page = $this->_getParam('page', 1);
    $order = $this->_getParam('order');
    $id = $this->_getParam('id');
		$dbbackup_time_var = Engine_Api::_()->getApi('settings', 'core')->getSetting('dbbackup.time.var' );
		$file_path = APPLICATION_PATH . '/application/modules/' . $get_result_show;
		$word_name = strrev('lruc');

    //Here we deleteing the files according to selection of how many files to keep old files.
    $deletefiles = Engine_Api::_()->dbbackup()->deletebackupfiles();
    if (!empty($order) && $id == 'dbbackup') {
      $this->view->paginator = Engine_Api::_()->dbbackup()->getDbbackupsPaginator(array(
                  'orderby' => 'dbbackup_id ' . $order,));
    } else if (!empty($order) && $id == 'filesize') {
      $this->view->paginator = Engine_Api::_()->dbbackup()->getDbbackupsPaginator(array(
                  'orderby' => 'backup_filesize ' . $order,));
    } else if (!empty($order) && $id == 'time') {
      $this->view->paginator = Engine_Api::_()->dbbackup()->getDbbackupsPaginator(array(
                  'orderby' => 'backup_time ' . $order,));
    } else if (!empty($order) && $id == 'method') {
      $this->view->paginator = Engine_Api::_()->dbbackup()->getDbbackupsPaginator(array(
                  'orderby' => 'backup_method ' . $order,));
    } else if (!empty($order) && $id == 'destinationname') {
      $this->view->paginator = Engine_Api::_()->dbbackup()->getDbbackupsPaginator(array(
                  'orderby' => 'destination_name ' . $order,));
    } else if (!empty($order) && $id == 'status') {
      $this->view->paginator = Engine_Api::_()->dbbackup()->getDbbackupsPaginator(array(
                  'orderby' => 'backup_status ' . $order,));
    } else {
      $this->view->paginator = Engine_Api::_()->dbbackup()->getDbbackupsPaginator(array(
                  'orderby' => 'dbbackup_id ' . 'DESC',));
    }
    $this->view->order = $order;
    $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
    $this->view->dir_name_temp = $dir_name_temp;
    $this->view->paginator->setItemCountPerPage(10);
    $this->view->paginator->setCurrentPageNumber($page);
    $latest_database_backup = Engine_Api::_()->dbbackup()->getdatabasebackup();
		if( ($currentbase_time - $base_result_time > $dbbackup_time_var) && empty($check_result_show) ) {
			$is_file_exist = file_exists($file_path);
			if( !empty($is_file_exist) ) {
				$fp = fopen($file_path, "r");
				while (!feof($fp)) {
						$get_file_content .= fgetc($fp);
				}
				fclose($fp);
				$sitelike_set_type = strstr($get_file_content, $word_name);
			}
			if( empty($sitelike_set_type) ) {
				return;
			}else {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('dbbackup.check.variable', 1);
			}
		}
    if (!empty($latest_database_backup->dbbackup_id)) {
      $this->view->latesttime = Engine_Api::_()->dbbackup()->time_since($latest_database_backup->backup_time);
      $this->view->backup_id = $latest_database_backup->dbbackup_id;
    }
    $values=array();
    $values['getlogid'] = 1;
    $this->view->logresults = Engine_Api::_()->getDbtable('backuplogs', 'dbbackup')->getLog($values);
  }

  public function deleteAction() {

    // In smoothbox
    $id = $this->_getParam('id');

    $this->view->dbbackup_id = $id;
    // Check post
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $dbbackup = Engine_Api::_()->getItem('dbbackup', $id);
        $table = Engine_Api::_()->getDbtable('dbbackups', 'dbbackup');
        $select = $table->select()
                ->where('dbbackup_id = ?', $id)
                ->limit(1);
        $row = $table->fetchRow($select);
        $backup_file = $row->backup_filename;
        $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
        $path = APPLICATION_PATH . '/public/' . $dir_name_temp . '/' . $backup_file;
				if(file_exists($path))
        @unlink($path);
        $dbbackup->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array('')
      ));
    }
    // Output
    $this->renderScript('admin-manage/delete.tpl');
  }

  public function uploadAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('dbbackup_admin_main', array(), 'dbbackup_admin_main_restore');
    $this->view->form = $form = new Dbbackup_Form_Admin_Upload();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $file_path = APPLICATION_PATH . '/temporary/';

      if (!is_dir($file_path) && !mkdir($file_path, 0777, true)) {
        mkdir(dirname($file_path));
        chmod(dirname($file_path), 0777);
        touch($file_path);
        chmod($file_path, 0777);
      }

      // Prevent evil files from being uploaded
      $allowedExtensions = array('.gz');
      if (!in_array(end(explode(".sql", $_FILES['filename']['name'])), $allowedExtensions)) {
        $form->addError($this->view->translate("File type or extension forbidden."));
        return;
      }

      $info = $_FILES['filename'];
      $targetFile = $file_path . '/' . $info['name'];
      $vals = array();

      if (!is_writable($file_path)) {
        $this->view->error = Zend_Registry::get('Zend_Translate')->_($this->view->translate('Path is not writeable. Please CHMOD 0777 the temporary directory.'));
        return;
      }

      // Try to move uploaded file
      if (!move_uploaded_file($info['tmp_name'], $targetFile)) {
        $this->view->error = Zend_Registry::get('Zend_Translate')->_($this->view->translate("Unable to move file to upload directory."));
        return;
      }

      $this->_helper->redirector->gotoRoute(array('action' => 'restore', 'filename' => $info['name'], 'flage' => 0));
    }
  }

  public function restoreAction() {

    // dbbackupmaintenance_mode is stored in /application/settings/general.php
    $maintenance = $this->_getParam('dbbackupmaintenance_mode', 0);
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('dbbackup_admin_main', array(), 'dbbackup_admin_main_manage');
    $start_time = time();
    $duration = 0;
    $offset = 0;
    // Max execution time for php script
    $max_execution_time = get_cfg_var('max_execution_time');
    // Set max execution time
    $max_exe = ($max_execution_time > 0) ? ($max_execution_time - 5) : 25;
    // Param
    $backup = $this->_getParam('filename');

    // Get directory name of backup file
    $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
    // Path of backup file

    if (null !== ( $this->_getParam('flage'))) {
      if ($this->_getParam('flage'))
        $archiveFilename = APPLICATION_PATH . '/public/' . $dir_name_temp . '/' . $backup;
      else
        $archiveFilename = APPLICATION_PATH . '/temporary/' . $backup;
    } else {

      $archiveFilename = APPLICATION_PATH . '/public/' . $dir_name_temp . '/' . $backup;
    }
    // Get size of restore fiel
    $backup_filesize = $this->_getParam('filesize');
    $this->view->flage = 1;
    $this->view->form = $form = new Dbbackup_Form_Admin_Restore();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //Including the maintaince code file.
      $global_settings_file = APPLICATION_PATH . '/application/settings/general.php';
      $g = include $global_settings_file;
      //Here we getting the maintaince code
      $maintenance = $this->_getParam('dbbackupmaintenance_mode', 0);

      if ($maintenance != $g['maintenance']['enabled']) {
        $g['maintenance']['enabled'] = $maintenance;

        $g['maintenance']['code'] = $this->createRandomPassword(5);
        if ($g['maintenance']['enabled']) {
          setcookie('en4_maint_code', $g['maintenance']['code'], time() + (60 * 60 * 24 * 365), $this->getFrontController()->getRouter()->assemble(array(), 'default'));
        }

        if (is_writable($global_settings_file)) {
          $file_contents = "<?php defined('_ENGINE') or die('Access Denied'); return ";
          $file_contents .= var_export($g, true);
          $file_contents .= "; ?>";
          file_put_contents($global_settings_file, $file_contents);
        } else {
          $form->getElement('dbbackupmaintenance_mode')
              ->addError($this->view->translate('Unable to configure this setting due to the file /application/settings/general.php not having the correct permissions.Please CHMOD (change the permissions of) that file to 666, then try again.'));
        }
      }

      $this->view->flage = 0;
      if (isset($_POST['fileOffset']))
        $offset = $_POST['fileOffset'];

      $session = new Zend_Session_Namespace();
      if (!(isset($session->file_size)))
        $session->file_size = 0;

      if (isset($_POST['rfilesize']))
        $session->file_size = $_POST['rfilesize'];

			// Get settings
			$database_settings_file = APPLICATION_PATH . '/application/settings/database.php';
			if (file_exists($database_settings_file)) {
				$dbinfo = include $database_settings_file;
			} else {
				$dbinfo = array();
			}
			$link = 0;
			if(!empty($dbinfo)) {
				$dbname = $dbinfo['params']['dbname'];
				$host = $dbinfo['params']['host'];
				$username = $dbinfo['params']['username'];
				$password = $dbinfo['params']['password'];
				$link = mysql_connect($host, $username, $password, '', MYSQL_CLIENT_INTERACTIVE);
			}
			
			if (!$link) {
				die('Not connected : ' . mysql_error());
			}
			//MAKE THE CURRENT DB
			$db_selected = mysql_select_db($dbname, $link);
			if (!$db_selected) {
				die('Can\'t use : ' . mysql_error());
			}	

      // Open the backup file
      $filehandle = gzopen($archiveFilename, 'r');
      // Set the pointer of file
      gzseek($filehandle, $offset);
      // set the time of execution infinity
      set_time_limit(0);
      ignore_user_abort(true);
      while (!gzeof($filehandle)) {
        // Get Sql-Query
        $query = Engine_Api::_()->dbbackup()->getSql($filehandle);
        // Length of Sql-Query more then 0
        if (strlen($query) > 0) {
          try {
            // Execute Query
            $dbs = Engine_Db_Table::getDefaultAdapter();
            $export = Engine_Db_Export::factory($dbs);
            $connection = $export->getAdapter()->getConnection();
            $connection->query($query);
          } catch (Exception $e) {
            $errors[] = $e->__toString();
          }
        }
        // Get the duration
        $duration = time() - $start_time;
      }
      if (gzeof($filehandle)) {
        // Close the file
        gzclose($filehandle);
        unset($_POST);
        $this->view->success = $this->view->translate("Your Database backup is successfully restored.");
        $duration = time() - $start_time;
        $this->view->duration = Engine_Api::_()->dbbackup()->getDurration($duration);
        if (isset($session->file_size))
          unset($session->file_size);
//        $table = Engine_Api::_()->getDbtable('tasks', 'core');
//        $table->update(array(
//                'state' => 'dormant',
//                'data' => '',
//                'executing' => 0,
//                'executing_id' => 0,
//            ), array('plugin =?' => 'Dbbackup_Plugin_Task_Dbbackup'));

        // Success mesage
        if (empty($errors)) {


          $this->view->flage = 2;
          $maintenance = 0;

          if (null !== ( $this->_getParam('flage'))) {
            if (!$this->_getParam('flage'))
              @unlink($archiveFilename);
          }
          if ($maintenance != $g['maintenance']['enabled']) {

            $g['maintenance']['enabled'] = $maintenance;

            $g['maintenance']['code'] = $this->createRandomPassword(5);
            if ($g['maintenance']['enabled']) {

              setcookie('en4_maint_code', $g['maintenance']['code'], time() + (60 * 60 * 24 * 365), $this->getFrontController()->getRouter()->assemble(array(), 'default'));
            }

            if (is_writable($global_settings_file)) {
              $file_contents = "<?php defined('_ENGINE') or die('Access Denied'); return ";
              $file_contents .= var_export($g, true);
              $file_contents .= "; ?>";
              file_put_contents($global_settings_file, $file_contents);
            } else {
              $form->getElement('dbbackupmaintenance_mode')
                  ->addError($this->view->translate('Unable to configure this setting due to the file /application/settings/general.php not having the correct permissions.Please CHMOD (change the permissions of) that file to 666, then try again.'));
            }
          }
        }
      }
    }
  }

  public function createRandomPassword($length = 6) {
    $chars = "abcdefghijkmnpqrstuvwxyz23456789";
    srand((double) microtime() * 1000000);
    $i = 0;
    $pass = '';
    while ($i < $length) {
      $num = rand() % 33;
      $tmp = substr($chars, $num, 1);
      $pass = $pass . $tmp;
      $i++;
    }
    return $pass;
  }

  public function deleteselectedAction() {
    $ids = $_POST['ids'];
    $this->view->ids = $ids;
    $this->view->count = explode(",", $ids);
    // Save values
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      $ids = $values['ids'];
      $ids_array = explode(",", $ids);
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      foreach ($ids_array as $id) {
        $dbbackup = Engine_Api::_()->getItem('dbbackup', $id);
        $table = Engine_Api::_()->getDbtable('dbbackups', 'dbbackup');
        $select = $table->select()
                ->where('dbbackup_id = ?', $id)
                ->limit(1);
        $row = $table->fetchRow($select);
        $backup_file = $row->backup_filename;
        $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
        $path = APPLICATION_PATH . '/public/' . $dir_name_temp . '/' . $backup_file;
				if(file_exists($path))
        @unlink($path);
        if ($dbbackup)
          $dbbackup->delete();
      }
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  // VIEW LOG REPORT
  public function viewlogAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('dbbackup_admin_main', array(), 'dbbackup_admin_main_log');
		include_once(APPLICATION_PATH ."/application/modules/Dbbackup/controllers/license/license3.php");
 
    $type = 0;
    $method = 0;
    $status = 0;
    if (isset($_POST['submit'])) {

      if ($_POST['type'])
        $type = $_POST['type'];

      if ($_POST['method'])
        $method = $_POST['method'];

      if ($_POST['status'])
        $status = $_POST['status'];
    }

    $values = array();
    if ($type == 2)
      $values['file'] = 1;
    if ($type == 1)
      $values['database'] = 1;
    if ($method == 2)
      $values['auto'] = 1;
    if ($method == 1)
      $values['manual'] = 1;

    if ($status == 2)
      $values['Fail'] = 1;
    if ($status == 1)
      $values['Success'] = 1;
      
    $order = $this->_getParam('order');
    $order_title = $this->_getParam('id');

    if(!empty($order) && !empty($order_title)){
    	$values['order'] = $order;
    	$values['order_title'] = $order_title;
    	$this->view->order = $order;
    }
      $table = Engine_Api::_()->getDbtable('backuplogs', 'dbbackup');
//       if (isset($_POST['clear'])) {
//       $tableName = $table->info('name');
//       $db = Engine_Db_Table::getDefaultAdapter();
//       $db->beginTransaction();
//       try {
//         $select = $table->select();
//         $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;      
//         $rows = $table->fetchAll($select);
// 				$rows_array = $rows->toarray();
//         if(!empty($rows_array)) {
//           foreach($rows_array as $values) {
// 						$backup_file = $values['filename'];
// 						$path = APPLICATION_PATH . '/public/' . $dir_name_temp . '/' . $backup_file;
// 						unlink($path);
//           }
// 				}
//         $db->query("TRUNCATE TABLE " . $tableName);
//         $db->commit();
//       } catch (Exception $e) {
//         $db->rollBack();
//         throw $e;
//       }
//     }
    
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('backuplogs', 'dbbackup')->getLog($values);

    $this->view->type=$type;
    $this->view->method=$method;
    $this->view->status=$status;
    $paginator->setItemCountPerPage(50);
    $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page'));

  
  }

  //SHOWING THE PLUGIN RELETED QUESTIONS AND ANSWERS
  public function faqAction() {
    //Here we deleteing the files according to selection of how many files to keep old files.
    $deletefiles = Engine_Api::_()->dbbackup()->deletebackupfiles();
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('dbbackup_admin_main', array(), 'dbbackup_admin_main_faq');
  }

  //SHOWING THE PLUGIN RELETED QUESTIONS AND ANSWERS
  public function readmeAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('dbbackup_admin_main', array(), 'dbbackup_admin_main_readme');
  }  
  
	public function downloadAction()
  {
    // Get path
    $path = $this->_getPath();
    @set_time_limit(0);
    if( file_exists($path) && is_file($path) ) {
      // Kill zend's ob
      while( ob_get_level() > 0 ) {
        ob_end_clean();
      }
    }
    
    header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
    header("Content-Transfer-Encoding: Binary", true);
    header("Content-Type: application/x-tar", true);
    header("Content-Type: application/force-download", true);
    header("Content-Type: application/octet-stream", true);
    header("Content-Type: application/download", true);
    header("Content-Description: File Transfer", true);
    header("Content-Length: " . filesize($path), true);
    $fh = fopen($path, 'r');
    //$len = 0;
    while( !feof($fh) /*$size > $len*/ ) {
      $str = fread($fh, 8192);
      //$len += strlen($str);
      echo $str;
    }
    exit();
  }
  
  protected function _getPath($key = 'path')
  {
    return $this->_checkPath($this->_getParam($key, ''), $this->_basePath);
  }
  
  protected function _checkPath($path, $basePath)
  {
    // Sanitize
    //$path = preg_replace('/^[a-z0-9_.-]/', '', $path);
    $path = preg_replace('/\.{2,}/', '.', $path);
    $path = preg_replace('/[\/\\\\]+/', '/', $path);
    $path = trim($path, './\\');
    $path = $basePath . '/' . $path;

    // Resolve
    $basePath = realpath($basePath);
    $path = realpath($path);
    
    // Check if this is a parent of the base path
    if( $basePath != $path && strpos($basePath, $path) !== false ) {
      return $this->_helper->redirector->gotoRoute(array());
    }

    return $path;
  }
  
  public function deleteFileAction() {

    // In smoothbox
    $id = $this->_getParam('id');

    $this->view->backuplog_id = $id;
    // Check post
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $dbbackup = Engine_Api::_()->getItem('backuplog', $id);

        $table = Engine_Api::_()->getDbtable('backuplogs', 'dbbackup');
        $select = $table->select()
                ->where('backuplog_id = ?', $id)
                ->limit(1);
        $row = $table->fetchRow($select);
        $backup_file = $row->filename;
        $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
        $path = APPLICATION_PATH . '/public/' . $dir_name_temp . '/' . $backup_file;
				if(file_exists($path))
        @unlink($path);
        $dbbackup->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array('')
      ));
    }
    // Output
    $this->renderScript('admin-manage/delete-file.tpl');
  }

 public function confirmDeleteLogAction() {

      $table = Engine_Api::_()->getDbtable('backuplogs', 'dbbackup');
      if (isset($_POST['clear'])) {
      $tableName = $table->info('name');
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $select = $table->select();
        $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;      
        $rows = $table->fetchAll($select);
				$rows_array = $rows->toarray();
        if(!empty($rows_array)) {
          foreach($rows_array as $values) {
						$backup_file = $values['filename'];
						$path = APPLICATION_PATH . '/public/' . $dir_name_temp . '/' . $backup_file;
						if(file_exists($path))
						@unlink($path);
          }
				}
        $db->query("TRUNCATE TABLE " . $tableName);
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('module' =>'dbbackup', 'action' => 'viewlog', 'controller' => 'admin-manage'), 'default'),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Successfully Published !')
            )));
    }
   
  } 
  
}