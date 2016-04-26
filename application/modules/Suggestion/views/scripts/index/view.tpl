<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<script type="text/javascript">
  en4.suggestion = {

  };
  en4.core.setBaseUrl('<?php echo $this->url(array(), 'default', true) ?>');
  var cancelSuggestion = function(entity, entity_id, notificationType)
  {
    // SENDING REQUEST TO AJAX
    var request = en4.suggestion.displays.disInfo(entity, entity_id, notificationType);
    // RESPONCE FROM AJAX
    request.addEvent('complete', function(responseJSON)
    {
      if(responseJSON.status)
      { // Redirect to the "Suggestion Listing Page" with confirm message.
        window.location = en4.core.baseUrl + "suggestions/viewall?type=" + entity;
      }
    });
  }

  en4.suggestion.displays = {
  
    disInfo : function(entity, entity_id, notificationType)
    {
      var request = new Request.JSON({
        url : en4.core.baseUrl + 'suggestion/main/suggestion-cancel',
        data : {
          format: 'json',
          entity_id: entity_id,
          entity: entity,
          notificationType: notificationType
        }
      });
      request.send();
      return request;
    }
  }
</script>

<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/scripts/core.js');
$getSuggestionInfo = $this->getSuggestion;
$modInfos = $getSuggestionInfo['modInfos'];
$senderName = $getSuggestionInfo['senderName'];
$suggestionTableObj = $getSuggestionInfo['suggObj'];
$tempReviewObj = $modObj = $modTableObj = $getSuggestionInfo['modObj'];
$modType = $modInfos['templateInfoFlag'];
if (!empty($modInfos['defaultFriendship'])) {
  $defaultFriendship = $modInfos['defaultFriendship'];
}
$view_type = 'view_page';
include APPLICATION_PATH . '/application/modules/Suggestion/widgets/templateInfoPartial.tpl';
if ($modType == 'photo') {
  $modType = $this->translate('profile photo');
}
?>

<div class="layout_right"></div>
<div class="layout_middle">
<?php
// $modInfo is the content array, which are comming from the "templateInfoPartial.tpl" file.
if (!empty($modInfo)) {
  if ($this->viewer_id == $suggestionTableObj->owner_id) {
?>
  <div class="suggestions_heading">
<?php $labelTitle = $this->translate("You have a %s suggestion", $modInfos['displayName']);

  $module_DisplayName = $modInfos['displayName'];
  $module_DisplayName = strtolower($module_DisplayName);


 ?>
    <span class="fright">
 <?php //$module_DisplayName = $this->translate($module_DisplayName); 
   $getStr = $this->translate('View all %s suggestions', $modInfos['displayName']);
   $getStr = $this->translate($getStr);
?>
      <a href="<?php echo $this->url(array(), 'suggestions_display') ?>#<?php echo $modInfos['templateInfoFlag']; ?>"><?php echo $this->translate('%s &raquo;', $getStr); ?></a>
    </span>
  </div>
  <div class="suggestion_view_list" id="sugg_divid">
    <div class="item_photo">
<?php echo $modInfo['mod_image_normal']; ?>
    </div>
    <div class="item_details">
      <div class="title"><?php echo $modInfo['mod_title_full']; ?></div>
      <div class="description">
        <div><?php echo $modInfo['mod_item_members']; ?></div>
        <div>
        <?php 
  $module_DisplayName = strtolower($module_DisplayName);
  $module_DisplayName = $this->translate($module_DisplayName);
  
  $getSenderTitle = $this->translate('This %s was suggested by', $module_DisplayName);
  $getSenderTitle = $this->translate($getSenderTitle);
 $modBodyText = $this->translate('%s <b> %s </b>', $getSenderTitle, $this->htmlLink($senderName->getHref(), $senderName->getTitle())); ?>
 <?php echo $modBodyText; ?>
        </div>
      </div>
      <div class="item_buttons" id="item_buttons">
        <?php
        $modButton = $modInfo['view_title_link']; // $this->htmlLink($modObj->getHref(), $this->translate( $modInfo['view_title'] ), array('class' => 'buttonlink '));
        $modCancelButton = '<a class="disabled" title="' . $this->translate('Cancel this suggestion') . '" href="javascript:void(0);" onclick="cancelSuggestion(\'' . $suggestionTableObj->entity . '\', \'' . $suggestionTableObj->entity_id . '\', \'' . $modInfos['notificationType'] . '\', \'' . $modInfos['templateInfoFlag'] . '\');"> ' . $this->translate('Ignore') . ' </a>';
        echo $this->translate('%s or %s', $modButton, $modCancelButton);
        ?>
<?php
        if (($modInfos['pluginName'] == 'group') || ($modInfos['pluginName'] == 'event')):
  $shareTitle = $this->translate('Share %s', $modInfos['displayName']);
          echo '<a class="smoothbox link" href="' . $this->sugg_baseUrl . '/activity/index/share/type/' . $modInfos['pluginName'] . '/id/' . $modObj->getIdentity() . '/format/smoothbox">' . $this->translate($shareTitle) . '</a>';
        endif;
?>
            </div>
          </div>
        </div>
        <div id="sugg_cancel"></div>
  <?php
      }else {
        echo "<div class='tip'><span>" . $this->translate('You are not authorized to view this suggestion.') . '</span></div>';
      }
    } else {
      echo "<div class='tip'><span>" . $this->translate('Suggestion not found.') . '</span></div>';
    }
  ?>

</div>
