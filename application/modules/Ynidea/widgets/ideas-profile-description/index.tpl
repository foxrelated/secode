<?php echo wordwrap($this->idea->body, 105, "\n", true)?>
<div style="margin-top:10px;">
  <?php echo $this->action("list", "comment", "core", array("type"=>"ynidea_idea", "id"=>$this->idea->getIdentity())); ?>
  </div>