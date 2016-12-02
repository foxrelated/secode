<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/styles/sitegroup-tooltip.css');
?> 
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1);?>
<ul class="sitegroup_sidebar_list jq-sitegroup_tooltip">
  <?php foreach ($this->suggestedsitegroup as $sitegroup): ?>
    <li>
      <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()), $this->itemPhoto($sitegroup, 'thumb.icon')) ?>

      <div class="suggestsitegroup_tooltip" style="display:none;">
        <div class="suggestsitegroup_tooltip_content_outer">
          <div class="suggestsitegroup_tooltip_content_inner">
            <div class="suggestsitegroup_tooltip_arrow">
              <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/tooltip_arrow.png' alt="" />
            </div>
            <div class='suggestsitegroups_tooltip_info'>
              <div class="title">
                <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()), $sitegroup->getTitle()) ?>
                <span>
                  <?php if ($sitegroup->featured == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                  <?php endif; ?>
                </span>
                <span>
                  <?php if ($sitegroup->sponsored == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                  <?php endif; ?>
                </span>
              </div>
              <?php if ($this->sitereviewEnabled && $sitegroup->rating > 0): ?>
                  <?php
                  $currentRatingValue = $sitegroup->rating;
                  $difference = $currentRatingValue - (int) $currentRatingValue;
                  if ($difference < .5) {
                    $finalRatingValue = (int) $currentRatingValue;
                  } else {
                    $finalRatingValue = (int) $currentRatingValue + .5;
                  }
                  ?>

                  <span class="clr" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                    <?php for ($x = 1; $x <= $sitegroup->rating; $x++): ?>
                      <span class="rating_star_generic rating_star" ></span>
                    <?php endfor; ?>
                    <?php if ((round($sitegroup->rating) - $sitegroup->rating) > 0): ?>
                      <span class="rating_star_generic rating_star_half" ></span>
                    <?php endif; ?>
                  </span>
              <?php endif; ?>
              
							<div class='suggestsitegroups_tooltip_info_date clr'>
								<?php echo $this->timestamp(strtotime($sitegroup->creation_date)) ?> 
								<?php if($postedBy):?>
									- <?php echo $this->translate('created by'); ?>
									<?php echo $this->htmlLink($sitegroup->getOwner()->getHref(), $sitegroup->getOwner()->getTitle()) ?>
								<?php endif;?>
							</div>
              <div class='suggestsitegroups_tooltip_info_date'>
                <?php echo $this->translate(array('%s comment', '%s comments', $sitegroup->comment_count), $this->locale()->toNumber($sitegroup->comment_count)) ?>, 

								<?php if ($this->sitereviewEnabled): ?>
									<?php echo $this->translate(array('%s review', '%s reviews', $sitegroup->review_count), $this->locale()->toNumber($sitegroup->review_count)) ?>,
								<?php endif; ?>

                <?php echo $this->translate(array('%s view', '%s views', $sitegroup->view_count), $this->locale()->toNumber($sitegroup->view_count)) ?>, 
                <?php echo $this->translate(array('%s like', '%s likes', $sitegroup->like_count), $this->locale()->toNumber($sitegroup->like_count)) ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class='sitegroup_sidebar_list_info'>
        <div class='sitegroup_sidebar_list_title'>
          <?php $sitegroup_title = strip_tags($sitegroup->title);
          $sitegroup_title = Engine_String::strlen($sitegroup_title) > 40 ? Engine_String::substr($sitegroup_title, 0, 40) . '..' : $sitegroup_title; ?>   
          <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()), $sitegroup_title) ?>
        </div>
      </div>

    </li>
  <?php endforeach; ?>
</ul>

<script type="text/javascript">
  /* moo style */
  window.addEvent('domready',function() {
    //opacity / display fix
    $$('.suggestsitegroup_tooltip').setStyles({
      opacity: 0,
      display: 'block'
    });
    //put the effect in place
    $$('.jq-sitegroup_tooltip li').each(function(el,i) {
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