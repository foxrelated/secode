<?php

class Socialstore_AdminTaxesController extends Core_Controller_Action_Admin {
	public function init(){
		Zend_Registry::set('admin_active_menu', 'socialstore_admin_main_taxes');
	}

  public function indexAction() {
    
    $table = new Socialstore_Model_DbTable_Vats;
    $select = $table->select();

    $paginator = $this->view->paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $paginator->setItemCountPerPage(10);
  }

  public function editAction() {
    //Get Form Edit VAT
    $form = $this->view->form = new Socialstore_Form_Admin_Vat_Edit();

    //Check Post Method
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        // Edit VAT In The Database
        $vat_id = $values["vat_id"];
        $table  = new Socialstore_Model_DbTable_Vats;
        $select = $table -> select() -> where('vat_id = ?', "$vat_id");
        $row    = $table -> fetchRow($select);

        $row->name = $values["name"];
        $row->value = $values["value"];
        $row->modified_date = date('Y-m-d h:i:s');

        //Database Commit
        $row->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      //Close Form If Editing Successfully
      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
    }

    // Get Code Id - Throw Exception If There Is No Code Id
    if (!($vat_id = $this->_getParam('vat_id'))) {
      throw new Zend_Exception('No Taxes Id specified');
    }

    // Generate and assign form
    $table = new Socialstore_Model_DbTable_Vats;
    $select = $table->select()->where('vat_id = ?', "$vat_id");
    $vat = $table->fetchRow($select);
    
    $form->submit->setLabel('Edit Tax');
    $form->populate(array('name'   => $vat->name,
                          'value'  => $vat->value,
                          'vat_id' => $vat->vat_id));
    
    //Output
    $this->renderScript('admin-taxes/form.tpl');
  }

  public function deleteAction()
  {
    //Get Delete Form
    $form = $this->view->form = new Socialstore_Form_Admin_Vat_Delete();
    
    //Check Post Method
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
          $values = $form->getValues();
          $db = Engine_Db_Table::getDefaultAdapter();
          $db->beginTransaction();
          try{
              //Get Row From Database
              $vat_id = $values["vat_id"];
              $table = new Socialstore_Model_DbTable_Vats;
              $select = $table->select()->where('vat_id = ?', "$vat_id");
              $row = $table ->fetchRow($select);

              //Database Commit
              $row->delete();
              $db->commit();
          } catch (Exception $e) {
              $db->rollBack();
              throw $e;
          }
          
      //Close Form If Editing Successfully
      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
    }
    
    // Get Code Id - Throw Exception If There Is No Code Id
    if (!($vat_id = $this->_getParam('vat_id'))) {
      throw new Zend_Exception('No Taxed Id specified');
    }

    //Generate form
    $form->populate(array('vat_id' => $vat_id));
    
    //Output
    $this->renderScript('admin-taxes/form.tpl');
  }

  public function addAction(){
    //Get VAT Form
    $form = $this->view->form = new Socialstore_Form_Admin_Vat_Create();

    //Check Post Method
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
          //Get Form Values And Create Database Connection
          $values = $form->getValues();
          $db = Engine_Db_Table::getDefaultAdapter();
          $db->beginTransaction();
          try{
             //Insert Values Into A Row.
             $table = new Socialstore_Model_DbTable_Vats;
             $row = $table->createRow();
             $row->name = $values["name"];
             $row->value = $values["value"];
             $row->creation_date = date('Y-m-d h:i:s');
             $row->modified_date = date('Y-m-d h:i:s');

             $row->save();
             $db->commit();
          }
          catch (Exception $e) {
              $db->rollBack();
              throw $e;
          }

      //Close Form If Editing Successfully
      $this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
    }
    //Output
    $this->renderScript('admin-taxes/form.tpl');
  }

}
