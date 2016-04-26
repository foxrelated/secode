<?php
    $totalVideos = $this->paginator->getTotalItemCount();
?>
<div class="ynvideochannel_count_videos">
    <i class="fa fa-bookmark"></i>
    <?php echo $this -> translate(array("%s video", "%s videos", $totalVideos), $totalVideos)?>
</div>
<?php if ($totalVideos > 0) : ?>
<ul class="ynvideochannel_video_manage_items">
    <?php foreach ($this->paginator as $video) : ?>
    <?php echo $this->partial('_video_item.tpl', array('item' => $video, 'showAddto' => false));?>
    <?php endforeach; ?>
</ul>
<?php
    echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues
    ));
else: ?>
<div class="tip">
    <?php echo $this->translate('No videos found.'); ?>
</div>
<?php endif; ?>

<script type="text/javascript">
    window.addEvent('domready', function () {
        $$('.ynvideochannel_main_manage').getParent().addClass('active');
    });
</script>