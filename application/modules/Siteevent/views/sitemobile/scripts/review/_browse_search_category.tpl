<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _browse_search_category.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="form-wrapper" id="category_id-wrapper" style='display:none;'>
    <div class="form-label" id="category_id-label">
        <label class="optional" for="category_id"><?php echo $this->translate('Category'); ?></label>
    </div>
    <div class="form-element" id="category_id-element">
        <select id="category_id" name="category_id" onchange='showFields(this.value, 1);
            addOptions(this.value, "cat_dependency", "subcategory_id", 0);'>
        </select>
    </div>
</div>

<div class="form-wrapper" id="subcategory_id-wrapper" style='display:none;'>
    <div class="form-label" id="subcategory_id-label">
        <label class="optional" for="subcategory_id"><?php echo $this->translate('Sub-Category'); ?></label>
    </div>
    <div class="form-element" id="subcategory_id-element">
        <select id="subcategory_id" name="subcategory_id" onchange='showFields(this.value, 2);
            addOptions(this.value, "subcat_dependency", "subsubcategory_id", 0);'>
        </select>
    </div>
</div>

<div class="form-wrapper" id="subsubcategory_id-wrapper" style='display:none;'>
    <div class="form-label" id="subsubcategory_id-label">
        <label class="optional" for="subsubcategory_id"><?php echo $this->translate('3%s Level Category', "<sup>rd</sup>") ?></label>
    </div>
    <div class="form-element" id="subsubcategory_id-element">
        <select id="subsubcategory_id" name="subsubcategory_id" onchange='showFields(this.value, 3);
                setSubSubCategorySlug(this.value);' >    
        </select>
    </div>
</div>

<script type="text/javascript">
    function setSubSubCategorySlug(value) {
        $('subsubcategoryname').value = siteevent_categories_slug[value];
    }
</script>  