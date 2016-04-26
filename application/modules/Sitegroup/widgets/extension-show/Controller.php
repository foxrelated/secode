<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Socialengineaddon
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php (var) 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitegroup_Widget_ExtensionShowController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

   $enableSubModules = array();
   $rss = Zend_Feed::import('http://www.socialengineaddons.com/groupextensions/feed');

    foreach( $rss as $item )
    {
      if($item->ptype() == 'contactgroupowners') {
        $enableSubModules[] = 'sitegroupadmincontact';
      } 
      elseif($item->ptype() == 'sitegroupshorturl') {
        $enableSubModules[] = 'sitegroupurl';
      }
      else {
				$enableSubModules[] = $item->ptype();
      }
    }
    $enableAllModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    $enableModules = array_intersect($enableSubModules, $enableAllModules);
    $this->view->channel = $enableModules;
  }

}
?>