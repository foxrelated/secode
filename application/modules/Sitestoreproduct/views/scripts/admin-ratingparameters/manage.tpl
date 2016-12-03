<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<script type="text/javascript">
  function more_subcats(cat_id) {
    $$('.subcats_'+cat_id).each(function(el){
				el.style.display = 'block';
		});
    
    $('more_link_cats_'+cat_id).style.display = 'none';
    $('fewer_link_cats_'+cat_id).style.display = 'block';
  }
  
  function fewer_subcats(cat_id) {
    $$('.subcats_'+cat_id).each(function(el){
				el.style.display = 'none';
		});
    
    $('fewer_link_cats_'+cat_id).style.display = 'none';
    $('more_link_cats_'+cat_id).style.display = 'block';
  }  
  
  function more_subsubcats(subcat_id) {
    $$('.subsubcats_'+subcat_id).each(function(el){
				el.style.display = 'block';
		});
    
    $('more_link_subcats_'+subcat_id).style.display = 'none';
    $('fewer_link_subcats_'+subcat_id).style.display = 'block';
  }
  
  function fewer_subsubcats(subcat_id) {
    $$('.subsubcats_'+subcat_id).each(function(el){
				el.style.display = 'none';
		});
    
    $('fewer_link_subcats_'+subcat_id).style.display = 'none';
    $('more_link_subcats_'+subcat_id).style.display = 'block';
  }    
</script> 

