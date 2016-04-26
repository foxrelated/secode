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

<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1)): ?>

    <?php if((!empty($this->seeAllReviews) && $this->siteevent->rating_users > 0) || ($this->createAllow == 1) || ($this->createAllow == 2)): ?>
        <div class="event_profile_buttons">
            <?php if (!empty($this->seeAllReviews) && $this->siteevent->rating_users > 0): ?>
                <button class="siteevent_buttonlink" onclick="writeAReview('seeAllReviews');"><?php echo $this->translate("See all Reviews") ?></button>
            <?php endif; ?>
            <?php if ($this->createAllow == 1): ?>
                <button class="siteevent_buttonlink" onclick="writeAReview('create');"><?php echo $this->translate("Write a Review") ?></button>
            <?php elseif ($this->createAllow == 2): ?>
                <button class="siteevent_buttonlink" onclick="writeAReview('update');"><?php echo $this->translate("Update your Review") ?></button>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <script type="text/javascript">
        function writeAReview(option) {
            <?php if ($this->event_profile_page): ?>
                if ($('main_tabs') && $('main_tabs').getElement('.tab_layout_siteevent_user_siteevent')) {
                    if ($('siteevent_create') && $('main_tabs').getElement('.tab_layout_siteevent_user_siteevent').hasClass('active')) {
                        window.location.hash = 'siteevent_create';
                        return;
                    } else if ($('siteevent_update') && $('main_tabs').getElement('.tab_layout_siteevent_user_siteevent').hasClass('active')) {
                        window.location.hash = 'siteevent_update';
                        return;
                    }
                    else if (option == 'seeAllReviews' && $('main_tabs').getElement('.tab_layout_siteevent_user_siteevent').hasClass('active')) {
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
                    if (option == 'create') {
                        (function() {
                            window.location.hash = 'siteevent_create';
                        }).delay(3000);
                    } else if (option == 'update') {
                        (function() {
                            window.location.hash = 'siteevent_update';
                        }).delay(3000);
                    }
                } else {
                    if (option == 'create') {
                        // 						(function(){
                        window.location.hash = 'siteevent_create';
                        // 						}).delay(3000);
                    } else if (option == 'update') {
                        // 						(function(){
                        window.location.hash = 'siteevent_update';
                        // 						}).delay(3000);
                    }
                }
            <?php else: ?>
                window.location.href = "<?php echo $this->siteevent->getHref(); ?>";
            <?php endif; ?>
        }
    </script>
<?php else: ?>
    <div id="" class="rating" onmouseout="rating_out1();">
        <span id="rate1_1" class="siteevent_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission)))): ?> onclick="rate1(1);" onmouseover="rating_over1(1);" <?php endif; ?> ></span>
        <span id="rate1_2" class="siteevent_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission)))): ?> onclick="rate1(2);" onmouseover="rating_over1(2);" <?php endif; ?>></span>
        <span id="rate1_3" class="siteevent_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission)))): ?> onclick="rate1(3);" onmouseover="rating_over1(3);" <?php endif; ?>></span>
        <span id="rate1_4" class="siteevent_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission)))): ?> onclick="rate1(4);" onmouseover="rating_over1(4);" <?php endif; ?>></span>
        <span id="rate1_5" class="siteevent_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission)))): ?> onclick="rate1(5);" onmouseover="rating_over1(5);" <?php endif; ?>></span>
        <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission)))): ?><span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate'); ?></span><?php endif; ?>
    </div>
    
    <script type="text/javascript">
        en4.core.runonce.add(function() {
            var pre_rate = <?php echo $this->siteevent->rating_users; ?>;
            var event_id = <?php echo $this->siteevent->event_id; ?>;
            new_text = '';

            var rating_over1 = window.rating_over1 = function(rating) {
                for (var x = 1; x <= 5; x++) {
                    if (x <= rating) {
                        $('rate1_' + x).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big');
                        if ($('rate2_' + x)) {
                            $('rate2_' + x).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big');
                        }
                    } else {
                        $('rate1_' + x).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big_disabled');
                        if ($('rate2_' + x)) {
                            $('rate2_' + x).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big_disabled');
                        }
                    }
                }
            }

            var rating_out1 = window.rating_out1 = function() {
                if (pre_rate != 0) {
                    set_rating1();
                }
                else {
                    for (var x = 1; x <= 5; x++) {
                        $('rate1_' + x).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big_disabled');
                    }
                }
            }

            var set_rating1 = window.set_rating1 = function() {
                var rating = pre_rate;
                $$('.siteevent_rating_star_big_generic').each(function(el) {
                    el.set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big');
                });
                for (var x = parseInt(rating) + 1; x <= 5; x++) {
                    if ($('rate2_' + x)) {
                        $('rate2_' + x).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big_disabled');
                    }
                    $('rate1_' + x).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big_disabled');
                }

                var remainder = Math.round(rating) - rating;
                if (remainder <= 0.5 && remainder != 0) {
                    var last = parseInt(rating) + 1;
                    $('rate1_' + last).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big_half');
                    if ($('rate2_' + last)) {
                        $('rate2_' + last).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big_half');
                    }
                }
            }

            var rate = window.rate1 = function(rating) {
                (new Request.JSON({
                    'format': 'json',
                    'url': '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'review', 'action' => 'rate'), 'default', true) ?>',
                    'data': {
                        'format': 'json',
                        'rating': rating,
                        'event_id': event_id
                    },
                    'onRequest': function() {
                    },
                    'onSuccess': function(responseJSON, responseText)
                    {
                        pre_rate = responseJSON[0].rating;
                        set_rating1();
                    }
                })).send();

            }
            set_rating1();
        });
    </script>  
<?php endif; ?>  