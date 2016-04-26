<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<div id="image_view" class="siteevent_cat_gd_wrap clr">
    <ul class="siteevent_cat_gd">
        <?php foreach ($this->categoryParams as $category): ?>  
            <li class="seao_cat_gd_col fleft o_hidden g_b <?php if (!empty($category['subCategories'])): ?>seao_cat_gd_col_links_wrap<?php endif ?>" style="height: <?php echo $this->columnHeight; ?>px; width: <?php echo $this->columnWidth; ?>px;">
                <div class="seao_cat_gd_cnt">

                    <?php
                    $url = $this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $category['category_id'])->getCategorySlug()), "" . $this->categoryRouteName . "");
                    if (!empty($this->category_id) && !empty($category['category_id'])) {
                        $url = $this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $this->category_id)->getCategorySlug(), 'subcategory_id' => $category['category_id'], 'subcategoryname' => Engine_Api::_()->getItem('siteevent_category', $category['category_id'])->getCategorySlug()), "siteevent_general_subcategory");
                    }
                    ?>            

                    <?php if (!empty($category['photo_id'])): ?>
                        <?php
                        $temStorage = $this->storage->get($category['photo_id'], '');
                        if (!empty($temStorage)):
                            ?>
                            <a href="<?php echo $url; ?>" class="dblock seao_cat_gd_img" style="background-image: url(<?php echo $temStorage->getPhotoUrl(); ?>);"></a> 
                        <?php endif;
                    else:
                        ?>
                        <a href="<?php echo $url; ?>" class="dblock seao_cat_gd_img" style="background-image: url('<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/nophoto_event_caregory.png');"></a> 
                        <?php endif; ?>
                </div> 
                <div class="seao_cat_gd_title">
                    <?php echo $this->htmlLink($url, $this->translate($category['title'])); ?>
                </div> 

                <?php if (!empty($category['subCategories'])): ?>
                    <div class='seao_cat_gd_col_links'>
                        <?php
                        $subCategoriesShopNowLink = null;
                        foreach ($category['subCategories'] as $subCategory):
                            $getUrl = $this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $category['category_id'])->getCategorySlug(), 'subcategory_id' => $subCategory['sub_category_id'], 'subcategoryname' => Engine_Api::_()->getItem('siteevent_category', $subCategory['sub_category_id'])->getCategorySlug()), "siteevent_general_subcategory");
                            if (!empty($subCategory['subcat_dependency'])) {
                                $getUrl = $this->url(array('category_id' => $subCategory['root_category_id'], 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $subCategory['root_category_id'])->getCategorySlug(), 'subcategory_id' => $category['category_id'], 'subcategoryname' => Engine_Api::_()->getItem('siteevent_category', $category['category_id'])->getCategorySlug(), 'subsubcategory_id' => $subCategory['sub_category_id'], 'subsubcategoryname' => Engine_Api::_()->getItem('siteevent_category', $subCategory['sub_category_id'])->getCategorySlug()), "siteevent_general_subsubcategory");

                                if (empty($subCategoriesShopNowLink))
                                    $subCategoriesShopNowLink = $this->url(array('category_id' => $subCategory['root_category_id'], 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $subCategory['root_category_id'])->getCategorySlug(), 'subcategory_id' => $category['category_id'], 'subcategoryname' => Engine_Api::_()->getItem('siteevent_category', $category['category_id'])->getCategorySlug()), "siteevent_general_subcategory");
                            }
                            echo '<p class="fleft widthfull">' . $this->htmlLink($getUrl, $this->translate($subCategory['title']), array('class' => 'fleft'));
                            if (!empty($this->count)):
                                echo "<span class='fleft'>&nbsp;" . $this->translate("(%s)", $subCategory['count']). "</span>";
                            endif;
                            echo '</p>';
                        endforeach;
                        if (empty($this->category_id)):
                            echo '<p class="view-all">' . $this->htmlLink($this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $category['category_id'])->getCategorySlug()), "" . $this->categoryRouteName . ""), $this->translate("Explore &raquo;")) . '</p>';
                        elseif (!empty($subCategoriesShopNowLink)):
                            echo '<p class="view-all">' . $this->htmlLink($subCategoriesShopNowLink, $this->translate("Shop Now &raquo;")) . '</p>';
                        endif;
                        ?>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="clear">
</div>