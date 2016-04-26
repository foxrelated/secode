<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<!-- assoc tips -->
<div class="tip">
  <span>
    <?php echo $this->translate('You do not have any store.');?>
      <?php echo $this->translate('Get started by %1$screating%2$s a store!', '<a href="'.$this->url(array('action' => 'create-store')).'">', '</a>'); ?>
  </span>
</div>