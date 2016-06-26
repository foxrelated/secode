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
<?php
//WORK FOR MAIN LOOP STARTS HERE

$moreMenu = $tempMenuArray = array();
$moreCountFlag = 1;
$arrValues = $this->arr_values;
$cssClassArray = $this->cssClassArray;
$contentHeight = 220;
$isTitleInside = $parentIsTitleInside = $tempIsTitleInside = 0;
//$temp_page_url = $_SERVER['REQUEST_URI'];
?>


<div id="sitemenu_main_menu_wrapper" class="sitemenu_main_menu_wrapper">
    <nav>
        <div class="sitemenu_mobile_menu_link">
<?php if ($this->isMobile): ?>
                <?php if (!empty($this->changeMyLocation)): ?> 
                    <?php echo $this->content()->renderWidget('seaocore.change-my-location', array('detactLocation' => 0, 'updateUserLocation' => 0, 'showLocationPrivacy' => 0, 'showSeperateLink' => 0, 'placedInMiniMenu' => 1, 'widgetContentId' => $this->identity)) ?> 
                <?php endif; ?> 
            <?php endif; ?>
            <a id="sitemenu_mobile_menu_link" href="javascript:void(0)" onclick="sitemenuMobileMenuLink('nav_cat_<?php echo $this->identity ?>');"><i></i></a>
        </div>

        <ul class="sitemenu_main_menu" id="nav_cat_<?php echo $this->identity ?>">
<?php if (!$this->isMobile): ?>
                <?php if (!empty($this->changeMyLocation)): ?> 
                    <li>
                    <?php echo $this->content()->renderWidget('seaocore.change-my-location', array('detactLocation' => 0, 'updateUserLocation' => 0, 'showLocationPrivacy' => 0, 'showSeperateLink' => 0, 'placedInMiniMenu' => 1, 'widgetContentId' => $this->identity)) ?>
                    </li>  
                    <?php endif; ?> 
            <?php endif; ?>         

            <?php
            if (!empty($this->mainMenusArray)):
                foreach ($this->mainMenusArray as $mainMenuTabArray) :
                    if (empty($mainMenuTabArray['info']))
                        continue;

                    $mainMenuTabMenuObj = $mainMenuTabArray['info']['menuObj'];
                    $menuObjParams = $mainMenuTabMenuObj->params;
                    if ($mainMenuTabMenuObj->custom && empty($menuObjParams['show_to_guest']) && !empty($this->isGuest)) {
                        continue;
                    }
                    $menuItemViewType = !empty($menuObjParams['menu_item_view_type']) ? $menuObjParams['menu_item_view_type'] : "1";

                    if (Engine_Api::_()->sitemenu()->isCurrentTheme('luminous')) {
                        $menuItemViewType = 1;
                    }

//          if( $menuItemViewType == '3' ||  $menuItemViewType == '4' || $menuItemViewType == '5') :
                    if ($menuItemViewType == '3' || $menuItemViewType == '4') :
                        $tempModuleId = !empty($menuObjParams['content']) ? $menuObjParams['content'] : 0;
                        $tempViewByField = !empty($menuObjParams['viewby']) ? $menuObjParams['viewby'] : 0;
                        $tempContentLimit = !empty($menuObjParams['content_limit']) ? $menuObjParams['content_limit'] : 3;
                        $isCategory = !empty($menuObjParams['is_category']) ? $menuObjParams['is_category'] : 0;
                        $tempCategoryLimit = !empty($menuObjParams['category_limit']) ? $menuObjParams['category_limit'] : 5;
                        $tempCategoryId = !empty($menuObjParams['category_id']) ? $menuObjParams['category_id'] : 0;
                        $contentHeight = !empty($menuObjParams['content_height']) ? $menuObjParams['content_height'] : 220;
                        $isTitleInside = !empty($menuObjParams['is_title_inside']) ? $menuObjParams['is_title_inside'] : 0;
                    endif;

                    $navMenuIdArray = null;
                    if ($menuItemViewType == 1) {
                        if (!empty($menuObjParams['nav_menu'])) {
                            $navMenuIdArray = !empty($menuObjParams['sub_navigation']) ? unserialize($menuObjParams['sub_navigation']) : array();
                        }
                    }

                    $tempMenuId = $mainMenuTabMenuObj->id;
                    $tempShowInTab = !empty($menuObjParams['show_in_tab']) ? $menuObjParams['show_in_tab'] : 0;
                    $icon = !empty($menuObjParams['icon']) ? $menuObjParams['icon'] : "";
                    $menuTarget = !empty($menuObjParams['target']) ? $menuObjParams['target'] : "";
                    $mainMenuTabObj = $mainMenuTabArray['info']['zendObj'];
