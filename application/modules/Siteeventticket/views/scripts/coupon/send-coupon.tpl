<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: send-coupon.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup">
  <?php $email =  "<b>".Engine_Api::_()->user()->getViewer()->email."</b>"; ?>
	<h3><?php echo $this->translate("Coupon Emailed");?></h3>
	<div class="clr o_hidden mtop10">
		<div class="fleft mright10">
			<?php echo "<img src='". $this->layout()->staticBaseUrl . "application/modules/Siteeventticket/externals/images/send.png' alt='' class='fleft' />" ?>
		</div>
		<div>
			<?php echo $this->translate("We've emailed you this coupon at %s. Please check spam folder if you do not see the coupon in your inbox.", $email);?>
		</div>
	</div>	
	<div class="clr" style="margin-top:10px;">
		<button class="fright" onclick="javascript:window.parent.Smoothbox.close();" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate('Okay'); ?></button>
	</div>	
</div>