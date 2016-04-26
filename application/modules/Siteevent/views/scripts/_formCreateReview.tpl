<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formCreateReview.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_rating.css');
?>

<script type="text/javascript">

    function doRating(element_id, ratingparam_id, classstar) {
        $(element_id + '_' + ratingparam_id).getParent().getParent().className = 'sr-us-box rating-box ' + classstar;
        $('review_rate_' + ratingparam_id).value = $(element_id + '_' + ratingparam_id).getParent().id;
    }

    function doDefaultRating(element_id, ratingparam_id, classstar) {
        $(element_id + '_' + ratingparam_id).getParent().getParent().className = 'siteevent_ug_rating ' + classstar;
        $('review_rate_' + ratingparam_id).value = $(element_id + '_' + ratingparam_id).getParent().id;
    }

</script>

<?php if (empty($this->isajax) && $this->can_create && !$this->hasPosted): ?>
    <?php $rating_value_2 = 0; ?>	
    <?php if (!empty($this->reviewRateMyData)): ?>	
        <?php foreach ($this->reviewRateMyData as $reviewRateMyData): ?>
            <?php if ($reviewRateMyData['ratingparam_id'] == 0): ?>
                <?php $rating_value_2 = $reviewRateMyData['rating']; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php
    switch ($rating_value_2) {
        case 0:
            $rating_value = '';
            break;
        case 1:
            $rating_value = 'onestar';
            break;
        case 2:
            $rating_value = 'twostar';
            break;
        case 3:
            $rating_value = 'threestar';
            break;
        case 4:
            $rating_value = 'fourstar';
            break;
        case 5:
            $rating_value = 'fivestar';
            break;
    }
    ?>

    <div class="form-wrapper" id="overall_rating">
        <div class="form-label">
            <label>
                <?php echo $this->translate("Overall Rating"); ?>
            </label>
        </div>	
        <div id="overall_rating-element" class="form-element">
            <ul id= 'rate_0' class='siteevent_ug_rating <?php echo $rating_value; ?>'>
                <li id="1" class="rate one"><a href="javascript:void(0);" onclick="doDefaultRating('star_1', '0', 'onestar');" title="<?php echo $this->translate("%s Star", 1); ?>"   id="star_1_0">1</a></li>
                <li id="2" class="rate two"><a href="javascript:void(0);"  onclick="doDefaultRating('star_2', '0', 'twostar');" title="<?php echo $this->translate("%s Star", 2); ?>"   id="star_2_0">2</a></li>
                <li id="3" class="rate three"><a href="javascript:void(0);"  onclick="doDefaultRating('star_3', '0', 'threestar');" title="<?php echo $this->translate("%s Star", 3); ?>" id="star_3_0">3</a></li>
                <li id="4" class="rate four"><a href="javascript:void(0);"  onclick="doDefaultRating('star_4', '0', 'fourstar');" title="<?php echo $this->translate("%s Star", 4); ?>"   id="star_4_0">4</a></li>
                <li id="5" class="rate five"><a href="javascript:void(0);"  onclick="doDefaultRating('star_5', '0', 'fivestar');" title="<?php echo $this->translate("%s Star", 5); ?>"  id="star_5_0">5</a></li>
            </ul>
            <input type="hidden" name='review_rate_0' id='review_rate_0' value='<?php echo $rating_value_2; ?>' />
        </div>
    </div>

    <div id="rating-box">
        <?php $rating_value_1 = 0; ?>
        <?php if (!empty($this->total_reviewcats)): ?>
            <?php foreach ($this->reviewCategory as $reviewcat): ?>
                <?php $rating_value_1 = 0; ?>
                <?php if (!empty($this->reviewRateMyData)): ?>	
                    <?php foreach ($this->reviewRateMyData as $reviewRateMyData): ?>
                        <?php if ($reviewRateMyData['ratingparam_id'] == $reviewcat->ratingparam_id): ?>
                            <?php $rating_value_1 = $reviewRateMyData['rating']; ?>
                            <?php break; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php
                switch ($rating_value_1) {
                    case 0:
                        $rating_value = '';
                        break;
                    case 1:
                        $rating_value = 'onestar-box';
                        break;
                    case 2:
                        $rating_value = 'twostar-box';
                        break;
                    case 3:
                        $rating_value = 'threestar-box';
                        break;
                    case 4:
                        $rating_value = 'fourstar-box';
                        break;
                    case 5:
                        $rating_value = 'fivestar-box';
                        break;
                }
                ?>

                <div class="form-wrapper">
                    <div class="form-label">
                        <label>
                            <?php echo $this->translate($reviewcat->ratingparam_name); ?>
                        </label>
                    </div>	
                    <div class="form-element">
                        <ul id= 'rate_<?php echo $reviewcat->ratingparam_id; ?>' class='sr-us-box rating-box <?php echo $rating_value; ?>'>
                            <li id="1" class="rate one"><a href="javascript:void(0);" onclick="doRating('star_1', '<?php echo $reviewcat->ratingparam_id; ?>', 'onestar-box');" id="star_1_<?php echo $reviewcat->ratingparam_id; ?>" title="<?php echo $this->translate('%s Star', '1'); ?>">1</a></li>
                            <li id="2" class="rate two"><a href="javascript:void(0);" onclick="doRating('star_2', '<?php echo $reviewcat->ratingparam_id; ?>', 'twostar-box');" id="star_2_<?php echo $reviewcat->ratingparam_id; ?>" title="<?php echo $this->translate('%s Star', '2'); ?>">2</a></li>
                            <li id="3" class="rate three"><a href="javascript:void(0);"  onclick="doRating('star_3', '<?php echo $reviewcat->ratingparam_id; ?>', 'threestar-box');" id="star_3_<?php echo $reviewcat->ratingparam_id; ?>" title="<?php echo $this->translate('%s Star', '3'); ?>">3</a></li>
                            <li id="4" class="rate four"><a href="javascript:void(0);"  onclick="doRating('star_4', '<?php echo $reviewcat->ratingparam_id; ?>', 'fourstar-box');" id="star_4_<?php echo $reviewcat->ratingparam_id; ?>" title="<?php echo $this->translate('%s Star', '4'); ?>">4</a></li>
                            <li id="5" class="rate five"><a href="javascript:void(0);"  onclick="doRating('star_5', '<?php echo $reviewcat->ratingparam_id; ?>', 'fivestar-box');" id="star_5_<?php echo $reviewcat->ratingparam_id; ?>" title="<?php echo $this->translate('%s Star', '5'); ?>">5</a></li>
                        </ul>
                        <input type="hidden" name='review_rate_<?php echo $reviewcat->ratingparam_id; ?>' id='review_rate_<?php echo $reviewcat->ratingparam_id; ?>' value='<?php echo $rating_value_1; ?>' />
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div id="thankYou" style="display:none;">
    <?php $title = ""; ?>
    <?php if (!empty($this->viewer_id)): ?>
        <?php $user_item = Engine_Api::_()->getItem('user', $this->viewer_id); ?>
        <?php $title = $user_item->getTitle(); ?>
    <?php endif; ?>
    <div class="siteevent_reply_thankyou_msg o_hidden">
        <?php if (empty($this->hasPosted)): ?>
            <?php if (!empty($this->viewer_id)): ?>
                <h4><i class="siteevent_icon_tick siteevent_icon mright5"></i><?php echo $this->translate("Thank You, %s!", $title); ?></h4>
            <?php else: ?>
                <h4><i class="siteevent_icon_tick siteevent_icon mright5"></i><?php echo $this->translate("Thank You!"); ?></h4>
            <?php endif; ?>
            <?php echo $this->translate("Your Review on %s has been successfully submitted.", $this->siteevent->gettitle()); ?>
            <?php if (empty($this->viewer_id)): ?>
                <p><?php echo $this->translate("The site administrator will act on your review and you will receive an email correspondingly."); ?></p>
            <?php endif; ?>
        <?php else: ?>
            <?php if (!empty($this->viewer_id)): ?>
                <h4><i class="siteevent_icon_tick siteevent_icon mright5"></i><?php echo $this->translate("Thank You, %s!", $title); ?></h4>
            <?php else: ?>
                <h4><i class="siteevent_icon_tick siteevent_icon mright5"></i><?php echo $this->translate("Thank You!"); ?></h4>
            <?php endif; ?>
            <?php echo $this->translate("Your Review on %s has been successfully updated.", $this->siteevent->gettitle()); ?>
        <?php endif; ?>
        <ul class="mtop10 siteevent_reply_thankyou_msg_links clr o_hidden">
            <?php if ($this->viewer_id): ?>
                <li id="go_to_review"></li>
            <?php endif; ?>
            <li>
                <a href='<?php echo $this->url(array('event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id, 'slug' => $this->siteevent->getSlug()), "siteevent_entry_view", true) ?>'><?php echo $this->translate("Go to the event %s", $this->siteevent->gettitle()); ?></a>
            </li>
        </ul>
        <div class="clr mtop10">
            <button onclick="closeThankYou();"><?php echo $this->translate('Close'); ?></button>
        </div>
    </div>			
