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
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
$this->headScript()->appendFile($this->layout()->staticBaseUrl.'application/modules/Sitegroup/externals/scripts/core.js');
?>

<?php if($this->loaded_by_ajax):?>
  <script type="text/javascript">
    var params = {
      requestParams :<?php echo json_encode($this->params) ?>,
      responseContainer :$$('.layout_sitegroup_profile_sitegroup')
    }
    en4.sitegroup.ajaxTab.attachEvent('<?php echo $this->identity ?>',params);
  </script>
<?php endif;?>
  
<?php if($this->showContent): ?>  
    <?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1);?>
    <script type="text/javascript">
      en4.core.runonce.add(function(){

        <?php if( !$this->renderOne ): ?>
          var anchor = $('profile_sitegroups_<?php echo $this->identity?>').getParent();
          $('profile_sitegroup_previous_<?php echo $this->identity?>').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
          $('profile_sitegroup_next_<?php echo $this->identity?>').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

          $('profile_sitegroup_previous_<?php echo $this->identity?>').removeEvents('click').addEvent('click', function(){
            en4.core.request.send(new Request.HTML({
              url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
              data : {
                format : 'html',     
                subject : en4.core.subject.guid,
                group : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>,
                isajax: 1
              }
            }), {
              'element' : anchor
            })
          });

          $('profile_sitegroup_next_<?php echo $this->identity?>').removeEvents('click').addEvent('click', function(){
            en4.core.request.send(new Request.HTML({
              url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
              data : {
                format : 'html',
                subject : en4.core.subject.guid,
                group : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>,
                isajax: 1
              }
            }), {
              'element' : anchor
            })
          });
        <?php endif; ?>
      });
    </script>
    <ul id="profile_sitegroups_<?php echo $this->identity;?>"  class="sitegroups_profile_tab">
      <?php foreach ($this->paginator as $item): ?>
          <li <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.fs.markers', 1)):?><?php if($item->featured):?>class="lists_highlight"<?php endif;?><?php endif;?>>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.fs.markers', 1)):?>
              <?php if($item->featured):?>
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/featured-label.png', '',  array('title' => 'Featured','class' => 'sitegroup_featured_label')) ?>
            <?php endif;?>
          <?php endif;?>
          <div class='sitegroups_profile_tab_photo'>
            <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($item->group_id, $item->owner_id, $item->getSlug()), $this->itemPhoto($item, 'thumb.normal', '', array('align' => 'left'))) ?>
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.fs.markers', 1)):?>
              <?php if (!empty($item->sponsored)): ?>
                <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.image', 1);
                if (!empty($sponsored)) { ?>
                  <div class="sitegroup_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.color', '#fc0505'); ?>;'>
                    <?php echo $this->translate('SPONSORED'); ?>                 
                  </div>
                <?php } ?>
              <?php endif; ?>
            <?php endif; ?>
          </div>
          <div class='sitegroups_profile_tab_info'>
            <div class='sitegroups_profile_tab_title'>
              <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($item->group_id, $item->owner_id, $item->getSlug()), $item->getTitle()) ?>
              <div class="fright">
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

                <?php if ($item->closed): ?>
                  <span>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
                  </span>
                <?php endif; ?>
                <span>
                    <?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.fs.markers', 1)) :?>
                      <?php if ($item->sponsored == 1): ?>
                        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                      <?php endif; ?>
                      <?php if ($item->featured == 1): ?>
                        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                      <?php endif; ?>
                    <?php endif; ?>
                </span>
              </div>
              <div class="clr"></div>
            </div>
            <div class='sitegroups_browse_info_date  seaocore_txt_light'>
                <?php echo $this->timestamp(strtotime($item->creation_date)) ?> 
                <?php if($postedBy):?>
                  - <?php echo $this->translate('created by'); ?>
                  <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>,
              <?php endif; ?>
              <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>,

              <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) : ?>
                <?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1);
                if ($item->member_title && $memberTitle) : ?>

                      <?php echo $item->member_count . ' ' . $item->member_title; ?>,

                <?php else : ?>
                    <?php echo $this->translate(array('%s member', '%s members', $item->member_count), $this->locale()->toNumber($item->member_count)) ?>,
                <?php endif; ?>
              <?php endif; ?>

              <?php $sitegroupreviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview'); ?>
              <?php if ($sitegroupreviewEnabled): ?>
                <?php echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?>,
              <?php endif; ?>

              <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>,
              <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
            </div>

            <?php if (!empty($item->group_owner_id)) : ?>
              <div class='sitegroups_browse_info_date sitegroups_profile_info_member'>
                <?php if ($item->group_owner_id == $item->owner_id) : ?>
                <i class="icon_sitegroups_group-owner"><?php echo $this->translate("GROUPMEMBER_OWNER"); ?></i>
                <?php else: ?>
                <i class="icon_sitegroup_member"><?php echo $this->translate("GROUPMEMBER_MEMBER"); ?></i>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <div class='sitegroups_browse_info_blurb clr'>
              <?php
              // Not mbstring compat
              echo substr(strip_tags($item->body), 0, 350);
              if (strlen($item->body) > 349)
                echo $this->translate("...");
              ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <div>
      <div id="profile_sitegroup_previous_<?php echo $this->identity?>" class="paginator_previous">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
          'onclick' => '',
          'class' => 'buttonlink icon_previous'
        )); ?>
      </div>
      <div id="profile_sitegroup_next_<?php echo $this->identity?>" class="paginator_next">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
          'onclick' => '',
          'class' => 'buttonlink_right icon_next'
        )); ?>
      </div>
    </div>
<?php endif; ?>