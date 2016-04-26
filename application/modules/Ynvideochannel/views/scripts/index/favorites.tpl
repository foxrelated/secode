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
    <li id="favorite_video_<?php echo $video->getIdentity()?>">
        <?php $photo_url = ($video->getPhotoUrl('thumb.normal')) ? $video->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_video_thumb_normal.png'; ?>
        <div><img width="200px" src="<?php echo $photo_url?>"/></div>
        <?php echo $this->partial('_video_duration.tpl', 'ynvideochannel', array('video' => $video)); ?>
        <div><a href="<?php echo $video -> getHref()?>"><?php echo $video -> getTitle()?></a></div>
        <div><?php echo $this -> translate("by %s", $video -> getOwner())?></div>
        <?php echo $this->timestamp(strtotime($video->creation_date)) ?>
        <div><?php echo $this -> translate(array("%s view", "%s views", $video -> view_count), $video -> view_count)?></div>
        <div><?php echo $this -> translate(array("%s like", "%s likes", $video -> like_count), $video -> like_count)?></div>
        <div><?php echo $this -> translate(array("%s comment", "%s comments", $video -> comment_count), $video -> comment_count)?></div>
        <div><?php echo $this -> translate(array("%s favorite", "%s favorites", $video -> favorite_count), $video -> favorite_count)?></div>
        <?php echo $this->partial('_video_rating.tpl', 'ynvideochannel', array('rating' => $video->rating)); ?>
        <div>
            <ul class="ynvideochannel_video_options-block">
                    <?php
                    echo $this->htmlLink(array(
                    'route' => 'ynvideochannel_video',
                    'action' => 'unfavorite',
                    'video_id' => $video->getIdentity(),
                    'format' => 'smoothbox'
                    ), '<i class="fa fa-trash"></i>'.$this->translate('Remove'), array('class' => 'smoothbox icon_ynvideochannel_unfavorite')); ?>
            </ul>
        </div>
        <?php echo $this->partial('_add_to_playlist.tpl','ynvideochannel', array('video' => $video)); ?>
    </li>
    <?php endforeach; ?>
</ul>
<?php
    echo $this->paginationControl($this->paginator, null, null, array(
'pageAsQuery' => true,
'query' => $this->formValues
));
else: ?>
<div class="tip">
    <span>
        <?php echo $this->translate('No videos found.'); ?>
    </span>
</div>
<?php endif; ?>

<script type="text/javascript">
    window.addEvent('domready', function() {
        $$('.ynvideochannel_main_manage').getParent().addClass('active');
    });
</script>


