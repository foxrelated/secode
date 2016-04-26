<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_rating.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js');
?>

<?php $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1); ?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>

<?php if ($this->enableLocation && $this->map_view && $this->paginator->count() > 0): ?>
    <?php
//GET API KEY
    $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
    $this->headScript()
            ->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
    ?>

<?php endif; ?>

<script type="text/javascript" >

    function owner(thisobj) {
        var Obj_Url = thisobj.href;
        Smoothbox.open(Obj_Url);
    }
</script>

<div id="siteevent_location_map_none" style="display: none;"></div>  

<?php if ($this->is_ajax_load): ?>
    <?php
    $ratingValue = $this->ratingType;
    $ratingShow = 'small-star';
    if ($this->ratingType == 'rating_editor') {
        $ratingType = 'editor';
    } elseif ($this->ratingType == 'rating_avg') {
        $ratingType = 'overall';
    } else {
        $ratingType = 'user';
    }
    ?>

    <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>

    <?php $latitude = $this->settings->getSetting('siteevent.map.latitude', 0); ?>
    <?php $longitude = $this->settings->getSetting('siteevent.map.longitude', 0); ?>
    <?php $defaultZoom = $this->settings->getSetting('siteevent.map.zoom', 1); ?>
    <?php $enableBouce = $this->settings->getSetting('siteevent.map.sponsored', 1); ?>

    <?php $doNotShowTopContent = 0; ?>
    <?php if ($this->categoryName && !empty($this->categoryObject->top_content)): ?>

        <h4 class="siteevent_browse_lists_view_options_head mbot10" style="display: inherit;">
            <?php echo $this->translate($this->categoryName); ?>
        </h4>

        <?php $doNotShowTopContent = 1; ?>
    <?php endif; ?>

    <?php if ($this->category_id && !$this->subcategory_id && !$this->subsubcategory_id && !empty($this->showTopBottomContent)): ?>
        <div class="siteevent_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('siteevent_category', $this->category_id)->top_content; ?></div>
    <?php elseif ($this->subcategory_id && $this->category_id && !$this->subsubcategory_id): ?>
        <div class="siteevent_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('siteevent_category', $this->subcategory_id)->top_content; ?></div>
    <?php elseif ($this->subsubcategory_id && $this->category_id && $this->subcategory_id): ?>
        <div class="siteevent_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('siteevent_category', $this->subsubcategory_id)->top_content; ?></div>
    <?php endif; ?> 

    <?php if ($this->paginator->count() > 0): ?>

        <script type="text/javascript">
            var pageAction = function(page) {

                var form;
                if ($('filter_form')) {
                    form = document.getElementById('filter_form');
                } else if ($('filter_form_siteevent')) {
                    form = $('filter_form_siteevent');
                }
                form.elements['page'].value = page;

                form.submit();
            }
        </script>

        <form id='filter_form_siteevent' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), "siteevent_general", true) ?>' style='display: none;'>
            <input type="hidden" id="page" name="page"  value=""/>
        </form>

      
            <div class="siteevent_browse_lists_view_options b_medium">
                <div class="fleft"> 
                    <?php if ($this->categoryName && $doNotShowTopContent != 1): ?>
                        <h4 class="siteevent_browse_lists_view_options_head">
                            <?php echo $this->translate($this->categoryName); ?>
                        </h4>
                    <?php endif; ?>
                    <?php echo $this->translate(array('%s event found.', '%s events found.', $this->totalResults), $this->locale()->toNumber($this->totalResults)) ?>
                </div>
              
               <?php if ((($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view)) && empty($this->is_ajax)): ?>

                <?php if ($this->enableLocation && $this->map_view): ?> 
                    <span class="seaocore_tab_select_wrapper fright">
                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Map View"); ?></div>
                        <span class="seaocore_tab_icon tab_icon_map_view" onclick="switchview(2);" ></span>
                    </span>
                <?php endif; ?>
                <?php if ($this->grid_view): ?>
                    <span class="seaocore_tab_select_wrapper fright">
                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
                        <span class="seaocore_tab_icon tab_icon_grid_view" onclick="switchview(1);" ></span>
                    </span>
                <?php endif; ?>
                <?php if ($this->list_view): ?>
                    <span class="seaocore_tab_select_wrapper fright">
                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                        <span class="seaocore_tab_icon tab_icon_list_view" onclick="switchview(0);" ></span>
                    </span>
                <?php endif; ?>
               <?php endif; ?>
            </div>
       
        <?php if ($this->list_view): ?>

            <div id="grid_view" <?php if ($this->defaultView != 0): ?> style="display: none;" <?php endif; ?>>

                <?php if (empty($this->viewType)): ?>

                    <ul class="siteevent_browse_list">
                        <?php foreach ($this->paginator as $siteevent): ?>
                            <?php ?>
                            <?php if (!empty($siteevent->sponsored)): ?>
                                <li class="list_sponsered1 b_medium">
                                <?php else: ?>
                                <li class="b_medium">
                                <?php endif; ?>
                                <div class='siteevent_browse_list_photo b_medium'>
                                    <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $siteevent->featured): ?>
                                        <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                                    <?php endif; ?>
                                    <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $siteevent->newlabel): ?>
                                        <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                    <?php endif; ?>

                                    <?php echo $this->htmlLink($siteevent->getHref(), $this->itemPhoto($siteevent, 'thumb.main', '', array('align' => 'center'))) ?>

                                    <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($siteevent->sponsored)): ?>
                                        <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                                            <?php echo $this->translate('SPONSORED'); ?>                 
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) && !empty($this->statistics) && in_array('ratingStar', $this->statistics)): ?>
                                    <div class="siteevent_browse_list_rating">
                                        <?php if (!empty($siteevent->rating_editor) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_editor')): ?>
                                            <div class="clr">	
                                                <div class="siteevent_browse_list_rating_stats">
                                                    <?php echo $this->translate("Editor Rating"); ?>
                                                </div>
                                                <?php $ratingData = $this->ratingTable->ratingbyCategory($siteevent->event_id, 'editor', $siteevent->getType()); ?>
                                                <div class="siteevent_ur_show_rating_star fnone o_hidden">
                                                    <span class="siteevent_browse_list_rating_stars">
                                                        <span class="fleft">
                                                            <?php echo $this->ShowRatingStarSiteevent($siteevent->rating_editor, 'editor', 'big-star'); ?>
                                                        </span>
                                                        <?php if (count($ratingData) > 1): ?>
                                                            <i class="fright arrow_btm"></i>
                                                        <?php endif; ?>
                                                    </span>

                                                    <?php if (count($ratingData) > 1): ?>
                                                        <div class="siteevent_ur_show_rating br_body_bg b_medium">
                                                            <div class="siteevent_profile_rating_parameters siteevent_ur_show_rating_box">

                                                                <?php foreach ($ratingData as $reviewcat): ?>

                                                                    <div class="o_hidden">
                                                                        <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                                                            <div class="parameter_title">
                                                                                <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                                                            </div>
                                                                            <div class="parameter_value">
                                                                                <?php echo $this->ShowRatingStarSiteevent($reviewcat['avg_rating'], 'editor', 'small-box', $reviewcat['ratingparam_name']); ?>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <div class="parameter_title">
                                                                                <?php echo $this->translate("Overall Rating"); ?>
                                                                            </div>	
                                                                            <div class="parameter_value" style="margin: 0px 0px 5px;">
                                                                                <?php echo $this->ShowRatingStarSiteevent($reviewcat['avg_rating'], 'editor', 'big-star'); ?>
                                                                            </div>
                                                                        <?php endif; ?> 
                                                                    </div>

                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div> 
                                        <?php endif; ?>
                                        <?php if (!empty($siteevent->rating_users) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_users')): ?>
                                            <div class="clr">
                                                <div class="siteevent_browse_list_rating_stats">
                                                    <?php echo $this->translate("User Ratings"); ?><br />
                                                    <?php
                                                    $totalUserReviews = $siteevent->review_count;
                                                    if ($siteevent->rating_editor) {
                                                        $totalUserReviews = $siteevent->review_count - 1;
                                                    }
                                                    ?>
                                                    <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1)): ?>
                                                        <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $totalUserReviews), $this->locale()->toNumber($totalUserReviews)) ?>
                                                    <?php endif; ?>
                                                </div>
                                                <?php $ratingData = $this->ratingTable->ratingbyCategory($siteevent->event_id, 'user', $siteevent->getType()); ?>
                                                <div class="siteevent_ur_show_rating_star fnone o_hidden">
                                                    <span class="siteevent_browse_list_rating_stars">
                                                        <span class="fleft">
                                                            <?php echo $this->ShowRatingStarSiteevent($siteevent->rating_users, 'user', 'big-star'); ?>
                                                        </span>
                                                        <?php if (count($ratingData) > 1): ?>
                                                            <i class="fright arrow_btm"></i>
                                                        <?php endif; ?>
                                                    </span>

                                                    <?php if (count($ratingData) > 1): ?>
                                                        <div class="siteevent_ur_show_rating  br_body_bg b_medium">
                                                            <div class="siteevent_profile_rating_parameters siteevent_ur_show_rating_box">

                                                                <?php foreach ($ratingData as $reviewcat): ?>

                                                                    <div class="o_hidden">
                                                                        <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                                                            <div class="parameter_title">
                                                                                <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                                                            </div>
                                                                            <div class="parameter_value">
                                                                                <?php echo $this->ShowRatingStarSiteevent($reviewcat['avg_rating'], 'user', 'small-box', $reviewcat['ratingparam_name']); ?>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <div class="parameter_title">
                                                                                <?php echo $this->translate("Overall Rating"); ?>
                                                                            </div>	
                                                                            <div class="parameter_value" style="margin: 0px 0px 5px;">
                                                                                <?php echo $this->ShowRatingStarSiteevent($reviewcat['avg_rating'], 'user', 'big-star'); ?>
                                                                            </div>
                                                                        <?php endif; ?> 
                                                                    </div>

                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div> 
                                                    <?php endif; ?> 
                                                </div>
                                            </div>  
                                        <?php endif; ?>

                                        <?php if (!empty($siteevent->rating_avg) && ($ratingValue == 'rating_avg')): ?>
                                            <div class="clr">
                                                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1)): ?>
                                                    <div class="siteevent_browse_list_rating_stats">
                                                        <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $siteevent->review_count), $this->locale()->toNumber($siteevent->review_count)) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php $ratingData = $this->ratingTable->ratingbyCategory($siteevent->event_id, null, $siteevent->getType()); ?>
                                                <div class="siteevent_ur_show_rating_star fnone o_hidden">
                                                    <span class="siteevent_browse_list_rating_stars">
                                                        <span class="fleft">
                                                            <?php echo $this->ShowRatingStarSiteevent($siteevent->rating_avg, $ratingType, 'big-star'); ?>
                                                        </span>
                                                        <?php if (count($ratingData) > 1): ?>
                                                            <i class="fright arrow_btm"></i>
                                                        <?php endif; ?>
                                                    </span>

                                                    <?php if (count($ratingData) > 1): ?>
                                                        <div class="siteevent_ur_show_rating  br_body_bg b_medium">
                                                            <div class="siteevent_profile_rating_parameters siteevent_ur_show_rating_box">

                                                                <?php foreach ($ratingData as $reviewcat): ?>

                                                                    <div class="o_hidden">
                                                                        <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                                                                            <div class="parameter_title">
                                                                                <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                                                            </div>
                                                                            <div class="parameter_value">
                                                                                <?php echo $this->ShowRatingStarSiteevent($reviewcat['avg_rating'], $ratingType, 'small-box', $reviewcat['ratingparam_name']); ?>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <div class="parameter_title">
                                                                                <?php echo $this->translate("Overall Rating"); ?>
                                                                            </div>	
                                                                            <div class="parameter_value" style="margin: 0px 0px 5px;">
                                                                                <?php echo $this->ShowRatingStarSiteevent($reviewcat['avg_rating'], 'user', 'big-star'); ?>
                                                                            </div>
                                                                        <?php endif; ?> 
                                                                    </div>

                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div> 
                                                    <?php endif; ?> 
                                                </div>
                                            </div>  
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div class='siteevent_browse_list_info'>  

                                    <div class='siteevent_browse_list_info_header o_hidden'>
                                        <div class="siteevent_list_title">
                                            <?php echo $this->htmlLink($siteevent->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncation), array('title' => $siteevent->getTitle())); ?>
                                        </div>
                                        <div class="clear"></div>
                                    </div>

                                    <?php if (!empty($this->statistics)) : ?>
                                        <?php echo $this->eventInfo($siteevent, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation)); ?>
                                    <?php endif; ?>
                                    <div class='siteevent_browse_list_info_blurb'>

                                        <?php echo $this->viewMore(strip_tags($siteevent->body), 125, 5000); ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                <?php else: ?>

                    <ul class="siteevent_browse_list">
                        <?php foreach ($this->paginator as $siteevent): ?>
                            <?php
                            ?>
                            <li class="b_medium">
                                <div class='siteevent_browse_list_photo b_medium'>
                                    <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $siteevent->featured): ?>
                                        <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                                    <?php endif; ?>
                                    <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $siteevent->newlabel): ?>
                                        <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                    <?php endif; ?>
                                    <?php echo $this->htmlLink($siteevent->getHref(), $this->itemPhoto($siteevent, 'thumb.main', '', array('align' => 'center'))) ?>
                                    <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($siteevent->sponsored)): ?>
                                        <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                                            <?php echo $this->translate('SPONSORED'); ?>                 
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class='siteevent_browse_list_info'>
                                    <div class="siteevent_browse_list_info_header">
                                        <div class="siteevent_list_title_small">
                                            <?php echo $this->htmlLink($siteevent->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncation), array('title' => $siteevent->getTitle())); ?>
                                        </div>
                                    </div>

                                    <div class="siteevent_browse_list_information fleft">
                                        <?php if (!empty($this->statistics)) : ?>
                                            <?php echo $this->eventInfo($siteevent, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation)); ?>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($this->statistics) && in_array('price', $this->statistics) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0) && !empty($siteevent->price) && $siteevent->price > 0) : ?>
                                        <?php $priceInfos = $siteevent->getPriceInfo(); ?>
                                        <?php $priceInfoCount = Count($priceInfos); ?>
                                    <?php endif; ?>

                                    <?php if (0)://if(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.wheretobuy', 0)):  ?>
                                        <div class="siteevent_browse_list_price_info">
                                            <?php if ($priceInfoCount > 0): ?>
                                                <?php
                                                $minPrice = $siteevent->getWheretoBuyMinPrice();
                                                $maxPrice = $siteevent->getWheretoBuyMaxPrice()
                                                ?>
                            <?php if ($minPrice): ?>
                                                    <div class="siteevent_price">

                                                        <?php if ($minPrice == $maxPrice): ?>
                                                            <?php echo $this->locale()->toCurrency($minPrice, $currency); ?>
                                                        <?php elseif ($priceInfoCount == 2): ?>
                                                            <?php echo $this->translate('%s and %1s', $this->locale()->toCurrency($minPrice, $currency), $this->locale()->toCurrency($maxPrice, $currency)); ?>
                                                        <?php else: ?>
                                                            <?php echo $this->locale()->toCurrency($minPrice, $currency); ?> - <?php echo $this->locale()->toCurrency($maxPrice, $currency); ?>
                                                    <?php endif ?>
                                                    </div>
                                                    <?php endif ?>
                                                <div class="siteevent_browse_list_price_info_stats">
                            <?php echo $this->translate(array('at %s store', 'at %s stores', $priceInfoCount), $this->locale()->toNumber($priceInfoCount)) ?>
                                                </div>

                                                <?php $iPrice = 0; ?>
                                                <?php foreach ($priceInfos as $priceInfo): ?>
                                                        <?php $url = $this->url(array('action' => 'redirect', 'id' => $siteevent->getIdentity()), 'siteevent_priceinfo', true) . '?url=' . @base64_encode($priceInfo->url); ?>
                                                    <div class="siteevent_browse_list_price_info_stats">
                                                    <?php if ($priceInfo->price > 0): ?> <?php echo $this->locale()->toCurrency($priceInfo->price, $currency); ?> <?php endif; ?> - <a href="<?php echo $url; ?>" target="_blank"><?php echo $priceInfo->wheretobuy_id == 1 ? $priceInfo->title : $priceInfo->wheretobuy_title; ?></a>
                                                    </div>
                                                    <?php if ($iPrice > 1) break; ?>
                                                    <?php $iPrice++; ?>
                                                <?php endforeach; ?>

                                                <?php //elseif($siteevent->price > 0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.wheretobuy', 0) == 2):  ?>
                                                <?php elseif ($siteevent->price > 0 && 0 == 2): ?>  
                                                <div class="siteevent_price">
                                                <?php echo $this->locale()->toCurrency($siteevent->price, $currency); ?>
                                                </div>
                        <?php endif; ?>

                                        </div>  
                    <?php endif; ?>


                                    <div class='siteevent_browse_list_des o_hidden'>
                                        <?php //if($this->bottomLine):  ?>
                                        <?php //echo $this->viewMore($siteevent->getBottomLine(), 300, 5000); ?>
                                        <?php //else:  ?>
                                        <?php echo $this->viewMore(strip_tags($siteevent->body), 300, 5000); ?>
                    <?php //endif;    ?>
                                    </div>
                                    <div class="clr dblock fright siteevent_browse_list_info_btn">
                                        <a href="<?php echo $siteevent->getHref(); ?>" class="siteevent_buttonlink"><?php echo $this->translate('Details &raquo;'); ?></a>
                                    </div>  
                                </div>
                            </li>
                <?php endforeach; ?>
                    </ul>

            <?php endif; ?>

            </div>

        <?php endif; ?>

        <?php if ($this->grid_view): ?>
            <div id="image_view" class="siteevent_container" <?php if ($this->defaultView != 1): ?> style="display: none;" <?php endif; ?>>
                <div class="siteevent_img_view">
                    <?php $isLarge = ($this->columnWidth > 170); ?>
            <?php foreach ($this->paginator as $siteevent): ?>          
                        <div class="siteevent_grid_view" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
                            <div class="siteevent_grid_thumb">
                                <?php if (!empty($this->statistics) && in_array('newLabel', $this->statistics) && $siteevent->newlabel): ?>
                                    <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                    <?php endif; ?>
                                <a href="<?php echo $siteevent->getHref() ?>" class="siteevent_thumb">
                                    <?php
                                    $url = $siteevent->getPhotoUrl($isLarge ? 'thumb.main' : 'thumb.main');
                                    if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_event_thumb_main.png';
                                    endif;
                                    ?>
                                    <span style="background-image: url(<?php echo $url; ?>);"></span>
                                </a>
                                <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $siteevent->featured): ?>
                                    <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                                <?php endif; ?>
                                    <?php if (!empty($this->titlePosition)) : ?>
                                    <div class="siteevent_grid_title">
                                    <?php echo $this->htmlLink($siteevent->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncationGrid), array('title' => $siteevent->getTitle())) ?>
                                    </div>
                            <?php endif; ?>
                            </div>
                                <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($siteevent->sponsored)): ?>
                                <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                                <?php echo $this->translate('SPONSORED'); ?>                 
                                </div>
                                <?php endif; ?>
                            <div class="siteevent_grid_info">
                                    <?php if (empty($this->titlePosition)) : ?>
                                    <div class="siteevent_grid_title">
                                    <?php echo $this->htmlLink($siteevent->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncationGrid), array('title' => $siteevent->getTitle())) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($this->statistics)) : ?>
                                    <?php echo $this->eventInfo($siteevent, $this->statistics, array('view_type' => 'grid_view', 'titlePosition' => $this->titlePosition, 'ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation)); ?>
                <?php endif; ?>
                            </div>
                            
                            <?php 
                            if ($this->params['shareOptions']) {
                                $this->subject = $siteevent;
                                include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_shareEventButtons.tpl';
                            }
                            ?>
                        </div>
            <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div id="siteevent_map_canvas_view_browse" <?php if ($this->defaultView != 2): ?> style="display: none;" <?php endif; ?>>
            <div class="seaocore_map clr" style="overflow:hidden;">
                <div id="siteevent_browse_map_canvas" class="siteevent_list_map"> </div>
                <div class="clear mtop10"></div>
                <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
                <?php if (!empty($siteTitle)) : ?>
                    <div class="seaocore_map_info"><?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?></div>
            <?php endif; ?>
            </div>
            <?php if ($this->enableLocation && $this->flageSponsored && $this->map_view && $enableBouce): ?>
                <a href="javascript:void(0);" onclick="toggleBounce()" class="fleft siteevent_list_map_bounce_link"> <?php echo $this->translate('Stop Bounce'); ?></a>
        <?php endif; ?>  
        </div>

      <div class="clr" id="scroll_bar_height"></div>
      <?php if (empty($this->is_ajax)) : ?>
        <div class = "seaocore_view_more mtop10" id="seaocore_view_more" style="display: none;">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
              'id' => '',
              'class' => 'buttonlink icon_viewmore'
          ))
          ?>
        </div>
    <div class="seaocore_view_more" id="loding_image" style="display: none;">
      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
      <?php echo $this->translate("Loading ...") ?>
    </div>
    <div id="hideResponse_div"> </div>
  <?php endif; ?>
    <?php elseif (isset($this->params['tag_id']) || isset($this->params['category_id'])): ?>
        <br/>
        <div class="tip mtip10">
            <span> <?php echo $this->translate('Nobody has created an event with that criteria.'); ?>
              <?php if ($this->can_create): ?>
                <?php if (Engine_Api::_()->siteevent()->hasPackageEnable()):?>
                  <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'index'), "siteevent_package") . '">', '</a>'); ?>
                <?php else:?>
                  <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'create'), "siteevent_general") . '">', '</a>'); ?>
                <?php endif;?>
        <?php endif; ?>
            </span> 
        </div>
    <?php else: ?>
        <div class="tip mtop10"> 
            <span> 
                <?php echo $this->translate('No events have been created yet.'); ?>
                <?php if ($this->can_create): ?>
                   <?php if (Engine_Api::_()->siteevent()->hasPackageEnable()):?>
                  <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'index'), "siteevent_package") . '">', '</a>'); ?>
                <?php else:?>
                  <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'create'), "siteevent_general") . '">', '</a>'); ?>
                <?php endif;?>
        <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>

    <?php if(empty($this->is_ajax)):?>
      <script type="text/javascript" >
              function switchview(flage) {
                  if (flage == 2) {
                      if ($('siteevent_map_canvas_view_browse')) {
                          $('siteevent_map_canvas_view_browse').style.display = 'block';
      <?php if ($this->enableLocation && $this->map_view && $this->paginator->count() > 0): ?>
                              google.maps.event.trigger(map, 'resize');
                              map.setZoom(<?php echo $defaultZoom ?>);
                              map.setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
      <?php endif; ?>
                          if ($('grid_view'))
                              $('grid_view').style.display = 'none';
                          if ($('image_view'))
                              $('image_view').style.display = 'none';
                      }
                  } else if (flage == 1) {
                      if ($('image_view')) {
                          if ($('siteevent_map_canvas_view_browse'))
                              $('siteevent_map_canvas_view_browse').style.display = 'none';
                          if ($('grid_view'))
                              $('grid_view').style.display = 'none';
                          $('image_view').style.display = 'block';
                      }
                  } else {
                      if ($('grid_view')) {
                          if ($('siteevent_map_canvas_view_browse'))
                              $('siteevent_map_canvas_view_browse').style.display = 'none';
                          $('grid_view').style.display = 'block';
                          if ($('image_view'))
                              $('image_view').style.display = 'none';
                      }
                  }
              }
              <?php if ($this->paginator->count() > 0): ?>
      switchview('<?php echo $this->defaultView ?>');
    <?php endif; ?>
      </script>
    <?php endif;?>

    <script type="text/javascript">

        /* moo style */
        en4.core.runonce.add(function() {
            //opacity / display fix
            $$('.siteevent_tooltip').setStyles({
                opacity: 0,
                display: 'block'
            });
            //put the effect in place
            $$('.jq-siteevent_tooltip li').each(function(el, i) {
                el.addEvents({
                    'mouseenter': function() {
                        el.getElement('div').fade('in');
                    },
                    'mouseleave': function() {
                        el.getElement('div').fade('out');
                    }
                });
            });
        });

    </script>

    <?php if ($this->enableLocation && $this->map_view && $this->paginator->count() > 0): ?>

        <script type="text/javascript">
            //<![CDATA[
            // this variable will collect the html which will eventually be placed in the side_bar
            var side_bar_html = "";

            // arrays to hold copies of the markers and html used by the side_bar
            // because the function closure trick doesnt work there
            var gmarkers = [];

            // global "map" variable
            var map = null;
            var infowindow = [];

            function initialize() {

                // create the map
                var myOptions = {
                    zoom: <?php echo $defaultZoom ?>,
                    center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
                    navigationControl: true,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }
                map = new google.maps.Map(document.getElementById("siteevent_browse_map_canvas"),
                        myOptions);

                google.maps.event.addListener(map, 'click', function() {
                    infowindow.close();
                    google.maps.event.trigger(map, 'resize');
                });
                infowindow = new google.maps.InfoWindow(
                {
                    size: new google.maps.Size(250, 50)
                });
            }
            
                                    // A function to create the marker and set up the event window function
            function createMarker(latlng, name, html) {
                var contentString = html;
                if (name == 0) {
                    var marker = new google.maps.Marker({
                        position: latlng,
                        map: map,
                        animation: google.maps.Animation.DROP,
                        zIndex: Math.round(latlng.lat() * -100000) << 5
                    });
                }
                else {
                    var marker = new google.maps.Marker({
                        position: latlng,
                        map: map,
                        draggable: false,
                        animation: google.maps.Animation.BOUNCE
                    });
                }
                gmarkers.push(marker);
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.setContent(contentString);
                    google.maps.event.trigger(map, 'resize');
                    infowindow.open(map, marker);
                });
            }

            function toggleBounce() {
                for (var i = 0; i < gmarkers.length; i++) {
                    if (gmarkers[i].getAnimation() != null) {
                        gmarkers[i].setAnimation(null);
                    }
                }
            }
            
          en4.core.runonce.add(function() {
            initialize();
          });
        </script>
    <?php endif; ?>

    <?php if(!empty($this->showTopBottomContent)):?>
        <?php if ($this->category_id && !$this->subcategory_id && !$this->subsubcategory_id): ?>
            <div class="siteevent_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('siteevent_category', $this->category_id)->bottom_content; ?></div>
        <?php elseif ($this->subcategory_id && $this->category_id && !$this->subsubcategory_id): ?>
            <div class="siteevent_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('siteevent_category', $this->subcategory_id)->bottom_content; ?></div>
        <?php elseif ($this->subsubcategory_id && $this->category_id && $this->subcategory_id): ?>
            <div class="siteevent_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('siteevent_category', $this->subsubcategory_id)->bottom_content; ?></div>
        <?php endif; ?> 
    <?php endif; ?>
    
  <script type="text/javascript">
      en4.core.runonce.add(function() {
      
      <?php if($this->locations):?>
     <?php foreach ($this->locations as $location) : ?>
        <?php if ($this->siteevent[$location->event_id]->canView()): ?>
                    // obtain the attribues of each marker
                    var lat = <?php echo $location->latitude ?>;
                    var lng =<?php echo $location->longitude ?>;
                    var point = new google.maps.LatLng(lat, lng);
            <?php if (!empty($enableBouce)): ?>
                        var sponsored = <?php echo $this->siteevent[$location->event_id]->sponsored ?>
            <?php else: ?>
                        var sponsored = 0;
            <?php endif; ?>

                    var contentString = "<?php
            echo $this->string()->escapeJavascript($this->partial('application/modules/Siteevent/views/scripts/_mapInfoWindowContent.tpl', array(
                        'siteevent' => $this->siteevent[$location->event_id],
                        'ratingValue' => $ratingValue,
                        'ratingType' => $ratingType,
                        'postedby' => $this->postedby,
                        'statistics' => $this->statistics,
                        'showContent' => array("price", "location"),
                        'ratingShow' => $ratingShow)), false);
            ?>";

                    var marker = createMarker(point, sponsored, contentString);
        <?php endif; ?>
    <?php endforeach; ?>
    <?php endif; ?>
      });
  </script>

