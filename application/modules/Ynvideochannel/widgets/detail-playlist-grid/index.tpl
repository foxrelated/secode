<?php echo $this->partial('_videos_grid.tpl', 'ynvideochannel', array('videos' => $this->paginator)); ?>
<?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
)); ?>
