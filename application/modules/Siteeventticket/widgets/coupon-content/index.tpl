<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $postedDate = $this->timestamp(strtotime($this->siteeventcoupon->creation_date)); ?>
<?php $viewer_id = $this->viewer->getIdentity();?>
<?php if(!empty($viewer_id)):?>
	<?php $oldTz = date_default_timezone_get();?>
	<?php date_default_timezone_set($this->viewer->timezone);?>
<?php endif;?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Siteeventticket/externals/styles/style_siteeventcoupon.css'); ?>

<div class="siteevent_viewevents_head">
	<?php echo $this->htmlLink($this->siteevent->getHref(), $this->itemPhoto($this->siteevent, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>
        <?php echo $this->siteevent->__toString() ?>
        <?php echo $this->translate('&raquo;');?>
        <?php echo $this->htmlLink($this->siteevent->getHref(array('tab' => $this->eventProfileCouponTabId)), $this->translate('Coupons')) ?>
        <?php echo $this->translate('&raquo;');?>
        <?php echo $this->siteeventcoupon->title ?>
	</h2>
</div>

<div class="siteevent_coupon_view">
   
	<div class="siteevent_coupon_block">
		<div class="siteevent_coupon_photo">
			<?php echo $this->itemPhoto($this->siteeventcoupon, 'thumb.normal'); ?>
		</div>
		<div class="siteevent_coupon_details">
			<div class="siteevent_coupon_title">
				<h3><?php echo $this->siteeventcoupon->title;?></h3>
			</div>
			
            <div class="siteevent_coupon_date">
                <?php if(in_array('startdate', $this->statistics)):?>
                    <div class="siteevent_coupon_date">
                        <span><?php echo $this->translate('Start date:'); ?></span>
                        <span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->siteeventcoupon->start_time)))?></span>
                    </div>
                <?php endif;?>
        
                <?php if(in_array('enddate', $this->statistics)):?>
                    <div class="siteevent_coupon_date">
                        <span><?php echo $this->translate('End date:'); ?></span>
                        <?php if($this->siteeventcoupon->end_settings == 1):?>
                            <span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->siteeventcoupon->end_time))) ?></span>
                        <?php else:?>
                            <span><?php echo $this->translate('Never Expires');?></span>
                        <?php endif;?>
                    </div>
                <?php endif;?>
			</div>
          
            <div class="siteevent_coupon_stats">
                <?php if(in_array('couponcode', $this->statistics)):?>
					<span class="siteevent_coupon_stat siteeventcoupon_code siteeventcoupon_tip_wrapper">
						<span class="siteeventcoupon_tip">
							<span><?php echo $this->translate('Select and Copy Code to use');?></span>
							<i></i>
						</span>
						<input type="text" value="<?php echo $this->siteeventcoupon->coupon_code;?>" class="siteeventcoupon_code_num" onclick="this.select()" readonly>
					</span>
				<?php endif;?>
        
                <?php if(in_array('discount', $this->statistics)):?>
                    <span class="siteevent_coupon_stat siteevent_coupon_discount siteeventcoupon_tip_wrapper">
                        <span class="siteeventcoupon_tip">
                            <span><?php echo $this->translate('Coupon Discount Value');?></span>
                            <i></i>
                        </span>
                        <?php if(!empty($this->siteeventcoupon->discount_type)):
                            $priceStr = Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->siteeventcoupon->discount_amount);?>
                            <span class="discount_value"><?php echo $priceStr; ?></span>&nbsp;&nbsp;
                        <?php else:?>
                            <span class="discount_value"><?php echo $this->siteeventcoupon->discount_amount . '%';?></span>&nbsp;&nbsp;
                        <?php endif;?>
                    </span>	
                <?php endif;?>
                
                <?php $today = date("Y-m-d H:i:s"); ?>     
                <?php if(in_array('expire', $this->statistics) && !empty($this->siteeventcoupon->end_settings) && $this->siteeventcoupon->end_time < $today):?>
                    <span class="siteeventcoupon_stat siteeventcoupon_left fright">
                      <b><?php echo $this->translate('Expired');?></b>&nbsp;&nbsp;&nbsp;
                    </span>
                <?php endif;?>
		  	</div>
     
            <div class="siteevent_coupon_stats">
                <?php echo nl2br($this->siteeventcoupon->description);?>
            </div>	  
        </div>
	</div>  
    
    <div class="siteeventcoupon_view_stat seaocore_txt_light">
        <?php echo $this->translate('Posted');?> <?php echo $postedDate;  ?>
       <span class="coupon_views">- <?php echo $this->translate(array('%s comment', '%s ', $this->siteeventcoupon->comments()->getCommentCount()),$this->locale()->toNumber($this->siteeventcoupon->comments()->getCommentCount())) ?>	-  
        <?php echo $this->translate(array('%s view', '%s views', $this->siteeventcoupon->view_count ), $this->locale()->toNumber($this->siteeventcoupon->view_count )) ?>
       - <?php echo $this->translate(array('%s like', '%s likes', $this->siteeventcoupon->likes()->getLikeCount()),$this->locale()->toNumber($this->siteeventcoupon->likes()->getLikeCount())) ?>
       </span>
    </div>
  
    <div class='siteeventcoupon_view_options' style="margin-bottom:15px;">

        <?php if($this->can_create_coupons):?>
            <?php echo $this->htmlLink(array('route' => 'siteeventticket_coupon', 'action' => 'create','event_id' => $this->siteevent->event_id, 'tab' => $this->tab_selected_id), $this->translate('Add a Coupon'), array('class' => 'buttonlink seaocore_icon_create')) ?>&nbsp; | &nbsp;
            <?php echo $this->htmlLink(array('route' => 'siteeventticket_coupon','action' => 'edit', 'coupon_id' => $this->siteeventcoupon->coupon_id, 'tab'=>$this->tab_selected_id, "event_id" => $this->siteevent->event_id), $this->translate('Edit Coupon'), array('class' => 'buttonlink seaocore_icon_edit')) ?>&nbsp; | &nbsp;
            <?php echo $this->htmlLink(array('route' => 'siteeventticket_coupon', 'action' => 'delete','coupon_id' => $this->siteeventcoupon->coupon_id, 'tab'=>$this->identity_temp, "event_id" => $this->siteevent->event_id), $this->translate('Delete Coupon'), array('class' => 'smoothbox buttonlink seaocore_icon_delete')) ?>&nbsp; | &nbsp;
            <?php if (!empty($this->siteeventcoupon->status)): ?>
                <a class='buttonlink seaocore_icon_disapproved' href='javascript:void(0)' onclick='Smoothbox.open("<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'coupon','action' => 'enable-disable', 'coupon_id' => $this->siteeventcoupon->coupon_id, "event_id" => $this->siteevent->event_id), 'default', true); ?>")'><?php echo $this->translate('Disable Coupon'); ?></a>	
            <?php else: ?>
                <a class='buttonlink seaocore_icon_approved' href='javascript:void(0)' onclick='Smoothbox.open("<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'coupon','action' => 'enable-disable', 'coupon_id' => $this->siteeventcoupon->coupon_id, "event_id" => $this->siteevent->event_id), 'default', true); ?>")'><?php echo $this->translate('Enable Coupon'); ?></a>
            <?php endif; ?>&nbsp; | &nbsp;          
        <?php endif; ?>   

        <?php echo $this->htmlLink(array('route' => 'siteeventticket_coupon','action' => 'print', 'coupon_id' => $this->siteeventcoupon->coupon_id, "event_id" => $this->siteevent->event_id), $this->translate('Print Coupon'), array('target' => '_blank',' class' => 'buttonlink icon_siteeventticket_printer')) ?>&nbsp; | &nbsp;

        <?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'siteeventticket_coupon', 'id' => $this->siteeventcoupon->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox buttonlink seaocore_icon_share')); ?>&nbsp; | &nbsp;

        <?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' =>  $this->siteeventcoupon->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox buttonlink seaocore_icon_report')); ?>
    
        <?php if(!empty($viewer_id)) : ?>
            &nbsp; | &nbsp;
            <?php echo $this->htmlLink(array('route' => 'siteeventticket_coupon', 'action' => 'resend-coupon', 'coupon_id' => $this->siteeventcoupon->coupon_id, "event_id" => $this->siteevent->event_id),Zend_Registry::get('Zend_Translate')->_('Email Me'),array('class' => 'smoothbox buttonlink icon_siteeventticket_invite')); ?>
        <?php endif; ?>
    </div>

	<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listComment.tpl';?>

</div>

<?php if(!empty($viewer_id)):?>
	<?php date_default_timezone_set($oldTz);?>
<?php endif;?>

<script type="text/javascript">
    var share_coupon =  '<?php echo $this->share_coupon;?>';
    var coupon_id =  '<?php echo $this->siteeventcoupon->coupon_id;?>';

    if(share_coupon != '') {
        var url = en4.core.baseUrl + 'activity/index/share/type/siteeventticket_coupon/id/'+ coupon_id +'/format/smoothbox';
        Smoothbox.open(url);
    }
</script>