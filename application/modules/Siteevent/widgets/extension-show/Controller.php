<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Siteevent_Widget_ExtensionShowController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

   $enableSubModules = array();
   $rss = Zend_Feed::import('http://www.socialengineaddons.com/eventextensions/feed');

    foreach( $rss as $item )
    {
      if($item->ptype() == 'contacteventowners') {
        $enableSubModules[] = 'siteeventadmincontact';
      } 
      elseif($item->ptype() == 'siteeventshorturl') {
        $enableSubModules[] = 'siteeventurl';
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