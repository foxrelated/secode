<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Widget_MusicBrowseSearchController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();

    // Get browse params
    $this->view->form = $formFilter = new Siteadvsearch_Form_Musicsearch();
    if ($formFilter->isValid($p)) {
      $values = $formFilter->getValues();
    } else {
      $values = array();
    }
    $this->view->formValues = array_filter($values);
    
    $siteadvsearch_browse_search = Zend_Registry::isRegistered('siteadvsearch_browse_search') ? Zend_Registry::get('siteadvsearch_browse_search') : null;
    if (empty($siteadvsearch_browse_search))
      return $this->setNoRender();
    
    // Show
    $viewer = Engine_Api::_()->user()->getViewer();
    if (@$values['show'] == 2 && $viewer->getIdentity()) {
      // Get an array of friend ids
      $values['users'] = $viewer->membership()->getMembershipsOfIds();
    }
    unset($values['show']);
  }

}