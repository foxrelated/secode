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
class Siteadvsearch_Widget_AlbumBrowseSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $searchForm = $this->view->searchForm = new Album_Form_Search();
    $request = Zend_Controller_Front::getInstance()->getRequest();

    $searchForm
      ->setMethod('get')
      ->populate($request->getParams())
      ; 
    
    $siteadvsearch_browse_search = Zend_Registry::isRegistered('siteadvsearch_browse_search') ? Zend_Registry::get('siteadvsearch_browse_search') : null;
    if (empty($siteadvsearch_browse_search))
      return $this->setNoRender();
    
  }
}