//          $temp_mainMenuTabObj_uri = $mainMenuTabObj->getHref();
                    unset($mainMenuTabArray['info']);
                    // IF THERE IS MORE LINK IN MAIN NAVIGATION
                    if (!empty($this->noOfTabs) && ($moreCountFlag++ > $this->noOfTabs)):
                        if (empty($this->moreLink)) :
                            break;
                        endif;
                        $tempMenuArray['label'] = $mainMenuTabObj->getLabel();
                        $tempMenuArray['menuUri'] = $mainMenuTabObj->getHref();
                        $tempMenuArray['target'] = $menuTarget;
                        $tempMenuArray['tempShowInTab'] = $tempShowInTab;
                        $tempMenuArray['icon'] = $icon;
                        $moreMenu[] = $tempMenuArray;
                        continue;
                    endif;

                    $subMenus = COUNT($mainMenuTabArray);
                    ?>

            <!--<li class="level<?php // echo 0 . "" . (!empty($subMenus) ? 'parent' : ''); echo " ".$cssClassArray[$menuItemViewType]; ?> <?php // if(!empty($temp_mainMenuTabObj_uri) && stristr($temp_page_url, $temp_mainMenuTabObj_uri)): ?>active<?php // endif;?>"  >-->
                    <li class="level<?php echo 0 . "" . ((!empty($subMenus) || !empty($navMenuIdArray)) ? 'parent' : '');
            echo " " . $cssClassArray[$menuItemViewType]; ?>">

                        <?php $mainMenuTaburl = $mainMenuTabObj->getHref(); ?>
                        <a id="main_menu_<?php echo $tempMenuId; ?>" class="level-top" <?php if (!empty($mainMenuTaburl)) { ?>href=" <?php echo $mainMenuTabObj->getHref(); ?>"  <?php } ?> <?php if (!empty($tempModuleId) && ($menuItemViewType == 3 || ($menuItemViewType == 4 && empty($subMenus)))): ?> onmouseover ='getTabContent(<?php echo $tempMenuId; ?>, <?php echo $tempModuleId ?>,<?php echo $tempViewByField ?>, "<?php echo $tempContentLimit ?>",<?php echo $isCategory ?>, <?php echo $tempCategoryLimit ?>, <?php echo $this->truncationLimitContent ?>, <?php echo $this->truncationLimitCategory ?>, <?php echo $tempCategoryId; ?>,<?php echo $contentHeight ?>,<?php echo $isTitleInside ?>);' <?php endif; ?>  <?php if (!empty($menuTarget)): ?> target="_blank" <?php endif; ?>>

                            <?php if (((!empty($tempShowInTab) || $tempShowInTab == 2) && !empty($icon)) && empty($this->isMobile)): ?>
                                <span><i style="background-image:url(<?php echo $icon; ?>)"></i></span>
                            <?php endif; ?>
                            <?php if ((empty($tempShowInTab) || $tempShowInTab == 2) || !empty($this->isMobile)): ?>
                                <span><?php echo $this->translate($mainMenuTabObj->getLabel()) ?></span>
                            <?php endif; ?>
                            <?php if (empty($this->isMobile)): ?>
                                <span>
                                    <?php if (!empty($this->sitemenu_is_arrow) && (!empty($subMenus) || (!empty($tempModuleId) && $menuItemViewType != 1 && $menuItemViewType != 2 ) || (!empty($navMenuIdArray) && $menuItemViewType == 1))): ?>
                                        <i></i>
                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>
                        </a>

                        <?php if (!empty($this->isMobile) && !empty($this->sitemenu_is_arrow) && (!empty($subMenus) || !empty($navMenuIdArray)) && ($menuItemViewType == 1)): ?>
                            <i onclick="void(0);"></i>
                        <?php endif; ?>          
                        <!--DISPLAY SUB-MENUS OR CONTENT ACCORDING TO THE LAYOUT-->
                        <?php if (!empty($menuItemViewType)): ?>            
                            <?php
                            switch ($menuItemViewType):
                                case(1):
                                    include APPLICATION_PATH . '/application/modules/Sitemenu/widgets/menu-main/_standardHierarchicalNavigationMenu.tpl';

                                    break;
                                case (2):
                                    if (empty($this->isMobile)):
                                        include APPLICATION_PATH . '/application/modules/Sitemenu/widgets/menu-main/_multiColumn.tpl';
                                    endif;
                                    break;
                                case (3):
                                    if (empty($this->isMobile)):
                                        include APPLICATION_PATH . '/application/modules/Sitemenu/widgets/menu-main/_mainMenuwithContent.tpl';
                                    endif;
                                    break;
                                case (4):
                                    if (empty($this->isMobile)):
                                        include APPLICATION_PATH . '/application/modules/Sitemenu/widgets/menu-main/_mixedMenu.tpl';
                                    endif;
                                    break;
                            endswitch;
                            ?>            
                        <?php endif; ?>

                    </li>
                    <?php
                endforeach;
            endif;
            ?>

            <?php include APPLICATION_PATH . '/application/modules/Sitemenu/widgets/menu-main/_moreLink.tpl'; ?>
        </ul>
        <?php include APPLICATION_PATH . '/application/modules/Sitemenu/widgets/menu-main/_otherMainMenuOptions.tpl'; ?>
    </nav>
