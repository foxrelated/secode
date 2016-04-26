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
class Sitetagcheckin_Widget_BylocationGroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    // Make form
    $this->view->form = $form = new Sitetagcheckin_Form_GroupLocationsearch();

 		if (isset($_POST['is_ajax']) && !empty($_POST['is_ajax'])) {
			$this->view->is_ajax = $_POST['is_ajax'];
		}
		
	  if(empty($_POST['sitepage_location'])) {
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
			if (isset($values['sitepage_street']) && !empty($values['sitepage_street'])) {
				$values['sitepage_location'] = $values['sitepage_street'] . ',';
				unset($values['sitepage_street']);
			}
			
			if (isset($values['sitepage_city']) && !empty($values['sitepage_city'])) {
				$values['sitepage_location'].= $values['sitepage_city'] . ',';
				unset($values['sitepage_city']);
			}	
			
			if (isset($values['sitepage_state']) && !empty($values['sitepage_state'])) {
				$values['sitepage_location'].= $values['sitepage_state'] . ',';
				unset($values['sitepage_state']);
			} 
			
			if (isset($values['sitepage_country']) && !empty($values['sitepage_country'])) {
				$values['sitepage_location'].= $values['sitepage_country'];
				unset($values['sitepage_country']);
			}
		}
    
    $result = Engine_Api::_()->sitetagcheckin()->getGroupsSelect($values);
   	$this->view->paginator = $paginator = Zend_Paginator::factory($result);
   	$this->view->totalresults = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage(15);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);

    $this->view->mobile = Engine_Api::_()->seaocore()->isMobile();
  }
}