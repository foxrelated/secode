<?php
    $item = $this -> item;
    $photoUrl = $item ->getPhotoUrl('thumb.profile');
    if (!$photoUrl)  $photoUrl = $this->baseUrl().'/application/modules/Ynvideochannel/externals/images/nophoto_video_thumb_normal.png';
?>

<div class="ynvideochannel_feed_item">
    <div class="ynvideochannel_img">
        <img src="<?php echo $photoUrl; ?>" alt="">

        <div class="ynvideochannel_duration_btn">
            <?php echo $this->partial('_video_duration.tpl', 'ynvideochannel', array('video' => $item)) ?>
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