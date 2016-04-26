<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Userconnection_Plugin_Core
{
	// Call when page render.
	public function onRenderLayoutDefault()
	{
		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$session = new Zend_Session_Namespace();
		$front = Zend_Controller_Front::getInstance();
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		// $curr_url = $front->getRequest()->getRequestUri();
		$curr_url = $view->seaddonsBaseUrl();

		// When on "Profile" page friend tab required.
		if(strpos($curr_url, 'profile/' . $user_id) !== FALSE && isset($session->friend_tab_set))
		{
			// Find out the friend tab id.
			$content_table = Engine_Api::_()->getItemTable('content');
			$content_name = $content_table->info('name');
			
			$content_select = $content_table->select()
				->from($content_name, array('content_id'))
				->where('name = ?', 'user.profile-friends')
				->where('type = ?', 'widget');
			$content_fetch = $content_select->query()->fetchAll();
			if(!empty($content_fetch))
			{
				$content_id = $content_fetch[0]['content_id'];
			}
			else {
				$content_id = 0;
			}
			
			// Open the friend tab. 
			$friend_tab = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
			$friend_tab_function = <<<EOF
			var content_id = "$content_id";
			this.onload = function() 
			{
				tabContainerSwitch($('main_tabs').getElement('.tab_' + content_id));
			}
EOF;
			$friend_tab->headScript()->appendScript($friend_tab_function);
			unset($session->friend_tab_set);
		}
	}
	
	// When user delete his profile then remove his value from "Userconnection" table.
  public function onUserDeleteBefore()
  {
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();    
    $table  = Engine_Api::_()->getItemTable('userconnection');
  	$select = $table->select()
      ->setIntegrityCheck(false)
      ->where('user_id = ?', $user_id);
    foreach( $table->fetchAll($select) as $userconnection ) {
    	$userconnection->delete();
    }
  }
}