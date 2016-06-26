<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->form): ?>
    <?php echo $this->form->render($this) ?>
<?php endif; ?>

<script type="text/javascript">
    window.addEvent('domready', function () {
        isSubMenuItem();
    });
    var isViewByList = false;

// SHOW/HIDE PARENT MENU AND PARENT SUB MENU SELECT BOX 
    function isSubMenuItem()
    {

        if ($('is_submenu-1').checked)
        {
            $('root_id-wrapper').style.display = 'block';
            $('menu_item_view_type-wrapper').style.display = 'none';
            $('content-wrapper').style.display = 'none';
            $('content_height-wrapper').style.display = 'none';
            $('viewby-wrapper').style.display = 'none';
            $('content_limit-wrapper').style.display = 'none';
            $('is_title_inside-wrapper').style.display = 'none';
            $('is_category-wrapper').style.display = 'none';
            $('category_limit-wrapper').style.display = 'none';
            $('category_id-wrapper').style.display = 'none';
            if ($('luminous_enabled_message'))
                $('luminous_enabled_message').style.display = 'none';
            getParentSubMenuItems();
        } else {
            $('root_id-wrapper').style.display = 'none';
            $('parent_id-wrapper').style.display = 'none';
            $('noSubMenuMessage-wrapper').style.display = 'none';
            $('menu_item_view_type-wrapper').style.display = 'block';
            if ($('luminous_enabled_message'))
                $('luminous_enabled_message').style.display = 'block';
        }
        isMenuItemContent();
    }

// GET ALL THE SUB MENUS OF THE PARENT MENU
    function getParentSubMenuItems()
    {
        $('noSubMenuMessage-wrapper').style.display = 'none';
        var parentId = $('root_id').value;
        var request = new Request.JSON({
            url: en4.core.baseUrl + 'admin/sitemenu/menu-settings/get-sub-menus/',
            onRequest: function () {
                $('parent_id_backgroundimage').style.display = 'block';
                $('parent_id-wrapper').style.display = 'none';
            },
            data: {
                format: 'json',
                parent_id: parentId
            },
            onSuccess: function (responseJSON) {
                var submenus = responseJSON.submenus;
                clear('parent_id');
                $('parent_id_backgroundimage').style.display = 'none';
                $('parent_id-wrapper').style.display = 'block';
                addOption($('parent_id'), " ", '0');
                for (i = 0; i < submenus.length; i++) {
                    addOption($('parent_id'), submenus[i]['label'], submenus[i]['id']);
                }
                if ($('parent_id').style.display == 'none' && $('is_submenu-1').checked) {
                    $('noSubMenuMessage-wrapper').style.display = 'block';
                }

                if (responseJSON.parentViewType) {
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
                        if ($("content-wrapper").style.display == 'none') {
                            viewByList(1);
                        }
                    }
                }

            }

        });
        request.send();

    }

// SHOW/HIDE THE CONTENT FIELDS
    function isMenuItemContent()
    {
        if (!$('menu_item_view_type-1').checked && !$('menu_item_view_type-2').checked)
        {
            $('content-wrapper').style.display = 'block';
            $('content_height-wrapper').style.display = 'block';
            $('viewby-wrapper').style.display = 'block';
            $('content_limit-wrapper').style.display = 'block';
            $('is_title_inside-wrapper').style.display = 'block';
            if ($('category_id-wrapper')) {
                if ($("category_id").length != 0) {
                    $('category_id-wrapper').style.display = 'block';
                    $('is_category-wrapper').style.display = 'block';
                }
            }
            if (!isViewByList)
                viewByList(1);
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
    }

// GET THE POPULARITY CRITERIA
    function viewByList(flag)
    {
        isViewByList = true;

        var request = new Request.JSON({
            url: en4.core.baseUrl + 'admin/sitemenu/menu-settings/get-option-list',
            onRequest: function () {
                $('viewby-wrapper').style.display = 'none';
                $('viewby_backgroundimage').style.display = 'block';
                $('is_category-wrapper').style.display = 'none';
                $('category_limit-wrapper').style.display = 'none';
                if ($('category_id-wrapper')) {
                    $('category_id-wrapper').style.display = 'none';
                    clear('category_id');
                }
            },
            data: {
                format: 'json',
                moduleId: $('content').value
            },
            onSuccess: function (responseJSON) {
                $('content-wrapper').style.display = 'block';
                $('content_height-wrapper').style.display = 'block';
                $('content_limit-wrapper').style.display = 'block';
                $('is_title_inside-wrapper').style.display = 'block';
                $('viewby_backgroundimage').style.display = 'none';
                var modules = responseJSON.modules;
                if (modules != 'null') {
                    clear('viewby');
                    addViewBy($('viewby'), " ", '0');
                    $('viewby-wrapper').style.display = 'block';
                    $('viewby-label').style.display = 'block';

                    for (var key in modules) {

                        if (modules.hasOwnProperty(key))
                        {
                            addViewBy($('viewby'), modules[key], key);
                        }
                    }
                }
                var is_category = responseJSON.is_category;
                if (is_category == 1) {
                    //clear('category_id');
                    $('is_category-wrapper').style.display = 'block';
                    showCategoryLimit();
                    if (responseJSON.categoryArray) {
                        var categoryArray = responseJSON.categoryArray;
                        $('category_id-wrapper').style.display = 'block';
                        addCategory($('category_id'), "", '0');
                        categoryArray.each(function (item, index) {
                            addCategory($('category_id'), item['category_name'], item['category_id']);
                        });
                    }
                } else {
                    $('is_category-wrapper').style.display = 'none';
                    $('category_id-wrapper').style.display = 'none';
                    showCategoryLimit();
                }

            }

        });
        request.send();
    }

// SHOW/HIDE THE CATEGORY LIMIT
    function showCategoryLimit() {
        if ($('is_category-1').checked && $('is_category-wrapper').style.display == 'block') {
            $('category_limit-wrapper').style.display = 'block';
        } else {
            $('category_limit-wrapper').style.display = 'none';
        }
    }

// ADD OPTIONS IN THE PARENT SUB MENU SELECT BOX
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

// SHOW/HIDE THE CHECKBOXES OF POPULARITY CRITERIA
    function addViewBy(selectbox, text, value) {
//    $('viewByCheck'+text).style.display='block';

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
