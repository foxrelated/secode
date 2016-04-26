<?php
    $item = $this -> item;
    $photoUrl = $item ->getPhotoUrl('thumb.profile');
    if (!$photoUrl) {
        $photoUrl = $item->getLastVideoPhoto();
    }
    if (!$photoUrl) {
        $photoUrl = $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Ynvideochannel/externals/images/nophoto_playlist_thumb_icon.png';
    }
    $count = $item->getVideoCount();
?>

<div class="ynvideochannel_feed_item">
    <div class="ynvideochannel_img">
        <img src="<?php echo $photoUrl; ?>" alt="">

        <div class="ynvideochannel_duration_btn">
            <span class="ynvideochannel_feed_count_video"><?php echo $this->translate(array('<b>%1$s</b> video','<b>%1$s</b> videos', $count), $this->locale()->toNumber($count)); ?></span> &nbsp;&nbsp;
            <a href="<?php echo $item->getHref(); ?>" class="ynvideochannel_btn_play_feed" ><i class="fa fa-play"></i></a>
        </div>
    </div>

    <div class="ynvideochannel_info">
        <div class="ynvideochannel_title">
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => '', 'title' => $item->getTitle())); ?>
        </div>
        
        <div class="ynvideochannel_description">
            <?php echo $this -> string() -> truncate($item->description, 500); ?>
        </div>
    </div>
</div>