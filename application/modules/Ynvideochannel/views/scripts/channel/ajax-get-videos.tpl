<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <?php foreach ($this->paginator as $video):?>
        <li>
            <?php $photo_url = ($video->getPhotoUrl('thumb.normal')) ? $video->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_video_thumb_normal.png'; ?>
            <div><img width="200px" src="<?php echo $photo_url?>"/></div>
            <?php echo $this->partial('_video_duration.tpl', 'ynvideochannel', array('video' => $video)); ?>
            <div><a href="<?php echo $video -> getHref()?>"><?php echo $video -> getTitle()?></a></div>
            <div><?php echo $this -> translate("by %s", $video -> getOwner())?></div>
            <?php echo $this->timestamp(strtotime($video->creation_date)) ?>
            <div><?php echo $this -> translate(array("%s view", "%s views", $video -> view_count), $video -> view_count)?></div>
            <?php echo $this->partial('_video_rating.tpl', 'ynvideochannel', array('rating' => $video->rating)); ?>
            <?php echo $this->partial('_video_options.tpl', 'ynvideochannel', array('video' => $video)); ?>
        </li>
    <?php endforeach;?>
    <?php echo $this->paginationControl($this->paginator, null, array(0 => '_pagination.tpl', 1 => 'ynvideochannel'),null); ?>
<?php endif;?>



