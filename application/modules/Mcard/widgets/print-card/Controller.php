<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.tpl 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
class Mcard_Widget_PrintCardController extends Seaocore_Content_Widget_Abstract {
/*Method used to show the Membership card in the widget
 */
  public function indexAction() 
  {
		$viewer = Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();
		$print_msg = '';
		$this->view->subject_id = $subject_id = Engine_Api::_()->core()->getSubject()->getIdentity();
		$member_visibility = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.visibility');
		$member_print = Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.print');
		$sub_level_id = Engine_Api::_()->getItem('user', $subject_id)->level_id;
		$sub_mp_id = Engine_Api::_()->mcard()->getProfileTypeId($subject_id);

		// Get subject and check auth
		$subject = Engine_Api::_()->core()->getSubject('user');
		if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
		  return $this->setNoRender();
		}
		// GET LEVEL ID.
		if( empty($user_id)  ) {
			// If 'Everyone can view' the 'loggden user' & 'loggdout user' both can view card.
			if ( !empty($member_visibility) ) {
				return $this->setNoRender();
			}else if( empty($member_visibility) && empty($member_print) ) {
				$print_msg = $this->view->translate('Print Membership Card');
			}
		} else {
			$level_id = Engine_Api::_()->getItem('user', $subject_id)->level_id;
			$mp_id = Engine_Api::_()->mcard()->getProfileTypeId($user_id);
			if ( empty($sub_mp_id) )
			{
				return $this->setNoRender();
			}
			// Check card status if set dont show for this level then return from the tab.
			$crad_status = Engine_Api::_()->getItemTable('mcard_info')->getVal($level_id, $mp_id);
			$show_card = $crad_status['card_status'];
			$sub_crad_status = Engine_Api::_()->getItemTable('mcard_info')->getVal($sub_level_id, $sub_mp_id);
			$sub_show_card = $sub_crad_status['card_status'];
			if ( empty($sub_show_card) || empty($show_card) ) {
				return $this->setNoRender();
			}
			
			$print_msg = '';
			//$email_msg = '';
			if ($user_id == 0) 
			{
				return $this->setNoRender();
			}
			// Condition for "Display Card".
			// Only owners can view membership cards.
			if( $member_visibility == 2 )
			{
				if( $subject_id != $user_id )
				{
					return $this->setNoRender();
				}
			}
			// Condition for "Print Mcard".
			// Nobody, disable printing of membership cards.
			if ( $member_print == 3 )
			{
				$print_msg = '';
			}
			// Only owners can print membership cards.
			else if ( $member_print == 2 )
			{
				if( $subject_id == $user_id )
				{
					$print_msg = $this->view->translate('Print Membership Card');
				}
			}
			// Only members can print membership cards.
			else if ( $member_print == 1 )
			{
				$print_msg = $this->view->translate('Print Membership Card');
			}
			// Everyone can print membership cards.
			else if ( $member_print == 0 )
			{
				$print_msg = $this->view->translate('Print Membership Card');
			}

			$rtoView =  Engine_Api::_()->getApi('settings', 'core')->getSetting('core.zendmc');
			if( empty($rtoView) ) 
			{
				return $this->setNoRender();
			}
		}
		// Show card.
		$data = Engine_Api::_()->mcard()->showCard($sub_level_id, $subject_id, $sub_mp_id);
		$this->view->userCard = str_replace('"', "'", $data);
		$this->view->print_message = $print_msg;
		$this->view->comunity_name = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
  }
}
