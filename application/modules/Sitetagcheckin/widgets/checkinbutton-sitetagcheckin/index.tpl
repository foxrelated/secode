<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
   $this->headLink()
   ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css');
?>
<?php if($this->checkin_button_sidebar) :?>
	<ul class="seaocore_sidebar_list clr">
	  <li class="stcheck_button_narrow">
	    <?php if ($this->checkin_button): ?>
				<div class="stchecckin_button" onclick="showCheckinLightbox();">
					<div>
            <?php if($this->checkin_icon):?>
						  <div class="stchackin_button_icon stchackin_button_icon_pin"></div> <!--//FOR PIN ICON-->
            <?php else:?>
              <div class="stchackin_button_icon stchackin_button_icon_tick"></div> <!--//FOR MAP ICON-->
            <?php endif;?>
						<div class="stcheckin_check"><?php echo $this->translate($this->checkin_button_link); ?></div>
					</div>
				</div>
	    <?php else: ?>
	    	<div>
					<?php if($this->checkin_icon):?>
						<a href='javascript:void(0);' onclick="showCheckinLightbox();" class="item_icon_sitetagcheckin buttonlink">
							<?php echo $this->translate($this->checkin_button_link); ?>
						</a> 
          <?php else:?>
						<a href='javascript:void(0);' onclick="showCheckinLightbox();" class="stchackin_icon_tick buttonlink">
							<?php echo $this->translate($this->checkin_button_link); ?>
						</a> 
          <?php endif;?>
		    </div>
	    <?php endif; ?>
	  	<div class="stcheck_ckeckin_stat clr">
				<?php echo $this->translate(array('%1$s %2$s time.', '%1$s %2$s times.', $this->user_check_in_count), $this->translate($this->checkin_your), $this->locale()->toNumber($this->user_check_in_count)) ?>
			</div>
			<div class="stcheck_ckeckin_stat clr">
				<?php echo $this->translate(array('%1$s %2$s time.', '%1$s %2$s times.', $this->check_in_count), $this->translate($this->checkin_total), $this->locale()->toNumber($this->check_in_count)) ?>

			</div>	
	  </li>
	</ul>	
<?php else:?>
	<ul class="seaocore_sidebar_list clr">
	  <li class="stcheck_button_wide">
	    <?php if ($this->checkin_button): ?>
		   	<div class="stchecckin_button fleft" onclick="showCheckinLightbox();" style="width:200px;">
					<div>
            <?php if($this->checkin_icon):?>
						  <div class="stchackin_button_icon stchackin_button_icon_pin"></div> <!--//FOR PIN ICON-->
            <?php else:?>
              <div class="stchackin_button_icon stchackin_button_icon_tick"></div> <!--//FOR MAP ICON-->
            <?php endif;?>
						<div class="stcheckin_check"><?php echo $this->translate($this->checkin_button_link); ?></div>
					</div>
				</div>	
	    <?php else: ?>
	    	<div class="fleft stcheckin_checkin_link">
					<?php if($this->checkin_icon):?>
						<a href='javascript:void(0);' onclick="showCheckinLightbox();" class="item_icon_sitetagcheckin buttonlink">
							<?php echo $this->translate($this->checkin_button_link); ?>
						</a> 
          <?php else:?>
						<a href='javascript:void(0);' onclick="showCheckinLightbox();" class="stchackin_icon_tick buttonlink">
							<?php echo $this->translate($this->checkin_button_link); ?>
						</a> 
          <?php endif;?>
		    </div>
	    <?php endif; ?>
	    
			<div class="stcheck_ckeckin_stat">
				<?php echo $this->translate(array('%1$s %2$s time.', '%1$s %2$s times.', $this->user_check_in_count), $this->translate($this->checkin_your), $this->locale()->toNumber($this->user_check_in_count)) ?>
			</div>
			
			<div class="stcheck_ckeckin_stat">
				<?php echo $this->translate(array('%1$s %2$s time.', '%1$s %2$s times.', $this->check_in_count), $this->translate($this->checkin_total), $this->locale()->toNumber($this->check_in_count)) ?>
			</div>
			
	  </li>
	</ul>
<?php endif;?>
<?php
  $isMobile = Engine_Api::_()->seaocore()->isMobile(); 
	$url = $this->url(array('action' => 'check-in', 'module' => 'sitetagcheckin', 'controller' => 'checkin', 'resource_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'checkin_use' => $this->checkin_use, 'checkin_verb' => $this->checkin_verb,  'checkedinto_verb' => $this->checkedinto_verb, 'checkin_your' => $this->checkin_your), 'default', true);
?>
<script type="text/javascript">
   
  function showCheckinLightbox() {
		<?php if($isMobile) :?>
			window.location.href ='<?php echo $url; ?>';
		<?php else:?>
			Smoothbox.open('<?php echo $url; ?>');
		<?php endif;?>
  }

</script>