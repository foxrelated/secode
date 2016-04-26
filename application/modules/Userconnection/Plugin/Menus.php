<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Userconnection_Plugin_Menus
{
	//user_home
  public function onMenuInitialize_UserconnectionHomeConnection($row)
  {
		$viewer = Engine_Api::_()->user()->getViewer();
		if( $viewer->getIdentity() )
		{
			return array(
				'label' => $row->label,
				'icon' => $row->params['icon'],
				'route' => 'connection'
			);
		}
		return false;
  }
}