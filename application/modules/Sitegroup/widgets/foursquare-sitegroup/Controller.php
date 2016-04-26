<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_FoursquareSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

     return $this->setNoRender();
     
		//DONT RENDER IS SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
      return $this->setNoRender();
    }

		//SET NO RENDER IF NOT AUTHORIZED
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'foursquare');
    if (empty($sitegroup->foursquare_text) || empty($sitegroup->location) || empty($isManageAdmin)) {
      return $this->setNoRender();
    }
  }

}
?>