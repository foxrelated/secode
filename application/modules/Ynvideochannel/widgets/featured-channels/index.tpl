<link rel="stylesheet" href="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynvideochannel/externals/styles/masterslider.css" />
<script src="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynvideochannel/externals/scripts/jquery-1.10.2.min.js"></script>
<script src="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynvideochannel/externals/scripts/jquery.easing.min.js"></script>
<script src="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynvideochannel/externals/scripts/masterslider.js"></script>


<div class="master-slider ms-skin-default ynvideochannel_channels_slideshow" id="ynvideochannel_featured_channel">
    <?php foreach($this -> paginator as $item):?>
    <div class="ms-slide ynvideochannel_channels_slideshow-item">
        <div class="ynvideochannel_channels_slideshow-options">
            <?php echo $this->partial('_channel_options.tpl', 'ynvideochannel', array('channel' => $item)); ?>
        </div>

        <?php $cover_url = ($item->getCoverUrl('thumb.main')) ? $item->getCoverUrl('thumb.main') : 'application/modules/Ynvideochannel/externals/images/nophoto_channel_cover.png'; ?>
        <div class="ynvideochannel_channels_slideshow-bg" style="background-image: url('<?php echo $cover_url?>')">
            <div class="ynvideochannel_channels_slideshow-bgopacity"></div>
            <?php $photo_url = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_channel_thumb_normal.png'; ?>
            <div class="ynvideochannel_channels_slideshow-thumb" style="background-image: url('<?php echo $photo_url?>')"></div>

            <div class='ynvideochannel_channels_slideshow-title'><a href="<?php echo $item -> getHref()?>"><?php echo $item -> getTitle()?></a></div>
            
            <div class="ynvideochannel_channels_slideshow-category-date-owner">
                <span class="ynvideochannel_channels_slideshow-category">
                    <?php if ($item->category_id)
                        echo $this->partial('_category_breadcrumb.tpl', 'ynvideochannel', array('item' => $item));
                    ?>
                </span>
                &nbsp;.&nbsp;
                <span class="ynvideochannel_channels_slideshow-date-owner">
                    <?php echo $this -> translate("%1s by %2s", $this->timestamp(strtotime($item->creation_date)), $item -> getOwner());?>
                </span>
            </div>
    
            <div class="ynvideochannel_channels_slideshow-count">
                <span><?php echo $this -> translate(array("%s subscriber", "%s subscribers", $item -> subscriber_count), $item -> subscriber_count)?></span>&nbsp;.&nbsp;
                <span><?php echo $this -> translate(array("%s video", "%s videos", $item -> video_count), $item -> video_count)?></span>&nbsp;.&nbsp;
                <span><?php echo $this -> translate(array("%s like", "%s likes", $item -> like_count), $item -> like_count)?></span>&nbsp;.&nbsp;
                <span><?php echo $this -> translate(array("%s comment", "%s comments", $item -> comment_count), $item -> comment_count)?></span>
            </div>
        </div>

        <?php
        $videos = $item -> getVideos(4);
        if(count($videos)):?>
        <ul class="ynvideochannel_channels_slideshow-videos clearfix">
            <?php foreach($videos as $video):?>
                <li class="ynvideochannel_channels_slideshow-video">
                    <?php $photo_url = ($video->getPhotoUrl('thumb.normal')) ? $video->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_video_thumb_normal.png'; ?>
                    <div class="ynvideochannel_channels_slideshow-video-bg" style="background-image: url('<?php echo $photo_url?>');">
                        <div class="ynvideochannel_channels_slideshow-video-duration ynvideochannel_videos_duration">
                            <?php echo $this->partial('_video_duration.tpl', 'ynvideochannel', array('video' => $video)); ?>
                            <a href="<?php echo $video -> getHref()?>"><i class="fa fa-play" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </li>
            <?php endforeach;?>
         <?php endif;?>
        </ul>
        
        <div class="ynvideochannel_channels_slideshow-description"><?php echo $this -> string() -> truncate($item -> description, 200);?></div>
        <?php $viewer = $this -> viewer();?>
        <div class="ynvideochannel_channels_slideshow-subscribe">
            <?php if(($viewer->getIdentity() != 0) && ($viewer->getIdentity() != $item->owner_id)):
                 echo $this->partial('_subscribe_channel.tpl', 'ynvideochannel', array('item' => $item, 'user_id' => $viewer->getIdentity()));
            endif;?>
        </div>

    </div>
    <?php endforeach;?>
</div>


<script type="text/javascript">
    jQuery.noConflict();
    var slider_channel = new MasterSlider();
 
    slider_channel.control("arrows", {autohide:false});
     
    slider_channel.setup("ynvideochannel_featured_channel", {
        width:1000,
        // height:10,
        space:0,
        layout:"fillwidth",
        autoHeight:true,
        loop:true
    });


</script>