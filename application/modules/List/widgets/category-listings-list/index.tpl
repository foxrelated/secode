<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php 
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
  	              . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<script type="text/javascript">

	function showListingPhoto(ImagePath, category_id, listing_id) {
    var elem = document.getElementById('listing_elements_'+category_id).getElementsByTagName('a'); 
    for(var i = 0; i < elem.length; i++)
    { 
			var cat_listingid = elem[i].id;
			$(cat_listingid).erase('class');
		}
    $('listing_link_class_'+listing_id).set('class', 'active');
    
		$('listingImage_'+category_id).src = ImagePath;
	}

</script>

<ul class="seaocore_categories_box">
  <li> 
    <?php $ceil_count = 0; $k = 0; ?>
    <?php for ($i = 0; $i <= count($this->categories); $i++) { ?>
			<?php if($ceil_count == 0) :?>      
				<div>      
			<?php endif;?>  
			<div class="seaocore_categories_list_row">
				<?php $ceil_count++;?>				
				<?php $category = "";
					if (isset($this->categories[$k]) && !empty($this->categories[$k])): 
						$category = $this->categories[$k];
					endif;
					$k++;

					if (empty($category)) {
						break;
					}
				?>

				<div class="seaocore_categories_list">
					<?php $total_subcat = Count($category['category_listings']); ?>
					<h6>
						<?php echo $this->htmlLink($this->url(array('category' => $category['category_id'], 'categoryname' => Engine_Api::_()->getDbTable('categories', 'list')->getCategorySlug($category['category_name'])), 'list_general_category'), $this->translate($category['category_name'])) ?>
					</h6>	
					<div class="sub_cat" id="subcat_<?php echo $category['category_id'] ?>">

						<?php $total_count = 1; ?>
		
						<?php foreach ($category['category_listings'] as $categoryListings) : ?>

							<?php 
								$imageSrc = $categoryListings['imageSrc']; 
								if(empty($imageSrc)) {
									$imageSrc = $this->layout()->staticBaseUrl . 'application/modules/List/externals/images/nophoto_list_thumb_icon.png';
								}

								$category_id = $category['category_id'];
								$listing_id = $categoryListings['listing_id'];
							?>
							<?php if($total_count == 1): ?>
								<div class="seaocore_categories_img" >
									<img src="<?php echo $imageSrc; ?>" id="listingImage_<?php echo $category['category_id'] ?>" alt="" class="thumb_icon" />
								</div>
								<div id='listing_elements_<?php echo $category_id;?>'>
								<?php echo $this->htmlLink(Engine_Api::_()->list()->getHref($categoryListings['listing_id'], $categoryListings['owner_id'], $categoryListings['slug']), Engine_Api::_()->seaocore()->seaocoreTruncateText($categoryListings['listing_title'], 25)." (".$categoryListings['populirityCount'].")", array('onmouseover' => "javascript:showListingPhoto('$imageSrc', '$category_id', '$listing_id');",'title' => $categoryListings['listing_title'], 'class' => 'active', 'id' => "listing_link_class_$listing_id"));?>
							<?php else: ?>
								<?php echo $this->htmlLink(Engine_Api::_()->list()->getHref($categoryListings['listing_id'], $categoryListings['owner_id'], $categoryListings['slug']), Engine_Api::_()->seaocore()->seaocoreTruncateText($categoryListings['listing_title'], 25)." (".$categoryListings['populirityCount'].")", array('onmouseover' => "javascript:showListingPhoto('$imageSrc', '$category_id', '$listing_id');",'title' => $categoryListings['listing_title'], 'id' => "listing_link_class_$listing_id"));?>
							<?php endif; ?>

							<?php $total_count++; ?>
            <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
     <?php if($ceil_count %2 == 0) :?>      
     </div>
     <?php $ceil_count=0; ?>
     <?php endif;?>
    <?php } ?> 
  </li>	
</ul>