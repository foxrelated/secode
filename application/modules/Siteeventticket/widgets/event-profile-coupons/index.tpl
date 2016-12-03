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
<?php $this->headLink()
            ->appendStylesheet($this->layout()->staticBaseUrl
                    . 'application/modules/Siteeventticket/externals/styles/style_siteeventcoupon.css'); ?>
<?php $viewer_id = $this->viewer->getIdentity(); ?>
<?php if (!empty($viewer_id)): ?>
    <?php $oldTz = date_default_timezone_get(); ?>
    <?php date_default_timezone_set($this->viewer->timezone); ?>
<?php endif; ?>

<?php if ($this->loaded_by_ajax): ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_siteeventticket_event_profile_coupons')
        }
        en4.siteevent.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>

<?php if ($this->showContent): ?>

    <?php if ($this->can_create_coupons): ?>
        <div class="seaocore_add">
            <?php echo $this->htmlLink(array('route' => 'siteeventticket_coupon', 'action' => 'create','event_id' => $this->siteevent->event_id), $this->translate('Add a Coupon'), array('class' => 'buttonlink seaocore_icon_create')); ?>
        </div>
    <?php endif; ?>

    <?php if (count($this->paginator) > 0): ?>
        <ul class="siteevent_coupon_view">
          <?php foreach ($this->paginator as $coupon): ?>
                <li class="siteevent_coupon_block">
                    <div class="siteevent_coupon_photo">
                        <?php echo $this->htmlLink($coupon->getHref(), $this->itemPhoto($coupon, 'thumb.normal')) ?>
                    </div>  
                    
                    <div class='siteevent_profile_list_options'>
                        <?php if ($this->can_create_coupons): ?>
                            <?php echo $this->htmlLink(array('route' => 'siteeventticket_coupon', 'action' => 'edit', 'coupon_id' => $coupon->coupon_id, "event_id" => $this->siteevent->event_id), $this->translate('Edit Coupon'), array('class' => 'buttonlink seaocore_icon_edit'));?>	
                            <?php echo $this->htmlLink(array('route' => 'siteeventticket_coupon', 'action' => 'delete', 'coupon_id' => $coupon->coupon_id, "event_id" => $this->siteevent->event_id), $this->translate('Delete Coupon'), array('class' => 'buttonlink seaocore_icon_delete'));?>

                            <?php if (!empty($coupon->status)): ?>
                                <a class='buttonlink seaocore_icon_disapproved' href='javascript:void(0)' onclick='Smoothbox.open("<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'coupon','action' => 'enable-disable', 'coupon_id' => $coupon->coupon_id, "event_id" => $this->siteevent->event_id), 'default', true); ?>")'><?php echo $this->translate('Disable Coupon'); ?></a>		
                            <?php else: ?>
                                <a class='buttonlink seaocore_icon_approved' href='javascript:void(0)' onclick='Smoothbox.open("<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'coupon','action' => 'enable-disable', 'coupon_id' => $coupon->coupon_id, "event_id" => $this->siteevent->event_id), 'default', true); ?>")'><?php echo $this->translate('Enable Coupon'); ?></a>	
                            <?php endif; ?>
                        <?php endif; ?>
                
                        <?php echo $this->htmlLink(array('route' => 'siteeventticket_coupon','action' => 'print', 'coupon_id' => $coupon->coupon_id, "event_id" => $this->siteevent->event_id), $this->translate('Print Coupon'), array('target' => '_blank', ' class' => 'buttonlink icon_siteeventticket_printer')); ?>
                
                    </div>

                    <div class='siteevent_coupon_details'>
                        <div class='siteevent_coupon_title'>
                            <h3> 
                                <?php echo $this->htmlLink($coupon->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($coupon->getTitle(), $this->truncation), array('title' => $coupon->title)) ?>
                            </h3>
                        </div>

                        <div class="siteevent_coupon_date">
                            <?php if (in_array('startdate', $this->statistics)): ?>
                                <div class="siteevent_coupon_date">
                                    <span><?php echo $this->translate('Start date:'); ?></span>
                                    <span><?php echo $this->translate(gmdate('M d, Y', strtotime($coupon->start_time))); ?></span>
                                </div>
                            <?php endif; ?>
                  
                            <?php if (in_array('enddate', $this->statistics)): ?>
                                <div class="siteevent_coupon_date">
                                    <span><?php echo $this->translate('End date:'); ?></span>
                                    <?php if ($coupon->end_settings == 1): ?><span><?php echo $this->translate(gmdate('M d, Y', strtotime($coupon->end_time))); ?></span><?php else: ?><span><?php echo $this->translate('Never Expires'); ?></span><?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                
                        <div class="siteevent_coupon_stats ">
                            <?php if (in_array('couponcode', $this->statistics)): ?>
                                <span class="siteevent_coupon_stat siteeventcoupon_code siteeventcoupon_tip_wrapper">
                                    <span class="siteeventcoupon_tip">
                                        <span><?php echo $this->translate('Select and Copy Code to use');?></span>
                                        <i></i>
                                    </span>
                                    <input type="text" value="<?php echo $coupon->coupon_code; ?>" class="siteeventcoupon_code_num" onclick="this.select()" readonly>
                                </span>
                            <?php endif; ?>
                    
                            <span class="siteevent_coupon_stat siteevent_coupon_discount siteeventcoupon_tip_wrapper">
                                <?php if (in_array('discount', $this->statistics)): ?>
                                    <span class="siteeventcoupon_tip">
                                        <span><?php echo $this->translate('Coupon Discount Value');?></span>
                                        <i></i>
                                    </span>
                                    <?php if(!empty($coupon->discount_type)):
                                        $priceStr = Engine_Api::_()->siteeventticket()->getPriceWithCurrency($coupon->discount_amount);?>
                                        <span class="discount_value"><?php echo $priceStr; ?></span>&nbsp;&nbsp;
                                    <?php else:?>
                                        <span class="discount_value"><?php echo $coupon->discount_amount . '%'; ?></span>&nbsp;&nbsp;
                                    <?php endif;?>
                                <?php endif;?>
                            </span>
                    
                            <?php $today = date("Y-m-d H:i:s"); ?>
                            <?php if(in_array('expire', $this->statistics) && !empty($coupon->end_settings) && $coupon->end_time < $today):?>
                                <span class="siteeventcoupon_stat siteeventcoupon_left fright">
                                    <b><?php echo $this->translate('Expired'); ?></b>
                                </span>
                            <?php endif; ?>	
                        </div>
               	
                        <div class="siteevent_coupon_stats">
                            <?php echo nl2br($coupon->description);?>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="tip" id='siteeventticket_search'>
            <span>
                <?php echo $this->translate('No coupons have been created in this event yet.'); ?>
                <?php if ($this->can_create_coupons): ?>
                    <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'create', 'event_id' => $this->siteevent->event_id), 'siteeventticket_coupon') . '">', '</a>'); ?>
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>
    <br/>
    <div>
        <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
            <div id="user_group_members_previous" class="paginator_previous">
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => 'paginateSiteeventCoupon(siteeventCouponPage - 1)', 'class' => 'buttonlink icon_previous')); ?>
            </div>
        <?php endif; ?>
        <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
            <div id="user_group_members_next" class="paginator_next">
              <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => 'paginateSiteeventCoupon(siteeventCouponPage + 1)', 'class' => 'buttonlink_right icon_next')); ?>
            </div>
      <?php endif; ?>
    </div>

    <a id="siteevent_coupon_anchor" style="position:absolute;"></a>

    <script type="text/javascript">
        var siteeventCouponPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
        var paginateSiteeventCoupon = function(page) {
            var params = {
                requestParams:<?php echo json_encode($this->params) ?>,
                responseContainer: $$('.layout_siteeventticket_event_profile_coupons')
            }
            params.requestParams.content_id = <?php echo sprintf('%d', $this->identity) ?>;
            params.requestParams.page = page;
            en4.siteevent.ajaxTab.sendReq(params);
        }
    </script>  
<?php endif; ?>