<?php else:?>

    <div id="layout_siteevent_browse_events_<?php echo $this->identity; ?>">
        <!--    <div class="seaocore_content_loader"></div>-->
    </div>
        
    <script type="text/javascript"> 
        var requestParams = $merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'});
        var params = {
            'detactLocation': <?php echo $this->detactLocation; ?>,
            'responseContainer': 'layout_siteevent_browse_events_<?php echo $this->identity; ?>',
            'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
            requestParams: requestParams,
        };

        en4.seaocore.locationBased.startReq(params);
    </script>        

<!--    <script type="text/javascript">
        var requestParams = $merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'})
        var params = {
            'detactLocation': <?php echo $this->detactLocation; ?>,
            'responseContainer': 'layout_siteevent_browse_events_<?php echo $this->identity; ?>',
            requestParams: requestParams
        };

        var timeOutForLocationDetaction = 0;

    <?php if ($this->detactLocation): ?>
            var locationsParams = {};
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;

                    mapGetDirection = new google.maps.Map(document.getElementById("siteevent_location_map_none"), {
                        zoom: 8,
                        center: new google.maps.LatLng(lat, lng),
                        navigationControl: true,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    });

                    if (!position.address) {
                        var service = new google.maps.places.PlacesService(mapGetDirection);
                        var request = {
                            location: new google.maps.LatLng(lat, lng),
                            radius: 500
                        };

                        service.search(request, function(results, status) {
                            if (status == 'OK') {
                                var index = 0;
                                var radian = 3.141592653589793 / 180;

                                locationsParams.location = (results[index].vicinity) ? results[index].vicinity : '';
                                locationsParams.Latitude = lat;
                                locationsParams.Longitude = lng;
                                locationsParams.locationmiles = <?php echo $this->defaultLocationDistance ?>;
                                setLocationsParams();
                            }
                        });
                    } else {
                        var delimiter = (position.address && position.address.street != '' && position.address.city != '') ? ', ' : '';
                        var location = (position.address) ? (position.address.street + delimiter + position.address.city) : '';

                        locationsParams.location = location;
                        locationsParams.Latitude = lat;
                        locationsParams.Longitude = lng;
                        locationsParams.locationmiles = <?php echo $this->defaultLocationDistance ?>;
                        setLocationsParams();
                    }
                }, function() {
                    timeOutForLocationDetaction = 1;
                    en4.seaocore.locationBased.sendReq(params);
                }, {
                    maximumAge: 6000,
                    timeout: 3000
                });

                var locationTimeout = window.setTimeout(function() {
                    if (timeOutForLocationDetaction == 0) {
                        en4.seaocore.locationBased.sendReq(params);
                    }
                }, 3000);

                var setLocationsParams = function() {
                    if (!document.getElementById('location'))
                        return;
                    document.getElementById('location').value = locationsParams.location;
                    if (document.getElementById('Latitude'))
                        document.getElementById('Latitude').value = locationsParams.latitude;
                    if (document.getElementById('Longitude'))
                        document.getElementById('Longitude').value = locationsParams.longitude;
                    if (document.getElementById('locationmiles'))
                        document.getElementById('locationmiles').value = locationsParams.locationmiles;

                    params.requestParams = $merge(params.requestParams, locationsParams);

                    timeOutForLocationDetaction = 1;

                    en4.seaocore.locationBased.sendReq(params);
                }
            }
            else {
                timeOutForLocationDetaction = 1;
                en4.seaocore.locationBased.sendReq(params);
            }
    <?php else: ?>
            timeOutForLocationDetaction = 1;
            en4.seaocore.locationBased.sendReq(params);
    <?php endif; ?>
    </script>  -->