</div>

<div id="background-image" style="display:none;">
    <center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif" /></center>
</div>

<script type="text/javascript">
    var review_has_posted = '<?php echo $this->hasPosted; ?>';

    en4.core.runonce.add(function() {
        if (review_has_posted == 0) {
            showRating($('siteevent_create'), $('overall_rating'), $('rating-box'));
        } else {
            showRating($('siteevent_update'), $('overall_my_rating'), $('rating-my-edit-box'));
        }
    });

    function showRating(formObject, overallratingid, ratingsmallbox) {

        if (formObject && formObject.getElementById('pros-wrapper')) {
            var divRatingId = overallratingid;
            divRatingId.inject(formObject.getElementById('pros-wrapper'), 'before');
            var divRatingbox = ratingsmallbox;
            divRatingbox.inject(formObject.getElementById('pros-wrapper'), 'before');
        } else if (formObject && formObject.getElementById('title-wrapper')) {
            var divRatingId = overallratingid;
            divRatingId.inject(formObject.getElementById('title-wrapper'), 'before');
            var divRatingbox = ratingsmallbox;
            divRatingbox.inject(formObject.getElementById('title-wrapper'), 'before');
        } else if (formObject && formObject.getElementById('body-wrapper')) {
            var divRatingId = overallratingid;
            divRatingId.inject(formObject.getElementById('body-wrapper'), 'before');
            var divRatingbox = ratingsmallbox;
            divRatingbox.inject(formObject.getElementById('body-wrapper'), 'before');
        }

        if (overallratingid)
            overallratingid.style.display = "block";

        if (ratingsmallbox)
            ratingsmallbox.style.display = "block";

    }

    var getImageDiv = $('background-image');
    if ($('submit-wrapper')) {
        getImageDiv.inject($('submit-wrapper'));
    }

    function closeThankYou() {
        Smoothbox.close();
        window.location.href = '<?php echo $this->url(array('event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id, 'slug' => $this->siteevent->getSlug(), 'tab' => Engine_Api::_()->siteevent()->existWidget('siteevent_reviews', 0)), "siteevent_entry_view", true) ?>';
    }

    function submitForm(review_id, formObject, type) {

        var flag = true;
        formElement = formObject;
        var focusEl = '';
        currentValues = formElement.toQueryString();
        if ($('overallrating_error'))
            $('overallrating_error').destroy();
        if ($('pros_error'))
            $('pros_error').destroy();
        if ($('cons_error'))
            $('cons_error').destroy();
        if ($('pros_length_error'))
            $('pros_length_error').destroy();
        if ($('cons_length_error'))
            $('cons_length_error').destroy();
        if ($('title_error'))
            $('title_error').destroy();
        if ($('title_length_error'))
            $('title_length_error').destroy();
        if ($('body_error'))
            $('body_error').destroy();
        if ($('body_length_error'))
            $('body_length_error').destroy();
        if ($('captcha_error'))
            $('captcha_error').destroy();
  $('siteevent_create').getElement('.form-elements').getElementById('title-wrapper').getAllNext().each (function (element, key) { 
                if($('field_error_' + key))
                    $('field_error_' + key).destroy();
		if(element.getElement('.form-label').getElement('.required') && element.getElement('.form-element').getElement('.field_container') && element.getElement('.form-element').getElement('.field_container').value == '') {
                   liElement = new Element('span', {'html':'<?php echo $this->translate("* Please complete this field - it is required."); ?>', 'class':'review_error', 'id':'field_error_' + key}).inject(element.getElement('.form-element'));
                   flag = false;
               
        }	
	});
        if (typeof formElement['review_rate_0'] != 'undefined' && formElement['review_rate_0'].value == 0) {
            liElement = new Element('span', {'html': '<?php echo $this->translate("* Please complete this field - it is required."); ?>', 'class': 'review_error', 'id': 'overallrating_error'}).inject($('overall_rating-element'));
            flag = false;
        }

        if (formElement['pros'] && formElement['pros'].value == '') {
<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proncons', 1)): ?>
                liElement = new Element('span', {'html': '<?php echo $this->translate("* Please complete this field - it is required."); ?>', 'class': 'review_error', 'id': 'pros_error'}).inject($('pros-element'));
                flag = false;
                if (focusEl == '') {
                    focusEl = 'pros';
                }
<?php endif; ?>
        }
        else if (formElement['pros'] && formElement['pros'].value != '') {
            var str = formElement['pros'].value;
            var length = str.replace(/\s+/g, '').length;
            if (length < 10) {
                var message = en4.core.language.translate('<?php echo $this->translate("* Please enter at least 10 characters (you entered %s characters).") ?>', length);
                liElement = new Element('span', {'html': message, 'class': 'review_error', 'id': 'pros_length_error'}).inject($('pros-element'));
                flag = false;
                if (focusEl == '') {
                    focusEl = 'pros';
                }
            }
        }

        if (formElement['cons'] && formElement['cons'].value == '') {
<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proncons', 1)): ?>
                liElement = new Element('span', {'html': '<?php echo $this->translate("* Please complete this field - it is required."); ?>', 'class': 'review_error', 'id': 'cons_error'}).inject($('cons-element'));
                flag = false;
                if (focusEl == '') {
                    focusEl = 'cons';
                }
<?php endif; ?>
        }
        else if (formElement['cons'] && formElement['cons'].value != '') {
            var str = formElement['cons'].value;
            var length = str.replace(/\s+/g, '').length;
            if (length < 10) {
                var message = en4.core.language.translate('<?php echo $this->translate("* Please enter at least 10 characters (you entered %s characters).") ?>', length);
                liElement = new Element('span', {'html': message, 'class': 'review_error', 'id': 'cons_length_error'}).inject($('cons-element'));
                flag = false;
                if (focusEl == '') {
                    focusEl = 'cons';
                }
            }
        }

        if (formElement['title'] && formElement['title'].value == '') {
            liElement = new Element('span', {'html': '<?php echo $this->translate("* Please complete this field - it is required."); ?>', 'class': 'review_error', 'id': 'title_error'}).inject($('title-element'));
            flag = false;
            if (focusEl == '') {
                focusEl = 'title';
            }
        }
        else if (formElement['title'] && formElement['title'].value != '') {
            var str = formElement['title'].value;
            var length = str.replace(/\s+/g, '').length;
            if (length < 2) {
                var message = en4.core.language.translate('<?php echo $this->translate("* Please enter at least 2 characters (you entered %s characters).") ?>', length);
                liElement = new Element('span', {'html': message, 'class': 'review_error', 'id': 'title_length_error'}).inject($('title-element'));
                flag = false;
                if (focusEl == '') {
                    focusEl = 'title';
                }
            }
        }

<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.summary', 1)): ?>
            if (formElement['body'] && formElement['body'].value == '') {
                liElement = new Element('span', {'html': '<?php echo $this->translate("* Please complete this field - it is required."); ?>', 'class': 'review_error', 'id': 'body_error'}).inject($('body-element'));
                flag = false;
                if (focusEl == '') {
                    focusEl = 'body';
                }
            } else if (formElement['body'] && formElement['body'].value != '') {
                var str = formElement['body'].value;
                var length = str.replace(/\s+/g, '').length;
                if (length < 10) {
                    var message = en4.core.language.translate('<?php echo $this->translate("* Please enter at least 10 characters (you entered %s characters).") ?>', length);
                    liElement = new Element('span', {'html': message, 'class': 'review_error', 'id': 'body_length_error'}).inject($('body-element'));
                    flag = false;
                    if (focusEl == '') {
                        focusEl = 'body';
                    }
                }
            }
<?php endif; ?>

        if (formElement['captcha[input]'] && formElement['captcha[input]'].value == '') {
            liElement = new Element('span', {'html': "<?php echo $this->translate("* Please be sure that you've entered the same characters you see in the image."); ?>", 'class': 'review_error', 'id': 'captcha_error'}).inject($('captcha-element'));
            flag = false;
        }

        if (flag == false) {
            if ($(focusEl))
                $(focusEl).focus();
            return false;
        }
        var url = '';
        var hasPosted = '<?php echo $this->hasPosted ?>';
        if (type == 'create') {
            url = '<?php echo $this->url(array('action' => 'create', 'event_id' => $this->event_id), "siteevent_user_general"); ?>';
        } else if (type == 'update') {
            url = '<?php echo $this->url(array('action' => 'update', 'event_id' => $this->event_id), "siteevent_user_general"); ?>';
        }

        if (flag == true) {
            //$('background-image').style.display = 'block';
            var formSubmitElement = formObject.getElementById('submit-wrapper').innerHTML;
            formObject.getElementById('submit-wrapper').innerHTML = $('background-image').innerHTML;

            var request = new Request.JSON({
                url: url + '/review_id/' + review_id,
                method: 'post',
                data: {
                    format: 'html'
                },
                //responseTree, responseElements, responseHTML, responseJavaScript
                onSuccess: function(responseJSON) {
                    var viewer_id = '<?php echo $this->viewer_id; ?>';
                    var captcha = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.captcha', 1); ?>';
                    if (responseJSON && responseJSON.captchaError == 1 && viewer_id == 0 && captcha == 1) {
                        liElement = new Element('span', {'html': "<?php echo $this->translate("* Please be sure that you've entered the same characters you see in the image."); ?>", 'class': 'review_error', 'id': 'body_length_error'}).inject($('captcha-element'));
                        formObject.getElementById('submit-wrapper').innerHTML = formSubmitElement;
                        if ($('background-image'))
                            $('background-image').style.display = 'none';
                        return;
                    }

                    formObject.innerHTML = "";
                    if (!hasPosted && $('review-my-rating'))
                        $('review-my-rating').style.display = 'none';

                    $('thankYou').style.display = "none";

                    if (viewer_id != 0 && $('go_to_review'))
                        $('go_to_review').innerHTML = '<a href=' + responseJSON.review_href + '><?php echo $this->translate("Go to your Review"); ?></a>';
                    Smoothbox.open($('thankYou').innerHTML);
                    formElement.style.display = 'none';
                    if ($('background-image'))
                        $('background-image').style.display = 'none';
                }
            });
            request.send(currentValues);
        }
        return false;
    }

    function validateEmail(email) {
        if (email != '') {
            var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return filter.test(email);
        } else {
            return true;
        }
    }

    function showForm() {

        if ($('siteevent_create')) {
            $('siteevent_create').style.display = 'block';
            location.hash = 'siteevent_create';
            showRating($('siteevent_create'), $('overall_rating'), $('rating-box'));
        }
        if ($('siteevent_update')) {
            location.hash = 'siteevent_update';
            $('siteevent_update').style.display = 'block';
            if ($('siteevent_update').getElementById('body'))
                $('siteevent_update').getElementById('body').focus();
            showRating($('siteevent_update'), $('overall_my_rating'), $('rating-my-edit-box'));

        }
        return false;
    }

</script>