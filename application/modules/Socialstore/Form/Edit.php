<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Edit.php
 * @author     Long Le
 */
class Socialstore_Form_Edit extends Socialstore_Form_Create
{
  public $_error = array();
  protected $_item;

  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }  
  public function init()
  {
    parent::init();
		
    $this->setTitle('Edit Store')
         ->setDescription('Edit your store below, then click "Save Changes" to save your store.');

    $this->execute->setLabel('Save Changes');
  }
}