</div>
<!--WORK FOR MAIN LOOP ENDS-->

<script type="text/javascript">
<?php if ($arrValues['sitemenu_is_fixed'] == 0): ?>
        // Fix main header, when we scroll the window
        var height_dif = $('global_header').getSize().y - $('sitemenu_main_menu_wrapper').getSize().y;
        window.addEvent('scroll', function () {
    <?php if ($arrValues['sitemenu_fixed_height'] > 0): ?>
                height_dif = <?php echo $arrValues['sitemenu_fixed_height']; ?>;
    <?php endif; ?>
            mainMenuScrolling(height_dif);
        });
<?php endif; ?>

    en4.core.runonce.add(function () {
        mainMenuDropdownContent();
    });

    function addDropdownMenu() {
        NavigationSitemenu("nav_cat_<?php echo $this->identity ?>", {"show_delay": "100", "hide_delay": "100"});
    }
</script>

<style type="text/css">
    /*Main menu seprators work (dot and pipline)*/
    .sitemenu_main_menu > li+li:before {
        <?php if (!empty($this->sitemenu_separator_style) && $this->sitemenu_separator_style == '1'): ?>
            background: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemenu/externals/images/dot.png) no-repeat center center;
            height: 6px;
            margin: 16px -3px 0;
        <?php elseif (!empty($this->sitemenu_separator_style) && $this->sitemenu_separator_style == '2'): ?>
            background: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemenu/externals/images/smooth_pipe.png) repeat-y center center;
            height: <?php echo $this->sitemenu_main_menu_height + 16; ?>px;
            margin: auto -3px 0;
        <?php elseif (!empty($this->sitemenu_separator_style) && $this->sitemenu_separator_style == '3'): ?>
            background: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemenu/externals/images/pipeline.png) repeat-y center center;
            height: <?php echo $this->sitemenu_main_menu_height + 16; ?>px;
            margin: auto -3px 0;
        <?php endif; ?>
        content: "";
        width: 6px;
        position: absolute;
    }

    /*Main Menu Cart*/
    <?php if (!empty($this->show_extra)): ?>
        #global_header #main_menu_cart{
            display:table !important;
        }
    <?php else: ?>
        #global_header #main_menu_cart{
            display:none;
        }
    <?php endif; ?>

    /*Main menu width settings on scrolling/fixed  */
    <?php if (!empty($this->show_extra)): ?>
        /*Main menu width on always*/
        <?php if ($this->showOption == 2 && !empty($this->show_cart)): ?> 
            /*when { language box and Cart } both will enable*/
            .sitemenu_main_menu_wrapper { display:table; }
            .sitemenu_main_menu{ float:left; width:84%; }
            .sitemenu_main_menu_search{ display:block; }
            #global_header.fixed .sitemenu_main_menu{width:84%; }
            .multi_column ul.level1.shown-sublist{width:116.5%;}
            .main_ContentView .shown-sublist, .mixed_menu .secondlevel_block{width:118.8%;}
        <?php elseif (($this->showOption == 3 || $this->showOption == 4 || $this->showOption == 5) && !empty($this->show_cart)): ?> 
            /*when {(Global Search Box / Product Search Box / Advanced Search Box) and Cart } both will enable*/
            .sitemenu_main_menu_wrapper { display:table; }
            .sitemenu_main_menu{ float:left; width:90%; }
            .sitemenu_main_menu_search{ display:block; }
            #global_header.fixed .sitemenu_main_menu{width:90%; }
            .multi_column ul.level1.shown-sublist{width:108.6%;}
            .main_ContentView .shown-sublist, .mixed_menu .secondlevel_block{width:110.9%;}
            @media only screen and (min-device-width: 600px) and (max-device-width: 890px){
                .sitemenu_main_menu{width: 85%;}
                #global_header.fixed .sitemenu_main_menu{width:85%; }
            } 
        <?php endif; ?>

        <?php if ($this->showOption == 1 && !empty($this->show_cart)): ?>
            /* when only Cart will enable*/
            .sitemenu_main_menu_wrapper { display:table; }
            .sitemenu_main_menu{ float:left; width:96%; }
            .sitemenu_main_menu_search{ display:block; }
            #global_header.fixed .sitemenu_main_menu{ width: 96%;  }
            .main_ContentView .shown-sublist, .mixed_menu .secondlevel_block{width:104%;}
        <?php endif; ?>

        <?php if ($this->showOption == 2 && empty($this->show_cart)): ?>
            /*  when language box is enable and cart not enable */
            .sitemenu_main_menu{ float:left; width:90%; }
            #global_header.fixed .sitemenu_main_menu{width:90%;}
            .main_ContentView .shown-sublist, .mixed_menu .secondlevel_block{width:110.8%;}
        <?php elseif (($this->showOption == 3 || $this->showOption == 4 || $this->showOption == 5) && empty($this->show_cart)): ?>
            /*  when (Global Search Box / Product Search Box / Advanced Search Box) anyone in these will enable and cart not enable*/
            .sitemenu_main_menu{ float:left; width:96%; }
            #global_header.fixed .sitemenu_main_menu{width:96%;}
            .main_ContentView .shown-sublist, .mixed_menu .secondlevel_block{width:110.8%;}
        <?php endif; ?>

        <?php if ($this->showOption == 1 && empty($this->show_cart)): ?>
            /* when nothing is selected in Main menu*/
            .sitemenu_main_menu{ width:100%; }
            .sitemenu_main_menu_search{ display:none; }
            #global_header.fixed .sitemenu_main_menu_search{ display:block; }
            #global_header.fixed .sitemenu_main_menu > li > a.level-top { padding-left:7px; padding-right:7px; }
            .sitemenu_main_menu + div{ display:none; }
            #global_header.fixed .sitemenu_main_menu{ width:100%; }
        <?php endif; ?>

    <?php else: ?>
        /*Main menu width on scrolling*/
        <?php if ($this->showOption == 2 && !empty($this->show_cart)): ?> 
            /*when {(language box ) and Cart } both will enable*/
            .sitemenu_main_menu_wrapper { display:table; }
            .sitemenu_main_menu{ float:left; width:100%; }
            .sitemenu_main_menu+div{display:none}
            #global_header.fixed .sitemenu_main_menu+div{ display:block; }
            #global_header.fixed .sitemenu_main_menu{width:84%; }
            #global_header.fixed .multi_column ul.level1.shown-sublist{width:116.5%;}
            #global_header.fixed .main_ContentView .shown-sublist, #global_header.fixed .mixed_menu .secondlevel_block{width:118.8%;}
        <?php elseif (($this->showOption == 3 || $this->showOption == 4 || $this->showOption == 5) && !empty($this->show_cart)): ?> 
            /*when {(Global Search Box / Product Search Box / Advanced Search Box) and Cart } both will enable*/
            .sitemenu_main_menu_wrapper { display:table; }
            .sitemenu_main_menu{ float:left; width:100%; }
            .sitemenu_main_menu+div{display:none}
            #global_header.fixed .sitemenu_main_menu+div{ display:block; }
            #global_header.fixed .sitemenu_main_menu{width:90%; }
            #global_header.fixed .multi_column ul.level1.shown-sublist{width:108.6%;}
            #global_header.fixed .main_ContentView .shown-sublist, #global_header.fixed .mixed_menu .secondlevel_block{width:110.9%;}
        <?php endif; ?>

        <?php if ($this->showOption == 1 && !empty($this->show_cart)): ?>
            /* when only Cart will enable*/
            .sitemenu_main_menu_wrapper { display:table; }
            .sitemenu_main_menu{ float:left; width:100%; }
            .sitemenu_main_menu+div{display:none}
            #global_header.fixed .sitemenu_main_menu+div{ display:block; }
            #global_header.fixed .sitemenu_main_menu{ width: 96%;  }
            #global_header.fixed .main_ContentView .shown-sublist{width:104%;}
        <?php endif; ?>

        <?php if ($this->showOption == 2 && empty($this->show_cart)): ?>
            /*  when language box is enable and cart not enable*/
            .sitemenu_main_menu{ width:100%; }
            #global_header.fixed .sitemenu_main_menu{width:90%;}
            .sitemenu_main_menu+div{display:none}
            #global_header.fixed .sitemenu_main_menu+div{ display:block; }
            #global_header.fixed .main_ContentView .shown-sublist, #global_header.fixed .mixed_menu .secondlevel_block{width:110.8%;}
        <?php elseif (($this->showOption == 3 || $this->showOption == 4 || $this->showOption == 5) && empty($this->show_cart)): ?>
            /*  when (Global Search Box / Product Search Box / Advanced Search Box) anyone in these will enable and cart not enable*/
            .sitemenu_main_menu{ width:100%; }
            #global_header.fixed .sitemenu_main_menu{width:96%;}
            .sitemenu_main_menu+div{display:none}
            #global_header.fixed .sitemenu_main_menu+div{ display:block; }
            #global_header.fixed .main_ContentView .shown-sublist, #global_header.fixed .mixed_menu .secondlevel_block{width:110.8%;}
        <?php endif; ?>

        <?php if ($this->showOption == 1 && empty($this->show_cart)): ?>
            /* when nothing is selected in Main menu*/
            .sitemenu_main_menu{ width:100%; }
            .sitemenu_main_menu+div{display:none}
            #global_header.fixed .sitemenu_main_menu+div{ display:block; }
            #global_header.fixed .sitemenu_main_menu > li > a.level-top { padding-left:7px; padding-right:7px; }
            #global_header.fixed .sitemenu_main_menu{ width:100%; }
        <?php endif; ?>
    <?php endif; ?>

    /*---------Common css for all the layouts----------*/
    <?php if (empty($arrValues['sitemenu_style'])): ?>
        /*custom colors settings for main menu */
        /*Round-Corner Setting Css start*/
        .sitemenu_main_menu_wrapper {
            background-color: <?php echo $arrValues['sitemenu_menu_background_color']; ?>; /* color of menu main menu widget  */
            <?php if (empty($this->is_box_shadow)): ?> box-shadow: none; <?php endif; ?>
            <?php if ($this->sitemenu_corner_rounding == 1): ?> border-radius: 5px;
            <?php elseif ($this->sitemenu_corner_rounding == 2): ?> border-radius: 20px; <?php endif; ?>
        }
        .sitemenu_main_menu > li.over:first-child a{
            <?php if (empty($this->is_box_shadow)): ?> box-shadow: none; <?php endif; ?>
            <?php if ($this->sitemenu_corner_rounding == 1): ?> border-top-left-radius: 5px;border-bottom-left-radius: 5px;
            <?php elseif ($this->sitemenu_corner_rounding == 2): ?> border-top-left-radius: 20px;border-bottom-left-radius: 20px; <?php endif; ?>
        }
        .sitemenu_main_menu > li.over:last-child a{
            <?php if ($this->sitemenu_corner_rounding == 1): ?> border-top-right-radius: 5px;border-bottom-right-radius: 5px;
            <?php elseif ($this->sitemenu_corner_rounding == 2): ?> border-top-left-radius: 20px;border-bottom-right-radius: 20px; <?php endif; ?>
        }
        .standard_nav li.over:first-child a{
            <?php if ($this->sitemenu_corner_rounding == 1): ?> border-top-left-radius: 5px;border-top-right-radius: 5px;
            <?php elseif ($this->sitemenu_corner_rounding == 2): ?> border-top-left-radius: 20px;border-top-right-radius: 20px; <?php endif; ?>
        }
        .standard_nav li.over:last-child a{
            <?php if ($this->sitemenu_corner_rounding == 1): ?> border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;
            <?php elseif ($this->sitemenu_corner_rounding == 2): ?> border-bottom-left-radius: 20px;border-bottom-right-radius: 20px; <?php endif; ?>
        }
        .standard_nav ul, .multi_column > a + ul, .main_ContentView .shown-sublist, .mixed_menu .secondlevel_block{
            <?php if ($this->sitemenu_corner_rounding == 1): ?> border-radius: 5px;
            <?php elseif ($this->sitemenu_corner_rounding == 2): ?> border-radius: 20px;<?php endif; ?>
        }
        /*Round-Corner Setting Css end*/
        .sitemenu_main_menu > li > a{/* color of main menu label  main menu widget  */
            color: <?php echo $arrValues['sitemenu_menu_link_color']; ?> 
        }
        .sitemenu_main_menu > li > a.over, .sitemenu_main_menu ul li a.over,.main_ContentView .sitemenu_main_menu ul li a:hover, .mixed_menu .sitemenu_main_menu .categories_section li a:hover{/* color of main menu label on hover main menu widget  */
            color: <?php echo $arrValues['sitemenu_menu_hover_color']; ?> ;
            background-color: <?php echo $arrValues['sitemenu_menu_hover_background_color']; ?>; 
        }

        .main_ContentView .categories_section li > a, .main_ContentView .categories_section li > span, .sitemenu_main_menu ul, .level1 ul, .standard_nav ul ul > li:first-child > a:after {/* color of sub menu ul background main menu widget  */
            background-color: <?php echo $arrValues['sitemenu_sub_background_color']; ?> 
        }
        .sitemenu_main_menu ul li > a, .sitemenu_main_menu ul li > span{/* color of sub menu label in main menu widget  */
            color: <?php echo $arrValues['sitemenu_sub_link_color']; ?> 
        }
        .level1 > li > a.over{/* color of main menu label on hover main menu widget  */
            color: <?php echo $arrValues['sitemenu_sub_hover_color']; ?>
                background-color: <?php echo $arrValues['sitemenu_menu_background_color']; ?>; 
        }
        .level2 > li > a.over{/* color of sub sub menu label in main menu widget  */
            color: <?php echo $arrValues['sitemenu_sub_hover_color']; ?>
        }

        /*List Tab View*/
        .multi_column .level2 div {/* color of sub sub menu ul background main menu widget  */
            background-color:<?php echo $arrValues['sitemenu_sub_background_color']; ?> 
        }
        /*Tab view with Content*/
        .main_ContentView .sitemenu_main_menu > li > a.level-top + div , 
        .main_ContentView .sitemenu_main_menu .sitemenu_main_menu_more{/* color of sub menu ul background main menu widget  */
            background-color: <?php echo $arrValues['sitemenu_sub_background_color']; ?> 
        }
        /*Single Column view with content*/
        .mixed_menu .secondlevel_block,
        .mixed_menu .sitemenu_main_menu ul li > a.over, .mixed_menu .secondlevel_block ul li > a.mixed_sub_menu_over{/* color of sub menu ul background main menu widget  */
            background-color: <?php echo $arrValues['sitemenu_sub_background_color']; ?> ;
        }
    <?php else: ?>
        /* color of menu main menu widget  */
        .sitemenu_main_menu_wrapper {box-shadow: none;}  
    <?php endif; ?>

    /*Height of content box when there will be no content*/
    .sitemenu_nocontent{
        line-height:<?php echo!empty($contentHeight) ? $contentHeight : '220'; ?>px;
    }

    /* HEIGHT OF MAIN MENU WRAPPER*/

    .sitemenu_main_menu > li > a.level-top{
        height: <?php echo $this->sitemenu_main_menu_height; ?>px;
        line-height: <?php echo $this->sitemenu_main_menu_height; ?>px;
    }
</style>

<?php
$is_sitemenu_mini_menu_widget_enabled = Zend_Registry::isRegistered('is_sitemenu_mini_menu_widget_enabled') ? Zend_Registry::get('is_sitemenu_mini_menu_widget_enabled') : null;

$sitemenu_on_logged_out_settings = Zend_Registry::isRegistered('sitemenu_on_logged_out_settings') ? Zend_Registry::get('sitemenu_on_logged_out_settings') : null;

if (empty($this->viewer_id) && !empty($is_sitemenu_mini_menu_widget_enabled) && empty($sitemenu_on_logged_out_settings)):
    ?>
    <style type="text/css">
        .layout_page_header{
            left: 0;
            top:0;
            position: fixed;
            right: 0;
            z-index:11;
        }
        .layout_page_header .layout_main{
            padding-top:0;
        }
    </style>
    <?php
endif;
?>