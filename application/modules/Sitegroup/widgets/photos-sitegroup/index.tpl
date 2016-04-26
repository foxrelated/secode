<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/styles/style_sitegroup_profile.css')
?>

<?php 
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/hideWidgets.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/hideTabs.js');
?>


<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/Adintegration.tpl';
?>

<?php if($this->can_edit): ?>
	<script type="text/javascript">
			var SortablesInstance;
			en4.core.runonce.add(function() {
				$$('.thumbs_nocaptions > li').addClass('sortable');
				SortablesInstance = new Sortables($$('.thumbs_nocaptions'), {
					clone: true,
					constrain: true,
					//handle: 'span',
					onComplete: function(e) {
						var ids = [];
						$$('.thumbs_nocaptions > li').each(function(el) {
							ids.push(el.get('id').match(/\d+/)[0]);
						});
						//console.log(ids);
						// Send request
						
						var url = '<?php echo $this->url(array('action' => 'album-order','group_id' => $this->sitegroup->group_id), 'sitegroup_albumphoto_general')?>';
						var request = new Request.JSON({
							'url' : url,
							'data' : {
								format : 'json',
								order : ids
							}
						});
						request.send();
					}
				});
			});
	</script>
<?php endif; ?>

<?php if (!empty($this->show_content)) : ?>
	<script type="text/javascript">
	  var paginateGroupAlbums = function(group, groups) {
	  $('album_image').innerHTML = '<center><img src="'+en4.core.staticBaseUrl+'application/modules/Sitegroup/externals/images/loader.gif" /></center>';
	    var url = en4.core.baseUrl + 'widget/index/mod/sitegroup/name/photos-sitegroup';	
	    en4.core.request.send(new Request.HTML({
	      'url' : url,
	      'data' : {
	        'format' : 'html',
	        'subject' : en4.core.subject.guid,
	        'group' : group,
	        'groups' : groups,
	        'isajax' : '1',
	        'tab' : '<?php echo $this->content_id ?>',
	        'itemCount' : '<?php echo $this->itemCount ?>',
          'itemCount_photo' : '<?php echo $this->itemCount_photo ?>',
          'albumsorder' : '<?php echo $this->albums_order ?>'
	      }
	    }), {
	      'element' : $('id_' + <?php echo $this->content_id ?>)
	    });
	  }
	  var paginateGroupPhotos = function(groups,group) {
	  	$('photo_image').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/loader.gif" /></center>';
	    var url = en4.core.baseUrl + 'widget/index/mod/sitegroup/name/photos-sitegroup';	
	    en4.core.request.send(new Request.HTML({
	      'url' : url,
	      'data' : {
	        'format' : 'html',
	        'subject' : en4.core.subject.guid,
	        'groups' : groups,
	        'group' : group,
	        'isajax' : '1',
	        'tab' : '<?php echo $this->content_id ?>',
          'itemCount' : '<?php echo $this->itemCount ?>',
          'itemCount_photo' : '<?php echo $this->itemCount_photo ?>',
          'albumsorder' : '<?php echo $this->albums_order ?>'
	      },
     		onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
           $('id_' + <?php echo $this->content_id ?>).innerHTML=responseHTML;
//          if($('white_content_default')){
//           $('white_content_default').addEvent('click', function(event) {
//            event.stopPropagation();
//            });
//          }
        }
	    }), {
	    //  'element' : $('id_' + <?php //echo $this->content_id ?>)
	    });
	  }  
	</script>
<?php endif;?>

<?php if (empty($this->isajax)) : ?>
	<div id="id_<?php echo $this->content_id; ?>">
<?php endif;?>

