<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _mixedMenu.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<div>                        
            <!--DISPLAY SUB-MENUS-->
            <div id="main_menu_content_<?php echo $tempMenuId; ?>" class="<?php if(!empty($subMenus) || !empty($tempModuleId)): ?><?php echo 'secondlevel_block' ?><?php endif; ?>" <?php if(!empty($subMenus) || !empty($tempModuleId)): ?>style="height: <?php echo $contentHeight;?>px"<?php endif;?> >
              <?php $sitemenu_mainmenu_singleColumnContent = Zend_Registry::isRegistered('sitemenu_mainmenu_singleColumnContent') ? Zend_Registry::get('sitemenu_mainmenu_singleColumnContent') : null; ?>
              <?php if (!empty($subMenus)): ?>
              <?php $menu_count = $tempIndex = 0;  ?>
              <ul class="level1" id="mixed_menu_level1block" style="height: <?php echo $contentHeight;?>px">
                  <?php
                  foreach ($mainMenuTabArray as $subMenusList):
                    $subMainMenuTabMenuObj = $subMenusList['info']['menuObj'];
                    $subMenuObjParams = $subMainMenuTabMenuObj->params;
                    if( $tempIndex++ == 0 ) {
                      $parentTempModuleId = $subTempModuleId = !empty($subMenuObjParams['content']) ? $subMenuObjParams['content'] : 0;
                      $parentTempViewByField = $tempViewByField = !empty($subMenuObjParams['viewby']) ? $subMenuObjParams['viewby'] : 0;
                      $parentTempContentLimit = $tempContentLimit = !empty($subMenuObjParams['content_limit']) ? $subMenuObjParams['content_limit'] : 3;
                      $parentTempCategoryLimit = $tempCategoryLimit = !empty($subMenuObjParams['category_limit']) ? $subMenuObjParams['category_limit'] : 5;
                      $parentTempCategoryId = $tempCategoryId = !empty($subMenuObjParams['category_id']) ? $subMenuObjParams['category_id'] : 0;
                      $parentIsCategory = $isCategory = !empty($subMenuObjParams['is_category']) ? $subMenuObjParams['is_category'] : 0;
                      $parentIsTitleInside = $tempIsTitleInside = !empty($subMenuObjParams['is_title_inside']) ? $subMenuObjParams['is_title_inside'] : 0;
                      $parentTempSubMenuId = $tempSubMenuId = $subMainMenuTabMenuObj->id;
                    } else {
                      $subTempModuleId = !empty($subMenuObjParams['content']) ? $subMenuObjParams['content'] : 0;
                      $tempViewByField = !empty($subMenuObjParams['viewby']) ? $subMenuObjParams['viewby'] : 0;
                      $tempContentLimit = !empty($subMenuObjParams['content_limit']) ? $subMenuObjParams['content_limit'] : 3;
                      $tempCategoryLimit = !empty($subMenuObjParams['category_limit']) ? $subMenuObjParams['category_limit'] : 5;
                      $tempCategoryId = !empty($subMenuObjParams['category_id']) ? $subMenuObjParams['category_id'] : 0;
                      $isCategory = !empty($subMenuObjParams['is_category']) ? $subMenuObjParams['is_category'] : 0;
                      $tempIsTitleInside = !empty($subMenuObjParams['is_title_inside']) ? $subMenuObjParams['is_title_inside'] : 0;
                      $tempSubMenuId = $subMainMenuTabMenuObj->id;
                    }
                    
                    $menu_count++;
                    if (empty($sitemenu_mainmenu_singleColumnContent) || ($subMainMenuTabMenuObj->custom && empty($subMenuObjParams['show_to_guest']) && !empty($this->isGuest))) {
                      continue;
                    }
                    $tempShowInSubTab = !empty($subMenuObjParams['show_in_tab']) ? $subMenuObjParams['show_in_tab'] : 0;
                    $subIcon = !empty($subMenuObjParams['icon']) ? $subMenuObjParams['icon'] : "";
                    $subMenuTarget = !empty($subMenuObjParams['target']) ? $subMenuObjParams['target'] : "";
                    $subMainMenuTabObj = $subMenusList['info']['zendObj'];
                    unset($subMenusList['info']);
                    $subSubMenus = COUNT($subMenusList);
                    ?>
                    <li class="level<?php echo 1 . "" . (!empty($subTempModuleId) ? 'parent' : '') ?>">
                
                      <?php $subMainMenuTabUrl = $subMainMenuTabObj->getHref(); ?>
                      <a id="sub_menu_<?php echo $tempSubMenuId ?>" <?php if(!empty ($subMainMenuTabUrl)){ ?>href="<?php echo $subMainMenuTabObj->getHref() ?>" <?php } if (!empty($subTempModuleId)): ?> onmouseover='getTabContent(<?php echo $tempSubMenuId; ?>, <?php echo $subTempModuleId ?>,<?php echo $tempViewByField ?>,"<?php echo $tempContentLimit ?>",<?php echo $isCategory ?>, <?php echo $tempCategoryLimit ?>, <?php echo $this->truncationLimitContent?>, <?php echo $this->truncationLimitCategory?>, <?php echo $tempCategoryId;?>,<?php echo $contentHeight;?>,<?php echo $tempIsTitleInside?>);' rel="<?php echo $tempSubMenuId."#".$subTempModuleId."#".$tempViewByField."#".$tempContentLimit ."#".$isCategory."#".$tempCategoryLimit."#".$this->truncationLimitContent."#".$this->truncationLimitCategory."#".$tempCategoryId."#".$contentHeight."#".$tempIsTitleInside ?>" <?php endif; ?> <?php if(!empty($subMenuTarget)): ?> target="_blank" <?php endif;?> <?php if(!empty($this->isMobile) && (!empty($subTempModuleId))):?>onclick="return advancedMenuMainClick(this); "<?php endif;?>>
                        <?php if ((!empty($tempShowInSubTab) || $tempShowInSubTab == 2) && !empty($subIcon)): ?>
                           <span><i style="background-image:url(<?php echo $subIcon; ?>)"></i></span>
                          <?php endif; ?>
                        <?php if (empty($tempShowInSubTab) || $tempShowInSubTab == 2): ?>
                            <span><?php echo $this->translate($subMainMenuTabObj->getLabel()) ?></span>
                        <?php endif; ?>
                      </a> 
                         <div id="tab_content_<?php echo $tempSubMenuId; ?>"></div>
                     </li>
                  <?php endforeach; ?>
                  <?php $containerHeight = $menu_count * 30; ?>
                  <?php $remainingHeight = $contentHeight - $containerHeight; ?>
                  <?php if( !empty($remainingHeight) && $remainingHeight > 1 ) : ?>
                  <li id="extra_space_container_<?php echo $tempMenuId; ?>" style="height: <?php echo $remainingHeight ?>px">
                    <div id="tab_content_<?php echo $tempMenuId; ?>_<?php echo $parentTempSubMenuId; ?>"></div>
                  </li>
                  <?php endif; ?>
                </ul>        
              <?php endif; ?>     
              <div id="tab_content_<?php echo $tempMenuId; ?>"></div>
            </div>   
          </div>

