<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<?php
 $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_infotooltip.css');
?>


<script type="text/javascript">

		en4.core.runonce.add(function() {
			en4.core.language.addData({
				"You wrote on %s s wall.":"<?php echo $this->string()->escapeJavascript($this->translate("You wrote on %s s wall."));?>"
			});
		});
	// function to switch to next and last months through ajax request
	function another_month(date_current) {
		var data_another_month = {
			'date_current'  : date_current, 'format' : 'html'
		};
		en4.core.request.send(new Request.HTML({
			'url' : '<?php echo $this->url(array('module' => 'birthday', 'controller' => 'index', 'action' =>
				'index'), 'default', true) ?>',
			'data' : data_another_month,
				onSuccess : function () {

		setTimeout ("update_tooltip()", 500);
			}
		}), {
			'element' : $('dyanamic_code').getParent()
		});
	}

	/* moo style */
	window.addEvent('domready',function() {
				//opacity / display fix
		$$('.birthday-jq-checkpointSubhead').setStyles({
			opacity: 0,
			display: 'none'
		});
		//put the effect in place
		$$('.birthday-jq-checkpoints li').each(function(el,i) {
			el.addEvents({
				'mouseenter': function() {
					el.getElement('div').style.display = 'block';
					el.getElement('div').fade('in');
				},
				'mouseleave': function() {
					el.getElement('div').style.display = 'none';
					el.getElement('div').fade('out');
				}
			});
		});
	});

	function update_tooltip () {
	//opacity / display fix
		$$('.birthday-jq-checkpointSubhead').setStyles({
			opacity: 0,
			display: 'none'
		});
		//put the effect in place
		$$('.birthday-jq-checkpoints li').each(function(el,i) {
			el.addEvents({
				'mouseenter': function() {
					el.getElement('div').style.display = 'block';
					el.getElement('div').fade('in');
				},
				'mouseleave': function() {
					el.getElement('div').style.display = 'none';
					el.getElement('div').fade('out');
				}
			});
		});

	}

  var flag = 0;
  var activitywriteonclick = function (thisobj, wallvalue, event, object_id) {
		if (event == 1) {
			if (thisobj.value == wallvalue) {
				thisobj.value = '';
				$('activity-write-body-' + object_id).value = '';
			}
		}
		else if (event == 2 && thisobj.value == '') {

			(function() {
				thisobj.value = wallvalue;
			}).delay(250);
		}
  }

  function statusubmit(e, users_id, boxvalue) {
    var keycode=null;
    var url = '';
     var referenceNode = $('user_' + users_id);
        var parent = referenceNode.parentNode;
    if (e!=null){
      if (window.event!=undefined){
      if (window.event.keyCode) keycode = window.event.keyCode;
        else if (window.event.charCode) keycode = window.event.charCode;
      } else{
        keycode = e.keyCode;
      }
    }
    if( keycode != 13 ) {
      if( $('activity-write-body-' +users_id ).value == '' ){
        flag = 0;
      } else {
        flag = 1;
      }
    } else {
      if( $('activity-write-body-' + users_id).value =='' ){
        return;
      }
			url = en4.core.baseUrl + 'birthday/index/statusubmit';
			en4.core.request.send(new Request.HTML({
				url : url,
				data : {
					format : 'html',
					object_id : users_id,
					body : $('activity-write-body-' +users_id).value
				},
				'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
				{
					var newdiv = document.createElement('div');
						newdiv.id = 'meassge_show';
						newdiv.innerHTML = en4.core.language.translate("<?php echo $this->translate('You wrote on %s s wall.') ?>", boxvalue);
					parent.insertBefore(newdiv, referenceNode.nextSibling);
				}
			}))
			$('activity-write-body-' + users_id).style.display='none';
			flag = 0;
    }
  }
</script>