<?php if (!empty($this->show_content)) :?>
	<?php if($this->showtoptitle == 1):?>
		<div class="layout_simple_head" id="layout_photo">
			<?php echo $this->translate($this->sitegroup->getTitle());?><?php echo $this->translate("'s Photos");?>
		</div>
	<?php endif;?>	
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumwidget', 3) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)):?>
		<div class="layout_right" id="communityad_photo">
			<?php
				echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumwidget', 3),"loaded_by_ajax"=>1,'widgetId'=>'group_photo')); 
			?>
		</div>
		<div class="layout_middle">
	<?php endif;?>
	<?php  if($this->can_edit && !empty($this->allowed_upload_photo)): ?>
		<div class="seaocore_add">
			<a href='<?php echo $this->url(array('group_id' => $this->sitegroup->group_id, 'album_id' => 0, 'tab' => $this->identity_temp), 'sitegroup_photoalbumupload', true) ?>'  class='buttonlink icon_sitegroup_photo_new '><?php echo $this->translate('Create an Album'); ?></a>
		</div>
	<?php elseif(!empty($this->allowed_upload_photo) && ($this->sitegroup->owner_id != $this->viewer_id)): ?>
		<div class="seaocore_add">
			<a href='<?php echo $this->url(array('group_id' => $this->sitegroup->group_id, 'album_id' => $this->default_album_id, 'tab' => $this->identity_temp), 'sitegroup_photoalbumupload', true) ?>'  class='buttonlink icon_sitegroup_photo_new '><?php echo $this->translate('Add Photos'); ?></a>
		</div>
	<?php endif; ?>

	
		<div class="sitegroup_profile_photos_head">
		<?php if($this->album_count > 0) :?>
     <b><?php echo $this->translate($this->sitegroup->getTitle()); ?><?php echo $this->translate("'s Albums");?></b> &#8226;
     <?php echo $this->translate(array('%s Photo Album', '%s Photo Albums', count($this->paginator)),$this->locale()->toNumber(count($this->paginator))) ?>
		</div>
		<?php endif;?>
		<div class="sitegroup_profile_album_paging" align="right">
    	<?php if($this->album_count > $this->albums_per_group) :?>
			<?php $next_groups = $this->currentAlbumGroupNumbers+1; ?>
			<?php $previous_groups = $this->currentAlbumGroupNumbers-1; ?>
			<?php $maxgroupss = $this->maxgroups;?>
			<?php if($this->maxgroups >= $next_groups) :?>
				<div id="user_group_members_next">
					<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Last') , array(
							'onclick' => "paginateGroupAlbums('$maxgroupss', $this->currentGroupNumbers)",
						)); ?>
				</div>
				<div id="user_group_members_next">
					<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
							'onclick' => "paginateGroupAlbums('$next_groups', $this->currentGroupNumbers)",
						)); ?>
				</div>
			<?php endif; ?>

			<div class="paging_count">
				<?php foreach($this->groupsarray as $valuess) :
					if($valuess['links'] == 1) {  	
						echo $valuess['groups'] ?>
							<?php 	  
					}
					else {
					?> 
					<a href='javascript:void(0);' onclick="paginateGroupAlbums('<?php echo $valuess['groups'] ?>', '<?php echo $this->currentGroupNumbers?>')"><?php echo $valuess['groups'] ?></a> 
						<?php
					}
				endforeach; ?>
			</div>  

			<?php if($this->pstarts != $this->currentAlbumGroupNumbers):?>
				<?php if($this->currentAlbumGroupNumbers != $this->pstarts+1): ?>
					<div id="user_group_members_previous">
						<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Prev') , array(
							'onclick' => "paginateGroupAlbums('$previous_groups', '$this->currentGroupNumbers')",
						)); ?>
				</div> 
				<?php endif; ?>
				<div id="user_group_members_previous">
				<?php echo $this->htmlLink('javascript:void(0);', $this->translate('First') , array(
					'onclick' => "paginateGroupAlbums('$this->pstarts', '$this->currentGroupNumbers')",
				)); ?>
				</div> 
			<?php endif; ?>
		<?php endif; ?>
	</div>
	
	<?php if(count($this->paginator) > 0) :?>
		<div id='album_image' class="sitegroup_album_box clr">
			<ul class="thumbs thumbs_nocaptions">
		    <?php foreach ($this->paginator as $albums): ?>
		      <li style="height:200px;"  id="thumbs-photo-<?php echo $albums->photo_id ?>"> 
		      <?php if($albums->photo_id != 0): ?>
		        <a href="<?php echo $this->url(array( 'group_id' => $this->sitegroup->group_id, 'album_id' => $albums->album_id,'slug' => $albums->getSlug(), 'tab' => $this->identity_temp), 'sitegroup_albumphoto_general') ?>" class="thumbs_photo" title="<?php echo $albums->title;?>">
		          <span style="background-image: url(<?php echo $albums->getPhotoUrl('thumb.normal'); ?>);"></span>
		        </a>
		      <?php else: ?>
		        <a href="<?php echo $this->url(array('group_id' => $this->sitegroup->group_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug(),'tab' => $this->identity_temp), 'sitegroup_albumphoto_general') ?>" class="thumbs_photo" title="<?php echo $albums->title;?>" >
		          <span><?php echo $this->itemPhoto($albums, 'thumb.normal'); ?></span>
		        </a>
		        <?php endif; ?>
		        <div class="sitegroup_profile_album_title">
		        	<a href="<?php echo $this->url(array( 'group_id' => $this->sitegroup->group_id, 'album_id' => $albums->album_id,'slug' => $albums->getSlug(), 'tab' => $this->identity_temp), 'sitegroup_albumphoto_general') ?>" title="<?php echo $albums->title;?>"><?php echo $albums->title;?></a>
		        </div>
		        <div class="sitegroup_profile_album_stat">
		        	<?php echo $this->translate(array('%s photo', '%s photos', $albums->count()),$this->locale()->toNumber($albums->count())) ?>
		        	-		        	
		        	<?php echo $this->translate(array('%s like', '%s likes', $albums->like_count), $this->locale()->toNumber($albums->like_count)) ?>        
		        </div>
		      </li>		      
		    <?php endforeach; ?>
			</ul>
		</div>
	<?php else: ?>
    <div class="tip">
        <span> 
           <?php echo $this->translate("Nobody has created an album yet.");?>        
        </span>
    </div>
  <?php endif;?> 
	
	<?php if(count($this->paginators) > 0) :?>
		<div class="sitegroup_profile_photos_head">
			<b><?php echo $this->translate('Photos by Others');?></b> &#8226;
			<?php echo $this->translate(array('%s photo', '%s photos', count($this->paginators)),$this->locale()->toNumber(count($this->paginators))) ?>
		</div>	
	<?php endif; ?>
	
	<div class="sitegroup_profile_album_paging" align="right">
		<?php if($this->total_images > $this->photos_per_group) :?>
		  <?php $next_group = $this->currentGroupNumbers+1; ?>
		  <?php $previous_group = $this->currentGroupNumbers-1; ?>
		  <?php $maxgroups = $this->maxgroup;?>
		  <?php if($this->maxgroup >= $next_group) :?>
			  <div id="user_group_members_next">
			        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Last') , array(
			          'onclick' => "paginateGroupPhotos('$maxgroups', '$this->currentAlbumGroupNumbers')",
			        )); ?>
				</div>
				<div id="user_group_members_next">
				  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
				    'onclick' => "paginateGroupPhotos('$next_group', '$this->currentAlbumGroupNumbers')",
				  )); ?>
				</div>
			<?php endif; ?>
			<div class="paging_count">
				<?php foreach($this->grouparray as $values) :
				 if($values['link'] == 1) { 	
				 	 echo $values['group'] ?>
				 <?php  }
				 else { ?>
				   <a href='javascript:void(0);' onclick="paginateGroupPhotos('<?php echo $values['group'] ?>', '<?php echo $this->currentAlbumGroupNumbers ?>')"><?php echo $values['group'] ?></a>
					<?php
				}
				endforeach; ?>
			</div>	
	 		<?php if($this->pstart != $this->currentGroupNumbers):?>
			 <?php if($this->currentGroupNumbers != $this->pstart+1): ?>
			    <div id="user_group_members_previous">
		        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Prev') , array(
		          'onclick' => "paginateGroupPhotos('$previous_group', '$this->currentAlbumGroupNumbers')",
		        )); ?>
			 	</div> 
			 <?php endif; ?>
		    <div id="user_group_members_previous">	
			    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('First') , array(
			      'onclick' => "paginateGroupPhotos('$this->pstart', '$this->currentAlbumGroupNumbers')",
			    )); ?>
			 </div> 
		  <?php endif; ?>
		<?php endif; ?>
	</div>
	
	<?php if(count($this->paginators) > 0) :?>
		<div id='photo_image' class="sitegroup_album_box clr">
			<ul class="sitegroup_thumbs">
        <?php $k =0;?>
		    <?php foreach ($this->paginators as $photo): ?>
		      <li>
		        <?php //if(!$this->showLightBox):?>
