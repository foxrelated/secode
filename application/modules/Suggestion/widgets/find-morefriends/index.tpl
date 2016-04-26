<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="generic_suggestion_widget">
	<div class="findfriend_block_title">
	<?php echo $this->htmlLink(array('route' => 'friends_suggestions_viewall'), $this->viewer_displayname . ', ' .  $this->translate('More Friends are Waiting!') ) ;
	$inviteurl =(_ENGINE_SSL ? 'https://' : 'http://' )
	                    . $_SERVER['HTTP_HOST'].$this->url(array(),  'friends_suggestions_viewall', true);
	?>
	</div>
	<div class="findfriend_block_icons clr">
		<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/google32.png', '',array('title' => 'Gmail')) ?>
		<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/yahoo32.png', '',array('title' => 'Yahoo')) ?>
		<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/windows32.png', '',array('title' => 'Windows Live')) ?>
		<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/facebook32.png', '',array('title' => 'Facebook')) ?>
		<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/twitter32.png', '',array('title' => 'Twitter')) ?>
		<?php //echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/findmorefriend_linkedin.png', '',array('title' => 'LinkedIn')) ?>
		<?php //echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/images/findmorefriend_aol.png', '',array('title' => 'Aol')) ?>
	</div>
	<div class="clr" style="margin-bottom:5px;">
	   <?php echo $this->translate('Want to add your contacts as friends? Find them here.');?>
	</div>
	<div class="clr">		  		
		<button onclick="window.location.href='<?php echo $inviteurl;?>'" id="invitefriends" name="invitefriends"><?php echo $this->translate('Find Friends');?></button>
	</div>
</div>
