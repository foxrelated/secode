<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<!-- assoc tips -->
<div class="tip">
  <span>
    <?php echo $this->translate('You already created a store %1$shere%2$s. You cannot create anymore.','<a href="'.$this->url(array('action' => 'index')).'">', '</a>');?>
  </span>
</div>