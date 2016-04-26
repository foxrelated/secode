<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
		parent::init();

		$this
			->setTitle('Member Level Settings')
			->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

		if ( !$this->isPublic() ) {

      $this->addElement('Radio', 'gpcreate', array(
        'label' => 'Allow Creation of Polls in Groups?',
        'description' => 'Do you want to let members of this level to create polls in group ?',
        'multiOptions' => array(
          2 => 'Yes, allow members to create polls in all groups, including private ones.',
          1 => 'Yes, allow members to create polls in groups.',
          0 => 'No, do not allow members to create polls in groups.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if ( !$this->isModerator() ) {
        unset($this->gpcreate->options[2]);
      }

			$this->addElement('MultiCheckbox', 'auth_gpcreate', array(
				'label' => 'Poll Creation Options',
				'description' => 'Your members can choose from any of the options checked below when they decide who can create the polls in their group. If you do not check any options, everyone will be allowed to create.',
				'multiOptions' => array(
					'registered' => 'Registered Members',
					'member' => 'All Group Members',
					'officer' => 'Officers and Owner Only',
				)
			));

			$this->addElement('MultiCheckbox', 'gp_auth_vote', array(
				'label' => 'Poll Voting Options',
				'description' => 'Your members can choose from any of the options checked below when they decide who can vote on the polls in their group. If you do not check any options, everyone will be allowed to vote.',
				'multiOptions' => array(
					'1' => 'Registered Members',
					'2' => 'All Group Members',
					'3' => 'Officers and Owner Only',
				)
			));
		}
  }
}
?>