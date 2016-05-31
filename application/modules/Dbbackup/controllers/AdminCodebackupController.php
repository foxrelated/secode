<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: AdminCodebackupController.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_AdminCodebackupController extends Core_Controller_Action_Admin {

	public function indexAction() 
	{
		$order = $this->_getParam('order');
		$id = $this->_getParam('id');
		//Here we deleteing the files according to selection of how many files to keep old files.
		$deletefiles = Engine_Api::_()->dbbackup()->deletebackupfiles();  
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('dbbackup_admin_main', array(), 'dbbackup_admin_main_codebackup');
		include_once(APPLICATION_PATH ."/application/modules/Dbbackup/controllers/license/license3.php");
    $page = $this->_getParam('page', 1);
		if (!empty($order) && $id == 'dbbackup') {
      $this->view->paginator = Engine_Api::_()->dbbackup()->getDbbackupsPaginatorCodebackup(array(
                  'orderby' => 'dbbackup_id ' . $order, ));
    } 
    else if (!empty($order) && $id == 'filesize') {
      $this->view->paginator = Engine_Api::_()->dbbackup()->getDbbackupsPaginatorCodebackup(array(
                  'orderby' => 'backup_filesize ' . $order,   ));
    } 
    else if (!empty($order) && $id == 'time') {
      $this->view->paginator = Engine_Api::_()->dbbackup()->getDbbackupsPaginatorCodebackup(array(
                  'orderby' => 'backup_time ' . $order, ));
    } 
    else if (!empty($order) && $id == 'method') {
      $this->view->paginator = Engine_Api::_()->dbbackup()->getDbbackupsPaginatorCodebackup(array(
                  'orderby' => 'backup_method ' . $order,));
    } 
    else if (!empty($order) && $id == 'destinationname') {
      $this->view->paginator = Engine_Api::_()->dbbackup()->getDbbackupsPaginatorCodebackup(array(
                  'orderby' => 'destination_name ' . $order,));
    } 
    else if (!empty($order) && $id == 'status') {
      $this->view->paginator = Engine_Api::_()->dbbackup()->getDbbackupsPaginatorCodebackup(array(
                  'orderby' => 'backup_status ' . $order, ));
   	} 
   	else {
      $this->view->paginator = Engine_Api::_()->dbbackup()->getDbbackupsPaginatorCodebackup(array(
                  'orderby' => 'dbbackup_id ' . 'DESC',));
    }
    $this->view->order = $order;
		$dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
		$this->view->dir_name_temp = $dir_name_temp;
    $this->view->paginator->setItemCountPerPage(10);
    $this->view->paginator->setCurrentPageNumber($page);
		$latest_database_backup = Engine_Api::_()->dbbackup()->getcodebackup();
		if(!empty($latest_database_backup->dbbackup_id)) {
			$this->view->latesttime = Engine_Api::_()->dbbackup()->time_since($latest_database_backup->backup_time);
			$this->view->backup_id = $latest_database_backup->dbbackup_id;
		}
    $values=array();
    $values['getlogid'] = 1;
    $this->view->logresults = Engine_Api::_()->getDbtable('backuplogs', 'dbbackup')->getLog($values);
  }

  public function deleteAction() 
  {
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
        $backup_file = $row->backup_filename1;
        $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
        $path = APPLICATION_PATH . '/public/' . $dir_name_temp . '/' . $backup_file;
				if(file_exists($path))
        @unlink($path);
				$dbbackup->delete();
        $db->commit();
      } 
      catch (Exception $e) {
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
    $this->renderScript('admin-codebackup/delete.tpl');
  }

  public function deleteselectedAction() 
  {
		$ids = $_POST['ids'];
    $this->view->ids = $ids;
    $this->view->count = explode(",", $ids);
    // Save values
		if ($this->getRequest()->isPost()) {
			$values = $this->getRequest()->getPost();
      $ids = $values['ids'];
      $ids_array = explode(",", $ids);
      foreach ($ids_array as $id) {
        $dbbackupid = Engine_Api::_()->getItem('dbbackup', $id);
        $backup_file = $dbbackupid->backup_filename1;
        $dir_name_temp = Engine_Api::_()->getApi('settings', 'core')->dbbackup_directoryname;
        $path = APPLICATION_PATH . '/public/' . $dir_name_temp . '/' . $backup_file;
				if(file_exists($path))
        @unlink($path);
        if ($dbbackupid)
          $dbbackupid->delete();
      }
			$this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }
}