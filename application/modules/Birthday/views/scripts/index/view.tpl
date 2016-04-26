<?php

/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: view.tpl 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<script type = "text/javascript">

  var birthdayPage = <?php echo sprintf('%d', $this->current_page) ?>;
  var cdate = <?php echo sprintf('%d', $this->current_date) ?>;
  var cmonth = <?php echo sprintf('%d', $this->current_month) ?>;
  var cyear = <?php echo sprintf('%d', $this->current_year) ?>;
  var next_start = <?php echo sprintf('%d', $this->next_start) ?>;
  var prev_start = <?php echo sprintf('%d', $this->prev_start) ?>;

  var birthdays = function(page) {
   if (page > birthdayPage) {
    var url = en4.core.baseUrl + "birthday/index/view/startindex/" + next_start + "/page/" + page;
   }
   else {
     var url = en4.core.baseUrl + "birthday/index/view/startindex/" + prev_start + "/page/" + page;
   }
   window.location.href = url;
  }

</script>
<div class="birthday_view">
<h3>
	<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Birthday/externals/images/calendar.png" alt="" />
	<?php echo $this->translate("Birthdays"); ?>
</h3>
<form action="" name="myform" method="post" id="myform">
  <input type="hidden" name="next_start" value='<?php echo $this->next_start?>' >
  <input type="hidden" name="prev_start" value='<?php echo $this->prev_start?>' >
  <input type="hidden" name="old_page" value='<?php echo sprintf('%d', $this->current_page) ?>' >
</form>
<?php if(!empty($this->paginator)) : ?>
<?php if(!empty($this->birthday_array['today'])) : ?>
	<div class="birthday_list">
    <h4><?php echo $this->translate("Today");?></h4>
    <?php $today_birthday_array = $this->birthday_array['today']; ?>
    <?php $i = 0; ?>
  	<?php foreach($today_birthday_array as $key => $values) : ?>
	<?php $i++; ?>
    	<div class="birthday_list_item" style="float:<?php if(($i%2) == 0) { echo 'right';} else { echo 'left';}?>">
    		<div class="item_thumb"> 
					<a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > <?php echo $this->itemPhoto($this->user($values[0]), 'thumb.icon') ?></a>
				</div>
				<div class="item_info">
					<div class="item_title"> 	    
						<a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > <?php echo $this->user($values[0])->getTitle() ?></a> 
					</div>
					
					  <div class="item_stat">	
					    <?php
					      $date_array = explode("-", $values[1]);
					    ?>
					    <?php if($this->age_display == 1 && !empty($date_array[0])) :?>
					      <?php $show_label_age = Zend_Registry::get('Zend_Translate')->_('%s years old'); ?>
					      <?php $show_label_age = sprintf($show_label_age, $values[2]); ?>
					      <?php echo $show_label_age; ?>
					    <?php endif; ?>
					  </div>
				</div>
				<div class="birthday_list_item_right_links">
    				<a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" class="icon_type_wish_birthday buttonlink"><?php echo $this->translate('Wish'); ?></a>
					<a class="icon_type_message_birthday buttonlink" href="<?php echo $this->sugg_baseUrl; ?>/messages/compose/to/<?php echo $values[0]; ?>" ><?php echo $this->translate('Send Message'); ?></a>
    		</div>		
    	</div>
    <?php endforeach; ?>
  </div>  
<?php endif; ?>

<?php if(!empty($this->birthday_array['tomorrow'])) : ?>
	<div class="birthday_list">
    <h4><?php echo $this->translate("Tomorrow");?></h4>
    <?php $tomorrow_birthday_array = $this->birthday_array['tomorrow']; ?>
    <?php $i = 0; ?>
  	<?php foreach($tomorrow_birthday_array as $key => $values) : ?>
	<?php $i++; ?>
    	<div class="birthday_list_item" style="float:<?php if(($i%2) == 0) { echo 'right';} else { echo 'left';}?>">
    		<div class="item_thumb"> 
					<a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > <?php echo $this->itemPhoto($this->user($values[0]), 'thumb.icon') ?></a>
				</div>
				<div class="item_info">
					<div class="item_title"> 	    
						<a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > <?php echo $this->user($values[0])->getTitle() ?></a> 
					</div>
					<div class="item_stat">	
						<?php
						  $date_array = explode("-", $values[1]);
						  $timestamp = mktime(0, 0, 0,$date_array[1], $date_array[2], $values[3]);
							$date_display = Engine_Api::_()->birthday()->get_dateDisplay($date_array[2], $timestamp);
							echo $date_display;
							
						?>
						
						<?php if(!empty($this->age_display) && !empty($date_array[0])) :?>
						  &nbsp;|&nbsp;
						  <?php $show_label_age = Zend_Registry::get('Zend_Translate')->_('%s years old'); ?>
						  <?php $show_label_age = sprintf($show_label_age, $values[2]); ?>
						  <?php echo $show_label_age; ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="birthday_list_item_right_links">
					<a class="icon_type_message_birthday buttonlink" href="<?php echo $this->sugg_baseUrl; ?>/messages/compose/to/<?php echo $values[0]; ?>" ><?php echo $this->translate('Send Message'); ?></a>
    		</div>		
    	</div>
    <?php endforeach; ?>
  </div>  
