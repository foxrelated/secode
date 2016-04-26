<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    List
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: templatePartial.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/scripts/core.js');
$div_id = 1;
$modInfoArray = $this->modInfo;
$getWidLimit = $this->getWidLimit;
$tempFindFriendFlag = !empty($this->tempFindFriendFlag)? true: false;
$is_middle_layout_enabled = $is_activityPlugin = false;
$linkId = '';
$is_middle_layout_enabled = $is_welcomeTab_enabled = 0;

if(  !empty($this->is_welcomeTab_enabled) ) {
	$is_welcomeTab_enabled = true;
}

if( !empty($this->getLayout) ) {
	$is_middle_layout_enabled = true;
}

if (!empty($modInfoArray['mod_type'])) {
  $modInfoView = $modInfoType = $modInfoArray['mod_type'];
}
if( !empty($this->is_activityPlugin) ) {
  $is_activityPlugin = true;
}
$recommendedStartFlag = $this->recommendedStartFlag;
$recommendedEndFlag = $this->recommendedEndFlag;
$block_div = '';
$showAllFlag = 0;
if (!empty($this->showAllFlag)) {
  $showAllFlag = $this->showAllFlag;
}
$isAjaxRecommended = $this->is_recommended;
$isAjax = $this->isAjax; // That variable set only when calling this file from Ajax.
$is_div_id = $this->div_id; // Get Ajax Div_ID
if (!empty($modInfoArray['mod_view']) && !empty($recommendedEndFlag)) {
  $modInfoType = $modInfoArray['mod_type'];
  $modInfoView = $modInfoArray['mod_view'];
  $modInfoArray['count'] = $recommendedEndFlag + 1;
  $div_id = $recommendedStartFlag + 1;
}
if (!empty($isAjaxRecommended)) {
  $modInfoView = $isAjaxRecommended;
}

// Make an class name b/c for "Explore Suggestion" there are no need this class and for other "Mix or Other Widgets" this class required.
$block_class = 'generic_suggestion_widget';
$explore_class = '';
if (($modInfoView == 'explore') || ($modInfoView == 'findFriend') || !empty($is_middle_layout_enabled) ) {
  $block_div = $block_class = '';
  $explore_class = 'generic_suggestion_explore';
}
if (($modInfoView == 'findFriend')) {
  $block_class = 'generic_suggestion_find';
}

$gridview_class= '';
if( ($modInfoView == 'friend') && !empty($is_middle_layout_enabled) ) {
  $modInfoView = 'friend_middle';
  $gridview_class = 'seaocore_grid_view'; 
}



?>

<script type="text/javascript">
  var <?php echo $modInfoView . '_wid_content_count'; ?> = "<?php echo $modInfoArray['count']; ?>";
  var display_mod_str;
</script>

  <?php if (empty($isAjax) && empty($recommendedStartFlag)): ?>
  <div class="<?php echo $block_class; ?>">

  <?php
// This is the "See All" link which will be display only in "PeopleYouMayKnow widgets". 

if( !empty($showAllFlag) && $showAllFlag == 'friend' && empty($is_activityPlugin) ) {
	echo '<div class="suggestion_view_all"><a id="pymk_see_al" href="' . $this->url(array(), 'friends_suggestions_viewall' ) . '" title="'.$this->translate("Find your Friends").'">'.$this->translate("See All").'</a></div>';
}
?>
	<?php 
		endif;
		if( !empty($modInfoArray['mod_array']) ) :

      $content_ids = array();
    
    if( $modInfoView == 'magentoint' ) {
      $moValueArray = array();
      $modFlag = 1;
      if( !empty($modInfoArray['mod_array'][0]['magentoint']) ) {
        $totalModCount = @COUNT($modInfoArray['mod_array'][0]['magentoint']);
        $modArray = $modInfoArray['mod_array'][0]['magentoint']->toArray();
        shuffle($modArray);
        
        foreach($modArray as $value) {
          if( $modFlag > $getWidLimit ) {
            break;
          }
          $moValueArray[] = $value;
          $modFlag++;
        }
        $modInfoArray['mod_array'][0]['magentoint'] = $moValueArray;
      }
    }
    $tempLimit = 1;
      foreach ($modInfoArray['mod_array'] as $modInfos):
        foreach ($modInfos as $modType => $modObjects):
          foreach ($modObjects as $modObj):
        
        if(empty($tempFindFriendFlag) && ($modType == 'friend' || $modType == 'user') && isset($tempLimit) && isset($getWidLimit) && $tempLimit > $getWidLimit)
          break;

        if( ($modInfoArray['mod_type'] == 'magentoint') && is_array($modObj) ) {
          $getModId = $modObj['link_id'];
        }else {
          $getModId = $modObj->getIdentity();
        }
        
  ?>
            <script	type="text/javascript">
              display_mod_str = display_mod_str + ',' + '<?php echo $modInfoType; ?>' + '_' + '<?php echo $getModId; ?>';
            </script>
  <?php
            $modDivId = $modInfoView . '_' . $div_id;
            if (!empty($is_div_id)) {
              $modDivId = $is_div_id;
            }
            $content_ids[] = $getModId;
            $isTitle = '';
            include APPLICATION_PATH . '/application/modules/Suggestion/widgets/templateInfoPartial.tpl';
            if (empty($is_div_id)):
  ?>
        <div id="<?php echo $modInfoView . '_' . $div_id; ?>" class="<?php echo $explore_class; ?>" style="">
        <?php endif; ?>
          <div class="suggestion_list <?php echo $gridview_class; ?>">
            <div class="item_photo" id="<?php if ($modType == 'friend') {
                echo 'item_photo_' . $modInfoView . '_' . $getModId;
              } ?>">
              <?php echo $modInfo['mod_image']; ?>
            </div>
            <div class="item_details" id="<?php if ($modType == 'friend') {
                echo 'item_details_' . $modInfoView . '_' . $getModId;
              } ?>">
              <?php
                      // if (!empty($modInfoArray['viewer_id']) && empty($is_welcomeTab_enabled)):	// If cross not required for the welcome tab then enabled this code.
              if (!empty($modInfoArray['viewer_id'])):
                if ($modType == 'friend') {
                  $linkId = 'item_cancel_' . $modInfoView . '_' . $getModId;
                }
                echo '<a class="suggest_cancel" title="' . $this->translate('Do not show this suggestion') . '" href="javascript:void(0);" onclick="takeContent(' . $getModId . ', \'' . $modInfoView . '\', \'' . $modDivId . '\', \'' . $modType . '\', ' . $is_middle_layout_enabled . ');" id="' . $linkId . '"></a>';
              endif; ?>
                  <div class="item_title" id="<?php if ($modType == 'friend') {
                echo 'item_title_' . $modInfoView . '_' . $getModId;
              } ?>"><?php echo $modInfo['mod_title']; ?></div>
                    <div class="item_stats">
  <?php echo $modInfo['mod_item_members']; ?>
                    </div>
              <?php if( ($modType == 'siteevent') && ($modInfoView == 'siteevent') ): ?>
                    
              <?php else: ?>
                <div class="item_stats">
  <?php echo $modInfo['view_title']; ?>
                    </div>
              <?php  endif; ?>
<?php
              $div_id++;
?>				
                  </div>
                </div>
<?php if (empty($is_div_id)): ?>
                </div>
<?php endif; $tempLimit++; ?>
<?php endforeach; ?>
<?php endforeach; ?>
<?php
                endforeach;
              endif;
?>
<?php echo $block_div; ?>
<?php if (empty($isAjax) && ($recommendedStartFlag == $recommendedEndFlag)): ?></div><?php endif; ?>