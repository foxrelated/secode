<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: FollowEmailController.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_FollowEmailController extends Core_Controller_Action_Standard {

  public function indexAction() {
    //send video limit to one user
    $videoSendMax = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo.videoSendMac', 5);
    $chanelVideoTable = Engine_Api::_()->getDbtable('chanelvideos', 'sesvideo');
    $chanelVideoTableName = $chanelVideoTable->info('name');
    $chanelFollowTable = Engine_Api::_()->getDbtable('chanelfollows', 'sesvideo');
    $chanelFollowTableName = $chanelFollowTable->info('name');
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $userTable->info("name");
    $videoTable = Engine_Api::_()->getDbtable('videos', 'sesvideo');
    $videoTableName = $videoTable->info("name");
    $results = $chanelFollowTable->select()
            ->setIntegrityCheck(false)
            ->from($chanelFollowTableName, array('owner_id', 'chanelfollow_id'))
            ->joinLeft($chanelVideoTableName, "$chanelVideoTableName.chanel_id = $chanelFollowTableName.chanel_id", array('chanel_id', 'chanelvideos' => new Zend_Db_Expr('GROUP_CONCAT(video_id)')))
            ->joinLeft($userTableName, "$userTableName.user_id = $chanelFollowTableName.owner_id", array('email'))
            ->where($chanelVideoTableName . '.creation_date > ' . $chanelFollowTableName . '.modified_date')
            ->where($chanelVideoTableName . '.video_id != ?', '')
            ->where($userTableName . '.user_id != ?', '')
            ->where($chanelVideoTableName . '.owner_id != ' . $chanelFollowTableName . '.owner_id')
            ->group($chanelFollowTableName . '.owner_id')
            ->query()
            ->fetchAll();
    foreach ($results as $result) {
      $videoIds = $result['chanelvideos'];
      $owner_id = $result['owner_id'];
      //select video limit videoSendMax
      $videos = $videoTable->select()
              ->from($videoTableName)
              ->where('FIND_IN_SET(video_id,(?))', "$videoIds");
      $paginator = Zend_Paginator::factory($videos);
      $paginator->setItemCountPerPage($videoSendMax);
      $paginator->setCurrentPageNumber(1);
      $chanel = Engine_Api::_()->getItem('sesvideo_chanel', $result['chanel_id']);
      $body = '';
      foreach ($paginator as $video) {
        $body .= "<img src='" . $video->getPhotoUrl() . "'><br /><br />";
        $body .= "Title is : <a href='" . $video->getHref() . "'>" . $video->getTitle() . "</a><br /><br />";
        $body .= "Description : " . $video->description . "<br /><br />";
      }
      $body = "<a style=\"text-align:center\" href='" . $chanel->getHref() . "'>" . $chanel->getTitle() . "</a>";
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($result['email'], 'sesvideo_follow_email', array(
          'subject' => 'Follow Email for chanel ' . $chanel->getTitle(),
          'body' => $body,
      ));
      $dbObject = Engine_Db_Table::getDefaultAdapter();
      $dbObject->query('UPDATE engine4_video_chanelfollows SET modified_date = "' . date('Y-m-d h:i:s') . '" WHERE chanelfollow_id = ' . $result['chanelfollow_id']);
      die('sad');
    }
    die('NO record found.');
  }

}
