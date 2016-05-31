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
class Dbbackup_AdminDestinationsettingsController extends Core_Controller_Action_Admin {

  public function indexAction() {
    $destination_page = $this->_getParam('show');
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('dbbackup_admin_main', array(), 'dbbackup_admin_main_destinationsettings');
    $table = Engine_Api::_()->getDbtable('destinations', 'dbbackup');

    $select_query = $table->select()
            ->where('destination_mode != ?', 0)
            ->order('destinations_id DESC');

    $paginator = Zend_Paginator::factory($select_query);
    $paginator->setItemCountPerPage(20);
    $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page'));
    $this->view->form = $form = new Dbbackup_Form_Admin_Directorysettings();
    $values = $form->getValues();

    $table = Engine_Api::_()->getDbtable('backupauthentications', 'dbbackup');
    $select = $table->select()
            ->where('backupauthentication_id = ?', 1)
            ->limit();
    $row = $table->fetchRow($select);
    $this->view->backup_enable = $row->htpasswd_enable;

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $this->getRequest()->getPost();
      // user name which you want to store in the htpassword file.
      $htusername = $values['htusername'];
      // password which you want to store in the htpassword file.
      $htpassword = $values['htpassword'];
      //Getting the table where those information is saved.
      $backup_enable = $values['backup_enable'];
      // Current directory name.
      $current_dir_name = $values['dbbackup_directoryname'];
      $flag = 0;
      //Selecting the row if exist if not exist then insert into the table.
      $select = $table->select()
              ->where('backupauthentication_id = ?', 1)
              ->limit();

      $row = $table->fetchRow($select);
      //Checking if these two are not empty then insert into the database and updated.
      if (!empty($htusername) && !empty($htpassword)) {
        if (null === $row) {
          $table->insert(array('htpassword_username' => $htusername, 'htpassword_password' => $htpassword, 'htpasswd_enable' => $backup_enable));
        } else {
          $table->update(array('htpassword_username' => $htusername, 'htpassword_password' => $htpassword, 'htpasswd_enable' => $backup_enable), array('backupauthentication_id = ?' => 1,));
        }
      }
      $this->view->backup_enable = $backup_enable;
      $htpasswd_text = "$htusername:" . crypt($htpassword) . "";
      if (!preg_match('/^[a-zA-Z0-9]+$/', $current_dir_name)) {
        $this->view->error = $this->view->translate("The Backup Directory Name must be alphanumeric. Please do not enter any special characters or space in it.");
      } else {
        $flage = 0;
        if (preg_match('/^[a-zA-Z]+$/', $current_dir_name))
          $flage = 1;
        if (preg_match('/^[0-9]+$/', $current_dir_name))
          $flage = 1;

        if ($flage) {
          $this->view->error = $this->view->translate("The Backup Directory Name must contain both alphabets and numbers.");
        } else {
          //Checking if directory name is exist or not.
          if (!empty(Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname)) {
            $pre_dir_name = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
          } else {
            $pre_dir_name = $current_dir_name;
          }
          //Checking if current directorty and previous directory is equal and not exist the previous directory then creating the new directory.

          $file_path = APPLICATION_PATH . '/public/' . $pre_dir_name;

          if (($pre_dir_name == $current_dir_name) && !(is_dir($pre_dir_name))) {
            $file_path = APPLICATION_PATH . '/public/' . $current_dir_name;
            if (!is_dir($file_path) && !mkdir($file_path, 0777, true)) {
              mkdir(dirname($file_path));
              chmod(dirname($file_path), 0777);
              touch($file_path);
              chmod($file_path, 0777);
            }
            $backup_filepath = APPLICATION_PATH . '/public/' . $pre_dir_name . '/.htpasswd';
            $fp = fopen($backup_filepath, 'w');
            fwrite($fp, $htpasswd_text);
            fclose($fp);
          }
          //Checking if current directorty and previous directory is equal and not exist the previous directory then renameing the current directory.
          elseif (($pre_dir_name != $current_dir_name) && !(is_dir($current_dir_name))) {
            if (is_dir(APPLICATION_PATH . '/public/' . $pre_dir_name)) {
              rename(APPLICATION_PATH . '/public/' . $pre_dir_name, APPLICATION_PATH . '/public/' . $current_dir_name);
              chmod(dirname(APPLICATION_PATH . '/public/' . $current_dir_name), 0777);
              $backup_filepath = APPLICATION_PATH . '/public/' . $current_dir_name . '/.htpasswd';
              $fp = fopen($backup_filepath, 'w');
              fwrite($fp, $htpasswd_text);
              fclose($fp);
            } else {
              $file_path = APPLICATION_PATH . '/public/' . $current_dir_name;
              if (!is_dir($file_path) && !mkdir($file_path, 0777, true)) {
                mkdir(dirname($file_path));
                chmod(dirname($file_path), 0777);
                touch($file_path);
                chmod($file_path, 0777);
              }
              $backup_filepath = APPLICATION_PATH . '/public/' . $pre_dir_name . '/.htpasswd';
              $fp = fopen($backup_filepath, 'w');
              fwrite($fp, $htpasswd_text);
              fclose($fp);
            }
          }


          $table = Engine_Api::_()->getDbtable('settings', 'core');
          $table->update(array('value' => $current_dir_name,), array('name = ?' => 'dbbackup.directoryname',));
          $table = Engine_Api::_()->getDbtable('destinations', 'dbbackup');
          $table->update(array('destinationname' => 'Server Backup Directory', 'dbbackup_directoryname' => $current_dir_name), array('	destination_mode = ?' => 0,));
          $current_dir_nametemp = $_SERVER['HTTP_HOST'];
          $authtication_name_store = $current_dir_name;
          $authtication_path = APPLICATION_PATH . '/public/' . $current_dir_name . '/.htpasswd';
          $format = "AuthType Basic \n AuthName  $authtication_name_store \n AuthUserFile $authtication_path \n Require valid-user";
          $backupfilepath = APPLICATION_PATH . '/public/' . $current_dir_name . '/.htaccess';
          $fp = fopen($backupfilepath, 'w');
          fwrite($fp, $format);
          fclose($fp);

          $password_check_format = "Congratulations! Your backup directory is PASSWORD PROTECTED.\n\nBackups provide insurance for your site. In the event that something on your site goes wrong, you can restore your site's content with the most recent backup file.\n\n **********   Backup and Restore Plugin by SocialEngineAddOns (http://www.socialengineaddons.com)   **********";
          $password_check_path = APPLICATION_PATH . '/public/' . $current_dir_name . '/password_check.txt';
          $fp = fopen($password_check_path, 'w');
          fwrite($fp, $password_check_format);
          fclose($fp);
        }
      }

      if ($backup_enable != 1) {
        $backup_filepath_htaccess = APPLICATION_PATH . '/public/' . $current_dir_name . '/.htaccess';
        if (is_file($backup_filepath_htaccess))
          unlink($backup_filepath_htaccess);
        $flag = 1;
      }

      if ($flag == 0) {
        if (empty($htusername)) {
          $this->view->error = $this->view->translate("Username * Please complete this field - it is required.");
        }
        if (empty($htpassword)) {
          $this->view->error = $this->view->translate("Password * Please complete this field - it is required.");
        }
        if (empty($htusername) && empty($htpassword)) {
          $this->view->error = $this->view->translate("Please complete all the required field.");
        }
      }


      if (empty($this->view->error)) {

        // Redirect to index action
        $this->_helper->redirector->gotoRoute(array('action' => 'index', 'show' => 2));
      }
    }


