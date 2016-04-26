<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var rsvp = '<?php echo $this->rsvp; ?>';
  var viewType = '<?php echo $this->viewType; ?>';
</script>
<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>
<?php
$siteevent_approved = true;
?>
<?php if (!$this->pagination && !$this->autoContentLoad): ?>
  <div class='siteevent_manage_event' id="siteevent_manage_event">
    <div class="clr o_hidden t_l">            
      <div class="fright">
        <a href="javascript:void(0);" <?php if ($this->viewType == 'past'):?> onclick="rsvp = -1;
          url = sm4.core.baseUrl+'widget/index/content_id/<?php echo sprintf('%d', $this->identity) ?>';
          viewType = 'upcoming';
            filter_rsvp(url,'siteevent_manage_event');" <?php endif;?> <?php if ($this->viewType == 'upcoming') echo 'class="bold"'; ?>><?php echo $this->translate('Upcoming'); ?></a>

        |

        <a href="javascript:void(0);" <?php if ($this->viewType == 'upcoming'):?> onclick="rsvp = -1;
        url = sm4.core.baseUrl+'widget/index/content_id/<?php echo sprintf('%d', $this->identity) ?>';
            viewType = 'past';
            filter_rsvp(url,'siteevent_manage_event');" <?php endif;?> <?php if ($this->viewType == 'past') echo 'class="bold"'; ?>><?php echo $this->translate('Past'); ?></a>

      </div>
    </div>        
  <?php endif; ?>  
  <?php if ($this->paginator->count() > 0): ?>
    <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
      <?php if (!$this->autoContentLoad) : ?>
        <div class="listing">
          <ul id="managesiteevents_ul"> 
          <?php endif; ?>  
          <?php $isLarge = ($this->columnWidth > 170); ?>
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
                $temp_url = $siteevent->getPhotoUrl($isLarge ? 'thumb.midum' : 'thumb.normal');
                if (!empty($temp_url)): $url = $siteevent->getPhotoUrl('thumb.profile');
                endif;
                ?>
                <span style="background-image: url(<?php echo $url; ?>);"> </span>
                <h3 id="tick_<?php echo $siteevent->getIdentity() ?>" class="list-title<?php if ($rsvp == 2): ?> tickmark ui-icon-ok<?php endif; ?>">
                  <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncation) ?>
                </h3>
              </a>
              <div class="list-info">
                                
                <?php
                $startDateObject = new Zend_Date(strtotime($siteevent->starttime));
                if ($this->viewer() && $this->viewer()->getIdentity()) {
                  $tz = $this->viewer()->timezone;
                  $startDateObject->setTimezone($tz);
                }
                ?>
                <span class="datemonth">
                  <span class="month"><?php echo $this->locale()->toDateTime($siteevent->starttime, array('format' => 'MMM')); ?></span>
                  <span class="date"><?php echo $this->locale()->toDateTime($siteevent->starttime, array('format' => 'dd')); ?></span>
                </span>
                <span class="list-stats f_small">
                  <?php echo $this->locale()->toTime($siteevent->starttime) ?> 
                </span>
                <span class="list-stats f_small">
                  <?php if (!empty($siteevent->location)): ?>
                    <?php echo $this->translate("at ").$siteevent->location; ?>
                  <?php endif; ?>
                </span>

                <span class="list-stats f_small">
                  <?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>  
                    <?php echo $this->translate(array('%s guest', '%s guests', $siteevent->member_count), $this->locale()->toNumber($siteevent->member_count)) ?>
                  <?php endif; ?>  
                  <?php
                  //CHECK IF THE VIEWER IS LEADER
                  $list = $siteevent->getLeaderList();
                  $leaderRow = $list->get($this->viewer());
                  $hostText = '';
                  if ($this->viewer()->getIdentity() == $siteevent->owner_id)
                    $hostText = $this->viewType == 'upcoming' ? "You are owner." : "You were owner.";
                  if ($leaderRow != null && empty($hostText))
                    $hostText = $this->viewType == 'upcoming' ? "You are leader." : "You were leader.";
                  if (($this->viewer()->getIdentity() == (int) $siteevent->host_id) && $siteevent->host_type == 'user' && empty($hostText))
                    $hostText = $this->viewType == 'upcoming' ? "You are host." : "You were host.";
                  if (!Engine_Api::_()->siteevent()->isTicketBasedEvent() && empty($hostText) && isset($siteevent->rsvp) && $siteevent->membership_userid == $this->viewer()->getIdentity())
                    $hostText = $this->viewType == 'upcoming' ? ($siteevent->rsvp == 3 ? "You are invited." : "You are guest.") : ($siteevent->rsvp == 3 ? "You were invited." : "You were guest.");
                  if (empty($hostText))
                    $hostText = 'You like this.';
                  ?>
                  <?php if (!empty($hostText)) : ?>
                    <?php echo $this->translate("| %s",$hostText); ?>
                  <?php endif; ?>
                </span>
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
      <div id="manage_events" class="sm-content-list"> 
        <ul data-role="listview" data-inset="false" id="siteevent_browse_list">
          <?php $prev_date = 0; ?>
          <?php foreach ($this->paginator as $siteevent): ?>
            <li data-icon="arrow-r">
              <a href="<?php echo $siteevent->getHref(); ?>">
                <?php echo $this->itemPhoto($siteevent, 'thumb.icon'); ?>
                <p class="fright" title="<?php echo $this->translate('Start Time') ?>"> 
                  <b><?php echo $this->locale()->toEventTime($siteevent->starttime, array('size' => $datetimeFormat)); ?></b>
                </p>
                <h3><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($siteevent->getTitle(), $this->title_truncation); ?></h3>

                <p>
                  <?php
                  $startDateObject = new Zend_Date(strtotime($siteevent->starttime));
                  if ($this->viewer() && $this->viewer()->getIdentity()) {
                    $tz = $this->viewer()->timezone;
                    $startDateObject->setTimezone($tz);
                  }
                  ?>
                  <?php
                  if ($datetimeFormat != 'full')
                    echo $this->locale()->toDate($siteevent->starttime, array('format' => 'EEEE')) . ', ' . $this->locale()->toDate($siteevent->starttime, array('size' => $datetimeFormat));
                  else
                    echo $this->locale()->toDate($siteevent->starttime, array('size' => $datetimeFormat));
                  ?>
                </p>
      <!--                        <p>
                <?php echo $this->translate('led by'); ?>
                <?php echo strip_tags($siteevent->getLedBys()); ?>,
                <?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>  
                    <?php echo $this->translate(array('%s guest', '%s guests', $siteevent->member_count), $this->locale()->toNumber($siteevent->member_count)) ?>,  
                <?php endif; ?>
                <?php echo $this->translate(array('%s comment', '%s comments', $siteevent->comment_count), $this->locale()->toNumber($siteevent->comment_count)) ?>,

                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 2): ?>
                  <?php echo $this->translate(array('%s review', '%s reviews', $siteevent->review_count), $this->locale()->toNumber($siteevent->review_count)) ?>,
                <?php endif; ?>        

                <?php echo $this->translate(array('%s view', '%s views', $siteevent->view_count), $this->locale()->toNumber($siteevent->view_count)) ?>,
                <?php echo $this->translate(array('%s like', '%s likes', $siteevent->like_count), $this->locale()->toNumber($siteevent->like_count)) ?>
                        </p>-->
                <p>
                  <?php if (!empty($siteevent->location)): ?>
                    <?php echo $siteevent->location; ?>
                  <?php endif; ?>
                </p>

                <p>
                  <?php
                  //CHECK IF THE VIEWER IS LEADER
                  $list = $siteevent->getLeaderList();
                  $leaderRow = $list->get($this->viewer());
                  $hostText = '';
                  if ($this->viewer()->getIdentity() == $siteevent->owner_id)
                    $hostText = $this->viewType == 'upcoming' ? "You are owner." : "You were owner.";
                  if ($leaderRow != null && empty($hostText))
                    $hostText = $this->viewType == 'upcoming' ? "You are leader." : "You were leader.";
                  if (($this->viewer()->getIdentity() == (int) $siteevent->host_id) && $siteevent->host_type == 'user' && empty($hostText))
                    $hostText = $this->viewType == 'upcoming' ? "You are host." : "You were host.";
                  if (!Engine_Api::_()->siteevent()->isTicketBasedEvent() && empty($hostText) && isset($siteevent->rsvp) && $siteevent->membership_userid == $this->viewer()->getIdentity())
                    $hostText = $this->viewType == 'upcoming' ? ($siteevent->rsvp == 3 ? "You are invited." : "You are guest.") : ($siteevent->rsvp == 3 ? "You were invited." : "You were guest.");
                  if (empty($hostText))
                    $hostText = 'You like this.';
                  ?>
                  <?php if (!empty($hostText)) : ?>
                    <?php echo $this->translate($hostText); ?>
                  <?php endif; ?>
                </p>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php if ($this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): ?>
          <?php
          echo $this->paginationAjaxControl(
            $this->paginator, $this->identity, "manage_events", array("viewType" => $this->viewType, "isajax" => true, "rsvp" => -1));
          ?>
        <?php endif; ?> 
      </div>
    <?php endif; ?>
  <?php elseif ($this->rsvp != -1): ?>
    <div class="tip"> 
      <span>
        <?php
        if (!empty($siteevent_approved)) {
          echo $this->translate('You do not have any event that match your search criteria.');
        } else {
          echo $this->translate($this->event_manage_msg);
        }
        ?> 
      </span> 
    </div>
  <?php else: ?>
    <div class="tip">
      <span> 
        <?php
        if (!empty($siteevent_approved)) {
          if ($this->viewType != 'past')
            echo $this->translate('You do not have any event.');
          else
            echo $this->translate('You do not have any event that match your search criteria.');
        } else {
          echo $this->translate($this->event_manage_msg);
        }
        ?>
      </span> 
    </div>
  <?php endif; ?>
 <?php if (!$this->pagination && !$this->autoContentLoad): ?>
  </div>
<?php endif; ?>

<script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) { ?>
sm4.core.runonce.add(function() { 
  var ul_id = 'managesiteevents_ul';
      //call function to change rsvp - bind using click
      sm4app.core.Module.event.changeRsvpApp(ul_id);
       
           var manageSiteeventWidgetUrl = sm4.core.baseUrl + 'widget/index/mod/siteevent/name/manage-events-siteevent';
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->formValues);?>, 'contentUrl' : manageSiteeventWidgetUrl, 'activeRequest' : false, 'container' : ul_id }; 
          });
          
         
   <?php } ?>    
</script>
<script type="text/javascript">

  
</script> 