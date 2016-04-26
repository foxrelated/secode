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
?>
<?php
$div_id = 1;

$modInfoArray = $this->modInfo;
$getWidLimit = $this->getWidLimit;
$is_middle_layout_enabled = $is_activityPlugin = false;
$linkId = '';
$is_middle_layout_enabled = $is_welcomeTab_enabled = 0;

if (!empty($this->is_welcomeTab_enabled)) {
    $is_welcomeTab_enabled = true;
}

if (!empty($this->getLayout)) {
    $is_middle_layout_enabled = true;
}

if (!empty($modInfoArray['mod_type'])) {
    $modInfoView = $modInfoType = $modInfoArray['mod_type'];
}
if (!empty($this->is_activityPlugin)) {
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
if (($modInfoView == 'explore') || ($modInfoView == 'findFriend') || !empty($is_middle_layout_enabled)) {
    $block_div = $block_class = '';
    $explore_class = 'generic_suggestion_explore';
}
if (($modInfoView == 'findFriend')) {
    $block_class = 'generic_suggestion_find';
}

if (($modInfoView == 'friend') && !empty($is_middle_layout_enabled)) {
    $modInfoView = 'friend_middle';
}
?>
<?php if (empty($isAjax) && empty($recommendedStartFlag)): ?>
    <?php
// This is the "See All" link which will be display only in "PeopleYouMayKnow widgets". 
    if (!empty($showAllFlag) && $showAllFlag == 'friend' && empty($is_activityPlugin)) {
        echo '<div class="suggestion_view_all"><a id="pymk_see_al" href="' . $this->url(array(), 'friends_suggestions_viewall') . '" title="' . $this->translate("Find your Friends") . '">' . $this->translate("See All") . '</a></div>';
    }
    ?>

    <?php
endif;
if (!empty($modInfoArray['mod_array'])) :

    $content_ids = array();

    if ($modInfoView == 'magentoint') {
        $moValueArray = array();
        $modFlag = 1;
        if (!empty($modInfoArray['mod_array'][0]['magentoint'])) {
            $totalModCount = @COUNT($modInfoArray['mod_array'][0]['magentoint']);
            $modArray = $modInfoArray['mod_array'][0]['magentoint']->toArray();
            shuffle($modArray);

            foreach ($modArray as $value) {
                if ($modFlag > $getWidLimit) {
                    break;
                }
                $moValueArray[] = $value;
                $modFlag++;
            }
            $modInfoArray['mod_array'][0]['magentoint'] = $moValueArray;
        }
    }
    ?>

    <?php
    foreach ($modInfoArray['mod_array'] as $modInfos):
        foreach ($modInfos as $modType => $modObjects):
            foreach ($modObjects as $modObj):

                if (($modInfoArray['mod_type'] == 'magentoint') && is_array($modObj)) {
                    $getModId = $modObj['link_id'];
                } else {
                    $getModId = $modObj->getIdentity();
                }
                ?>
            <li class="iscroll_item">                            
                    <?php
                    $modDivId = $modInfoView . '_' . $div_id;
                    if (!empty($is_div_id)) {
                        $modDivId = $is_div_id;
                    }
                    $content_ids[] = $getModId;
                    $isTitle = '';
                    include APPLICATION_PATH . '/application/modules/Suggestion/widgets/sitemobile_templateInfoPartial.tpl';
                    ?>
                    <!--UI GRID AND LIST VIEW-->
                    <?php if ($this->recommendationView == 'list'): ?>
                        <a href="<?php echo $modObj->getHref(); ?>">
                            <?php if ($this->contentType == 'mix'): ?>
                                <div class="ui-item-member-action" >
                                    <div style="float: left;" class ="<?php echo $modInfo['view_title']; ?>"></div>  
                                </div>
                            <?php endif; ?>

                            <?php echo $this->itemPhoto($modObj, 'thumb.icon'); ?>
                            <div class="ui-list-content">
                                <h3> <?php echo $modObj->getTitle(); ?></h3>
                                <p><?php echo $modInfo['mod_item_members']; ?></p>
                            </div>
                            <?php
                            $div_id++;
                            ?>
                        </a>
                    <?php elseif ($this->recommendationView == 'grid'): ?>                    
                        <a href="<?php echo $modObj->getHref() ?>" class="ui-link-inherit">
                          <div class="sugg_list_grid_img">
<!--                              <?php //
                            //  $url = $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png';
                           //   $temp_url = $modObj->getPhotoUrl('thumb.profile');
                            //  if (!empty($temp_url)): $url = $modObj->getPhotoUrl('thumb.profile');
                           //   endif;
                              ?>
                              <span style="background-image: url(<?php //echo $url; ?>);"> </span>-->
                            <?php echo $this->itemPhoto($modObj, 'thumb.profile'); ?>
                          </div>
                          <div class="sugg_list_grid_cnt">
                            <?php if ($this->contentType == 'mix'): ?>
                              <div class="sugg_list_grid_cnt_rel <?php echo $modInfo['view_title']; ?>"></div>  
                            <?php endif; ?>
                            <div class="sugg_list_grid_title">
                                <?php echo $this->string()->chunk($this->string()->truncate($modObj->getTitle(), 20), 10); ?>
                            </div>
                            <div class="sugg_list_grid_stats">
                              <?php echo $modInfo['mod_item_members']; ?>
                            </div>
                          </div>

                        </a>
                    <?php endif; ?>
                    <!--UI GRID AND LIST VIEW-->
                </li>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <?php
    endforeach;
    ?>

<?php endif; ?>
<!--        Sitemobile UI   -->                      

<?php echo $block_div; ?>
