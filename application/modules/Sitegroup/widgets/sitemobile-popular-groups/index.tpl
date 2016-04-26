



<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if ($this->is_ajax_load): ?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>

<?php if (!$this->viewmore) : ?>
<?php if (count($this->layouts_views) > 1) :?>
  <div class="p_view_op ui-group-content p_l">
    <span <?php if($this->viewType == 'gridview'): ?> onclick='sm4.switchView.getViewTypeEntity("listview", <?php echo $this->identity; ?>, widgetUrl);' <?php endif;?> class="sm-widget-block"><i class="ui-icon ui-icon-th-list"></i></span>
    <span <?php if($this->viewType == 'listview'): ?> onclick='sm4.switchView.getViewTypeEntity("gridview", <?php echo $this->identity; ?>, widgetUrl);'  <?php endif;?> class="sm-widget-block"><i class="ui-icon ui-icon-th-large"></i></span>
  </div>
<?php endif; ?>
  <div id="main_layout" class="ui-group-content">
  <?php endif;?>
    <?php if ($this->viewType == 'listview'): ?> 
      <?php if (!$this->viewmore): ?>
        <div id="list_view" class="sm-content-list">                  
          <ul data-role="listview" data-inset="false" >
            <?php endif; ?>
            <?php  foreach ($this->sitegroups as $sitegroup): ?>
              <li data-icon="arrow-r">
                <a href="<?php echo $sitegroup->getHref(); ?>">
                <?php echo $this->itemPhoto($sitegroup, 'thumb.icon') ?>
                <h3><?php  echo $this->string()->chunk($this->string()->truncate($sitegroup->getTitle(), 45), 10); ?></h3>				
                <p>
                  <?php $contentArray = array(); ?>
                  <?php if (in_array('date', $this->contentDisplayArray)): ?>
                    <?php $contentArray[] = $this->timestamp(strtotime($sitegroup->creation_date)) ?> 
                  <?php endif; ?>

                  <?php if (in_array('owner', $this->contentDisplayArray)): ?>
                    <?php $contentArray[] = $this->translate('posted by') . '<b> ' . $sitegroup->getOwner()->getTitle() . '</b>'; ?>
                  <?php endif; ?>
                  <?php
                  if (!empty($contentArray)) {
                    echo join(" - ", $contentArray);
                  }
                  ?> 
                </p>
           
                <p>                        
                    <?php $contentArray = array(); ?>
                    <?php
                     if (in_array('memberCount', $this->contentDisplayArray) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
                      $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.title', 1);
                      if ($sitegroup->member_title && $memberTitle) {
                          $contentArray[] = $sitegroup->member_count . ' ' . $sitegroup->member_title;
                      } else {
                        $contentArray[] = $this->translate(array('%s member', '%s members', $sitegroup->member_count), $this->locale()->toNumber($sitegroup->member_count));
                      }
                    }
                    
                    if (in_array('likeCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s like', '%s likes', $sitegroup->like_count), $this->locale()->toNumber($sitegroup->like_count));
                    }
                    if (in_array('followCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s follower', '%s followers', $sitegroup->follow_count), $this->locale()->toNumber($sitegroup->follow_count));
                    }

                    if (in_array('reviewCount', $this->contentDisplayArray) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview') && !empty($this->ratngShow)) {
                      $contentArray[] = $this->translate(array('%s review', '%s reviews', $sitegroup->review_count), $this->locale()->toNumber($sitegroup->review_count));
                    }

                    if (in_array('commentCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s comment', '%s comments', $sitegroup->comment_count), $this->locale()->toNumber($sitegroup->comment_count));
                    }

                    if (in_array('viewCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s view', '%s views', $sitegroup->view_count), $this->locale()->toNumber($sitegroup->view_count));
                    }
                    ?>
                    <?php
                    if (!empty($contentArray)) {
                      echo join(" - ", $contentArray);
                    }
                    ?>  
                </p>
                
                <p> 
                  <?php if (in_array('price', $this->contentDisplayArray) && !empty($sitegroup->price) && $this->enablePrice): ?>            
                  <?php echo  $this->translate("Price: ") . $this->locale()->toCurrency($sitegroup->price, $currency); ?>
                  <?php endif; ?>
                </p>
                <p>
                  <?php if (in_array('location', $this->contentDisplayArray) && !empty($sitegroup->location) && $this->enableLocation): ?>
                    <?php
                    $locationId = Engine_Api::_()->getDbTable('locations', 'sitegroup')->getLocationId($sitegroup->group_id, $sitegroup->location);
                    echo $this->translate("Location: ") . $this->translate($sitegroup->location);
                    ?>     
                  <?php endif; ?>                 
                </p>

                <p>
                  <?php if (in_array('ratings', $this->contentDisplayArray) && $this->ratngShow): ?>
                    <?php if (($sitegroup->rating > 0)): ?>
                      <?php
                      $currentRatingValue = $sitegroup->rating;
                      $difference = $currentRatingValue - (int) $currentRatingValue;
                      if ($difference < .5) {
                        $finalRatingValue = (int) $currentRatingValue;
                      } else {
                        $finalRatingValue = (int) $currentRatingValue + .5;
                      }
                      ?>
                      <span class="list_rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                        <?php for ($x = 1; $x <= $sitegroup->rating; $x++): ?>
                          <span class="rating_star_generic rating_star" ></span>
                        <?php endfor; ?>
                      <?php if ((round($sitegroup->rating) - $sitegroup->rating) > 0): ?>
                          <span class="rating_star_generic rating_star_half" ></span>
                      <?php endif; ?>
                      </span>		
                    <?php endif; ?>
                  <?php endif; ?>


                  <?php if (in_array('closed', $this->contentDisplayArray) && $sitegroup->closed): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
                  <?php endif; ?> 

                  <?php if (0 &&  Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.fs.markers', 1)) : ?>
                    <?php if (in_array('sponsored', $this->contentDisplayArray) && ($sitegroup->sponsored == 1)): ?>
                      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                    <?php endif; ?>
                    <?php if (in_array('featured', $this->contentDisplayArray) && ($sitegroup->featured == 1)): ?>
          <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
        <?php endif; ?>
      <?php endif; ?>

                </p>
              </a>
              </li>
            <?php endforeach; ?>
            <?php if (!$this->viewmore): ?>
            </ul>
          </div>
          <?php endif; ?>
        <?php else: ?>
          <?php $isLarge = ($this->columnWidth > 170); ?>
          <?php if (!$this->viewmore): ?>
            <div id="grid_view">
              <ul class="p_list_grid"> 
              <?php endif; ?>             
              <?php foreach ($this->sitegroups as $sitegroup): ?>
                <li style="height:<?php echo $this->columnHeight ?>px;">
                  <a href="<?php echo $sitegroup->getHref(); ?>" class="ui-link-inherit">
                <div class="p_list_grid_top_sec">
                  <div class="p_list_grid_img">
                    <?php $url = $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/nophoto_group_thumb_profile.png';
                    $temp_url = $sitegroup->getPhotoUrl('thumb.profile');
                    if (!empty($temp_url)): $url = $sitegroup->getPhotoUrl('thumb.profile');
                      endif; ?>
                    <span style="background-image: url(<?php echo $url; ?>);"> </span>
                  </div>
                  <div class="p_list_grid_title">
                    <span><?php echo $this->string()->chunk($this->string()->truncate($sitegroup->getTitle(), 45), 10); ?></span>
                  </div>
                  <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
                    <div class="list-label-wrap">
                      <?php if (in_array('sponsored', $this->contentDisplayArray)&& ($sitegroup->sponsored == 1)): ?>
                        <span class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.color', '#fc0505'); ?>;'>
                          <?php echo $this->translate('SPONSORED'); ?>     				
                        </span>
                      <?php endif; ?>
                      <?php if (in_array('featured', $this->contentDisplayArray)  && ($sitegroup->featured == 1)): ?>
                        <span class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.featured.color', '#0cf523'); ?>;'><?php echo $this->translate('FEATURED')?></span>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>  
                </div>  
                </a>
                <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
                  <?php if (in_array('closed', $this->contentDisplayArray) && $sitegroup->closed): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>								
                  <?php endif;?> 
                  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.fs.markers', 1)) : ?>
                    <?php if (in_array('sponsored', $this->contentDisplayArray)&& ($sitegroup->sponsored == 1)): ?>
                      <div class="sm-sl" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.color', '#fc0505'); ?>;'>
                        <?php echo $this->translate('SPONSORED'); ?>     				
                      </div>
                    <?php endif; ?>
                    <?php if (in_array('featured', $this->contentDisplayArray)  && ($sitegroup->featured == 1)): ?>
                      <i title="<?php echo $this->translate('Featured')?>" class="sm-fl"></i>
                    <?php endif; ?>
                  <?php endif; ?>
                <?php endif; ?>
                
                <div class="p_list_grid_info">
                  
                  <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
                    <span class="p_list_grid_stats">
                    <span class="fleft">
                        <?php if (in_array('date', $this->contentDisplayArray)): ?>
                          <?php echo $this->timestamp(strtotime($sitegroup->creation_date)) ?> 
                        <?php endif; ?>
                      </span>
                      <span class="fright">
                        <?php if (in_array('owner', $this->contentDisplayArray)): ?>
                        <?php echo $this->translate('by ') . '<b>' .$this->htmlLink($sitegroup->getOwner()->getHref(), $this->string()->truncate($sitegroup->getOwner()->getTitle(), 16)) . '</b>'; ?>
                        <?php endif; ?>
                      </span>
                    </span>
                  <?php endif ?>
                  
                  <span class="p_list_grid_stats">
                      <?php if (in_array('ratings', $this->contentDisplayArray) && $this->ratngShow): ?>
                        <?php if (($sitegroup->rating > 0)): ?>
                            <?php
                            $currentRatingValue = $sitegroup->rating;
                            $difference = $currentRatingValue - (int) $currentRatingValue;
                            if ($difference < .5) {
                              $finalRatingValue = (int) $currentRatingValue;
                            } else {
                              $finalRatingValue = (int) $currentRatingValue + .5;
                            }
                            ?>
                          <span class="list_rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
          <?php for ($x = 1; $x <= $sitegroup->rating; $x++): ?>
                              <span class="rating_star_generic rating_star" ></span>
                          <?php endfor; ?>
                          <?php if ((round($sitegroup->rating) - $sitegroup->rating) > 0): ?>
                              <span class="rating_star_generic rating_star_half" ></span>
                          <?php endif; ?>
                          </span>		
                        <?php endif; ?>
                      <?php endif; ?>                   
                    </span>
                  <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
                   <span class="p_list_grid_stats">
                      <?php if (in_array('date', $this->contentDisplayArray)): ?>
                        <?php echo $this->timestamp(strtotime($sitegroup->creation_date)) ?> 
                      <?php endif; ?>
                    </span>
                    <span class="p_list_grid_stats">
                      <?php if (in_array('owner', $this->contentDisplayArray)): ?>
                      <?php echo $this->translate('posted by ') . '<b>' .$this->htmlLink($sitegroup->getOwner()->getHref(), $sitegroup->getOwner()->getTitle()) . '</b>'; ?>
                      <?php endif; ?>
                    </span>
                  <?php endif ?>
                   <span class="p_list_grid_stats">                                            
                    <?php $contentArray = array(); ?>
                    <?php
                     if (in_array('memberCount', $this->contentDisplayArray) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
                      $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.title', 1);
                      if ($sitegroup->member_title && $memberTitle) {
                          $contentArray[] = $sitegroup->member_count . ' ' . $sitegroup->member_title;
                      } else {
                        $contentArray[] = $this->translate(array('%s member', '%s members', $sitegroup->member_count), $this->locale()->toNumber($sitegroup->member_count));
                      }
                    }
                    
                    if (in_array('likeCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s like', '%s likes', $sitegroup->like_count), $this->locale()->toNumber($sitegroup->like_count));
                    }
                    if (in_array('followCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s follower', '%s followers', $sitegroup->follow_count), $this->locale()->toNumber($sitegroup->follow_count));
                    }

                    if (in_array('reviewCount', $this->contentDisplayArray) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview') && !empty($this->ratngShow)) {
                      $contentArray[] = $this->translate(array('%s review', '%s reviews', $sitegroup->review_count), $this->locale()->toNumber($sitegroup->review_count));
                    }

                    if (in_array('commentCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s comment', '%s comments', $sitegroup->comment_count), $this->locale()->toNumber($sitegroup->comment_count));
                    }

                    if (in_array('viewCount', $this->contentDisplayArray)) {
                      $contentArray[] = $this->translate(array('%s view', '%s views', $sitegroup->view_count), $this->locale()->toNumber($sitegroup->view_count));
                    }
                    ?>
                    <?php
                    if (!empty($contentArray)) {
                      echo join(" - ", $contentArray);
                    }
                    ?>  
                    </span>
                    <?php if (in_array('price', $this->contentDisplayArray) && !empty($sitegroup->price) && $this->enablePrice): ?>
                      <span class="p_list_grid_stats">
                        <?php echo $this->translate("Price: ") . $this->locale()->toCurrency($sitegroup->price, $currency); ?>
                      </span>
                    <?php endif; ?>
                    <?php if (in_array('location', $this->contentDisplayArray) && !empty($sitegroup->location) && $this->enableLocation): ?>
                      <span class="p_list_grid_stats">
                      <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
                        <i class="ui-icon-map-marker"></i>
                         <?php echo $this->htmlLink('https://maps.google.com/?q='.urlencode($sitegroup->location), $sitegroup->location, array('target' => 'blank')) ?>
                      <?php else: ?>
                      <?php
                        $locationId = Engine_Api::_()->getDbTable('locations', 'sitegroup')->getLocationId($sitegroup->group_id, $sitegroup->location);
                        echo $this->translate("Location: ") . $this->translate($sitegroup->location);
                      ?>  
                      <?php endif; ?>   
                      </span>
                    <?php endif; ?>    
                </div>   
              
                </li>
              <?php endforeach; ?>
              <?php if (!$this->viewmore): ?> 
              </ul>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      <?php if ($this->params['page'] < 2 && $this->totalCount > ($this->params['page'] * $this->params['limit'])) : ?>
        <div class="feed_viewmore clr" style="margin-bottom: 5px;">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
              'id' => 'feed_viewmore_link',
              'class' => 'ui-btn-default icon_viewmore',
              'onclick' => 'sm4.switchView.viewMoreEntity(' . $this->identity . ',widgetUrl)'
          ))
          ?>
        </div>
        <div class="seaocore_loading feeds_loading" style="display: none;">
          <i class="icon_loading"></i>
        </div>
      <?php endif; ?> 
      <script type="text/javascript">
                var widgetUrl = sm4.core.baseUrl + 'widget/index/mod/sitegroup/name/sitemobile-popular-groups';
                sm4.core.runonce.add(function() {
                  var currentpageid = $.mobile.activePage.attr('id') + '-' + <?php echo $this->identity; ?>;
                  sm4.switchView.pageInfo[currentpageid] = $.extend({}, sm4.switchView.pageInfo[currentpageid], {'viewType': '<?php echo $this->viewType; ?>', 'params': <?php echo json_encode($this->params) ?>, 'totalCount': <?php echo $this->totalCount; ?>});
                  
                });
      </script>


      <?php if (!$this->viewmore) : ?>
      </div>
      <style type="text/css">
        .ui-collapsible-content{padding-bottom:0;}
      </style>
    <?php endif; ?>
<?php else: ?>
  <div id="layout_sitegroup_sitemobile_popular_groups_<?php echo $this->identity; ?>">
  </div>
<script type="text/javascript">
  var requestParams = $.extend(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'});
  var params = {
      'detactLocation': <?php echo $this->detactLocation; ?>,
      'responseContainer': 'layout_sitegroup_sitemobile_popular_groups_<?php echo $this->identity; ?>',
      'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
      requestParams: requestParams
  };
  sm4.core.runonce.add(function() {
    setTimeout((function() {
      $.mobile.loading().loader("show");
    }), 100);

    sm4.core.locationBased.startReq(params);
  });
</script>
<?php endif; ?>