<!--              <a class="thumbs_photo" href="<?php //echo $photo->getHref(); ?>" title="<?php //echo $photo->title;?>">
                <span style="background-image: url(<?php //echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
              </a>-->
            <?php //else:?>             
              <a href="<?php echo $photo->getHref() ?>"  <?php if(SEA_SITEGROUPALBUM_LIGHTBOX) :?> onclick="openSeaocoreLightBox('<?php echo $photo->getHref() . '/type/creation_date' . '/count/'.$this->total_images . '/offset/' . $k. '/owner_id/' . $this->viewer_id; ?>');return false;" <?php endif;?> class="thumbs_photo">
                <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
              </a>    
            <?php //endif; ?>
		      </li>
          <?php $k++;?>
		    <?php endforeach; ?>
			</ul>
		</div> 
		<?php endif; ?>
		<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumwidget', 3) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)):?>
			</div>		
	 <?php endif; ?>
<?php endif;?>
<?php if (empty($this->isajax)) : ?>
	</div>
<?php endif;?>

<script type="text/javascript">
    var photo_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumwidget', 3);?>'; 
    var adwithoutpackage = '<?php echo Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup) ?>';
    var group_communityad_integration = '<?php echo $group_communityad_integration; ?>';
		var is_ajax_divhide = '<?php echo $this->isajax;?>';
	  var execute_Request_Photo = '<?php echo $this->show_content;?>';
	  var show_widgets = '<?php echo $this->widgets ?>';
	//window.addEvent('domready', function () {	   	
	  var PhototabId = '<?php echo $this->module_tabid;?>';	   
    var PhotoTabIdCurrent = '<?php echo $this->identity_temp; ?>';	
    if (PhotoTabIdCurrent == PhototabId) {
    	if(group_showtitle != 0) {
    		if($('profile_status') && show_widgets == 1) {
				  $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitegroup->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Photos');?></h2>";	
    		}
    		if($('layout_photo')) {
			    $('layout_photo').style.display = 'block';
			  }	
    	}
	
      hideWidgetsForModule('sitegroupalbum');
		  prev_tab_id = '<?php echo $this->content_id; ?>'; 
		  prev_tab_class = 'layout_sitegroup_photos_sitegroup';   
		  execute_Request_Photo = true;
		  hideLeftContainer (photo_ads_display, group_communityad_integration, adwithoutpackage);
    }	  
    else if (is_ajax_divhide != 1) {	  	
	  	if($('global_content').getElement('.layout_sitegroup_photos_sitegroup')) {
				$('global_content').getElement('.layout_sitegroup_photos_sitegroup').style.display = 'none';
		  }	
	  	
	  }
   //});	
	$$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function() {
		$('global_content').getElement('.layout_sitegroup_photos_sitegroup').style.display = 'block';
    if(group_showtitle != 0) {
    	if($('profile_status') && show_widgets == 1) {
			  $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitegroup->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Photos');?></h2>";	
    	}	    	
    }	
	
    hideWidgetsForModule('sitegroupalbum');
		$('id_' + <?php echo $this->content_id ?>).style.display = "block";
    
    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
      $$('.'+ prev_tab_class).setStyle('display', 'none');
    }	
		
		if (prev_tab_id != '<?php echo $this->content_id; ?>') {
			execute_Request_Photo = false;
			prev_tab_id = '<?php echo $this->content_id; ?>';		
			prev_tab_class = 'layout_sitegroup_photos_sitegroup';   	
		}
		
		if(execute_Request_Photo == false) {	
			ShowContent('<?php echo $this->content_id; ?>', execute_Request_Photo, '<?php echo $this->identity_temp?>', 'photo', 'sitegroup', 'photos-sitegroup', group_showtitle, 'null', photo_ads_display, group_communityad_integration, adwithoutpackage, '<?php echo $this->itemCount ?>', '<?php echo $this->itemCount_photo ?>', null, '<?php echo $this->albums_order ?>');
			execute_Request_Photo = true;    		
		}   

		if('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1);?>' && photo_ads_display == 0) 
    {
       setLeftLayoutForGroup();
    }
	}); 
</script>
<?php //if($this->showLightBox):?>
<?php
  //include APPLICATION_PATH . '/application/modules/Sitegroupalbum/views/scripts/_lightboxImage.tpl';
?>
<?php //endif; ?>