<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    ynvideochannel
 * @author     YouNet Company
 */
?>
<?php
    $video = $this->video;
    $videoId = $video->getIdentity();
    $photoUrl = $video ->getPhotoUrl();
    if (!$photoUrl) $photoUrl = $this->baseUrl().'/application/modules/ynvideochannel/externals/images/nophoto_video_thumb_normal.png';
?>

<div class="ynvideochannel_playlist_detail_item ms-slide">
    <img src="<?php echo $this->baseUrl().'/application/modules/Ynvideochannel/externals/scripts/masterslider/blank.gif'?>" data-src="<?php echo $photoUrl ?>" alt="lorem ipsum dolor sit"/>
    <?php echo $video->getPlayerDOM(); ?>

    <div class="ynvideochannel_playlist_detail_infomation ms-thumb">
        <img src="<?php echo $photoUrl ?>" alt="">

        <div class="ynvideochannel_playlist_detail_infomation_detail">
            <div class="ynvideochannel_title">
                <?php echo $video->getTitle(); ?>
            </div>

            <div class="ynvideochannel_owner">
                 <?php echo $video->getOwner()->getTitle(); ?>
            </div>
            
            <div class="ynvideochannel_duration">
                 <?php echo $video->duration; ?>
            </div>
        </div>
    </div>

    <input type="hidden" class="video_id" value="<?php echo $videoId; ?>"/>
    <input type="hidden" class="title" value="<?php echo $video->getTitle(); ?>"/>
    <input type="hidden" class="href" value="<?php echo $video->getHref(); ?>"/>

</div>