<ul class="layout_birthday_widget" id="dyanamic_code">
	<!--CASE1: WHEN ADMIN SETTING IS TO SHOW ONLY TITLE-->
  <?php if($this->display_action == 0) : ?>
		<li class="birthday_widget_name_list">
		 	<div class="b_wish_icon">
				<?php $url = $this->url(array('module' => 'birthday', 'controller' => 'index', 'action' =>
	      'view'), 'default', true); ?>
				<a href='<?php echo $url ?>' title="<?php echo $this->translate('View Birthdays') ?>">
					<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Birthday/externals/images/wish.png" alt="" align="left" />
				</a>
			</div>
			<ul class="birthday-jq-checkpoints">
			  <?php $count = count($this->result);
			    if ($count > $this->display_entries ):
						$count = $this->display_entries;
			    endif;
			  ?>
			  <?php foreach($this->result as $values): ?>
		    <?php $tooltip_string = ""; ?>
	      <?php if($this->display_count < $this->display_entries) : ?>
					<li class="blist_name">
					  <?php $user_subject = Engine_Api::_()->user()->getUser($values->item_id);
									$profile_url = $this->url(array('id' => $values->item_id), 'user_profile'); ?>
					<!--Tooltip code start here-->
           <?php include APPLICATION_PATH .
            '/application/modules/Birthday/views/scripts/tooltip_birthday.tpl'; ?>
						<!--Tooltip work end-->
							  	&nbsp;
						<a href="<?php echo $profile_url ?>"><?php echo $this->user($user_subject)->getTitle()
							?></a><?php if($count > 1 ) { echo ","; } ?>
			    </li>
				<?php endif; ?>
	      <?php $this->display_count = $this->display_count + 1; ?>
	    	<?php $count--; ?>
	    	<?php endforeach; ?>
	    	<li class="birthday-show-more">
		      <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'birthday', 'controller'
            => 'index', 'action' => 'view'), $this->translate('More &raquo;'));  ?>
        </li>
	    </ul>
	  </li>
 	<?php endif; ?>

	<!--CASE2: WHEN ADMIN SETTING IS TO SHOW ONLY PHOTO-->
  <?php if($this->display_action == 1) :?>
  	<li>
  		<ul class="birthday-jq-checkpoints">
		    <?php foreach($this->result as $values) : ?>
	      <?php $tooltip_string = ""; ?>
	      <?php if($this->display_count < $this->display_entries) : ?>
      		<li class="birthday_widget_userlist blist_photo">
						<?php $user_subject = Engine_Api::_()->user()->getUser($values->item_id);
						$profile_url = $this->url(array('id' => $values->item_id), 'user_profile'); ?>
            <!--Tooltip code start here-->
      			<?php include APPLICATION_PATH .
            '/application/modules/Birthday/views/scripts/tooltip_birthday.tpl'; ?>
						<!--Tooltip work end-->
						<a href="<?php echo $profile_url ?>" class="birthday_userlist_thumb"> <?php echo
                $this->itemPhoto($this->user($user_subject), 'thumb.icon') ?></a>
					</li>
    		<?php endif; ?>
    		<?php $this->display_count = $this->display_count + 1; ?>
    		<?php endforeach; ?>
	    		<li class="birthday-show-more">
		      <?php
						echo $this->htmlLink(array('route' => 'default', 'module' => 'birthday', 'controller'
            => 'index', 'action' => 'view'), $this->translate('More &raquo;'));      ?>
	      </li>
    	</ul>
    </li>
  <?php endif; ?>


	<!--CASE3: WHEN ADMIN SETTING IS TO SHOW TITLE WITH PHOTO-->
  <?php if($this->display_action == 2) : ?>
  	<li>
  		<ul class="birthday-jq-checkpoints">
	    	<?php foreach($this->result as $values) : ?>
	      <?php $tooltip_string = ""; ?>
	      <?php if($this->display_count < $this->display_entries) : ?>
      		<li class="birthday_widget_userlist blist_both">
						<?php $user_subject = Engine_Api::_()->user()->getUser($values->item_id);
						$profile_url = $this->url(array('id' => $values->item_id), 'user_profile'); ?>
            <!--Tooltip code start here-->
             <?php include APPLICATION_PATH .
                '/application/modules/Birthday/views/scripts/tooltip_birthday.tpl'; ?>
						<!--Tooltip work end-->
						<a href="<?php echo $profile_url ?>" class="birthday_userlist_thumb"> <?php echo
            $this->itemPhoto($this->user($user_subject), 'thumb.icon') ?></a>
						<a href="<?php echo $profile_url ?>" class="birthday_userlist_name"><?php echo
              $this->user($user_subject)->getTitle() ?></a>
					</li>
	      <?php endif; ?>
	      <?php $this->display_count = $this->display_count + 1; ?>
		    <?php endforeach; ?>
	   		<li class="birthday-show-more">
		      <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'birthday',
            'controller' => 'index', 'action' => 'view'), $this->translate('More &raquo;'));  ?>
	      </li>
    	</ul>
    </li>
  <?php endif; ?>

	<!--CASE4: WHEN ADMIN SETTING IS TO SHOW CALENDER-->
  <?php if($this->display_action == 3) : ?>
  	<li>
    <?php $date_last = $this->date_last; ?>
    <?php $date_next = $this->date_next; ?>

    <div id="calender" class="birthday_calendar">
			<div class="caption">
				<div class="pre">
				      <?php
					    $ajax_month = $this->current_month;
					    $prev_array = array();
					    $next_array = array();
					    $array_incr = 12;
					    while($array_incr > date('m', time())+1) {
					      $prev_array[] = $array_incr;
					      $array_incr = $array_incr - 1 ;
					    }
					    $array_incr = 1;
					    while($array_incr < date('m', time())-1) {
					      $next_array[] = $array_incr;
					      $array_incr = $array_incr + 1 ;
					    }
				      ?>
				      <?php if($this->current_year >= date('Y', time()) || ($this->current_year == date('Y', time())-1
&& in_array($ajax_month, $prev_array))) :?>
					  <a href='javascript:void(0);'  onclick = 'another_month(<?php echo $date_last ?>)' title="<?php