<?php endif; ?>

<?php if(!empty($this->birthday_array['week'])) : ?>
	<div class="birthday_list">
    <h4><?php echo $this->translate("This week");?></h4>
    <?php $week_birthday_array = $this->birthday_array['week']; ?>
    <?php $i = 0; ?>
  	<?php foreach($week_birthday_array as $key => $values) : ?>
	<?php $i++; ?>
    	<div class="birthday_list_item" style="float:<?php if(($i%2) == 0) { echo 'right';} else { echo 'left';}?>">
    		<div class="item_thumb"> 
					<a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > <?php echo $this->itemPhoto($this->user($values[0]), 'thumb.icon') ?></a>
				</div>
				<div class="item_info">
					<div class="item_title"> 	    
						<a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > <?php echo $this->user($values[0])->getTitle() ?></a> 
					</div>
					<div class="item_stat">	
						<?php
						  $date_array = explode("-", $values[1]);
						  $timestamp = mktime(0, 0, 0,$date_array[1], $date_array[2], $values[3] );
							$date_display = Engine_Api::_()->birthday()->get_dateDisplay($date_array[2], $timestamp);
							echo $date_display;
						?>
						
						<?php if(!empty($this->age_display) && !empty($date_array[0])) :?>
						  &nbsp;|&nbsp;
						  <?php $show_label_age = Zend_Registry::get('Zend_Translate')->_('%s years old'); ?>
						  <?php $show_label_age = sprintf($show_label_age, $values[2]); ?>
						  <?php echo $show_label_age; ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="birthday_list_item_right_links">
					<a class="icon_type_message_birthday buttonlink" href="<?php echo $this->sugg_baseUrl; ?>/messages/compose/to/<?php echo $values[0]; ?>" ><?php echo $this->translate('Send Message'); ?></a>
    		</div>		
    	</div>
    <?php endforeach; ?>
  </div>  
<?php endif; ?>

<?php if(!empty($this->birthday_array['this_month'])) : ?>
	<div class="birthday_list">
  	<h4><?php echo $this->translate("This month");?></h4>
  	<?php $this_month_birthday = $this->birthday_array['this_month'];  ?>
	<?php $i = 0; ?>
  	<?php foreach($this_month_birthday as $key => $values) : ?>
		<?php $i++; ?>
    	<div class="birthday_list_item" style="float:<?php if(($i%2) == 0) { echo 'right';} else { echo 'left';}?>">
				<div class="item_thumb">
      		<a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > <?php echo $this->itemPhoto($this->user($values[0]), 'thumb.icon') ?></a>
      	</div>	  
				<div class="item_info">
					<div class="item_title"> 
      			<a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > <?php echo $this->user($values[0])->getTitle() ?></a>
      		</div>
      		<div class="item_stat">
 						<?php
						  $date_array = explode("-", $values[1]);
						  $timestamp = mktime(0, 0, 0,$date_array[1], $date_array[2], $this->current_year);
							$date_display = Engine_Api::_()->birthday()->get_dateDisplay($date_array[2], $timestamp);
							echo $date_display;
						?>
					
						<?php if(!empty($this->age_display) && !empty($date_array[0])) :?>
						 &nbsp;|&nbsp;
						  <?php $show_label_age = Zend_Registry::get('Zend_Translate')->_('%s years old'); ?>
						  <?php $show_label_age = sprintf($show_label_age, $values[2]); ?>
						  <?php echo $show_label_age; ?>
						<?php endif; ?>
      		</div>
      	</div>		 
    		<div class="birthday_list_item_right_links">
					<a class="icon_type_message_birthday buttonlink" href="<?php echo $this->sugg_baseUrl; ?>/messages/compose/to/<?php echo $values[0]; ?>" ><?php echo $this->translate('Send Message'); ?></a>
    		</div>
    	</div>
  	<?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="birthday_list">
  <?php foreach($this->next_month_text as $key => $month) : ?>

    <?php if(!empty($this->birthday_array[$month])) : ?>
      <h4><?php echo $this->translate($month); ?></h4>
      <?php $current_month_birthday_array = $this->birthday_array[$month]; ?>
      <?php $i = 0; ?>
      <?php foreach($current_month_birthday_array as $key => $values) : ?>
		
			<?php $i++; ?>
    	<div class="birthday_list_item" style="float:<?php if(($i%2) == 0) { echo 'right';} else { echo 'left';}?>">
				<div class="item_thumb">
	  			<a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > <?php echo $this->itemPhoto($this->user($values[0]), 'thumb.icon') ?></a>
				</div>
				<div class="item_info">
					<div class="item_title">     
	  				<a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > <?php echo $this->user($values[0])->getTitle() ?></a> 
					</div>
					<div class="item_stat">	
					 	<?php
						  $date_array = explode("-", $values[1]);
						  $timestamp = mktime(0, 0, 0,$date_array[1] , $date_array[2], $values[3]);
							$date_display = Engine_Api::_()->birthday()->get_dateDisplay($date_array[2], $timestamp, $month);
							echo $date_display;
						?>
						
						<?php if(!empty($this->age_display) && !empty($date_array[0])) :?>
						  &nbsp;|&nbsp;
						  <?php $show_label_age = Zend_Registry::get('Zend_Translate')->_('%s years old'); ?>
						  <?php $show_label_age = sprintf($show_label_age, $values[2]); ?>
						  <?php echo $show_label_age; ?>
						<?php endif; ?>
					</div>
				</div>
    		<div class="birthday_list_item_right_links">
					<a class="icon_type_message_birthday buttonlink" href="<?php echo $this->sugg_baseUrl; ?>/messages/compose/to/<?php echo $values[0]; ?>" ><?php echo $this->translate('Send Message'); ?></a>
    		</div>	
			</div>
      <?php endforeach; ?>
    <?php endif; ?>
  <?php endforeach; ?>
