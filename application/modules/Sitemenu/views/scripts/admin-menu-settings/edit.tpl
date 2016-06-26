<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php echo $this->form->render($this); ?>

<script type="text/javascript">
    var parent_view_type = <?php echo $this->parent_view_type; ?>;
<?php if (!empty($this->countStandardNavigation)): ?>
        showMenuCheckbox();
<?php endif; ?>


    if ($('is_submenu-wrapper')) {
        isSubMenuItem(<?php echo $this->depth ?>);
        if (<?php echo $this->depth ?> == 3) {
            $('is_submenu-wrapper').style.display = 'none';
        }
        getParentSubMenuItems();
    }

    var isViewByList = false;

    // SHOW/HIDE STANDARD NAVIGATION MENUS
    function showMenuCheckbox() {
        if ($('is_submenu-0').checked) {
            if ($('menu_item_view_type-1').checked) {
                if ($('is_sub_navigation-wrapper')) {
                    $('is_sub_navigation-wrapper').style.display = 'block';
                    if ($('is_sub_navigation-1').checked) {
                        $('select_sub_navigation-wrapper').style.display = 'block';
                    } else {
                        $('select_sub_navigation-wrapper').style.display = 'none';
                    }
                }
            } else {
                if ($('is_sub_navigation-wrapper')) {
                    $('is_sub_navigation-wrapper').style.display = 'none';
                    $('select_sub_navigation-wrapper').style.display = 'none';
                }
            }
        } else {
            if ($('is_sub_navigation-wrapper')) {
                $('is_sub_navigation-wrapper').style.display = 'block';
                if ($('is_sub_navigation-1').checked) {
                    $('select_sub_navigation-wrapper').style.display = 'block';
                } else {
                    $('select_sub_navigation-wrapper').style.display = 'none';
                }
            }
        }
    }
    // SHOW/HIDE THE PARENT AND ROOT MENUS
    // DEPTH -> 1 = 3RD LEVEL, 2 = 2ND LEVEL, 3 = 1ST LEVEL

    function isSubMenuItem(depth)
    {
        if ($('is_submenu-1').checked)
        {
            if (depth == 1) {
                $('root_id-wrapper').style.display = 'block';
                $('parent_id-wrapper').style.display = 'block';
                $('message-wrapper').style.display = 'none';
                $('menu_item_view_type-wrapper').style.display = 'none';
                if ($('luminous_enabled_message'))
                    $('luminous_enabled_message').style.display = 'none';
            } else if (depth == 2) {
                $('root_id-wrapper').style.display = 'block';
                $('parent_id-wrapper').style.display = 'none';
                $('parent_id').value = 0;
                $('message-wrapper').style.display = 'block';
                $('menu_item_view_type-wrapper').style.display = 'none';
                if ($('luminous_enabled_message'))
                    $('luminous_enabled_message').style.display = 'none';
            } else if (depth == 3) {
                $('root_id-wrapper').style.display = 'none';
                $('parent_id-wrapper').style.display = 'none';
                $('root_id').value = 0;
                $('parent_id').value = 0;
                $('is_submenu-0').checked = true;
                $('message-wrapper').style.display = 'block';
                $('menu_item_view_type-wrapper').style.display = 'block';
                if ($('luminous_enabled_message'))
                    $('luminous_enabled_message').style.display = 'block';
            }

            // TO HIDE CONTENT AND CATEGORY WRAPPER
            $('content-wrapper').style.display = 'none';
            $('content_height-wrapper').style.display = 'none';
            $('content_limit-wrapper').style.display = 'none';
            $('is_title_inside-wrapper').style.display = 'none';
            $('is_category-wrapper').style.display = 'none';
            $('category_limit-wrapper').style.display = 'none';
            $('category_id-wrapper').style.display = 'none';
            $('viewby-wrapper').style.display = 'none';
        } else {
            $('root_id-wrapper').style.display = 'none';
            $('parent_id-wrapper').style.display = 'none';
            $('message-wrapper').style.display = 'none';
            $('noSubMenuMessage-wrapper').style.display = 'none';
            $('menu_item_view_type-wrapper').style.display = 'block';
            if ($('luminous_enabled_message'))
                $('luminous_enabled_message').style.display = 'block';
            showMenuCheckbox();
        }
        isMenuItemContent();
    }

    // SHOW/HIDE THE CONTENT FIELDS
    function isMenuItemContent(flag)
    {
        if (!$('menu_item_view_type-1').checked && !$('menu_item_view_type-2').checked)
        {
            if ($('content-wrapper'))
                $('content-wrapper').style.display = 'block';
            if ($('content_height-wrapper') && $('is_submenu-0').checked)
                $('content_height-wrapper').style.display = 'block';

            if ($('category_id-wrapper')) {
                if ($("category_id").length != 0) {
                    $('category_id-wrapper').style.display = 'block';
                    $('is_category-wrapper').style.display = 'block';
                }
            }

            if (!isViewByList) {
                viewByList(1);
            } else {
                $('viewby-wrapper').style.display = 'block';
            }
            $('content_limit-wrapper').style.display = 'block';
            $('is_title_inside-wrapper').style.display = 'block';

        } else {
            $('content-wrapper').style.display = 'none';
            $('content_height-wrapper').style.display = 'none';
            $('viewby-wrapper').style.display = 'none';
            $('content_limit-wrapper').style.display = 'none';
            $('is_title_inside-wrapper').style.display = 'none';
            $('is_category-wrapper').style.display = 'none';
            $('category_limit-wrapper').style.display = 'none';
            $('category_id-wrapper').style.display = 'none';
        }

        if ($('is_submenu-0').checked)
        {
            showMenuCheckbox();
        } else {
            if (parent_view_type != 0) {
                if (parent_view_type == 1 || parent_view_type == 2) {
                    $('content-wrapper').style.display = 'none';
                    $('content_height-wrapper').style.display = 'none';
                    $('viewby-wrapper').style.display = 'none';
                    $('content_limit-wrapper').style.display = 'none';
                    $('is_title_inside-wrapper').style.display = 'none';
                    $('is_category-wrapper').style.display = 'none';
                    $('category_limit-wrapper').style.display = 'none';
                    $('category_id-wrapper').style.display = 'none';
                } else {
                    $('content-wrapper').style.display = 'block';
                    if ($('is_submenu-0').checked)
                        $('content_height-wrapper').style.display = 'block';
                    $('viewby-wrapper').style.display = 'block';
                    $('content_limit-wrapper').style.display = 'block';
                    $('is_title_inside-wrapper').style.display = 'block';
                    viewByList(1);
                }
                if (parent_view_type == 1) {
                    if ($('is_sub_navigation-wrapper')) {
                        $('is_sub_navigation-wrapper').style.display = 'block';
                        if ($('is_sub_navigation-1').checked) {
                            $('select_sub_navigation-wrapper').style.display = 'block';
                        } else {
                            $('select_sub_navigation-wrapper').style.display = 'none';
                        }
                    }
                } else {
                    if ($('is_sub_navigation-wrapper')) {
                        $('is_sub_navigation-wrapper').style.display = 'none';
                        $('select_sub_navigation-wrapper').style.display = 'none';
                    }
                }
            }
        }

    }

    // SHOW/HIDE CONTENT CATEGORY LIMIT 
    function showCategoryLimit() {
        if ($('is_category-1').checked && $('is_category-wrapper').style.display == 'block') {
            $('category_limit-wrapper').style.display = 'block';
        } else {
            $('category_limit-wrapper').style.display = 'none';
        }
        if ($('is_category-wrapper').style.display == 'block') {
            $('category_id-wrapper').style.display == 'block';
        }
    }
    //GET ALL CHILD MENUS OF PARENT MENU        
    function getParentSubMenuItems()
    {
        var parentId = $('root_id').value;
        var temp_depth =<?php echo $this->depth ?>;
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'admin/sitemenu/menu-settings/get-sub-menus',
            onRequest: function () {
                $('noSubMenuMessage-wrapper').style.display = 'none';
                $('parent_id_backgroundimage').style.display = 'block';
                $('parent_id-wrapper').style.display = 'none';
            },
            data: {
                format: 'json',
                parent_id: parentId,
                main_tab_id: <?php echo $this->menuItem->id; ?>
            },
            onSuccess: function (responseJSON) {
                $('parent_id_backgroundimage').style.display = 'none';
                if ($('is_submenu-1').checked && temp_depth != 2) {
                    $('parent_id-wrapper').style.display = 'block';
                }
                var submenus = responseJSON.submenus;
                clear('parent_id');
                addOption($('parent_id'), " ", '0');

                if (responseJSON.submenus.length)
                {
                    for (i = 0; i < submenus.length; i++) {
                        addOption($('parent_id'), submenus[i]['label'], submenus[i]['id']);
                    }

                }
                if ($('parent_id-wrapper').style.display == 'none' && $('is_submenu-1').checked && $('message-wrapper').style.display == 'none') {
                    $('noSubMenuMessage-wrapper').style.display = 'block';
                }

<?php if (empty($this->submenu_id)) : ?>
                    $('parent_id').value = 0;
<?php else: ?>
                    $('parent_id').value = <?php echo $this->submenu_id; ?>;
<?php endif; ?>

                if ($('is_submenu-1').checked && responseJSON.parentViewType) {
                    parent_view_type = responseJSON.parentViewType;
                    if (responseJSON.parentViewType == 1 || responseJSON.parentViewType == 2) {
                        $('content-wrapper').style.display = 'none';
                        $('content_height-wrapper').style.display = 'none';
                        $('viewby-wrapper').style.display = 'none';
                        $('content_limit-wrapper').style.display = 'none';
                        $('is_title_inside-wrapper').style.display = 'none';
                        $('is_category-wrapper').style.display = 'none';
                        $('category_limit-wrapper').style.display = 'none';
                        $('category_id-wrapper').style.display = 'none';
                    } else {
                        $('content-wrapper').style.display = 'block';
                        if ($('is_submenu-0').checked)
                            $('content_height-wrapper').style.display = 'block';
                        $('viewby-wrapper').style.display = 'block';
                        $('content_limit-wrapper').style.display = 'block';
                        $('is_title_inside-wrapper').style.display = 'block';
                        viewByList(1);
                    }
                    if (responseJSON.parentViewType == 1) {
                        if ($('is_sub_navigation-wrapper')) {
                            $('is_sub_navigation-wrapper').style.display = 'block';
                            if ($('is_sub_navigation-1').checked) {
                                $('select_sub_navigation-wrapper').style.display = 'block';
                            } else {
                                $('select_sub_navigation-wrapper').style.display = 'none';
                            }
                        }
                    } else {
                        if ($('is_sub_navigation-wrapper')) {
                            $('is_sub_navigation-wrapper').style.display = 'none';
                            $('select_sub_navigation-wrapper').style.display = 'none';
                        }
                    }
                }
            }
        }));

    }

    // ADD OPTIONS IN SELECTBOX PARENT SUB MENU
    function addOption(selectbox, text, value)
    {
        var optn = document.createElement("OPTION");
        optn.text = text;
        optn.value = value;

        if (optn.text != '' && optn.value != '') {
            $('parent_id').style.display = 'block';
            $('parent_id-label').style.display = 'block';
            selectbox.options.add(optn);
        } else {
            $('parent_id').style.display = 'none';
            $('parent_id-label').style.display = 'none';
            selectbox.options.add(optn);
        }
    }

    // REMOVE ALL THE OPTIONS FROM THE SELECTBOX        
    function clear(ddName)
    {
        for (var i = (document.getElementById(ddName).options.length - 1); i >= 0; i--)
        {
            document.getElementById(ddName).options[ i ] = null;
        }
    }

    // SHOW/HIDE MESSAGE FOR SUB MENUS
    function show_message(childCount)
    {
        if (childCount > 1)
        {
            $('parent_id-wrapper').style.display = 'none';
            $('message-wrapper').style.display = 'block';
        }
        else
        {
            $('message-wrapper').style.display = 'none';
            $('parent_id-wrapper').style.display = 'block';
        }
    }

    // GET POPULARITY CRITERIA
    function viewByList(flag)
    {
        isViewByList = true;
        var request = new Request.JSON({
            url: en4.core.baseUrl + 'admin/sitemenu/menu-settings/get-option-list',
            onRequest: function () {
                $('viewby-wrapper').style.display = 'none';
                $('viewby_backgroundimage').style.display = 'block';
                $('is_category-wrapper').style.display = 'none';
                $('category_id-wrapper').style.display = 'none';
            },
            data: {
                format: 'json',
                moduleId: $('content').value
            },
            onSuccess: function (responseJSON) {
                $('viewby_backgroundimage').style.display = 'none';
                var modules = responseJSON.modules;
                if (modules != 'null') {
                    clear('viewby');
                    addViewBy($('viewby'), " ", '0');
                    if (!$('menu_item_view_type-1').checked && !$('menu_item_view_type-2').checked) {
                        if ($('content-wrapper'))
                            $('content-wrapper').style.display = 'block';
                        if ($('content_height-wrapper') && ($('is_submenu-0').checked))
                            $('content_height-wrapper').style.display = 'block';

                        $('is_category-wrapper').style.display = 'none';
                        $('category_limit-wrapper').style.display = 'none';
                        $('category_id-wrapper').style.display = 'none';
                        $('content_limit-wrapper').style.display = 'block';
                        $('is_title_inside-wrapper').style.display = 'block';
                    }

                    $('viewby-wrapper').style.display = 'block';
                    $('viewby-label').style.display = 'block';
                    for (var key in modules) {
                        if (modules.hasOwnProperty(key))
                        {
                            addViewBy($('viewby'), modules[key], key);
                        }
                    }
                }

<?php if (empty($this->viewby_value)) : ?>
                    $('viewby').value = 0;
<?php else: ?>
                    $('viewby').value = <?php echo $this->viewby_value; ?>;
<?php endif; ?>
                var is_category = responseJSON.is_category;
                if (is_category == 1) {
                    $('is_category-wrapper').style.display = 'block';
                    showCategoryLimit();
                    if (responseJSON.categoryArray) {
                        var categoryArray = responseJSON.categoryArray;
                        $('category_id-wrapper').style.display = 'block';
                        clear('category_id');
                        addCategory($('category_id'), "", '0');
                        categoryArray.each(function (item, index) {
                            addCategory($('category_id'), item['category_name'], item['category_id']);
                        });
                    }
                } else {
                    $('is_category-wrapper').style.display = 'none';
                    showCategoryLimit();
                }

<?php if (empty($this->category_id)) : ?>
                    $('category_id').value = 0;
<?php else: ?>
                    $('category_id').value = <?php echo $this->category_id; ?>;
<?php endif; ?>
            }
        });
        request.send();
    }

    // SHOW/HIDE THE POPULARITY CRITERIA CHECKBOXES
    function addViewBy(selectbox, text, value)
    {
//            $('viewByCheck'+text).style.display='block';

        var optn = document.createElement("OPTION");
        optn.text = text;
        optn.value = value;

        if (optn.text != '' && optn.value != '') {
            $('viewby').style.display = 'block';
            $('viewby-label').style.display = 'block';
            selectbox.options.add(optn);
        } else {
            $('viewby').style.display = 'none';
            $('viewby-label').style.display = 'none';
            selectbox.options.add(optn);
        }
    }

    // ADD OPTIONS IN THE CATEGORY SELECT BOX
    function addCategory(selectbox, text, value)
    {
        var optn = document.createElement("OPTION");
        optn.text = text;
        optn.value = value;

        if (optn.text != '' && optn.value != '') {
            $('category_id').style.display = 'block';
            $('category_id-label').style.display = 'block';
            selectbox.options.add(optn);
        } else {
            $('category_id').style.display = 'none';
            $('category_id-label').style.display = 'none';
            selectbox.options.add(optn);
        }
    }
</script>
