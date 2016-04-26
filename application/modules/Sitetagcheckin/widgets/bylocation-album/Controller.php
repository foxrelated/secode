<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:20:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Widget_BylocationAlbumController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    // Make form
    $this->view->form = $form = new Sitetagcheckin_Form_AlbumLocationsearch();

		if(isset($_POST['is_ajax']) && !empty($_POST)) {
			$this->view->is_ajax = $_POST['is_ajax'];
		}
		
	  if(empty($_POST['album_location'])) {
			$this->view->locationVariable = '1';
		}

    if (isset($_POST['is_ajax']) && empty($_POST['is_ajax'])) {
			$p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
			$form->isValid($p);
			$values = $form->getValues();
			$this->view->is_ajax = $this->_getParam( 'is_ajax', 0 );
    } else {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
			$values = $_POST;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    if (!empty($values['view_view']) && $values['view_view'] == 1) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }
      $values['users'] = $ids;
    }

    unset($values['or']);
    $this->view->assign($values); 

    $this->view->current_page = $page = $this->_getParam( 'page' , 1 ) ;
    $this->view->current_totalpages = $page * 15 ;
    
    //check for miles or street.
		if (isset($values['locationmiles']) && !empty($values['locationmiles'])) {
			if (isset($values['album_street']) && !empty($values['album_street'])) {
				$values['album_location'] = $values['album_street'] . ',';
				unset($values['album_street']);
			}
			
			if (isset($values['album_city']) && !empty($values['album_city'])) {
				$values['album_location'].= $values['album_city'] . ',';
				unset($values['album_city']);
			}	
			
			if (isset($values['album_state']) && !empty($values['album_state'])) {
				$values['album_location'].= $values['album_state'] . ',';
				unset($values['album_state']);
			} 
			
			if (isset($values['album_country']) && !empty($values['album_country'])) {
				$values['album_location'].= $values['album_country'];
				unset($values['album_country']);
			}
		}
		
    $result = Engine_Api::_()->sitetagcheckin()->getAlbumsSelect($values);
   	$this->view->paginator = $paginator = Zend_Paginator::factory($result);
   	$this->view->totalresults = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage(15);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);

    $this->view->mobile = Engine_Api::_()->seaocore()->isMobile();
  }
}