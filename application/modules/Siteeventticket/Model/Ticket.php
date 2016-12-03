<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Ticket.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_Ticket extends Core_Model_Item_Abstract {

    public function getHref($params = array()) {
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $this->event_id);
        $tabId = Engine_Api::_()->siteevent()->getTabId('siteeventticket.tickets-buy');
        $slug = $siteevent->getSlug();

        $params = array_merge(array(
            'route' => 'siteevent_entry_view',
            'reset' => true,
            'event_id' => $this->event_id,
            'slug' => $slug
                ), $params);
        
        if($tabId) {
            $params = array_merge(array('tab' => $tabId), $params);            
        }
        
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);

        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

}
