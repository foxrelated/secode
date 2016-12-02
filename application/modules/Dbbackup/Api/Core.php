<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Core.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_Api_Core extends Core_Api_Abstract {

//This function write into the backup file.
  public function writeintobackupfile($data, $path) {
    if ($data != '') {
      $gz = gzopen($path, 'ab');
      gzwrite($gz, $data);
      gzclose($gz);
    }
    $data = '';
  }

//Here we getting how many result should be shown in history page for database backup.
  public function getDbbackupsPaginator($params = array()) {
    $paginator = Zend_Paginator::factory($this->getDbbackupsSelect($params));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

//Here weselecting the database backup.
  public function getDbbackupsSelect($params = array()) {
    $table = Engine_Api::_()->getDbtable('dbbackups', 'dbbackup');
    $select = $table->select()
            ->where('backup_codemethod= ?', 1)
            ->orwhere('backup_codemethod= ?', 3)
            ->where('backup_filesize1 = ?', '')
            ->order($params['orderby']);

    return $select;
  }

//Here we getting how many result should be shown in history page for code backup.
  public function getDbbackupsPaginatorCodebackup($params = array()) {
    $paginator = Zend_Paginator::factory($this->getDbbackupsSelectCodebackup($params));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

//Here we selecting the code backup.
  public function getDbbackupsSelectCodebackup($params = array()) {
    $table = Engine_Api::_()->getDbtable('dbbackups', 'dbbackup');
    $select = $table->select()
            ->where('backup_codemethod= ?', 0)
            ->orwhere('backup_codemethod= ?', 2)
            ->where('backup_filesize1!= ?', '')
            ->order($params['orderby']);
    return $select;
  }

//Get table name
  function dbbackup_find_tablename($table) {
    $table = substr($table, 0, 150);
    $table = str_ireplace('DROP TABLE', '', $table);
    $table = str_ireplace('DROP VIEW', '', $table);
    $table = str_ireplace('CREATE TABLE', '', $table);
    $table = str_ireplace('INSERT INTO', '', $table);
    $table = str_ireplace('REPLACE INTO', '', $table);
    $table = str_ireplace('IF NOT EXISTS', '', $table);
    $table = str_ireplace('IF EXISTS', '', $table);

    $table = str_ireplace(';', ' ;', $table); // space as delimiter
    $table = trim($table);

    $specify = substr($table, 0, 1);
    if ($specify != '`')
      $specify = ' ';
    $found = false;
    $post = 1;
    while (!$found) {
      if (substr($table, $post, 1) == $specify)
        $found = true;
      if ($post >= strlen($table))
        $found = true;
      $post++;
    }
    $table = substr($table, 0, $post);
    $table = trim(str_replace('`', '', $table));
    return $table;
  }

  public function deletedatabasebackup($id) {
    $dbbackup = Engine_Api::_()->getItem('dbbackup', $id);
    $table = Engine_Api::_()->getDbtable('dbbackups', 'dbbackup');
    $select = $table->select()
            ->where('dbbackup_id = ?', $id)
            ->where('backup_codemethod = ?', 1);
    $row = $table->fetchAll($select);
    foreach ($row as $values) {
      $backup_file = $values->backup_filename;
      $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
      $path = APPLICATION_PATH . '/public/' . $dir_name_temp . '/' . $backup_file;
      if (file_exists($path)) {
        unlink($path);
      }
      if ($dbbackup)
        $dbbackup->delete();
    }
  }

// Get comment
  function dbbackup_find_dbcomments($query) {
    $array = array();
    preg_match_all("/(\/\*(.+)\*\/)/U", $query, $array);
    if (is_array($array[0])) {
      $query = str_replace($array[0], '', $query);
    }
    if ($query == ';')
      $query = '';
    return $query;
  }

  public function deletecodebackup($id) {
    $dbbackup = Engine_Api::_()->getItem('dbbackup', $id);
    $table = Engine_Api::_()->getDbtable('dbbackups', 'dbbackup');
    $select = $table->select()
            ->where('dbbackup_id = ?', $id)
            ->limit(1);
    $row = $table->fetchAll($select);

    foreach ($row as $values)
      $backup_file1 = $values->backup_filename1;
    $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
    $path1 = APPLICATION_PATH . '/public/' . $dir_name_temp . '/' . $backup_file1;
    if (file_exists($path1)) {
      unlink($path1);
    }
    if ($dbbackup)
      $dbbackup->delete();
  }

//This function return the single sql query at a time.
  public function getSql($file) {
    $session = new Zend_Session_Namespace();
    $complete_sql = '';
    $sql_line_status = 0;
    $restoreBackup['filehandle'] = $file;
    $restoreBackup['fileEOF'] = false;
    $restoreBackup['tables_to_restore'] = false;
    $temp_file_string = '';
    WHILE ($sql_line_status != 100 && !$restoreBackup['fileEOF']) {
// Get the line of file
      $temp_file_string .= $gzLine = gzgets($restoreBackup['filehandle']);
      $restoreBackup['flag'] = -1;
      if ($sql_line_status == 0) {
// Convert to gzLine into Uppercase
        $gzLine2 = strtoupper(trim($gzLine));

        $subStr9 = substr($gzLine2, 0, 9);
        $subStr7 = substr($subStr9, 0, 7);
        $subStr6 = substr($subStr7, 0, 6);
        $subStr4 = substr($subStr6, 0, 4);
        $subStr3 = substr($subStr4, 0, 3);
        $subStr2 = substr($subStr3, 0, 2);
        $subStr1 = substr($subStr2, 0, 1);

        if ($subStr7 == 'INSERT ') {
          $sql_line_status = 3;
          $restoreBackup['real_table'] = $this->dbbackup_find_tablename($gzLine);
        } elseif ($subStr7 == 'LOCK TA')
          $sql_line_status = 4;
        elseif ($subStr6 == 'COMMIT')
          $sql_line_status = 7;
        elseif (substr($subStr6, 0, 5) == 'BEGIN')
          $sql_line_status = 7;
        elseif ($subStr9 == 'UNLOCK TA')
          $sql_line_status = 4;
        elseif ($subStr3 == 'SET')
          $sql_line_status = 4;
        elseif ($subStr6 == 'START ')
          $sql_line_status = 4;
        elseif ($subStr3 == '/*!')
          $sql_line_status = 5;
        elseif ($subStr9 == 'ALTER TAB')
          $sql_line_status = 4;
        elseif ($subStr9 == 'CREATE TA')
          $sql_line_status = 2;
        elseif ($subStr9 == 'CREATE AL')
          $sql_line_status = 2;
        elseif ($subStr9 == 'CREATE IN')
          $sql_line_status = 4;
        elseif (($sql_line_status != 5) && (substr($gzLine2, 0, 2) == '/*'))
          $sql_line_status = 6;
        elseif ($subStr9 == 'DROP TABL')
          $sql_line_status = 1;
        elseif ($subStr9 == 'DROP VIEW')
          $sql_line_status = 1;
        elseif ($subStr9 == 'CREATE DA ')
          $sql_line_status = 7;
        elseif ($subStr9 == 'DROP DATA ')
          $sql_line_status = 7;
        elseif ($subStr3 == 'USE')
          $sql_line_status = 7;
        elseif (gzeof($restoreBackup['filehandle'])) {
          $restoreBackup['fileEOF'] = true;
          $gzLine = '';
          $gzLine2 = '';
          $sql_line_status = 100;
        } elseif ($subStr2 == '--' || $subStr1 == '#') {
          $gzLine = '';
          $gzLine2 = '';
          $sql_line_status = 0;
        }

        if ($restoreBackup['flag'] == 1)
          $sql_line_status = 3;

        if (($sql_line_status == 0) && (trim($complete_sql) > '') && ($restoreBackup['flag'] == -1)) {
          die('SQl:' . $gzLine . '<br><br>' . $complete_sql);
        }
      }
// Get last char of gzLine
      $last_char = substr(rtrim($gzLine), -1);
      $complete_sql.=$gzLine . "\n";

      if ($sql_line_status == 3) {
        if ($this->dbbackup_finish_sql($complete_sql)) {
          $sql_line_status = 100;
          $complete_sql = trim($complete_sql);
          if (substr($complete_sql, -2) == ');') {
            $restoreBackup['flag'] = -1;
          } else
          if (substr($complete_sql, -2) == '),') {
            $complete_sql = substr($complete_sql, 0, -1) . ';';
            $restoreBackup['flag'] = 1;
          }

          if (substr(strtoupper($complete_sql), 0, 7) != 'INSERT ') {
            if (!isset($restoreBackup['insert_syntax']))
              $restoreBackup['insert_syntax'] = $this->dbbackup_insert_format($restoreBackup['real_table']);
            $complete_sql = $restoreBackup['insert_syntax'] . ' VALUES ' . $complete_sql . ';';
          }
          else {
            $insert_position = strpos(strtoupper($complete_sql), ' VALUES');
            if (!$insert_position === false)
              $restoreBackup['insert_syntax'] = substr($complete_sql, 0, $insert_position);
            else
              $restoreBackup['insert_syntax'] = 'INSERT INTO `' . $restoreBackup['real_table'] . '`';
          }
        }
      }
      else
      if ($sql_line_status == 1) {
        if ($last_char == ';')
          $sql_line_status = 100;
        $restoreBackup['real_table'] = $this->dbbackup_find_tablename($complete_sql);
      }
      else
      if ($sql_line_status == 2) {
// Create table
        if ($last_char == ';') {
          $do_it = true;
          if (is_array($restoreBackup['tables_to_restore'])) {
            $do_it = false;
            if (in_array($restoreBackup['real_table'], $restoreBackup['tables_to_restore'])) {
              $do_it = true;
            }
          }
          if ($do_it) {

            $tablename = $this->dbbackup_create_format($complete_sql);
          }
          $complete_sql = '';
          $sql_line_status = 0;
        }
      } else
      if ($sql_line_status == 4) {
        if ($last_char == ';') {
          $complete_sql = $this->dbbackup_find_dbcomments($complete_sql);
          $sql_line_status = 100;
        }
      } else
      if ($sql_line_status == 5) {
        $t = strrpos($gzLine, '*/;');
        if (!$t === false) {
          $sql_line_status = 100;
        }
      } else
      if ($sql_line_status == 6) {
        $t = strrpos($gzLine, '*/');
        if (!$t === false) {
          $complete_sql = '';
          $sql_line_status = 0;
        }
      } else
      if ($sql_line_status == 7) {
        if ($last_char == ';') {
          $complete_sql = '';
          $sql_line_status = 0;
        }
      }

      if (gzeof($restoreBackup['filehandle']))
        $restoreBackup['fileEOF'] = true;
    }

    if (isset($session->file_size))
      $session->file_size = $session->file_size + strlen($temp_file_string);
    return trim($complete_sql);
  }

//Create table
  function dbbackup_create_format($query) {
		//Getting the table name
    $tablename = $this->dbbackup_find_tablename($query);
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
    $dbs = Engine_Db_Table::getDefaultAdapter();
    $export = Engine_Db_Export::factory($dbs);
    $connection = $export->getAdapter()->getConnection();
    $connection->query($query);

    return $tablename;
  }

  public function getModuleSubject() {
    $dbbackup_menu = hash('ripemd160', 'dbbackup');
    Engine_Api::_()->getApi('settings', 'core')->setSetting('dbbackup.menu', $dbbackup_menu);

    $db = Engine_Api::_()->getDbtable('menuitems', 'core');
    $db_name = $db->info('name');

    $db_select = $db->select()
            ->where('name =?', 'dbbackup_admin_main_destinationsettings');
    $setting_obj = $db->fetchAll($db_select)->toArray();
    if (empty($setting_obj)) {
      $db->insert(array(
              'name' => 'dbbackup_admin_main_destinationsettings',
              'module' => 'dbbackup',
              'label' => 'Destinations',
              'plugin' => NULL,
              'params' => '{"route":"admin_default","module":"dbbackup","controller":"destinationsettings"}',
              'menu' => 'dbbackup_admin_main',
              'submenu' => '',
              'custom' => 0,
              'order' => 1,
      ));
    }

    $db_select = $db->select()
            ->where('name =?', 'dbbackup_admin_main_backupsettings');
    $setting_obj = $db->fetchAll($db_select)->toArray();
    if (empty($setting_obj)) {
      $db->insert(array(
              'name' => 'dbbackup_admin_main_backupsettings',
              'module' => 'dbbackup',
              'label' => 'Take Backup',
              'plugin' => NULL,
              'params' => '{"route":"admin_default","module":"dbbackup","controller":"backupsettings"}',
              'menu' => 'dbbackup_admin_main',
              'submenu' => '',
              'custom' => 0,
              'order' => 4,
      ));
    }

    $db_select = $db->select()
            ->where('name =?', 'dbbackup_admin_main_restore');
    $setting_obj = $db->fetchAll($db_select)->toArray();
    if (empty($setting_obj)) {
      $db->insert(array(
              'name' => 'dbbackup_admin_main_restore',
              'module' => 'dbbackup',
              'label' => 'Database Restore',
              'plugin' => NULL,
              'params' => '{"route":"admin_default","module":"dbbackup","controller":"manage","action":"upload"}',
              'menu' => 'dbbackup_admin_main',
              'submenu' => '',
              'custom' => 0,
              'order' => 5,
      ));
    }

    $db_select = $db->select()
            ->where('name =?', 'dbbackup_admin_main_manage');
    $setting_obj = $db->fetchAll($db_select)->toArray();
    if (empty($setting_obj)) {
      $db->insert(array(
              'name' => 'dbbackup_admin_main_manage',
              'module' => 'dbbackup',
              'label' => 'Database Backups',
              'plugin' => NULL,
              'params' => '{"route":"admin_default","module":"dbbackup","controller":"manage"}',
              'menu' => 'dbbackup_admin_main',
              'submenu' => '',
              'custom' => 0,
              'order' => 6,
      ));
    }

    $db_select = $db->select()
            ->where('name =?', 'dbbackup_admin_main_codebackup');
    $setting_obj = $db->fetchAll($db_select)->toArray();
    if (empty($setting_obj)) {
      $db->insert(array(
              'name' => 'dbbackup_admin_main_codebackup',
              'module' => 'dbbackup',
              'label' => 'Files Backups',
              'plugin' => NULL,
              'params' => '{"route":"admin_default","module":"dbbackup","controller":"codebackup"}',
              'menu' => 'dbbackup_admin_main',
              'submenu' => '',
              'custom' => 0,
              'order' => 7,
      ));
    }
  }

//Insert formate for the sql query.
  function dbbackup_insert_format($tablename) {
    $introduce = '';
    $query = 'SHOW COLUMNS FROM `' . $tablename . '`';
    $result = mysql_query($query);
    if ($result) {
      $introduce = 'INSERT INTO `' . $tablename . '` (';
      while ($row = mysql_fetch_object($result)) {
        $introduce.='`' . $row->Field . '`,';
      }
      $introduce = substr($introduce, 0, strlen($introduce) - 1) . ') ';
    } else {
      SQLError($query, mysql_error());
    }
    return $introduce;
  }

//Checking the sql query  is complete or not.
  function dbbackup_finish_sql($chain) {
    $chain = str_replace('\\\\', '', trim($chain));
    $chain = trim($chain);
    $quotations = substr_count($chain, '\'');
    $missed_quotations = substr_count($chain, '\\\'');
    if (($quotations - $missed_quotations) % 2 == 0) {
      $compare = substr($chain, -2);
      if ($compare == '*/')
        $compare = substr(trim($this->dbbackup_delete_comment($chain)), -2);
      if ($compare == ');')
        return true;
      if ($compare == '),')
        return true;
    }
    return false;
  }

//Remove the commment
  function dbbackup_delete_comment($commentstring) {
    if (substr(trim($commentstring), -2) == '*/') {
      $position = strrpos($commentstring, '/*');
      if ($position > 0) {
        $commentstring = trim(substr($commentstring, 0, $pos));
      }
    }
    return $commentstring;
  }

//Here we deleting the old backup files.
  function deletebackupfiles() {
    $autodeleteoption = Engine_Api::_()->getApi('settings', 'core')->dbbackup_deleteoptions;
    $autodeletelimit = Engine_Api::_()->getApi('settings', 'core')->dbbackup_deletelimit;
    if ($autodeleteoption == 1) {
      $table = Engine_Api::_()->getDbtable('dbbackups', 'dbbackup');
      $select = $table->select()
              ->where('backup_codemethod = ?', 1)
              ->where('backup_filesize1= ?', '')
              ->order('backup_time DESC');
      $row = $table->fetchAll($select);
      if ($row !== null) {
        $i = 0;
        foreach ($row as $value) {
          $i++;
          if ($i <= $autodeletelimit) {
            continue;
          }
          $dbbackup_id = $value->dbbackup_id;
          Engine_Api::_()->dbbackup()->deletedatabasebackup($dbbackup_id);
        }
      }
    }
    $autodeletecodeoption = Engine_Api::_()->getApi('settings', 'core')->dbbackup_deletecodeoptions;
    $autodeletecodelimit = Engine_Api::_()->getApi('settings', 'core')->dbbackup_deletecodelimit;
    if ($autodeletecodeoption == 1) {
      $table = Engine_Api::_()->getDbtable('dbbackups', 'dbbackup');
      $select = $table->select()
              ->where('backup_codemethod = ?', 2)
              ->orwhere('backup_codemethod = ?', 0)
              ->order('backup_time DESC');
      $row = $table->fetchAll($select);
      if ($row !== null) {
        $i = 0;
        foreach ($row as $value) {
          $i++;
          if ($i <= $autodeletecodelimit) {
            continue;
          }
          $dbbackup_id = $value->dbbackup_id;
          Engine_Api::_()->dbbackup()->deletecodebackup($dbbackup_id);
        }
      }
    }
  }

//Here we getting the database backup results for history page.
  public function getdatabasebackup() {
    $table = Engine_Api::_()->getDbtable('dbbackups', 'dbbackup');
    $select = $table->select()
            ->where('backup_codemethod= ?', 1)
            ->orwhere('backup_codemethod= ?', 3)
            ->where('backup_filesize1= ?', '')
            ->order('backup_time DESC')
            ->limit(1);
    $row = $table->fetchRow($select);
    return $row;
  }

//Here we getting the files backup results for history page.
  public function getcodebackup() {
    $table = Engine_Api::_()->getDbtable('dbbackups', 'dbbackup');
    $select = $table->select()
            ->where('backup_codemethod= ?', 0)
            ->orwhere('backup_codemethod= ?', 2)
            ->where('backup_filesize1!= ?', '')
            ->order('backup_time DESC')
            ->limit(1);
    $row = $table->fetchRow($select);
    return $row;
  }

  public function time_since($time) {

    $now = time();
    $now_day = date("j", $now);
    $now_month = date("n", $now);
    $now_year = date("Y", $now);

    $time_day = date("j", $time);
    $time_month = date("n", $time);
    $time_year = date("Y", $time);
    $time_since = "";
    $lang_var = 0;

    switch (TRUE) {

      case ($now - $time < 60):
// RETURNS SECONDS
        $seconds = $now - $time;
        $time_since = $seconds;
        $lang_var = $time_since . ' second(s) ';
        break;
      case ($now - $time < 3600):
// RETURNS MINUTES
        $minutes = round(($now - $time) / 60);
        $time_since = $minutes;
        $lang_var = $time_since . ' minute(s) ';

        break;
      case ($now - $time < 86400):
// RETURNS HOURS
        $hours = round(($now - $time) / 3600);
        $time_since = $hours;
        $lang_var = $time_since . ' hour(s) ';
        break;
      case ($now - $time < 1209600):
// RETURNS DAYS
        $days = round(($now - $time) / 86400);
        $time_since = $days;
        $lang_var = $time_since . ' day(s) ';
        break;
      case (mktime(0, 0, 0, $now_month - 1, $now_day, $now_year) < mktime(0, 0, 0, $time_month, $time_day, $time_year)):
// RETURNS WEEKS
        $weeks = round(($now - $time) / 604800);
        $time_since = $weeks;

        $lang_var = $time_since . ' week(s) ';
        break;
      case (mktime(0, 0, 0, $now_month, $now_day, $now_year - 1) < mktime(0, 0, 0, $time_month, $time_day, $time_year)):

// RETURNS MONTHS
        if ($now_year == $time_year) {
          $subtract = 0;
        } else {
          $subtract = 12;
        }

        $months = round($now_month - $time_month + $subtract);

        $time_since = $months;
        $lang_var = $time_since . ' month(s) ';
        break;
      default:
// RETURNS YEARS
        if ($now_month < $time_month) {
          $subtract = 1;
        } elseif ($now_month == $time_month) {
          if ($now_day < $time_day) {
            $subtract = 1;
          } else {
            $subtract = 0;
          }
        } else {
          $subtract = 0;
        }
        $years = $now_year - $time_year - $subtract;
        $time_since = $years;
        $lang_var = $time_since . ' year(s) ';
        if ($years == 0) {
          $time_since = "";
          $lang_var = 0;
        }
        break;
    }

    return $lang_var;
  }

  function getDurration($time) {


    $hr = 0;
    $min = 0;
    $sec = 0;
    if ($time >= 3600) {
      $hr = $time / 3600;
      $time = $time % 3600;
    }

    if ($time >= 60) {

      $min = $time / 60;
      $time = $time % 60;
    }

    $sec = $time;

    $duration = '';

    if (!empty($hr)) {
      if ((int) $hr > 1)
        $duration.=(int) $hr . " Hours ";
      else
        $duration.=(int) $hr . " Hour ";
    }

    if (!empty($hr) || !empty($min)) {
      if ((int) $min > 1)
        $duration.= (int) $min . " Minutes ";
      else
        $duration.= (int) $min . " Minute ";
    }

    if (!empty($hr) || !empty($min) || !empty($sec)) {
      if ((int) $sec > 1)
        $duration.=(int) $sec . " Seconds ";
      else
        $duration.=(int) $sec . " Second ";
    }

    if (empty($hr) && empty($min) && empty($sec))
      $duration.="0.5 Second ";

    return $duration;
  }

  // For SE4.1.1
  public function canRunTask($module,$taskPlugin, $old_started_last){
     $taskTable = Engine_Api::_()->getDbtable('tasks', 'core');
     $task= $taskTable->fetchRow(array('module = ?' => $module,'plugin = ?' => $taskPlugin));
   		if($task){
   			if(time()>=($task->timeout + $old_started_last)){
   			return 1;
   			}
   		 return 0;
   		}
   return 0;
  }

}
