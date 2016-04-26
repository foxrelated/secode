<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/css.php?request=/application/modules/Sitegroupmember/externals/styles/style_sitegroupmember.css'); ?>
<?php
		$layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
		$tab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.photos-sitegroup', $this->sitegroup->group_id, $layout); ?>

<?php if(empty($this->isAjax)) :?>
	<div class='sitegroup_cover_wrapper' id="sitegroup_cover_photo">
<?php endif;?>

<?php if(!empty($this->isAjax)) :?>
  <?php if (!empty($this->sitegroup->group_cover)) : ?>
     <?php if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroupalbum' )) : ?>
			<div class="sitegroup_cover_photo">
				<?php echo $this->itemPhoto($this->photo, 'thumb.profile', '', array('align' => 'left')); ?>
			</div>
    <?php else : ?> 
			<?php $k=0; ?>
			<div class="sitegroup_members_cover_listing">
				<?php foreach ($this->paginator as $photo):
				$user = Engine_Api::_()->getItem('user', $photo->user_id); ?>
					<div class="sitegroup_members_cover_member"> 
						<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user->getOwner(), 'thumb.profile')) ?>
						<span class="sitegroup_members_cover_member_name"><a href="<?php echo $user->getHref() ?>" ><?php echo $user->getTitle() ?></a></span>
					</div>
					<?php $k++;?>
				<?php endforeach; ?>
			</div> 
    <?php endif; ?>
  <?php elseif(empty($this->sitegroup->group_cover)  && empty($this->show_member) && empty($this->paginatorCount)) : ?>
		<div class="sitegroup_cover_photo_empty"> </div>
  <?php elseif(!empty($this->show_member) || !empty($this->can_edit)) : ?>
		<?php $k=0; ?>
		<div class="sitegroup_members_cover_listing">
			<?php foreach ($this->paginator as $photo):
			$user = Engine_Api::_()->getItem('user', $photo->user_id); ?>
				<div class="sitegroup_members_cover_member"> 
					<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user->getOwner(), 'thumb.profile')) ?>
			    <span class="sitegroup_members_cover_member_name"><a href="<?php echo $user->getHref() ?>" ><?php echo $user->getTitle() ?></a></span>
				</div>
				<?php $k++;?>
			<?php endforeach; ?>
		</div> 
  <?php endif; ?>
  <?php if (!empty($this->can_edit) && (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroupalbum' ))) : ?>
		<div id="edit" class="sitegroup_cover_options">
      <ul>
        <li>
          <span class="sitegroup_cover_photo_btn sitegroup_icon_photos_settings"><?php echo $this->translate("Edit Cover Photo");?></span>
          <ul class="sitegroup_cover_options_pulldown">
          	<li>
            	<a href='<?php echo $this->url(array('group_id' => $this->sitegroup->group_id, 'album_id' => 0, 'tab' => $this->identity_temp), 'sitegroup_photoalbumupload', true)  ?>'  class="icon_sitegroup_photo_new"><?php echo $this->translate('Create an Album'); ?></a>
            </li>
            <li >
              <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab'=>$tab_id)),$this->translate('Choose from Album Photos'), array( ' class' => 'sitegroup_icon_photos_manage')); ?>
            </li>
            <?php if (!empty($this->sitegroup->group_cover)) : ?>
              <li>
                <?php echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'remove-coverphoto', 'group_id' => $this->sitegroup->group_id), $this->translate('Remove Cover Photo'), array( ' class' => 'smoothbox sitegroup_icon_photos_delete')); ?>
              </li>
            <?php endif; ?>
          </ul>
        </li> 
      </ul>
		</div>
  <?php endif; ?>
  <div class="clr"></div>
<?php endif;?>

<?php if(empty($this->isAjax)) :?>
	</div>
<?php endif;?>

<?php if(empty($this->isAjax)) :?>
	<script type="text/javascript">
		$('sitegroup_cover_photo').innerHTML = '<center><img src="application/modules/Sitegroup/externals/images/loader.gif" /></center>';
		en4.core.request.send(new Request.HTML({
			'url' : en4.core.baseUrl + 'widget/index/mod/sitegroup/name/group-cover-information-sitegroup',
			'data' : {
				'format' : 'html',
				'subject' : en4.core.subject.guid,
				'isAjax': 1,
        'show_member': '<?php echo $this->show_member ?>'
			}
		}), {
			'element' : $('sitegroup_cover_photo')
		});
	</script>
<?php endif;?>
