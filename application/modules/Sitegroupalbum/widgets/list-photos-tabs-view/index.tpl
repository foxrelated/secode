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
        <a href='javascript:void(0);'  onclick="tabSwitchSitegroupalbumPhoto('<?php echo$tab->name; ?>');"><?php echo $this->translate($tab->getTitle()) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<div id="hideResponse_div" style="display: none;"></div>

<div id="sitegrouplbum_photos_tabs">
<?php endif; ?>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <?php if($this->is_ajax !=2): ?>
    <ul class="thumbs thumbs_nocaptions" id ="sitegroupalbum_list_tab_photo_content">
     <?php endif; ?> 
      <?php $i=0; ?>
      <?php foreach( $this->paginator as $item ): ?>
       <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
					$tab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.photos-sitegroup', $item->group_id, $layout);?>
       <?php if($this->activTab->name=='featured_groupphotos' || $this->activTab->name=='random_groupphotos' ):
               $i=0;
       endif; ?>
      <li style="margin-left:<?php echo $this->marginPhoto ?>px;margin-right:<?php echo $this->marginPhoto ?>px;">
         <a class="thumbs_photo" href="<?php echo $item->getHref(); ?>"  <?php if($this->showLightBox): ?>
            onclick="openSeaocoreLightBox('<?php echo $item->getHref()?>');return false;"
       <?php endif; ?> >
            <span style="background-image: url(<?php echo $item->getPhotoUrl('thumb.normal'); ?>);"></span>
          </a>
          <span class="show_content_des">
          	<?php if( $this->activTab->name == 'viewed_groupphotos' ): ?> 
	            <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
	            <br />
	          <?php elseif( $this->activTab->name == 'commented_groupphotos' ): ?>
	            <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>
	            <br />
	          <?php elseif( $this->activTab->name == 'liked_groupphotos' ): ?> 
	            <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
	            <br />
	          <?php endif; ?>           
            <?php
              $owner = $item->getOwner();
              $parent= Engine_Api::_()->getItem('sitegroup_album', $item->album_id);
              echo $this->translate('in ').
                  $this->htmlLink($parent->getHref(array('tab' => $tab_id)), $this->string()->truncate($parent->getTitle(),25),array('title' => $parent->getTitle()));
            ?>
							<?php $sitegroup_object = Engine_Api::_()->getItem('sitegroup_group', $item->group_id);?>
							<?php
							$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.title.truncation', 18);
							$tmpBody = strip_tags($sitegroup_object->title);
							$group_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
							?>
						<?php echo $this->translate("of ") . $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($item->group_id, $item->user_id, $item->getSlug()),  $group_title,array('title' => $sitegroup_object->title)) ?>      
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1)):?> 
							<?php echo $this->translate('by ').
										$this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title' => $owner->getTitle()));?>
            <?php endif;?>
          </span>
      	</li>
      <?php $i++; ?>
      <?php endforeach;?>
    </ul>
  <?php else: ?>
    <div class="tip">
      <span>
         <?php echo $this->translate('No photos have been uploaded yet.');?>
      </span>
    </div>
  <?php endif; ?>
<?php if(empty($this->is_ajax)): ?>
</div>
<?php if (!empty($this->showViewMore)): ?>
<div class="seaocore_view_more" id="sitegroupalbum_photos_tabs_view_more" onclick="viewMoreTabGroupalbumphotos()">
  <?php
  echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
      'id' => 'feed_viewmore_link',
      'class' => 'buttonlink icon_viewmore'
  ))
  ?>
</div>
<div class="seaocore_loading" id="sitegroupalbum_photos_tabs_loding_image" style="display: none;">
  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="" />
  <?php echo $this->translate("Loading ...") ?>
</div>
<?php endif; ?>
</div>
<?php endif; ?>

<?php if(empty($this->is_ajax)): ?>
<script type="text/javascript">
  
  var tabSwitchSitegroupalbumPhoto = function (tabName) {
 <?php foreach ($this->tabs as $tab): ?>
  if($('<?php echo 'sitegroupalbum_'.$tab->name.'_tab' ?>'))
        $('<?php echo 'sitegroupalbum_' .$tab->name.'_tab' ?>').erase('class');
  <?php  endforeach; ?>

 if($('sitegroupalbum_'+tabName+'_tab'))
        $('sitegroupalbum_'+tabName+'_tab').set('class', 'active');
   if($('sitegrouplbum_photos_tabs')) {
      $('sitegrouplbum_photos_tabs').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/loader.gif" class="sitegroup_tabs_loader_img" /></center>';
    }
     if($('sitegroupalbum_photos_tabs_view_more'))
    $('sitegroupalbum_photos_tabs_view_more').style.display =  'none';
    var request = new Request.HTML({
     method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitegroupalbum/name/list-photos-tabs-view',
      'data' : {
        format : 'html',
        isajax : 1,
        category_id : '<?php echo $this->category_id;?>',
        tabName: tabName,
        margin_photo : '<?php echo $this->marginPhoto ?>'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('sitegrouplbum_photos_tabs').innerHTML = responseHTML;
             <?php if(!empty ($this->showViewMore)): ?>
              hideViewMoreLinkSitegroupAlbumPhoto();
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
    hideViewMoreLinkSitegroupAlbumPhoto();  
    });
    function getNextGroupSitegroupAlbumPhoto(){
      return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }
    function hideViewMoreLinkSitegroupAlbumPhoto(){
      if($('sitegroupalbum_photos_tabs_view_more'))
        $('sitegroupalbum_photos_tabs_view_more').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>';
    }
    
       
    function viewMoreTabGroupalbumphotos()
  {
    $('sitegroupalbum_photos_tabs_view_more').style.display ='none';
    $('sitegroupalbum_photos_tabs_loding_image').style.display ='';
    en4.core.request.send(new Request.HTML({
      method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitegroupalbum/name/list-photos-tabs-view',
      'data' : {
        format : 'html', 
        isajax : 2,
        category_id : '<?php echo $this->category_id;?>',
        tabName : '<?php echo $this->activTab->name ?>',
        margin_photo : '<?php echo $this->marginPhoto ?>',
        group: getNextGroupSitegroupAlbumPhoto()
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {    
        $('hideResponse_div').innerHTML=responseHTML;      
        var photocontainer = $('hideResponse_div').getElement('.layout_sitegroupalbum_list_photos_tabs_view').innerHTML;
        $('sitegroupalbum_list_tab_photo_content').innerHTML = $('sitegroupalbum_list_tab_photo_content').innerHTML + photocontainer;
        $('sitegroupalbum_photos_tabs_loding_image').style.display ='none';
        $('hideResponse_div').innerHTML="";        
      }
    }));

    return false;

  }  
</script>
<?php endif; ?>

