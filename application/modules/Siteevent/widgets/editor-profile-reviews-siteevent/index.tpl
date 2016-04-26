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
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js');
  $this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_rating.css');
  $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');?>

<?php if($this->loaded_by_ajax):?>
  <script type="text/javascript">
    var params = {
      requestParams :<?php echo json_encode($this->params) ?>,
      responseContainer :$$('.layout_siteevent_editor_profile_reviews_siteevent')
    }
    en4.siteevent.ajaxTab.attachEvent('<?php echo $this->identity ?>',params);
  </script>
<?php endif;?>

<?php if($this->showContent): ?>

    <?php if ($this->type == 'editor'): ?>
        <script type="text/javascript">

            var paginatorEditorReviewSiteevent = function(page) {
                var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
                en4.core.request.send(new Request.HTML({
                    'url': url,
                    'data': {
                        'format': 'html',
                        'subject': en4.core.subject.guid,
                        'page': page,
                        'isAjax': 1,
                        'itemCount': '<?php echo $this->itemCount ?>',
                        'type': '<?php echo $this->type ?>'
                    }
                }), {
                    'element': $('editorReviewContent_<?php echo $this->identity; ?>').getParent()
                });
            }

        </script>

        <div id='editorReviewContent_<?php echo $this->identity; ?>' class="o_hidden">
            <?php if ($this->showEditorLink): ?>
                <h4 class="o_hidden">
                    <?php echo $this->htmlLink(array('route' => "siteevent_review_editor_profile", 'username' => $this->user->username, 'user_id' => $this->subject()->user_id), $this->translate('View Editor Profile'), array('class' => 'fright buttonlink_right icon_next')) ?>
                </h4>
            <?php endif; ?>
            <ul class="siteevent_reviews_event o_hidden clr">
                <?php if ($this->paginator->getTotalItemCount()): ?>
                    <?php foreach ($this->paginator as $review): ?>
                        <li>
                            <div class='review_info'>
                                <div class='siteevent_reviews_event_title'>
                                    <?php if ($review->featured): ?>
                                        <i class="siteevent_icon seaocore_icon_featured fright" title="<?php echo $this->translate('Featured'); ?>"></i> 
                                    <?php endif; ?>	 
                                    <?php echo $this->htmlLink($review->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($review->title, $this->truncation), array('title' => $review->title)) ?>
                                </div>

                                <div class="siteevent_reviews_event_stat mbot5">
                                    <?php $ratingData = $review->getRatingData(); ?>
                                    <?php
                                    $rating_value = 0;
                                    foreach ($ratingData as $reviewcat):
                                        if (empty($reviewcat['ratingparam_name'])):
                                            $rating_value = $reviewcat['rating'];
                                            break;
                                        endif;
                                    endforeach;
                                    ?>
                                    <?php echo $this->ShowRatingStarSiteevent($rating_value, $review->type, 'big-star'); ?>
                                </div>

                                <?php $siteevent = $review->getParent() ?>
                                <div class="siteevent_reviews_event_date seaocore_txt_light">
                                    <?php echo $this->translate('For'); ?>  
                                    <?php echo $this->htmlLink($siteevent->getHref(), $siteevent->getTitle()) ?> 
                                    <?php echo $this->translate('on %s', $this->timestamp(strtotime($review->modified_date))); ?>
                                </div>          
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="tip mtop10"> 
                        <span> 
                            <?php echo $this->translate('No Editor Review has been written yet.'); ?>
                        </span>
                    </div>       
                <?php endif; ?>  
            </ul>
            <div class="seaocore_pagination">
                <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
                    <div id="siteevent_previous_<?php echo $this->identity; ?>" class="paginator_previous">
                        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => 'paginatorEditorReviewSiteevent(' . $this->page . ' - 1)', 'class' => 'buttonlink icon_previous')); ?>
                    </div>
                <?php endif; ?>
                <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
                    <div id="siteevent_next_<?php echo $this->identity; ?>" class="paginator_next">
                        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => 'paginatorEditorReviewSiteevent(' . $this->page . ' + 1)', 'class' => 'buttonlink_right icon_next')); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <script type="text/javascript">

            var paginatorUserReviewSiteevent = function(page) {
                var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
                en4.core.request.send(new Request.HTML({
                    'url': url,
                    'data': {
                        'format': 'html',
                        'subject': en4.core.subject.guid,
                        'page': page,
                        'isAjax': 1,
                        'itemCount': '<?php echo $this->itemCount ?>',
                        'type': '<?php echo $this->type ?>'
                    }
                }), {
                    'element': $('userReviewContent_<?php echo $this->identity;?>').getParent()
                });
            }
            var active_request_review = false;
            function reviewHelpful(option, review_id) {
                if (active_request_review)
                    return;
        <?php if (!$this->viewer_id): ?>
                    return;
        <?php endif; ?>
                active_request_review = true;
                var url = en4.core.baseUrl + 'siteevent/review/helpful' + "/helpful/" + option + '/review_id/' + review_id;
                var request = new Request.HTML({
                    url: url,
                    data: {
                        format: 'html'
                    },
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        if ($('review_helpful_message_' + review_id)) {
                            $('review_helpful_message_' + review_id).style.display = 'block';
                        }
                        $('review_helpful_' + review_id).style.display = 'none';
                        active_request_review = false;
                    }
                });
                request.send();
                return false;
            }
        </script>

        <div id='userReviewContent_<?php echo $this->identity;?>' class="o_hidden">
            <ul class="siteevent_reviews_event o_hidden clr">
                <?php foreach ($this->paginator as $review): ?>
                    <li>
                        <div class=" siteevent_reviews_event_title">
                            <?php if ($review->featured): ?>
                                <i class="siteevent_icon seaocore_icon_featured fright" title="<?php echo $this->translate('Featured'); ?>"></i> 
                            <?php endif; ?>	
                            <?php echo $this->htmlLink($review->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($review->title, $this->truncation), array('title' => $review->title)) ?>
                        </div>

                        <div class="siteevent_reviews_event_stat mbot5">
                            <?php $ratingData = $review->getRatingData(); ?>
                            <?php
                            $rating_value = 0;
                            foreach ($ratingData as $reviewcat):
                                if (empty($reviewcat['ratingparam_name'])):
                                    $rating_value = $reviewcat['rating'];
                                    break;
                                endif;
                            endforeach;
                            ?>
                            <?php echo $this->ShowRatingStarSiteevent($rating_value, 'user', 'big-star'); ?>
                        </div>

                        <?php $siteevent = $review->getParent() ?>
                        <div class="siteevent_reviews_event_date seaocore_txt_light">
                            <?php echo $this->translate('For'); ?>  
                            <?php echo $this->htmlLink($siteevent->getHref(), $siteevent->getTitle()) ?> 
                            <?php echo $this->translate('on %s', $this->timestamp(strtotime($review->modified_date))); ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="seaocore_pagination">
                <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
                    <div id="siteevent_previous_<?php echo $this->identity; ?>" class="paginator_previous">
                        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => 'paginatorUserReviewSiteevent(' . $this->page . ' - 1)', 'class' => 'buttonlink icon_previous')); ?>
                    </div>
                <?php endif; ?>
                <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
                    <div id="siteevent_next_<?php echo $this->identity; ?>" class="paginator_next">
                        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => 'paginatorUserReviewSiteevent(' . $this->page . ' + 1)', 'class' => 'buttonlink_right icon_next')); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>