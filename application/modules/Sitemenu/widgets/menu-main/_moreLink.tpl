<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _moreLink.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<?php ?>
    <!--FOR MORE LINK OF MENUS-->
    <?php if (!empty($this->moreLink) && !empty($moreMenu)) : ?>
    <li class="level0parent standard_nav" id="more_link_li">
        <a class="level-top">
          <?php $tempShowOption = $this->sitemenu_more_link_icon; ?>
            <?php if (!empty($tempShowOption) && empty($this->isMobile)): ?>
            <span><i style="background-image:url('<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitemenu/externals/images/more.png'; ?>')"></i></span>
          <?php endif; ?>
          <span><?php echo $this->translate("More") ?></span>
        </a>
      <?php if(!empty($this->isMobile)): ?>
          <i onclick="void(0);"></i>
                  <?php endif; ?> 

        <!--DISPALYING ALL REMAINING MAIN MENUS AS SUB MENUS INSIDE THE MORE LINK-->
        <ul class="level<?php echo 1 ?> sitemenu_main_menu_more">
          <?php foreach ($moreMenu as $menuList) : ?>
            <li class="level<?php echo 1 . " " . (!empty($subMenus) ? 'parent' : '') ?> ">
              <a  <?php if(!empty ($menuList['menuUri'])): ?> href="<?php echo $menuList['menuUri'] ?>" <?php endif?> <?php if(!empty($menuList['target'])): ?> target="_blank" <?php endif;?> >
                
                <?php if ((!empty($menuList['tempShowInTab']) || $menuList['tempShowInTab'] == 2) && !empty($menuList['icon'])): ?>
                <span><i style="background-image:url(<?php echo $menuList['icon']; ?>)"></i></span>
              <?php endif; ?>
              <?php if (empty($menuList['tempShowInTab']) || $menuList['tempShowInTab'] == 2): ?>
                <span><?php echo $this->translate($menuList['label']) ?></span>
              <?php endif; ?> 
                
                <!--<span><?php // echo $this->translate($menuList['label']) ?></span>-->
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </li>
    <?php endif;