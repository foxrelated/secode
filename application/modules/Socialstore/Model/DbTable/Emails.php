<?php
 /**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Params.php
 * @author     Long Le
 */
class Socialstore_Model_DbTable_Emails extends Engine_Db_Table
{
  protected $_rowClass = 'Socialstore_Model_Email';
  
  public function add($params){
  	
	$item  = $this->fetchNew();
	$item->setFromArray($params);
	$item->save();
	return $item;
  }
}