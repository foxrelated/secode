<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Heevent_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    
    $filter = !empty($p['filter']) ? $p['filter'] : 'future';
    if( $filter != 'past' && $filter != 'future' ) $filter = 'future';
    $this->view->filter = $filter;

    // Create form
    if( false !== stripos($_SERVER['REQUEST_URI'], 'events/manage') ) {
      $this->view->form = $formFilter = new Event_Form_Filter_Manage();
      $defaultValues = $formFilter->getValues();
        if(Engine_Api::_()->hasModuleBootstrap('pageevent') && $settings->getSetting('page.browse.pageevent', 0)){
          $formFilter->getElement('view')->setMultiOptions(array(
                      '2' => 'All My Events',
                      '3' => 'Only Events I Lead',
                    ));
        }
      } else {
        $this->view->form = $formFilter = new Event_Form_Filter_Browse();
        $defaultValues = $formFilter->getValues();

        if( !$viewer || !$viewer->getIdentity() ) {
          $formFilter->removeElement('view');
        } else if(Engine_Api::_()->hasModuleBootstrap('pageevent') && $settings->getSetting('page.browse.pageevent', 0)){
          $formFilter->getElement('view')->setMultiOptions(array(
                      '0' => 'Everyone\'s Events',
                      '1' => 'My Friend\'s Events',
                      '2' => 'All My Events',
                      '3' => 'Only Events I Lead',
                    ));
          $formFilter->removeElement('category_id');
        } else {
          // Populate options
          foreach( Engine_Api::_()->getDbtable('categories', 'event')->select()->order('title ASC')->query()->fetchAll() as $row ) {
            $formFilter->category_id->addMultiOption($row['category_id'], $row['title']);
          }
          if (count($formFilter->category_id->getMultiOptions()) <= 1) {
            $formFilter->removeElement('category_id');
          }
        }
    }
    $formFilter->setAttrib('onsubmit', 'return false');
    if($search_text = $formFilter->getElement('search_text'))
      $search_text->setName('search');
    if($search_text = $formFilter->getElement('text'))
      $search_text->setName('search');


    // Populate form data
    if( $formFilter->isValid($p) ) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    // Prepare data
    $this->view->formValues = $values = $formFilter->getValues();

    if( $formFilter instanceof Event_Form_Filter_Browse ) {
      if( $viewer->getIdentity() && @$values['view'] == 1 ) {
        $values['users'] = array();
        foreach( $viewer->membership()->getMembersInfo(true) as $memberinfo ) {
          $values['users'][] = $memberinfo->user_id;
        }
      }

      $values['search'] = 1;

      if( $filter == "past" ) {
        $values['past'] = 1;
      } else {
        $values['future'] = 1;
      }

       // check to see if request is for specific user's listings
      if( ($user_id = $this->_getParam('user')) ) {
        $values['user_id'] = $user_id;
      }
    }
  }
}
