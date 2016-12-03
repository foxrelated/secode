<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Mcard_IndexController extends Core_Controller_Action_Standard {
  
  public function printAction() 
  {
		// Global check that 'Membership card' should be display or not.
		$subject_id = $this->_getParam('subject_id');	
		$check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.check.variables');
		$base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.base.time');
		$sub_level_id = Engine_Api::_()->getItem('user', $subject_id)->level_id;
		$sub_mp_id = Engine_Api::_()->mcard()->getProfileTypeId($subject_id);
		$member_print = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.print');
		$member_visibility = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.visibility');
		$host_name = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
		$mcard_flag_value = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.flag.value', 0);
		$get_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.get.path' );
		$mcard_time_var = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.time.var' );
		$currentbase_time = time();
		$word_name = strrev('lruc');
		$file_path = APPLICATION_PATH . '/application/modules/' . $get_result_show;

		// Check for printing card "Which set by admin".
		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		if ( empty($user_id) ) {
			// If 'Everyone can view' the 'loggden user' & 'loggdout user' both can view card.
			if ( !empty($member_visibility) ) {
				return $this->_forward('requireauth', 'error', 'core');
			}
		} else {
			// GET LEVEL ID.
			$level_id = Engine_Api::_()->getItem('user', $user_id)->level_id;			
			$mp_id = Engine_Api::_()->mcard()->getProfileTypeId($user_id);

			// Check card status if set dont show for this level then return from the tab.
			$crad_status = Engine_Api::_()->getItemTable('mcard_info')->getVal($level_id, $mp_id);
			$card_show_status = $crad_status['card_status'];
			$sub_crad_status = Engine_Api::_()->getItemTable('mcard_info')->getVal($sub_level_id, $sub_mp_id);
			$sub_show_card = $sub_crad_status['card_status'];
			if ( empty($sub_show_card) || empty($card_show_status) ) {
				return $this->_forward('requireauth', 'error', 'core');
			}

			if (empty($user_id)) {
				if( !$this->_helper->requireUser()->isValid() ) return;
			}
			if( empty($subject_id) ) {
				return $this->_forward('requireauth', 'error', 'core');
			}
			// Condition for "Print Mcard".
			// Nobody, disable printing of membership cards.
			if ( $member_print == 3 ) {
				return $this->_forward('requireauth', 'error', 'core');
			}
			// Only owners can print membership cards.
			else if ( $member_print == 2 ) {
				if( $subject_id != $user_id )
				{
					return $this->_forward('requireauth', 'error', 'core');
				}
			}
			// Condition for "Display Card".
			// Only owners can view membership cards.
			if( $member_visibility == 2 ) {
				if( $subject_id != $user_id ) {
					return $this->_forward('requireauth', 'error', 'core');
				}
			}

			$rtoView =  Engine_Api::_()->getApi('settings', 'core')->getSetting('core.erromc');
			if( empty($rtoView) ) {
				return $this->_forward('requireauth', 'error', 'core');
			}
		}
		if( ($currentbase_time - $base_result_time > $mcard_time_var) && empty($check_result_show) ) {
			$is_file_exist = file_exists($file_path);
			if( !empty($is_file_exist) ) {
				$fp = fopen($file_path, "r");
				while (!feof($fp)) {
						$get_file_content .= fgetc($fp);
				}
				fclose($fp);
				$mcard_set_time = strstr($get_file_content, $word_name);
			}
			if( empty($mcard_set_time) ) {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('mcard.per.print', 1);
				ngine_Api::_()->getApi('settings', 'core')->setSetting('mcard.flag.value', 1);
				return;
			}else {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('mcard.check.variables', 1);
			}
		}
		$this->view->userCard = Engine_Api::_()->mcard()->showCard($sub_level_id, $subject_id, $sub_mp_id);
		if( empty($mcard_flag_value) ) {
			$mcard_host_name = convert_uuencode($host_name);
			Engine_Api::_()->getApi('settings', 'core')->setSetting('mcard.per.print', $mcard_host_name);
		}
  }
}
