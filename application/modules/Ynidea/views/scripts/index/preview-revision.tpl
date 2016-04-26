 <?php 
$menu = $this->partial('_menu.tpl', array());  
echo $menu;
?>
<h3>
	<?php echo $this->revision->title; ?>
</h3>
<div>
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
			  echo $this->timestamp($this->revision->creation_date);
		?>
	</div>
	<div style="padding-left: 400px">
		<?php echo $this->translate("Version")." ";
			  echo $this->revision->idea_version." | ";
			  echo $this->timestamp($this->revision->modified_date);?>
	</div>
</div>
<div style="padding-top: 5px; margin-bottom: 15px">
	<?php echo $this->translate("Cost").": ";
		  echo $this->revision->cost." | ";
		  echo $this->translate("Feasibility").": ";
		  switch ($this->revision->feasibility) 
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
		  echo $this->revision->reproducible?$this->translate("Yes"):$this->translate("No"); 
	?>
</div>
<h3><?php echo $this->translate("Summary")?></h3>
<div class="ynidea_content">  
<?php echo $this->revision->description;?>
</div>

<h3><?php echo $this->translate("Description")?></h3>
<div class="ynidea_content">  
<?php echo $this->revision->body;?>
</div>

<h4 style="border: none;">  
<?php echo $this->htmlLink(array(
                  'action' => 'history',
                  'id' => $this->idea->getIdentity(),
                  'route' => 'ynidea_specific',
                  'reset' => true,
                ), "&laquo; ".$this->translate('View Idea History'), array(
                )) ?>
</h4>
