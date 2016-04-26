<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _my-friends-likes.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $flag = 0; ?>
<?php $this->headScriptSM()->prependFile($this->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/scripts/sitemobile/core.js'); ?>
<?php //if(empty($this->isajaxrequest)):?>
  <div class="sm-content-list" id="dynamic_app_info_<?php echo $this->urlAction;?>">
<?php //endif;?>


<?php if( !empty($this->enablemodules) ): ?>
	<select name="like_view" onchange="show_app_likes('', this, url, '<?php echo $this->urlAction;?>');" >
		<?php foreach ($this->enablemodules as $module) : ?>
			<?php $moduleTitle = null;
				$mixsettingstable = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' );
				$sub_status_select = $mixsettingstable->fetchRow(array('resource_type = ?'=> $module));
				$moduleTitle = $sub_status_select->item_title;
			?>
			<?php  if (!empty($moduleTitle)) { ?>
				<option value="<?php echo $module;?>" <?php if($this->activetab == $module):?> selected="selected" <?php endif;?> ><?php echo $this->translate("$moduleTitle") ?></option> 
			<?php } ?>
		<?php endforeach;?>
	</select>
	<?php if($this->paginator->count() > 0):?>
		<?php $base_url =  Zend_Controller_Front::getInstance()->getBaseUrl();?>
			<ul data-role="listview" data-icon="none" >
				<?php foreach($this->paginator as $row_mix_fetch):?>
					<?php	 $href = '';?>
					<?php  
						if ($row_mix_fetch->resource_type == 'sitepage_photo' || $row_mix_fetch->resource_type == 'sitepagenote_photo' || $row_mix_fetch->resource_type == 'list_photo' || $row_mix_fetch->resource_type == 'recipe_photo' || $row_mix_fetch->resource_type == 'group_photo' || $row_mix_fetch->resource_type == 'event_photo' || $row_mix_fetch->resource_type == 'album_photo' || $row_mix_fetch->resource_type == 'sitebusiness_photo' || $row_mix_fetch->resource_type == 'sitebusinessnote_photo' )
						{
							$href = $row_mix_fetch->getHref() . '/type/' . 'like_count'.'/urlaction/'. $this->urlAction;       
							if(SEA_DISPLAY_LIGHTBOX) {
								$flag = 1;
							} 
							else {
								if(SEA_LIKE_LIGHTBOX) {
									$flag = 1;
								}
							}
						}	
					?>
        <li>
         <a href="<?php echo $row_mix_fetch->getHref(); ?>" <?php if($flag):?> class="thumbs_photo" data-linktype="photo-gallery"<?php endif;?>>
           <?php if ($row_mix_fetch->resource_type == 'blog') :?>   
             <?php echo $this->itemPhoto($row_mix_fetch->getOwner(), 'thumb.icon');?>
           <?php else:?>
             <?php echo $this->itemPhoto($row_mix_fetch, 'thumb.icon');?>
           <?php endif;?>
           <div class="ui-list-content">
							<h3><strong><?php echo $row_mix_fetch->getTitle(); ?></strong></h3>
							<?php $num_of_like = Engine_Api::_()->getApi('like', 'seaocore')->likeCount($row_mix_fetch->resource_type, $row_mix_fetch->resource_id ); ?>
							<p class="sm-ui-lists-action"><?php echo $this->translate(array('%s like', '%s likes', $num_of_like), $this->locale()->toNumber($num_of_like)) ; ?>
           </div>
         </a>
        </li>
			<?php endforeach;?>
			<?php if ($this->paginator->count() > 1): ?>
				<?php 
					echo $this->paginationAjaxControl(
								$this->paginator, 0, "dynamic_app_info_$this->urlAction", array('url' => $this->url(array('module' => 'sitelike', 'controller' => 'index', 'action' => $this->urlAction), 'default', true), 'isajax' =>1, 'resource_type' => $this->activetab ));
				?>
			<?php endif; ?>
	<?php else:?>
		<div class="tip">
			<span><?php echo $this->translate('No items could be found.') ?></span>
		</div>
	<?php endif;?>
	<?php else:?>
		<div class="tip" style="margin:10px 0 0 270px;"><span><?php echo $this->translate('No items could be found.') ?></span></div>
	<?php endif;?>
</div>