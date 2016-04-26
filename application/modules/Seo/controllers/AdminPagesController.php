<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Seo
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

class Seo_AdminPagesController extends Core_Controller_Action_Admin
{
  
  public function init()
  {
    // Get subject
    $page = null;
    if( null !== ($pageIdentity = $this->_getParam('id')) ) {
      $page = Engine_Api::_()->getItem('seo_page', $pageIdentity);
      if( null !== $page ) {
        Engine_Api::_()->core()->setSubject($page);
      }
    }

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'edit' => 'seo_page',
    ));  
  }
  
  
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('seo_admin_main', array(), 'seo_admin_main_pages');
 
      
    $this->view->formFilter = $formFilter = new Seo_Form_Admin_Page_Filter();

    // Process form
    $values = array();
    if($formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }
    
    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
          
    $this->view->formValues = $values;

    $this->view->assign($values);
   
    $this->view->paginator = Engine_Api::_()->seo()->getSeoPagesPaginator($values);
    $this->view->paginator->setItemCountPerPage(20);
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page',1));

    $this->view->hook_installed = Engine_Api::_()->seo()->isLayoutHookInstalled();
        
  }

  
  public function addAction()
  {
    $this->view->form = $form = new Seo_Form_Admin_Page_Add();
    
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $values = $form->getValues();

      $uri = $values['url'];
      
      $params = array('action' => 'create', 'module' => 'seo', 'controller' => 'admin-pages');
      $router = $this->getFrontController()->getRouter();
      if (!empty($uri) && Zend_Uri::check($uri))
      {
        try
        {
          $uri = Zend_Uri::factory($uri);
          $request = new Zend_Controller_Request_Http($uri);
          
          $request = $router->route($request);
          
          $params = array_merge($params, array(
          	'm' => $request->getModuleName(),
            'c' => $request->getControllerName(),
            'a' => $request->getActionName()
          ));
          

          
        }
        catch (Exception $e)
        {
          
        }

      }
      
      $redirectUrl = $router->assemble($params, 'default', true);
      //die($redirectUrl);
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRedirectTime' => 10,
          'parentRedirect'=> $redirectUrl,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Redirecting to create form ...'))
      ));
    }    
    
  }
  
  public function createAction()
  {
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('seo_admin_main', array(), 'seo_admin_main_pages');    
    
    $this->view->form = $form = new Seo_Form_Admin_Page_Create();
    
    $form->setAction($this->getFrontController()->getRouter()->assemble(array('module'=>'seo', 'controller'=>'pages', 'action' => 'create'), 'admin_default', true));
    
    
    $prepopular = array(
    	'page_module' => $this->_getParam('m'),
      'page_controller' => $this->_getParam('c'),
      'page_action' => $this->_getParam('a'),
    );
    $form->populate($prepopular);
    
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $values = $form->getValues();

      $table = Engine_Api::_()->getDbtable('pages', 'seo');
      
      $db = $table->getDefaultAdapter();
      $db->beginTransaction();

      try
      {

        $page = $table->createRow();
        $page->setFromArray($values);
        $page->save();


        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
      /*
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('SEO Page created.'))
      ));
      */
    }
  }
  
  
  public function editAction()
  {
      
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('seo_admin_main', array(), 'seo_admin_main_pages');    
    
    $this->view->form = $form = new Seo_Form_Admin_Page_Edit();
    
    $page = Engine_Api::_()->core()->getSubject('seo_page');
    
    $form->populate($page->toArray());
    
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $values = $form->getValues();

      $table = Engine_Api::_()->getDbtable('pages', 'seo');
      
      $db = $table->getDefaultAdapter();
      $db->beginTransaction();

      try
      {

        $page->setFromArray($values);
        $page->save();


        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      
        $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved.");
        $form->addNotice($savedChangesNotice);

    }
  }
  
  public function deleteAction()
  {
    // In smoothbox
    $id = $this->_getParam('id');
    $this->view->seo_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $seo = Engine_Api::_()->getItem('seo_page', $id);
        $seo->delete();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('SEO Page deleted.'))
      ));
    }

  }
  
  public function deleteselectedAction()
  {
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach( $ids_array as $id ){
        $seo = Engine_Api::_()->getItem('seo_page', $id);
        if( $seo ) $seo->delete();
      }

      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

  }
  
  
  public function hookAction()
  {
    $installed = Engine_Api::_()->seo()->isLayoutHookInstalled();
    $this->view->layout_script = $layout_script = APPLICATION_PATH . DS . 'application/modules/Core/layouts/scripts/default.tpl';
    if (!$installed)
    {
      
      $content = file_get_contents($layout_script);
      
      $search = '<?php echo $this->headTitle()->toString()';
      $replace = '<?php echo $this->hooks("onRenderLayoutDefaultSeo", $this) ?>' . "\n  $search";
      
      $content = str_replace($search, $replace, $content);
      file_put_contents($layout_script, $content);
    }
    
    $this->view->installed = $installed = Engine_Api::_()->seo()->isLayoutHookInstalled();
    
  }
}