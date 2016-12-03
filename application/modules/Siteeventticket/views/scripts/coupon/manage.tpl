<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  
  Asset.css('<?php echo $this->layout()->staticBaseUrl
	    . 'application/modules/Siteeventticket/externals/styles/style_siteeventcoupon.css'?>');
</script>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
	    . 'application/modules/Siteeventticket/externals/styles/style_siteeventcoupon.css');
?>

<?php $oldTz = date_default_timezone_get();?>
<?php date_default_timezone_set($this->viewer->timezone);?>

<?php if (!$this->only_list_content): ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
    <div class="siteevent_dashboard_content">
        <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
        <div class="siteevent_event_form">
            <div id="siteevent_manage_order_content"> 
<?php endif; ?>

<div class="layout_middle">
    <div>
		<div>
			<div>
				<div class="mbot10"><h3><?php echo $this->translate('Manage Coupons') ?></h3></div>

                <?php if($this->can_create_coupons): ?>
                    <?php echo $this->translate('You can add attractive coupons for your event by clicking on the "Add a Coupon" link.') ?>
                    <br /><br />                
                    <div class="clr mbot10">
                      <!--ADD BUTTON-->
                      <?php
                      echo $this->htmlLink(
                        array('route' => "siteeventticket_coupon", 'action' => 'create', "event_id" => $this->siteevent->event_id), $this->translate('Add a Coupon'), array('class' => 'buttonlink seaocore_icon_add'));
                      ?>
                      <!--ADD BUTTON-->
                    </div>                    
			   <?php endif; ?>
				<ul class="siteevent_coupon_view">
					<?php if($this->paginator->getTotalItemCount()): ?>
						<?php foreach ($this->paginator as $item): ?>
                            <li class="siteevent_coupon_block">
                                <div class="siteevent_coupon_photo">
                                    <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
                                </div>
                                <?php if($this->can_create_coupons): ?>
                                    <div class='siteevent_profile_list_options'>
                                        <?php echo $this->htmlLink(array('route' => 'siteeventticket_coupon', 'action' => 'edit','coupon_id'=>$item->coupon_id, "event_id" => $this->siteevent->event_id), $this->translate('Edit Coupon'), array('class' => 'buttonlink seaocore_icon_edit')); ?>	
                                        <?php echo $this->htmlLink(array('route' => 'siteeventticket_coupon', 'action' => 'delete','coupon_id'=>$item->coupon_id, "event_id" => $this->siteevent->event_id), $this->translate('Delete Coupon'), array('class' => 'smoothbox buttonlink seaocore_icon_delete')) ?>
                                        <?php if (!empty($item->status )): ?>
                                            <a class='buttonlink seaocore_icon_disapproved' href='javascript:void(0)' onclick='Smoothbox.open("<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'coupon','action' => 'enable-disable', 'coupon_id' => $item->coupon_id, "event_id" => $this->siteevent->event_id), 'default', true); ?>")'><?php echo $this->translate('Disable Coupon '); ?></a>	
                                        <?php else: ?>
                                            <a class='buttonlink seaocore_icon_approved' href='javascript:void(0)' onclick='Smoothbox.open("<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'coupon','action' => 'enable-disable', 'coupon_id' => $item->coupon_id, "event_id" => $this->siteevent->event_id), 'default', true); ?>")'><?php echo $this->translate('Enable Coupon '); ?></a>	
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <div class='siteevent_coupon_details'>
                                    <div class='siteevent_coupon_title'>
                                        <h3><?php echo $this->htmlLink($item->getHref(), $item->title) ?></h3>
                                    </div>
                  
                                    <div class="siteevent_coupon_date">
                                        <div class="siteevent_coupon_date">
                                            <span><?php echo $this->translate('Start date') . ":"; ?></span>
                                            <span><?php echo $this->translate(gmdate('M d, Y', strtotime($item->start_time)))?></span>
                                        </div>
                                        <div class="siteevent_coupon_date">
                                          <span><?php echo $this->translate('End date') . ":"; ?></span>
                                          <?php if($item->end_settings == 1):?>
                                            <span><?php echo $this->translate( gmdate('M d, Y', strtotime($item->end_time))) ?></span>
                                          <?php else:?>
                                            <span><?php echo $this->translate('Never Expires') ?></span>
                                          <?php endif;?>
                                        </div>
                                    </div> 
                  
                                    <div class="siteevent_coupon_stats fleft">
                                        <?php if(!empty($item->coupon_code)):?>
                                            <span class="siteevent_coupon_stat siteeventcoupon_code siteeventcoupon_tip_wrapper">
                                                <span class="siteeventcoupon_tip">
                                                    <span><?php echo $this->translate('Select and Copy Code to use');?></span>
                                                    <i></i>
                                                </span>
                                                <input type="text" value="<?php echo $item->coupon_code; ?>" class="siteeventcoupon_code_num" onclick="this.select()" readonly>
                                            </span>
                                        <?php endif; ?>

                                        <span class="siteevent_coupon_stat siteevent_coupon_discount siteeventcoupon_tip_wrapper">
                                            <?php if (!empty($item->discount_type)):
                                            $priceStr = Engine_Api::_()->siteeventticket()->getPriceWithCurrency($item->discount_amount);?>
                                                <span class="siteeventcoupon_tip">
                                                  <span><?php echo $this->translate('Coupon Discount Value');?></span>
                                                  <i></i>
                                                </span>
                                                <span class="discount_value"><?php echo $priceStr; ?></span>&nbsp;&nbsp;
                                            <?php else: ?>
                                              <span class="siteeventcoupon_tip">
                                                <span><?php echo $this->translate('Coupon Discount Value');?></span>
                                                <i></i>
                                                </span>
                                                <span class="discount_value"><?php echo $item->discount_amount . '%'; ?></span>&nbsp;&nbsp;
                                            <?php endif; ?>
                                        </span>
                                    </div>
 
                                    <div class="siteevent_coupon_date">
										<?php $today = date("Y-m-d H:i:s"); ?>
                                        <?php if( !empty($item->end_settings) && $item->end_time < $today):?>
                                            <span class="siteeventcoupon_stat siteeventcoupon_left fright">
                                                <b><?php echo $this->translate('Expired'); ?></b>
                                            </span>
                                        <?php endif; ?>
									</div>
                  
                                    <div class='siteevent_coupon_stats'>
                                        <?php echo nl2br($item->description); ?>
                                    </div>
			  					
                                    <?php if($item->end_settings == 1 && ($item->end_time < $today)):?><br />
										<div class="tip" id='siteeventcoupon_search'>
											<span>
												<?php echo $this->translate('This coupon has expired.');?>
                                                <?php if($this->can_create_coupon): ?>
													<?php echo $this->translate('If you want this coupon to be displayed again, then please %1$sedit it%2$s to change its expiry date.', '<a href="'.$this->url(array('action' => 'edit','event_id' => $this->siteevent->event_id, 'coupon_id'=>$item->coupon_id,'tab' => $this->tab_selected_id)).'" class="smoothbox ">', '</a>'); ?>
                                                <?php endif;?>
											</span>
										</div> 
                                    <?php endif;?>
                                </div>
                            </li>
						<?php  endforeach; ?>
					<?php else:?>
						<div class="tip" id='siteeventcoupon_search'>
							<span>
								<?php echo $this->translate('No coupons have been added yet.'); ?>
								<?php if($this->can_create_coupon): ?>
									<?php echo $this->translate('Click %1$shere%2$s to create the first coupon of this event.', '<a href="'.$this->url(array( 'action' => 'create','event_id' => $this->siteevent->event_id,'coupon_event'=> '1', 'tab' => $this->tab_selected_id)).'" class="smoothbox ">', '</a>'); ?>
								<?php endif;?>
							</span>
						</div>	
                    <?php endif;?>
				</ul>
				<?php  if(!empty($paginator)) { echo $this->paginationControl($this->paginator);} ?>
			</div>
		</div>
	</div>
</div>	
                
<?php date_default_timezone_set($oldTz);?>


<?php if (!$this->only_list_content): ?>
      </div>
    </div>	
  </div>	
<?php endif; ?>