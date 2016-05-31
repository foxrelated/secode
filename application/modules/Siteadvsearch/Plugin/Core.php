<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function onSitereviewListingtypeUpdateAfter($event) {

    $listingtype = $event->getPayload();
    $contentTable = Engine_Api::_()->getItemTable('siteadvsearch_content');

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $listingtype_id = $db->select()
            ->from('engine4_siteadvsearch_contents', 'listingtype_id')
            ->where('listingtype_id = ?', $listingtype->listingtype_id)
            ->limit(1)
            ->query()
            ->fetchColumn();

    if (empty($listingtype_id)) {
      $content = $contentTable->createRow();
      $content->module_name = 'sitereview';
      $content->resource_type = 'sitereview_listingtype_' . $listingtype->listingtype_id;
      $content->resource_title = $listingtype->title_plural;
      $content->listingtype_id = $listingtype->listingtype_id;
      $content->content_tab = 0;
      $content->default = 1;
      $content->order = 999;
      $content->save();
    }
  }

}