    if ($destination_page == 1) {
      $this->view->destination_block = $destination_page;
    } else {
      $this->view->destination_block = 0;
    }

    if ($this->view->backup_enable == 1) {
      if ($destination_page == 2) {
        $this->view->message = 1;
        $this->view->currentdirectory = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
      }
    } else {
      if ($destination_page == 2) {
        $this->view->message = 2;
      }
    }
  }

  public function destinationAction() {
    $destination_page = $this->_getParam('show');
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('dbbackup_admin_main', array(), 'dbbackup_admin_main_destinationsettings');

    // Get Option
    if (null !== ($mode = $this->_getParam('mode'))) {
      $this->view->destination_mode = $destination_mode = $mode;
    } else {
      $this->view->destination_mode = $destination_mode = 1;
    }
    $this->view->form = $form = new Dbbackup_Form_Admin_Destination();



    if (!empty($mode)) {

      switch ($mode) {
        case 1:
          $description = 'Email destination type can be used for database backups (manual and automatic).';
          break;
        case 2:
          $description = 'FTP Directory destination type can be used for database backups (manual and automatic) and files backups (manual).';
          break;
        case 3:
          $description = 'MySQL Database destination type can be used for database backups (manual and automatic).';
          break;
      }

      $form->destination_mode->setValue($mode);
      $form->destination_mode->setDescription($description);
    }

    // Get Option
    if (null !== ($mode = $this->_getParam('mode'))) {
      $this->view->destination_mode = $destination_mode = $mode;
    } else {
      $this->view->destination_mode = $destination_mode = 1;
    }

    //If not post or from not valid, return
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      $validation_flage = 1;

      if (empty($_POST['destinationname'])) {
        $form->addError($this->view->translate("Destination Name *  Please complete this field - it is required."));
        $validation_flage = 0;
      }

      if ($destination_mode != 3)
        $values['dbhost'] = '';
      if ($destination_mode != 2)
        $values['ftpportno'] = '';

      switch ($destination_mode) {
        case 1:
          if (empty($_POST['email'])) {
            $form->addError($this->view->translate("Email *  Please complete this field - it is required."));
            $validation_flage = 0;
          }

          if (!empty($_POST['email'])) {
            $regexp = "/^[a-z0-9]+([a-z0-9_\+\\.-]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
            if (!(bool) preg_match($regexp, $_POST['email'])) {
              $form->addError($this->view->translate('Email * Please enter correct email id.'));
              $validation_flage = 0;
            }
          }

          if (!$validation_flage) {
            return;
          }
          break;
        case 2:
          if (empty($_POST['ftphost'])) {
            $form->addError($this->view->translate("Host *  Please complete this field - it is required."));
            $validation_flage = 0;
          }
          if (empty($_POST['ftpportno'])) {
            $form->addError($this->view->translate("Port number *  Please complete this field - it is required."));
            $validation_flage = 0;
          }
          if (!empty($_POST['ftpportno'])) {
            if (!preg_match('/^[0-9]+$/', $_POST['ftpportno'])) {
              $form->addError($this->view->translate("Port number *  Please enter numeric value."));
              $validation_flage = 0;
            }
          }
          if (empty($_POST['ftppath'])) {
            $form->addError($this->view->translate("Path *  Please complete this field - it is required."));
            $validation_flage = 0;
          }
          if (empty($_POST['ftpuser'])) {
            $form->addError($this->view->translate("User *  Please complete this field - it is required."));
            $validation_flage = 0;
          }

          if (empty($_POST['ftpdirectoryname'])) {
            $form->addError($this->view->translate("Backup Directory *  Please complete this field - it is required."));
            $validation_flage = 0;
          }

          if (!empty($_POST['ftpdirectoryname'])) {
            if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['ftpdirectoryname'])) {
              $form->addError($this->view->translate("The Backup Directory name must be alpha-numeric. Please do not enter any special characters or space in it."));
              $validation_flage = 0;
            }
          }


          if (!$validation_flage) {
            return;
          } else {
            // Attempt to connect to the remote server
            $conn = @ftp_connect($_POST['ftphost'], $_POST['ftpportno']);

            if (!$conn) {
              $form->addError($this->view->translate("FTP Connection could not be established. Please enter correct FTP Host and Port number."));
              return FALSE;
            }

            // Attempt to login to the remote server
            $login = @ftp_login($conn, $_POST['ftpuser'], $_POST['ftppassword']);

            if (!$login) {
              $form->addError($this->view->translate("FTP login failed. Please enter correct username and password combination."));
              return FALSE;
            }


            if (!(@ftp_chdir($conn, $_POST['ftppath']))) {
              $form->addError($this->view->translate("Path is not valid. Please enter a correct path."));
              return FALSE;
            } else {

              if (!(@ftp_chdir($conn, $_POST['ftppath'] . "/" . $_POST['ftpdirectoryname']))) {
                if (@ftp_mkdir($conn, $_POST['ftppath'] . "/temp_" . $_POST['ftpdirectoryname'])) {
                  @ftp_rmdir($conn, $_POST['ftppath'] . "/temp_" . $_POST['ftpdirectoryname']);
                } else {
                  $form->addError($this->view->translate("You do not have permission for create directory"));
                  return FALSE;
                }
              }
            }
          }
          break;
        case 3 :
          if (empty($_POST['dbhost'])) {
            $form->addError($this->view->translate("Host *  Please complete this field - it is required."));
            $validation_flage = 0;
          }
          if (empty($_POST['dbname'])) {
            $form->addError($this->view->translate("Database Name *  Please complete this field - it is required."));
            $validation_flage = 0;
          }
          if (!empty($_POST['dbname'])) {
            if (!preg_match('/[a-zA-Z0-9_\$]+/', $_POST['dbname'])) {
              $form->addError($this->view->translate('The database name is not valid.'));
            }
          }
          if (empty($_POST['dbuser'])) {
            $form->addError($this->view->translate("User *  Please complete this field - it is required."));
            $validation_flage = 0;
          }

          if (!$validation_flage) {
            return;
          } else {
            $dbconn = @mysql_connect($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpassword']);
            if (!$dbconn) {
              $form->addError("Could not connect to database: " . mysql_error());
              return;
            }
            $db_selected = @mysql_select_db($_POST['dbname'], $dbconn);
            if (!$db_selected) {
              $form->addError('Could not connect to database: ' . mysql_error());
              return;
            }
          }
          break;
      }
      $table = Engine_Api::_()->getDbtable('destinations', 'dbbackup');
      try {
        // Insert into table
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $row = $table->createRow();
        $row->setFromArray($values);
        $row->save();
        $db->commit();
        // Redirect to index action 
        $this->_helper->redirector->gotoRoute(array('action' => 'index', 'show' => 1));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
    if ($destination_page == 1) {
      $this->view->destination_block = $destination_page;
    } else {
      $this->view->destination_block = 0;
    }
  }

  // Edit action
  public function editAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('dbbackup_admin_main', array(), 'dbbackup_admin_main_destinationsettings');
    $destination_page = $this->_getParam('show');
    // Get Option
    if (null !== ($id = $this->_getParam('id'))) {
      $this->view->destination_id = $destination_id = $id;
    } else {
      $this->view->destination_id = $destination_id = 0;
    }

    $row = Engine_Api::_()->getItem('destinations', $id);
    $this->view->destination_mode = $destination_mode = $row->destination_mode;
    $this->view->ftpdirectory = $row->ftpdirectoryname;



    $this->view->ftppath = $row->ftppath;
    $this->view->form = $form = new Dbbackup_Form_Admin_Destination();
    $form->setTitle($this->view->translate('Edit a Backup Destination'));
    $form->setDescription($this->view->translate('If you are using this destination in the Automatic Backup, then changing it will affect your automatic backup also. Please check your backup settings after making this change.'));
    if (!$this->getRequest()->isPost())
      $form->populate($row->toArray());




    $form->destination_mode->setValue($destination_mode);
    $form->submit->setLabel($this->view->translate('Save Changes'));
    $form->removeElement('destination_mode');

    $destination_mode_name = 'Email';
    switch ($destination_mode) {
      case 2:
        $destination_mode_name = 'FTP Directory';
        break;
      case 3:
        $destination_mode_name = 'MySQL Database';
    }


    if (!empty($destination_mode)) {

      switch ($destination_mode) {
        case 1:
          $description = 'Email destination type can be used for database backups (manual and automatic).';
          break;
        case 2:
          $description = 'FTP Directory destination type can be used for database backups (manual and automatic) and files backups (manual).';
          break;
        case 3:
          $description = 'MySQL Database destination type can be used for database backups (manual and automatic).';
          break;
      }

     
    }

    $form->addElement('Dummy', 'directory_path', array(
            'label' => 'Destination Type',
            'description' => $destination_mode_name.'    ['. $description . ']',
            'order' => 0
    ));



    $this->view->form = $form;
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      $validation_flage = 1;
      if (empty($_POST['destinationname'])) {
        $form->addError($this->view->translate("Destination Name *  Please complete this field - it is required."));
        $validation_flage = 0;
      }

      if ($destination_mode != 3)
        $values['dbhost'] = '';
      if ($destination_mode != 2)
        $values['ftpportno'] = '';

      switch ($destination_mode) {
        case 1:
          if (empty($_POST['email'])) {
            $form->addError($this->view->translate("Email *  Please complete this field - it is required."));
            $validation_flage = 0;
          }

          if (!empty($_POST['email'])) {
            $regexp = "/^[a-z0-9]+([a-z0-9_\+\\.-]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
            if (!(bool) preg_match($regexp, $_POST['email'])) {
              $form->addError($this->view->translate('Email * Please enter correct email id.'));
              $validation_flage = 0;
            }
          }

          if (!$validation_flage) {
            return;
          }
          break;
        case 2:
          if (empty($_POST['ftphost'])) {
            $form->addError($this->view->translate("Host *  Please complete this field - it is required."));
            $validation_flage = 0;
          }
          if (empty($_POST['ftpportno'])) {
            $form->addError($this->view->translate("Port number *  Please complete this field - it is required."));
            $validation_flage = 0;
          }
          if (!empty($_POST['ftpportno'])) {
            if (!preg_match('/^[0-9]+$/', $_POST['ftpportno'])) {
              $form->addError($this->view->translate("Port number *  Please enter numeric value."));
              $validation_flage = 0;
            }
          }
          if (empty($_POST['ftppath'])) {
            $form->addError($this->view->translate("Path *  Please complete this field - it is required."));
            $validation_flage = 0;
          }
          if (empty($_POST['ftpuser'])) {
            $form->addError($this->view->translate("User *  Please complete this field - it is required."));
            $validation_flage = 0;
          }
          if (empty($_POST['ftpdirectoryname'])) {
            $form->addError($this->view->translate("Backup Directory *  Please complete this field - it is required."));
            $validation_flage = 0;
          }
          if (!empty($_POST['ftpdirectoryname'])) {
            if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['ftpdirectoryname'])) {
              $form->addError($this->view->translate("The Backup Directory name must be alpha-numeric. Please do not enter any special characters or space in it."));
              $validation_flage = 0;
            }
          }

          if (!$validation_flage) {
            return;
          } else {

            // Attempt to connect to the remote server
            $conn = @ftp_connect($_POST['ftphost'], $_POST['ftpportno']);

            if (!$conn) {
              $form->addError($this->view->translate("FTP Connection could not be established. Please enter correct FTP Host and Port number."));
              return FALSE;
            }

            // Attempt to login to the remote server
            $login = @ftp_login($conn, $_POST['ftpuser'], $_POST['ftppassword']);

            if (!$login) {
              $form->addError($this->view->translate("FTP login failed. Please enter correct username and password combination."));
              return FALSE;
            }

            if (!(@ftp_chdir($conn, $_POST['ftppath']))) {
              $form->addError($this->view->translate("Path is not valid. Please enter a correct path."));
              return FALSE;
            } else {
              $currentdir = @ftp_pwd($conn);
              if (@ftp_chdir($conn, $currentdir . "/" . $this->view->ftpdirectory)) {
                if ($this->view->ftpdirectory != $_POST['ftpdirectoryname']) {
                  if (!(@ftp_rename($conn, $_POST['ftppath'] . "/" . $this->view->ftpdirectory, $_POST['ftppath'] . "/" . $_POST['ftpdirectoryname']))) {
                    $form->addError($this->view->translate("You do not have permission for rename directory"));
                    return FALSE;
                  }
                }
              } else {
                if (!(@ftp_chdir($conn, $currentdir . "/" . $_POST['ftpdirectoryname']))) {
                  if (!(@ftp_mkdir($conn, $_POST['ftppath'] . "/" . $_POST['ftpdirectoryname']))) {
                    $form->addError($this->view->translate("You do not have permission for create directory"));
                    return FALSE;
                  }
                }
              }
            }
          }
          break;
        case 3 :
          if (empty($_POST['dbhost'])) {
            $form->addError($this->view->translate("Host *  Please complete this field - it is required."));
            $validation_flage = 0;
          }
          if (empty($_POST['dbname'])) {
            $form->addError($this->view->translate("Database Name *  Please complete this field - it is required."));
            $validation_flage = 0;
          }
          if (!empty($_POST['dbname'])) {
            if (!preg_match('/[a-zA-Z0-9_\$]+/', $_POST['dbname'])) {
              $form->addError($this->view->translate('The database name is not valid.'));
            }
          }
          if (empty($_POST['dbuser'])) {
            $form->addError($this->view->translate("User *  Please complete this field - it is required."));
            $validation_flage = 0;
          }

          if (!$validation_flage) {
            return;
          } else {
            $dbconn = @mysql_connect($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpassword']);
            if (!$dbconn) {
              $form->addError("Could not connect to database: " . mysql_error());
              return;
            }

            $db_selected = @mysql_select_db($_POST['dbname'], $dbconn);
            if (!$db_selected) {
              $form->addError('Could not connect to database: ' . mysql_error());
              return;
            }
          }
          break;
      }

      $values['destination_mode'] = $row->destination_mode;
      try {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $row->setFromArray($values);
        $row->toarray();
        $row->save();
        $db->commit();
        // REDIRECT TO INDEX ACTION
        $this->_helper->redirector->gotoRoute(array('action' => 'index', 'show' => 1));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
    if ($destination_page == 1) {
      $this->view->destination_block = $destination_page;
    } else {
      $this->view->destination_block = 0;
    }
  }

  // Delete action
  public function deleteAction() {
    $this->view->id = $id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      try {
        // delete the section entry into the database
        $row = Engine_Api::_()->getItem('destinations', $id);
        $row->delete();
        $table = Engine_Api::_()->getDbTable('settings', 'core');
        $table->update(array('value' => 1), array('name =?' => 'dbbackup.backupoptions'));

        Engine_Api::_()->getApi('settings', 'core')->setSetting('destinations', 0);
        $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
        ));
      } catch (Exception $e) {
        throw $e;
      }
    }
    // Output
    $this->renderScript('admin-destinationsettings/delete.tpl', array('class' => 'smoothbox'));
  }

}
?>