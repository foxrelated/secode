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

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
	    . 'application/modules/Siteeventticket/externals/styles/style_siteeventcoupon.css');
?>

<?php $viewer_id = $this->viewer->getIdentity();?>
<?php if(!empty($viewer_id)):?>
    <?php $oldTz = date_default_timezone_get();?>
	<?php date_default_timezone_set($this->viewer->timezone);?>
<?php endif;?>

<?php if($this->paginator->getTotalItemCount()):?>
    <form id='filter_form_siteeventticketcoupons' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'siteeventticket_coupon', true) ?>' style='display: none;'>
        <input type="hidden" id="page" name="page"  value=""/>
    </form>

	<div class='layout_middle'>
		<ul class="siteevent_coupon_view">
			<?php foreach ($this->paginator as $coupon): ?>
				<li class="siteevent_coupon_block">
					<div class="siteevent_coupon_photo"> 
          	<?php echo $this->htmlLink($coupon->getHref(), $this->itemPhoto($coupon, 'thumb.normal')) ?>
					</div>
                    
					<div class='siteevent_coupon_details'>
            <div class="siteevent_coupon_title">
                <h3>
                    <?php echo $this->htmlLink($coupon->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($coupon->getTitle(), $this->truncation), array('title' => $coupon->description)); ?>
                </h3>
            </div>
                        
						<div class="siteevent_coupon_date">
							<?php $event = Engine_Api::_()->getItem('siteevent_event', $coupon->event_id); ?>
							<?php echo $this->translate("in %s", $this->htmlLink($event->getHref(),  $event->getTitle())) ?>
						</div>

						<div class="siteevent_coupon_date">
							<?php if(in_array('startdate', $this->statistics)):?>
                <div class="siteevent_coupon_date">
                    <span><?php echo $this->translate('Start date:'); ?></span>
                    <span><?php echo $this->translate(gmdate('M d, Y', strtotime($coupon->start_time)));?></span>
                </div>
              <?php endif;?>
              
              <?php if(in_array('enddate', $this->statistics)):?>
                  <div class="siteevent_coupon_date">
                      <span><?php echo $this->translate('End date:'); ?></span>
                      <?php if($coupon->end_settings == 1):?>
                          <span><?php echo $this->translate(gmdate('M d, Y', strtotime($coupon->end_time))); ?></span>
                      <?php else:?>
                          <span><?php echo $this->translate('Never Expires');?></span>
                      <?php endif;?>
                  </div>
              <?php endif;?>
              
              <div class="seaocore_txt_light siteevent_coupon_date">          
              <?php
                  $statistics = '';
  
                  if (!empty($this->statistics) && in_array('viewCount', $this->statistics)) {
                      $statistics .= $this->translate(array('%s view', '%s views', $coupon->view_count), $this->locale()->toNumber($coupon->view_count)) . ', ';
                  }
  
                  if (!empty($this->statistics) && in_array('likeCount', $this->statistics)) {
                      $statistics .= $this->translate(array('%s like', '%s likes', $coupon->like_count), $this->locale()->toNumber($coupon->like_count)) . ', ';
                  }
                  
                  if (!empty($this->statistics) && in_array('commentCount', $this->statistics)) {
                      $statistics .= $this->translate(array('%s comment', '%s comments', $coupon->comment_count), $this->locale()->toNumber($coupon->comment_count)) . ', ';
                  }                            
  
                  $statistics = trim($statistics);
                  $statistics = rtrim($statistics, ',');
              ?>
            	<?php echo $statistics; ?>
            </div>
          	</div>

            <div class="siteevent_coupon_stats fleft"> 
              <?php if(in_array('couponcode', $this->statistics)):?>
                <span class="siteevent_coupon_stat siteeventcoupon_code siteeventcoupon_tip_wrapper">
                  <span class="siteeventcoupon_tip">
                      <span><?php echo $this->translate('Select and Copy Code to use');?></span>
                      <i></i>
                  </span>
                  <input type="text" value="<?php echo $coupon->coupon_code;?>" class="siteeventcoupon_code_num" onclick="this.select()" readonly>
                </span>
              <?php endif;?>
              <span class="siteevent_coupon_stat siteevent_coupon_discount siteeventcoupon_tip_wrapper">
                <?php if(in_array('discount', $this->statistics)):?>
                  <span class="siteeventcoupon_tip">
                    <span><?php echo $this->translate('Coupon Discount Value');?></span>
                    <i></i>
                  </span>
                  <?php if(!empty($coupon->discount_type)):
                    $priceStr = Engine_Api::_()->siteeventticket()->getPriceWithCurrency($coupon->discount_amount);?>
                    <span class="discount_value"><?php echo $priceStr; ?></span>&nbsp;&nbsp;
                  <?php else:?>
                    <span class="discount_value"><?php echo $coupon->discount_amount . '%';?></span>&nbsp;&nbsp;
                  <?php endif;?>
                <?php endif;?>
              </span>
            </div> 
            
						<div class="siteevent_coupon_date "> 
              <?php $today = date("Y-m-d H:i:s"); ?>
              <?php if(in_array('expire', $this->statistics) && !empty($coupon->end_settings) && $coupon->end_time < $today):?>
                <span class="siteeventcoupon_stat siteeventcoupon_left fright">
                  <b><?php echo $this->translate('Expired');?></b>
                </span>
              <?php endif;?>
						</div> 
            <div class="siteevent_coupon_stats">
              <?php $description = strip_tags($coupon->description);?>
              <?php if (!empty($description)):?>
    <?php $truncate_description = ( Engine_String::strlen($description) > 190 ? Engine_String::substr($description, 0, 190) . '...' : $description );?>
                  <?php if(Engine_String::strlen($description) > 190):?>
                    <?php $truncate_description .= $this->htmlLink($coupon->getHref(), $this->translate('More &raquo;'));?>
                  <?php endif;?>
                  <?php echo $truncate_description;?>
              <?php endif;?>
            </div>
            
          </li>
        <?php endforeach; ?>
    </ul>
        <?php echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true, 'query' => $this->formValues)); ?>
	</div>
<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There are no search results to display.');?>
		</span>
	</div>
<?php endif;?>
                    
<?php if(!empty($viewer_id)):?>
	<?php date_default_timezone_set($oldTz);?>
<?php endif;?>