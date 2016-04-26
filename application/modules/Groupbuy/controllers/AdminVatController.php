<?php

class Groupbuy_AdminVatController extends Core_Controller_Action_Admin {

  public function indexAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('groupbuy_admin_main', array(), 'groupbuy_admin_main_vats');

    $table = new Groupbuy_Model_DbTable_Vats;
    $select = $table->select();

    $paginator = $this->view->paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $paginator->setItemCountPerPage(10);
  }

  public function editVatAction() {
    //Get Form Edit VAT
    $form = $this->view->form = new Groupbuy_Form_Admin_Vat();

    //Check Post Method
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        // Edit VAT In The Database
        $vat_id = $values["vat_id"];
        $table  = new Groupbuy_Model_DbTable_Vats;
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
      throw new Zend_Exception('No VAT Id specified');
    }

    // Generate and assign form
    $table = new Groupbuy_Model_DbTable_Vats;
    $select = $table->select()->where('vat_id = ?', "$vat_id");
    $vat = $table->fetchRow($select);
    
    $form->submit->setLabel('Edit VAT');
    $form->populate(array('name'   => $vat->name,
                          'value'  => $vat->value,
                          'vat_id' => $vat->vat_id));
    
    //Output
    $this->renderScript('admin-vat/form.tpl');
  }

  public function deleteVatAction()
  {
    //Get Delete Form
    $form = $this->view->form = new Groupbuy_Form_Admin_Vat_Delete();
    
    //Check Post Method
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
          $values = $form->getValues();
          $db = Engine_Db_Table::getDefaultAdapter();
          $db->beginTransaction();
          try{
              //Get Row From Database
              $vat_id = $values["vat_id"];
              $table = new Groupbuy_Model_DbTable_Vats;
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
      throw new Zend_Exception('No VAT Id specified');
    }

    //Generate form
    $form->populate(array('vat_id' => $vat_id));
    
    //Output
    $this->renderScript('admin-vat/form.tpl');
  }

  public function addVatAction(){
    //Get VAT Form
    $form = $this->view->form = new Groupbuy_Form_Admin_Vat();

    //Check Post Method
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
          //Get Form Values And Create Database Connection
          $values = $form->getValues();
          $db = Engine_Db_Table::getDefaultAdapter();
          $db->beginTransaction();
          if(!is_numeric($values['value']) || $values['value'] <= 0)
          {
                $form->getElement('value')->addError('The value number is invalid! (Ex: 10)');
                $this->renderScript('admin-vat/form.tpl');
                return false;
          }
          try{
             //Insert Values Into A Row.
             $table = new Groupbuy_Model_DbTable_Vats;
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
    $this->renderScript('admin-vat/form.tpl');
  }

}
