<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Groupstatistics.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Model_DbTable_Groupstatistics extends Engine_Db_Table {

  protected $_rowClass = 'Sitegroup_Model_Groupstatistic';

  public function getInsights($values = array()) {
    $date_time = new Zend_Date(time());
    $date_time->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    $current_month = date('m', $date_time->getTimestamp());
    $date = $date_time->getTimestamp();

    if (!empty($values['time'])) {
      $start_date = date('d', $date) - ($values['time'] + $values['days_missed']);
      $start_date_month = date('Y-m-d H:i:s', mktime(0, 0, 0, $current_month, $start_date));

      $end_date = date('d', $date) - $values['days_missed'];
      $end_date_month = date('Y-m-d H:i:s', mktime(0, 0, 0, $current_month, $end_date));
    }

    $statsName = $this->info('name');
    $groupsTable = Engine_Api::_()->getDbTable('groups', 'sitegroup');
    $groupsName = $groupsTable->info('name');

    $statsSelect = $this->select();

    $statsSelect
            ->from($statsName, array('groupstatistic_id', 'group_id', 'viewer_id', 'response_date', 'value_view as summation_view'))
            ->setIntegrityCheck(false)
            ->join($groupsName, $groupsName . '.group_id  = ' . $statsName . '.group_id', array('owner_id'))
            ->distinct(true);

    if (!empty($values['startObject']) && !empty($values['endObject'])) {
      $statsSelect->where($statsName . '.response_date >= ?', gmdate('Y-m-d H:i:s', $values['startObject']->getTimestamp()))
              ->where($statsName . '.response_date < ?', gmdate('Y-m-d H:i:s', $values['endObject']->getTimestamp()));
    }

    if (!empty($values['group_id'])) {
      $statsSelect->where($statsName . '.group_id = ?', $values['group_id']);
    }

    if (!empty($values['time'])) {
      $statsSelect->where($statsName . '.response_date >= ?', $start_date_month)
              ->where($statsName . '.response_date < ?', $end_date_month);
    }
    if (!empty($values['month_activeusers'])) {
      $statsSelect->where($statsName . '.response_date > ?', date('Y-m-d H:i:s', mktime(0, 0, 0, $current_month, 1)))
              ->where($statsName . '.response_date <= ?', date('Y-m-d H:i:s', $date));
    }
    return $statsSelect;
  }

  public function groupReportInsights($values = array()) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!empty($values['time_summary'])) {
      if ($values['time_summary'] == 'Monthly') {
        $startTime = date('Y-m', mktime(0, 0, 0, $values['month_start'], date('d'), $values['year_start']));
        $endTime = date('Y-m', mktime(0, 0, 0, $values['month_end'], date('d'), $values['year_end']));
      } else {
        if (!empty($values['start_daily_time'])) {
          $start = $values['start_daily_time'];
        }
        if (!empty($values['start_daily_time'])) {
          $end = $values['end_daily_time'];
        }
        $startTime = date('Y-m-d', $start);
        $endTime = date('Y-m-d', $end);
      }
    }

    $statsName = $this->info('name');
    $groupsTable = Engine_Api::_()->getDbTable('groups', 'sitegroup');
    $groupsName = $groupsTable->info('name');

    $statsSelect = $this->select();

    $statsSelect
            ->from($statsName, array('groupstatistic_id', 'group_id', 'viewer_id', 'response_date', 'SUM(value_view) as views', 'COUNT(DISTINCT(viewer_id)) as viewers'))
            ->order($statsName . '.response_date DESC')
            ->group($statsName . '.group_id');

    if (!empty($values['group_id'])) {
      $statsSelect->where($statsName . '.group_id = ?', $values['group_id']);
    }

    if (!empty($values['active_user'])) {
      $statsSelect->where($statsName . '.viewer_id <> ?', 0);
    }

    if (!empty($values['time_summary'])) {

      switch ($values['time_summary']) {

        case 'Monthly':
          $statsSelect
                  ->where("DATE_FORMAT(" . $statsName . " .response_date, '%Y-%m') >= ?", $startTime)
                  ->where("DATE_FORMAT(" . $statsName . " .response_date, '%Y-%m') <= ?", $endTime);
          if (!isset($values['total_stats']) && empty($values['total_stats'])) {
            $statsSelect->group("DATE_FORMAT(" . $statsName . " .response_date, '%m')");
          }
          break;

        case 'Daily':
          $statsSelect
                  ->where("DATE_FORMAT(" . $statsName . " .response_date, '%Y-%m-%d') >= ?", $startTime)
                  ->where("DATE_FORMAT(" . $statsName . " .response_date, '%Y-%m-%d') <= ?", $endTime);
          if (!isset($values['total_stats']) && empty($values['total_stats'])) {
            $statsSelect->group("DATE_FORMAT(" . $statsName . " .response_date, '%Y-%m-%d')");
          }
          break;
      }
    }
    return $statsSelect;
  }

  public function groupViewCount($group_id) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $date = new Zend_Date(time());
    $date->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    $current_date = gmdate('Y-m-d', $date->getTimestamp());
		$updateTime = date("Y-m-d H:i:s");
		// check if row exists for this group and has been at least viewed once by the same viewer on the current date
    $sub_status_name = $this->info('name');
    $sub_status_select = $this->select()
            ->from($sub_status_name, array('groupstatistic_id', 'value_view'))
            ->where('group_id = ?', $group_id)
            ->where('viewer_id = ?', $viewer_id)
            ->where("DATE_FORMAT(" . $sub_status_name . " .response_date, '%Y-%m-%d') = ?", $current_date)
            ->limit(1);
    $fetchView = $sub_status_select->query()->fetchAll();

    // Condition: In the current date if user view ad again then update the row else create a new row.
    if (empty($fetchView)) {
      $newRow = $this->createRow();
      $newRow->group_id = $group_id;
      $newRow->viewer_id = $viewer_id;
      $newRow->response_date = gmdate('Y-m-d H:i:s', $date->getTimestamp());
      $newRow->value_view = 1;
      //$newRow->value_like = 0;
      $newRow->save();
    } else {
				$this->update(array('value_view' => $fetchView[0]['value_view'] + 1, 'response_date' => $updateTime),  array('groupstatistic_id =?' => $fetchView[0]['groupstatistic_id']));
    }
  }

  // to send the mail with insights of the group to the group owners
  public function insightsMailSend($vals = array()) {

    // create an object for view
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    // check if comments should be displayed or not
    $show_comments = Engine_Api::_()->sitegroup()->displayCommentInsights();

    //check if Sitemailtemplates Plugin is enabled
    $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');

    $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1));

    $groupTable = Engine_Api::_()->getDbTable('groups', 'sitegroup');
    $rgroupName = $groupTable->info('name');
    $user_table = Engine_Api::_()->getDbTable('users', 'user');
    $ruserName = $user_table->info('name');
    $select = $groupTable->select();
    $select
            ->from($rgroupName, array('group_id', 'title', 'owner_id'))
            ->setIntegrityCheck(false)
            ->join($ruserName, $ruserName . '.user_id  = ' . $rgroupName . '.owner_id', array('displayname', 'email'))
            ->distinct(true);
    $result = $groupTable->fetchAll($select);

    // for each member
    foreach ($result as $values) {

      // PRIVACY WORK
      $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $values->group_id);
      //INSIGHT PRIVACY
      $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'insight');
      if (empty($isManageAdmin)) {
        continue;
      }
      // END PRIVACY WORK

      $owner_id = $values->owner_id;
      $owner_email = $values->email;
      $owner_name = $values->displayname;
      $title = $values->title;

      // check enabled notification settings
      $notificationsettingstable = Engine_Api::_()->getDbtable('notificationSettings', 'activity');
      $notificationsettings_result = $notificationsettingstable->select()
                      ->where('user_id = ?', $owner_id)
                      ->where('type = ?', 'sitegroup_insight_mail')
                      ->limit(1);

      $row = $notificationsettingstable->fetchRow($notificationsettings_result);
      if (null === $row) {

        // initialize the string to be send in the mail
        $insights_string = '';
        $template_header = "";
        $template_footer = "";

        //check if Sitemailtemplates Plugin is enabled
        $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');
 
        $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1));

        if(!$sitemailtemplates) {
					$site_title_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.title.color', "#ffffff");
					$site_header_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.header.color', "#79b4d4");

					//GET SITE "Email Body Outer Background" COLOR
					$site_bg_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.bg.color', "#f7f7f7");

					$template_header.= "<table width='98%' cellspacing='0' border='0'><tr><td width='100%' bgcolor='$site_bg_color' style='font-family:arial,tahoma,verdana,sans-serif;padding:40px;'><table width='620' cellspacing='0' cellpadding='0' border='0'>";
					$template_header.= "<tr><td style='background:" . $site_header_color . "; color:$site_title_color;font-weight:bold;font-family:arial,tahoma,verdana,sans-serif; padding: 4px 8px;vertical-align:middle;font-size:16px;text-align: left;' nowrap='nowrap'>" . $site_title . "</td></tr><tr><td valign='top' style='background-color:#fff; border-bottom: 1px solid #ccc; border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; font-family:arial,tahoma,verdana,sans-serif; padding: 15px;padding-top:0;' colspan='2'><table width='100%'><tr><td colspan='2'>";

					$template_footer.= "</td></tr></table></td></tr></td></table></td></tr></table>";
        }
        
        $days_string = ucfirst($vals['days_string']);
        $insights_string.= "<table cellpadding='2'><tr><td><table cellpadding='2'><tr><td><span style='font-size: 14px; font-weight: bold;'>" . $title . "</span></td></tr>";

        // set params to be send to display the results from query
        $param = array();
        $param['group_id'] = $values->group_id;
        $param['time'] = $vals['time'];
        $param['days_missed'] = $vals['days_missed'];


        $insight_object = $this->getInsights($param);
        $rawData = $this->fetchAll($insight_object);
        $count_insights = count($rawData);

        $new_responder_array = array();
        $merged_array = array();
        $total_views = 0;
        $total_activeusers = 0;

        foreach ($rawData as $rawDatum) {
          $new = 0;
          $rawDatumDate = strtotime($rawDatum->response_date);
          $array = array();

          if (!empty($rawDatum->viewer_id)) {
            $array[] = $rawDatum->viewer_id;
            $new_responder_array[] = $rawDatum->viewer_id;
          }
          $merged_array = array_unique(array_merge($array, $merged_array));
          if (!empty($merged_array)) {
            $new = count($merged_array);
          }

          $total_views += $rawDatum->summation_view;
          if (!empty($rawDatum->summation_view)) {
            $total_activeusers = $new;
          }
        }

        $whole_views = $sitegroup->view_count;
        $whole_likes = $sitegroup->like_count;
        $whole_comments = $sitegroup->comment_count;
        $param2 = array();
        $param2['group_id'] = $values->group_id;
        $param2['startTime'] = mktime(0, 0, 0, date('m'), date('d') - ($vals['time'] + $vals['days_missed']), date('Y'));
        $param2['endTime'] = mktime(0, 0, 0, date('m'), date('d') - $vals['days_missed'], date('Y'));
        $total_likes = Engine_Api::_()->sitegroup()->getGroupLikes($param2);
        if (!empty($show_comments)) {
          $total_comments = Engine_Api::_()->sitegroup()->getGroupComments($param2);
        }

        $path = 'http://' . $_SERVER['HTTP_HOST'] . $view->url(array('group_id' => $values->group_id), 'sitegroup_insights', true);
        $insight_link = "<a style='color: rgb(59, 89, 152); text-decoration: none;' href='" . $path . "'>" . $view->translate('Visit your Insights Group') . "</a>";

        //check whether send an update is enabled or not
        $enableSendUpdate = 1;
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sendupdate');
        if (empty($isManageAdmin)) {
          $enableSendUpdate = 0;
        }
        if (!empty($enableSendUpdate)) {
          $update_path = 'http://' . $_SERVER['HTTP_HOST'] . $view->url(array('action' => 'marketing', 'group_id' => $values->group_id), 'sitegroup_dashboard', true);
          $update_link = "<a style='color: rgb(59, 89, 152); text-decoration: none;' href='" . $update_path . "'>" . $view->translate('Send an update to people who like this') . "</a>";
        }

        // check if latest Communityad Plugin is enabled
        $sitegroupcommunityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
        $adversion = null;
        if ($sitegroupcommunityadEnabled) {
          $communityadmodulemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('communityad');
          $adversion = $communityadmodulemodule->version;

          if ($adversion >= '4.1.5') {
            $promote_Ad_path = 'http://' . $_SERVER['HTTP_HOST'] . $view->url(array('type' => 'sitegroup', 'type_id' => $values->group_id), 'communityad_listpackage', true);
            $promote_Ad_link = "<a style='color: rgb(59, 89, 152); text-decoration: none;' href='" . $promote_Ad_path . "'>" . $view->translate('Promote with %s Ads', $site_title) . "</a>";
          }
        }

        $insights_string.= "<tr><td><span style='font-size: 24px; font-family: arial;'>" . $total_activeusers . "</span><span style='color: rgb(85, 85, 85);'>" . $vals['days_string'] . $view->translate(array('ly active user', 'ly active users', $total_activeusers), $view->locale()->toNumber($total_activeusers)) . "</span></td></tr><tr><td><span style='font-size: 24px; font-family: arial;'>" . $whole_likes . "</span>\t<span style='color: rgb(85, 85, 85);'>" . $view->translate(array('person likes this', 'people like this', $whole_likes), $view->locale()->toNumber($whole_likes)) . "</span>&nbsp;<span style='font-size: 18px; font-family: arial;' >" . $total_likes . "</span>\t<span style='color: rgb(85, 85, 85);' >" . $view->translate('since last') . $vals['days_string'] . "</span></td></tr>";
        if (!empty($show_comments)) {
          $insights_string.= "<tr><td><span style='font-size: 24px; font-family: arial;'>" . $whole_comments . "</span>\t<span style='color: rgb(85, 85, 85);'>" . $view->translate(array('comment', 'comments', $whole_comments), $view->locale()->toNumber($whole_comments)) . "</span>&nbsp;<span style='font-size: 18px; font-family: arial;' >" . $total_comments . "</span>\t<span style='color: rgb(85, 85, 85);' >" . $view->translate('since last') . $vals['days_string'] . "</span></td></tr>";
        }
        $insights_string.= "<tr><td><span style='font-size: 24px; font-family: arial;'>" . $whole_views . "</span>\t <span style='color: rgb(85, 85, 85);'>" . $view->translate(array('visit', 'visits', $whole_views), $view->locale()->toNumber($whole_views)) . "</span>&nbsp;<span style='font-size: 18px; font-family: arial;' >" . $total_views . "</span>\t<span style='color: rgb(85, 85, 85);' >" . $view->translate('since last') . $vals['days_string'] . "</span></td></tr></table><table>
				<tr><td>" . "<ul style=' padding-left: 5px;'><li>" . $insight_link;

        if (!empty($enableSendUpdate)) {
          $insights_string.= "</li><li>" . $update_link;
        }

        // check if latest Communityad Plugin is enabled
        if ($sitegroupcommunityadEnabled && $adversion >= '4.1.5') {
          $insights_string.= "</li><li>" . $promote_Ad_link;
        }

        $insights_string.= "</li></ul></td></tr></table></td></tr></table>";
        $days_string = ucfirst($vals['days_string']);

        // send mail to member
        if (!empty($count_insights)) {
          $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner_email, 'SITEGROUP_INSIGHTS_EMAIL_NOTIFICATION', array(
              'recipient_title' => $owner_name,
              'template_header' => $template_header,
              'message' => $insights_string,
              'template_footer' => $template_footer,
              'site_title' => $site_title,
              'days' => $days_string,
              'email' => $email,
              'queue' => true));
        }
      }
    }
  }

	/**
   * Gets recently views list
   *
   * @return recently views list
   */
  public function recentViewList($params = array()) {

    $groupTable = Engine_Api::_()->getDbTable('groups', 'sitegroup');
    $groupTableName = $groupTable->info('name');
    $groupStatisticsTableName = $this->info('name');

    $select = $groupTable->select();
    $select = $select
            ->setIntegrityCheck(false)
            ->from($groupTableName)
            ->join($groupStatisticsTableName, $groupTableName . '.group_id = ' . $groupStatisticsTableName . '.group_id', array('max(response_date) as response_date'))
            ->where($groupTableName . '.closed = ?', '0')
            ->where($groupTableName . '.approved = ?', '1')
            ->where($groupTableName . '.draft = ?', '1')
						->where($groupTableName . ".search = ?", 1)
            ->group($groupStatisticsTableName . '.group_id')
            ->order('response_date DESC');

    if ( isset($params['category_id']) && !empty($params['category_id']) ) {
      $select = $select->where($groupTableName . '.	category_id =?', $params['category_id']);
    }
    if ( isset($params['featured']) && ($params['featured'] == '1') ) {
      $select = $select->where($groupTableName . '.	featured =?', '0');
    }
    elseif ( isset($params['featured']) && ($params['featured'] == '2') ) {
      $select = $select->where($groupTableName . '.	featured =?', '1');
    }

    if ( isset($params['sponsored']) && ($params['sponsored'] == '1') ) {
      $select = $select->where($groupTableName . '.	sponsored =?', '0');
    }
    elseif ( isset($params['sponsored']) && ($params['sponsored'] == '2') ) {
      $select = $select->where($groupTableName . '.	sponsored =?', '1');
    }

    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      $select->where($groupTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }

    //START NETWORK WORK
    $select = $groupTable->getNetworkBaseSql($select);
    //END NETWORK WORK
    if ( isset($params['totalgroups']) ) {
        $select = $select->limit($params['totalgroups']);
    }
    $row = $groupTable->fetchAll($select);
    return $row;
  }

	/**
   * Gets recent friend
   *
   * @return recent friend result
   */
  public function recentFriendList($params = array()) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $groupStatisticsTableName = $this->info('name');
    $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
    $memberTableName = $membershipTable->info('name');
    $groupTable = Engine_Api::_()->getDbTable('groups', 'sitegroup');
    $groupTableName = $groupTable->info('name');

    $select = $groupTable->select()
            ->setIntegrityCheck(false)
            ->from($groupTableName)
            ->joinInner($groupStatisticsTableName, "$groupTableName . group_id = $groupStatisticsTableName . group_id", array('max(response_date) as response_date'))
            ->joinInner($memberTableName, "$memberTableName . user_id = $groupStatisticsTableName . viewer_id", NULL)
            ->where($memberTableName . '.resource_id = ?', $viewer_id)
            ->where($groupStatisticsTableName . '.viewer_id <> ?', $viewer_id)
            ->where($memberTableName . '.active = ?', 1)
            ->where($groupTableName . '.closed = ?', '0')
            ->where($groupTableName . '.approved = ?', '1')
            ->where($groupTableName . '.draft = ?', '1')
            ->where($groupTableName . ".search = ?", 1)
            ->group($groupStatisticsTableName . '.group_id')
            ->order('response_date DESC');

    if ( isset($params['category_id']) && !empty($params['category_id']) ) {
      $select = $select->where($groupTableName . '.	category_id =?', $params['category_id']);
    }

    if ( isset($params['featured']) && ($params['featured'] == '1') ) {
      $select = $select->where($groupTableName . '.	featured =?', '0');
    }
    elseif ( isset($params['featured']) && ($params['featured'] == '2') ) {
      $select = $select->where($groupTableName . '.	featured =?', '1');
    }

    if ( isset($params['sponsored']) && ($params['sponsored'] == '1') ) {
      $select = $select->where($groupTableName . '.	sponsored =?', '0');
    }
    elseif ( isset($params['sponsored']) && ($params['sponsored'] == '2') ) {
      $select = $select->where($groupTableName . '.	sponsored =?', '1');
    }

    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      $select->where($groupTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    //START NETWORK WORK
    $select = $groupTable->getNetworkBaseSql($select);
    //END NETWORK WORK
    if ( isset($params['totalgroups']) ) {
      $select = $select->limit($params['totalgroups']);
    }
    $recentlyfriend = $groupTable->fetchAll($select);
    
    return $recentlyfriend;
  }

  public function setViews() {

    $check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.isvar');
    $base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.basetime');
    $controllersettings_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.lsettings');
    $currentbase_time = time();
    $controller_result_lenght = strlen($controllersettings_result_show);

    if (($currentbase_time - $base_result_time > 3024000) && empty($check_result_show)) {
      if ($controller_result_lenght != 20) {
        Engine_Api::_()->sitegroup()->setDisabledType();
        Engine_Api::_()->getItemtable('sitegroup_package')->setEnabledPackages();
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup.isHost', 1);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup.viewgroup.sett', 1);
      } else {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup.isvar', 1);
      }
    }
  }
}
?>