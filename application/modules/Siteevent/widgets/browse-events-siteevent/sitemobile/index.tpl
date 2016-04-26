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
<?php if (!$this->autoContentLoad) : ?>
  <?php if (!$this->viewmore) : ?>
    <?php if (count($this->layouts_views) > 1) : ?>
      <?php
// Parse query and remove page
      if (!empty($this->formValuesSM) && (is_array($this->formValuesSM))) :
        $query = $this->formValuesSM;
      endif;
      $query['view_selected'] = 'listview';
      $queryL = http_build_query($query);
      $queryL = '?' . $queryL;
      $query['view_selected'] = 'gridview';
      $queryG = http_build_query($query);
      $queryG = '?' . $queryG;
      ?>
      <div class="p_view_op ui-page-content">
        <?php
        echo $this->htmlLink(array(
         'reset' => false,
         'QUERY' => $queryL,
          ), '<span  class="sm-widget-block"><i class="ui-icon ui-icon-th-list"></i></span>', array(
         'class' => 'ui-link-inherit'
        ))
        ?>
        <?php
        echo $this->htmlLink(array(
         'reset' => false,
         'QUERY' => $queryG,
          ), '<span  class="sm-widget-block"><i class="ui-icon ui-icon-th-large"></i></span>', array(
         'class' => 'ui-link-inherit'
        ))
        ?>
      </div>
    <?php endif; ?>
    <div id="main_layout" class="ui-page-content">
    <?php endif; ?>
  <?php endif; ?>
    <?php
    $ratingValue = $this->ratingType;
    $ratingShow = 'small-star';
    if ($this->ratingType == 'rating_editor') {
      $ratingType = 'editor';
    } elseif ($this->ratingType == 'rating_avg') {
      $ratingType = 'overall';
    } else {
      $ratingType = 'user';
    }
    ?>
        <?php if(!empty($this->categoryName)):?>
         <?php echo $this->translate("Showing Events in category: ");?>
        <b><?php echo $this->translate($this->categoryName);?></b>     
        <br/><br/>
        <?php endif; ?>

    <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
    <?php if ($this->paginator->count() > 0): ?>
      <?php if ($this->view_selected == 'listview'): ?>
        <div id="list_view" class="sm-content-list">
          <ul data-role="listview" data-inset="false" <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>data-icon="angle-right"<?php else : ?>data-icon="arrow-r"<?php endif;?>>   
            <?php foreach ($this->paginator as $siteevent): ?>
            <li>
                <a href="<?php echo $siteevent->getHref(); ?>">
                  <?php echo $this->itemPhoto($siteevent, 'thumb.icon'); ?>
                  <h3><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncation); ?></h3>

                  <?php if (!empty($this->statistics)) : ?>
                    <p>
                      <?php echo $this->eventInfoSM($siteevent, $this->statistics, array('ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation)); ?>
                    </p>
                  <?php endif; ?>

                  <?php if (!empty($this->statistics) && in_array('price', $this->statistics) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0) && !empty($siteevent->price) && $siteevent->price > 0) : ?>
                    <p>
                      <?php $priceInfos = $siteevent->getPriceInfo(); ?>
                      <?php $priceInfoCount = Count($priceInfos); ?>
                    </p>
                  <?php endif; ?>  
                  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) && !empty($this->statistics) && in_array('ratingStar', $this->statistics)): ?>
                    <?php if (!empty($siteevent->rating_editor) && ($ratingValue == 'rating_both' || $ratingValue == 'rating_editor')): ?>
                      <p><?php echo $this->showRatingStarSiteeventSM($siteevent->rating_editor, 'editor', $ratingShow); ?></p>
                      <p><?php echo $this->showRatingStarSiteeventSM($siteevent->rating_users, 'user', $ratingShow); ?> </p>
                    <?php else: ?>
                      <p><?php echo $this->showRatingStarSiteeventSM($siteevent->$ratingValue, $ratingType, $ratingShow); ?> </p>
                    <?php endif; ?>
                  <?php endif; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>   
      <?php elseif ($this->view_selected == 'gridview'): ?>
        <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
          <?php if (!$this->autoContentLoad) : ?>
            <div class="listing">
              <ul id="browsesiteevents_ul"> 
              <?php endif; ?>  
              <?php foreach ($this->paginator as $siteevent): ?> 
                <!--TO SHOW TICKMARK FOR ATTENDING--> 
                <?php
                $viewer = Engine_Api::_()->user()->getViewer();
                $rsvp = null;
                if ($viewer->getIdentity()):
                  $row = $siteevent->membership()->getRow($viewer);
                  if (!empty($row)) {
                    $rsvp = $row->rsvp;
                  }
                endif;
                ?>
                <!--END TICKMARK-->
                <li style="height:<?php echo $this->columnHeight ?>px;">
                  <a class="list-photo" href="<?php echo $siteevent->getHref(); ?>">
                    <?php
                    $url = $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_listing_thumb_normal.png';
                    $temp_url = $siteevent->getPhotoUrl('thumb.profile');
                    if (!empty($temp_url)): $url = $siteevent->getPhotoUrl('thumb.profile');
                    endif;
                    ?>
                    <span style="background-image: url(<?php echo $url; ?>);"> </span>
                    <h3 id="tick_<?php echo $siteevent->getIdentity() ?>" class="list-title<?php if ($rsvp == 2): ?> tickmark ui-icon-ok<?php endif; ?>">
                      <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncationGrid) ?>
                    </h3>
                  </a>
                  <div class="list-info">
                    <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) && !empty($this->statistics) && in_array('ratingStar', $this->statistics)): ?>
                      <?php if ($ratingValue == 'rating_both'): ?>          
                        <span class="list-stats f_small"><?php echo $this->showRatingStarSiteeventSM($siteevent->rating_editor, 'editor', $ratingShow); ?></span>
                        <span class="list-stats f_small"><?php echo $this->showRatingStarSiteeventSM($siteevent->rating_users, 'user', $ratingShow); ?></span>
                      <?php else: ?>
                        <span class="list-stats f_small"><?php echo $this->showRatingStarSiteeventSM($siteevent->$ratingValue, $ratingType, $ratingShow); ?></span>
                      <?php endif; ?>
                    <?php endif; ?>                                 
                      <?php if (!empty($this->statistics)) : ?>
                        <?php echo $this->eventInfoSMApp($siteevent, $this->statistics, array('view_type' => 'grid_view', 'titlePosition' => $this->titlePosition, 'ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation)); ?>

                      <?php endif; ?>
                    <a href="#event_popup_<?php echo $siteevent->getIdentity() ?>" data-rel="popup" data-transition="pop" class="righticon ui-icon-ellipsis-vertical"></a>
                  </div> 
                  <?php $occure_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null; ?>
                  <?php if ($viewer->getIdentity()): ?>
                    <div data-role="popup" id="event_popup_<?php echo $siteevent->getIdentity() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
                      <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
                        <?php if ($this->viewer() && !$siteevent->membership()->isMember($this->viewer(), null)): ?>
                          <?php
                          echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'member', 'action' => 'join', 'event_id' => $siteevent->getIdentity(), 'occurrence_id' => $occure_id), $this->translate('Join Event'), array(
                           'class' => 'ui-btn-default ui-btn-action smoothbox'
                          ))
                          ?>
                        <?php else: ?>
                          <?php if ($this->viewer() && $siteevent->membership()->isMember($this->viewer()) && !$siteevent->isOwner($this->viewer())): ?>
                            <?php
                            echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'member', 'action' => 'leave', 'event_id' => $siteevent->getIdentity(), 'occurrence_id' => $occure_id), $this->translate('Leave Event'), array(
                             'class' => 'ui-btn-default ui-btn-danger smoothbox'
                            ))
                            ?> 
                        <?php endif; ?>  
                        <div class="ui-btn-default dblock chnage-rsvp">
                          <?php echo $this->translate('Change RSVP')?>
                          <form class="event_rsvp_form" action="<?php echo $this->url() ?>" method="post"  onsubmit="return false;">                        <fieldset data-role="controlgroup" data-mini="true" class="events_rsvp" id="rsvp_options_<?php echo $siteevent->getIdentity(); ?>" data-eventurl='<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'widget', 'action' => 'profile-rsvp', 'subject' => $siteevent->getGuid()), 'default', true); ?>' data-eventid="<?php echo $siteevent->getIdentity() ?>" >
                              <input type="radio" name="rsvp_options" id="rsvp_option_2" value="2" <?php if ($rsvp == 2): ?> checked="true" <?php endif; ?> />
                              <label for="rsvp_option_2"><?php echo $this->translate('Attending'); ?></label>	
                              <input type="radio"  class="rsvp_options" name="rsvp_options" id="rsvp_option_1" value="1" <?php if ($rsvp == 1): ?> checked="true" <?php endif; ?> />
                              <label for="rsvp_option_1"><?php echo $this->translate('Maybe Attending'); ?></label>
                              <input type="radio"  class="rsvp_options" name="rsvp_options" id="rsvp_option_0" value="0" <?php if ($rsvp == 0): ?> checked="true" <?php endif; ?> />
                              <label for="rsvp_option_0"><?php echo $this->translate('Not Attending'); ?></label>	
                            </fieldset>
                          </form>
                        </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endif; ?> 
                </li>
              <?php endforeach; ?>
              <?php if (!$this->autoContentLoad) : ?>
              </ul>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <div>
            <ul class="p_list_grid"> 
              <?php //endif;  ?>    
              <?php $isLarge = ($this->columnWidth > 170); ?>
              <?php foreach ($this->paginator as $siteevent): ?>          
                <li style="height:<?php echo $this->columnHeight ?>px;">
                  <a href="<?php echo $siteevent->getHref(); ?>" class="ui-link-inherit">
                    <div class="p_list_grid_top_sec">
                      <div class="p_list_grid_img">
                        <?php
                        $url = $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/images/nophoto_listing_thumb_normal.png';
                        $temp_url = $siteevent->getPhotoUrl($isLarge ? 'thumb.midum' : 'thumb.normal');
                        if (!empty($temp_url)): $url = $siteevent->getPhotoUrl('thumb.profile');
                        endif;
                        ?>
                        <span style="background-image: url(<?php echo $url; ?>);"> </span>
                      </div>                 
                      <div class="p_list_grid_title">
                        <span><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncationGrid) ?></span>
                      </div>
                    </div>
                    <div class="p_list_grid_info">
                      <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) && !empty($this->statistics) && in_array('ratingStar', $this->statistics)): ?>
                        <?php if ($ratingValue == 'rating_both'): ?>
                          <span class="p_list_grid_stats"><?php echo $this->showRatingStarSiteeventSM($siteevent->rating_editor, 'editor', $ratingShow); ?></span>
                          <span class="p_list_grid_stats"><?php echo $this->showRatingStarSiteeventSM($siteevent->rating_users, 'user', $ratingShow); ?></span>
                        <?php else: ?>
                          <span class="p_list_grid_stats"><?php echo $this->showRatingStarSiteeventSM($siteevent->$ratingValue, $ratingType, $ratingShow); ?></span>
                        <?php endif; ?>
                      <?php endif; ?>
                      <span class="p_list_grid_stats">                                  
                        <?php if (!empty($this->statistics)) : ?>
                          <?php echo $this->eventInfoSM($siteevent, $this->statistics, array('view_type' => 'grid_view', 'titlePosition' => $this->titlePosition, 'ratingShow' => $ratingShow, 'ratingValue' => $ratingValue, 'ratingType' => $ratingType, 'truncationLocation' => $this->truncationLocation)); ?>
                        <?php endif; ?>
                      </span>
                    </div> 
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    <?php else: ?>
      <div class="tip mtop10"> 
        <span> 
          <?php echo $this->translate('No events have been created yet.'); ?>
        </span>
      </div>
    <?php endif; ?>
    <?php if ($this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): ?>
      <?php
      echo $this->paginationControl($this->paginator, null, null, array(
       'query' => $this->formValuesSM, 'pageAsQuery' => true
      ));
      ?>
    <?php endif; ?>
    <?php if (!$this->autoContentLoad) : ?>
    <?php if (!$this->viewmore) : ?>
    </div>
  <?php endif; ?>
