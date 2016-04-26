<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Edit.php
 * @author     Minh Nguyen
 */
class Groupbuy_Form_Edit extends Groupbuy_Form_Create
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
		
	// check if currency is available if it is not running.
	if($this->_item->current_sold < 1){
		if(isset($this->currency)){
			$this->removeElement('currency');
		}
	}
			
    $this->setTitle('Edit Deal')
         ->setDescription('Edit your deal below, then click "Save Changes" to save your deal.');
     $this->addElement('Radio', 'cover', array(
      'label' => 'Album Cover',
    ));
    $this->execute->setLabel('Save Changes');
  }
}