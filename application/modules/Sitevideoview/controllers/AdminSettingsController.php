<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideoview
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2012-06-028 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideoview_AdminSettingsController extends Core_Controller_Action_Admin {

  public function indexAction() {
    $this->view->isModsSupport = Engine_Api::_()->sitevideoview()->isModulesSupport();
    $onactive_disabled = array('sitevideoview_lightbox_bgcolor', 'sitevideoview_lightbox_fontcolor', 'submit');
    $afteractive_disabled = array('environment_mode', 'submit_lsetting');
    include APPLICATION_PATH . '/application/modules/Sitevideoview/controllers/license/license1.php';
    $this->addLikeCloumnVideo();
  }

  public function faqAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitevideoview_admin_main', array(), 'sitevideoview_admin_main_faq');
  }

  public function readmeAction() {
    
  }

  private function addLikeCloumnVideo() {
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $videoTable = $db->query('SHOW TABLES LIKE \'engine4_video_videos\'')->fetch();
    if (!empty($videoTable)) {
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_video_videos LIKE \'like_count\'')->fetch();

      if (empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_video_videos` ADD `like_count` INT( 11 ) NOT NULL AFTER `comment_count`;");
        $db->query("UPDATE `engine4_video_videos`  SET  `engine4_video_videos`.`like_count`= (SELECT count(*) AS `likeCount`
FROM `engine4_core_likes` WHERE (`engine4_core_likes`.`resource_type` = 'video' and
`engine4_core_likes`.`resource_id` =`engine4_video_videos`.`video_id`) GROUP BY
`engine4_core_likes`.`resource_id` )
WHERE EXISTS (SELECT count(*) AS `likeCount` FROM `engine4_core_likes` WHERE
(`engine4_core_likes`.`resource_type` = 'video' and  `engine4_core_likes`.`resource_id`
=`engine4_video_videos`.`video_id`) GROUP BY `engine4_core_likes`.`resource_id` );");
      }
    }
  }

}