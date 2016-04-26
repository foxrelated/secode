<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: viewall.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/scripts/core.js');

if (isset($this->type)) {
  echo '<ul class="form-notices" style="margin:0px;"><li>' . $this->translate('%s suggestion has been deleted.', $this->type) . '</li></ul>';
}

if (!empty($this->getViewAllSugg)) {
  foreach ($this->getViewAllSugg as $getMod => $getModValue) {
    if ($getMod == 'friendfewfriend') {
      $getMod = 'friend';
    }
    $numOfSuggestion = $this->numOfSuggestion;
    $getModInfo = $this->getModInfo;
    $getModName = $this->getModName;    
    $getModDisplayname = $getModInfo[$getMod]['displayName'];
    if (!empty($getModName[$getMod])) {
      $getModDisplayname = $getModInfo[$getModName[$getMod]]['displayName'];
    }
?>
    <a name="<?php echo $getMod; ?>" class="pabsolute"></a>
    <script type="text/javascript">
      var <?php echo $getMod . '_get_display_count'; ?> = "<?php echo $numOfSuggestion[$getMod]; ?>";
    </script>
<?php
    $getModNumOfSuggestion = '<span id="' . $getMod . '_get_count">' . $numOfSuggestion[$getMod] . '</span>';
    if( !empty($getModValue[0]['modInfos']['pluginName']) && strstr($getModValue[0]['modInfos']['pluginName'], "sitereview") ) {
      $getModDisplayname = $getModValue[0]['modInfos']['displayName'];
    }
    $getModDisplayname = $this->translate($getModDisplayname);
    
    
    $dieplaySuggestion = $this->translate('You have %s %s suggestion.', $getModNumOfSuggestion, $getModDisplayname);
    if ($numOfSuggestion[$getMod] > 1) {
      $dieplaySuggestion = $this->translate('You have %s %ss suggestion.', $getModNumOfSuggestion, $getModDisplayname);
    }
    
    // Show how much suggestion you have.
    echo '<div class="suggestions_heading" id="' . $getMod . '_default">' . $dieplaySuggestion . '</div>';
    foreach ($getModValue as $getSuggestionInfo) {
      $modInfos = $getSuggestionInfo['modInfos'];
      $senderName = $getSuggestionInfo['senderName'];
      $senderCount = $getSuggestionInfo['senderCount'];
      $suggestionTableObj = $getSuggestionInfo['suggObj'];
      $tempReviewObj = $modObj = $modTableObj = $getSuggestionInfo['modObj'];
      $numOfSuggestion = $senderDisplayname = '';
      if (!empty($getSuggestionInfo['senderDisplayname'])) {
        $senderDisplayname = $getSuggestionInfo['senderDisplayname'];
      }
      if (!empty($getSuggestionInfo['numOfSuggestion'])) {
        $numOfSuggestion = $getSuggestionInfo['numOfSuggestion'];
      }
      $senderCountMsg = '';
      if ($senderCount > 1) {
        $senderCountMsg = $this->translate('(%s times)', $senderCount);
      }

      $modType = $getMod;
      if (!empty($modInfos['defaultFriendship'])) {
        $defaultFriendship = $modInfos['defaultFriendship'];
      }
      $view_type = 'viewall_page';
      include APPLICATION_PATH . '/application/modules/Suggestion/widgets/templateInfoPartial.tpl';
?>

  <!-- Start Parent Div -->
  <div style="display:block;" class="suggestion_view_list" id="<?php echo $getMod . '_' . $modObj->getIdentity(); ?>">
    <!-- Display Image -->
    <div class="item_photo">
<?php echo $modInfo['mod_image_normal']; ?>
    </div>
    <div class="item_details">
      <div class="title"><?php echo $modInfo['mod_title_full']; ?></div>
    <div class="description">
      <div><?php if (!empty($modInfo['mod_item_members'])) {
          echo $modInfo['mod_item_members'];
        } ?></div>

      <?php $getModDisplayname = strtolower($getModDisplayname); ?>
      <div><?php 
  $getSenderTitle = $this->translate('This %s was suggested by', $getModDisplayname);
  $getSenderTitle = $this->translate($getSenderTitle);
echo $this->translate("%s <b> %s </b> %s", $getSenderTitle, $this->htmlLink($senderName->getHref(), $senderName->getTitle()), $senderCountMsg); ?></div>
    </div>
    <div class="item_buttons">
      <div id="<?php echo 'sugg_viewall_button_' . $suggestionTableObj->suggestion_id ?>">
<?php
        $modButton = $modInfo['view_title_link'];
        $modCancelButton = '<a class="disabled" title="' . $this->translate('Cancel this suggestion') . '" href="javascript:void(0);" onclick="removeSuggNotification(\'' . $suggestionTableObj->entity . '\', \'' . $suggestionTableObj->entity_id . '\', \'' . $modInfos['notificationType'] . '\', \'sugg_viewall_button_' . $suggestionTableObj->suggestion_id . '\', 0, \'' . $senderCount . '\');"> ' . $this->translate('Ignore') . ' </a>';

        echo $this->translate('%s or %s', $modButton, $modCancelButton);
		
        if (($getMod == 'group') || ($getMod == 'event')):
        $shareLink = $this->translate('Share %s', ucfirst($getMod));
          echo '<a class="smoothbox link" href="' . $this->sugg_baseUrl . '/activity/index/share/type/' . $getMod . '/id/' . $modObj->getIdentity() . '/format/smoothbox">' . $this->translate($shareLink) . '</a>';
        endif;
?>
              </div>
            </div>
          </div>
        </div>
<?php
      }
    }
  }else {
    echo '<div class="tip"><span>' . $this->translate('Suggestion not found') . '</span></div>';
  }
