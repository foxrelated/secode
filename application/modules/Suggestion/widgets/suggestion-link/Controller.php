<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php (var) 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Widget_SuggestionLinkController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Logout user will not show this widgets.
    // Widgets will not work for the default modules.
    $subject = Engine_Api::_()->core()->getSubject();
    $getModName = $subject->getModuleName();
    $getModName = strtolower($getModName);
    $getModObj = Engine_Api::_()->getDbtable('modinfos', 'suggestion')->getMod($getModName);
    $getModObj = !empty($getModObj)? $getModObj[0]: null;

    if( empty($getModObj) || !empty($getModObj['default']) || empty($getModObj['enabled']) || empty($getModObj['link']) ) {
      return $this->setNoRender();
    }

    $this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    $this->view->modName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();


    $this->view->subject_id = $subject->getIdentity();

  }

}
?>