<?php
class Ynresponsivemetro_AdminMenusController extends Core_Controller_Action_Admin
{
  protected $_enabledModuleNames;
  public function init()
  {
    $this->_enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    $this -> view -> navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynresponsivemetro_admin_main', array(), 'ynresponsivemetro_admin_main_manage_menus');
  }
  
  public function indexAction()
  {
    $name = 'core_main';
    // Get menu items
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
    $menuItemsSelect = $menuItemsTable->select()
      ->where('menu = ?', $name)
      ->order('order');
    if( !empty($this->_enabledModuleNames) ) {
      $menuItemsSelect->where('module IN(?)',  $this->_enabledModuleNames);
    }
    $this->view->menuItems = $menuItems = $menuItemsTable->fetchAll($menuItemsSelect);
  }

  public function editAction()
  {
    $this->view->name = $name = $this->_getParam('name');

    // Get menu item
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
    $menuItemsSelect = $menuItemsTable->select()
      ->where('name = ?', $name);
    if( !empty($this->_enabledModuleNames) ) {
      $menuItemsSelect->where('module IN(?)',  $this->_enabledModuleNames);
    }
    $this->view->menuItem = $menuItem = $menuItemsTable->fetchRow($menuItemsSelect);

    if( !$menuItem ) {
      throw new Core_Model_Exception('missing menu item');
    }

    // Get form
    $this->view->form = $form = new Ynresponsivemetro_Form_Admin_Menu_ItemEdit();
	$url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this -> view -> url(array('controller' => 'files'), 'admin_default', true);
	$form -> addNotice($this -> view -> translate('Please go to <a href = "%s" target = "_blank">File & Media Manager</a> to upload and copy URL for icons.', $url));
	$form -> addNotice("If you do not want to set color in color textbox, please kindly copy `transparent` and paste it into color textbox. Please kindly remove `transparent` before select color.");
    // Make safe
    $menuItemData = $menuItem->toArray();
    if( isset($menuItemData['params']) && is_array($menuItemData['params']) ) {
      $menuItemData = array_merge($menuItemData, $menuItemData['params']);
    }
    if( !$menuItem->custom ) {
      $form->removeElement('uri');
    }
    unset($menuItemData['params']);

    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      $form->populate($menuItemData);
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Save
    $values = $form->getValues();

    $menuItem->label = $values['label'];
    $menuItem->enabled = !empty($values['enabled']);
    unset($values['label']);
    unset($values['enabled']);

    if ($menuItem -> custom)
	{
		$menuItem -> params = $values;
	}
	else
	{
		if ($menuItem -> params)
		{
			$menuItem -> params = array_merge($menuItem -> params, $values);
		}
		else
		{
			$menuItem -> params = $values;
		}
	}
    if( !empty($values['target']) ) {
      $menuItem->params = array_merge($menuItem->params, array('target' => $values['target']));
    } else if( isset($menuItem->params['target']) ){
      // Remove the target
      $tempParams = array();
      foreach( $menuItem->params as $key => $item ){
        if( $key != 'target' ){
          $tempParams[$key] = $item;
        }
      }
      $menuItem->params = $tempParams; 
    }
    $menuItem->save();
    
    $this->view->status = true;
    $this->view->form = null;
  }

  public function orderAction()
  {
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    
    $table = Engine_Api::_()->getDbtable('menuItems', 'core');
    $menuitems = $table->fetchAll($table->select()->where("menu = 'core_main'"));
    foreach( $menuitems as $menuitem ) 
    {
      $order = $this->getRequest()->getParam('admin_menus_item_'.$menuitem->name);
      if( !$order ){
        $order = 999;
      }
      $menuitem->order = $order;
      $menuitem->save();
    }
    return;
  }

}
