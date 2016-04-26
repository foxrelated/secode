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
class Sitetagcheckin_Widget_BylocationVideoController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    // Make form
    $this->view->form = $form = new Sitetagcheckin_Form_VideoLocationsearch();

		if (isset($_POST['is_ajax']) && !empty($_POST['is_ajax'])) {
			$this->view->is_ajax = $_POST['is_ajax'];
		}
		
	  if(empty($_POST['video_location'])) {
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
			if (isset($values['video_street']) && !empty($values['video_street'])) {
				$values['video_location'] = $values['video_street'] . ',';
				unset($values['video_street']);
			}
			
			if (isset($values['video_city']) && !empty($values['video_city'])) {
				$values['video_location'].= $values['video_city'] . ',';
				unset($values['video_city']);
			}	
			
			if (isset($values['video_state']) && !empty($values['video_state'])) {
				$values['video_location'].= $values['video_state'] . ',';
				unset($values['video_state']);
			} 
			
			if (isset($values['video_country']) && !empty($values['video_country'])) {
				$values['video_location'].= $values['video_country'];
				unset($values['video_country']);
			}
		}
		
    $result = Engine_Api::_()->sitetagcheckin()->getVideosSelect($values);
   	$this->view->paginator = $paginator = Zend_Paginator::factory($result);
   	$this->view->totalresults = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage(15);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);

    $this->view->mobile = Engine_Api::_()->seaocore()->isMobile();
  }
}