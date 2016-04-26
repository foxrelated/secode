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

  var tagAction = function(tag_id, tag){
    $('tag_id').value = tag_id;
    $('tag').value = tag;
    $('filter_form').submit();
  }

	var categoryAction =function(category,sub, categoryname) {      
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

<div class='profile_fields'>
	<h4>
		<span><?php if(!empty($this->list_description)){ echo$this->translate('Listing Information'); } else { exit(); }  ?></span>
	</h4>
	<ul>
		<li>
			<span><?php echo $this->translate('Posted By:'); ?> </span>
			<span><?php echo $this->htmlLink($this->list->getParent(), $this->list->getParent()->getTitle()) ?></span>
		</li>
		<li>
			<span><?php echo $this->translate('Posted:'); ?></span>
			<span><?php echo $this->timestamp(strtotime($this->list->creation_date)) ?></span>
		</li>
		<li>
			<span><?php echo $this->translate('Last Updated:'); ?></span>
			<span><?php echo $this->timestamp(strtotime($this->list->modified_date)) ?></span>
		</li>
		<?php if(!empty($this->list->comment_count)): ?>
			<li>
				<span><?php echo $this->translate('Comments:'); ?></span>
				<span><?php echo $this->translate( $this->list->comment_count) ?></span>
			</li>
		<?php endif; ?>
		<?php if(!empty($this->list->view_count)): ?>
			<li>
				<span><?php echo $this->translate('Views:'); ?></span>
				<span><?php echo $this->translate( $this->list->view_count) ?></span>
			</li>
		<?php endif; ?>
		<?php if(!empty($this->list->like_count)): ?>
			<li>
				<span><?php echo $this->translate('Likes:'); ?></span>
				<span><?php echo $this->translate( $this->list->like_count) ?></span>
			</li>
		<?php endif; ?>
		<form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'list', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>' style='display: none;'>
			<input type="hidden" id="tag" name="tag" value=""/>
			<input type="hidden" id="tag_id" name="tag_id" value=""/>
			<input type="hidden" id="category" name="category" value=""/>
			<input type="hidden" id="subcategory" name="subcategory" value=""/>
			<input type="hidden" id="categoryname" name="categoryname" value=""/>
			<input type="hidden" id="subcategoryname" name="subcategoryname" value=""/>	      
			<input type="hidden" id="start_date" name="start_date" value="<?php if ($this->start_date) echo $this->start_date; ?>"/>
			<input type="hidden" id="end_date" name="end_date" value="<?php if ($this->end_date) echo $this->end_date; ?>"/>
		</form>
	  <li class="mtop_5">
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
		<li>
			<?php if (count($this->listTags) >0): $tagCount=0;?>
				<span><?php echo $this->translate('Tags:'); ?></span>
				<span>
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
				</span>
			<?php endif; ?>
		</li>
		<?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.locationfield', 1); ?>
		<?php if($this->list->location && $enableLocation):?>
			<li>
				<span><?php echo $this->translate('Location:'); ?></span>
				<span><?php echo $this->list->location ?>&nbsp;&nbsp;&nbsp;
					<?php //echo $this->htmlLink('https://maps.google.com/?daddr='.urlencode($this->list->location), $this->translate('Get Direction'), array('target' => 'blank')); ?>
				</span>
			</li>
		<?php endif; ?>
    
    <?php if($this->expiry_setting == 1 && !empty($this->list->end_date)):?>
    <li>
      <?php $current_date = date("Y-m-d i:s:m", time());?>
      <span><?php echo $this->translate('End Date:'); ?></span>
      <?php if($this->list->end_date >= $current_date ):?>
				<span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->list->end_date))) ?></span>
      <?php else:?>
        <span><?php echo $this->translate('Expired') ?></span>
      <?php endif;?>
    </li>
    <?php endif;?>
	  <li>
			<span><?php echo $this->translate('Description:'); ?></span>
			<span><?php echo $this->translate(''); ?> <?php echo $this->list->body ?></span>
	  </li>
	</ul>
  <?php $str = $this->fieldValueLoop($this->list, $this->fieldStructure);?>
	<?php if(!empty($str)): ?>
		<h4>
			<span><?php echo$this->translate('Profile Information') ?></span>
		</h4>
		<?php echo Engine_Api::_()->list()->removeMapLink($this->fieldValueLoop($this->list, $this->fieldStructure)) ?>
	<?php endif; ?>
	<br />
	<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('list.checkcomment.widgets', 1)):?>
		<?php 
        include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listComment.tpl';
    ?>
	<?php endif; ?>
</div>
