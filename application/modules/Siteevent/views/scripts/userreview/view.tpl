<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_rating.css')
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css')
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteeventprofile.css');
?>
<div id="profile_review" class="pabsolute"></div>
<div class="o_hidden">
  <div class="siteevent_view_top">
    <?php echo $this->htmlLink($this->user_subject->getHref(), $this->itemPhoto($this->user_subject, 'thumb.icon', $this->user_subject->getTitle()), array('class' => "thumb_icon", 'title' => $this->user_subject->getTitle())) ?>
    <h2>
      <?php echo $this->htmlLink($this->user_subject->getHref(), $this->user_subject->getTitle()) ?>
    </h2>
    <div class="siteevent_reviews_event_stat seaocore_txt_light">
      <?php echo $this->translate('in'); ?> <?php echo $this->htmlLink($this->siteevent->getHref(), $this->siteevent->getTitle()); ?>
    </div> 
    <div class="clr"></div>
  </div>

  <?php if ($this->averageUserReviews): ?>
    <div class="siteevent_profile_review b_medium siteevent_review_block">
      <div class="siteevent_profile_review_left">
        <div class="siteevent_profile_review_title">
          <?php echo $this->translate("Average User Rating"); ?>
        </div>
        <div class="siteevent_profile_review_stars">
          <span class="siteevent_profile_review_rating">
            <span class="fleft">
              <?php echo $this->ShowRatingStarSiteevent($this->averageUserReviews, 'user', 'big-star',null, false, false); ?>
            </span>
          </span>
        </div>
        <div class="siteevent_profile_review_stat clr">
          <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $this->totalReviews), $this->locale()->toNumber($this->totalReviews)); ?>
        </div>
        <?php if (!empty($this->viewer_id) && $this->can_rated): ?>
          <div class="siteevent_profile_review_title mtop5" id="review-my-rating">
            <?php echo $this->translate("My Rating"); ?>
          </div>	
          <div class="siteevent_profile_review_stars">
            <?php echo $this->ShowRatingStarSiteevent($this->myReviews, 'user', 'big-star',null, false, false); ?>		     
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
  <?php if (( $this->paginator->getTotalItemCount() > 0)): ?>
    <div class="siteevent_browse_lists_view_options b_medium">
      <div> 
        <?php echo $this->translate(array("%s review found.", "%s reviews found.", $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
      </div>
    </div>
    <ul class="siteevent_reviews_event" id="profile_siteevent_content">
      <?php
      foreach ($this->paginator as $review):
        $user_subject = Engine_Api::_()->user()->getUser($review->viewer_id);
        ?>
        <li>
          <div class="siteevent_reviews_event_photo">
            <?php echo $this->htmlLink($user_subject->getOwner()->getHref(), $this->itemPhoto($user_subject->getOwner(), 'thumb.icon', $user_subject->getOwner()->getTitle()), array('class' => "thumb_icon")) ?>
          </div>
          <div class="siteevent_reviews_event_info">
            <div class=" siteevent_reviews_event_title">
              <div class="siteevent_ur_show_rating_star">
                <?php $rating_value = $review->rating; ?>
                <span class="fright">
                  <span class="fleft">
                    <?php echo $this->ShowRatingStarSiteevent($rating_value, 'user', 'big-star',null, false, false); ?>
                  </span>
                </span>
              </div>
              <div class="siteevent_review_title"><?php echo $review->title ?></div>
            </div>

            <div class="siteevent_reviews_event_stat seaocore_txt_light">
              <?php echo $this->timestamp(strtotime($review->modified_date)); ?> - 
              <?php if (!empty($review->viewer_id)): ?>
                <?php echo $this->translate('by'); ?> <?php echo $this->htmlLink($user_subject->getOwner()->getHref(), $user_subject->getOwner()->getTitle()) ?>
              <?php endif; ?>
            </div> 
            <div class="clr"></div>

            <div class="siteevent_reviews_event_proscons">
              <!--<b><?php //echo $this->translate("Summary")   ?>: </b>-->
              <?php echo $this->viewMore($review->description, 1000, 5000); ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
      <?php if ($this->tab_id) : ?>
        <div class="fleft mbot10">
          <a href='<?php echo $this->url(array('event_id' => $this->event_id, 'slug' => $this->siteevent->getSlug(), 'tab' => $this->tab_id), "siteevent_entry_view", true) ?>' class="buttonlink siteevent_item_icon_back"><?php echo $this->translate('Back to Guests'); ?></a>
        </div>
      <?php endif; ?>
      <?php if ($this->can_rated && $this->rateuser && $this->viewer_id): ?>
        <?php echo $this->update_form->setAttrib('class', 'siteevent_review_form global_form')->render($this) ?>
        <?php include APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_formUpdateUserreview.tpl';
        ?>
      <?php endif; ?>
      <?php if ($this->paginator->count() > 1): ?>
        <div class="o_hidden mtop10">
          <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
            <div class="paginator_previous">
              <?php
              echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                  'onclick' => 'paginateSitepageReview(sitepageReviewPage - 1)',
                  'class' => 'buttonlink icon_previous'
              ));
              ?>
            </div>
          <?php endif; ?>
          <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
            <div class="paginator_next">
              <?php
              echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                  'onclick' => 'paginateSitepageReview(sitepageReviewPage + 1)',
                  'class' => 'buttonlink_right icon_next'
              ));
              ?>
            </div>
          <?php endif; ?>
        </div>	
      <?php endif; ?> 
    </ul>
  <?php else: ?>
    <?php if (!$this->can_create): ?>
      <div class="tip">
        <span>
          <?php echo $this->translate("No reviews have been written for this user yet."); ?>
        </span>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>



