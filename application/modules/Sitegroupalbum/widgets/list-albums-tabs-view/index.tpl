<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitegroupalbum/externals/styles/style_sitegroupalbum.css');

include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<?php if(empty($this->is_ajax)): ?>
<div class="layout_core_container_tabs">
<div class="tabs_alt tabs_parent">
  <ul id="main_tabs">
    <?php foreach ($this->tabs as $tab): ?>
    <?php $class = $tab->name == $this->activTab->name ? 'active' : '' ?>
      <li class = '<?php echo $class ?>'  id = '<?php echo 'sitegroupalbum_' . $tab->name.'_tab' ?>'>
        <a href='javascript:void(0);'  onclick="tabSwitchSitegroupalbum('<?php echo$tab->name; ?>');"><?php echo $this->translate($tab->getTitle()) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<div id="hideResponse_div" style="display: none;"></div>
<div id="sitegrouplbum_albums_tabs">   
   <?php endif; ?>
   <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <?php if($this->is_ajax !=2): ?>
     <ul class="thumbs sitegroup_content_thumbs" id ="sitegroupalbum_list_tab_album_content">
       <?php endif; ?>
      <?php foreach( $this->paginator as $album ): ?>
        <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.photos-sitegroup', $album->group_id, $layout);?>
        <li style="margin-left:<?php echo $this->marginPhoto ?>px;margin-right:<?php echo $this->marginPhoto ?>px;">
         <?php if($album->photo_id != 0):?>
						<a class="thumbs_photo" href="<?php echo $album->getHref(array( 'group_id' => $album->group_id, 'album_id' => $album->album_id,'slug' => $album->getSlug(), 'tab' => $tab_id)); ?>">
								<span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
						</a>
          <?php else:?>
						<a class="thumbs_photo" href="<?php echo $album->getHref(array( 'group_id' => $album->group_id, 'album_id' => $album->album_id,'slug' => $album->getSlug(), 'tab' => $tab_id)); ?>">
								<span><?php echo $this->itemPhoto($album, 'thumb.normal'); ?></span>
						</a>
          <?php endif;?>
          <p class="thumbs_info">
            <span class="thumbs_title">
              <?php echo $this->htmlLink($album->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10),array('title' => $album->getTitle())) ?>
            </span>
							<?php $sitegroup_object = Engine_Api::_()->getItem('sitegroup_group', $album->group_id);?>
							<?php
							$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.title.truncation', 18);
							$tmpBody = strip_tags($sitegroup_object->title);
							$group_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
							?>
						<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($album->group_id, $album->owner_id, $album->getSlug()),  $group_title,array('title' => $sitegroup_object->title)) ?>  
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1)):?>     
							<?php echo $this->translate('by ').$this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('title' => $album->getOwner()->getTitle(),'class' => 'thumbs_author')) ?>
            <?php endif;?>
            <br />
            <?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?>
       
          <?php if( $this->activTab->name == 'viewed_groupalbums' ): ?> -
            <?php echo $this->translate(array('%s view', '%s views', $album->view_count), $this->locale()->toNumber($album->view_count)) ?>
          <?php elseif( $this->activTab->name == 'commented_groupalbums' ): ?> -
            <?php echo $this->translate(array('%s comment', '%s comments', $album->comment_count), $this->locale()->toNumber($album->comment_count)) ?>
          <?php elseif( $this->activTab->name == 'liked_groupalbums' ): ?> -
            <?php echo $this->translate(array('%s like', '%s likes', $album->like_count), $this->locale()->toNumber($album->like_count)) ?>
          <?php endif; ?>
          </p>
        </li>
      <?php endforeach;?>
       <?php if($this->is_ajax !=2): ?>  
    </ul>  
      <?php endif; ?>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('No albums have been created yet.');?>
      </span>
    </div>
  <?php endif; ?>   
<?php if(empty($this->is_ajax)): ?>    
</div>
<?php if (!empty($this->showViewMore)): ?>
<div class="seaocore_view_more" id="sitegroupalbum_albums_tabs_view_more" onclick="viewMoreTabGroupalbum()">
  <?php
  echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
      'id' => 'feed_viewmore_link',
      'class' => 'buttonlink icon_viewmore'
  ))
  ?>
</div>
<div class="seaocore_loading" id="sitegroupalbum_albums_tabs_loding_image" style="display: none;">
  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="" />
  <?php echo $this->translate("Loading ...") ?>
</div>
<?php endif; ?>
</div>
<?php endif; ?>

<?php if(empty($this->is_ajax)): ?>
<script type="text/javascript">
  
  var tabSwitchSitegroupalbum = function (tabName) {
 <?php foreach ($this->tabs as $tab): ?>
  if($('<?php echo 'sitegroupalbum_'.$tab->name.'_tab' ?>'))
        $('<?php echo 'sitegroupalbum_' .$tab->name.'_tab' ?>').erase('class');
  <?php  endforeach; ?>

 if($('sitegroupalbum_'+tabName+'_tab'))
        $('sitegroupalbum_'+tabName+'_tab').set('class', 'active');
   if($('sitegrouplbum_albums_tabs')) {
      $('sitegrouplbum_albums_tabs').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/loader.gif" class="sitegroup_tabs_loader_img" /></center>';
    }   
    if($('sitegroupalbum_albums_tabs_view_more'))
    $('sitegroupalbum_albums_tabs_view_more').style.display =  'none';
    var request = new Request.HTML({
     method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitegroupalbum/name/list-albums-tabs-view',
      'data' : {
        format : 'html',
        isajax : 1,
        tabName: tabName,
        category_id : '<?php echo $this->category_id;?>',
        margin_photo : '<?php echo $this->marginPhoto ?>'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('sitegrouplbum_albums_tabs').innerHTML = responseHTML;
            <?php if(!empty ($this->showViewMore)): ?>
              hideViewMoreLinkSitegroupAlbumAlbum();
             <?php endif; ?> 
      }
    });

    request.send();
  }
</script>
<?php endif; ?>
<?php if(!empty ($this->showViewMore)): ?>
<script type="text/javascript">
    en4.core.runonce.add(function() {
    hideViewMoreLinkSitegroupAlbumAlbum();  
    });
    function getNextGroupSitegroupAlbumAlbum(){
      return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }
    function hideViewMoreLinkSitegroupAlbumAlbum(){
      if($('sitegroupalbum_albums_tabs_view_more'))
        $('sitegroupalbum_albums_tabs_view_more').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>';
    }
        
    function viewMoreTabGroupalbum()
  {
    $('sitegroupalbum_albums_tabs_view_more').style.display ='none';
    $('sitegroupalbum_albums_tabs_loding_image').style.display ='';
    en4.core.request.send(new Request.HTML({
      method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitegroupalbum/name/list-albums-tabs-view',
      'data' : {
        format : 'html', 
        isajax : 2,
        category_id : '<?php echo $this->category_id;?>',
        tabName : '<?php echo $this->activTab->name ?>',
        margin_photo : '<?php echo $this->marginPhoto ?>',
        group: getNextGroupSitegroupAlbumAlbum()
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {    
        $('hideResponse_div').innerHTML=responseHTML;      
        var photocontainer = $('hideResponse_div').getElement('.layout_sitegroupalbum_list_albums_tabs_view').innerHTML;
        $('sitegroupalbum_list_tab_album_content').innerHTML = $('sitegroupalbum_list_tab_album_content').innerHTML + photocontainer;
        $('sitegroupalbum_albums_tabs_loding_image').style.display ='none';
        $('hideResponse_div').innerHTML="";        
      }
    }));

    return false;

  }  
</script>
<?php endif; ?>
