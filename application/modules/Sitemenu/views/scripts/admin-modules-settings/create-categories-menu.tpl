<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create-categories-menu.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


  <?php if (!empty($this->categoryArray) && count($this->categoryArray) > 0): ?>
    <div class="sitestoreproduct_popup_form settings global_form_popup">
      <form id="create_categories_menu" method="post" class="global_form" enctype="application/x-www-form-urlencoded"><div><div>
            <h3>Create categories menu for <?php echo $this->moduleTitle; ?></h3>
            
            <?php if(!empty($this->isPost)):?>
<ul class="form-errors"><li><ul class="error"><li>No category selected. Please select at least one category.</li></ul></li>
<?php endif;?>
            
            <p class="form-description">Select the categories that you want to add as menu for this module. These added categories will be shown as sub menu for this module in the menu editor section of the admin panel.
            </p>
            <div class="form-elements">
              <div class="form-wrapper" id="categories-wrapper" >
                <div class="form-element" id="categories-element">
                  <ul class="form-options-wrapper">
                    <?php foreach ($this->categoryArray as $category) : ?>
                      <?php if (!empty($category['category_id'])) : ?>
                        <li id="<?php echo 'category-'.$category['category_id']; ?> " >
                          <input type="checkbox" value="<?php echo $category['category_name']; ?>" id="<?php echo $category['category_id']; ?>" name="category_submenu[<?php echo $category['category_id']; ?>]">
                          <label id="<?php echo $category['category_name']; ?>" for="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></label>
                        </li>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
              <br />
              <button type="submit" id="submit" name="submit">Create Menu</button>
              or <a onclick="javascript:parent.Smoothbox.close();" href="javascript:void(0);" type="button" id="cancel" name="cancel">cancel</a>
            </div>
          </div>
        </div>
      </form>
    </div>
<?php else: ?>
<?php
  echo '<div class="global_form" style="margin:15px 0 0 15px;"><div><div><h3>' . $this->translate("Alert!") . '</h3>';
  echo '<div class="form-elements" style="margin-top:10px;"><div class="form-wrapper" style="margin-bottom:10px;">' . $this->translate("Currently, there are no categories available in this module to create menus.") . '</div>';
  echo '<div class="form-wrapper"><button onclick="parent.Smoothbox.close()">' . $this->translate("Close") . '</button></div></div></div></div></div>';
  ?>
  <?php endif; ?>



<!--<script type="text/javascript">
$('create_categories_menu').removeEvents('submit').addEvent('submit', function(e) {
  e.stop();
  
  console.log($('category_submenu[]'));
  
//  if( !$("").checked )
    return;
//  else
    e.submit();
});
</script>-->