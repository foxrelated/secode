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

<div id="profile_options">
    <?php
    echo $this->navigation()
            ->menu()
            ->setContainer($this->gutterNavigation)
            ->setUlClass('navigation siteevents_gutter_options')
            ->render();
    ?>
</div>