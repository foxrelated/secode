<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SitetagcheckinActivity.php 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_View_Helper_Sitetagcheckinactivity extends Zend_View_Helper_Abstract {

  public function sitetagcheckinactivity(Activity_Model_Action $action = null, array $data = array()) {

    $viewer = Engine_Api::_()->user()->getViewer();
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $activity_moderate = "";
    if ($viewer->getIdentity()) {
      $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
    }

    $form = new Sitetagcheckin_Form_Comment();
    $data = array_merge($data, array(
        'actions' => array($action),
        'commentForm' => $form,
        'user_limit' => $coreSettings->getSetting('activity_userlength'),
        'allow_delete' => $coreSettings->getSetting('activity_userdelete'),
        'activity_moderate' => $activity_moderate,
            ));

    return $this->view->partial(
                    '_sitetagcheckinactivityText.tpl', 'sitetagcheckin', $data
    );
  }

}