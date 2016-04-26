<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Seo
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>

<div style="padding: 10px; width: 540px;">
  <?php if ($this->installed): ?>
  
  
    <ul class="form-notices"><li><?php echo $this->translate('SEO Page layout hook is installed and working properly.')?></li></ul>
  
  <?php else: ?>
  
    <ul class="form-errors">
      <li><?php echo $this->translate('Could NOT automatically install SEO Page layout hook.') ?></li>
    </ul>
  
  <?php endif; ?>
  
  <h3><?php echo $this->translate('Guide to manually install SEO Page layout hook')?></h3>
  <ol id="seo_hook_guide">
    <li><?php echo $this->translate('Open and edit the layout script file:')?>
    <br /><code>application/modules/Core/layouts/scripts/default.tpl</code>
  <?php //echo $this->layout_script?></li>
    <li><?php echo $this->translate('Around line 58 (depending on your SE version), locate the following code:')?>
    <br/><code>&lt;?php echo $this->headTitle()->toString()."\n" ?&gt;</code></li>
    <li><?php echo $this->translate('Right above that line, paste in the following code:')?>
    <br/><code>&lt;?php echo $this->hooks("onRenderLayoutDefaultSeo", $this) ?&gt;</code></li>
    <li><?php echo $this->translate('The end result should look something like this:')?>
    <br/><code>&lt;?php echo $this->hooks("onRenderLayoutDefaultSeo", $this) ?&gt;
  <br/>  &lt;?php echo $this->headTitle()->toString()."\n" ?&gt;
    </code></li>
    </li>
  </ol>

    <p>
      <a href='javascript:void(0);' onclick='javascript:parent.window.location.reload( false );'>
        <?php echo $this->translate("close") ?>
      </a>
    </p>

</div>