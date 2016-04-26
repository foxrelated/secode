<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<div class="sesbasic_breadcrumb">
  <?php if($this->viewPageType == 'album'): ?>
    <?php if($this->album->resource_type == 'sesvideo_chanel' && $this->album->resource_id): ?>
    <a href="<?php echo $this->url(array('action' => 'index', 'chanel_id' => $this->album->resource_id), "sesvideo_chanel_view"); ?>"><?php echo $this->translate("Chanel"); ?></a>&nbsp;&raquo;
      <?php $tab_id = Engine_Api::_()->sesbasic()->getWidgetTabId(array('name' => 'sesvideo.profile-musicalbums'));  ?>
      <a href="<?php echo $this->url(array('action' => 'index', 'chanel_id' => $this->album->resource_id), "sesvideo_chanel_view") . '/tab/' . $tab_id ?>"><?php echo $this->translate("Chanel Music"); ?></a>&nbsp;&raquo;
      <?php echo $this->album->getTitle(); ?>
    <?php else:  ?>
      <a href="<?php echo $this->url(array('action' => 'home'), "sesmusic_general"); ?>"><?php echo $this->translate("Music Album Home"); ?></a>&nbsp;&raquo;
      <a href="<?php echo $this->url(array('action' => 'browse'), "sesmusic_general"); ?>"><?php echo $this->translate("Music Albums"); ?></a>&nbsp;&raquo;
      <?php echo $this->album->getTitle(); ?>
    <?php endif; ?>
  <?php elseif($this->viewPageType == 'song'): ?>
    <?php if($this->album->resource_type == 'sesvideo_chanel' && $this->album->resource_id): ?>
      <a href="<?php echo $this->url(array('action' => 'index', 'chanel_id' => $this->album->resource_id), "sesvideo_chanel_view"); ?>"><?php echo $this->translate("Chanel"); ?></a>&nbsp;&raquo;
      <?php $tab_id = Engine_Api::_()->sesbasic()->getWidgetTabId(array('name' => 'sesvideo.profile-musicalbums'));  ?>
      <a href="<?php echo $this->url(array('action' => 'index', 'chanel_id' => $this->album->resource_id), "sesvideo_chanel_view") . '/tab/' . $tab_id ?>"><?php echo $this->translate("Chanel Music"); ?></a>&nbsp;&raquo;
      <?php echo $this->htmlLink($this->album->getHref(), $this->album->getTitle()) ?>&nbsp;&raquo;
      <?php echo $this->album->getTitle(); ?>
    <?php else:  ?>
    <a href="<?php echo $this->url(array('action' => 'home'), "sesmusic_general"); ?>"><?php echo $this->translate("Music Album Home"); ?></a>&nbsp;&raquo;
    <a href="<?php echo $this->url(array('action' => 'browse'), "sesmusic_general"); ?>"><?php echo $this->translate("Music Albums"); ?></a>&nbsp;&raquo;
    <a href="<?php echo $this->url(array('action' => 'browse'), "sesmusic_songs"); ?>"><?php echo $this->translate("Songs"); ?></a>&nbsp;&raquo;
    <?php echo $this->htmlLink($this->album->getHref(), $this->album->getTitle()) ?>&nbsp;&raquo;
    <?php echo $this->albumSong->getTitle(); ?>
    <?php endif; ?>
  <?php elseif($this->viewPageType == 'artist'): ?>
    <a href="<?php echo $this->url(array('action' => 'home'), "sesmusic_general"); ?>"><?php echo $this->translate("Music Album Home"); ?></a>&nbsp;&raquo;
    <a href="<?php echo $this->url(array('action' => 'browse'), "sesmusic_general"); ?>"><?php echo $this->translate("Music Albums"); ?></a>&nbsp;&raquo;
    <a href="<?php echo $this->url(array('action' => 'browse'), "sesmusic_artists"); ?>"><?php echo $this->translate("Artists"); ?></a>&nbsp;&raquo;
    <?php echo $this->artist->name; ?>
  <?php elseif($this->viewPageType == 'playlist'): ?>
    <a href="<?php echo $this->url(array('action' => 'home'), "sesmusic_general"); ?>"><?php echo $this->translate("Music Album Home"); ?></a>&nbsp;&raquo;
    <a href="<?php echo $this->url(array('action' => 'browse'), "sesmusic_playlists"); ?>"><?php echo $this->translate("Playlists"); ?></a>&nbsp;&raquo;
    <?php if($this->viewer_id): ?>
    <a href="<?php echo $this->url(array('action' => 'manage'), "sesmusic_playlists"); ?>"><?php echo $this->translate("My Playlists"); ?></a>&nbsp;&raquo;
    <?php endif; ?>
    <?php echo $this->playlist->getTitle(); ?>
  <?php endif; ?>
</div>
