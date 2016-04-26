<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2010-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_Installer extends Engine_Package_Installer_Module
{
  function onInstall()
  {
		$db = $this->getDb();

		//CHECK SOCIALENGINEADDONS PLUGIN IS INSTALL OR NOT.
    $pluginName = 'Groups - Polls Extension Plugin';

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'seaocore');
    $check_socialengineaddons = $select->query()->fetchAll();

    $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
    $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
    if ( strstr($url_string, "manage/install") ) {
      $calling_from = 'install';
    }
    else if ( strstr($url_string, "manage/query") ) {
      $calling_from = 'queary';
    }
    $explode_base_url = explode("/", $baseUrl);
    foreach ( $explode_base_url as $url_key ) {
      if ( $url_key != 'install' ) {
        $core_final_url .= $url_key . '/';
      }
    }

    if( empty($check_socialengineaddons) ) {
      // Page plugin is not install at your site.
			return $this->_error('<div class="global_form"><div><div>The SocialEngineAddOns Core Plugin is not installed on your site. Please download the latest version of this FREE plugin from your Client Area on <a href="http://www.socialengineaddons.com" target="_blank">SocialEngineAddOns</a> and install on your site before installing this plugin.</div></div></div>');
    } else if( !empty($check_socialengineaddons) && empty($check_socialengineaddons[0]['enabled']) ) {
      // Plugin not Enable at your site
      return $this->_error("<span style='color:red'>Note: You have installed the SocialEngineAddOns Core Plugin but not enabled it on your site yet. Please enabled it first before installing the
      $pluginName .</span><br/> <a href='" . 'http://' . $core_final_url . "install/manage/'>Click here</a> to enabled the SocialEngineAddOns Core Plugin.");

    } else if( $check_socialengineaddons[0]['version'] < '4.2.0p1' ) {
      // Please activate page plugin
      return $this->_error('<div class="global_form"><div><div> You do not have the latest version of the SocialEngineAddOns Core Plugin. Please download the latest version of this FREE plugin from your Client Area on <a href="http://www.socialengineaddons.com" target="_blank">SocialEngineAddOns</a> and upgrade this on your site.</div></div></div>');
    }

		//CHECK THAT ADVANCED-GROUP PLUGIN IS INSTALLED OR NOT
		$select = new Zend_Db_Select($db);
   	$select
      ->from('engine4_core_modules')
      ->where('name = ?', 'advgroup')
			->where('enabled = ?', 1);
    $check_advgroup = $select->query()->fetchObject();

		//CHECK THAT GROUP PLUGIN IS INSTALLED OR NOT
		$select = new Zend_Db_Select($db);
   	$select
      ->from('engine4_core_modules')
      ->where('name = ?', 'group')
			->where('enabled = ?', 1);
    $check_group = $select->query()->fetchObject();
		if(!empty($check_group) || !empty($check_advgroup)) {
		
			//INSERT COMMENT AUTHORIZATION VALUES IN 'engine4_authorization_levels' TABLE
			$select = new Zend_Db_Select($db);
			$select->from('engine4_authorization_levels', array('level_id'))->where('level_id != ?', 0);
			$total_levels = $select->query()->fetchAll();

			$level_ids = array();
			foreach($total_levels as $key => $id) {
				$level_id = $id['level_id'];

				$select_comment = new Zend_Db_Select($db);
				$select_comment
					->from('engine4_authorization_permissions')
					->where('level_id = ?', $level_id)
					->where('type = ?', 'grouppoll_poll')
					->where('name = ?', 'comment')
					->limit(1);
				$comment_level = $select_comment->query()->fetchObject();

				if(empty($comment_level)) {
					$db->insert('engine4_authorization_permissions', array(
						'level_id' => $level_id,
						'type'    => 'grouppoll_poll',
						'name'    => 'comment',
						'value' => 1,
						'params'   => NULL,
					));
				} 
			}

			parent::onInstall();
		}
		else {
			$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
			return $this->_error("<span style='color:red'>Note: You do not have installed the Group Plugin on your site. Please install first Group Plugin on your site before installing this Group Poll Plugin.</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.");
		}
  }

	function onDisable()
  {
    $db = $this->getDb();

		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_activity_actiontypes')
			->where('type = ?', 'grouppoll_new')
			->where('module = ?', 'group')
			->limit(1);
		$info = $select->query()->fetch();
		if(!empty($info)) {
			$db->delete('engine4_activity_actiontypes', array(
				'type = ?' => 'grouppoll_new',
				'module = ?' => 'group'
			));
		}

		parent::onDisable();
  }

	function onEnable()
  {
		$db = $this->getDb();

		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_activity_actiontypes')
			->where('type = ?', 'grouppoll_new')
			->where('module = ?', 'group')
			->limit(1);
		$info = $select->query()->fetch();
		if(empty($info)) {
			$db->insert('engine4_activity_actiontypes', array(
				'type'    => 'grouppoll_new',
				'module'    => 'group',
				'body' => '{item:$subject} created a new poll:',
				'enabled'   => 1,
				'displayable'   => 2,
				'attachable'   => 2,
				'commentable'   => 1,
				'shareable'   => 1,
				'is_generated'   => 1,
			));
		}
		parent::onEnable();
	}
}
?>