echo $this->translate('Previous') ?>"><?php echo $this->translate("&laquo;"); ?></a>
				      <?php endif; ?>
				</div>
				<div class="month_name"><strong><?php echo $this->translate($this->current_month_text); ?>
								<?php echo ",\t". $this->current_year; ?></strong></div>
				<div class="nxt">
				      <?php if($this->current_year <= date('Y', time()) || ($this->current_year == date('Y', time())+1
&& in_array($ajax_month, $next_array))) :?>
					  <a href='javascript:void(0);' onclick = 'another_month(<?php echo $date_next ?>)' title="<?php
echo $this->translate('Next') ?>"><?php echo $this->translate("&raquo;"); ?></a>
				      <?php endif; ?>
				</div>
			</div>
      <table cellpadding="0" cellspacing="0">
      	<thead>
					<?php $day_start = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthday.daystart',
1);?>
					<tr>
					  <?php if($day_start == 1) :?>
					    <td class='' title="<?php echo $this->translate('Sunday') ?>"><div class="day"><?php echo
$this->translate("Su") ?></div></td>
					  <?php endif; ?>
					  <td class='' title="<?php echo $this->translate('Monday') ?>"><div class="day"><?php echo
$this->translate("Mo"); ?></div></td>
					  <td class='' title="<?php echo $this->translate('Tuesday') ?>"><div class="day"><?php echo
$this->translate("Tu") ?></div></td>
					  <td class='' title="<?php echo $this->translate('Wednesday') ?>"><div class="day"><?php echo
$this->translate("We") ?></div></td>
					  <td class='' title="<?php echo $this->translate('Thursday') ?>"><div class="day"><?php echo
$this->translate("Th") ?></div></td>
					  <td class='' title="<?php echo $this->translate('Friday') ?>"><div class="day"><?php echo
$this->translate("Fr") ?></div></td>
					  <td class='' title="<?php echo $this->translate('Saturday') ?>"><div class="day"><?php echo
$this->translate("Sa") ?></div></td>
					  <?php if($day_start == 0) :?>
					    <td class='' title="<?php echo $this->translate('Sunday') ?>"><div class="day"><?php echo
$this->translate("Su") ?></div></td>
					  <?php endif; ?>
					</tr>
				</thead>
				<tbody>
	<?php $i = 1; $date = 1; $empty = 1; $flag = 0;?>
	<?php if($day_start == 1) {
					$this->first_day_of_month++;
					$this->last_day_of_month++;
	      }
	?>
	<?php $total_cells = $this->total_cells ?>
        <?php $total_month_birthcount = count ($this->result);?>
	<?php while( $total_cells > 0) : ?>
	  <?php $entry_counter = 0;
	  $tooltip_string = "";
          $div_active = 0;
           ?>
	  <?php if(($i-1)%7 == 0) { ?>
	    <tr>
	  <?php } ?>
	  <?php if(!empty($this->result)) : ?>
	    <td>
	      <?php $date_entry = 0;
		    $days_count = 1;
        ?>
	      <?php //if($this->current_month == 02 && ($date-1) == 28 && $february_flag == 0 ) { $february_flag
        //=1;} ?>
	      <?php if($i >= $this->first_day_of_month && $i <= $this->last_day_of_month) :?>
		<?php //if($february_flag == 1) { $february_last_entry = 1; }?>
		<?php $flaghead = false; ?>
		    <?php foreach($this->result as $values) : ?>

		      <?php if($date == $values->Day) : ?>
			  <?php if($div_active == 0) :?>
			    <?php if($date == date('d', time()) && $values->Month == date('m', time())) : ?>
			      <div id='view_<?php echo $date ?>' class="day today_birthday"> <?php $div_active++;?>
			    <?php else : ?>
			      <div id='view_<?php echo $date ?>' class="active day"> <?php $div_active++;?>
			     <?php endif;?>
			  <?php endif; ?>

			  <ul class="birthday-jq-checkpoints">
			  <li class="blist_calendar">
			    <?php $entry_counter = $entry_counter+1; ?>
			    	<?php if($entry_counter < 3) : ?>
			      <?php $user_subject = Engine_Api::_()->user()->getUser($values->item_id);
						$profile_url = $this->url(array('id' => $values->item_id), 'user_profile'); ?>
						


