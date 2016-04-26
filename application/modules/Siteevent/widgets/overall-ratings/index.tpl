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

<?php if (($this->siteevent->rating_editor && ($this->show_rating == 'both' || $this->show_rating == 'editor')) || ($this->show_rating == 'both' || $this->show_rating == 'avg')): ?>
    <div class="siteevent_up_overall_rating b_medium">
        <?php if ($this->siteevent->rating_editor && ($this->show_rating == 'both' || $this->show_rating == 'editor')): ?> 
            <div class="siteevent_up_overall_rating_title o_hidden">
                <div class="fright"><?php echo $this->ShowRatingStarSiteevent($this->siteevent->rating_editor, 'editor', 'big-star'); ?></div>
                <div class="o_hidden"><?php echo $this->translate("Editor Rating") ?></div>
            </div>
            <?php if (count($this->ratingEditorData) > 1 && $this->ratingParameter): ?>
                <div class="siteevent_up_overall_rating_paramerers clr">
                    <?php foreach ($this->ratingEditorData as $reviewcat): ?>
                        <?php if (!empty($reviewcat['ratingparam_id'])): ?>
                            <div class="o_hidden">
                                <div class="parameter_count">&nbsp;
                                </div>
                                <div class="parameter_value">
                                    <?php echo $this->ShowRatingStarSiteevent($reviewcat['avg_rating'], 'editor', 'small-box', $reviewcat['ratingparam_name']); ?>
                                </div>

                                <div class="parameter_title">
                                    <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($this->siteevent->rating_editor): ?>
                <div class="siteevent_up_overall_rating_stat">
                    <?php echo $this->translate('%s contributed to this review on %1s.', $this->htmlLink($this->editorReview->getOwner('editor')->getHref(), $this->editorReview->getOwner('editor')->getTitle(), array('')), $this->timestamp(strtotime($this->editorReview->creation_date))) ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($this->show_rating == 'both' || $this->show_rating == 'avg'): ?>
            <?php if ($this->show_rating == 'both'): ?>
                <?php if ($this->siteevent->rating_editor): ?>
                    <div class="siteevent_up_overall_rating_sep b_medium"></div>
                <?php endif; ?>
                <div class="siteevent_up_overall_rating_title o_hidden">
                    <div class="fright"><?php echo $this->ShowRatingStarSiteevent($this->siteevent->rating_users, 'user', 'big-star'); ?></div>
                    <div class="o_hidden"><?php echo $this->translate('User Ratings') ?></div>
                </div>
                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1)): ?>
                    <div class="siteevent_up_overall_rating_stat">
                        <?php echo $this->translate(array('Based on %1s%s review%2s', 'Based on %1s%s reviews%2s', $this->subject()->getNumbersOfUserRating($this->type)), '<a href="javascript:void(0)" onclick="seeAllReviews();">', '<b>' . $this->locale()->toNumber($this->subject()->getNumbersOfUserRating($this->type)) . '</b>',  '</a>') ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="siteevent_up_overall_rating_title o_hidden">
                    <div class="fright"><?php echo $this->ShowRatingStarSiteevent($this->siteevent->rating_avg, 'overall', 'big-star'); ?></div>
                    <?php if ($this->reviewsAllowed == 2): ?>
                        <div class="o_hidden"><?php echo $this->translate('Average User Rating') ?></div>
                    <?php else: ?>
                        <div class="o_hidden"><?php echo $this->translate('Average Rating') ?></div>
                    <?php endif; ?>
                </div>
                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1)): ?>
                    <div class="siteevent_up_overall_rating_stat">
                        <?php
                        echo $this->translate(array('Based on %1s%s review%2s', 'Based on %1s%s reviews%2s', $this->subject()->getNumbersOfUserRating($this->type)), '<a href="javascript:void(0)" onclick="seeAllReviews();">', '<b>' . $this->locale()->toNumber($this->subject()->getNumbersOfUserRating($this->type)) . '</b>', '</a>');
                        ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (count($this->ratingData) > 1 && $this->ratingParameter): ?>
                <div class="siteevent_up_overall_rating_paramerers clr">
                    <?php foreach ($this->ratingData as $reviewcat): ?>
                        <?php if (!empty($reviewcat['ratingparam_id'])): ?>
                            <div class="o_hidden">
                                <div class="parameter_count">
                                    <?php echo $this->subject()->getNumbersOfUserRating($this->type, $reviewcat['ratingparam_id']); ?> </div>
                                <div class="parameter_value">
                                    <?php echo $this->ShowRatingStarSiteevent($reviewcat['avg_rating'], 'user', 'small-box', $reviewcat['ratingparam_name']); ?>
                                </div>

                                <div class="parameter_title">
                                    <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($this->siteevent->rating_users && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.recommend', 1)): ?>
                <div class="siteevent_up_overall_rating_title mtop10">
                    <?php echo $this->translate("Recommendations") ?>
                </div>
                <div class="siteevent_up_overall_rating_stat">
                    <?php echo $this->translate("Recommended by %s users", '<b>' . $this->recommend_percentage . '%</b>'); ?>
                </div>
            <?php endif; ?>
    <?php endif; ?>
    </div>
<?php endif; ?>
<script type="text/javascript">
    function seeAllReviews() {
        if ($('main_tabs') && $('main_tabs').getElement('.tab_layout_siteevent_user_siteevent')) {
            if ($('main_tabs').getElement('.tab_layout_siteevent_user_siteevent').hasClass('active')) {
                window.location.hash = 'user_review';
                return;
            }

            tabContainerSwitch($('main_tabs').getElement('.tab_layout_siteevent_user_siteevent'));
<?php if ($this->contentDetails && isset($this->contentDetails->params['loaded_by_ajax']) && $this->contentDetails->params['loaded_by_ajax']): ?>
                var params = {
                    requestParams:<?php echo json_encode($this->contentDetails->params) ?>,
                    responseContainer: $$('.layout_siteevent_user_siteevent')
                }

                params.requestParams.content_id = '<?php echo $this->contentDetails->content_id ?>';
                en4.siteevent.ajaxTab.sendReq(params);
<?php endif; ?>
        }
    }
</script>
