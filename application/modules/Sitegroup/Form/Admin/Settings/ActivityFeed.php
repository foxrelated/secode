<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ActivityFeed.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Admin_Settings_ActivityFeed extends Engine_Form {

  public function init() {
    $this
            ->setAttribs(array(
                'id' => 'activity_feed_form',
            ))
            ->setTitle('Activity Feed Settings')
            ->setDescription('Below are the options to customize the Activity Feeds related to Groups. Once you configure all the settings below, click on "Save Changes" to save them.');
    $subCoreGroupApi = Engine_Api::_()->getApi('subCore', 'sitegroup');
    $socialengineaddonFeedIsAdded = $subCoreGroupApi->isCoreActivtyFeedWidget('user_index_home', 'activity.feed');
    $advancedactivityHomeFeedsIsAdded = $subCoreGroupApi->isCoreActivtyFeedWidget('user_index_home', 'advancedactivity.home-feeds');
    $translate = Zend_Registry::get('Zend_Translate');
    $description = "Select the type of activity feeds that should be published for groups. By default, the photo and name of Group Admin is shown in activity feeds of groups. Using this setting, you can instead choose to show the Group's Photo and Title.";
    if ($socialengineaddonFeedIsAdded || $advancedactivityHomeFeedsIsAdded) {
      $description .= "If you choose the 2nd option to show the Group's Photo and Title, then users will also receive on their home page the updates from Groups that they have Liked.";
    }
    $options1 = 'Group\'s Photo and Title';
    if ($socialengineaddonFeedIsAdded) {
      $options1.=" (Choosing this will send to users on their homegroup, the updates from Groups that they Like. If you have the \"Advanced Activity Feeds / Wall Plugin\" (http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin) installed on your site, then Group Photo and Title will also come for comments made by Group Admins on activity feeds of their Group and comments on the content of their Group (photo, video, document, etc.))";
    } elseif ($advancedactivityHomeFeedsIsAdded) {
      $options1.=" (Choosing this will send to users on their homegroup, the updates from Groups that they Like. Group Photo and Title will also come for comments made by Group Admins on activity feeds of their Group and comments on the content of their Group (photo, video, document, etc.))";
    } else {
      $options1.=" (If you have the \"Advanced Activity Feeds / Wall Plugin\" (http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin) installed on your site, then Group Photo and Title will also come for comments made by Group Admins on activity feeds of their Group and comments on the content of their Group (photo, video, document, etc.))";
    }
    $this->getView()->escape($options1);
    $this->addElement('Radio', 'sitegroup_feed_type', array(
        'label' => 'Groups Activity Feed Type',
        'description' => $description,
        'multiOptions' => array(
            '0' => 'Group Admin\'s Photo and Name',
            '1' => $options1,
        ),
        'onclick' => 'showEditingOptions("sitegroupfeed_likegroup_dummy-wrapper",this.value)',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.feed.type', 0),
    ));

    if (!$socialengineaddonFeedIsAdded && !$advancedactivityHomeFeedsIsAdded) {
      $this->addElement('Dummy', 'sitegroupfeed_likegroup_dummy', array(
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => 'admin-settings/activity-feed/_feedLikegroup.tpl',
                      'class' => 'form element'
              )))
      ));
    }

		$this->addElement('Radio', 'sitegroup_feed_onlyliked', array(
			'label' => 'Show Groups / Communities Activity Feeds On Member Home Group',
			'description' => 'Select the type of Activity Feeds corresponding to Groups / Communities which you want to display to users on Member Home Group.',
			'multiOptions' => array(
					'0' => 'Show Feeds of all Groups / Communities (This will be dependent on the Content Privacy of user.)',
					'1' => 'Show Feeds only of Liked Groups / Communities',
			),
			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.feed.onlyliked', 1),
		));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>