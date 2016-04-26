<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: viewfriendsuggestion.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
	var processFlag = 0;
	var processFlagDiv = '';
</script>

<?php
	  
		  include_once(APPLICATION_PATH ."/application/modules/Seaocore/views/scripts/_invite.tpl");
	?>

	<?php
	    $show_link = Engine_Api::_()->getApi('Invite', 'Seaocore')->canInvite();
      if ($show_link || !Engine_Api::_()->user()->getViewer()->getIdentity())
		  include_once(APPLICATION_PATH ."/application/modules/Suggestion/views/scripts/_friendInviter.tpl");
	?>

	<!-- Friend show code from here -->
	<?php if( empty($getSignupAction) ):  ?>
	<div class="suggestion_invite_userlist">
		<div>
			<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/add.png', '', array('style'=>'float:left;margin-right:5px;')) ?>	
			<b><?php echo $this->translate("People You May Know"); ?></b>
		</div>
		<?php
				$modCount = $numOfContent = @COUNT($this->modArray);
				if( $modCount > 1 ){ 
					$numOfContent = @COUNT($this->modArray) - 1; 
				}

				if( !empty($numOfContent) ){
					$recommendedEndFlag = $numOfContent;
					$recommendedFlag = 0;
					foreach( $this->modArray as $modArray ) {
						echo $this->partial('application/modules/Suggestion/widgets/templatePartial.tpl', array( 'modInfo' => $modArray, 'recommendedStartFlag' => $recommendedFlag, 'recommendedEndFlag' => $recommendedEndFlag, 'tempFindFriendFlag' => 1 ));
						$recommendedFlag++;
					}
				}else {
					echo "<div class='tip' style='margin-top:10px;'><span>" . $this->translate("You do not have any more friend suggestions.") . "</span></div>";
				}
		?>
	</div>
	<div class="suggestion_inviter" style="border-bottom:none;">
		<div class="header">	
			<div class="title">	
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/search.png', '') ?>
				<?php echo $this->translate('Search for People'); ?>
			</div>
		</div>
		<div class="people-search">
			<form action="<?php echo $this->url(array(), 'user_extended', true); ?>">
			<input type="text" name="displayname" id="displayname">
			<button type='submit'><?php echo $this->translate("Search"); ?></button>
			</form>
		</div>
	</div>	  
	<?php endif; ?>
	<!-- Friend show code end here -->
	<?php 
    if ($show_link || !Engine_Api::_()->user()->getViewer()->getIdentity())
		include_once(APPLICATION_PATH ."/application/modules/Suggestion/views/scripts/_friendInviteContent.tpl");
	?>
</div>
<script type="text/javascript">
<?php if ($this->success_fbinvite): ?>

   if ($('id_nonsite_success_mess'))
      $('id_nonsite_success_mess').style.display = 'block';
   
<?php endif;?>   
  
window.addEvent('domready', function () { 
  if ($('suggestion_invite_friends'))
  $('suggestion_invite_friends').addClass('active');

}); 
</script>