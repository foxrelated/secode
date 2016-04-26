<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>
<?php 
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
  	              . 'application/modules/Seaocore/externals/styles/styles.css');

include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<script type="text/javascript">

	function showGroupPhoto(ImagePath, category_id, group_id) {
    var elem = document.getElementById('group_elements_'+category_id).getElementsByTagName('a'); 
    for(var i = 0; i < elem.length; i++)
    { 
			var cat_groupid = elem[i].id;
			$(cat_groupid).erase('class');
		}
    $('group_link_class_'+group_id).set('class', 'active');
    
		$('groupImage_'+category_id).src = ImagePath;
	}

  var categoryAction =function(category,sub) 
  { if($("tag"))
      $("tag").value='';
    $('category').value = category;
    $('subcategory').value = sub;
    if($('filter_form')) {
	    $('filter_form').submit();
    } else {
    	$('filter_form_category').submit();
    }
  }
  var subcategoryAction = function(category,subcategory) 
  { if($("tag"))
      $("tag").value='';
    $('category').value = category;
    $('subcategory').value = subcategory;
    if($('filter_form')) {
	    $('filter_form').submit();
    } else {
    	$('filter_form_category').submit();
    }
  }
</script>

<ul class="seaocore_categories_box">
  <li> 
    <?php $ceil_count = 0; $k = 0; ?>
    <?php for ($i = 0; $i <= count($this->categories); $i++) { ?>
			<?php if($ceil_count == 0) :?>      
				<div>      
			<?php endif;?>  
          <div class="seaocore_categories_list_row" style="width: <?php echo (round(100/$this->columnCount)-1) ?>%" >
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
					<?php $total_subcat = Count($category['category_groups']); ?>
					<h6>
						<?php echo $this->htmlLink($this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($category['category_name'])), 'sitegroup_general_category'), $this->translate($category['category_name'])) ?>
					</h6>	
					<div class="sub_cat" id="subcat_<?php echo $category['category_id'] ?>">

						<?php $total_count = 1; ?>
		
						<?php foreach ($category['category_groups'] as $categoryGroups) : ?>

							<?php 
								$imageSrc = $categoryGroups['imageSrc']; 
								if(empty($imageSrc)) {
									$imageSrc = $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/nophoto_sitegroup_thumb_icon.png';
								}

								$category_id = $category['category_id'];
								$group_id = $categoryGroups['group_id'];
							?>
							<?php if($total_count == 1): ?>
								<div class="seaocore_categories_img" >
									<img src="<?php echo $imageSrc; ?>" id="groupImage_<?php echo $category['category_id'] ?>" alt="" class="thumb_icon" />
								</div>
								<div id='group_elements_<?php echo $category_id;?>'>
								<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($categoryGroups['group_id'], $categoryGroups['owner_id'], $categoryGroups['slug']), Engine_Api::_()->sitegroup()->truncation($categoryGroups['group_title'], 25)." (".$categoryGroups['populirityCount'].")", array('onmouseover' => "javascript:showGroupPhoto('$imageSrc', '$category_id', '$group_id');",'title' => $categoryGroups['group_title'], 'class'=>'active', 'id'=>"group_link_class_$group_id"));?>
							<?php else: ?>
								<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($categoryGroups['group_id'], $categoryGroups['owner_id'], $categoryGroups['slug']), Engine_Api::_()->sitegroup()->truncation($categoryGroups['group_title'], 25)." (".$categoryGroups['populirityCount'].")", array('onmouseover' => "javascript:showGroupPhoto('$imageSrc', '$category_id', '$group_id');",'title' => $categoryGroups['group_title'], 'id'=>"group_link_class_$group_id"));?>
							<?php endif; ?>

							<?php $total_count++; ?>
            <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
     <?php if($ceil_count % $this->columnCount == 0) :?>      
     </div>
     <?php $ceil_count=0; ?>
     <?php endif;?>
    <?php } ?> 
  </li>	
</ul>