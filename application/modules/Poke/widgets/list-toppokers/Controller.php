<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: Controller.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_Widget_ListToppokersController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		// Get user table info
    $userTable = Engine_Api::_()->getItemTable('user');
    $userTableName = $userTable->info('name');
    
    // Get poke table info
    $pokeTableName = Engine_Api::_()->getDbtable('pokeusers', 'poke')->info('name');	

    //Here we selecting how many rows of pokeback=0 in the table.
    $select = $userTable->select()
			->setIntegrityCheck( false )	
			->from($userTableName, array('*', 'count(*) as poke_count'))
			->join($pokeTableName, "`{$pokeTableName}`.resourceid=`{$userTableName}`.`user_id`")
      //->where("$pokeTableName.`isdeleted` = 1")
      ->group("$pokeTableName.resourceid")
      ->order("poke_count DESC")
			->order("$pokeTableName.created DESC")
			->limit($this->_getParam('itemCountPerPage', 4)); 

		//Here we faching the results of the rows.
	  $rowinfo = $userTable->fetchAll($select); 
	  
		//Here we count how many no of rows found in the table for pokeback=0.
    $rowinfocount = count($rowinfo);
    
 		//Here we passing all the information to the tpl.
	  if($rowinfocount !== null) {
			$this->view->paginator = $paginator = $rowinfo;
		} 

		//Here we checking if both rows are zero then we not rendering the tpl.
		if( $rowinfocount == 0) {
			return  $this->setNoRender();
		}
	}
}
?>