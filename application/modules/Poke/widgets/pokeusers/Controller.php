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

class Poke_Widget_PokeusersController extends Seaocore_Content_Widget_Abstract
{

  public function indexAction()
  {
  	//Getting the logged in user information.
  	$viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

  	$table = Engine_Api::_()->getDbtable('pokeusers', 'poke');
		$select = $table->select()
							->where("userid =?", $viewer_id)
							->where("isdeleted =?", 1)
              ->order("created DESC");
    
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    // Set item count per page and current page number
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->isajax = $isajax = $this->_getParam('isajax' , 0);
    $this->view->form = new Poke_Form_Sitemobile_Search();
	}

}

?>