<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GeoSitetagCheckin.php 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_View_Helper_GetSitetagCheckin extends Zend_View_Helper_Abstract {

  /**
   * Assembles action string
   * 
   * @return string
   */
  public function getSitetagCheckin($action, $content, $onlyLocation = null) {
    $includeLocationStr = "";
    $params = (array) $action->params;
    $includeSym = ' &#151; ';
    if (is_array($params) && isset($params['checkin'])) {
      if ((empty($action->body) && $action->type == 'sitetagcheckin_checkin') || strstr($content, '&#151;') !== FALSE) {
        $includeSym = "";
      }

      $checkin = $params['checkin'];
      $addPrifix = $this->view->translate((!empty($checkin['prefixadd'])) ? $checkin['prefixadd'] : 'at');
      if ($onlyLocation == 1) {
        $addPrifix = '';
        $includeSym = "";
      }
      $checkinTypeArray = array('Page', 'Business', 'Event', 'Group', 'Classified', 'Blog', 'Video', 'Forum', 'Music', 'Listing', 'Recipe', 'Poll', 'Store');
      if (isset($checkin['type']) && $checkin['type'] == 'place') {
        $location = isset($checkin['vicinity']) ? ((isset($checkin['name']) && $checkin['name'] != $checkin['vicinity']) ? ( $checkin['name'] . ", " . $checkin['vicinity']) : $checkin['vicinity']) : $checkin['label'];
        $is_mobile = Engine_Api::_()->seaocore()->isMobile();
        $smMobile = Engine_Api::_()->seaocore()->isSiteMobileModeEnabled();
        if ($smMobile) {
            $includeLocationStr .= '<span class="seaocore_txt_light">' . $includeSym . $addPrifix . "</span> " . $this->view->htmlLink('https://maps.google.com/?q=' . urlencode($location), $location, array('target' => '_blank'));
        } else {
          if (!$is_mobile) {
            $includeLocationStr .= '<span class="seaocore_txt_light">' . $includeSym . $addPrifix . "</span> " . $this->view->htmlLink($this->view->url(array('guid' => $action->getGuid(), 'format' => 'smoothbox'), 'sitetagcheckin_viewmap', true), $location, array('class' => 'smoothbox'));
          } else {
            $includeLocationStr .= '<span class="seaocore_txt_light">' . $includeSym . $addPrifix . "</span> " . $this->view->htmlLink($this->view->url(array('guid' => $action->getGuid()), 'sitetagcheckin_viewmap', true), $location, array());
          }
        }
      } elseif (isset($checkin['type']) && $checkin['type'] == 'just_use') {
        $includeLocationStr .= '<span class="seaocore_txt_light">' . $includeSym . $addPrifix . "</span> " . "<span class='feed_item_bodytext'>" . $checkin['label'] . "</span>";
      } else if (isset($checkin['type']) && in_array($checkin['type'], $checkinTypeArray)) {
        $item = Engine_Api::_()->getItemByGuid($checkin['resource_guid']);
        if ($item) {
          $includeLocationStr .= '<span class="seaocore_txt_light">' . $includeSym . $addPrifix . "</span> " . $this->view->htmlLink($item->getHref(), $item->getTitle(), array('title' => $item->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel' => $item->getType() . ' ' . $item->getIdentity()));
        }
      }
    }

    if ($onlyLocation == 1) {
      return array($content, $includeLocationStr);
    } else if ($includeLocationStr) {
      $includeLocationStr .= ".";
    }

    if (empty($includeSym) && !empty($includeLocationStr))
      $content = substr_replace($content, ' ', -1);

    return $content . $includeLocationStr;
  }

}