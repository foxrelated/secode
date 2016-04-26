<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>

<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitegroupalbum/externals/styles/style_sitegroupalbum.css');
	$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/core.js');
?>

<?php if ($this->can_edit):?>
	<script type="text/javascript">
    var SortablesInstance;
    sm4.core.runonce.add(function() {
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
          var url = '<?php echo $this->url(array('action' => 'order')) ?>';
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
<?php endif ;?>  

<script type="text/javascript" >
//	function editalbum(thisobj) {
//		var Obj_Url = thisobj.href;
//		Smoothbox.open(Obj_Url);
//	}
</script>

<a id="sitegroup_profile_photo_anchor" style="position:absolute;"></a>
<script type="text/javascript">
  var paginateGroupPhotos = function(groups, url) {
  	$('album_backgroundimage').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/loader.gif" /></center>';
    var url = url;
    sm4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'groups' : groups
      },
     		onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          $('sitegroup_profile_photo_anchor').getParent().innerHTML=responseHTML;
          if($('white_content_default')){  
           $('white_content_default').addEvent('click', function(event) {
            event.stopPropagation();
            });
          }
        }

    }), {
     // 'element' : $('sitegroup_profile_photo_anchor').getParent()
    });
  }  
</script>
<div class="sitegroup_viewgroups_head">
	<?php echo $this->htmlLink($this->sitegroup->getHref(), $this->itemPhoto($this->sitegroup, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>
	  <?php $link =  $this->htmlLink($this->sitegroup->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Albums')) ?>
	  <?php echo $this->translate('%1$s  &raquo; ' .  $link . ' &raquo;  %2$s',
	    $this->sitegroup->__toString(),
	    ( '' != trim($this->album->getTitle()) ? $this->album->getTitle() : '<em>' . $this->translate('Untitled') . '</em>')
	  ); ?>
	</h2>
</div>	
<!--RIGHT AD START HERE-->
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumview', 3) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)):?>
	<div class="layout_right" id="communityad_albumview">
		<?php
				echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumview', 3),"loaded_by_ajax"=>1,'widgetId'=>'group_albumview')); 			 
			?>
	</div>
<?php endif;?>
<!--RIGHT AD END HERE-->
<div class="layout_middle">


  <div class="sitegroup_album_options">
  
  <!--FACEBOOK LIKE BUTTON START HERE-->
  
   <?php  $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
        if (!empty ($fbmodule)) :
          $enable_facebookse = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse'); 
          if (!empty ($enable_facebookse) && !empty($fbmodule->version)) :
            $fbversion = $fbmodule->version; 
            if (!empty($fbversion) && ($fbversion >= '4.1.5')) { ?>
               <div class="mbot15">
                <?php echo Engine_Api::_()->facebookse()->isValidFbLike(); ?>
              </div>
            
            <?php } ?>
          <?php endif; ?>
   <?php endif; ?>
   
  <?php
	  $url = $this->url(array('action' => 'view','group_id' => $this->sitegroup->group_id, 'album_id' => $this->album_id, 'slug' => $this->album->getSlug(), 'tab' => $this->tab_selected_id), 'sitegroup_albumphoto_general', true);
    //Checking layout for user is enabled or not.
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);

    //Getting the tab id.
    $tab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.photos-sitegroup', $this->album->group_id, $layout);
    ?>
		<!--  Start: Suggest to Friend link show work -->
		<?php if( !empty($this->albumSuggLink) ): ?>				
			<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'suggestion', 'controller' => 'index', 'action' => 'popups', 'sugg_id' => $this->album->album_id, 'sugg_type' => 'group_album'), $this->translate('Suggest to Friends'), array(
					'class'=>'buttonlink  icon_group_friend_suggestion smoothbox')) ?>
		<?php endif; ?>					
		<!--  End: Suggest to Friend link show work -->
    <?php if($this->album_count > 1):?>
	  	<?php echo $this->htmlLink(array('route' => 'sitegroup_albumphoto_general', 'action' => 'view-album','group_id' => $this->album->group_id, 'slug' => $this->album->getSlug(),'tab' => $this->tab_selected_id), $this->translate('View Albums'), array(
	      'class' => 'buttonlink sitegroup_icon_photos_manage'
	    )) ?>
    <?php endif;?>
		<?php if ($this->upload_photo == 1):?>
	    <?php echo $this->htmlLink(array('route' => 'sitegroup_photoalbumupload','album_id' => $this->album_id, 'group_id' => $this->sitegroup->group_id, 'tab' => $this->tab_selected_id), $this->translate('Add More Photos'), array(
	      'class' => 'buttonlink sitegroup_icon_photos_new'
	    )) ?>
		<?php endif;?>
      <?php if ($this->can_edit):?>
		<?php if ($this->total_images):?> 
			<?php echo $this->htmlLink(array('route' => 'sitegroup_albumphoto_general', 'action' => 'edit-photos', 'album_id' => $this->album_id, 'group_id' => $this->sitegroup->group_id, 'slug' => $this->album->getSlug(),'tab' => $this->tab_selected_id), $this->translate('Manage Photos'), array(
					'class' => 'buttonlink sitegroup_icon_photos_manage'
				)) ?>
     <?php endif;?>
			<?php echo $this->htmlLink(array('route' => 'sitegroup_albumphoto_general', 'action' => 'edit', 'album_id' => $this->album_id, 'group_id' => $this->sitegroup->group_id, 'slug' => $this->album->getSlug(), 'tab' => $tab_id), $this->translate('Edit Album'), array(
				'class' => 'buttonlink sitegroup_icon_photos_settings', 'onclick' => 'editalbum(this);return false'
			)) ?>	
	   <?php if($this->default_value != 1):?>
		    <?php echo $this->htmlLink(array('route' => 'sitegroup_albumphoto_general', 'action' => 'delete','album_id' => $this->album_id, 'group_id' => $this->sitegroup->group_id, 'slug' => $this->album->getSlug(), 'tab' => $this->tab_selected_id), $this->translate('Delete Album'), array(
		      'class' => 'buttonlink sitegroup_icon_photos_delete', 'onclick' => 'editalbum(this);return false'
		    )) ?>
			<?php endif;?>
		<?php endif;?>
  </div>
	<?php if($this->total_images > $this->photos_per_group) :?>
 		<div class="sitegroup_profile_album_paging">
		  <?php $next_group = $this->currentGroupNumbers+1; ?>
		  <?php $previous_group = $this->currentGroupNumbers-1; ?>
		  <?php $maxgroups = $this->maxgroup;?>
		  <?php if($this->maxgroup >= $next_group) :?>
			  <div id="user_group_members_next">
			        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Last') , array(
			          'onclick' => "paginateGroupPhotos('$maxgroups', '$url')",
			        )); ?>
				</div>				
				<div id="user_group_members_next">
				  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
				    'onclick' => "paginateGroupPhotos('$next_group', '$url')",
				  )); ?>
				</div>
			<?php endif; ?>
			<div class="paging_count">
	 			<?php foreach($this->grouparray as $values) :
					 if($values['link'] == 1) { 	
					 	 echo $values['group'] ?>
					 <?php  }
					 else { ?>
					   <a href='javascript:void(0);' onclick="paginateGroupPhotos('<?php echo $values['group'] ?>', '<?php echo $url ?>')"><?php echo $values['group'] ?></a>
					 	  <?php
					 }
	  		endforeach; ?> 
			</div>			
 			<?php if($this->pstart != $this->currentGroupNumbers):?>
				<?php if($this->currentGroupNumbers != $this->pstart+1): ?>
					<div id="user_group_members_previous">
			    	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Prev') , array(
			          'onclick' => "paginateGroupPhotos('$previous_group', '$url')",
			        )); ?>
				 	</div> 
				<?php endif; ?>
  	    <div id="user_group_members_previous">
			    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('First') , array(
			      'onclick' => "paginateGroupPhotos('$this->pstart', '$url')",
			    )); ?>
				</div> 
		  <?php endif; ?>
		</div>  
	<?php endif; ?>
	<?php if($this->total_images) :?>
		<div class="sitegroup_album_box" id="album_backgroundimage">
		  <ul class="thumbs thumbs_nocaptions">
		    <?php foreach( $this->photos as $photo ): ?>
		      <li id="thumbs-photo-<?php echo $photo->photo_id ?>">	         
            <?php //if(!$this->showLightBox):?>
<!--              <a class="thumbs_photo" href="<?php //echo $photo->getHref(); ?>" title="<?php //echo $photo->title;?>">
                <span style="background-image: url(<?php //echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
              </a>-->
              <?php //else:?>            
                <a href="<?php echo $photo->getHref(); ?>"  <?php if(SEA_SITEGROUPALBUM_LIGHTBOX) :?> onclick ='openSeaocoreLightBox("<?php echo $photo->getHref(); ?>");return false;' <?php endif;?> class="thumbs_photo">               
                  <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
                </a>
            <?php //endif; ?>
		      </li>
		    <?php endforeach;?>
		  </ul>
		</div> 
  <?php endif; ?>

	<?php echo $this->action("list", "comment", "core", array("type"=>"sitegroup_album", "id"=> $this->album->album_id)); ?>
</div>