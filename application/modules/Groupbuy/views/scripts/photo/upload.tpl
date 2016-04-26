<?php
if($this->canUpload):
 ?>
<h2><?php echo $this->translate('Deal Listing Photos');?></h2>
<?php echo $this->form->render($this) ?>
<?php  else: ?>
<div class="tip" style="clear: inherit;">
      <span>
<?php  echo $this->translate('You can not upload photos!');?>
 </span>
           <div style="clear: both;"></div>
    </div>
<?php endif; ?>