<?php if (!$flaghead) : ?>
						<?php if($values->Day == date('d', time()) && $values->Month == date('m', time())) :?>
						<?php
$tooltip_string.= '<div class="info_tip_content_head"><div class="info_tip_content_head_title
fleft">' . $this->translate("Today's Birthdays") . '</div></div>' ?>
<?php endif; ?>
<?php $flaghead = true; endif; ?>


			      <?php $tooltip_string.= "<div class='intu_list'><div class='intu_thumb'><a href=". $profile_url.
">". $this->itemPhoto($this->user($user_subject), 'thumb.icon') . "</a></div>"  ?>

<?php $tooltip_string.= "<div class='intu_body'><div class='intu_title'  id='user_"
.$values->item_id ."'><a href=". $profile_url. ">". $this->user($user_subject)->getTitle().
"</a></div>"; ?>

			        <?php if($values->Day == date('d', time()) && $values->Month == date('m', time())) :?>


			        
<?php
$tooltip_string.= '<div class="intu_stats"><input type="text" id="activity-write-body-'. $values->item_id .'" onfocus="activitywriteonclick($(this), \'' . $this->translate("Write on " . $this->user($user_subject)->getTitle() . "s wall...") .'\', 1, '. $values->item_id . ')"  onblur="activitywriteonclick($(this),\''.$this->translate("Write on " . $this->user($user_subject)->getTitle() . "s wall...") . '\', 2, ' . $values->item_id .')" onkeyup="statusubmit(event, '. $values->item_id . ', \''. $this->translate($this->user($user_subject)->getTitle()) . '\')" value="' . $this->translate("Write on ". $this->user($user_subject)->getTitle() . "s wall...") . '"></input></div>'; ?>
			      	<?php endif; ?>
			     		<?php $tooltip_string.= "<div class='intu_stats'><a class='icon_type_message_birthday buttonlink' href='".
$this->sugg_baseUrl. "/messages/compose/to/". $values->item_id. "'>". $this->translate('Send Message').
"</a></div></div></div>"; ?>

			    <?php endif; ?>
		      <?php else : ?>
		      <?php if($div_active == 0 && $days_count == $total_month_birthcount) :?>
                         <?php $div_active++; endif; ?>
		      <?php endif; ?>
		      <?php $empty = 0;$days_count++; ?>
		  <?php endforeach; ?>
		<?php
		  if($entry_counter != 0) {
		    echo $this->htmlLink(
			    array('route' => 'default', 'module' => 'birthday', 'controller' => 'index', 'action' =>
'viewallcalender', 'year' => $this->current_year, 'month' => $this->current_month ,'date' => $date),
			    $date, array('class'=>'birthdate_link')
		    );
		  ?>
			    <div class="info_tip_wrapper birthday-jq-checkpointSubhead" style="display:none;">
						<div class="uiOverlay info_tip" style="width: 330px;">
							<div class="info_tip_content_wrapper">
								<div class="info_tip_content">
								  <?php echo $tooltip_string; ?>
										<div class="intu_list_more">
											<?php echo $this->htmlLink(
													array('route' => 'default', 'module' => 'birthday', 'controller' => 'index',
                          'action' => 'viewallcalender', 'year' => $this->current_year, 'month' =>
                           $this->current_month ,'date' => $date),	$this->translate("More &raquo;"),
                           array('class'=>'tooltip_seeall'))
                        ?>
										</div>
								</div>
								<i class="info_tip_arrow_right"></i>	  
						  </div>
						</div>
			    </div>
		    </li>
		  </ul>
		  <?php

		  }
		  else {
		  	?>
		  	<div class="day">
		    <?php echo $date; ?>

		 <?php }
		  $date = $date+1;
		 ?>
		</div>
	      </div>
	      <?php endif; ?>

	    </td>
	  <?php else : ?>
	    <td>&nbsp;</td>
	  <?php endif; ?>
	  <?php if($i%7 == 0) { ?>
	    </tr>
	  <?php } ?>
	  <?php $i = $i+1; ?>
	  <?php $total_cells = $total_cells-1; ?>
	<?php endwhile; ?>
				</tbody>
      </table>
      <div>
          </div>
   </li>

	<?php if($empty == 1) {?>
		<li>
			<div class="tip"><span style="margin-bottom:0px;font-size:11px;"><?php echo $this->translate("No birthdays in this month."); ?></span></div>
		</li>
	<?php }?>
  <?php endif; ?>
</ul>
