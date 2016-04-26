<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
?>
<div class="sesbasic_breadcrumb">
  <?php if($this->viewPageType == 'video' && ($this->video->parent_type == 'sesblog_blog' || $this->video->parent_type == 'sesevent_event')): ?>
	  <?php $item = Engine_Api::_()->getItem($this->video->parent_type, $this->video->parent_id); ?>
	  <a href="<?php echo $item->getHref(); ?>"><?php echo $item->getTitle(); ?> ?></a>&nbsp;&raquo;
		<?php echo $this->video->getTitle(); ?>
  <?php else: ?>
	  <?php if($this->viewPageType == 'video'):  ?>
	  <a href="<?php echo $this->url(array('action' => 'home'), "sesvideo_general"); ?>"><?php echo $this->translate("Videos Home"); ?></a>&nbsp;&raquo;
	  <a href="<?php echo $this->url(array('action' => 'browse'), "sesvideo_general"); ?>"><?php echo $this->translate("Browse Videos"); ?></a>&nbsp;&raquo;
	  <?php echo $this->video->getTitle(); ?>
	  <?php elseif($this->viewPageType == 'chanel'): ?>
	  <a href="<?php echo $this->url(array('action' => 'home'), "sesvideo_general"); ?>"><?php echo $this->translate("Videos Home"); ?></a>&nbsp;&raquo;
	  <a href="<?php echo $this->url(array('action' => 'browse'), "sesvideo_general"); ?>"><?php echo $this->translate("Browse Videos"); ?></a>&nbsp;&raquo;
	  <a href="<?php echo $this->url(array('action' => 'browse'), "sesvideo_chanel"); ?>"><?php echo $this->translate("Browse Chanel"); ?></a>&nbsp;&raquo;
	  <?php echo $this->chanel->getTitle(); ?>
	  <?php elseif($this->viewPageType == 'artist'): ?>
	  <a href="<?php echo $this->url(array('action' => 'home'), "sesvideo_general"); ?>"><?php echo $this->translate("Video Home"); ?></a>&nbsp;&raquo;
	  <a href="<?php echo $this->url(array('action' => 'browse'), "sesvideo_general"); ?>"><?php echo $this->translate("Browse Videos"); ?></a>&nbsp;&raquo;
	  <a href="<?php echo $this->url(array('action' => 'browse'), "sesvideo_artists"); ?>"><?php echo $this->translate("Artists"); ?></a>&nbsp;&raquo;
	  <?php echo $this->artist->name; ?>
	  <?php elseif($this->viewPageType == 'playlist'): ?>
	  <a href="<?php echo $this->url(array('action' => 'home'), "sesvideo_general"); ?>"><?php echo $this->translate("Videos Home"); ?></a>&nbsp;&raquo;
	  <a href="<?php echo $this->url(array('action' => 'browse'), "sesvideo_playlist"); ?>"><?php echo $this->translate("Browse Playlists"); ?></a>&nbsp;&raquo;
	  <?php echo $this->playlist->getTitle(); ?>
	  <?php endif; ?>
	<?php endif; ?>
</div>
