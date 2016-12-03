<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */
class Goal_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    
    // Form
    $this->view->form = $formFilter = new Goal_Form_Filter_Browse();
    $defaultValues = $formFilter->getValues();

    if( !$viewer || !$viewer->getIdentity() ) {
      $formFilter->removeElement('view');
    }

    // Populate options
    $categories = Engine_Api::_()->getDbtable('categories', 'goal')->getCategoriesAssoc();
    $formFilter->category_id->addMultiOptions($categories);

    // Populate form data
    if( $formFilter->isValid($p) ) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    // Prepare data
    $this->view->formValues = $values = $formFilter->getValues();

    if( $viewer->getIdentity() && @$values['view'] == 1 ) {
      $values['users'] = array();
      foreach( $viewer->membership()->getMembersInfo(true) as $memberinfo ) {
        $values['users'][] = $memberinfo->user_id;
      }
    }

    $values['search'] = 1;

    // check to see if request is for specific user's listings
    $user_id = $this->_getParam('user');
    if( $user_id ) {
      $values['user_id'] = $user_id;
    }
  }
}
