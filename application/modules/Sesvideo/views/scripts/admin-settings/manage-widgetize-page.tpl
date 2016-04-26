<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manage-widgetize-page.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/dismiss_message.tpl';?>
<h3><?php echo $this->translate("Manage Widgetize Pages") ?></h3>
<p>
	<?php echo $this->translate('This page lists all of the Widgetize Page in this plugin. From here you can easily go to particular page in "Layout Editor" by clicking on "Get Widgetize Page" and also you can view directly user side page by click on "View Page" link.'); ?>
</p>
<br />
<table class='admin_table'>
  <thead>
    <tr>
      <th><?php echo $this->translate("Page Name") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
      <th><?php echo $this->translate("Demo Links") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->pagesArray as $item):
    $corePages = Engine_Api::_()->sesvideo()->getwidgetizePage(array('name' => $item));
    $page = explode("_",$corePages->name);
    $executed = false;
    ?>
    <tr>
      <td><?php echo $corePages->displayname ?></td>
      <td>
        <?php $url = $this->url(array('module' => 'core', 'controller' => 'content', 'action' => 'index'), 'admin_default').'?page='.$corePages->page_id;?>
        <a href="<?php echo $url;?>"  target="_blank"><?php echo "Get Widgetize Page";?></a>
        |
        <?php if($corePages->name == 'sesvideo_artist_view'): ?>
        <?php $results = Engine_Api::_()->sesvideo()->getRow(array('table_name' => 'artists', 'id' => 'artist_id'));  ?>
        <?php $executed = true; ?>
        <?php elseif($corePages->name == 'sesvideo_playlist_view'): ?>
          <?php $results = Engine_Api::_()->sesvideo()->getRow(array('table_name' => 'playlists', 'id' => 'playlist_id'));  ?>
         <?php $executed = true; ?>
        <?php elseif($corePages->name == 'sesvideo_chanel_view'): ?>
          <?php $results = Engine_Api::_()->sesvideo()->getRow(array('table_name' => 'chanelphotos', 'id' => 'chanelphoto_id'));  ?>
         <?php $executed = true; ?>
        <?php elseif($corePages->name == 'sesvideo_chanel_index'): ?>
          <?php $results = Engine_Api::_()->sesvideo()->getRow(array('table_name' => 'chanels', 'id' => 'chanel_id'));  ?>
         <?php $executed = true; ?>
        <?php elseif($corePages->name == 'sesvideo_category_index'): ?>
          <?php $results = Engine_Api::_()->sesvideo()->getRow(array('table_name' => 'categories', 'id' => 'category_id'));  ?>
          <?php $executed = true; ?>
        <?php elseif($corePages->name == 'sesvideo_index_view'): ?>
        <?php $results = Engine_Api::_()->sesvideo()->getRow(array('table_name' => 'videos', 'id' => 'video_id'));  ?>
         <?php $executed = true; ?>
        <?php endif; ?>
        <?php if($results): ?>
        <a href="<?php echo $results->getHref(); ?>" target="_blank"><?php echo $this->translate("View Page") ?></a>
        <?php elseif($executed):?>
        	 <a href="javascript:;"  title="No record found"><?php echo $this->translate("View Page") ?></a>
        <?php else: ?>
        <?php $viewPageUrl = $this->url(array('module' => $page[0], 'controller' => $page[1], 'action' => $page[2]), 'default');?>
        <a href="<?php echo $viewPageUrl; ?>" target="_blank"><?php echo $this->translate("View Page") ?></a>
        <?php endif; ?>
      </td>
      <td>
	      <?php if($corePages->name == 'sesvideo_index_welcome'): ?>
		      <a target="_blank" href="http://demo.socialenginesolutions.com/videos"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_index_tags'): ?>
			    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/tags?type=video"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_artist_view'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/artist/7/chris-brown"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_artist_browse'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/artists"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_index_locations'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/locations"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_playlist_view'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/playlist/1/videos-i-like"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_playlist_browse'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/playlist"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_chanel_view'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/video/channel/super-music"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_chanel_index'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/video/channel/super-music"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_index_home'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/home"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_index_manage'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/manage"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_index_create'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/create"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_chanel_create'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/channels/create"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_category_index'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/category/film-animation"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_category_browse'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/categories"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_chanel_category'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/channels/category?category_id=16"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_chanel_browse'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/channels"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_index_browse'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/browse"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_index_view'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/video/45/23/how-to-make-orange-chicken-recipe-asian-food-recipes"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_index_browse-pinboard'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/browse-pinboard"><?php echo $this->translate("View"); ?></a>
		    <?php elseif($corePages->name == 'sesvideo_index_edit'): ?>
		    <a target="_blank" href="http://demo.socialenginesolutions.com/videos/edit/video_id/22"><?php echo $this->translate("View"); ?></a>
		    <?php endif; ?>
      </td>
    </tr>
    <?php $results = ''; ?>
    <?php endforeach; ?>
  </tbody>
</table>

