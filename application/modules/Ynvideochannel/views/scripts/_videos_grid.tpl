<ul class="ynvideochannel_videos_grid clearfix">
    <?php foreach($this -> videos as $item):?>
        <li class="ynvideochannel_videos_grid-item">
                <?php $photo_url = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_video_thumb_normal.png'; ?>
            <div class="ynvideochannel_videos_grid-item-bg" style="background-image: url('<?php echo $photo_url?>')">


                <div class="ynvideochannel_videos_grid-item-options clearfix">
                    <?php echo $this->partial('_video_options.tpl', 'ynvideochannel', array('video' => $item)); ?>
                    <?php echo $this->partial('_add_to_menu.tpl','ynvideochannel', array('video' => $item)); ?>
                </div>

                <a href="<?php echo $item -> getHref()?>">
                    <?php if($item -> is_featured):?>
                        <span class="ynvideochannel_videos_channel-featured"><?php echo $this -> translate("Featured")?></span>
                    <?php endif;?>
                </a>
    
                <div class="ynvideochannel_videos_grid-item-duration ynvideochannel_videos_duration">
                    <?php echo $this->partial('_video_duration.tpl', 'ynvideochannel', array('video' => $item)); ?>
                    <a href="<?php echo $item -> getHref()?>"><i class="fa fa-play" aria-hidden="true"></i></a>
                </div>
            </div>
            

            <div class="ynvideochannel_videos_grid-item-info">
                <div class="ynvideochannel_videos_grid-item-title">
                    <a href="<?php echo $item -> getHref()?>">
                        <?php echo $item -> getTitle()?>
                    </a>
                </div>
                
                <div class="ynvideochannel_videos_grid-item-owner-creation_date">
                    <div class="ynvideochannel_videos_grid-item-owner">
                        <?php echo $this -> translate("by %s", $item -> getOwner())?>
                    </div>

                    <div class="ynvideochannel_videos_grid-item-createion_date">
                        <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
                    </div>
                </div>
                
                <div class="ynvideochannel_videos_grid-item-count-rating clearfix">
                    <div class="ynvideochannel_videos_grid-item-count">
                        <?php echo $this -> translate(array("%s view", "%s views", $item -> view_count), $item -> view_count)?>
                    </div>

                    <div class="ynvideochannel_videos_grid-item-rating ynvideochannel_videos_rating">
                        <?php echo $this->partial('_video_rating.tpl', 'ynvideochannel', array('rating' => $item->rating)); ?>
                    </div>
                </div>
            </div>

        </li>
    <?php endforeach;?>
</ul>