<?php endif; ?>  
    
    
<?php if (empty($this->is_ajax)) : ?>
  <script type="text/javascript">
    function viewMoreEvent()
    {
      var viewType = 2;
      if($('grid_view')) {
        if($('grid_view').isDisplayed()== true)
          viewType = 0;
      }
      if($('image_view')) {
        if($('image_view').isDisplayed()== true)
          viewType = 1;
      }
   
      $('seaocore_view_more').style.display = 'none';
      $('loding_image').style.display = '';
      var params = {
        requestParams:<?php echo json_encode($this->params) ?>
      };
      setTimeout(function() {
        en4.core.request.send(new Request.HTML({
          method: 'get',
          'url': en4.core.baseUrl + 'widget/index/mod/siteevent/name/browse-events-siteevent',
          data: $merge(params.requestParams, {
            format: 'html',
            subject: en4.core.subject.guid,
            page: getNextPage(),
            is_ajax: 1,
            show_content: '<?php echo $this->paginationType;?>',
            view_type: viewType,
            loaded_by_ajax: true
          }),
          evalScripts: true,
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('hideResponse_div').innerHTML = responseHTML;
            if($('grid_view')) {
              $('grid_view').getElement('.siteevent_browse_list').innerHTML = $('grid_view').getElement('.siteevent_browse_list').innerHTML + $('hideResponse_div').getElement('.siteevent_browse_list').innerHTML;
            }
            if($('image_view')) {
              $('image_view').getElement('.siteevent_img_view').innerHTML = $('image_view').getElement('.siteevent_img_view').innerHTML + $('hideResponse_div').getElement('.siteevent_img_view').innerHTML;
            }
            $('hideResponse_div').innerHTML = '';
            $('loding_image').style.display = 'none';
            switchview(viewType);
          }
        }));
      }, 800);

      return false;
    }
  </script>
