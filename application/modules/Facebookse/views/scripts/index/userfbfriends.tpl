<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: userfbfriends.tpl 6590 2010-11-25 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<div class="headline">
   <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>
<?php $sitename = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');?>
<div class='layout_middle'>
	<h2><?php echo $this->translate('Facebook Friends on');?> <?php echo $sitename;?></h2>
	<div class="facebookfriend_list_box">
		<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
			<?php foreach ($this->paginator as $value) {
					$user_facebook_photo = '<img src="http://graph.facebook.com/'.$value->facebook_uid . '/picture">';
					$member = $this->item('user', $value->user_id);
			?>
		
				<div class="facebookfriend_list" style="float:<?php echo $this->cycle(array("left", "right")) ->next()?>">
					<div class="photo">
						<?php echo $this->htmlLink(
							$value->getHref(), $user_facebook_photo );
						?>
					</div>
					<div class='user_details'>
						<div class='name'>
							<?php echo $this->htmlLink(
								$value->getHref(),
								$value->displayname,
								array('class' => 'users_browse_photo')
							);?>
						</div>  
					</div>
					<div class="friends_option">
						<?php if( $this->viewer()->getIdentity() && !$this->viewer()->isSelf($member) ): ?>
								<?php if( !$this->viewer()->membership()->isMember($member) ): ?>
										<?php echo $this->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'add', 'user_id' => $member->getIdentity()), $this->translate('Add Friend'), array(
											'class' => 'buttonlink smoothbox icon_friend_add'
										)) ?>
								<?php else: ?>
									 <?php echo $this->htmlLink(array('route' => 'user_extended', 'controller'=>'friends', 'action' => 'remove', 'user_id' => $member->getIdentity()), $this->translate('Remove Friend'), array(
											'class' => 'buttonlink smoothbox icon_friend_remove'
										)) ?>
								<?php endif; ?>
								<a style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Messages/externals/images/send.png);" class="buttonlink" href="<?php echo $this->baseUrl (); ?>/messages/compose/to/<?php echo $member->getIdentity() ?>"> <?php echo $this->translate('Send Message'); ?> </a>
						<?php endif; ?>
					</div>
				</div>	 	
		
	
		<?php } ?>
	
		<?php else : ?>
			<div class="tip">
				<span>
					<?php echo $this->translate('Currently there are no friends which matches your criteria.');?>
				</span>
			</div>   
		<?php endif; ?>
		<div class='browse_nextlast' style="margin-top:15px;">
			<?php echo $this->paginationControl($this->paginator); ?>
		</div>
		<?php
		$this->headScript()
			 ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Facebookse/externals/scripts/facebookse.js')
		?>
	</div>
</div>
