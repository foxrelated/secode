<ul class="ynvideochannel_playlists_grid">
    <?php foreach($this->playlists as $item):?>
        <li>
            <?php $photo_url = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_playlist_thumb_icon.png'; ?>
            <div><img width="200px" src="<?php echo $photo_url?>"/></div>
            <div>
                <?php echo $this->partial('_playlist_options.tpl', 'ynvideochannel', array('playlist' => $item)); ?>
            </div>
            <?php echo $this->translate(array('%1$s video', '%1$s videos', $item->video_count), $this->locale()->toNumber($item->video_count)) ?>
            <div><?php echo $item ?></div>
            <?php echo $this->translate('by %1$s &middot; %2$s',
                $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()),
                $this->locale()->todateTime(strtotime($item->creation_date), array('type' => 'date'))) ?>
            <ul>
                <?php foreach($item->getVideos(2) as $video): ?>
                    <li>
                        <i class="fa fa-angle-right"></i>&nbsp;<?php echo $this->htmlLink($video->getHref(), $video->getTitle()) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php endforeach;?>
</ul>