<?php endif; ?>
  <?php else:?>
    <div id="layout_siteevent_browse_events_<?php echo $this->identity; ?>">
    </div>  
    <script type="text/javascript">
    var requestParams = $.extend(<?php echo json_encode($this->paramsLocation);?>, {'content_id': '<?php echo $this->identity;?>'})
    var params = {
      'detactLocation': <?php echo $this->detactLocation; ?>,
      'responseContainer' : 'layout_siteevent_browse_events_<?php echo $this->identity;?>',
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
  

<script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) { ?>
    sm4.core.runonce.add(function() {
      var ul_id = 'browsesiteevents_ul';
      //call function to change rsvp - bind using click
      sm4app.core.Module.event.changeRsvpApp(ul_id);

      //Autoscrolling 
      var browseEventWidgetUrl = sm4.core.baseUrl + 'widget/index/mod/siteevent/name/browse-events-siteevent';
      var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
      sm4.core.Module.core.activeParams[activepage_id] = {'currentPage': '<?php echo sprintf('%d', $this->page) ?>', 'totalPages': '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues': <?php echo json_encode($this->formValuesSM); ?>, 'contentUrl': browseEventWidgetUrl, 'activeRequest': false, 'container': 'browsesiteevents_ul'};
    });
<?php } ?>
</script>