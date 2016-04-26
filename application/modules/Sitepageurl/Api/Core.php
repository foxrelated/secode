<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageurl
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageurl_Api_Core extends Core_Api_Abstract {

  /**
   * Get Sitepage banned url
   * @param array $params : contain desirable Sitepagebanned info
   * @return  object of Sitepageurl
   */
  public function getBlockUrl($values = array()) {
 
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');
    $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
    $bannedPageurlsTableName = $bannedPageurlsTable->info('name');
    $select = $bannedPageurlsTable->select();
    $select = $select
              ->from($bannedPageurlsTableName)
              ->setIntegrityCheck(false)
              ->joinInner($pageTableName, "$pageTableName.page_url = $bannedPageurlsTableName.word",array('page_id','page_url','title'))
              ->order((!empty($values['order']) ? $values['order'] : 'bannedpageurl_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    return Zend_Paginator::factory($select);

  }

}
?>