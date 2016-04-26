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

class Sitegroup_Widget_AlphabeticsearchSitegroupController extends Seaocore_Content_Widget_Abstract {

   public function indexAction() {

    $groupResults = Engine_Api::_()->getDbTable('groups', 'sitegroup')->checkGroup();
    if(empty($groupResults)) {
       return $this->setNoRender();
     }
   }
}
?>