<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Widget.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_Form_Admin_Widget extends Engine_Form
{
  public function init()
  {
		$this
		->setTitle('Widget Settings')
		->setDescription('Configure the settings for the various widgets available with this plugin.');

		$this->addElement('Text', 'grouppoll_comment_widgets', array(
			'label' => 'Group Profile Most Commented Polls',
      'maxlength' => '3',
			'description' => 'How many polls should be shown in the Group Profile Most Commented Polls (value cannot be empty or zero) ?',
			'required' => true,
			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.comment.widgets', 3),
		));

		$this->addElement('Text', 'grouppoll_view_widgets', array(
			'label' => 'Group Profile Most Viewed Polls',
      'maxlength' => '3',
			'description' => 'How many polls should be shown in the Group Profile Most Viewed Polls (value cannot be empty or zero) ?',
			'required' => true,
			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.view.widgets', 3),
		));
		
		$this->addElement('Text', 'grouppoll_recent_widgets', array(
			'label' => 'Group Profile Most Recent Polls',
      'maxlength' => '3',
			'description' => 'How many polls should be shown in the Group Profile Most Recent Polls (value cannot be empty or zero) ?',
			'required' => true,
			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.recent.widgets', 3),
		));

    $this->addElement('Text', 'grouppoll_vote_widgets', array(
			'label' => 'Group Profile Most Voted Polls',
      'maxlength' => '3',
			'description' => 'How many polls should be shown in the Group Profile Most Voted Polls (value cannot be empty or zero) ?',
			'required' => true,
			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.vote.widgets', 3),
		));

    $this->addElement('Text', 'grouppoll_like_widgets', array(
			'label' => 'Group Profile Most Liked Polls',
      'maxlength' => '3',
			'description' => 'How many polls should be shown in the Group Profile Most Liked Polls (value cannot be empty or zero) ?',
			'required' => true,
			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.like.widgets', 3),
		));

		$this->addElement('Button', 'submit', array(
			'label' => 'Save Changes',
			'type' => 'submit',
			'ignore' => true
		));
  }
}
?>