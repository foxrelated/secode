<?php
class Ynidea_AdminTrophyController extends Core_Controller_Action_Admin
{
  
  public function indexAction()
  {
   
    // Get navigation bar
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynidea_admin_main', array(), 'ynidea_admin_main_trophy');
    
    
    
    $this->view->form = $form = new Ynidea_Form_Admin_SearchTrophy;
    
    $form->isValid($this->_getAllParams());
    $params = $form->getValues();
    
    if(empty($params['orderby'])) $params['orderby'] = 'creation_date';
    if(empty($params['direction'])) $params['direction'] = 'DESC';
    $this->view->formValues = $params;
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $idea = Engine_Api::_()->getItem('ynidea_trophy', $value);          
          if( $idea ) $idea->delete();
        }
      }
    }
   
    $this->view->paginator = Engine_Api::_()->ynidea()->getTrophyPaginator($params);
    
    $items_per_page = 10;// Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.page',10);
    $this->view->paginator->setItemCountPerPage($items_per_page);
    if(isset($params['page'])) $this->view->paginator->setCurrentPageNumber($params['page']);
    
  }

  /*----- Delete Page Function-----*/
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
        $idea = Engine_Api::_()->getItem('ynidea_trophy', $id);
        
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
    $this->renderScript('admin-trophy/delete.tpl');
  }
}