<div id="background-image" style="display:none;">
  <center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif" /></center>
</div>
<script type="text/javascript">

  function doDefaultRating(element_id, ratingparam_id, classstar) {
    $(element_id + '_' + ratingparam_id).getParent().getParent().className = 'siteevent_ug_rating ' + classstar;
    $('review_rate_' + ratingparam_id).value = $(element_id + '_' + ratingparam_id).getParent().id;
  }

</script>
<?php $rating_value_2 = 0; ?>
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


<?php if ($this->viewer_id && empty($this->can_rated) && ($this->user_id != $this->viewer_id)) : ?>
  <?php echo $this->form->render($this); ?>
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
<?php endif; ?>

<div id="thankYou" style="display:none;">
  <div class="siteevent_reply_thankyou_msg o_hidden">
    <?php if (empty($this->can_rated)): ?>
        <h4><i class="siteevent_icon_tick siteevent_icon mright5"></i><?php echo $this->translate("Thank You, %s!", $this->viewer->getTitle()); ?></h4>
      <?php echo $this->translate("Your Review on %s has been successfully submitted.", $this->user_subject->getTitle()); ?>
    <?php else: ?>
      <h4><i class="siteevent_icon_tick siteevent_icon mright5"></i><?php echo $this->translate("Thank You, %s!", $this->viewer->getTitle()); ?></h4>
      <?php echo $this->translate("Your Review on %s has been successfully updated.", $this->user_subject->getTitle()); ?>
    <?php endif; ?>

    <ul class="mtop10 siteevent_reply_thankyou_msg_links clr o_hidden">
			<?php if ($this->viewer_id): ?>
				<li id="go_to_review"></li>
			<?php endif; ?>
      <li>
        <a href='<?php echo $this->url(array('event_id' => $this->event_id), "siteevent_entry_view", true) ?>'><?php echo $this->translate("Go to the event %s", $this->siteevent->gettitle()); ?></a>
      </li>
    </ul>
    <div class="clr mtop10">
      <button onclick="closeThankYou();"><?php echo $this->translate('Close'); ?></button>
    </div>
  </div>
</div>


