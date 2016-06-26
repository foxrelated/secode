<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _multiColumn.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<?php if( !empty($mainMenuTabArray) ) : ?><ul class="level1">
    <?php
    $tempIndex = 0;
    $otherCountFlag = 1;
    $otherSubMenu = array();
    $sitemenu_mainmenu_listtabview = Zend_Registry::isRegistered('sitemenu_mainmenu_listtabview') ? Zend_Registry::get('sitemenu_mainmenu_listtabview') : null;
    foreach ($mainMenuTabArray as $subMenusList):
      $subMainMenuTabMenuObj = $subMenusList['info']['menuObj'];
      $subMenuObjParams = $subMainMenuTabMenuObj->params;
      $tempSubMenuId = $subMainMenuTabMenuObj->id;
      if (empty($sitemenu_mainmenu_listtabview) || ($subMainMenuTabMenuObj->custom && empty($subMenuObjParams['show_to_guest']) && !empty($this->isGuest))) {
        continue;
      }
      $tempShowInSubTab = !empty($subMenuObjParams['show_in_tab']) ? $subMenuObjParams['show_in_tab'] : 0;
      $subIcon = !empty($subMenuObjParams['icon']) ? $subMenuObjParams['icon'] : "";
      $subMenuTarget = !empty($subMenuObjParams['target']) ? $subMenuObjParams['target'] : "";
      $subMainMenuTabObj = $subMenusList['info']['zendObj'];
      unset($subMenusList['info']);
      
      // IF THE NUMBER OF MENUS EXCEEDS THE LIMIT OF 5 FURTHER SUB MENUS ARE NOT SHOWN.
      if ($otherCountFlag++ > 8):
        break;
      endif;

      $subSubMenus = COUNT($subMenusList);
      ?>
    <?php if( $tempIndex++ % 4 == 0 ) : ?>
      <li class="o_hidden">
        <ul class="multi_column_content">
    <?php endif; ?>
          <li class="level<?php echo 1 . "" . (!empty($subSubMenus) ? 'parent' : '') ?>">
            <?php $subMainMenuTabUrl = $subMainMenuTabObj->getHref(); ?>
            <a <?php if(!empty ($subMainMenuTabUrl)){ ?> href="<?php echo $subMainMenuTabObj->getHref() ?>" <?php } ?> <?php if(!empty($subMenuTarget)): ?> target="_blank" <?php endif;?> >
              <?php if ((!empty($tempShowInSubTab) || $tempShowInSubTab == 2) && !empty($subIcon)): ?>
                <span><i style="background-image:url(<?php echo $subIcon; ?>)"></i></span>
              <?php endif; ?>
              <?php if (empty($tempShowInSubTab) || $tempShowInSubTab == 2): ?>
                <span><?php echo $this->translate($subMainMenuTabObj->getLabel()) ?></span>
              <?php endif; ?>
            </a>
           
            <!--DISPLAY SUB-SUB-MENUS-->
            <?php if (!empty($subSubMenus)): ?>
              <ul class="level2" style="display: block">
                <?php
                foreach ($subMenusList as $subSubMenu):
                  $subSubMainMenuTabMenuObj = $subSubMenu['info']['menuObj'];
                  $subSubMenuObjParams = $subSubMainMenuTabMenuObj->params;
                  $tempSubSubId = $subSubMainMenuTabMenuObj->id;
                  if ($subSubMainMenuTabMenuObj->custom && empty($subSubMenuObjParams['show_to_guest']) && !empty($this->isGuest)) {
                    continue;
                  }
                  $tempShowInSubSubTab = !empty($subSubMenuObjParams['show_in_tab']) ? $subSubMenuObjParams['show_in_tab'] : 0;
                  $subSubIcon = !empty($subSubMenuObjParams['icon']) ? $subSubMenuObjParams['icon'] : "";
                  $subSubMenuTarget = !empty($subSubMenuObjParams['target']) ? $subSubMenuObjParams['target'] : "";
                  $subSubMainMenuTabObj = $subSubMenu['info']['zendObj'];
                  unset($subSubMenu['info']);
                  ?>
                  <li class="level2">
                     <?php $subSubMainMenuUrl = $subSubMainMenuTabObj->getHref(); ?>
                    <a <?php if(!empty ($subSubMainMenuUrl)){ ?> href="<?php echo $subSubMainMenuTabObj->getHref() ?>" <?php } ?> <?php if(!empty($subSubMenuTarget)): ?> target="_blank" <?php endif;?> >
                      <?php if ((!empty($tempShowInSubSubTab) || $tempShowInSubSubTab == 2) && !empty($subSubIcon) ): ?>
                        <span><i style="background-image:url(<?php echo $subSubIcon; ?>)"></i></span>
                      <?php endif; ?>
                      <?php if (empty($tempShowInSubSubTab) || $tempShowInSubSubTab == 2): ?>
                        <span><?php echo $this->translate($subSubMainMenuTabObj->getLabel()) ?></span>
                      <?php endif; ?>
                    </a>
                    
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </li>
    <?php if( $tempIndex % 4 == 0 ) : ?>
        </li>
      </ul>
    <?php endif; ?>
        <?php endforeach; ?>
    <?php if( $tempIndex % 4 != 0 ) : ?>
        </li>
      </ul>
    <?php endif; ?>
</ul>
<?php endif; ?>