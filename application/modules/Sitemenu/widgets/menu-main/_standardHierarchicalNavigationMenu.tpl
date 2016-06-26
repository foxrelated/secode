<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _standardHierarchicalNavigationMenu.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
if (!empty($menuObjParams['nav_menu'])) {
    $tempNavMenuObj = Engine_Api::_()->getApi('menus', 'core')->getNavigation($menuObjParams['nav_menu']);
    $navMenuIdArray = !empty($menuObjParams['sub_navigation']) ? unserialize($menuObjParams['sub_navigation']) : null;
    if (!is_array($navMenuIdArray)) {
        $navMenuIdArray = null;
    }
} else {
    $navMenuIdArray = null;
}
$menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
$sitemenu_main_menu_list_standard_navigation = Zend_Registry::isRegistered('sitemenu_main_menu_list_standard_navigation') ? Zend_Registry::get('sitemenu_main_menu_list_standard_navigation') : null;
?>  
<!--DISPLAY SUB-MENUS-->
<?php if (!empty($subMenus) || !empty($navMenuIdArray)): ?>
    <ul class="level1" >
        <?php
        // FOR STANDARD NAVIGATION
        if (!empty($tempNavMenuObj)):

            foreach ($tempNavMenuObj as $navMenu):
                $tempMenuClass = explode(" ", $navMenu->getClass());
                $getMenuId = $menuItemsTable->select()->from($menuItemsTable->info('name'), array("id"))
                                ->where("name = ?", end($tempMenuClass))->query()->fetchColumn();

                if (!empty($getMenuId) && is_array($navMenuIdArray) && in_array($getMenuId, $navMenuIdArray)):
                    ?>
                    <li class="level1nav">
                        <!-- IF IS PAID LISTING TYPE AND PACKAGE COUNT IS GREATOR THAN ZERO REDIRECT TO PACKAGE PAGE -->
                        <?php
                        $temp_nav_action = $navMenu->getHref();
                        $tabs_listingtype_id = Engine_Api::_()->sitemenu()->isPaidListingPackageEnabled($tempMenuClass);
                        if (!empty($tabs_listingtype_id)):
                            $temp_nav_action = $this->url(array('action' => 'index'), 'sitereview_package_listtype_' . $tabs_listingtype_id, true);
                        endif;

                        if (Engine_Api::_()->sitemenu()->isEventPackageEnabled($tempMenuClass)):
                            $temp_nav_action = $this->url(array('action' => 'index'), "siteevent_package", true);
                        endif;
                        ?>
                        <a href="<?php echo $temp_nav_action ?>">
                            <span><?php echo $this->translate($navMenu->getLabel()) ?></span>
                        </a>
                    </li>
                    <?php
                endif;
            endforeach;
        endif;
        ?>
        <?php
        foreach ($mainMenuTabArray as $subMenusList):
            $subMainMenuTabMenuObj = $subMenusList['info']['menuObj'];
            $subMenuObjParams = $subMainMenuTabMenuObj->params;
            if (empty($sitemenu_main_menu_list_standard_navigation) || ($subMainMenuTabMenuObj->custom && empty($subMenuObjParams['show_to_guest']) && !empty($this->isGuest))) {
                continue;
            }
            $tempShowInSubTab = !empty($subMenuObjParams['show_in_tab']) ? $subMenuObjParams['show_in_tab'] : 0;
            $subIcon = !empty($subMenuObjParams['icon']) ? $subMenuObjParams['icon'] : "";
            $subMenuTarget = !empty($subMenuObjParams['target']) ? $subMenuObjParams['target'] : "";
            $subMainMenuTabObj = $subMenusList['info']['zendObj'];
            unset($subMenusList['info']);
            $subSubMenus = COUNT($subMenusList);

            if (!empty($subMenuObjParams['nav_menu'])) {
                $tempSubNavMenuObj = Engine_Api::_()->getApi('menus', 'core')->getNavigation($subMenuObjParams['nav_menu']);
                $subNavMenuIdArray = !empty($subMenuObjParams['sub_navigation']) ? unserialize($subMenuObjParams['sub_navigation']) : null;
                if (!is_array($subNavMenuIdArray)) {
                    $subNavMenuIdArray = null;
                }
            } else {
                $subNavMenuIdArray = null;
            }
            ?>
            <li class="level<?php echo 1 . ((!empty($subSubMenus) || (!empty($subNavMenuIdArray) && is_array($subNavMenuIdArray))) ? 'parent' : ''); ?>">
                <?php $subMainMenuTabUrl = $subMainMenuTabObj->getHref(); ?>
                <a <?php if (!empty($subMainMenuTabUrl)) { ?> href="<?php echo $subMainMenuTabObj->getHref() ?>" <?php } ?> <?php if (!empty($subMenuTarget)): ?> target="_blank" <?php endif; ?> >
                    <?php if ((!empty($tempShowInSubTab) || $tempShowInSubTab == 2) && !empty($subIcon)): ?>
                        <span><i style="background-image:url(<?php echo $subIcon; ?>)"></i></span>
                    <?php endif; ?>
                    <?php if (empty($tempShowInSubTab) || $tempShowInSubTab == 2): ?>
                        <span><?php echo $this->translate($subMainMenuTabObj->getLabel()) ?></span>
                    <?php endif; ?>
                </a>

                <!--DISPLAY SUB-SUB-MENUS-->
                <?php if (!empty($subSubMenus) || !empty($subNavMenuIdArray)): ?>
                    <ul class="level2 sitemenu_standnav_submenu" >

                        <?php
                        if (!empty($tempSubNavMenuObj)):
                            foreach ($tempSubNavMenuObj as $subNavMenu):
                                $tempMenuClass = explode(" ", $subNavMenu->getClass());
                                $getSubMenuId = $menuItemsTable->select()->from($menuItemsTable->info('name'), array("id"))
                                                ->where("name = ?", end($tempMenuClass))->query()->fetchColumn();

                                if (!empty($getSubMenuId) && is_array($subNavMenuIdArray) && in_array($getSubMenuId, $subNavMenuIdArray)):
                                    ?>
                                    <li class="level2">
                                        <!-- IF IS PAID LISTING TYPE AND PACKAGE COUNT IS GREATOR THAN ZERO REDIRECT TO PACKAGE PAGE -->
                                        <?php
                                        $temp_nav_action = $subNavMenu->getHref();
                                        $tabs_listingtype_id = Engine_Api::_()->sitemenu()->isPaidListingPackageEnabled($tempMenuClass);
                                        if (!empty($tabs_listingtype_id)):
                                            $temp_nav_action = $this->url(array('action' => 'index'), 'sitereview_package_listtype_' . $tabs_listingtype_id, true);
                                        endif;

                                        if (Engine_Api::_()->sitemenu()->isEventPackageEnabled($tempMenuClass)):
                                            $temp_nav_action = $this->url(array('action' => 'index'), "siteevent_package", true);
                                        endif;
                                        ?>
                                        <a href="<?php echo $temp_nav_action ?>">
                                            <span><?php echo $this->translate($subNavMenu->getLabel()) ?></span>
                                        </a>
                                    </li>
                                    <?php
                                endif;
                            endforeach;
                        endif;
                        ?>

                        <?php
                        foreach ($subMenusList as $subSubMenu):
                            $subSubMainMenuTabMenuObj = $subSubMenu['info']['menuObj'];
                            $subSubMenuObjParams = $subSubMainMenuTabMenuObj->params;
                            if (empty($sitemenu_main_menu_list_standard_navigation) || ($subSubMainMenuTabMenuObj->custom && empty($subSubMenuObjParams['show_to_guest']) && !empty($this->isGuest))) {
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
                                <a <?php if (!empty($subSubMainMenuUrl)) : ?> href="<?php echo $subSubMainMenuTabObj->getHref() ?>" <?php endif; ?> <?php if (!empty($subSubMenuTarget)): ?> target="_blank" <?php endif; ?>>
                                    <?php if ((!empty($tempShowInSubSubTab) || $tempShowInSubSubTab == 2) && !empty($subSubIcon)): ?>
                                        <span><i style="background-image:url(<?php echo $subSubIcon; ?>)"></i></span>
                                    <?php endif; ?>
                                    <?php if (empty($tempShowInSubSubTab) || $tempShowInSubSubTab == 2): ?>
                                        <span><?php echo $this->translate($subSubMainMenuTabObj->getLabel()) ?></span>
                                    <?php endif; ?>
                                </a>
                                <?php
                                if (!empty($subSubMenuObjParams['nav_menu'])) {
                                    $tempSubSubNavMenuObj = Engine_Api::_()->getApi('menus', 'core')->getNavigation($subSubMenuObjParams['nav_menu']);
                                    $subSubNavMenuIdArray = !empty($subSubMenuObjParams['sub_navigation']) ? unserialize($subSubMenuObjParams['sub_navigation']) : array();
                                } else {
                                    $subSubNavMenuIdArray = null;
                                }
                                ?>
                                <?php if (!empty($subSubNavMenuIdArray)): ?>
                                    <ul class="level3">
                                        <?php
                                        if (!empty($tempSubSubNavMenuObj)):
                                            foreach ($tempSubSubNavMenuObj as $subSubNavMenu):
                                                $tempMenuClass = explode(" ", $subSubNavMenu->getClass());
                                                $getSubSubMenuId = $menuItemsTable->select()->from($menuItemsTable->info('name'), array("id"))
                                                                ->where("name = ?", end($tempMenuClass))->query()->fetchColumn();

                                                if (!empty($getSubSubMenuId) && in_array($getSubSubMenuId, $subSubNavMenuIdArray)):
                                                    ?>
                                                    <li class="level3">
                                                        <!-- IF IS PAID LISTING TYPE AND PACKAGE COUNT IS GREATOR THAN ZERO REDIRECT TO PACKAGE PAGE -->
                                                        <?php
                                                        $temp_nav_action = $subSubNavMenu->getHref();
                                                        $tabs_listingtype_id = Engine_Api::_()->sitemenu()->isPaidListingPackageEnabled($tempMenuClass);
                                                        if (!empty($tabs_listingtype_id)):
                                                            $temp_nav_action = $this->url(array('action' => 'index'), 'sitereview_package_listtype_' . $tabs_listingtype_id, true);
                                                        endif;

                                                        if (Engine_Api::_()->sitemenu()->isEventPackageEnabled($tempMenuClass)):
                                                            $temp_nav_action = $this->url(array('action' => 'index'), "siteevent_package", true);
                                                        endif;
                                                        ?>
                                                        <a href="<?php echo $temp_nav_action ?>">
                                                            <span><?php echo $this->translate($subSubNavMenu->getLabel()) ?></span>
                                                        </a>
                                                    </li>
                                                    <?php
                                                endif;
                                            endforeach;
                                        endif;
                                        ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>                        
    <?php


     endif;  