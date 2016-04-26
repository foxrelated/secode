<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: thirdpagedisplay.tpl 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="headline">
	<h2><?php echo $this->translate('My 3rd Level Friends');?></h2>
	<div class='tabs'>
	  <?php
	    // Render the menu
	    echo $this->navigation()
	      ->menu()
	      ->setContainer($this->navigation)
	      ->render();
	  ?>
	</div> 
</div>	 
<div class='layout_middle'>
	<div id="friend_list_box">
 		<?php if($this->third_degree_fetch_record != NULL){ 
 		  foreach( $this->third_degree_fetch_record as $third_level_display ): ?>
	    <div class="friend_list">
				<div class="photo">
	     		<?php echo $this->htmlLink($third_level_display->getHref(), $this->itemPhoto($third_level_display, 'thumb.icon'), array('class' => 'popularmembers_thumb')) ?>
	      </div>
		    <div class="friends_option">
		     	<?php echo $this->userFriendship($third_level_display)?>
		     	<a style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Messages/externals/images/send.png);" class="buttonlink" href="<?php echo $this->base_url; ?>/messages/compose/to/<?php echo $third_level_display->user_id ?>"><?php echo $this->translate('Send Message'); ?></a>
		    </div> 
	      <div class='user_details'>
		    	<div class='name'>
		      	<?php echo $this->htmlLink($third_level_display->getHref(), $third_level_display->getTitle()) ?>
		      </div>  
		     	<div class='friends'>
		      	<?php echo $this->translate(array('%s friend', '%s friends', $third_level_display->member_count),$this->locale()->toNumber($third_level_display->member_count)) ?>
		      </div>
		    </div>	
		  </div>
	 	<?php endforeach;
	 	echo $this->paginationControl($this->third_degree_fetch_record);
 		} 
 		else { 
 			echo '<div class="tip"><span>' . $this->translate('You do not have any 3rd Level Friends.') . '</span></div>'; }?>
	</div> 
</div>