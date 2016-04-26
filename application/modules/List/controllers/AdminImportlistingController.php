<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: AdminImportListingController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_AdminImportlistingController extends Core_Controller_Action_Admin {

	//ACTION FOR SHOWING IMPORT INSTRUCTIONS
	public function indexAction() { 
    
		//INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('list_admin_main', array(), 'list_admin_main_import');
	}

  //ACTION FOR IMPORTING DATA FROM CSV FILE
  public function importAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');

    //MAKE FORM
    $this->view->form = $form = new List_Form_Admin_Import_Import();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //MAKE SURE THAT FILE EXTENSION SHOULD NOT DIFFER FROM ALLOWED TYPE
      $ext = str_replace(".", "", strrchr($_FILES['filename']['name'], "."));
      if (!in_array($ext, array('csv', 'CSV'))) {
        $error = $this->view->translate("Invalid file extension. Only 'csv' extension is allowed.");
        $error = Zend_Registry::get('Zend_Translate')->_($error);

        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      //START READING DATA FROM CSV FILE
      $fname = $_FILES['filename']['tmp_name'];
      $fp = fopen($fname, "r");

      if (!$fp) {
        echo "$fname File opening error";
        exit;
      }
			
			$formData = array();
			$formData = $form->getValues();

			if($formData['import_seperate'] == 1) {
				while ($buffer = fgets($fp, 4096)) {
					$explode_array[] = explode('|', $buffer);
				}
			}
			else {
				while ($buffer = fgets($fp, 4096)) {
					$explode_array[] = explode(',', $buffer);
				}
			}
      //END READING DATA FROM CSV FILE

      $import_count = 0;
      foreach ($explode_array as $explode_data) {

        //GET LIST DETAILS FROM DATA ARRAY
        $values = array();
        $values['title'] = trim($explode_data[0]);
        $values['category'] = trim($explode_data[1]);
        $values['sub_category'] = trim($explode_data[2]);
        $values['body'] = trim($explode_data[3]);
        $values['overview'] = trim($explode_data[4]);
        $values['tags'] = trim($explode_data[5]);
				$values['location'] = trim($explode_data[6]);

        //IF LIST TITLE AND CATEGORY IS EMPTY THEN CONTINUE;
        if (empty($values['title']) || empty($values['body'])) {
          continue;
        }

        $db = Engine_Api::_()->getDbtable('imports', 'list')->getAdapter();
        $db->beginTransaction();

        try {
          $import = Engine_Api::_()->getDbtable('imports', 'list')->createRow();
          $import->setFromArray($values);
          $import->save();

          //COMMIT
          $db->commit();

          if (empty($import_count)) {
            $first_import_id = $last_import_id = $import->import_id;

            //SAVE DATA IN `engine4_list_importfiles` TABLE
            $db = Engine_Api::_()->getDbtable('importfiles', 'list')->getAdapter();
            $db->beginTransaction();

            try {

              //FETCH PRIVACY
              if (empty($formData['auth_view'])) {
                $formData['auth_view'] = "everyone";
              }

              if (empty($formData['auth_comment'])) {
                $formData['auth_comment'] = "everyone";
              }

              //SAVE OTHER DATA IN engine4_list_importfiles TABLE
              $importFile = Engine_Api::_()->getDbtable('importfiles', 'list')->createRow();
              $importFile->filename = $_FILES['filename']['name'];
              $importFile->status = 'Pending';
              $importFile->first_import_id = $first_import_id;
              $importFile->last_import_id = $last_import_id;
              $importFile->current_import_id = $first_import_id;
              $importFile->first_listing_id = 0;
              $importFile->last_listing_id = 0;
              $importFile->view_privacy = $formData['auth_view'];
              $importFile->comment_privacy = $formData['auth_comment'];
              $importFile->save();

              //COMMIT
              $db->commit();
            } catch (Exception $e) {
              $db->rollBack();
              throw $e;
            }
          } else {

            //UPDATE LAST IMPORT ID
            $last_import_id = $import->import_id;
            $importFile->last_import_id = $last_import_id;
            $importFile->save();
          }

          $import_count++;
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }

      //CLOSE THE SMOOTHBOX
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRedirect' => $this->_helper->url->url(array('module' => 'list', 'controller' => 'admin-importlisting', 'action' => 'manage')),
          'parentRedirectTime' => '15',
          'format' => 'smoothbox',
					'messages' => array(Zend_Registry::get('Zend_Translate')->_('CSV file has been imported succesfully !'))
      ));
    }
  }

  //ACTION FOR IMPORTING DATA FROM CSV FILE
  public function dataImportAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');
		$current_import = $this->_getParam('current_import');

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //RETURN IF importfile_id IS EMPTY
    if (empty($importfile_id)) {
      return;
    }

    //GET IMPORT FILE OBJECT
    $importFile = Engine_Api::_()->getItem('list_importfile', $importfile_id);
    if (empty($importFile)) {
      return;
    }

		//CHECK IF IMPORT WORK IS ALREADY IN RUNNING STATUS FOR SOME FILE
		$tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'list');
		$importFileStatusData = $tableImportFile->fetchRow(array('status = ?' => 'Running'));
		if(!empty($importFileStatusData) && empty($current_import)) {
			return;
		}

		//UPDATE THE STATUS
		$importFile->status = 'Running';
		$importFile->save();

    $first_import_id = $importFile->first_import_id;
    $last_import_id = $importFile->last_import_id;

    $current_import_id = $importFile->current_import_id;
    $return_current_import_id = $this->_getParam('current_import_id');
    if (!empty($return_current_import_id)) {
      $current_import_id = $this->_getParam('current_import_id');
    }

    //MAKE QUERY
    $tableImport = Engine_Api::_()->getDbtable('imports', 'list');

    $sqlStr = "import_id BETWEEN " . "'" . $current_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

    $select = $tableImport->select()
                    ->from($tableImport->info('name'), array('import_id'))
                    ->where($sqlStr);
    $importDatas = $select->query()->fetchAll();

    if (empty($importDatas)) {
      return;
    }

    //START CODE FOR CREATING THE CSVToListImport.log FILE
    if (!file_exists(APPLICATION_PATH . '/temporary/log/CSVToListImport.log')) {
      $log = new Zend_Log();
      try {
        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/CSVToListImport.log'));
      } catch (Exception $e) {
        //CHECK DIRECTORY
        if (!@is_dir(APPLICATION_PATH . '/temporary/log') &&
                @mkdir(APPLICATION_PATH . '/temporary/log', 0777, true)) {
          $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/CSVToListImport.log'));
        } else {
          //Silence ...
          if (APPLICATION_ENV !== 'production') {
            $log->log($e->__toString(), Zend_Log::CRIT);
          } else {
            //MAKE SURE LOGGING DOESN'T CAUSE EXCEPTIONS
            $log->addWriter(new Zend_Log_Writer_Null());
          }
        }
      }
    }

    //GIVE WRITE PERMISSION TO LOG FILE IF EXIST
    if (file_exists(APPLICATION_PATH . '/temporary/log/CSVToListImport.log')) {
      @chmod(APPLICATION_PATH . '/temporary/log/CSVToListImport.log', 0777);
    }
    //END CODE FOR CREATING THE CSVToListImport.log FILE

    //GET LIST TABLE
    $listTable = Engine_Api::_()->getItemTable('list_listing');

    $import_count = 0;

    //START THE IMPORT WORK
    foreach ($importDatas as $importData) {

      //GET IMPORT FILE OBJECT
      $importFile = Engine_Api::_()->getItem('list_importfile', $importfile_id);

      //BREAK IF STATUS IS STOP
      if ($importFile->status == 'Stopped') {
        break;
      }

      $import_id = $importData['import_id'];
      if (empty($import_id)) {
        continue;
      }

      $import = Engine_Api::_()->getItem('list_import', $import_id);
      if (empty($import)) {
        continue;
      }

      //GET LIST DETAILS FROM DATA ARRAY
      $values = array();
      $values['title'] = $import->title;
      $list_category = $import->category;
      $list_subcategory = $import->sub_category;
      $values['body'] = $import->body;
      $values['overview'] = $import->overview;
			$values['location'] = $import->location;
      $list_tags = $import->tags;
      $values['owner_id'] = $viewer->getIdentity();

      //IF LIST TITLE AND DESCRIPTION IS EMPTY THEN CONTINUE;
      if (empty($values['title']) || empty($values['body'])) {
        continue;
      }

      $db = $listTable->getAdapter();
      $db->beginTransaction();

      try {

        $list = $listTable->createRow();
        $list->setFromArray($values);

        $list->approved = 1;
        $list->approved_date = date('Y-m-d H:i:s');

        $list->view_count = 0;
        $list->save();
        $listing_id = $list->listing_id;

        $importFile->current_import_id = $import->import_id;
        $importFile->last_listing_id = $listing_id;
        $importFile->save();

        if (empty($importFile->first_listing_id)) {
          $importFile->first_listing_id = $listing_id;
          $importFile->save();
        }
        $import_count++;

        $list->save();

        //START CATEGORY WORK
				if(!empty($list_category)) {
					$listCategoryTable = Engine_Api::_()->getDbtable('categories', 'list');
					$listCategory = $listCategoryTable->fetchRow(array('category_name = ?' => $list_category, 'cat_dependency = ?' => 0));
					if (!empty($listCategory)) {
						$list->category_id = $listCategory->category_id;

						$listSubcategory = $listCategoryTable->fetchRow(array('category_name = ?' => $list_subcategory, 'cat_dependency = ?' => $list->category_id));

						if (!empty($listSubcategory)) {
							$list->subcategory_id = $listSubcategory->category_id;
						}
					}
				}
        //END CATEGORY WORK
        //SAVE TAGS
        $tags = preg_split('/[#]+/', $list_tags);
        $tags = array_filter(array_map("trim", $tags));
        $list->tags()->addTagMaps($viewer, $tags);
				$list->save();

				$list->setLocation();

        //SET PRIVACY
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $privacyMax = array_search('everyone', $roles);

        if (empty($importFile->view_privacy)) {
          $importFile->view_privacy = "everyone";
        }

        if (empty($importFile->comment_privacy)) {
          $importFile->comment_privacy = "everyone";
        }

        $viewMax = array_search($importFile->view_privacy, $roles);
        $commentMax = array_search($importFile->comment_privacy, $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($list, $role, 'view', ($i <= $viewMax));
          $auth->setAllowed($list, $role, 'comment', ($i <= $commentMax));
        }
       
        //Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //IF ALL LISTS HAS BEEN IMPORTED THAN CHANGE THE STATUS
      if ($importFile->current_import_id == $importFile->last_import_id) {
        $importFile->status = 'Completed';
      }
      $importFile->save();

      //CREATE LOG ENTRY IN LOG FILE
      if (file_exists(APPLICATION_PATH . '/temporary/log/CSVToListImport.log')) {

				$stringData = '';
				if($import_count == 1) {
					$stringData .= "\n\n----------------------------------------------------------------------------------------------------------------\n";
					$stringData .= $this->view->translate("Import History of '").$importFile->filename.$this->view->translate("' with file id: ").$importFile->importfile_id.$this->view->translate(", created on ").$importFile->creation_date.$this->view->translate(" is given below.");
					$stringData .= "\n----------------------------------------------------------------------------------------------------------------\n\n";
				}
				
        $myFile = APPLICATION_PATH . '/temporary/log/CSVToListImport.log';
        $fh = fopen($myFile, 'a') or die("can't open file");
        $current_time = date('D, d M Y H:i:s T');
        $listing_id = $list->listing_id;
        $list_title = $list->title;
        $stringData .= $this->view->translate("Successfully created a new listing at ").$current_time.$this->view->translate(". ID and title of that List are ").$listing_id.$this->view->translate(" and '").$list_title.$this->view->translate("' respectively.")."\n\n";
        fwrite($fh, $stringData);
        fclose($fh);
      }

      if ($import_count >= 100) {
        $current_import_id = $importFile->current_import_id + 1;
        $this->_redirect("admin/list/importlisting/data-import?importfile_id=$importfile_id&current_import_id=$current_import_id&current_import=1");
      }
    }

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('Importing is done successfully !'))
    ));
  }

  //ACTION FOR MANAGING THE CSV FILES DATAS
  public function manageAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('list_admin_main', array(), 'list_admin_main_import');

    //FORM CREATION FOR SORTING
    $this->view->formFilter = $formFilter = new List_Form_Admin_Import_Filter();
    $list = $this->_getParam('page', 1);

    $tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'list');
    $select = $tableImportFile->select();

		//IF IMPORT IS IN RUNNING STATUS FOR SOME FILE THAN DONT SHOW THE START BUTTON FOR ALL
		$importFileStatusData = $tableImportFile->fetchRow(array('status = ?' => 'Running'));
		$this->view->runningSomeImport = 0;
		if(!empty($importFileStatusData)) {
			$this->view->runningSomeImport = 1;
		}

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
                'order' => 'importfile_id',
                'order_direction' => 'DESC',
                    ), $values);

    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'importfile_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->total_slideshows = $paginator->getTotalItemCount();
    $this->view->paginator->setItemCountPerPage(100);
    $this->view->paginator = $paginator->setCurrentPageNumber($list);
  }

  //ACTION FOR STOP IMPORTING DATA
  public function stopAction() {

    //UPDATE THE STATUS TO STOP
    $importfile_id = $this->_getParam('importfile_id');
    $importFile = Engine_Api::_()->getItem('list_importfile', $importfile_id);
    $importFile->status = 'Stopped';
    $importFile->save();

    //REDIRECTING TO MANAGE LIST IF FORCE STOP
    $forceStop = $this->_getParam('forceStop');
    if (!empty($forceStop)) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
    }
  }

  //ACTION FOR ROLLBACK IMPORTING DATA
  public function rollbackAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');

    //FETCH IMPORT FILE OBJECT
    $importFile = Engine_Api::_()->getItem('list_importfile', $importfile_id);

    //IF STATUS IS PENDING THAN RETURN
    if ($importFile->status == 'Pending') {
      return;
    }

    $returend_current_listing_id = $this->_getParam('current_listing_id');

		$redirect = 0;
		if(isset($_GET['redirect'])) {
			$redirect = $_GET['redirect'];
		}

		if(empty($redirect) && isset($_POST['redirect'])) {
			$redirect = $_POST['redirect'];
		}

    //START ROLLBACK IF CONFIRM BY USER OR RETURNED CURRENT LIST ID IS NOT EMPTY
    if (!empty($redirect)) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $first_listing_id = $importFile->first_listing_id;
        $last_listing_id = $importFile->last_listing_id;

        if (!empty($first_listing_id) && !empty($last_listing_id)) {
          $listTable = Engine_Api::_()->getDbtable('listings', 'list');

          $current_listing_id = $first_listing_id;

          if (!empty($returend_current_listing_id)) {
            $current_listing_id = $returend_current_listing_id;
          }

          //MAKE QUERY
          $sqlStr = "listing_id BETWEEN " . "'" . $current_listing_id . "'" . " AND " . "'" . $last_listing_id . "'" . "";

          $select = $listTable->select()
                          ->from($listTable->info('name'), array('listing_id'))
                          ->where($sqlStr);
          $listDatas = $select->query()->fetchAll();

          if (!empty($listDatas)) {
            $rollback_count = 0;
            foreach ($listDatas as $listData) {
              $listing_id = $listData['listing_id'];

              //DELETE LIST
							$list = Engine_Api::_()->getItem('list_listing', $listing_id);
							$list->delete();

              $db->commit();

              $rollback_count++;

              //REDIRECTING TO SAME ACTION AFTER EVERY 100 ROLLBACKS
              if ($rollback_count >= 100) {
                $current_listing_id = $listing_id + 1;
                $this->_redirect("admin/list/importlisting/rollback?importfile_id=$importfile_id&current_listing_id=$current_listing_id&redirect=1");
              }
            }
          }
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //UPDATE THE DATA IN engine4_list_importfiles TABLE
      $importFile->status = 'Pending';
      $importFile->first_listing_id = 0;
      $importFile->last_listing_id = 0;
      $importFile->current_import_id = $importFile->first_import_id;
      $importFile->save();

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
					'messages' => array(Zend_Registry::get('Zend_Translate')->_('Rollbacked successfully !'))
      ));
    }
    $this->renderScript('admin-importlisting/rollback.tpl');
  }

  //ACTION FOR DELETE IMPORT FILES AND IMPORT DATA
  public function deleteAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');

    //IF CONFIRM FOR DATA DELETION
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //IMPORT FILE OBJECT
        $importFile = Engine_Api::_()->getItem('list_importfile', $importfile_id);

        if (!empty($importFile)) {

          $first_import_id = $importFile->first_import_id;
          $last_import_id = $importFile->last_import_id;

          //MAKE QUERY FOR FETCH THE DATA
          $tableImport = Engine_Api::_()->getDbtable('imports', 'list');

          $sqlStr = "import_id BETWEEN " . "'" . $first_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

          $select = $tableImport->select()
                          ->from($tableImport->info('name'), array('import_id'))
                          ->where($sqlStr);
          $importDatas = $select->query()->fetchAll();

          if (!empty($importDatas)) {
            foreach ($importDatas as $importData) {
              $import_id = $importData['import_id'];

              //DELETE IMPORT DATA BELONG TO IMPORT FILE
              $tableImport->delete(array('import_id = ?' => $import_id));
            }
          }

          //FINALLY DELETE IMPORT FILE DATA
          Engine_Api::_()->getDbtable('importfiles', 'list')->delete(array('importfile_id = ?' => $importfile_id));
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
					'messages' => array(Zend_Registry::get('Zend_Translate')->_('Import data has been deleted successfully !'))
      ));
    }
    $this->renderScript('admin-importlisting/delete.tpl');
  }

  //ACTION FOR DELETE SLIDESHOW AND THEIR BELONGINGS
  public function multiDeleteAction() {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

      //IF ADMIN CLICK ON DELETE SELECTED BUTTON
      if (!empty($values['delete'])) {
        foreach ($values as $key => $value) {
          if ($key == 'delete_' . $value) {
            $importfile_id = (int) $value;
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
              //IMPORT FILE OBJECT
              $importFile = Engine_Api::_()->getItem('list_importfile', $importfile_id);

              if (!empty($importFile)) {

                $first_import_id = $importFile->first_import_id;
                $last_import_id = $importFile->last_import_id;

                //MAKE QUERY FOR FETCH THE DATA
                $tableImport = Engine_Api::_()->getDbtable('imports', 'list');

                $sqlStr = "import_id BETWEEN " . "'" . $first_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

                $select = $tableImport->select()
                                ->from($tableImport->info('name'), array('import_id'))
                                ->where($sqlStr);
                $importDatas = $select->query()->fetchAll();

                if (!empty($importDatas)) {
                  foreach ($importDatas as $importData) {
                    $import_id = $importData['import_id'];

                    //DELETE IMPORT DATA BELONG TO IMPORT FILE
                    $tableImport->delete(array('import_id = ?' => $import_id));
                  }
                }

                //FINALLY DELETE IMPORT FILE DATA
                Engine_Api::_()->getDbtable('importfiles', 'list')->delete(array('importfile_id = ?' => $importfile_id));
              }

              $db->commit();
            } catch (Exception $e) {
              $db->rollBack();
              throw $e;
            }
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
  }

  //ACTION FOR DOWNLOADING THE CSV TEMPLATE FILE
  public function downloadAction() {
    //GET PATH
    $basePath = realpath(APPLICATION_PATH . "/application/modules/List/settings");

    $path = $this->_getPath();

    if (file_exists($path) && is_file($path)) {
      //KILL ZEND'S OB
      while (ob_get_level() > 0) {
        ob_end_clean();
      }

      header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
      header("Content-Transfer-Encoding: Binary", true);
      header("Content-Type: application/x-tar", true);
      header("Content-Type: application/force-download", true);
      header("Content-Type: application/octet-stream", true);
      header("Content-Type: application/download", true);
      header("Content-Description: File Transfer", true);
      header("Content-Length: " . filesize($path), true);
      readfile("$path");
    }

    exit();
  }

  protected function _getPath($key = 'path') {
    $basePath = realpath(APPLICATION_PATH . "/application/modules/List/settings");
    return $this->_checkPath($this->_getParam($key, ''), $basePath);
  }

  protected function _checkPath($path, $basePath) {
    //SANATIZE
    $path = preg_replace('/\.{2,}/', '.', $path);
    $path = preg_replace('/[\/\\\\]+/', '/', $path);
    $path = trim($path, './\\');
    $path = $basePath . '/' . $path;

    //Resolve
    $basePath = realpath($basePath);
    $path = realpath($path);

    //CHECK IF THIS IS A PARENT OF THE BASE PATH
    if ($basePath != $path && strpos($basePath, $path) !== false) {
      return $this->_helper->redirector->gotoRoute(array());
    }
    return $path;
  }

}
