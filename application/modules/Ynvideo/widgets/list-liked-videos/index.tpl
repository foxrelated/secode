<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<ul class="generic_list_widget ynvideo_widget <?php echo ($this->viewType == 'small')?'':'videos_browse ynvideo_frame ynvideo_list'?>">
    <?php foreach ($this->videos as $item): ?>
        <li style="<?php echo isset($this->marginLeft)?'margin-left:' . $this->marginLeft . 'px':''?>; width:<?php echo $this -> width?>px; height:<?php echo $this -> height + 70?>px">
            <?php
                if ($this->viewType == 'small') {
                    echo $this->partial('_video_widget.tpl', 'ynvideo', 
                        array('video' => $item, 'infoCol' => 'like',
						'height' => $this -> height, 'width' => $this -> width, 'margin_left' => $this -> margin_left
						)); 
                } else {
                    echo $this->partial('_video_listing.tpl', 'ynvideo', array(
                        'video' => $item,
                        'recentCol' => 'creation_date',
                        'infoCol' => 'like',
                        'height' => $this -> height, 'width' => $this -> width, 'margin_left' => $this -> margin_left
                    ));
                }
            ?>
        </li>
    <?php endforeach; ?>
</ul>