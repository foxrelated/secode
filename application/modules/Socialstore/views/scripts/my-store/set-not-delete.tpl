<br />
<div class ="tip" style = "margin-left: 10px">
<span>
<?php echo $this->translate('This set has been used. You cannot delete it!') ?>
</span>
</div>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
