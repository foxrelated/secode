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


<div class="siteevent_up_overall_rating b_medium">
    <div id="" class="rating" onmouseout="rating_out();">
        <span id="rate2_1" class="siteevent_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission)))): ?> onclick="rate(1);" onmouseover="rating_over(1);" <?php endif; ?> ></span>
        <span id="rate2_2" class="siteevent_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission)))): ?> onclick="rate(2);" onmouseover="rating_over(2);" <?php endif; ?>></span>
        <span id="rate2_3" class="siteevent_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission)))): ?> onclick="rate(3);" onmouseover="rating_over(3);" <?php endif; ?>></span>
        <span id="rate2_4" class="siteevent_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission)))): ?> onclick="rate(4);" onmouseover="rating_over(4);" <?php endif; ?>></span>
        <span id="rate2_5" class="siteevent_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission)))): ?> onclick="rate(5);" onmouseover="rating_over(5);" <?php endif; ?>></span>
        <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission)))): ?><span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate'); ?></span><?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        var pre_rate = <?php echo $this->siteevent->rating_users; ?>;
        var event_id = <?php echo $this->siteevent->event_id; ?>;
        new_text = '';

        var rating_over = window.rating_over = function(rating) {
            for (var x = 1; x <= 5; x++) {
                if (x <= rating) {
                    $('rate2_' + x).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big');
                    if ($('rate1_' + x)) {
                        $('rate1_' + x).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big');
                    }
                } else {
                    $('rate2_' + x).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big_disabled');
                    if ($('rate1_' + x)) {
                        $('rate1_' + x).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big_disabled');
                    }
                }
            }
        }

        var rating_out = window.rating_out = function() {
            if (pre_rate != 0) {
                set_rating();
            }
            else {
                for (var x = 1; x <= 5; x++) {
                    $('rate2_' + x).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big_disabled');
                }
            }
        }

        var set_rating = window.set_rating = function() {
            var rating = pre_rate;
            $$('.siteevent_rating_star_big_generic').each(function(el) {
                el.set('class', 'siteevent_rating_star_big_generic rating_star_big');
            });
            for (var x = parseInt(rating) + 1; x <= 5; x++) {
                $('rate2_' + x).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big_disabled');
                if ($('rate1_' + x)) {
                    $('rate1_' + x).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big_disabled');
                }
            }

            var remainder = Math.round(rating) - rating;
            if (remainder <= 0.5 && remainder != 0) {
                var last = parseInt(rating) + 1;
                $('rate2_' + last).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big_half');
                if ($('rate1_' + last)) {
                    $('rate1_' + last).set('class', 'siteevent_rating_star_big_generic siteevent_rating_star_big_half');
                }
            }
        }

        var rate = window.rate = function(rating) {
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
                    set_rating();
                }
            })).send();

        }
        set_rating();
    });
</script>