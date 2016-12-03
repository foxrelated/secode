<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Widget_SponsoredSlideshowSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $params = array();
    $params['totalstores'] = $this->_getParam('itemCount', 10);
    $params['category_id'] = $this->_getParam('category_id', 0);  
    
    //GET STORE DATAS
    $this->view->show_slideshow_object = $sitestore = Engine_Api::_()->sitestore()->getLising('Sponosred Slideshow', $params);
    $this->view->sitestore_sponsored = $sitestore_sponsored =1;

    $this->view->num_of_slideshow = count($sitestore);
    if ( !(count($sitestore) > 0) || empty($sitestore_sponsored) ) {
      return $this->setNoRender();
    }
  }

}

?>