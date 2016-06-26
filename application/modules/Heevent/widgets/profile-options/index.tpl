<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>

<div id='profile_options' class="heevent-block heevent-widget">
  <div class="heevent-widget-inner">
  <?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
    echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->setPartial(array('_navIcons.tpl', 'core'))
      ->render()
  ?>
  </div>
</div>