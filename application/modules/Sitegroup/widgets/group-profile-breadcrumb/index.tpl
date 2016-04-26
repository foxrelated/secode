<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="sitegroup_profile_breadcrumb">
  <?php 
      $temp_general_url = $this->url(array(),'sitegroup_general', false );
    
      if($this->category_name):
        $temp_general_category = $this->url(array('category_id' => $this->sitegroup->category_id, 'categoryname' => Engine_Api::_()->getItem('sitegroup_category', $this->sitegroup->category_id)->getCategorySlug()), "sitegroup_general_category");
      endif;
      
      if(!empty($this->subcategory_name)):
        $temp_general_subcategory = $this->url(array('category_id' => $this->sitegroup->category_id, 'categoryname' => Engine_Api::_()->getItem('sitegroup_category', $this->sitegroup->category_id)->getCategorySlug(), 'subcategory_id' => $this->sitegroup->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitegroup_category', $this->sitegroup->subcategory_id)->getCategorySlug()), "sitegroup_general_subcategory");
      endif;
      
      if(!empty($this->subsubcategory_name)):
        $temp_general_subsubcategory = $this->url(array('category_id' => $this->sitegroup->category_id, 'categoryname' => Engine_Api::_()->getItem('sitegroup_category', $this->sitegroup->category_id)->getCategorySlug(), 'subcategory_id' => $this->sitegroup->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitegroup_category', $this->sitegroup->subcategory_id)->getCategorySlug(), 'subsubcategory_id' => $this->sitegroup->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getItem('sitegroup_category', $this->sitegroup->subsubcategory_id)->getCategorySlug()), "sitegroup_general_subsubcategory");
      endif;
  ?>
  <a href="<?php echo $temp_general_url;?>">
    <?php echo $this->translate("Groups Home");?>
  </a>
  <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
  <?php if ($this->category_name && $temp_general_category): ?>
    <a href="<?php echo $temp_general_category; ?>"><?php echo $this->translate($this->category_name); ?></a>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <?php if (!empty($this->subcategory_name) && $temp_general_subcategory): ?>
      <a href="<?php echo $temp_general_subcategory; ?>"><?php echo $this->translate($this->subcategory_name); ?></a>
      <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
      <?php if (!empty($this->subsubcategory_name) && $temp_general_subsubcategory):?>
        <a href="<?php echo $temp_general_subsubcategory; ?>"><?php echo $this->translate($this->subsubcategory_name); ?></a>
        <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
  <?php echo $this->sitegroup->getTitle(); ?>
</div>

<style type="text/css">

.sitegroup_profile_breadcrumb{
  font-size:11px;
  margin-bottom:10px;
}
.sitegroup_profile_breadcrumb .brd-sep{
  margin:0 3px;
}

</style>