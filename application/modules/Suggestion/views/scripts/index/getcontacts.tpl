<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getcontacts.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

if (!empty($this->addtofriend)) {?>

  <div id='show_sitefriend' style="display:block;">
<?php
} 
else { ?>
  <div id='show_sitefriend' style="display:none;">
<?php
}  
$total = count($this->addtofriend);
echo $total . " friends from your Address Book are on this side and not your friends."; 
if (!empty($this->addtofriend)) {
  $total_contacts = 0;
  foreach($this->addtofriend as $values) {
	foreach ($values as $value) { 
	  $total_contacts++;?>
	  <input type="checkbox" name="contactname_"<?php echo $total_contacts;?>  id="contact_<?php echo $total_contacts;?>" value="<?php echo $value['user_id'];?>" checked="checked">
	  <?php echo $this->itemPhoto($value, 'thumb.icon');
	   echo $value['displayname'];
	} 
  } ?>
  <input type="hidden" name="total_contacts"  id="total_contacts" value="<?php echo $total_contacts;?>" >
  <input type="checkbox" name="select_all"  id="select_all" onclick="checkedAll();" class="cbox" checked="checked">
<?php
}
?>
<input type="button" name="addtofriends"  id="addtofriends" value="Add as Friends" onclick="sendFriendRequests();">
<input type="button" name="skip_addtofriends"  id="skip_addtofriends" value="Skip" onclick="skip_addtofriends();">
</div>

<?php 
if (empty($this->addtofriend)) { ?>
  <div id='show_nonsitefriends' style="display:block;">
<?php
} else { ?>
  <div id='show_nonsitefriends' style="display:none;">
<?php
}
$total = count($this->addtononfriend);
echo  "You have "  . $total . " Gmail contacts that are not on SiteNetwork. Select which contacts to invite from the list below."; 
if (!empty($this->addtononfriend)) {
  $total_contacts = 0;
  foreach($this->addtononfriend as $values) {
	$total_contacts++;?>
	<input type="checkbox" name="nonsitecontactname_"<?php echo $total_contacts;?>  id="nonsitecontact_<?php echo $total_contacts;?>" checked="checked" value='<?php echo $values['contactMail'];?>'>
	<?php
	echo $values['contactMail'];
	echo $values['contactName'];
  } ?>

  <input type="hidden" name="nonsitetotal_contacts"  id="nonsitetotal_contacts" value="<?php echo $total_contacts;?>"  onclick="nonsiteInviteFriends();">
  <input type="checkbox" name="nonsiteselect_all"  id="nonsiteselect_all" onclick="nonsitecheckedAll();" class="cbox" checked="checked">
<?php
}
?>
<input type="button" name="invitefriends"  id="invitefriends" value="Invite Friends" onclick="inviteFriends();">
<input type="button" name="skip_invite"  id="skip_invite" value="Skip" onclick="skipinvites();">
</div>