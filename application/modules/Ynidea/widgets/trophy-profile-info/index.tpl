<div style="float: left">
<h3>
	<?php echo $this->trophy->getTitle() ?>
</h3>
</div>
<div id="trophy_status" style="font-weight: bold; text-transform: capitalize; float: right; padding-right: 30px">
	<?php
			  echo $this->translate($this->trophy->status);
		?>
</div>
<div style="clear: both">
	<?php echo $this->timestamp($this->trophy->creation_date)." | ";
		  echo $this->translate("Nominees: %s", $this->trophy->getNominees())." | ";
		  echo $this->translate("Judges: %s", $this->trophy->getJudges());
	?> 
</div>
<div style="padding-top: 10px;">
	<h3>
		<?php echo $this->translate("Description");?>
	</h3>
	<div class="yntinymce" style="margin-bottom: 15px;">
	<?php echo $this->trophy->description;?>
	</div>
</div>
<div style="margin-top:10px;">
  <?php echo $this->action("list", "comment", "core", array("type"=>"ynidea_trophy", "id"=>$this->trophy->getIdentity())); ?>
</div>