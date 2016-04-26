<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Review.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_Review extends Core_Model_Item_Abstract {

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
		//GET CONTENT ID
    $content_id = Engine_Api::_()->list()->existWidget('list_reviews', 0);

    $params = array_merge(array(
      'route' => 'list_entry_view',
      'reset' => true,
      'user_id' => $this->owner_id,
      'listing_id' => $this->listing_id,
      'slug' => $this->getSlug(),
			'tab' => $content_id,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

}