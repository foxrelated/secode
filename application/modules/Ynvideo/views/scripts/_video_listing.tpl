<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<div class="ynvideo_thumb_wrapper video_thumb_wrapper" style="height: <?php if($this -> height) echo $this -> height + 5;?>px; width:<?php echo $this -> width?>px">
    <?php if ($this->video->duration): ?>
        <?php echo $this->partial('_video_duration.tpl', 'ynvideo', array('video' => $this->video)) ?>
    <?php endif ?>
    <?php
    if ($this->video->photo_id && $this->video -> getPhotoUrl('thumb.large')) 
    {
        echo $this->htmlLink($this->video->getHref(), $this->itemPhoto($this->video, 'thumb.large', '', array('style' => 'width:'.$this -> width.'px;height:'.$this->height.'px;margin-left:'.$this->margin_left.'px')));
    } 
    elseif($this->video->photo_id && $this->video -> getPhotoUrl('thumb.normal'))
	{
		echo $this->htmlLink($this->video->getHref(), $this->itemPhoto($this->video, 'thumb.normal', '', array('style' => 'width:'.$this -> width.'px;height:'.$this->height.'px;margin-left:'.$this->margin_left.'px')));
	} 
	else
    {
        echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Ynvideo/externals/images/video.png">';
    }
    ?>
    <span class="video_button_add_to_area">
        <button class="ynvideo_uix_button ynvideo_add_button" id="ynvideo_btn_video_<?php echo $this->video->getIdentity()?>" 
            video-id="<?php echo $this->video->getIdentity()?>">
            <div class="ynvideo_plus" />
        </button>
    </span>
</div>
<?php 
    echo $this->htmlLink($this->video->getHref(), 
            $this->video->getTitle(), 
            array('class' => 'ynvideo_title', 'title' => $this->video->getTitle())) 
?>

<div class="video_author">
    <?php $user = $this->video->getOwner() ?>
    <?php if ($user) : ?>
        <?php echo $this->translate('By') ?>
        <?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())) ?>
    <?php endif; ?>
    <?php 
    	$session = new Zend_Session_Namespace('mobile');
		 if(!$session -> mobile)
		 {
    ?>
    |
    <?php } ?>
    <span class="video_views">
        <?php if (!isset($this->infoCol) || ($this->infoCol == 'view')) : ?>
            <?php echo $this->translate(array('%1$s view', '%1$s views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?>
        <?php else : ?>
            <?php if ($this->infoCol == 'like') : ?>
                <?php
                    $likeCount = $this->video->likes()->getLikeCount();
                    echo $this->translate(array('%1$s like', '%1$s likes', $likeCount), $this->locale()->toNumber($likeCount));
                ?>
            <?php elseif ($this->infoCol == 'comment') : ?>
                <?php
                    $commentCount = $this->video->comments()->getCommentCount();
                    echo $this->translate(array('%1$s comment', '%1$s comments', $commentCount), $this->locale()->toNumber($commentCount));
                ?>
            <?php elseif ($this->infoCol == 'favorite') : ?>
            <?php
                echo $this->translate(array('%1$s favorite', '%1$s favorites', $this->video->favorite_count), $this->locale()->toNumber($this->video->favorite_count));
            ?>
            <?php endif; ?>
        <?php endif; ?>
    </span>
</div>

    <?php 
        echo $this->partial('_video_rating_big.tpl', 'ynvideo', array('video' => $this->video));
    ?>