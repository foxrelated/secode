<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminFieldsController.php
 * @author     Minh Nguyen
 */
class Groupbuy_AdminFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'groupbuy_deal';

  protected $_requireProfileType = false;

  public function indexAction()
  {
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('groupbuy_admin_main', array(), 'groupbuy_admin_main_fields');
    parent::indexAction();
  }

  public function fieldCreateAction(){
    parent::fieldCreateAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form->setTitle('Add Deal Question');
      $display = $form->getElement('display');
      $display->setLabel('Show on deal page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on deal page',
          0 => 'Hide on deal page'
        )));
    }
  }

  public function fieldEditAction(){
    parent::fieldEditAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form->setTitle('Edit Deal Question');

      $display = $form->getElement('display');
      $display->setLabel('Show on deal page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on deal page',
          0 => 'Hide on deal page'
        )));
    }
  }
}