<script type="text/javascript">
  // var review_has_posted = 0;
  var review_has_posted = '<?php echo $this->can_rated; ?>';
  en4.core.runonce.add(function() {
    if (review_has_posted == 0) {
      showRating($('siteevent_userreview_create'), $('overall_rating'));
    } else {
      showRating($('siteevent_update'), $('overall_my_rating'));
    }
  });

  function showRating(formObject, overallratingid) {
    if (formObject && formObject.getElementById('title-wrapper')) {
      var divRatingId = overallratingid;
      divRatingId.inject(formObject.getElementById('title-wrapper'), 'before');
    } else if (formObject && formObject.getElementById('description-wrapper')) {
      var divRatingId = overallratingid;
      divRatingId.inject(formObject.getElementById('description-wrapper'), 'before');
    }

    if (overallratingid)
      overallratingid.style.display = "block";
  }

  var getImageDiv = $('background-image');
  if ($('submit-wrapper')) {
    getImageDiv.inject($('submit-wrapper'));
  }

  function closeThankYou() {
    Smoothbox.close();
    window.location.href = '<?php echo $this->url(array('action' => 'view', 'event_id' => $this->event_id, 'user_id' => $this->user_id, 'tab_id' => $this->tab_id), "siteevent_user_review", true) ?>';
  }

  function submitForm(review_id, formObject, type) {
    var event_id = '<?php echo $this->event_id ?>';
    var user_id = '<?php echo $this->user_id ?>';
    var flag = true;
    formElement = formObject;
    var focusEl = '';
    currentValues = formElement.toQueryString();
    if ($('overallrating_error'))
      $('overallrating_error').destroy();
    if ($('title_error'))
      $('title_error').destroy();
    if ($('title_length_error'))
      $('title_length_error').destroy();
    if ($('description_error'))
      $('description_error').destroy();
    if ($('description_length_error'))
      $('description_length_error').destroy();

    if (typeof formElement['review_rate_0'] != 'undefined' && formElement['review_rate_0'].value == 0) {
      liElement = new Element('span', {'html': '<?php echo $this->translate("* Please complete this field - it is required."); ?>', 'class': 'review_error', 'id': 'overallrating_error'}).inject($('overall_rating-element'));
      flag = false;
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

    if (formElement['description'] && formElement['description'].value == '') {
      liElement = new Element('span', {'html': '<?php echo $this->translate("* Please complete this field - it is required."); ?>', 'class': 'review_error', 'id': 'description_error'}).inject($('description-element'));
      flag = false;
      if (focusEl == '') {
        focusEl = 'description';
      }
    }
    else if (formElement['description'] && formElement['description'].value != '') {
      var str = formElement['description'].value;
      var length = str.replace(/\s+/g, '').length;
      if (length < 10) {
        var message = en4.core.language.translate('<?php echo $this->translate("* Please enter at least 10 characters (you entered %s characters).") ?>', length);
        liElement = new Element('span', {'html': message, 'class': 'review_error', 'id': 'description_length_error'}).inject($('description-element'));
        flag = false;
        if (focusEl == '') {
          focusEl = 'description';
        }
      }
    }

    if (flag == false) {
      if ($(focusEl))
        $(focusEl).focus();
      return false;
    }
    var url = '';
    var hasPosted = '<?php echo $this->hasPosted ?>';
    if (type == 'create') {
      url = '<?php echo $this->url(array('action' => 'create', 'event_id' => $this->event_id, 'user_id' => $this->user_id), "siteevent_user_review"); ?>';
    } else if (type == 'update') {
      url = '<?php echo $this->url(array('action' => 'update', 'event_id' => $this->event_id, 'user_id' => $this->user_id), "siteevent_user_review"); ?>';
    }
    
    viewURL = '<?php echo $this->url(array('action' => 'view', 'event_id' => $this->event_id, 'user_id' => $this->user_id), "siteevent_user_review"); ?>';
    
    if (flag == true) {
      //$('background-image').style.display = 'block';
      var formSubmitElement = formObject.getElementById('submit-wrapper').innerHTML;
      formObject.getElementById('submit-wrapper').innerHTML = $('background-image').innerHTML;

      var request = new Request.HTML({
        url: url,
        method: 'post',
        data: {
          format: 'html'
        },
        //responseTree, responseElements, responseHTML, responseJavaScript
        onSuccess: function(responseJSON) {
          var viewer_id = '<?php echo $this->viewer_id; ?>';

          formObject.innerHTML = "";
          if (!hasPosted && $('review-my-rating'))
            $('review-my-rating').style.display = 'none';

          $('thankYou').style.display = "none";

          if (viewer_id != 0 && $('go_to_review')) 
            $('go_to_review').innerHTML = '<a href=' + viewURL + '><?php echo $this->translate("Go to your Review"); ?></a>';
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

</script>