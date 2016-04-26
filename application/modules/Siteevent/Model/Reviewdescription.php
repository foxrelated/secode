<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Reviewdescription.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_ReviewDescription extends Core_Model_Item_Abstract {

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {
        //GET CONTENT ID
        $content_id = Engine_Api::_()->siteevent()->existWidget('siteevent_reviews', 0);




        $params = array_merge(array(
            'route' => "siteevent_entry_view",
            'reset' => true,
            'event_id' => $this->event_id,
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