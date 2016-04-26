<?php if( !$this->video ): ?>
<?php echo $this->translate('The video you are looking for does not exist or has not been processed yet.') ?>
<?php return ?>
<?php endif; ?>
<h4>
    <?php echo $this->translate("HTML Code"); ?>
</h4>
<br />
<textarea cols="50" style="width: 100%" rows="4"><?php echo trim($this->embedCode);?></textarea>
<br />
<br />
<div>
    <a href="javascript:void(0);" onclick="parent.Smoothbox.close();">
        <button><?php echo $this->translate('Close') ?></button>
    </a>
</div>
