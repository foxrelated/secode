<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: contact.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript" >
  var submitformajax = 1;
</script>

<?php if (empty($this->is_ajax)) : ?>
<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
	<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>

  <div class="layout_middle">
    <?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/edit_tabs.tpl'; ?>
    <div class="sitegroup_edit_content">
      <div class="sitegroup_edit_header">
        <a href='<?php echo $this->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($this->sitegroup->group_id)), 'sitegroup_entry_view', true) ?>'><?php echo $this->translate('VIEW_GROUP'); ?>
        </a>
        <h3><?php echo $this->translate('Dashboard: ') . $this->sitegroup->title; ?></h3>
      </div>

      <div id="show_tab_content">
      <?php endif; ?> 

			<ul class="seaocore_browse_list">
				<?php foreach($this->wishlistGroupDatas as $wishlistGroupData): ?>
					<?php 
						$wishlist_id = $wishlistGroupData['wishlist_id'];
						$wishlist = Engine_Api::_()->getItem('sitegroupwishlist_wishlist', $wishlist_id);
						$owner = Engine_Api::_()->getItem('user', $wishlist->owner_id);
					?>

					<li>
						<?php echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon')) ?>

						<div class='seaocore_browse_list_options'>
							<?php echo $this->htmlLink(array('route' => 'sitegroupwishlist_view', 'wishlist_id' => $wishlist_id), $this->translate('View Wishlist'), array('class' => 'buttonlink notification_type_sitegroup_suggested')) ?>
						</div>

					<div class='seaocore_browse_list_info'>
						<div class='seaocore_browse_list_info_title'>
							<h3><?php echo $this->htmlLink($wishlist->getHref(), $wishlist->title) ?></h3>

						<?php echo $this->translate('Created %s by %s', $this->timestamp($wishlist->creation_date), $wishlist->getOwner()->toString()) ?>
						<br />
						
						<?php
							$auth = Engine_Api::_()->authorization()->context;
							$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
							$perms = array();
							foreach( $roles as $roleString ) {
								$role = $roleString;
								if( $auth->isAllowed($wishlist, $role, 'view') ) {
									$perms['auth_view'] = $roleString;
								}
							}
							
							switch ($perms['auth_view']) {
								case 'owner':
									$visibility = 'Just Me';
									break;
								case 'owner_member':
									$visibility = 'Only Friends';
									break;
								case 'owner_member_member':
									$visibility = 'Friends of Friends';
									break;
								case 'owner_network':
									$visibility = 'Friends and Networks';
									break;
								case 'registered':
									$visibility = 'All Registered Members';
									break;
								case 'everyone':
									$visibility = 'Everyone';
									break;
							}
						?>
						<?php echo $this->translate('Visible to : %s', "$visibility") ?>
						</div>
					</div>

					</li>

				<?php endforeach; ?>
			</ul>

      <br />
      <div id="show_tab_content_child">
      </div>

      <?php if (empty($this->is_ajax)) : ?>
      </div>
    </div>
  </div>
      </div>
  </div>
<?php endif; ?>