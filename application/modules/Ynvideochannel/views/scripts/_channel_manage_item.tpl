<?php $item = $this->item; ?>
<?php $photo_url = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') :
    'application/modules/Ynvideochannel/externals/images/nophoto_channel_thumb_normal.png'; ?>
<li class="ynvideochannel_manage_channel_item">
    <div class="ynvideochannel_channel_thumb">
        <img width="200px" src="<?php echo $photo_url?>"/>
    </div>
    <div class="ynvideochannel_channel_info">
        <div><a href="<?php echo $item -> getHref()?>"><?php echo $item -> getTitle()?></a></div>
        <?php if ($item->category_id)
            echo $this->partial('_category_breadcrumb.tpl', 'ynvideochannel', array('item' => $item));
        ?>
        <div><?php echo $this -> string()->truncate($item->description,300);?></div>
        <div><?php echo $this -> translate(array("%s video", "%s videos", $item -> video_count), $item -> video_count)?></div>
        <div><?php echo $this -> translate(array("%s subscriber", "%s subscribers", $item -> subscriber_count), $item -> subscriber_count)?></div>
        <div><?php echo $this -> translate(array("%s like", "%s likes", $item -> like_count), $item -> like_count)?></div>
        <div><?php echo $this -> translate(array("%s comment", "%s comments", $item -> comment_count), $item -> comment_count)?></div>
    </div>
    <?php echo $this->partial('_channel_options.tpl', 'ynvideochannel', array('channel' => $item)); ?>
</li>
