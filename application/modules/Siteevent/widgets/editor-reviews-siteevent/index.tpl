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

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_comment.css'); ?>

<?php
if ($this->addEditorReview):
    $this->headLink()
            ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_rating.css');
endif;

$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
if ($this->show_slideshow):
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/_class.noobSlide.packed.js');
endif;
?>

<?php if ($this->loaded_by_ajax): ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_siteevent_editor_reviews_siteevent')
        }
        en4.siteevent.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>

<?php if ($this->showContent): ?>
    <?php if ($this->addEditorReview): ?>
        <script type="text/javascript">
            var editorPageAction = function(page) {

                $('pagination_loader_image').style.display = 'block';

                var url = en4.core.baseUrl + 'widget/index/mod/siteevent/name/editor-reviews-siteevent';
                en4.core.request.send(new Request.HTML({
                    'url': url,
                    'data': {
                        'format': 'html',
                        subject: en4.core.subject.guid,
                        'isAjax': 1,
                        'page': page
                    },
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('pagination_loader_image').style.display = 'none';
                    }
                }), {
                    'element': $('editorReviewContent').getParent()
                });
            }
        </script>
    <?php endif; ?>

    <div class="siteevent_profile_tab_content clr">
        <?php if ($this->show_slideshow): ?>
            <?php echo $this->content()->renderWidget("siteevent.slideshow-list-photo", array('show_slideshow_always' => 1, 'slideshow_width' => $this->slideshow_width, 'slideshow_height' => $this->slideshow_height, 'showCaption' => $this->showCaption, 'captionTruncation' => $this->captionTruncation, 'showButtonSlide' => $this->showButtonSlide, 'mouseEnterEvent' => $this->mouseEnterEvent, 'thumbPosition' => $this->thumbPositions, 'autoPlay' => $this->autoPlay, 'slidesLimit' => $this->slidesLimit)) ?>
        <?php endif; ?>
        <div id='editorReviewContent'>
            <?php if ($this->addEditorReview): ?>

                <?php if ($this->current == 1): ?>
                    <div id="review_content">
                        <div class="siteevent_profile_review b_medium siteevent_review_block">
                            <div class="siteevent_profile_review_left">
                                <div class="siteevent_profile_review_title">
                                    <?php echo $this->translate("Editor Rating"); ?>
                                </div>	 
                                <div class="siteevent_profile_review_stars">
                                    <?php $ratingData = Engine_Api::_()->getDbtable('ratings', 'siteevent')->profileRatingbyCategory($this->review->review_id); ?>
                                    <?php foreach ($ratingData as $reviewCat): ?>
                                        <?php if (empty($reviewCat['ratingparam_name'])): ?>
                                            <span class="siteevent_profile_editorreview_overall_rating">
                                                <span class="fleft">
                                                    <?php echo $this->ShowRatingStarSiteevent($reviewCat['rating'], 'editor', 'big-star'); ?>
                                                </span>
                                                <?php if (count($ratingData) > 1): ?>
                                                    <i class="arrow_btm fleft"></i>
                                                <?php endif; ?>
                                            </span> 
                                            <?php break; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>	

                                <!--Rating Breakdown Hover Box Starts-->
                                <?php if (count($ratingData) > 1): ?>
                                    <div class="siteevent_ur_bdown_box_wrapper br_body_bg b_medium">
                                        <div class="siteevent_ur_bdown_box">
                                            <div class="siteevent_profile_review_title">
                                                <?php echo $this->translate("Editor Rating"); ?>
                                            </div>	 
                                            <div class="siteevent_profile_review_stars">
                                                <?php foreach ($ratingData as $reviewCat): ?>
                                                    <?php if (empty($reviewCat['ratingparam_name'])): ?>
                                                        <?php echo $this->ShowRatingStarSiteevent($reviewCat['rating'], 'editor', 'big-star'); ?>
                                                        <?php break; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>	

                                            <div class="siteevent_profile_rating_parameters">
                                                <?php foreach ($ratingData as $reviewCat): ?>
                                                    <?php if (empty($reviewCat['ratingparam_name'])): ?>
                                                        <?php continue; ?>
                                                    <?php endif; ?>
                                                    <div class="o_hidden">
                                                        <div class="parameter_title">
                                                            <?php echo $this->translate($reviewCat['ratingparam_name']); ?>
                                                        </div>
                                                        <div class="parameter_value">
                                                            <?php echo $this->ShowRatingStarSiteevent($reviewCat['rating'], 'editor', 'small-box', $reviewCat['ratingparam_name']); ?>
                                                        </div>
                                                    </div>	
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="clr"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <!--Rating Breakdown Hover Box Ends-->
                            </div>

                            <div class="siteevent_profile_review_user fright">
                                <div class="siteevent_profile_review_title t_right">
                                    <?php echo $this->translate("Average User Rating"); ?>
                                </div>	 
                                <div class="clr siteevent_profile_review_stars fright siteevent_profile_review_rating_right">
                                    <?php if (count($this->userRatingDataTopbox) > 1): ?>
                                        <i class="arrow_btm fright"></i>
                                    <?php endif; ?>
                                    <span class="fright">
                                        <?php echo $this->ShowRatingStarSiteevent($this->siteevent->rating_users, 'user', 'big-star'); ?>
                                    </span>
                                </div>

                                <?php if (count($this->userRatingDataTopbox) > 1): ?>
                                    <div class="siteevent_ur_bdown_box_wrapper_right br_body_bg b_medium">
                                        <div class="siteevent_ur_bdown_box">
                                            <div class="siteevent_profile_review_title">
                                                <?php echo $this->translate("Average User Rating"); ?>
                                            </div>
                                            <div class="siteevent_profile_review_stars clr">
                                                <?php echo $this->ShowRatingStarSiteevent($this->siteevent->rating_users, 'user', 'big-star'); ?>
                                            </div>
                                            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1)): ?>
                                                <div class="siteevent_profile_review_stat clr">
                                                    <?php //echo $this->translate(array('%s user review', '%s user reviews', ($this->siteevent->review_count - 1)), $this->locale()->toNumber(($this->siteevent->review_count - 1))) ?>
                                                    <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $this->subject()->getNumbersOfUserRating('user')), '<b>' . $this->locale()->toNumber($this->subject()->getNumbersOfUserRating('user')) . '</b>') ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="siteevent_profile_review_title mtop10">
                                                <?php echo $this->translate("Rating Parameter"); ?>
                                            </div>
                                            <div class="siteevent_profile_rating_parameters">
                                                <?php foreach ($this->userRatingDataTopbox as $reviewcatTopbox): ?>
                                                    <?php if (!empty($reviewcatTopbox['ratingparam_name'])): ?>
                                                        <div class="o_hidden">
                                                            <div class="parameter_title">
                                                                <?php echo $this->translate($reviewcatTopbox['ratingparam_name']) ?>
                                                            </div>
                                                            <div class="parameter_value">
                                                                <?php echo $this->ShowRatingStarSiteevent($reviewcatTopbox['avg_rating'], 'user', 'small-box', $reviewcatTopbox['ratingparam_name']); ?>
                                                            </div>
                                                            <div class="parameter_count"><?php echo $this->subject()->getNumbersOfUserRating('user', $reviewcatTopbox['ratingparam_id']); ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="clr"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <!--Rating Breakdown Hover Box Ends-->
                            </div>

                            <div class="siteevent_profile_review_middle">
                                <?php if ($this->min_price < 0): ?>
                                    <div class="siteevent_profile_review_title">
                                        <?php echo $this->translate("Review Date:"); ?>
                                    </div>
                                    <div>
                                        <?php echo $this->timestamp(strtotime($this->review->creation_date)) ?>
                                    </div>
                                <?php else: ?>
                                    <div>
                                        <?php if ($this->min_price == $this->max_price && $this->min_price > 0): ?>
                                            <span style='font-size:24px;'>
                                                <?php echo $this->locale()->toCurrency($this->min_price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?>
                                            </span>
                                        <?php elseif ($this->min_price > 0 && $this->max_price > 0): ?>

                                            <?php echo $this->translate("%s to %1s", "<span style='font-size:24px;'>" . $this->locale()->toCurrency($this->min_price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) . "</span>", $this->locale()->toCurrency($this->max_price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'))); ?>
                                        <?php endif; ?>
                                        <div>
                                            <?php echo $this->translate("Review Date:"); ?>
                                            <?php echo $this->timestamp(strtotime($this->review->creation_date)); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>  

                        <div class="siteevent_editor_review_stats_box b_medium">
                            <?php if ($this->review->pros): ?>
                                <div class='siteevent_reviews_event_proscons'>
                                    <?php echo '<b>' . $this->translate("The Good:") . ' </b>' . $this->viewMore($this->review->pros); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($this->review->cons): ?>
                                <div class="siteevent_reviews_event_proscons"> 
                                    <?php echo '<b>' . $this->translate("The Bad:") . ' </b>' . $this->viewMore($this->review->cons); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->review->title): ?>
                                <div class="siteevent_reviews_event_proscons">
                                    <?php echo '<b>' . $this->translate("The Bottom Line:") . ' </b>' . $this->review->title; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->review->profile_type_review): ?>
                                <div class="siteevent_reviews_event_proscons"> 
                                    <?php $custom_field_values = $this->FieldValueLoopReviewSiteevent($this->review, $this->fieldStructure); ?>
                                    <?php echo htmlspecialchars_decode($custom_field_values); ?>        
                                </div>   
                            <?php endif; ?>

                            <?php if ($this->review->update_reason): ?>
                                <div class="siteevent_reviews_event_proscons">
                                    <?php echo '<b>' . $this->translate("Update On ") . $this->timestamp(strtotime($this->review->modified_date)) . ':</b>' . $this->review->update_reason; ?>
                                </div>
                            <?php endif; ?>          

                        </div>
                    <?php endif; ?>

                    <div class="siteevent_editor_full_review">
                        <?php echo $this->body_pages; ?>
                    </div>    

                    <?php if ($this->showconclusion && $this->review->body): ?>
                        <div class='siteevent_reviews_event_proscons siteevent_editor_review_conclusion b_medium'>
                            <?php echo '<b>' . $this->translate("Conclusion: ") . '</b>'; ?>
                            <?php echo $this->review->body; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($this->pageCount > 1): ?>
                        <div class="seaocore_pagination o_hidden"> 
                            <div class="pages fright">    
                                <ul class="paginationControl">
                                    <?php /* Previous page link */ ?>
                                    <?php if (isset($this->previous)): ?>
                                        <li>
                                            <a href="javascript:void(0)" onclick="javascript:editorPageAction('<?php echo $this->previous; ?>')"><?php echo $this->translate("&#171; Previous") ?></a>
                                        </li>
                                    <?php endif; ?>

                                    <?php foreach ($this->pagesInRange as $page): ?>
                                        <?php if ($page != $this->current): ?>
                                            <li>
                                                <a href="javascript:void(0)" onclick="javascript:editorPageAction('<?php echo $page; ?>')"><?php echo $page; ?></a>
                                            </li>
                                        <?php else: ?>
                                            <li class="selected">
                                                <a href="javascript:void(0)"><?php echo $page; ?></a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                    <?php /* Next page link */ ?>
                                    <?php if (isset($this->next)): ?>
                                        <li>
                                            <a href="javascript:void(0)" onclick="javascript:editorPageAction('<?php echo $this->next; ?>')"><?php echo $this->translate("Next &#187;") ?></a>
                                        </li>	
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <span id="pagination_loader_image" style="display:none;">
                                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif" alt="" />
                            </span>  
                        </div> 
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="show_content_body">
                  <!--PACKAGE BASED CHECKS-->
                    <?php if(empty($this->CanShowOverview)):?>
                      <?php echo $this->siteevent->body;?>
                    <?php else:?>
                    <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1) && $this->overview ? $this->overview : $this->siteevent->body ?>
                    <?php endif; ?>
                </div>	
            <?php endif; ?>
        </div>
    </div>

    <?php
    //CHECK IF THE FACEBOOK PLUGIN IS ENABLED AND ADMIN HAS SET ONLY SHOW FACEBOOK COMMENT BOX THEN WE WILL NOT SHOW THE SITE COMMENT BOX.
    $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
    $success_showFBCommentBox = 0;

    if (!empty($fbmodule) && !empty($fbmodule->enabled) && $fbmodule->version > '4.2.7p1') {

        $success_showFBCommentBox = Engine_Api::_()->facebookse()->showFBCommentBox('siteevent');
    }
    ?>

    <?php if (empty($this->isAjax) && $this->showComments && $success_showFBCommentBox != 1): ?>
        <?php 
           include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listNestedComment.tpl';
        ?>
    <?php endif; ?>
    <?php if (empty($this->isAjax) && $success_showFBCommentBox != 0): ?>
        <?php echo $this->content()->renderWidget("Facebookse.facebookse-comments", array("type" => $this->siteevent->getType(), "id" => $this->siteevent->event_id, 'task' => 1, 'module_type' => 'siteevent', 'curr_url' => ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->siteevent->getHref())); ?>
    <?php endif; ?>

    <?php if ($this->addEditorReview): ?>
        <script type="text/javascript">
            en4.core.runonce.add(function() {
                <?php if ($this->current == 1 && count($ratingData) > 1): ?>
                    $$('.siteevent_profile_editorreview_overall_rating').addEvents({
                        'mouseover': function(event) {
                            document.getElements('.siteevent_ur_bdown_box_wrapper').setStyle('display', 'block');
                        },
                        'mouseleave': function(event) {
                            document.getElements('.siteevent_ur_bdown_box_wrapper').setStyle('display', 'none');
                        }});

                    $$('.siteevent_ur_bdown_box_wrapper').addEvents({
                        'mouseenter': function(event) {
                            document.getElements('.siteevent_ur_bdown_box_wrapper').setStyle('display', 'block');
                        },
                        'mouseleave': function(event) {
                            document.getElements('.siteevent_ur_bdown_box_wrapper').setStyle('display', 'none');
                        }});
                <?php endif; ?>

                <?php if (count($this->userRatingDataTopbox) > 1): ?>
                    $$('.siteevent_profile_review_rating_right').addEvents({
                        'mouseover': function(event) {
                            document.getElements('.siteevent_ur_bdown_box_wrapper_right').setStyle('display', 'block');
                        },
                        'mouseleave': function(event) {
                            document.getElements('.siteevent_ur_bdown_box_wrapper_right').setStyle('display', 'none');
                        }});
                    $$('.siteevent_ur_bdown_box_wrapper_right').addEvents({
                        'mouseenter': function(event) {
                            document.getElements('.siteevent_ur_bdown_box_wrapper_right').setStyle('display', 'block');
                        },
                        'mouseleave': function(event) {
                            document.getElements('.siteevent_ur_bdown_box_wrapper_right').setStyle('display', 'none');
                        }});
                <?php endif; ?>
            });
        </script>
    <?php endif; ?>
<?php endif; ?>