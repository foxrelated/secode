<!--Tooltip code start here-->
<?php
	$tooltip_string.= "<div class='intu_list'><div class='intu_thumb'><a href=". $profile_url.
	">". $this->itemPhoto($this->user($user_subject), 'thumb.icon'). "</a></div>"  ?>
<?php $tooltip_string.= "<div class='intu_body'><div class='intu_title'  id='user_"
.$values->item_id ."'><a href=". $profile_url. ">". $this->user($user_subject)->getTitle().
"</a></div>"; ?>
<?php
$tooltip_string.= '<div class="intu_stats"><input type="text" id="activity-write-body-'. $values->item_id .'" onfocus="activitywriteonclick($(this), \'' . $this->translate("Write on %s's wall...", $this->user($user_subject)->getTitle()) .'\', 1, '. $values->item_id . ')"  onblur="activitywriteonclick($(this),\''. $this->translate("Write on %s's wall...", $this->user($user_subject)->getTitle()) . '\', 2, ' . $values->item_id .')" onkeyup="statusubmit(event, '. $values->item_id . ', \''. $this->translate($this->user($user_subject)->getTitle()) . '\')" placeholder="' . $this->translate("Write on %s's wall...", $this->user($user_subject)->getTitle()) . '" value=""></input></div>';
?>
<?php $tooltip_string.= "<div class='intu_stats'><a class='icon_type_message_birthday
buttonlink' href='". $this->sugg_baseUrl. "/messages/compose/to/". $values->item_id. "'>".
$this->translate('Send Message'). "</a></div></div></div>"; ?>
<!--Birthday tooltip start here-->
<div class="info_tip_wrapper birthday-jq-checkpointSubhead" style="display:none;">
	<div class="uiOverlay info_tip" style="width: 330px;">
		<div class="info_tip_content_wrapper">
			<div class="info_tip_content">
				<div class="info_tip_content_head">
					<div class="info_tip_content_head_title fleft"><?php echo $this->translate("Today's Birthdays") ?></div>
				</div>
				<?php echo $tooltip_string; ?>
			</div>
			<i class="info_tip_arrow_right"></i>
		</div>
	</div>
</div>
<!--Birthday tooltip end here-->
<!--Tooltip work end-->