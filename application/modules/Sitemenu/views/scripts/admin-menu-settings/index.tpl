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
<script type="text/javascript">
        function fetchSettings(menu_name)
        {
            window.location.href= en4.core.baseUrl+'admin/sitemenu/menu-settings/index/name/' + menu_name;
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
<div class="settings">
  <?php if ($this->form): ?>
    <?php echo $this->form->render($this) ?>
  <?php elseif ($this->status): ?>
    <div><?php echo "Your changes have been saved." ?></div>

    <script type="text/javascript">
        setTimeout(function() {
            parent.window.location.replace( '<?php echo $this->url(array('action' => 'index', 'name' => $this->selectedMenu->name)) ?>' )
        }, 500);
    </script>

  <?php endif; ?>
</div>