<?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/admin-review/_navigationAdmin.tpl'; ?>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Category Based Rating Parameters") ?></h3>
        <p class="form-description">
          <?php echo $this->translate('Below, you can configure rating parameters for the various Product categories, sub-categories and 3rd level categories. By clicking on "Add", "Edit" and "Delete" respectively, you can add multiple new parameters, or edit and delete existing rating parameters. Hence, when a user would go to rate and review a Product belonging to a category, sub-category or 3rd level category he will be able to rate the Product on the parameters configured by you for that category, sub-category and 3rd level category. This extremely useful feature enables gathering of refined ratings, reviews and feedback for the Products in your community.<br /><br /><b>Note:</b> Sub-categories and 3rd level categories will be automatically associated with the rating parameters added in their higher level categories.') ?>
        </p>

        <?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2)): ?>
          <div class="tip">
            <span>
              <?php echo $this->translate("You have not allowed reviews for products. Please goto 'Global Settings' section to allow reviews for products."); ?>
            </span>
          </div> 
        <?php endif; ?>
        
        <?php if(count($this->categories) > 0):?>
          <table class='admin_table sr_sitestoreproduct_mapping_table' width="100%">
            <thead>
              <tr>
              	<th>
	                <div class="sr_sitestoreproduct_mapping_table_name fleft"><b class="bold"><?php echo $this->translate("Category Name") ?></b></div>
	                <div class="sr_sitestoreproduct_mapping_table_value fleft"><b class="bold"><?php echo $this->translate("Review Parameters") ?></b></div>
	                <div class="sr_sitestoreproduct_mapping_table_option fleft"><b class="bold"><?php echo $this->translate("Options") ?></b></div>
                </th>
              </tr>
            </thead>
          <tbody>
            <?php foreach ($this->categories as $category): ?>                
              <tr>
                <td>
                  <div class="sr_sitestoreproduct_mapping_table_name fleft">
                    <span><b class="bold"><?php echo $category['category_name'];?></b></span>
                    <?php if(Count($category['sub_categories']) >= 1):?>
                      <span id="fewer_link_cats_<?php echo $category['category_id']; ?>" >    
                        <a href="javascript:void(0)" onclick="fewer_subcats('<?php echo $category['category_id']; ?>');" title="<?php echo $this->translate('Click to hide sub-categories')?>">[-]</a>
                      </span>                      

                      <span id="more_link_cats_<?php echo $category['category_id']; ?>" style="display:none;">    
                        <a href="javascript:void(0)" onclick="more_subcats('<?php echo $category['category_id']; ?>');" title="<?php echo $this->translate('Click to show sub-categories') ?>">[+]</a>
                      </span>
                    <?php endif;?>
                  </div>                     
									
                  <div class="sr_sitestoreproduct_mapping_table_value fleft">
	                  <ul class="admin-review-cat">
	                    <?php $reviewcat_exist = 0;?>
	                    <?php if(!empty($category['cat_rating_params'])): ?>
	                      <?php $category_id = $category['category_id'];?>
	                      <?php foreach($category['cat_rating_params'][$category_id] as $ratingParams): ?>  
	                        <?php $reviewcat_exist = 1;?>
	                        <li><?php echo $ratingParams['cat_ratingparam_name']; ?></li>
	                      <?php endforeach; ?>
	                    <?php endif; ?>
	                  </ul>
	                  <?php if($reviewcat_exist == 0):?>
	                    ---
	                  <?php endif;?>
                	</div>
                	<div class="sr_sitestoreproduct_mapping_table_option fleft">
	                  <?php if($reviewcat_exist < 1):?>
	                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'ratingparameters', 'action' => 'create', 'category_id' => $category['category_id']), $this->translate('Add'), array(
	                    'class' => 'smoothbox',
	                  )) ?> 
	                  <?php endif; ?>
	
	                  <?php if($reviewcat_exist == 1):?>	
	                   <?php if($reviewcat_exist < 1):?> | <?php endif; ?><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'ratingparameters', 'action' => 'edit', 'category_id' => $category['category_id']), $this->translate('Edit'), array(
	                      'class' => 'smoothbox',
	                    )) ?>
	
	                    | <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'ratingparameters', 'action' => 'delete', 'category_id' => $category['category_id']), $this->translate('Delete'), array(
	                      'class' => 'smoothbox',
	                    )) ?>
	                  <?php endif; ?>
	              	</div>    
                </td>
              </tr>
              <?php foreach ($category['sub_categories'] as $subcategory) : ?> 
                <tr class="subcats_<?php echo $category['category_id']?>">
                  <td>
                    <div class="sr_sitestoreproduct_mapping_table_name fleft">
                      <span><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/gray_bullet.png" alt=""></span>
                      <span><?php echo $subcategory['sub_cat_name'];?></span>
                      <?php if(Count($subcategory['tree_sub_cat']) >= 1):?>
                        <span id="fewer_link_subcats_<?php echo $subcategory['sub_cat_id']; ?>" >    
                          <a href="javascript:void(0)" onclick="fewer_subsubcats('<?php echo $subcategory['sub_cat_id']; ?>');" title="<?php echo $this->translate('Click to hide 3rd level category')?>">[-]</a>
                        </span>                      

                        <span id="more_link_subcats_<?php echo $subcategory['sub_cat_id']; ?>" style="display:none;">    
                          <a href="javascript:void(0)" onclick="more_subsubcats('<?php echo $subcategory['sub_cat_id']; ?>');" title="<?php echo $this->translate('Click to show 3rd level category') ?>">[+]</a>
                        </span>
                      <?php endif;?>
                    </div>                  
                  	<div class="sr_sitestoreproduct_mapping_table_value fleft">
	                    <ul class="admin-review-cat">
	                      <?php $reviewcat_exist = 0;?>
	                      <?php if(!empty($subcategory['subcat_rating_params'])): ?>
	                        <?php $sub_cat_id = $subcategory['sub_cat_id'];?>
	                        <?php foreach($subcategory['subcat_rating_params'][$sub_cat_id] as $ratingParams): ?>  
	                          <?php $reviewcat_exist = 1;?>
	                          <li><?php echo $ratingParams['subcat_ratingparam_name']; ?></li>
	                        <?php endforeach; ?>
	                      <?php endif; ?>
	                    </ul>
	                    <?php if($reviewcat_exist == 0):?>
	                      ---
	                    <?php endif;?>
	                  </div>
	                  <div class="sr_sitestoreproduct_mapping_table_option fleft">
	                    <?php if($reviewcat_exist < 1):?>
	                      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'ratingparameters', 'action' => 'create', 'category_id' => $subcategory['sub_cat_id']), $this->translate('Add'), array(
	                      'class' => 'smoothbox',
	                    )) ?> 
	                    <?php endif; ?>
	
	                    <?php if($reviewcat_exist == 1):?>	
	                     <?php if($reviewcat_exist < 1):?> | <?php endif; ?><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'ratingparameters', 'action' => 'edit', 'category_id' => $subcategory['sub_cat_id']), $this->translate('Edit'), array(
	                        'class' => 'smoothbox',
	                      )) ?>
	
	                      | <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'ratingparameters', 'action' => 'delete', 'category_id' => $subcategory['sub_cat_id']), $this->translate('Delete'), array(
	                        'class' => 'smoothbox',
	                      )) ?>
	                    <?php endif; ?>
	                  </div>  
	              	</td>
                </tr> 
                <?php foreach ($subcategory['tree_sub_cat'] as $subsubcategory) : ?>
                  <tr class="subcats_<?php echo $category['category_id']?> subsubcats_<?php echo $subcategory['sub_cat_id']; ?>">
                    <td>
                    	<div class="sr_sitestoreproduct_mapping_table_name fleft">
                     		<span><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestoreproduct/externals/images/gray_arrow.png" alt=""></span>
                     		<span><?php echo $subsubcategory['tree_sub_cat_name'];?></span>
                     	</div>
                    
                    	<div class="sr_sitestoreproduct_mapping_table_value fleft">
	                      <ul class="admin-review-cat">
	                        <?php $reviewcat_exist = 0;?>
	                        <?php if(!empty($subsubcategory['tree_rating_params'])):?>
	                          <?php $tree_sub_cat_id = $subsubcategory['tree_sub_cat_id'];?>
	                          <?php foreach($subsubcategory['tree_rating_params'][$tree_sub_cat_id] as $ratingParams): ?>  
	                            <?php $reviewcat_exist = 1;?>
	                            <li><?php echo $ratingParams['tree_ratingparam_name']; ?></li>
	                          <?php endforeach; ?>
	                        <?php endif; ?>
	                      </ul>
	                      <?php if($reviewcat_exist == 0):?>
	                        ---
	                      <?php endif;?>
                    	</div>
                    	<div class="sr_sitestoreproduct_mapping_table_option fleft">
	                      <?php if($reviewcat_exist < 1):?>
	                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'ratingparameters', 'action' => 'create', 'category_id' => $subsubcategory['tree_sub_cat_id']), $this->translate('Add'), array(
	                        'class' => 'smoothbox',
	                      )) ?> 
	                      <?php endif; ?>
	
	                      <?php if($reviewcat_exist == 1):?>	
	                       <?php if($reviewcat_exist < 1):?> | <?php endif; ?><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'ratingparameters', 'action' => 'edit', 'category_id' => $subsubcategory['tree_sub_cat_id']), $this->translate('Edit'), array(
	                          'class' => 'smoothbox',
	                        )) ?>
	
	                        | <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'ratingparameters', 'action' => 'delete', 'category_id' => $subsubcategory['tree_sub_cat_id']), $this->translate('Delete'), array(
	                          'class' => 'smoothbox',
	                        )) ?>
	                      <?php endif; ?>
	                  	</div>    
                    </td>
                  </tr>                     
                <?php endforeach;?>
              <?php endforeach;?>
            <?php endforeach; ?>                  
          </tbody>
        </table>
        <?php else:?>
          <br/>
          <div class="tip">
            <span><?php echo $this->translate("There are currently no categories to be mapped.") ?></span>
          </div>
        <?php endif;?>
      </div>
    </form>
  </div>
</div>
