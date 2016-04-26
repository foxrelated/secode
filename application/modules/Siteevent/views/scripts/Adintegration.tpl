<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Adintegration.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$session = new Zend_Session_Namespace();
if (!empty($session->show_hide_ads)) {
    if ($session->event_communityad_integration == 1)
        $communityad_integration = $event_communityad_integration = $session->event_communityad_integration;
    else
        $communityad_integration = $event_communityad_integration = 0;
}
else {
    $communityad_integration = $event_communityad_integration = 1;
}
?>