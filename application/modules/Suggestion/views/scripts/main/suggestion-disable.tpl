<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: suggestion-disable.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
if (!empty($this->new_user_obj)) {
?>
  <script type="text/javascript">
    var add_id = "<?php echo $this->new_user_obj[0]->user_id; ?>";
    // Suggestion which are "Disply" in the widget.
    friend_displayed_suggestions += ',' + add_id;
  </script>
<?php
  $suggestion_home_user_id = $this->new_user_obj[0]->user_id;
  $suggestion_home_image = $this->htmlLink($this->new_user_obj[0]->getHref(), $this->itemPhoto($this->new_user_obj[0], 'thumb.icon'), array('class' => 'item_photo'));
  $suggestion_home_displayname = $this->htmlLink($this->new_user_obj[0]->getHref(), Engine_Api::_()->suggestion()->truncateTitle($this->new_user_obj[0]->getTitle()), array('title' => $this->new_user_obj[0]->getTitle()));

  // The "X" link
  $suggestion_home_cancel = '<a class="suggest_cancel" title="Do not show this suggestion" href="javascript:void(0);" onclick="cancelFriSuggestion(\'' . $this->new_user_obj[0]->user_id . '\', \'' . $this->div_id . '\');" style="margin-top:5px;"></a>';
  $user_relation = $this->userFriendship($this->new_user_obj[0]);

  echo '<div class="list" ><div class="user_photo">' . $suggestion_home_image . '</div><div class="user_details"><span style="float:right;">' . $suggestion_home_cancel . '</span><p class="name">' . $suggestion_home_displayname . '<br></p>' . $user_relation . '</div></div>';
} else {
  $current_display_sugg = --$this->display_suggestion;
  // In widget if no record available then show message.
  if ($current_display_sugg == 0) {
    echo "<div class='tip'><span> " . $this->translate("You do not have any more friend suggestions.") . "</span></div>";
  } else {
?>
    <script type="text/javascript">
      suggestion_display_count = '<?php echo $current_display_sugg; ?>';
    </script>
<?php
  }
}
?>