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

<script type="text/javascript">
  var tagAction =function(tag_id, tag){
    $('tag_id').value = tag_id;
    $('tag').value = tag;
    $('filter_form').submit();
  }

   var categoryAction =function(category,sub, categoryname){      
   	  $('category').value = category;
	  	$('categoryname').value = categoryname;
	    $('subcategory').value = 0;
	    $('subcategoryname').value = '';
	    $('filter_form').submit();
	  }

 	  var subcategoryAction = function(category,subcategory,categoryname,subcategoryname) {
	  	$('category').value = category;
	  	$('categoryname').value = categoryname;
	    $('subcategory').value = subcategory;
	    $('subcategoryname').value = subcategoryname;
	    $('filter_form').submit();	
	  }

 	  var subsubcategoryAction = function(category,subcategory,subsubcategory,categoryname,subcategoryname,subsubcategoryname) {
	  	$('category').value = category;
	  	$('categoryname').value = categoryname;
	    $('subcategory').value = subcategory;
	    $('subcategoryname').value = subcategoryname;
	    $('subsubcategory').value = subsubcategory;
	    $('subsubcategoryname').value = subsubcategoryname;
	    $('filter_form').submit();	
	  }
</script>
<ul class="list_sidebar_info seaocore_sidebar_list">
	<li>
		<?php if(!empty($this->owner_photo)):?>
		<?php echo $this->htmlLink($this->list->getParent(), $this->itemPhoto($this->list->getParent(), 'thumb.icon', '' , array('align' => 'center'))) ?>
		<?php endif ;?>
		<?php echo $this->htmlLink($this->list->getParent(), $this->list->getParent()->getTitle()) ?>  (Owner)
	</li>
	<form id='filter_form' class='global_form_box' method='get' action='<?php echo $this->url(array('module' => 'list', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>' style='display: none;'>
		<input type="hidden" id="tag" name="tag" value=""/>
		<input type="hidden" id="tag_id" name="tag_id" value=""/>
		<input type="hidden" id="category" name="category" value=""/>
		<input type="hidden" id="subcategory" name="subcategory" value=""/>
		<input type="hidden" id="categoryname" name="categoryname" value=""/>	
		<input type="hidden" id="subcategoryname" name="subcategoryname" value=""/>			
		<input type="hidden" id="start_date" name="start_date" value="<?php if ($this->start_date)
		echo $this->start_date; ?>"/>
		<input type="hidden" id="end_date" name="end_date" value="<?php if ($this->end_date)
		echo $this->end_date; ?>"/>
	</form>
	<li>
		<?php if($this->category_name != ''): ?> 
			<span><?php echo $this->translate('Category:'); ?></span>
			<span>
				<a href='javascript:void(0);' onclick="javascript:categoryAction('<?php echo $this->list->category_id?>',0, '<?php echo $this->tableCategory->getCategorySlug($this->translate($this->category_name)) ?>',0);"><?php echo $this->translate($this->category_name); ?></a>
				<?php if(!empty($this->subcategory_name)): echo ' &raquo;'; ?>
					<a href='javascript:void(0);' onclick="javascript:subcategoryAction('<?php echo $this->list->category_id?>','<?php echo $this->list->subcategory_id?>','<?php echo $this->tableCategory->getCategorySlug($this->translate($this->category_name)) ?>','<?php echo $this->tableCategory->getCategorySlug($this->translate($this->subcategory_name)) ?>');"><?php echo $this->translate($this->subcategory_name);?></a>
					<?php if(!empty($this->subsubcategory_name)): echo ' &raquo;'; ?>
						<a href='javascript:void(0);' onclick="javascript:subsubcategoryAction('<?php echo $this->list->category_id?>','<?php echo $this->list->subcategory_id?>','<?php echo $this->list->subsubcategory_id?>','<?php echo $this->tableCategory->getCategorySlug($this->translate($this->category_name)) ?>','<?php echo $this->tableCategory->getCategorySlug($this->translate($this->subcategory_name)) ?>','<?php echo $this->tableCategory->getCategorySlug($this->translate($this->subsubcategory_name)) ?>');"><?php echo $this->translate($this->subsubcategory_name);?></a>
					<?php endif; ?>
				<?php endif; ?>
			</span>
		<?php endif; ?>
	</li>
	<?php if  (count($this->listTags) >0): $tagCount=0;?>
		<li>
			<?php echo $this->translate('Tags:'); ?>
			<?php foreach ($this->listTags as $tag): ?>
				<?php if (!empty($tag->getTag()->text)):?>
					<?php $tag->getTag()->text = $this->string()->escapeJavascript($tag->getTag()->text)?>
					<?php if(empty($tagCount)):?>
					<a href='javascript:void(0);' onclick="javascript:tagAction('<?php echo $tag->getTag()->tag_id; ?>', '<?php echo $tag->getTag()->text; ?>');">#<?php echo $tag->getTag()->text?></a>
						<?php $tagCount++; else: ?>
					<a href='javascript:void(0);' onclick="javascript:tagAction('<?php echo $tag->getTag()->tag_id; ?>', '<?php echo $tag->getTag()->text; ?>');">#<?php echo $tag->getTag()->text?></a>
					<?php endif; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</li>
	<?php endif; ?>
	<li>
		<ul>
			<li>
				<?php echo $this->translate('Last updated %s', $this->timestamp($this->list->modified_date)) ?>
			</li>        
			<?php if(!empty($this->list->view_count)): ?>
				<li>
					<?php echo $this->translate( $this->list->view_count) ?>  <?php echo $this->translate('Total views')?>
				</li>
			<?php endif; ?>
		</ul>
	</li>
</ul>