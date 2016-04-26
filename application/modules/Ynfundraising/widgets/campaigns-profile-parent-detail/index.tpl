<div>
	<?php echo $this->htmlLink($this->parent->getHref(), $this->itemPhoto($this->parent, 'thumb.profile', $this->parent->getTitle()), array('title'=>$this->parent->getTitle())) ?>
	
	<?php echo $this->htmlLink($this->parent->getHref(),$this->parent->getTitle())?>	
	<p>
		<?php echo $this->translate("Creator: %s", $this->parent->getOwner())?>
	</p>
	<p>
		<?php echo $this->translate("Created date: %s", $this->timestamp($this->parent->creation_date))?>
	</p>	
</div>