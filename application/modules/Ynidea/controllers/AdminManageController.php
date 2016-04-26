<?php
class Ynidea_AdminManageController extends Core_Controller_Action_Admin
{
  
  public function indexAction()
  {
    // Get navigation bar
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynidea_admin_main', array(), 'ynidea_admin_main_manage');
    
    $this->view->form = $form = new Ynidea_Form_Admin_Search;
    
    $form->isValid($this->_getAllParams());
    $params = $form->getValues();
    
    if(empty($params['orderby'])) $params['orderby'] = 'creation_date';
    if(empty($params['direction'])) $params['direction'] = 'DESC';
    $this->view->formValues = $params;
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $idea = Engine_Api::_()->getItem('ynidea_idea', $value);          
          if( $idea ) $idea->delete();
        }
      }
    }
   	$params['admin'] = 1;
    $this->view->paginator = Engine_Api::_()->ynidea()->getIdeaPaginator($params);
    
    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('idea.page',10);
    $this->view->paginator->setItemCountPerPage($items_per_page);
    if(isset($params['page'])) $this->view->paginator->setCurrentPageNumber($params['page']);
    
  }

  /*----- Delete idea Function-----*/
  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->idea_id = $id;
    
    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      //Process delete action
      try
      {        
        $idea = Engine_Api::_()->getItem('ynidea_idea', $id);
        
        // delete the page into the database                
        $idea->delete();        
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      // Refresh parent page
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-manage/delete.tpl');
  }
  public function editDecisionAction()
  {
  	 $this->view->form = $form = new Ynidea_Form_Admin_Decision;
	 $idea = Engine_Api::_()->getItem('ynidea_idea', $this->_getParam('id'));
	 
	 $form->getElement('decision')->setValue($idea->decision);
	 
	 if( !$this->getRequest()->isPost() ) {
            return;
     }
    $post = $this->getRequest()->getPost();
    if(!$form->isValid($post))
        return;
    $idea->decision = $post['decision'];
	$idea->save();
	 $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('Edit successfully')
      ));
  }
}