<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_print.css');?>

<link href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_print.css'?>" type="text/css" rel="stylesheet" media="print">

<div class="seaocore_print_store">
	<div class="seaocore_print_title">	
        <span class="left">
            <?php echo $this->siteeventcoupon->getTitle().$this->translate(' in ') .$this->siteevent->title; ?>
        </span>

        <span class="right">
            <?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title'));?>
        </span>
	</div>
    
	<div class='seaocore_print_profile_fields'>
		<div class="seaocore_print_photo">
			<?php echo $this->itemPhoto($this->siteeventcoupon, 'thumb.normal'); ?>  
			<div id="printdiv" class="seaocore_print_button">
				<a href="javascript:void(0);" style="background-image: url('<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteeventticket/externals/images/printer.png');" class="buttonlink" onclick="printData()" align="right"><?php echo $this->translate('Take Print') ?></a>
			</div>
		</div>
		<div class="seaocore_print_details">	      
			<h4>
				<?php echo $this->translate('Coupon Information') ?>
			</h4>
			<ul>
                <?php if(!empty($this->siteeventcoupon->start_time)) : ?>
                    <li>
                        <span><?php echo $this->translate('Start date:'); ?></span>
						<span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->siteeventcoupon->start_time))) ?></span>
                    </li>
                <?php endif;?>
                <li>
					<span><?php echo $this->translate('End date:'); ?></span>
					<?php if($this->siteeventcoupon->end_settings == 1):?>
						<span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->siteeventcoupon->end_time))) ?></span>
					<?php else:?>
						<span><?php echo $this->translate('Never Expires') ?></span>
					<?php endif;?>
				</li>
                <?php if(!empty($this->siteeventcoupon->coupon_code)) : ?>
                    <li>
                        <span><?php echo $this->translate('Coupon Code:'); ?></span>
						<span><?php echo $this->translate( $this->siteeventcoupon->coupon_code) ?></span>
                    </li>
                <?php endif;?>
				<li>
					<span><?php echo $this->translate('Description:'); ?></span>
					<span><?php echo $this->siteeventcoupon->description ?></span>
				</li>
			</ul>        
		</div>	
	</div>
</div>

<script type="text/javascript">
	function printData() {
		document.getElementById('printdiv').style.display = "none";

		window.print();
		setTimeout(function() {
			document.getElementById('printdiv').style.display = "block";
		}, 500);
	}
</script>
