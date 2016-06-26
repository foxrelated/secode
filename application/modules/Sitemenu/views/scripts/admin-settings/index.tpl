<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<script>
    window.addEvent('domready', function() {
        hideCacheLifetime();
});

function hideCacheLifetime(){
    if($('sitemenu_cache_lifetime-wrapper')){
        if($('sitemenu_cache_enable-1') && $('sitemenu_cache_enable-1').checked){
            $('sitemenu_cache_lifetime-wrapper').style.display = 'block';
        }else{
            $('sitemenu_cache_lifetime-wrapper').style.display = 'none';
        }
    }
}
</script>

<h2>
    <?php echo 'Advanced Menus Plugin - Interactive and Attractive Navigation' ?>
</h2>
<div class='tabs'>
    <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
</div>
<div class='seaocore_settings_form'>
    <div class='settings'>
      <?php echo $this->form->render($this); ?>
    </div>
  </div>