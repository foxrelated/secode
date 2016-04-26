<div style="float: left">
<h3>
	<?php echo $this->idea->getTitle() ?>
	<?php if($this->idea->publish_status != 'publish'): ?>
	<span id="idea_status" style="text-transform: capitalize; color: red; font-weight: bold; font-size: 10pt">
	<?php	echo " - ".$this->translate('Not published');?>
	</span>
	<?php endif;?>
</h3>
</div>

<br/>
<div class="idea_category">
    <span><?php echo $this->translate('Category: ')?></span>
    <?php $i = 0;  
    $category = Engine_Api::_()->getItem('ynidea_category', $this->idea->category_id)  ?>
    <?php if($category) :?>
		<?php foreach($category->getBreadCrumNode() as $node): ?>
			<?php if($node -> category_id != 1) :?>
			<?php if($i != 0) :?>
				&raquo;	
			<?php endif;?>
    			<?php $i++; echo $this->htmlLink($node->getHref(), $this->translate($node->shortTitle()), array()) ?>
    		<?php endif; ?>
 	 <?php endforeach; ?>
 	 <?php if($category -> parent_id != 0 && $category -> parent_id  != 1) :?>
				&raquo;	
	 <?php endif;?>
 	 <?php echo $this->htmlLink($category->getHref(), $category->title); ?>
 	 <?php else:?>
 	 <?php echo $this->translate('None')?>
 	 <?php endif;?>
 	 
</div>
<div style="padding-top: 5px;">
	<?php echo $this->translate("Tags: ");
	if(count($this->tags) > 0):
		foreach($this->tags as $tag): ?>
			<a  href='javascript:void(0);'onclick='javascript:tagAction(<?php echo $tag->tag_id; ?>);' ><?php echo $tag->text?></a> 
		<?php endforeach;
	else:
	echo $this->translate("None");
		endif; ?> 
</div>
<div style="padding-top: 5px;">
	<div style="float: left">
		<?php echo $this->translate("Created by")." ";
			  echo $this->idea->getOwner()." | ";
			  echo $this->timestamp($this->idea->creation_date);
		?>
	</div>
	<div style="padding-left: 400px">
		<?php echo $this->translate("Version")." ";
			  echo $this->idea->version." | ";
			  echo $this->timestamp($this->idea->version_date);?>
	</div>
</div>
<div style="padding-top: 5px; margin-bottom: 15px">
	<?php echo $this->translate("Cost").": ";
		  echo $this->idea->cost." | ";
		  echo $this->translate("Feasibility").": ";
		  switch ($this->idea->feasibility) 
		  {
			  case 0:
				  echo $this->translate('Easy');
				  break;
			  case 1:
				  echo $this->translate('Slightly Complex');
				  break;
			  case 2:
				  echo $this->translate('Complex');
				  break;
			  case 3:
				  echo $this->translate('Very Complex');
				  break;
			  default:
				  echo $this->translate('Easy');
				  break;
		  }
		  echo " | ";
		  echo $this->translate("Reproducible").": ";
		  echo $this->idea->reproducible?$this->translate("Yes"):$this->translate("No"); 
	?>
</div>

<h3>
	<?php echo $this->translate("Summary");?>
</h3>
<div style="margin-bottom: 15px;">
<?php echo wordwrap($this->idea->description, 105, "\n", true);?>
</div>
<script type="text/javascript">
  var tagAction =function(tag){
    window.location = en4.core.baseUrl + 'ideas/view-all?tag=' + tag;
  }
</script>