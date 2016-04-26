<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Widget_ArtistBrowseSearchController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $requestParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();

    $searchOptionsType = $this->_getParam('searchOptionsType', array('searchBox', 'view'));
    if (empty($searchOptionsType))
      return $this->setNoRender();

    $this->view->form = $formFilter = new Sesmusic_Form_SearchArtist();

    if ($formFilter->isValid($requestParams))
      $values = $formFilter->getValues();
    else
      $values = array();
    $this->view->formValues = array_filter($values);
  }

}