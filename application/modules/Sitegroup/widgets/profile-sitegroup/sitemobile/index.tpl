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

<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1);?>
<?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
  <div id='profile_groups'>
    <ul class="p_list_grid">
      <?php foreach ($this->paginator as $item): ?>
        <li style="height:<?php echo $this->columnHeight ?>px;">
          <a href="<?php echo $item->getHref(); ?>" class="ui-link-inherit">
            <div class="p_list_grid_top_sec">
              <div class="p_list_grid_img">
                <?php $url = $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/nophoto_group_thumb_profile.png';
                $temp_url = $item->getPhotoUrl('thumb.profile');
                if (!empty($temp_url)): $url = $item->getPhotoUrl('thumb.profile');
                  endif; ?>
                <span style="background-image: url(<?php echo $url; ?>);"> </span>
              </div>
              <div class="p_list_grid_title">
                <span><?php echo $this->string()->chunk($this->string()->truncate($item->getTitle(), 45), 10); ?></span>
              </div>
              <div class="list-label-wrap">
                <?php if ($item->sponsored == 1): ?>
                  <span class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.color', '#fc0505'); ?>;'>
                    <?php echo $this->translate('Sponsored'); ?>     				
                  </span>
                <?php endif; ?>
                <?php if ($item->featured == 1): ?>
                  <span class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.featured.color', '#0cf523'); ?>;'><?php echo $this->translate('Featured')?></span>
                <?php endif; ?>
              </div>
            </div>
          </a>
          <div class="p_list_grid_info">
            <span class="p_list_grid_stats">
              <span class="fleft">            
                <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              </span>
              <?php if($postedBy):?>
                <span class="fright">
                    <?php echo $this->translate('by ') . '<b>' .$this->htmlLink($item->getOwner()->getHref(), $this->string()->chunk($this->string()->truncate($item->getOwner()->getTitle(), 16), 10)) . '</b>'; ?>
                </span>
              <?php endif; ?>
            </span>
            <?php if ($this->ratngShow): ?>
              <span class="p_list_grid_stats">
                <?php if (($item->rating > 0)): ?>
                  <?php
                  $currentRatingValue = $item->rating;
                  $difference = $currentRatingValue - (int) $currentRatingValue;
                  if ($difference < .5) {
                    $finalRatingValue = (int) $currentRatingValue;
                  } else {
                    $finalRatingValue = (int) $currentRatingValue + .5;
                  }
                  ?>
                  <span title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                    <?php for ($x = 1; $x <= $item->rating; $x++): ?>
                      <span class="rating_star_generic rating_star" ></span>
                    <?php endfor; ?>
                    <?php if ((round($item->rating) - $item->rating) > 0): ?>
                      <span class="rating_star_generic rating_star_half" ></span>
                    <?php endif; ?>
                  </span>
                <?php endif; ?>
              </span>
            <?php endif; ?>

            <span class="p_list_grid_stats">
              <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?> - 
              <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) : ?>
                <?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1);
                if ($item->member_title && $memberTitle) : ?>

                      <?php echo $item->member_count . ' ' . $item->member_title; ?> -

                <?php else : ?>
                    <?php echo $this->translate(array('%s member', '%s members', $item->member_count), $this->locale()->toNumber($item->member_count)) ?> -
                <?php endif; ?>
              <?php endif; ?>
              <?php $sitegroupreviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview'); ?>
              <?php if ($sitegroupreviewEnabled): ?>
                <?php echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?> - 
              <?php endif; ?>
              <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?> - 
              <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
            </span>
            <?php if (!empty($item->group_owner_id)) : ?>
              <span class="p_list_grid_stats">
                <?php if ($item->group_owner_id == $item->owner_id) : ?>
                  <?php echo $this->translate("GROUPMEMBER_OWNER"); ?>
                <?php  else: ?>
                  <?php echo $this->translate("GROUPMEMBER_MEMBER"); ?>
                <?php endif; ?>
              </span>
            <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php else :?>
  <div class="sm-content-list" id="profile_groups">
    <ul  data-role="listview" data-icon="arrow-r">
      <?php foreach ($this->paginator as $item): ?>
        <li>
          <a href="<?php echo $item->getHref(); ?>">
            <?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
            <h3><?php echo $item->getTitle() ?></h3>
            <p>
              <?php if ($this->ratngShow): ?>
                <?php if (($item->rating > 0)): ?>
                  <?php
                  $currentRatingValue = $item->rating;
                  $difference = $currentRatingValue - (int) $currentRatingValue;
                  if ($difference < .5) {
                    $finalRatingValue = (int) $currentRatingValue;
                  } else {
                    $finalRatingValue = (int) $currentRatingValue + .5;
                  }
                  ?>
                  <span title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                    <?php for ($x = 1; $x <= $item->rating; $x++): ?>
                      <span class="rating_star_generic rating_star" ></span>
                    <?php endfor; ?>
                    <?php if ((round($item->rating) - $item->rating) > 0): ?>
                      <span class="rating_star_generic rating_star_half" ></span>
                    <?php endif; ?>
                  </span>
                <?php endif; ?>
              <?php endif; ?>
            </p>
            <p>
              <?php if($postedBy):?><?php echo $this->translate('posted by'); ?>
                  <strong><?php echo $item->getOwner()->getTitle() ?></strong> - 
              <?php endif; ?>
            <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
            </p>
            <p class="ui-li-aside">
              <?php if ($item->closed): ?>
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
              <?php endif; ?>
              <?php if ($item->sponsored == 1): ?>
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
              <?php endif; ?>
              <?php if ($item->featured == 1): ?>
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
              <?php endif; ?>
            </p>
            <p>
              <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?> - 
              <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) : ?>
                <?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1);
                if ($item->member_title && $memberTitle) : ?>

                      <?php echo $item->member_count . ' ' . $item->member_title; ?> -

                <?php else : ?>
                    <?php echo $this->translate(array('%s member', '%s members', $item->member_count), $this->locale()->toNumber($item->member_count)) ?> -
                <?php endif; ?>
              <?php endif; ?>
              <?php $sitegroupreviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview'); ?>
              <?php if ($sitegroupreviewEnabled): ?>
                <?php echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?> - 
              <?php endif; ?>
              <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?> - 
              <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
            </p>
            <p>
              <?php if (!empty($item->group_owner_id)) : ?>
                <?php if ($item->group_owner_id == $item->owner_id) : ?>
                  <i class="icon_sitegroups_group-owner"><?php echo $this->translate("GROUPMEMBER_OWNER"); ?></i>
                <?php  else: ?>
                  <i class="icon_sitegroup_member"><?php echo $this->translate("GROUPMEMBER_MEMBER"); ?></i>
                <?php endif; ?>
              <?php endif; ?>
            </p>
          </a> 
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif;?>
<?php if ($this->paginator->count() > 1): ?>
  <?php
  echo $this->paginationAjaxControl(
          $this->paginator, $this->identity, 'profile_groups', array('groupAdmin' => $this->groupAdmin, "category_id" => $this->category_id));
  ?>
<?php endif; ?>