</div>

<?php if(!empty($this->birthday_array['this_month_remaining'])) : ?>
	<div class="birthday_list">
  	<h4><?php echo $this->translate(date('F', time()));?></h4>
  	<?php $this_month_remaining_birthday = $this->birthday_array['this_month_remaining']; ?>
	<?php $i = 0; ?>
  	<?php foreach($this_month_remaining_birthday as $key => $values) : ?>
		<?php $i++; ?>
    	<div class="birthday_list_item" style="float:<?php if(($i%2) == 0) { echo 'right';} else { echo 'left';}?>">
				<div class="item_thumb">
      		<a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > <?php echo $this->itemPhoto($this->user($values[0]), 'thumb.icon') ?></a>
      	</div>	  
				<div class="item_info">
					<div class="item_title"> 
      			<a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > <?php echo $this->user($values[0])->getTitle() ?></a>
      		</div>
      		<div class="item_stat">
 						<?php
						  $date_array = explode("-", $values[1]);
						  $timestamp = mktime(0, 0, 0,$date_array[1], $date_array[2], $values[3]);
							$date_display = Engine_Api::_()->birthday()->get_dateDisplay($date_array[2], $timestamp);
							echo $date_display;
						?>
						
						<?php if(!empty($this->age_display) && !empty($date_array[0])) :?>
						  &nbsp;|&nbsp;
						  <?php $show_label_age = Zend_Registry::get('Zend_Translate')->_('%s years old'); ?>
						  <?php $show_label_age = sprintf($show_label_age, $values[2]); ?>
						  <?php echo $show_label_age; ?>
						<?php endif; ?>
      		</div>
      	</div>		 
    		<div class="birthday_list_item_right_links">
					<a class="icon_type_message_birthday buttonlink" href="<?php echo $this->sugg_baseUrl; ?>/messages/compose/to/<?php echo $values[0]; ?>" ><?php echo $this->translate('Send Message'); ?></a>
    		</div>
    	</div>
  	<?php endforeach; ?>
  </div>
<?php endif; ?>

<?php else: ?>

  <div class="tip" style="margin:5px;"><span style="margin-bottom:0px;font-size:11px;"><?php echo $this->translate("No birthdays of your friends were found."); ?></span></div>
<?php endif; ?>

 <?php if( $this->total_pages > 1 ): ?>
    <div>
      <?php if( $this->current_page > 1 ): ?>
        <div id="birthday_listings_previous" class="paginator_previous">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => 'birthdays(birthdayPage - 1)',
            'class' => 'buttonlink icon_previous'
          )); ?>
        </div>
      <?php endif; ?>
      <?php if( $this->current_page < $this->total_pages ): ?>
        <div id="birthday_listings_next" class="paginator_next">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
            'onclick' => 'birthdays(birthdayPage + 1)',
            'class' => 'buttonlink_right icon_next'
          )); ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>