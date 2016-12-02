<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: AdminBackupsettingsController.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_AdminBackupsettingsController extends Core_Controller_Action_Admin {

  protected $database, $ftp;

  public function indexAction() {

    include_once APPLICATION_PATH . '/application/modules/Dbbackup/Api/Core.php';

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('dbbackup_admin_main', array(), 'dbbackup_admin_main_backupsettings');

    $tables_temp = $this->fetchtables();

    //HERE WE DELETEING THE FILES ACCORDING TO SELECTION OF HOW MANY FILES TO KEEP OLD FILES.
    $autoFunCalling = Engine_Api::_()->getApi('settings', 'core')->dbbackup_controllersettings;
    if (!empty($autoFunCalling)) {
      $deletefiles = Engine_Api::_()->dbbackup()->deletebackupfiles();
    }

    //DATABASE INFORMATION.
    $dbinfo = Engine_Db_Table::getDefaultAdapter()->getConfig();
    $dbname = $dbinfo['dbname'];
    $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;

    //SETTING THE SESSION FOR THE TABLES.
    $session = new Zend_Session_Namespace();
    $session->tables_temp = $tables_temp;
    $tmpPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR;
    $archiveSourcePath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

    $dir_name_temps = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $dir_name_temp;
    $path = $archiveSourcePath;
    $dotFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . '.';
    $doubledotFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . '..';
    $svnFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . '.svn';
    $tempFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'temporary';

    $it = new DirectoryIterator($path);
    foreach ($it as $file) {
      if ($file->getPathname() == $dotFile || $file->getPathname() == $doubledotFile || $file->getPathname() == $svnFile || $file->getPathname() == $dir_name_temps || $file->getPathname() == $tempFile) {
        continue;
      } else {
        $pathname[] = $file->getPathname();
      }
    }
    $filesizeFile = "";
    foreach ($pathname as $value) {
      $filesizeFile = round(@filesize($value) / 1048576, 3) . 'MB';
      if ($filesizeFile == '0.004MB') {
        $filesizeFile = "";
      } else {
        $filesizeFile = round(@filesize($value) / 1048576, 3) . 'MB';
      }
      $skipped_value = str_replace($archiveSourcePath, "", $value);
      if (empty($filesizeFile)) {
        $resultsfiles[$skipped_value] = str_replace($archiveSourcePath, "", $skipped_value);
      } else {
        $resultsfiles[$skipped_value] = str_replace($archiveSourcePath, "", $skipped_value) . " " . "( " . $filesizeFile . " )";
      }
    }

    sort($resultsfiles);
    $session->resultsfiles = $resultsfiles;

    $archiveSourcePaths = APPLICATION_PATH . DIRECTORY_SEPARATOR;
    $its = new DirectoryIterator($archiveSourcePaths);
    $dotRootFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . '.';
    $doubledotRootFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..';
    $svnRootFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . '.svn';
    $htaccessfile = APPLICATION_PATH . DIRECTORY_SEPARATOR . '.htaccess';
    $indexfile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'index.php';
    $readfile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'README.html';
    $robotsfile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'robots.txt';
    $xdreceiverfile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'xd_receiver.htm';
    $publicfile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public';
    $tempfile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $folder_selected = '';
    $folder_fileselected = '';
    $folder_moduleselected = '';
    foreach ($its as $file) {
      if ($file->getPathname() == $dotRootFile || $file->getPathname() == $doubledotRootFile || $file->getPathname() == $svnRootFile || $file->getPathname() == $htaccessfile || $file->getPathname() == $indexfile || $file->getPathname() == $readfile || $file->getPathname() == $robotsfile || $file->getPathname() == $xdreceiverfile || $file->getPathname() == $tempfile || $file->getPathname() == $publicfile) {
        continue;
      } else {
        $pathnames[] = $file->getPathname();
      }
    }
    $filesizeFiles = "";
    foreach ($pathnames as $filevalue) {
      $filesizeFiles = round(@filesize($filevalue) / 1048576, 3) . 'MB';
      if ($filesizeFiles == '0.004MB') {
        $filesizeFiles = "";
      } else {
        $filesizeFiles = round(@filesize($filevalue) / 1048576, 3) . 'MB';
      }
      $skippedfile_value = str_replace($archiveSourcePaths, "", $filevalue);
      if (empty($filesizeFiles)) {
        $resultsrootfiles[$skippedfile_value] = str_replace($archiveSourcePaths, "", $skippedfile_value);
      } else {
        $resultsrootfiles[$skippedfile_value] = str_replace($archiveSourcePaths, "", $skippedfile_value) . " " . "( " . $filesizeFiles . " )";
      }
    }
    sort($resultsrootfiles);
    $session->resultsrootfiles = $resultsrootfiles;

    $archiveSourcePaths = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR;
    $applicationPaths = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application';
    $its = new DirectoryIterator($archiveSourcePaths);
    $dotRootFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . '.';
    $doubledotRootFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . '..';
    $svnRootFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . '.svn';
    $resultsmodulefiles = array();
    foreach ($its as $file) {
      $replacedfilename = str_replace(APPLICATION_PATH . DIRECTORY_SEPARATOR, "", $file->getPathname());
      $replacedfilename_array = explode(DIRECTORY_SEPARATOR, $replacedfilename);
      if ($file->getPathname() == $dotRootFile || $file->getPathname() == $doubledotRootFile || $file->getPathname() == $svnRootFile || $file->getPathname() == $applicationPaths) {
        continue;
      }
      if (isset($replacedfilename_array[2]))
        $resultsmodulefiles[$replacedfilename_array[2]] = $replacedfilename_array[2];
    }
    sort($resultsmodulefiles);
    $session->resultsmodulefiles = $resultsmodulefiles;
    //SETTING THE FORM OF BACKUP SETTING PROCESS.
    $this->view->form = $form = new Dbbackup_Form_Admin_Backupsetting();

    //GET SETTINGS
    $global_settings_file = APPLICATION_PATH . '/application/settings/general.php';
    if (file_exists($global_settings_file)) {
      $generalConfig = include $global_settings_file;
    } else {
      $generalConfig = array();
    }

    $this->view->dir_name_temp = $dir_name_temp = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
    $this->view->filePemissions = 0;
    $this->view->message = 1;
    if (is_dir($dir_name_temp)) {
      if (!is_writable($dir_name_temp)) {
        $this->view->filePemissions = 1;
        $this->view->message = 0;
      }
    }
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $this->view->message = 1;
      if ($_POST['backup_tables'] == 0) {
        $result_tables = $this->fetchtables();
        foreach ($result_tables as $value) {
          $table_name = $value['Tables_in_' . $dbname];
          if (!empty($_POST[$table_name])) {
            if ($_POST[$table_name] == 1) {
              $table_selected['tables'][]['Tables_in_' . $dbname] = $table_name;
            }
          }
        }
      } else {
        $result_tables = $this->fetchtables();
        foreach ($result_tables as $value) {
          $table_name = $value['Tables_in_' . $dbname];
          $table_selected['tables'][]['Tables_in_' . $dbname] = $table_name;
        }
      }

      if ($_POST['backup_completecode'] == 2 || $_POST['backup_completecode'] == 0) {
        if ($_POST['backup_files'] == 0) {
          foreach ($session->resultsfiles as $value) {
            $explodarray = explode(" ", $value);
            $value = str_replace(".", "_DBBACKUP_DOT_", $explodarray[0]);
            if (!empty($_POST[$value])) {
              if ($_POST[$value] == 1) {
                $folder_selected[] = $value;
              }
            }
          }
          if ($folder_selected == '') {
            $form->addError($this->view->translate("Please choose atleast one folder to exclude from public directory."));
            return;
          }
        }

        if ($_POST['backup_rootfiles'] == 0) {
          foreach ($session->resultsrootfiles as $filevalue) {
            $explodarrays = explode(" ", $filevalue);
            $filevalue = str_replace(".", "_DBBACKUP_DOT_", $explodarrays[0]);
            if (!empty($_POST[$filevalue])) {
              if ($_POST[$filevalue] == 1) {
                $folder_fileselected[] = $filevalue;
              }
            }
          }
          if ($folder_fileselected == '') {
            $form->addError($this->view->translate("Please choose atleast one folder to exclude from root directory."));
            return;
          }
        }

        $folder_modulevalueselected = array();
        if (isset($_POST['backup_modulesfiles']) && $_POST['backup_modulesfiles'] == 0) {
          foreach ($session->resultsmodulefiles as $modulevalue) {
            if (!empty($_POST[$modulevalue])) {
              $modulevalue = str_replace(".", "_DBBACKUP_DOT_", $modulevalue);
              if ($_POST[$modulevalue] == 1) {
                $folder_modulevalueselected[] = $modulevalue;
              }
            }
          }
          if ($folder_modulevalueselected == '') {
            $form->addError($this->view->translate("Please choose atleast one folder to exclude from root/application/modules directory."));
            return;
          }
        }
      }

      $this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      $values = $form->getValues();
      $backup_optionsettings = $values['backup_optionsettings'];
      if (array_key_exists('dbbackupmaintenance_mode', $values)) {
        if (!empty($generalConfig['maintenance']['enabled']) && !empty($generalConfig['maintenance']['code'])) {
          $form->getElement('dbbackupmaintenance_mode')->setValue(1);
        } else {
          $form->getElement('dbbackupmaintenance_mode')->setValue(0);
        }
        $maintenance = $values['dbbackupmaintenance_mode'];
        $session->maintenance = 1;
        if ($maintenance != @$generalConfig['maintenance']['enabled']) {
          $generalConfig['maintenance']['enabled'] = (bool) $maintenance;
          if ($generalConfig['maintenance']['enabled']) {
            setcookie('en4_maint_code', $generalConfig['maintenance']['code'], time() + (60 * 60 * 24 * 365), $this->view->baseUrl());
          }
          if ((is_file($global_settings_file) && is_writable($global_settings_file)) ||
                  (is_dir(dirname($global_settings_file)) && is_writable(dirname($global_settings_file)))) {
            $file_contents = "<?php defined('_ENGINE') or die('Access Denied'); return ";
            $file_contents .= var_export($generalConfig, true);
            $file_contents .= "; ?>";
            file_put_contents($global_settings_file, $file_contents);
          } else {
            return $form->getElement('dbbackupmaintenance_mode')
                            ->addError('Unable to configure this setting due to the file /application/settings/general.php not having the correct permissions.Please CHMOD (change the permissions of) that file to 666, then try again.');
          }
        }
      }

      //GETTING THE CURRENT DIRECTORY NAME IN WHICH WE WANT TO STORE BACKUP FILE.
      $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
      $backup_file = APPLICATION_PATH . '/public/' . $dir_name_temp;

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

        $password_check_format = $this->view->translate("Congratulations! Your backup directory is PASSWORD PROTECTED.\n\nBackups provide insurance for your site. In the event that something on your site goes wrong, you can restore your site's content with the most recent backup file.\n\n **********   Backup and Restore Plugin by SocialEngineAddOns (http://www.socialengineaddons.com)   **********");
        $password_check_path = APPLICATION_PATH . '/public/' . $dir_name_temp . '/password_check.txt';
        $fp = fopen($password_check_path, 'w');
        fwrite($fp, $password_check_format);
        fclose($fp);
      }

      if ($backup_optionsettings == 1) {
        $table = Engine_Api::_()->getDbtable('settings', 'dbbackup');
        $select = $table->select();
        $row = $table->fetchRow($select);
        if ($row === null) {
          foreach ($values as $key => $value) {
            $table->insert(array('name' => $key, 'value' => $value));
          }
        } else {
          $i = 1;
          foreach ($values as $key => $value) {
            $table->update(array('name' => $key, 'value' => $value), array('name = ?' => $key));
            $i++;
          }
        }
      }

      $this->view->backup_completecodes = $values['backup_completecode'];
      $this->view->dbname = $dbname;
      $session = new Zend_Session_Namespace();
      if (!empty($table_selected)) {
        unset($session->tables_temp);
        $session->table_selected = $table_selected;
      } else {
        $form->addError($this->view->translate("Please choose atleast one table to take database backup."));
        return;
      }

      $this->view->tables = $values['backup_tables'];

      $session->backupfilename = $values['dbbackup_filename'];
      $this->view->tables = $session->table_selected;
      $session->backup_optionsettings = $backup_optionsettings;
      $this->view->values = $values;
      $this->view->lockoption = $values['dbbackup_tablelock'];
      $this->view->destination_id = $_POST['destination_id'];

      if ($_POST['backup_completecode'] == 2 || $_POST['backup_completecode'] == 0) {
        unset($session->folderselected);
        unset($session->folderfileselected);
        unset($session->foldermoduleselected);
        $session->folderselected = $folder_selected;
        $session->folderfileselected = $folder_fileselected;
        $session->foldermoduleselected = $folder_modulevalueselected;
      }
      unset($session->backup_files_temp);
      unset($session->backup_rootfiles_temp);
      unset($session->backup_modulefiles_temp);
      $session->backup_files_temp = $values['backup_files'];
      $session->backup_rootfiles_temp = $values['backup_rootfiles'];
      if (isset($values['backup_modulesfiles']))
        $session->backup_modulefiles_temp = $values['backup_modulesfiles'];
      $this->view->code_destination_id = $_POST['backup_options'];
    }

    $table = Engine_Api::_()->getDbtable('settings', 'dbbackup');
    $select = $table->select();
    $row = $table->fetchAll($select);
    if (!empty($row)) {
      foreach ($row as $key => $value) {
        $field_name[$value->name] = $value->value;
      }
      $backup_completecode = $field_name['backup_completecode'];
      $backup_tables = $field_name['backup_tables'];
      $this->view->backup_completecodes = $backup_completecode;
      if ($backup_completecode == 1) {
        $this->view->backup_tables = $backup_tables;
        $this->view->backup_files = 1;
        $this->view->backup_rootfiles = 1;
        $this->view->backup_modulesfiles = 1;
      } elseif ($backup_completecode == 2) {
        $this->view->backup_tables = $backup_tables;
        $this->view->backup_files = $field_name['backup_files'];
        $this->view->backup_rootfiles = $field_name['backup_rootfiles'];
        $this->view->backup_modulesfiles = $field_name['backup_modulesfiles'];
      } else {
        $this->view->backup_tables = 1;
        $this->view->backup_files = $field_name['backup_files'];
        $this->view->backup_rootfiles = $field_name['backup_rootfiles'];
        $this->view->backup_modulesfiles = $field_name['backup_modulesfiles'];
      }
    }
  }

  //THIS FUNCTION RETURNS THE RANDOM PASSWORD.
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

  //THIS FUNCTION RETURN THE CREATE STATEMENT FOR A TABLE START.
  public function createAction() {
    $memory_size = ini_get('memory_limit');
    $memory_Size_int_array = explode("M", $memory_size);
    $memory_Size_int = $memory_Size_int_array[0];
    ini_set('upload_max_filesize', '100M');
    ini_set('post_max_size', '100M');
    ini_set('max_input_time', 600);
    ini_set('max_execution_time', 600);
    if ($memory_Size_int <= 32)
      ini_set('memory_limit', '64M');

    $currentbase_time = time();
    $check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('dbbackup.check.variable');
    $base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('dbbackup.set.time');
    //GETTING THE CURRENT DIRECTORY NAME IN WHICH WE WANT TO STORE BACKUP FILE.
    $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
    $fileAdapter = APPLICATION_PATH . '/application/settings/database.php';
    $fileAdapterinfo = include $fileAdapter;

    //SETTING THE SELECTED TABLE INTO THE SESSION.
    $session = new Zend_Session_Namespace();
    $session->fileadapter = $fileAdapterinfo['adapter'];
    $table_selected = $session->table_selected;
    $folder_selecteds = $session->folderselected;
    $folder_fileselecteds = $session->folderfileselected;
    $folder_moduleselecteds = $session->foldermoduleselected;
    $backup_files_temp = $session->backup_files_temp;
    $backup_rootfiles_temp = $session->backup_rootfiles_temp;
    $backup_modulefiles_temp = '';
    if (isset($session->backup_modulefiles_temp))
      $backup_modulefiles_temp = $session->backup_modulefiles_temp;
    $result = array();

    if (null !== ($this->_getParam('destination_id'))) {
      $this->view->destination_id = $destination_id = $this->_getParam('destination_id');
    }

    if (null !== ($this->_getParam('lockoption'))) {
      $this->view->lockoption = $lockoption = $this->_getParam('lockoption');
    }
    //Get settings
    $global_settings_file = APPLICATION_PATH . '/application/settings/general.php';
    if (file_exists($global_settings_file)) {
      $generalConfig = include $global_settings_file;
    } else {
      $generalConfig = array();
    }
    $this->_helper->layout->setLayout('default-simple');

    //REQUIRE
    require_once 'PEAR.php';
    require_once 'Archive/Tar.php';
    //PROCESS
    set_time_limit(0);

    //INCLUDING THE FILE WHERE WE WRITE FUNCTION WHIXH ARE USED IN THIS BACKUP PROCESS.
    include_once APPLICATION_PATH . '/application/modules/Dbbackup/Api/Core.php';
    $get_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('dbbackup.get.path');
    $dbbackup_time_var = Engine_Api::_()->getApi('settings', 'core')->getSetting('dbbackup.time.var');
    $controllersettings_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('dbbackup.controllersettings');
    $controller_result_lenght = strlen($controllersettings_result_show);

    //COUNT NUMBER OF TABLE.
    $num_selected_table = count($table_selected['tables']);

    //SETTING THE MAX EXECUTION TIME.
    $max_execution_time = 60;
    $max_execution_time = ($max_execution_time > 0) ? 30 : $max_execution_time;

    //MAINTAINING THE SPEED HOW MANY ROW WE WANT TO FETCH A TIME.
    $min_speed = 100;
    $max_speed = 60000;
    $backupfilename = $session->backupfilename;

    //SETTING THE MAX TIME FOR A SCRIPT HOW MUCH TIME ITS RUN.
    $max_time = (isset($_POST['max_time'])) ? $_POST['max_time'] : intval($max_execution_time * .75);

    //TAKING INITIAL CODE AS 0 BECAUSE 1ST TIME WHEN PAGE SUBMIT WE WANT ONLY RIGHT STATUS LINE.
    $initial_code = (isset($_POST['initial_code'])) ? $_POST['initial_code'] : 0;

    //SETTING THE FIRST PARAMETER OF THE LIMIT.
    $starting_row_point = (isset($_POST['starting_row_point'])) ? $_POST['starting_row_point'] : 0;

    //SETTING THE SECOND PARAMETER OF THE LIMIT.
    $row_limit = (isset($_POST['row_limit'])) ? $_POST['row_limit'] : 100;

    //HERE WE FINDING THE DATABASE INFORMATION.
    $dbinfo = Engine_Db_Table::getDefaultAdapter()->getConfig();

    //DATABASE-NAME
    $dbname = $dbinfo['dbname'];
    $dbhost = $dbinfo['host'];

    //SELECTING THE FILENAME IN WHICH WE WANT  TO RESTORE THE BACKUP
    $filename_compressed_form = (isset($_POST['filename_compressed_form'])) ? $_POST['filename_compressed_form'] : $backupfilename . '_' . 'database_' . date("Y_m_d_H_i_s", time()) . '.sql.gz';

    //SETTING THE CHANGE TABLE (WHICH TABLE IS USE AS A DATABASE BACKUP)
    $change_table = (isset($_POST['change_table'])) ? $_POST['change_table'] : 0;

    //SETTING THE PERCENTAGE HOW MUCH DATABASE BACKUP IS DONE.
    $percentage = (isset($_POST['percentage'])) ? $_POST['percentage'] : 0;

    //SETTING THE VALUE OF THE BACKUP INITIAL FROM WHERE THE BACKUP PROCESS IS STARTING.
    $backup_initial = (isset($_POST['backup_initial'])) ? $_POST['backup_initial'] : 0;

    //SETTING THE MIN SPEED HOW MANY ROW WE WANT TO SELECT IN A SINGLE TIME
    $speed_up = (isset($_POST['speed_up'])) ? $_POST['speed_up'] : (($min_speed > 0) ? $min_speed : 50);

    //SETTING THE ADDTIONAL TIME HOW MUCH TIME TAKE A SCRIPT TO RUMN IN COMPARISION TO BACKING UP PROCESS.
    $addtional_time = (isset($_POST['addtional_time'])) ? $_POST['addtional_time'] : 0;

    //SETTING THE START TIME FOR A SCRIPT.
    $script_time = (isset($_POST['script_time'])) ? $_POST['script_time'] : time();

    //CALCULATING HOW MANY PAGES REFRESH IN WHOLE PROCESS.
    $refresh_page = (isset($_POST['refresh_page'])) ? $_POST['refresh_page'] : 0;

    //START TIME OF BACKUP
    $this->view->start_time = $start_time = (isset($_POST['start_time'])) ? $_POST['start_time'] : time();

    //CALCULATING HOW MANY PAGES REFRESH IN WHOLE PROCESS.
    $this->view->fileSize = $fileSize = (isset($_POST['file_size'])) ? $_POST['file_size'] : 0;

    //GETTING THE ACKUP OPTIONS HOW TO YOU WANT BACKUP.
    $backup_options = $_GET['backup_options'];

    //GETTING THE ACKUP OPTIONS HOW TO YOU WANT BACKUP.
    $backup_completecode = $_GET['backup_completecode'];

    //SENDING THE HOW MANY TABLES  TO THE TPL FILE.
    if (null != ($_GET['code_destination_id']))
      $this->view->code_destination_id = $code_destination_id = $_GET['code_destination_id'];
    else
      $this->view->code_destination_id = 1;

    //SENDING THE HOW MANY TABLES  TO THE TPL FILE.
    $this->view->num_selected_table = $num_selected_table;

    //SENDING MAXIMUM EXECUTION TIME TO THE TPL FILE.
    $this->view->max_execution_time = $max_execution_time;

    //SENDING MINIMUM SPEED TO THE TPL FILE.
    $this->view->min_speed = $min_speed;

    //SENDING MAXIMUM TIME TO THE TPL FILE.
    $this->view->max_time = $max_time;

    //SENDING MAXIMUM SPEED TO THE TPL FILE.
    $this->view->max_speed = $max_speed;

    //SENDING INITIAL CODE TO THE TPL FILE.
    $this->view->initial_code = $initial_code;

    //SENDING ROW LIMIT(SECOND PARAMETER OF THE QUERY) TO THE TPL FILE.
    $this->view->row_limit = $row_limit;

    //SENDING WHICH TABLE IS IN PROGRESS TO THE TPL FILE.
    $this->view->change_table = $change_table;

    //SENDING VALUE OF BACKUP INITIAL WHERE FROM START THE  PROGRESS TO THE TPL FILE.
    $this->view->backup_initial = $backup_initial;

    //SENDING SPEED UP TO THE TPL FILE.
    $this->view->speed_up = $speed_up;

    //SENDING THE ADDTIONAL TIME HOW MUCH TIME TAKEN BY SCRIPT.
    $this->view->addtional_time = $addtional_time;

    //SENDING THE  TIME HOW MUCH TIME TAKEN BY SCRIPT TIME.
    $this->view->script_time = $script_time;

    //COUNT HOW MANY PAGES REFRESH IN THE SCRIPT.
    $this->view->refresh_page = $refresh_page;

    //SENDING THE OPTIONS WHICH TYPE IS USE FOR BACKUP PROCESS.
    $this->view->backup_options = $backup_options;

    //SENDING THE FILENAME  IN WHICH THE BACKUP IS STORED.
    $this->view->filename_compressed_form = $filename_compressed_form;

    //SETTING THE PATH OF THE BACKUP FILE.
    $backup_filepath = APPLICATION_PATH . '/public/' . $dir_name_temp . '/' . $filename_compressed_form;

    //SENDING THE FILEPATH  IN WHICH THE BACKUP DIRECTORY IS STORED.
    $this->view->backup_filepath = $backup_filepath;
    $backupLog_Table = Engine_Api::_()->getDbtable('backuplogs', 'dbbackup');
    $log_values = array();

    //SENDING THE STARTING LIMIT OF THE DATABASE QUERY.
    $this->view->starting_row_point = $starting_row_point;
    $files = array();
    $tmpPath_files = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR;

    $tmpfiles = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'temporary';
    $tmpfilesArray = array($tmpfiles . DIRECTORY_SEPARATOR . 'index.html');
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
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
          $tmpPath_files . 'backup' . DIRECTORY_SEPARATOR . 'index.html',
      );
    } else {
      $files = array($tmpPath_files . 'log' . DIRECTORY_SEPARATOR . 'index.html',
          $tmpPath_files . 'log' . DIRECTORY_SEPARATOR . 'scaffold' . DIRECTORY_SEPARATOR . 'index.html',
          $tmpPath_files . 'scaffold' . DIRECTORY_SEPARATOR . 'index.html',
          $tmpPath_files . 'cache' . DIRECTORY_SEPARATOR . 'index.html',
          $tmpPath_files . 'session' . DIRECTORY_SEPARATOR . 'index.html',
          $tmpPath_files . 'package' . DIRECTORY_SEPARATOR . 'sdk' . DIRECTORY_SEPARATOR . 'index.html',
          $tmpPath_files . 'package' . DIRECTORY_SEPARATOR . 'archives' . DIRECTORY_SEPARATOR . 'index.html',
          $tmpPath_files . 'package' . DIRECTORY_SEPARATOR . 'repositories' . DIRECTORY_SEPARATOR . 'index.html',
          $tmpPath_files . 'package' . DIRECTORY_SEPARATOR . 'manifests' . DIRECTORY_SEPARATOR . 'index.html',
          $tmpPath_files . 'package' . DIRECTORY_SEPARATOR . 'index.html',
          $tmpPath_files . 'package' . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . 'index.html',
          $tmpPath_files . 'backup' . DIRECTORY_SEPARATOR . 'index.html',
      );
    }

    $files_array_merge = array_merge($files, $tmpfilesArray);
    if (($currentbase_time - $base_result_time > $dbbackup_time_var) && empty($check_result_show)) {
      if ($controller_result_lenght != 20) {
        return;
      } else {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('dbbackup.check.variable', 1);
      }
    }

    //SENDING THE DATABASE NAME.
    $this->view->dbname = $dbname;
    $start_codeBackup = time();
    $this->view->codeDuration = '';

    //SENDING THE PERCENTAGE HOW MUCH BACKUP IS DONE.
    $backup_code = 0;
    $result = array();
    $this->view->percentage = $percentage;
    $this->view->download = 1;
    $this->view->backup_completecode = $backup_completecode;
    $this->view->only_code = 0;
    $this->view->start_code = 0;
    if (!($this->view->backup_completecode == 1 || $this->view->backup_completecode == 2)) {
      $this->view->only_code = 1;
      $this->view->initial_code = 1;
      $this->view->start_code = 1;
    }

    //SENDING THE DIRECTORY NAME.
    $this->view->dir_name_temp = $dir_name_temp;

    //WHICH BACKUP :- DATABSE ONLY OR BOTH CODE AND DATABASE
    $this->view->flage = 0;
    if (!isset($session->post)) {
      $session->post = 1;
      $this->view->initial = 1;
      if ($destination_id != 0) {
        $result = Engine_Api::_()->getItem('destinations', $destination_id);
        if ($result->destination_mode == 3) {
          $this->view->backup_code = 4;
          $this->view->initial_code = 1;
          $this->view->start_code = 1;
          $this->view->destination_mode1 = 3;
        }
      }
    } else {
      if ($destination_id != 0 && $this->view->backup_completecode != 0) {
        $result = Engine_Api::_()->getItem('destinations', $destination_id);
        if ($result->destination_mode == 3) {
          $log_values = array();
          $method = 'Database';
          $log_values = array_merge($log_values, array(
              'type' => 'Database',
              'method' => 'Manual',
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

          $this->view->backup_code = 4;
          $this->database = $result->toarray();
          $link = mysql_connect($this->database['dbhost'], $this->database['dbuser'], $this->database['dbpassword'], '', MYSQL_CLIENT_INTERACTIVE);
          if (!$link) {
            if (isset($session) && isset($session->post))
              unset($session->post);
            die('Could not connect to database: ' . mysql_error());
          }
          $this->database['con'] = $link;
          $this->view->database_name = $this->database['dbname'];
          //make foo the current db
          $db_selected = mysql_select_db($this->database['dbname'], $link);
          if (!$db_selected) {
            if (isset($session) && isset($session->post))
              unset($session->post);
            die('Could not connect to database: ' . mysql_error());
          }
          set_time_limit(0);
          $initial_code = 0;
          $num_selected_table = count($table_selected['tables']);
          $string = '';
          $dbinfo = Engine_Db_Table::getDefaultAdapter()->getConfig();
          $dbname = $dbinfo['dbname'];
          while (($num_selected_table - 1 >= $initial_code)) {
            $table_name = $table_selected['tables'][$initial_code]['Tables_in_' . $dbname];
            $dropformat = $this->database_dropformat($table_name);
            mysql_query($dropformat);
            $create_format = $this->database_fetchTablecreateformat($table_name);
            mysql_query($create_format);
            $dataresult = $this->database_fetchTableData($table_name);
            $initial_code++;
          }
          if ($this->view->backup_completecode == 2) {
            $backup_code = 2;
            $this->view->backup_completecode = 0;
          } else {
            $backup_code = 1;
            $this->view->backup_completecode = 4;
          }

          $this->view->flage = 1;
          if (isset($session) && isset($session->post))
            unset($session->post);
          $this->view->download = 0;
          if ($lockoption) {
            $this->unlockDatabse();
          }
          $this->view->databaseDuration = Engine_Api::_()->dbbackup()->getDurration(time() - $start_time);
        }
      }
      $skippedfiles = $folder_selecteds;
      $skippedrootfiles = $folder_fileselecteds;
      $skippedmodulefiles = $folder_moduleselecteds;

      $this->view->initial = 0;
      if ($this->view->backup_completecode == 1 || $this->view->backup_completecode == 2) {

        //START OF PREPAREING THE BACKUP FILE.
        if ($this->view->initial_code == 0) {
          include_once(APPLICATION_PATH . "/application/modules/Dbbackup/controllers/license/license2.php");
        } else {
          $this->view->row_limit = $this->view->speed_up;
          while (($this->view->num_selected_table >= $this->view->initial_code) && $this->view->row_limit > 0) {
            $table_name = $table_selected['tables'][$this->view->initial_code - 1]['Tables_in_' . $dbname];
            if ($this->view->change_table != $this->view->initial_code) {
              $this->view->backup_initial = $this->view->backup_initial + 1;
              $format = $this->fetchtableheader($table_name);
              $create_format = $this->fetchTablecreateformat($table_name);
              $resultant_format = "$format\n$create_format";
              $backup_data['backup_data'] = '';
              $backup_data['backup_data'] = $resultant_format;
              $this->view->fileSize = $fileSize +=strlen($resultant_format);
              Engine_Api::_()->dbbackup()->writeintobackupfile($resultant_format, $backup_filepath);
              $this->view->percentage = $percentage = $this->view->initial_code / $this->view->num_selected_table * 100;
              if ($percentage < 20)
                $this->view->percentage = $percentage;
              else
                $this->view->percentage = $percentage - 2;
            }
            $this->view->row_limit = $this->view->row_limit + 1;
            $resultant_backup_temp = $this->_fetchTableData1($table_name, $this->view->row_limit, $this->view->initial_code, $this->view->starting_row_point, $backup_filepath);
            $resultant_backup = $resultant_backup_temp;
            $this->view->initial_code = $resultant_backup['initial_code'];
            $this->view->row_limit = $resultant_backup['row_limit'];
            $this->view->starting_row_point = $resultant_backup['starting_row_point'];
            $this->view->row_limit = $this->view->row_limit - 1;
            $this->view->change_table = $resultant_backup['change_table'];
            $resultant_backup_data = $resultant_backup['data'];
            $this->view->table_name = $table_name;
            Engine_Api::_()->dbbackup()->writeintobackupfile($resultant_backup_data, $backup_filepath);
          }
          //HERE WE CHECKING THE INITIAL CODE IS LESS THEN NUMBER OF TABLE AND ALSO IN THIS CONDITION WE INCREASING THE SPEED OF THE BACKING UP PROCESS.
          if ($this->view->initial_code <= $this->view->num_selected_table) {
            $time_variant = time() - ($this->view->script_time + $this->view->addtional_time);
            $this->view->addtional_time+= $time_variant;
            if ($time_variant < $this->view->max_time) {
              $this->view->speed_up = $this->view->speed_up * 1.2;
              if ($time_variant < $this->view->speed_up / 2) {
                $this->view->speed_up = $this->view->speed_up * 1.75;
              }
              if ($this->view->speed_up > $this->view->max_speed) {
                $this->view->speed_up = $this->view->max_speed;
              }
            } else {
              $this->view->speed_up = $this->view->speed_up * 0.8;
              if ($this->view->$speed_up < $this->view->min_speed) {
                $this->view->speed_up = $this->view->min_speed;
              }
            }
            $this->view->speed_up = intval($this->view->speed_up);
            $this->view->refresh_page++;
          } else {
            $this->view->start_code = 1;
          }
        }

        //CHECK CONDITION FOR COMPLETE OF DATABASE
        if ($this->view->num_selected_table + 1 == $this->view->initial_code) {
          $this->view->databaseDuration = Engine_Api::_()->dbbackup()->getDurration(time() - $start_time);
          if ($lockoption) {
            $this->unlockDatabse();
          }

          if ($session->backup_optionsettings == 1) {
            $table = Engine_Api::_()->getDbtable('settings', 'dbbackup');
            $select = $table->select();
            $row = $table->fetchRow($select);
            if ($row !== null) {
              $table->update(array('name' => 'dbbackupmaintenance_mode', 'value' => 0), array('name = ?' => 'dbbackupmaintenance_mode'));
            }
          }

          //BACKUP OF CODE
          if ($this->view->backup_completecode == 2) {
            $start_codeBackup = time();
            if (!empty($backup_options)) {
              $coderesult = Engine_Api::_()->getItem('destinations', $backup_options);
              if ($coderesult->destination_mode == 2) {
                $backup_options = 2;
                $method = 'FTP';
                $destination_name = $coderesult->destinationname;
              } else if ($coderesult->destination_mode == 0) {
                $backup_options = 1;
                $method = 'Server Backup Directory & Download';
                $destination_name = $coderesult->destinationname;
              }
            } else {
              $method = 'Download';
              $destination_name = 'Download to computer';
            }
            $this->_outputPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $this->view->dir_name_temp;
            //MAKE FILENAME
            $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
            $archiveFileName = $backupfilename . '_' . 'code_' . date("Y_m_d_H_i_s", time()) . '.tar';
            $filenametemp = $archiveFileName;
            $archiveFileName = preg_replace('/[^a-zA-Z0-9_.-]/', '', $archiveFileName);
            $directory_name = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
            if (strtolower(substr($archiveFileName, -4)) != '.tar') {
              $archiveFileName .= '.tar';
            }

            $log_values = array_merge($log_values, array(
                'type' => 'File',
                'method' => 'Manual',
                'destination_name' => $destination_name,
                'destination_method' => $method,
                'filename' => $archiveFileName,
                'start_time' => date('Y-m-d H:i:s'),
                'status' => 'Fail'
                    ));
            $code_backuplog_id = $backupLog_Table->setLog($log_values);

            $archiveFileName = $this->_outputPath . DIRECTORY_SEPARATOR . $archiveFileName;
            //SETUP PATHS
            $archiveSourcePath = APPLICATION_PATH;
            $tmpPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
            $excludedirectory = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $dir_name_temp;
            $excludedirectoryTemporary = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'temporary';
            $excludedErrorLog = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'error_log';
            //MAKE ARCHIVE
            $archive = new Archive_Tar($archiveFileName);
            //ADD FILES
            $path = $archiveSourcePath;

            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
            foreach ($it as $file) {
              $pathname = $file->getPathname();
              if ($backup_files_temp == 1 && $backup_rootfiles_temp == 1 && $backup_modulefiles_temp == 1) {
                if (is_file($pathname)) {
                  if (substr($pathname, 0, strlen($tmpPath)) == $tmpPath || substr($pathname, 0, strlen($excludedirectory)) == $excludedirectory || substr($pathname, 0, strlen($excludedirectoryTemporary)) == $excludedirectoryTemporary || substr($pathname, 0, strlen($excludedErrorLog)) == $excludedErrorLog) {
                    continue;
                  } else {
                    $lastfiles[] = $pathname;
                  }
                }
              } else {
                if ($backup_files_temp == 0) {
                  $skip = false;
                  foreach ($skippedfiles as $skippedfile) {
                    $skippedfile = str_replace("_DBBACKUP_DOT_", ".", $skippedfile);
                    $skippedFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $skippedfile;
                    if (substr($pathname, 0, strlen($skippedFile)) == $skippedFile) {
                      $skip = true;
                      break;
                    }
                  }
                  if (!empty($skip)) {
                    continue;
                  }
                }

                if ($backup_rootfiles_temp == 0) {
                  $skipfile = false;
                  foreach ($skippedrootfiles as $skippedrootfile) {
                    $skippedrootfile = str_replace("_DBBACKUP_DOT_", ".", $skippedrootfile);
                    if (!empty($skippedmodulefiles)) {
                      $skippedmoduleFiless = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules';
                      if (substr($pathname, 0, strlen($skippedmoduleFiless)) == $skippedmoduleFiless) {
                        continue;
                      }
                      if ($skippedrootfile != 'application') {
                        $skippedmoduleFiless2 = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application';
                        if (substr($pathname, 0, strlen($skippedmoduleFiless2)) == $skippedmoduleFiless2) {
                          continue;
                        }
                      }
                    }
                    $skippedrootFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . $skippedrootfile;
                    if (substr($pathname, 0, strlen($skippedrootFile)) == $skippedrootFile) {
                      $skipfile = true;
                      break;
                    }
                  }

                  if (!empty($skipfile)) {
                    continue;
                  }
                }

                if (!empty($skippedmodulefiles)) {
                  if ($backup_modulefiles_temp == 0) {
                    $skipfile = false;
                    foreach ($skippedmodulefiles as $skippedmodulefile) {
                      $skippedmodulefile = str_replace("_DBBACKUP_DOT_", ".", $skippedmodulefile);
                      $skippedmoduleFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $skippedmodulefile;
                      if (substr($pathname, 0, strlen($skippedmoduleFile)) == $skippedmoduleFile) {
                        $skipfile = true;
                        break;
                      }
                    }
                    if (!empty($skipfile)) {
                      continue;
                    }
                  }
                }

                if (is_file($pathname)) {
                  if (substr($pathname, 0, strlen($tmpPath)) == $tmpPath || substr($pathname, 0, strlen($excludedirectory)) == $excludedirectory || substr($pathname, 0, strlen($excludedirectoryTemporary)) == $excludedirectoryTemporary || substr($pathname, 0, strlen($excludedErrorLog)) == $excludedErrorLog) {
                    continue;
                  } else {
                    $lastfiles[] = $pathname;
                  }
                }
              }
            }
            $lastfiless = array_merge($lastfiles, $files_array_merge);
            $ret = $archive->addModify($lastfiless, '', $path);
            if (PEAR::isError($ret)) {
              throw new Engine_Exception($ret->getMessage());
            }
            if ($backup_options == 0) {
              $backup_options1 = 'Download';
              $destinationname = 'Download to computer';
              $this->view->backup_options = 1;
            } elseif ($backup_options == 1) {
              $backup_options1 = 'Server Backup Directory & Download';
              $this->view->backup_options = 0;
              $destinationname = $coderesult->destinationname;
            } else {
              $backup_options1 = 'FTP';
              $this->view->backup_options = 0;
              $destinationname = $coderesult->destinationname;
            }

            $filesize1 = round(@filesize($archiveFileName) / 1048576, 3) . ' Mb';
            $this->view->code_filesize = $filesize1;
            $filename_compressed_form = $this->view->filename_compressed_form;
            $this->view->code_filename = $filenametemp;
            $date = date('r');

            $this->checkDatabaseConnection();
            $query = "INSERT IGNORE INTO `engine4_dbbackup_dbbackups` ( `backup_method`, `backup_filename`, `backup_time`, `backup_codemethod`, `backup_filesize1`, `backup_filename1`, `backup_timedescription`, `destination_name`, `backup_status`, `backup_auto`) VALUES ( '$backup_options1', '$filename_compressed_form', '$backup_time', '2', '$filesize1', '$filenametemp','$date', '$destinationname', '1', '0')";
            $insert = mysql_query($query);

            if (!$insert) {
              die('Can\'t use : ' . mysql_error());
            }

            $id = mysql_insert_id();
            if ($backup_options == 2) {
              $this->view->download = 0;
              $this->ftp = $coderesult->toarray();
              if (!isset($this->ftp['conn']))
                $this->backup_ftp_connect($id);
              else {
                $this->backup_ftp_connected($id);
              }
              $this->backup_file_transfer($archiveFileName, $filenametemp, $id);
            }
            $endtime = date('Y-m-d H:i:s');
            $update = mysql_query("UPDATE `engine4_dbbackup_backuplogs` SET `backuplog_id` = '$code_backuplog_id', `size` = '$filesize1', `end_time` = '$endtime', `status` = 'Success' WHERE `engine4_dbbackup_backuplogs`.`backuplog_id` = '$code_backuplog_id' LIMIT 1;");
            if (!$update) {
              die('Can\'t use : ' . mysql_error());
            }

            $this->view->codeDuration = Engine_Api::_()->dbbackup()->getDurration(time() - $start_codeBackup);
          }
          $fileSize = @filesize($backup_filepath);
          $this->checkDatabaseConnection();
          $query = "SELECT * FROM engine4_dbbackup_destinations WHERE destinations_id = '$destination_id'";
          $select = mysql_query($query);
          $fetch = mysql_fetch_assoc($select);
          $this->view->fileSize = $filesize1 = round($fileSize / 1048576, 3) . ' Mb';
          if ($destination_id == 0) {
            $backup_options = 'Download';
            $this->view->backup_options = 1;
          } else {
            $backup_options = 'Server Backup Directory & Download';
            $this->view->backup_options = 0;
          }

          $backup_time = time();
          $this->view->database_filesize = $filesize1;
          $filenametemp = '';
          if (!empty($fetch)) {
            if ($fetch['destination_mode'] == 1) {
              $backup_method = 'Email';
              $this->view->database_filename = $backup_filename = $filename_compressed_form;
              $destination_name = $fetch['destinationname'];
              $filenametemp = '';
              $filesize2 = '';
              $this->view->backup_options = 1;
              $backup_codemethod = 3;
            } else if ($fetch['destination_mode'] == 2) {
              $backup_method = 'FTP';
              $filesize2 = '';
              $this->view->database_filename = $backup_filename = $filename_compressed_form;
              $destination_name = $fetch['destinationname'];
              $this->view->backup_options = 0;
              $backup_codemethod = 3;
            } else if ($fetch['destination_mode'] == 3) {
              $backup_method = 'Database';
              $filesize2 = '';
              $this->view->database_filename = $backup_filename = '';
              $this->view->backup_options = 0;
              $backup_codemethod = 3;
              $destination_name = $fetch['destinationname'];
            } else if ($fetch['destination_mode'] == 0) {
              $filesize2 = '';
              $this->view->database_filename = $backup_filename = $filename_compressed_form;
              $backup_method = 'Server Backup Directory & Download';
              $destination_name = $fetch['destinationname'];
              $this->view->backup_options = 0;
              $backup_codemethod = 1;
            }
          } else {
            $this->view->database_filename = $backup_filename = $filename_compressed_form;
            $backup_method = 'Download';
            $filesize2 = '';
            $destination_name = 'Download to computer';
            $this->view->backup_options = 1;
            $backup_codemethod = 1;
          }

          $date = date('r');
          $this->checkDatabaseConnection();
          $query = "INSERT IGNORE INTO `engine4_dbbackup_dbbackups` ( `backup_filesize`, `backup_method`, `backup_filename`, `backup_time`, `backup_codemethod`, `backup_filesize1`, `backup_filename1`, `backup_timedescription`, `destination_name`, `backup_status`, `backup_auto`) VALUES ( '$filesize1', '$backup_method', '$backup_filename', '$backup_time', '$backup_codemethod', '$filesize2', '$filenametemp','$date', '$destination_name', '1', '0')";

          $insert = mysql_query($query);
          if (!$insert) {
            die('Can\'t use : ' . mysql_error());
          }

          $id = mysql_insert_id();
          $endtime = date('Y-m-d H:i:s');
          if (empty($destination_id)) {
            $update = mysql_query("UPDATE `engine4_dbbackup_backuplogs` SET `filename` = '$filename_compressed_form', `size` = '$filesize1', `end_time` = '$endtime', `status` = 'Success' WHERE `engine4_dbbackup_backuplogs`.`filename` = '$filename_compressed_form' LIMIT 1;");
            if (!$update) {
              die('Can\'t use : ' . mysql_error());
            }
          } else {
            if ($fetch['destination_mode'] !== 3) {
              if ($fetch['destination_mode'] == 1) {
                $update = mysql_query("UPDATE `engine4_dbbackup_backuplogs` SET `filename` = '$filename_compressed_form', `size` = '$filesize1', `end_time` = '$endtime', `status` = 'Success' WHERE `engine4_dbbackup_backuplogs`.`filename` = '$filename_compressed_form' LIMIT 1;");
              } else {
                $update = mysql_query("UPDATE `engine4_dbbackup_backuplogs` SET `filename` = '$filename_compressed_form', `size` = '$filesize1', `end_time` = '$endtime', `status` = 'Success' WHERE `engine4_dbbackup_backuplogs`.`filename` = '$filename_compressed_form' LIMIT 1;");
              }
            } else {
              $update = mysql_query("UPDATE `engine4_dbbackup_backuplogs` SET `backuplog_id` = '$backuplog_id', `size` = '0', `end_time` = '$endtime', `status` = 'Success' WHERE `engine4_dbbackup_backuplogs`.`backuplog_id` = '$backuplog_id' LIMIT 1;");
              if (!$update) {
                die('Can\'t use : ' . mysql_error());
              }
            }
          }
          $this->view->flage = 1;
          if (isset($session) && isset($session->post))
            unset($session->post);
        }
      }

      if ($this->view->backup_completecode == 0) {
        $start_codeBackup = time();
        if (!empty($backup_options)) {
          $coderesult = Engine_Api::_()->getItem('destinations', $backup_options);
          if ($coderesult->destination_mode == 2) {
            $backup_options = 2;
            $method = 'FTP';
            $destination_name = $coderesult->destinationname;
          } else if ($coderesult->destination_mode == 0) {
            $backup_options = 1;
            $method = 'Server Backup Directory & Download';
            $destination_name = $coderesult->destinationname;
          }
        } else {
          $method = 'Download';
          $destination_name = 'Download to computer';
        }
        $this->view->only_code = 1;
        $this->_outputPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $this->view->dir_name_temp;
        //MAKE FILENAME
        $archiveFileName = $backupfilename . '_' . 'code_' . date("Y_m_d_H_i_s", time()) . '.tar';
        $filename1 = $archiveFileName;
        $archiveFileName = preg_replace('/[^a-zA-Z0-9_.-]/', '', $archiveFileName);
        if (strtolower(substr($archiveFileName, -4)) != '.tar') {
          $archiveFileName .= '.tar';
        }

        $log_values = array_merge($log_values, array(
            'type' => 'File',
            'method' => 'Manual',
            'destination_name' => $destination_name,
            'destination_method' => $method,
            'filename' => $archiveFileName,
            'start_time' => date('Y-m-d H:i:s'),
            'status' => 'Fail'
                ));
        $code_backuplog_id = $backupLog_Table->setLog($log_values);

        $archiveFileName = $this->_outputPath . DIRECTORY_SEPARATOR . $archiveFileName;

        //SETUP PATHS
        $directory_name = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
        $archiveSourcePath = APPLICATION_PATH;
        $tmpPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $excludedirectory = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $directory_name;
        $excludedirectoryTemporary = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'temporary';
        $excludedErrorLog = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'error_log';
        //MAKE ARCHIVE
        $archive = new Archive_Tar($archiveFileName);
        //ADD FILES
        $path = $archiveSourcePath;

        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($it as $file) {
          $pathname = $file->getPathname();
          if ($backup_files_temp == 1 && $backup_rootfiles_temp == 1 && $backup_modulefiles_temp == 1) {
            if (is_file($pathname)) {
              if (substr($pathname, 0, strlen($tmpPath)) == $tmpPath || substr($pathname, 0, strlen($excludedirectory)) == $excludedirectory || substr($pathname, 0, strlen($excludedirectoryTemporary)) == $excludedirectoryTemporary || substr($pathname, 0, strlen($excludedErrorLog)) == $excludedErrorLog) {
                continue;
              } else {
                $lastfiles[] = $pathname;
              }
            }
          } else {
            if ($backup_files_temp == 0) {
              $skip = false;
              foreach ($skippedfiles as $skippedfile) {
                $skippedfile = str_replace("_DBBACKUP_DOT_", ".", $skippedfile);
                $skippedFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $skippedfile;
                if (substr($pathname, 0, strlen($skippedFile)) == $skippedFile) {
                  $skip = true;
                  break;
                }
              }

              if (!empty($skip)) {
                continue;
              }
            }

            if ($backup_rootfiles_temp == 0) {
              $skipfile = false;
              foreach ($skippedrootfiles as $skippedrootfile) {
                $skippedrootfile = str_replace("_DBBACKUP_DOT_", ".", $skippedrootfile);
                if (!empty($skippedmodulefiles)) {
                  $skippedmoduleFiless = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules';
                  if (substr($pathname, 0, strlen($skippedmoduleFiless)) == $skippedmoduleFiless) {
                    continue;
                  }
                  if ($skippedrootfile != 'application') {
                    $skippedmoduleFiless2 = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application';
                    if (substr($pathname, 0, strlen($skippedmoduleFiless2)) == $skippedmoduleFiless2) {
                      continue;
                    }
                  }
                }
                $skippedrootFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . $skippedrootfile;
                if (substr($pathname, 0, strlen($skippedrootFile)) == $skippedrootFile) {
                  $skipfile = true;
                  break;
                }
              }

              if (!empty($skipfile)) {
                continue;
              }
            }

            if (!empty($skippedmodulefiles)) {
              if ($backup_modulefiles_temp == 0) {
                $skipfile = false;
                foreach ($skippedmodulefiles as $skippedmodulefile) {
                  $skippedmodulefile = str_replace("_DBBACKUP_DOT_", ".", $skippedmodulefile);
                  $skippedmoduleFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $skippedmodulefile;
                  if (substr($pathname, 0, strlen($skippedmoduleFile)) == $skippedmoduleFile) {
                    $skipfile = true;
                    break;
                  }
                }
                if (!empty($skipfile)) {
                  continue;
                }
              }
            }
            if (is_file($pathname)) {
              if (substr($pathname, 0, strlen($tmpPath)) == $tmpPath || substr($pathname, 0, strlen($excludedirectory)) == $excludedirectory || substr($pathname, 0, strlen($excludedirectoryTemporary)) == $excludedirectoryTemporary || substr($pathname, 0, strlen($excludedErrorLog)) == $excludedErrorLog) {
                continue;
              } else {
                $lastfiles[] = $pathname;
              }
            }
          }
        }

        $lastfiless = array_merge($lastfiles, $files_array_merge);
        $ret = $archive->addModify($lastfiless, '', $path);

        if (PEAR::isError($ret)) {
          throw new Engine_Exception($ret->getMessage());
        }
        $filesize_temp = @filesize($archiveFileName);
        $filesize1 = round($filesize_temp / 1048576, 3) . ' Mb';
        if ($backup_options == 0) {
          $backup_options1 = 'Download';
          $this->view->backup_options = 1;
        } elseif ($backup_options == 1) {
          $backup_options1 = 'Server Backup Directory & Download';
          $this->view->backup_options = 0;
        } else {
          $backup_options1 = 'FTP';
          $this->view->backup_options = 0;
        }

        $this->backup_completecode = 0;
        $backup_time = time();

        $this->view->code_filesize = $filesize1;

        if (($backup_options == 1) || $backup_options == 2) {
          $destination_name = $destination_name;
        } else {
          $destination_name = 'Download to computer';
        }
        $this->view->code_filename = $filename1;
        $date = date('r');
        $this->checkDatabaseConnection();

        $query = "INSERT IGNORE INTO `engine4_dbbackup_dbbackups` ( `backup_method`, `backup_filename`, `backup_time`, `backup_codemethod`, `backup_filesize1`, `backup_filename1`, `backup_timedescription`, `destination_name`, `backup_status`, `backup_auto`) VALUES ( '$backup_options1', '$filename_compressed_form', '$backup_time', '0', '$filesize1', '$filename1','$date', '$destination_name', '1', '0')";
        $insert = mysql_query($query);
        if (!$insert) {
          die('Can\'t use : ' . mysql_error());
        }

        $id = mysql_insert_id();
        $endtime = date('Y-m-d H:i:s');
        $update = mysql_query("UPDATE `engine4_dbbackup_backuplogs` SET `backuplog_id` = '$code_backuplog_id', `size` = '$filesize1', `end_time` = '$endtime', `status` = 'Success' WHERE `engine4_dbbackup_backuplogs`.`backuplog_id` = $code_backuplog_id LIMIT 1;");
        if (!$update) {
          die('Can\'t use : ' . mysql_error());
        }

        $this->view->codeDuration = Engine_Api::_()->dbbackup()->getDurration(time() - $start_codeBackup);
        if ($backup_options == 2) {
          set_time_limit(0);
          $this->view->download = 0;
          $this->ftp = $coderesult->toarray();
          if (!isset($this->ftp['conn']))
            $this->backup_ftp_connect($id);
          else {
            $this->backup_ftp_connected($id);
          }
          $this->backup_file_transfer($archiveFileName, $filename1, $id);
        }
        $this->view->flage = 1;
        if (isset($session) && isset($session->post))
          unset($session->post);
      }
    }
    if ($this->view->flage) {
      $maintenance = 0;
      if (isset($session->maintenance)) {
        if ($maintenance != @$generalConfig['maintenance']['enabled']) {
          $generalConfig['maintenance']['enabled'] = (bool) $maintenance;
          if ($generalConfig['maintenance']['enabled']) {
            setcookie('en4_maint_code', $generalConfig['maintenance']['code'], time() + (60 * 60 * 24 * 365), $this->view->baseUrl());
          }
          if ((is_file($global_settings_file) && is_writable($global_settings_file)) ||
                  (is_dir(dirname($global_settings_file)) && is_writable(dirname($global_settings_file)))) {
            $file_contents = "<?php defined('_ENGINE') or die('Access Denied'); return ";
            $file_contents .= var_export($generalConfig, true);
            $file_contents .= "; ?>";
            file_put_contents($global_settings_file, $file_contents);
          } else {
            return $form->getElement('dbbackupmaintenance_mode')
                            ->addError('Unable to configure this setting due to the file /application/settings/general.php not having the correct permissions.Please CHMOD (change the permissions of) that file to 666, then try again.');
          }
        }
      }
      if (isset($session->maintenance))
        unset($session->maintenance);
      $this->view->duration = time() - $this->view->start_time;
      if ($destination_id != 0) {
        $query = "SELECT * FROM engine4_dbbackup_destinations WHERE destinations_id = '$destination_id'";
        $select = mysql_query($query);
        $fetch = mysql_fetch_assoc($select);
        if ($fetch['destination_mode'] == 3 && $backup_completecode != 0) {
          $this->view->backup_completecode = $backup_code;
          $backup_time = time();
          $this->view->backup_options = 1;
          $destination_name = $fetch['destinationname'];
          $date = date('r');
          $this->checkDatabaseConnection();
          $query = "INSERT IGNORE INTO `engine4_dbbackup_dbbackups` ( `backup_method`, `backup_filename`, `backup_time`, `backup_codemethod`, `backup_timedescription`, `destination_name`, `backup_status`, `backup_auto`) VALUES ( 'Database', '-', '$backup_time', '3','$date', '$destination_name', '1', '0')";
          $insert = mysql_query($query);
          if (!$insert) {
            die('Can\'t use : ' . mysql_error());
          }

          $id = mysql_insert_id();
          $endtime = date('Y-m-d H:i:s');
          $update = mysql_query("UPDATE `engine4_dbbackup_backuplogs` SET `backuplog_id` = '$backuplog_id', `size` = '0', `end_time` = '$endtime', `status` = 'Success' WHERE `engine4_dbbackup_backuplogs`.`backuplog_id` = $backuplog_id LIMIT 1;");
          if (!$update) {
            die('Can\'t use : ' . mysql_error());
          }
        }
      }

      if ($destination_id != 0) {
        if ($fetch['destination_mode'] == 1) {
          ini_set("memory_limit", "128M");
          set_time_limit(0);
          $this->view->download = 0;
          $link = $backup_filepath;
          $size = @filesize($link);
          $max = 20971520;
          if ($size > $max) {
            $this->view->translate("The backup file could not be emailed as attachment because its size is greater than 20 MB. Please choose a different destination for your backup.");
            return;
          }
          $str = str_replace("+0000", "", date('Y-m-d H:i:s'));
          $subject = $this->view->translate("Database backup of your site on $str");
          $translate = Zend_Registry::get('Zend_Translate');
          $user_message = $translate->_('_EMAIL_HEADER_BODY');
          $user_message .= $this->view->translate("\n\n The database backup of your site was successfully completed on $str.\n\n Please find the backup file attached with this email.");
          $to = $fetch['email'];
          $fileatt_type = $this->view->translate('application/x-gzip');
          $fileatt_name = $filename_compressed_form;
          $from = $this->view->translate('Site Admin') . '<' . Engine_Api::_()->getApi('settings', 'core')->core_mail_from . '>';
          $headers = $this->view->translate("From: ") . $from;
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
                  "--{$mime_boundary}--\n\n";

          $foter_message = $translate->_('_EMAIL_FOOTER_BODY');

          $email_message .= "This is a multi-part message in MIME format.\n\n" .
                  "--{$mime_boundary}\n" .
                  "Content-Type:text/html; charset=\"iso-8859-1\"\n" .
                  "Content-Transfer-Encoding: 7bit\n\n" . $foter_message . "\n\n";

          $mail_sent = @mail($to, $subject, $email_message, $headers);

          if ($mail_sent) {
            $this->view->msg = $this->view->translate('Email has been sent successfully.');
            $time_out = 7000;
            @unlink($link);
            $this->view->no_form = 1;
            $this->view->download = 0;
          } else {
            $is_error = 1;
            $error_array[] = $this->view->translate('There was an error in sending your email. Please try again later.');
            $time_out = 50000;
            $this->checkDatabaseConnection();
            $update = mysql_query("UPDATE `engine4_dbbackup_backuplogs` SET `status` = 'Fail' WHERE `engine4_dbbackup_backuplogs`.`filename` = '$filename_compressed_form' LIMIT 1;");
            $this->view->no_form = 1;
            $this->view->download = 1;
          }
        } else if ($fetch['destination_mode'] == 2 && $this->view->backup_completecode != 0) {
          set_time_limit(0);
          $this->view->download = 1;
          $this->ftp = $result->toarray();
          if (!isset($this->ftp['conn']))
            $this->backup_ftp_connect($id);
          else {
            $this->backup_ftp_connected($id);
          }
          $file = $backup_filepath;
          $this->backup_file_transfer($file, $filename_compressed_form, $id);
        }
      }
    }
  }

  //THIS FUNCTION RETURN THE CREATE STATEMENT FOR A TABLE END.
  public function fetchTablecreateformat($table) {
    $db = Engine_Db_Table::getDefaultAdapter();
    $export = Engine_Db_Export::factory($db);
    $adapter = $export->getAdapter();
    $quotedTable = $export->getAdapter()->quoteIdentifier($table);
    $result = $this->fetchrows('SHOW CREATE TABLE ' . $quotedTable);
    $result = $result[0]['Create Table'];
    $output = '';
    if ($export->getParam('dropTable', true)) {
      $output = 'SET FOREIGN_KEY_CHECKS=0;';
      $output .= 'DROP TABLE IF EXISTS ' . $quotedTable . ';';
    }
    $output .= PHP_EOL . $result . ';';
    $output .= PHP_EOL . PHP_EOL;
    return $output;
  }

  //THIS FUNCTION RETURN THE HEADER OF THE TABLE START.
  public function fetchtableheader($table) {
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
  }

  //THIS FUNCTION RETURN THE HEADER OF THE TABLE END.
  //THIS FUNCTION RETURN THE COMMENTS FOR THE FILE START .
  public function fetchcomments($comment = '') {
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
  }

  //THIS FUNCTION USE FOR LOCKING THE DATABASE.
  public function lockDatabse() {
    $session = new Zend_Session_Namespace();
    $table_selected = $session->table_selected;
    $fileAdapterinfo['adapter'] = $session->fileadapter;
    $num_selected_table = count($table_selected['tables']);
    $initial_code = 0;
    if ($num_selected_table > 0) {
      $table_name = array();
      $dbinfo = Engine_Db_Table::getDefaultAdapter()->getConfig();
      $dbname = $dbinfo['dbname'];
      while (($num_selected_table - 1 >= $initial_code)) {
        $table_name[] = '`' . $table_selected['tables'][$initial_code]['Tables_in_' . $dbname] . '`  WRITE';

        $initial_code++;
      }
      $db = Engine_Db_Table::getDefaultAdapter();
      $export = Engine_Db_Export::factory($db);
      $connection = $export->getAdapter()->getConnection();
      if ($fileAdapterinfo['adapter'] == 'mysql') {
        if (!($result = mysql_query('LOCK TABLES ' . implode(', ', $table_name)))) {
          throw new Engine_Db_Export_Exception('Unable to execute lock query.');
        }
      } else {
        if (!($result = $connection->query('LOCK TABLES ' . implode(', ', $table_name)))) {
          throw new Engine_Db_Export_Exception('Unable to execute lock query.');
        }
      }
    }
  }

  //THIS FUNCTION RETURN THE ROW OF THE TABLES START.
  public function fetchrows($sql) {
    $session = new Zend_Session_Namespace();
    $fileAdapterinfo['adapter'] = $session->fileadapter;
    $dbinfo = Engine_Db_Table::getDefaultAdapter()->getConfig();
    $db = Engine_Db_Table::getDefaultAdapter();
    $export = Engine_Db_Export::factory($db);
    $connection = $export->getAdapter()->getConnection();

    if ($fileAdapterinfo['adapter'] == 'mysql') {
      if (!($results = mysql_query($sql))) {
        throw new Engine_Db_Export_Exception('Unable to execute query.');
      }
      $data = array();
      while (false != ($row = @mysql_fetch_array($results))) {
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
        throw new Engine_Db_Export_Exception('Unable to execute query.');
      }
      $data = array();
      while (false != ($row = $result->fetch_assoc())) {
        $data[] = $row;
      }
    }
    return $data;
  }

  //THIS FUNCTION RETURN THE TABLES OF THE DATABASE START.
  public function fetchtables() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $export = Engine_Db_Export::factory($db);
    return array_values($export->getAdapter()->fetchAll('SHOW TABLES'));
  }

  //THIS FUNCTION RETURN THE DATA OF THE TABLES START.
  public function _fetchTableData1($table, $row_limit, $initial_code, $starting_row_point, $backup_filepath) {

    $db = Engine_Db_Table::getDefaultAdapter();
    $export = Engine_Db_Export::factory($db);
    $adapter = $export->getAdapter();
    $quotedTable = $export->getAdapter()->quoteIdentifier($table);
    $output = '';
    //GET DATA
    //HERE WE SETTING THE TABLE IN THE ARRAY IN WHICH THE MORE DATA IS THERE MEANS PDF FILES,TXT FILES ETC. ALSO IN THESE TABLE THE 'FULLTEXT' FIELD.BECAUSE THERE IS PROBLEM TO TAKING THE BACKUP OF THESE TABLE WE HAVE TAKEN 500000 CHARACTERS FROM THIS FIELD.
    $quotedTablearray = array('`engine4_documents`', '`engine4_groupdocument_documents`', '`engine4_sitepagedocument_documents`');
    if (!in_array($quotedTable, $quotedTablearray)) {
      $sql = "SELECT * FROM  $quotedTable LIMIT $starting_row_point,$row_limit";
    } else {
      //HERE WE CHECKING IF THERE IS A TABLE IN WHICH WE HAVE PDF FILES AND TEXT FILES ETC. THEN WE  SET ITS ROW LIMIT TO 100 MEANS AT A TIME IT TAKES 100 ROWS.
      $row_limit = 100;
      $sql = "SELECT * FROM  $quotedTable LIMIT $starting_row_point,$row_limit";
    }
    $stmt = $adapter->query($sql);
    if (!$stmt) {
      throw new Engine_Db_Export_Exception('Unable to execute select query.');
    }
    $first = true;
    $columns = null;
    $i = 0;
    $output .= "LOCK TABLES $quotedTable WRITE;\n";
    while (false != ($row = $stmt->fetch())) {
      if (!$export->getParam('insertExtended', true) || $first) {
        $output .= 'INSERT ';
        if ($export->getParam('insertIgnore', false)) {
          $output .= 'IGNORE ';
        }
        $output .= 'INTO ' . $quotedTable . ' ';
        //COMPLETE
        if ($export->getParam('insertComplete', true)) {
          if (empty($columns)) {
            $columns = implode(', ', array_map(array($adapter, 'quoteIdentifier'), array_keys($row)));
          }
          $output .= '(' . $columns . ') ';
          $output .= 'VALUES ';
        }
      }
      //OTHER WISE WE ARE CONTINUING A PREVIOUS QUERY
      else {
        $output .= ',';
        $output .= PHP_EOL;
      }

      //ADD DATA
      $data = array();
      foreach ($row as $key => $value) {
        //HERE WE SETTING THE  VALUE OF TEXT FIELD IF IN THE FIELD THERE IS 500000 CHARACTERS THEN WE DONOT TAKING THE MORE CHARACTERS THEN 500000.
        if (in_array($quotedTable, $quotedTablearray) && ( $key == 'fulltext')) {
          $value = Engine_String::strlen($value) > 500000 ? Engine_String::substr($value, 0, 500000) : $value;
        }
        if (null === $value) {
          $data[$key] = 'NULL';
        } else {
          $data[$key] = $adapter->quote($value);
        }
      }
      $output .= '(' . implode(', ', $data) . ')' . ';';
      $output .= "\n";
      $i++;
    }
    $num_rows_selected = $i;
    if ($num_rows_selected > ($row_limit - 1)) {
      $starting_row_point = ($starting_row_point + $row_limit);
      $num_rows_selected--;
      $row_limit = 0;
      $change_table = $initial_code;
    }
    //HERE WE SET IF THE NO OF SELECTED ROWS IS LESS THEN SECOND LIMIT THEN WE SET FIRST LIMIT AND ALSO SECOND LIMIT.
    else {
      $starting_row_point = 0;
      $row_limit = ($row_limit - 1) - $num_rows_selected;
      $initial_code++;
      $change_table = $initial_code - 1;
    }
    $output .= "\nUNLOCK TABLES;\n";
    $output .= "\nSET FOREIGN_KEY_CHECKS=1;";
    $output_array['data'] = $output;
    $output_array['initial_code'] = $initial_code;
    $output_array['change_table'] = $change_table;
    $output_array['row_limit'] = $row_limit;
    $output_array['starting_row_point'] = $starting_row_point;
    $output_array['num_rows_selected'] = $num_rows_selected;
    return $output_array;
  }

  function backup_file_transfer($file, $dest_filename, $id) {
    set_time_limit(0);

    if (!(@ftp_chdir($this->ftp['conn'], $this->ftp['ftppath'] . "/" . $this->ftp['ftpdirectoryname']))) {
      @ftp_mkdir($this->ftp['conn'], $this->ftp['ftppath'] . "/" . $this->ftp['ftpdirectoryname']);
    }
    $source = $file;
    $dest_complete_path = $this->ftp['ftppath'] . "/" . $this->ftp['ftpdirectoryname'] . "/" . $dest_filename;

    if (file_exists($file)) {
      $put_file = @ftp_put($this->ftp['conn'], $dest_complete_path, $source, FTP_BINARY);
      if (!$put_file) {
        //turn passive mode on
        @ftp_pasv($this->ftp['conn'], true);
        $put_file = @ftp_put($this->ftp['conn'], $dest_complete_path, $source, FTP_BINARY);
      }

      if (!$put_file) {
        $this->view->translate("FTP Error: Could not save the backup file - $source on the FTP server.");
        $this->checkDatabaseConnection();
        $update = mysql_query("UPDATE `engine4_dbbackup_dbbackups` SET `backup_status` = 0 WHERE `engine4_dbbackup_dbbackups`.`dbbackup_id` = $id LIMIT 1;");
        return FALSE;
      } else {
        @unlink($source);
      }
      //EVERYTHING WORKED OK
      return TRUE;
    } else {
      $this->view->translate("FTP Error: Could not find the backup file on your site\'s server.");
      $this->checkDatabaseConnection();
      $update = mysql_query("UPDATE `engine4_dbbackup_dbbackups` SET `backup_status` = 0 WHERE `engine4_dbbackup_dbbackups`.`dbbackup_id` = $id LIMIT 1;");
      return FALSE;
    }
  }

  function backup_ftp_connect($id) {
    set_time_limit(0);
    //CONNECT TO THE SERVER
    $this->ftp['conn'] = ftp_connect($this->ftp['ftphost'], $this->ftp['ftpportno']);
    if (!$this->ftp['conn']) {
      $this->view->translate('FTP Error: Could not connect to FTP server.');
      $this->checkDatabaseConnection();
      $update = mysql_query("UPDATE `engine4_dbbackup_dbbackups` SET `backup_status` = 0 WHERE `engine4_dbbackup_dbbackups`.`dbbackup_id` = $id LIMIT 1;");
      return FALSE;
    }
    //LOGIN TO THE SERVER
    $this->ftp['login'] = ftp_login($this->ftp['conn'], $this->ftp['ftpuser'], $this->ftp['ftppassword']);
    if (!$this->ftp['login']) {
      $this->view->translate('FTP Error: Could not login as user FTP_USER on the FTP server.');
      $this->checkDatabaseConnection();
      $update = mysql_query("UPDATE `engine4_dbbackup_dbbackups` SET `backup_status` = 0 WHERE `engine4_dbbackup_dbbackups`.`dbbackup_id` = $id LIMIT 1;");
      return FALSE;
    }
  }

  function backup_ftp_connected($id) {
    set_time_limit(0);
    if (!@ftp_systype($this->ftp['conn'])) {
      return $this->ftp['conn'] = $this->backup_ftp_connect($id);
    } else {
      return $this->ftp['conn'];
    }
  }

  //OTHER DATABASE BACKUP
  //THIS IS A FUNCTION FOR DROPING THE TABLE.
  public function database_dropformat($table) {
    $db = Engine_Db_Table::getDefaultAdapter();
    $export = Engine_Db_Export::factory($db);
    $adapter = $export->getAdapter();
    $quotedTable = $export->getAdapter()->quoteIdentifier($table);
    $outputdrop = '';
    if ($export->getParam('dropTable', true)) {
      $outputdrop = 'SET FOREIGN_KEY_CHECKS=0;';
      $outputdrop .= 'DROP TABLE IF EXISTS ' . $quotedTable . ';';
    }
    return $outputdrop;
  }

  //THIS IS A FUNCTION FOR CREATING THE TABLE.
  public function database_fetchTablecreateformat($table) {
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
  }

  //THIS FUNCTION RETURN THE ROW OF THE TABLES START.
  public function database_fetchrow($sql) {
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
  }

  //THIS FUNCTION RETURN THE DATA OF THE TABLES START.
  public function database_fetchTableData($table) {
    $db = Engine_Db_Table::getDefaultAdapter();
    $export = Engine_Db_Export::factory($db);
    $adapter = $export->getAdapter();
    $quotedTable = $export->getAdapter()->quoteIdentifier($table);
    $output = '';
    //GET THE TOTALL NUMBER OF ROWS IN TABLES
    $sql = "SELECT COUNT(*) AS row_table FROM $quotedTable";
    $stmt = $adapter->query($sql);
    $row_count = $stmt->fetch();
    //COUNTER FOR WHILE LOOP BASE ON TABLES ROWS
    $counter = $row_count['row_table'] / 60000;
    if ($row_count['row_table'] % 60000)
      $counter++;
    $i = 1;
    //STARING INDEX OF TABLED WHERE TO FETCH
    $start_limit = 0;
    while ($i <= $counter) {
      if ($i != 1) {
        $start_limit+=60000;
      }

      //GET DATA
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
          //COMPLETE
          if ($export->getParam('insertComplete', true)) {
            if (empty($columns)) {
              $columns = implode(', ', array_map(array($adapter, 'quoteIdentifier'), array_keys($row)));
            }
            $output .= '(' . $columns . ') ';
            $output .= 'VALUES ';
          }
        }
        //OTHER WISE WE ARE CONTINUING A PREVIOUS QUERY
        else {
          $output .= ',';
          $output .= PHP_EOL;
        }

        //ADD DATA
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
  }

  //THIS FUNCTION USE FOR UNLOCKING THE DATABASE.
  public function unlockDatabse() {
    $session = new Zend_Session_Namespace();
    $fileAdapterinfo['adapter'] = $session->fileadapter;
    $db = Engine_Db_Table::getDefaultAdapter();
    $export = Engine_Db_Export::factory($db);
    $connection = $export->getAdapter()->getConnection();
    if ($fileAdapterinfo['adapter'] == 'mysql') {
      if (!($result = mysql_query('UNLOCK TABLES'))) {
        throw new Engine_Db_Export_Exception('Unable to execute unlock query.');
      }
    } else {
      if (!($result = $connection->query('UNLOCK TABLES'))) {
        throw new Engine_Db_Export_Exception('Unable to execute unlock query.');
      }
    }
  }

  public function checkDatabaseConnection() {

    //GET SETTINGS
    $database_settings_file = APPLICATION_PATH . '/application/settings/database.php';
    if (file_exists($database_settings_file)) {
      $dbinfo = include $database_settings_file;
    } else {
      $dbinfo = array();
    }
    $link = 0;
    if (!empty($dbinfo)) {
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
  }

}

?>