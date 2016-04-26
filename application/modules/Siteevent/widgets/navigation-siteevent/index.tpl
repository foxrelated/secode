<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

if ($this->checkSiteModeSM()) {
    include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/navigation_viewsSM.tpl';
} else {
    include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/navigation_views.tpl';
}?>
<script type="text/javascript">
        $$('.core_main_siteevent').getParent().addClass('active');
    </script>