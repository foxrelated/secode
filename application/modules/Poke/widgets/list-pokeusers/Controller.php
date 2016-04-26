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
class Poke_Widget_ListPokeusersController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		//Getting the viewer user id.
    $userid = Engine_Api::_()->user()->getViewer()->getIdentity();

		// Get user table info
    $tables = Engine_Api::_()->getItemTable('user');
    $userTableName = $tables->info('name');
    
    // Get poke table info
    $pokeTable = Engine_Api::_()->getDbtable('pokeusers', 'poke');
    $pokeTableName = $pokeTable->info('name');	
    
    //Selecting the limit how many items admin want to show in widgets.
    $limit = $this->_getParam('itemCountPerPage', 4);

		$this->view->user_photo = $this->_getParam('user_photo', 1);
    
    //Here we selecting how many rows of pokeback=0 in the table.
    $selects = $tables->select()
			->setIntegrityCheck( false )	
			->from($userTableName, array('*'))
			->join($pokeTableName, "`{$pokeTableName}`.resourceid=`{$userTableName}`.`user_id`")
      ->where("$pokeTableName.`userid` = $userid")
      ->where("$pokeTableName.`isdeleted` = 1")
			->order("$pokeTableName.created DESC")
			->limit($limit); 

		//Here we faching the results of the rows.
	  $rowinfo = $tables->fetchAll($selects); 
	  
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