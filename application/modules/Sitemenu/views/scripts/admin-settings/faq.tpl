<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<h2>
    <?php echo 'Advanced Menus Plugin - Interactive and Attractive Navigation' ?>
</h2>
<div class='tabs'>
    <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
</div>

<?php include_once APPLICATION_PATH . '/application/modules/Sitemenu/views/scripts/admin-settings/faq_help.tpl'; ?>