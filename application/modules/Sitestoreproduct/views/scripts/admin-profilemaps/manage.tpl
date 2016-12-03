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

<h2 class="fleft">
  <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin');?>
</h2>


<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='tabs'>
  <ul class="navigation">
    <li>
    <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestore','controller'=>'profilemaps','action'=>'manage'), $this->translate('Stores'), array())
    ?>
    </li>
    <li class="active">
    <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitestoreproduct','controller'=>'profilemaps','action'=>'manage'), $this->translate('Products'), array())
    ?>
    </li>			
  </ul>
</div>

<div class='seaocore_settings_form'>
	<div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Category to Product Profile Mapping") ?> </h3>
        <p class="form-description">
          <?php echo $this->translate("This mapping will associate a Product Profile Type with a Category / Sub-category / 3rd Level Category. After such a mapping for a category, sub-category or 3rd level category, product owner of products belonging to that category, sub-category or 3rd level category will be able to fill profile information fields for that profile type in their products. With this mapping, you will also be able to associate a profile type with multiple categories / sub-categories / 3rd level categories.<br /><br />For information on product profile types, profile fields and to create new profile types or profile fields, please visit the 'Profile Fields' section. An example use case of this feature would be associating category books with profile type having profile fields related to books and so on.<br /><br /><b>Note:</b> If you map a Category, then all its sub-categories and 3rd level categories will be automatically mapped with the same Product Profile Type. If you want to map different Product Profile Types for sub-categories and 3rd level categories, then you can anytime remove the mapping from Category and add new mapping for sub-categories and 3rd level categories. Same will happen when you map a sub-category, i.e. all its 3rd level categories will be automatically mapped with the same Product Profile Type.") ?>
        </p>
        
        <?php if(count($this->categories) > 0):?>
					<table class='admin_table sr_sitestoreproduct_mapping_table' width="100%">
						<thead>
							<tr>
								<th>
									<div class="sr_sitestoreproduct_mapping_table_name fleft"><b class="bold"><?php echo $this->translate("Category Name") ?></b></div>
									<div class="sr_sitestoreproduct_mapping_table_value fleft"><b class="bold"><?php echo $this->translate("Associated Profile") ?></b></div>
									<div class="sr_sitestoreproduct_mapping_table_option fleft"><b class="bold"><?php echo $this->translate("Mapping") ?></b></div>
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
                      <ul>
                        <li><?php echo $this->translate($category['cat_profile_type_label']); ?></li>
                      </ul>
                    </div>

                    <div class="sr_sitestoreproduct_mapping_table_option fleft">
                      <?php if(empty($category['cat_profile_type_id'])):?>
                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'profilemaps', 'action' => 'map', 'category_id' => $category['category_id']), $this->translate('Add'), array(
                          'class' => 'smoothbox',
                        )) ?>
                      <?php else: ?>
                      
                        <?php if($this->totalProfileTypes > 1): ?>
                          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'profilemaps', 'action' => 'edit', 'category_id' => $category['category_id'], 'profile_type' => $category['cat_profile_type_id']), $this->translate('Edit'), array('class' => 'smoothbox')) ?> | 
                        <?php endif; ?>   
                      
                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'profilemaps', 'action' => 'remove', 'category_id' => $category['category_id']), $this->translate('Remove'), array(
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
                        <ul>
                          <li><?php echo $this->translate($subcategory['subcat_profile_type_label']); ?></li>
                        </ul>
                      </div>  
                      <div class="sr_sitestoreproduct_mapping_table_option fleft">
                        <?php if(!empty($category['cat_profile_type_id'])):?>
                          <span title='<?php echo $this->translate("You can not map this sub-category as its parent category is already mapped to a profile type and sub-category inherits mapping from its parent category. If you want to map this sub-category then please remove parent category mapping first.");?>'><?php echo $this->translate('Add'); ?></span>
                        <?php elseif(empty($subcategory['subcat_profile_type_id'])):?>
                          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'profilemaps', 'action' => 'map', 'category_id' => $subcategory['sub_cat_id']), $this->translate('Add'), array(
                            'class' => 'smoothbox',
                          )) ?>
                        <?php else: ?>
                          <?php if($this->totalProfileTypes > 1): ?>
                            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'profilemaps', 'action' => 'edit', 'category_id' => $subcategory['sub_cat_id'], 'profile_type' => $subcategory['subcat_profile_type_id']), $this->translate('Edit'), array(
                            'class' => 'smoothbox',
                          )) ?> | 
                          <?php endif; ?>                                    
                          
                          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'profilemaps', 'action' => 'remove', 'category_id' => $subcategory['sub_cat_id']), $this->translate('Remove'), array(
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
	                        <ul>
	                          <li><?php echo $this->translate($subsubcategory['tree_profile_type_label']); ?></li>
	                        </ul>
                      	</div>
                      	<div class="sr_sitestoreproduct_mapping_table_option fleft">
	                        <?php if(!empty($subcategory['subcat_profile_type_id']) || !empty($category['cat_profile_type_id'])):?>
	                          <span title='<?php echo $this->translate("You can not map this 3rd level category as its parent category / sub-category is already mapped to a profile type and 3rd level category inherits mapping from its parent category / sub-category. If you want to map this 3rd level category then please remove parent category / sub-category mapping first.");?>'><?php echo $this->translate('Add'); ?></span>
	                        <?php elseif(empty($subsubcategory['tree_profile_type_id'])):?>
	                          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'profilemaps', 'action' => 'map', 'category_id' => $subsubcategory['tree_sub_cat_id']), $this->translate('Add'), array(
	                            'class' => 'smoothbox',
	                          )) ?>
	                        <?php else: ?>
	                          
	                          <?php if($this->totalProfileTypes > 1): ?>
	                            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'profilemaps', 'action' => 'edit', 'category_id' => $subsubcategory['tree_sub_cat_id'], 'profile_type' => $subsubcategory['tree_profile_type_id']), $this->translate('Edit'), array(
	                            'class' => 'smoothbox',
	                          )) ?> | 
	                          <?php endif; ?>        
	                          
	                          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'profilemaps', 'action' => 'remove', 'category_id' => $subsubcategory['tree_sub_cat_id']), $this->translate('Remove'), array(
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