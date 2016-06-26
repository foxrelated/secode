<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _otherMainMenuOptions.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<div class="fright" <?php if( $this->sitemenu_main_menu_height == 30 ) : ?> style="margin-top:5px;" <?php endif; ?></div>
  <?php if (!empty($this->show_cart)) : ?>
    <div id="main_menu_cart" class="fleft">
      <a href="<?php echo $this->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true) ?>" title="<?php echo $this->translate("Your Shopping Cart") ?>">
        <span class="navicon fleft" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemenu/externals/images/cart-icon-white.png);"></span>
         <?php if (!empty($this->itemCount)) : ?>
        <span id="main_menu_cart_item_count" class="seaocore_pulldown_count"><?php echo $this->itemCount ?></span>
        <?php else: ?>
        <span id="main_menu_cart_item_count"></span>
        <?php endif; ?>
      </a>
    </div>
  <?php endif; ?>
  <?php
  switch ($this->showOption) :
    case 2:
      echo $this->content()->renderWidget('sitemenu.language-sitemenu');
      break;
    case 3:
      ?>
      <?php echo $this->content()->renderWidget('sitemenu.searchbox-sitemenu', array('isMainMenu' => 1, 'advancedMenuProductSearch' => 1)) ?>
      <?php
      break;
    case 4:
      ?>
      <?php if (!empty($this->sitestoreproductEnable)) : ?>
        <?php echo $this->content()->renderWidget('sitemenu.searchbox-sitemenu', array('isMainMenu' => 1, 'advancedMenuProductSearch' => 2)) ?>
      <?php endif; ?>
      <?php
      break;
    case 5:
      ?>
      <?php echo $this->content()->renderWidget('sitemenu.searchbox-sitemenu', array('isMainMenu' => 1, 'advancedMenuProductSearch' => $this->showOption)) ?>
      <?php
      break;
  endswitch;
  ?>
</div>
