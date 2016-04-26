<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<ul class="generic_list_widget ynvideo_widget">
    <?php foreach ($this->videos as $item): ?>
        <li style="width:<?php echo $this -> width;?>px">
            <?php echo $this->partial('_video_widget.tpl', 'ynvideo', array('video' => $item, 'height' => $this -> height, 'width' => $this -> width, 'margin_left' => $this -> margin_left)) ?>
        </li>
    <?php endforeach; ?>
</ul>

