<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editphotos.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitegroupalbum/externals/styles/style_sitegroupalbum.css');
?>
<?php $i = 0; ?>
<script type="text/javascript">
  var paginateGroupPhotos = function(groups, url) {  	

   $('subcategory_backgroundimage').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/loader.gif" /></center>';

  	var is_ajax = 1;
    var url = url;
   url = url + '/groups/' + groups;
   if (history.pushState)
    history.pushState( {}, document.title, url );
   else{
    window.location.hash = photoUrl;
   }
     sm4.core.request.send(new Request.HTML({
      'url' : url,
      'method' : 'get',
         'data' : {
        'format' : 'html', 
        'is_ajax' :  is_ajax,
         'groups' : groups       
      }  
    }), {
      'element' : $('sitegroup_profile_photo_anchors').getParent()
    });
  }
</script>

<a id="sitegroup_profile_photo_anchors" style="position:absolute;"></a>



<div class="sitegroup_viewgroups_head">
	<?php echo $this->htmlLink($this->sitegroup->getHref(), $this->itemPhoto($this->sitegroup, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>	
	  <?php echo $this->sitegroup->__toString() ?>	
	  <?php echo $this->translate('&raquo; ');?>
    <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Albums')) ?>
    <?php echo $this->translate('&raquo; ');?>
    <?php echo $this->album->getTitle();?>
  </h2>  
</div>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumeditphoto', 5) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)):?>
	<div class="layout_right" id="communityad_editphotos">
		<?php
				echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumeditphoto', 3),"loaded_by_ajax"=>1,'widgetId'=>'group_editphotos')); 			 
			?>
	</div>
<?php endif;?>
<div class="layout_middle">
<h3><?php echo $this->translate('Edit Photos');?></h3>
<p><?php echo $this->translate('Here, you can edit photos of this album.');?></p>
<br />
<?php $url = $this->url(array('action' => 'edit-photos','group_id' => $this->group_id, 'album_id' => $this->album_id, 'slug' => $this->album->getSlug(), 'tab' => $this->tab_selected_id), 'sitegroup_albumphoto_general', true);?>
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
				   <a href='javascript:void(0);' onclick="paginateGroupPhotos('<?php echo $values['group'] ?>','<?php echo $url ?>')"><?php echo $values['group'] ?></a>
				 	  <?php
				 }
			 	endforeach; ?>
		 	</div> 	
	 		<?php if($this->pstart != $this->currentGroupNumbers):?>
				<?php if($this->currentGroupNumbers != $this->pstart+1): ?>
					<div id="user_group_members_previous">
			    	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous') , array(
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
	<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" id="subcategory_backgroundimage" class="sitegroup_album_box">
	  <?php echo $this->form->album_id; ?>
	  <ul class='sitegroups_editphotos'>
	    <?php foreach( $this->photos as $photo ): ?>
	      <li>
	        <div class="sitegroups_editphotos_photo">	      
	          <?php echo $this->itemPhoto($photo, 'thumb.normal')  ?>
	        </div>
	        <div class="sitegroups_editphotos_info">
	          <?php 
	            $key = $photo->getGuid();
	            echo $this->form->getSubForm($key)->render($this);	            
	          ?>
				    <div class="albums_editphotos_cover">
				    	<input type="radio" name="cover" value="<?php echo $photo->file_id ?>" 
	          <?php if(empty($this->album->photo_id) && $i==0): $i = 1;?>	          
	            checked="checked"
	          <?php else: ?>
	          <?php if( $this->album->photo_id == $photo->file_id ): ?> 
	             checked="checked"
	          <?php endif;?>
	           <?php endif;?>
	           />
				    </div>
				    <div class="albums_editphotos_label">
				    	<label><?php echo $this->translate('Album Cover');?></label>
				    </div>

            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')): ?>
							<div class="albums_editphotos_cover">
								<input type="radio" name="group_cover" value="<?php echo $photo->file_id ?>" 
							<?php if(empty($this->sitegroup->group_cover) && $i==0): $i = 1;?>
								checked="checked"
							<?php else: ?>
							<?php if( $this->sitegroup->group_cover == $photo->file_id ): ?> 
								checked="checked"
							<?php endif;?>
							<?php endif;?>
							/>
							</div>
							<div class="albums_editphotos_label">
								<label><?php echo $this->translate('Make Group Cover');?></label>
							</div>
				    <?php endif;?>
				    
	        </div>
	      </li>
	    <?php endforeach; ?>
	  </ul><br />
	  <div class="form-wrapper" >
	  	<div class="form-element">
			  <button type="submit" id="submit" name="submit"><?php echo $this->translate('Save Changes');?></button> <?php echo $this->translate(' or ');?><a onclick="javascript:history.go(-1);return false;" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate('cancel');?></a>
	    </div>
	 </div>
	  <br />
	</form>
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
						<a href='javascript:void(0);' onclick="paginateGroupPhotos('<?php echo $values['group'] ?>', '<?php echo $url?>')"><?php echo $values['group'] ?></a>
					<?php
					}
	  		endforeach; ?>
	  	</div>	
	 		<?php if($this->pstart != $this->currentGroupNumbers):?>
				<?php if($this->currentGroupNumbers != $this->pstart+1): ?>
					<div id="user_group_members_previous">
			    	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous') , array(
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
</div>