<?php if( !empty($tempMenuId) && !empty($subMenus) && !empty($parentTempModuleId) ) : ?>
  <script type="text/javascript">
   
    if( document.getElementById("main_menu_<?php echo $tempMenuId; ?>") ) {
      document.getElementById("main_menu_<?php echo $tempMenuId; ?>").removeEvent('mouseover').addEvent('mouseover', function(event) {  
        event.stopPropagation();
        var mouseover= $("main_menu_content_<?php echo $tempMenuId; ?>").getElement('.level1').getElement('.level1parent').getElement('a').get('rel');
        var par=mouseover.split('#');
        en4.advancedMenu.fireNavEvent($("main_menu_content_<?php echo $tempMenuId; ?>").getElement('.level1').getElement('.level1parent'),true);
        $("main_menu_content_<?php echo $tempMenuId; ?>").getElement('.level1').getElement('.level1parent').getElement('a').addClass('mixed_sub_menu_over');
        var i = 0;
        getTabContent(par[i++],par[i++],par[i++],par[i++],par[i++],par[i++],par[i++],par[i++],par[i++],par[i++],par[i++]);
      });
      
      document.getElementById("main_menu_<?php echo $tempMenuId; ?>").removeEvent('mouseout').addEvent('mouseout', function(event) {  
        event.stopPropagation();
        var li=$("main_menu_content_<?php echo $tempMenuId; ?>").getElement('.level1').getElement('.level1parent');
        en4.advancedMenu.fireNavEvent(li,false);
        $("main_menu_content_<?php echo $tempMenuId; ?>").getElement('.level1').getElement('.level1parent').getElement('a').removeClass('mixed_sub_menu_over');
      });
    }
    
//    if( document.getElementById("tab_content_<?php // echo $tempMenuId; ?>") ) {
//      document.getElementById("tab_content_<?php // echo $tempMenuId; ?>").removeEvent('mouseover').addEvent('mouseover', function(event) {
//        if( !$("sub_menu_"+containerElementId).hasClass('over') ) {
//          $("sub_menu_"+containerElementId).addClass('over');
//        }
//        $("tab_content_<?php // echo $tempMenuId; ?>").innerHTML = $("tab_content_" + containerElementId).innerHTML; 
//      });
//      document.getElementById("tab_content_<?php // echo $tempMenuId; ?>").removeEvent('mouseout').addEvent('mouseout', function(event) {
//        if( $("sub_menu_"+containerElementId).hasClass('over') ) {
//          $("sub_menu_"+containerElementId).removeClass('over');
//        }
//      });
//    }
//    
//    if( document.getElementById("extra_space_container_<?php // echo $tempMenuId; ?>") ) {
//      document.getElementById("extra_space_container_<?php // echo $tempMenuId; ?>").removeEvent('mouseover').addEvent('mouseover', function(event) {
//        $("tab_content_" +<?php // echo $tempMenuId; ?> + "_" + <?php // echo $parentTempSubMenuId; ?>).innerHTML = $("tab_content_" + containerElementId).innerHTML; 
//        if( ($("sub_menu_"+containerElementId)) && !$("sub_menu_"+containerElementId).hasClass('over') ) {
//          $("sub_menu_"+containerElementId).addClass('over');
//        }
//      });
//      document.getElementById("extra_space_container_<?php // echo $tempMenuId; ?>").removeEvent('mouseout').addEvent('mouseout', function(event) {
//        if( ($("sub_menu_"+containerElementId)) && $("sub_menu_"+containerElementId).hasClass('over') ) {
//          $("sub_menu_"+containerElementId).removeClass('over');
//        }
//      });
//    }
  </script>
<?php endif;