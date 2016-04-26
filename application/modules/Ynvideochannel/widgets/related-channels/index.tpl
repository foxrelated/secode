<ul class="ynvideochannel_related_channels">
    <?php foreach($this -> channels as $item):?>
    <li>
        <?php $photo_url = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_channel_thumb_normal.png'; ?>
        <div><img width="200px" src="<?php echo $photo_url?>"/></div>
        <div><a href="<?php echo $item -> getHref()?>"><?php echo $item -> getTitle()?></a></div>
        <div><?php echo $this -> translate(array("%s subscriber", "%s subscribers", $item -> subscriber_count), $item -> subscriber_count)?></div>
        <div><?php echo $this -> translate(array("%s video", "%s videos", $item -> video_count), $item -> video_count)?></div>
    </li>
    <?php endforeach;?>
</ul>