<?php endif; ?>

<?php if ($this->paginationType == 3): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'block';
      hideViewMoreLink('<?php echo $this->paginationType; ?>');
    });</script>
<?php elseif ($this->paginationType == 2): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'block';
      hideViewMoreLink('<?php echo $this->paginationType; ?>');
    });</script>
<?php else: ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      $('seaocore_view_more').style.display = 'none';
    });
  </script>
  <?php
  echo $this->paginationControl($this->result, null, array("pagination/pagination.tpl", "siteevent"), array("orderby" => $this->orderby, "query" => $this->formValues));
  ?>
<?php endif; ?>

<script type="text/javascript">

  function getNextPage() {
    return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
  }

  function hideViewMoreLink(showContent) {

    if (showContent == 3) {
      $('seaocore_view_more').style.display = 'none';
      var totalCount = '<?php echo $this->paginator->count(); ?>';
      var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

      function doOnScrollLoadPage()
      {
        if($('scroll_bar_height')) {
          if (typeof($('scroll_bar_height').offsetParent) != 'undefined') {
            var elementPostionY = $('scroll_bar_height').offsetTop;
          } else {
            var elementPostionY = $('scroll_bar_height').y;
          }
          if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {
            if ((totalCount != currentPageNumber) && (totalCount != 0))
              viewMoreEvent();
          }
        }
      }
      
      window.onscroll = doOnScrollLoadPage;

    }
    else if (showContent == 2)
    {
      var view_more_content = $('seaocore_view_more');
      view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
      view_more_content.removeEvents('click');
      view_more_content.addEvent('click', function() {
        viewMoreEvent();
      });
    }
  }
  
en4.core.runonce.add(function () {
 showShareLinks();
});  
</script>
