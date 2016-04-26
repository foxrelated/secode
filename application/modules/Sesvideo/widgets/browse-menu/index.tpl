<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
?>
<div class="headline">
  <h2>
    <?php if(!empty($this->params['title'])): ?>
      <?php echo $this->translate($this->params['title']); ?>
    <?php else: ?>
      <?php echo $this->translate('Videos'); ?>
    <?php endif; ?>
  </h2>
  <?php $countMenu = 0; ?>
  <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <ul class="navigation">
	  <?php foreach( $this->navigation as $navigationMenu ): ?>
	    <?php if( $countMenu < $this->max ): ?>
	      <li <?php if ($navigationMenu->active): ?><?php echo "class='active'";?><?php endif; ?>>
	      <?php if ($navigationMenu->action): ?>
                <a class= "<?php echo $navigationMenu->class ?>" href='<?php echo empty($navigationMenu->uri) ? $this->url(array('action' => $navigationMenu->action), $navigationMenu->route, true) : $navigationMenu->uri ?>'><?php echo $this->translate($navigationMenu->label); ?></a>
              <?php else : ?>
                <a class= "<?php echo $navigationMenu->class ?>" href='<?php echo empty($navigationMenu->uri) ? $this->url(array(), $navigationMenu->route, true) : $navigationMenu->uri ?>'><?php echo $this->translate($navigationMenu->label); ?></a>
              <?php endif; ?>
	      </li>
	    <?php else:?>
	      <?php break;?>
	    <?php endif;?>
	    <?php $countMenu++;?>
	  <?php endforeach; ?>
	<?php if (count($this->navigation) > $this->max):?>
	  <?php $countMenu = 0; ?>
	    <li class="sesbasic_browse_nav_tab_closed sesbasic_browse_nav_pulldown" onclick="moreTabSwitch($(this));">
	      <a href="javascript:void(0);"><?php echo $this->translate('More +') ?><span></span></a>
	      <div class="tab_pulldown_contents_wrapper sesbasic_bxs">
          <div class="tab_pulldown_contents">
            <ul>
              <?php foreach( $this->navigation as  $navigationMenu ): ?>
                <?php if ($countMenu >= $this->max): ?>
                <?php $urlNavigation = empty($navigationMenu->uri) ? $this->url(array('action' => $navigationMenu->action), $navigationMenu->route, true) : $navigationMenu->uri ?>
           <?php $http_https = isset($_SERVER['HTTPS']) ? 'https://' : 'http://'; ?>
 <li <?php if ($navigationMenu->active): ?><?php echo "class='active'";?><?php endif; ?> <?php if ($urlNavigation == "$http_https$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"): ?><?php echo "class='active'";?><?php endif; ?>  >
            <?php if ($navigationMenu->action): ?>
              <a class= "<?php echo $navigationMenu->class ?>" href='<?php echo $urlNavigation ?>'><?php echo $this->translate($navigationMenu->label); ?></a>
            <?php else : ?>
              <a class= "<?php echo $navigationMenu->class ?>" href='<?php echo empty($navigationMenu->uri) ? $this->url(array(), $navigationMenu->route, true) : $navigationMenu->uri ?>'><?php echo $this->translate($navigationMenu->label); ?></a>
            <?php endif; ?>
            </li>
                <?php endif;?>
                <?php $countMenu++;?>
              <?php endforeach; ?>
            </ul>
          </div>
	      </div>
	    </li>
	<?php endif;?>
      </ul>
    </div>
  <?php endif; ?>
</div>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    var moreTabSwitch = window.moreTabSwitch = function(el) {
      el.toggleClass('sesbasic_browse_nav_tab_open');
      el.toggleClass('sesbasic_browse_nav_tab_closed');
    }
  });
</script>