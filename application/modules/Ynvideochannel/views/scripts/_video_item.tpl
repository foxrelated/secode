<?php $item = $this->item; ?>
<li class="ynvideochannel_manage_video_item">
    <?php $item = $this->item; ?>
    <?php if($item -> is_featured):?>
    <div><?php echo $this -> translate("Featured")?></div>
    <?php endif;?>
    <?php $photo_url = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_video_thumb_normal.png'; ?>
    <div><img width="200px" src="<?php echo $photo_url?>"/></div>
    <?php echo $this->partial('_video_duration.tpl', 'ynvideochannel', array('video' => $item)); ?>
    <div><a href="<?php echo $item -> getHref()?>"><?php echo $item -> getTitle()?></a></div>
    <div><?php echo $this -> string() -> truncate($item -> description, 200) ?></div>
    <div><?php echo $this -> translate("by %s", $item -> getOwner())?></div>
    <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
    <div><?php echo $this -> translate(array("%s view", "%s views", $item -> view_count), $item -> view_count)?></div>
    <div><?php echo $this -> translate(array("%s like", "%s likes", $item -> like_count), $item -> like_count)?></div>
    <div><?php echo $this -> translate(array("%s comment", "%s comments", $item -> comment_count), $item -> comment_count)?></div>
    <div><?php echo $this -> translate(array("%s favorite", "%s favorites", $item -> favorite_count), $item -> favorite_count)?></div>
    <?php echo $this->partial('_video_rating.tpl', 'ynvideochannel', array('rating' => $item->rating)); ?>
    <?php echo $this->partial('_video_options.tpl', 'ynvideochannel', array('video' => $item)); ?>
    <?php if($this -> showAddto) echo $this->partial('_add_to_menu.tpl','ynvideochannel', array('video' => $item)); ?>
</li>