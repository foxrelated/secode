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
<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>

<?php if ($this->showContent): ?>
  <a id="siteevent_profile_guests_anchor"></a>
  <script type="text/javascript">
    var waiting = '<?php echo $this->waiting ?>';
    var rsvp = <?php echo $this->rsvp; ?>;
    var occurrence_id = '<?php echo $this->occurrence_id; ?>';
    var occurrenceid = '<?php echo $this->occurrenceid; ?>';
    var url = sm4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    var totalEventGuest = '<?php echo $this->params['totalEventGuest']; ?>';
    var params = {
      requestParams:<?php echo json_encode($this->params) ?>,
      responseContainer: $('.layout_siteevent_profile_members')
    }

    sm4.core.runonce.add(function() {
      $('#siteevent_members_search_input').bind('keypress', function(e) {
        if (e.which != 13)
          return;
        getSiteeventAjaxContent(url,this.value, null, rsvp, null);
      });
    });

    //CHECK EITHER TO SHOW ALL EVENTS OR ONLY EVENT OCCURENCE EVENT.
    function eventGuestsRsvp(memberRsvp, event_occurrence) {
      event_occurrence = event_occurrence || 0;
      rsvp = memberRsvp;
      var search = $('#siteevent_members_search_input').val();
      getSiteeventAjaxContent(url,search, event_occurrence, rsvp, null);
    }
  </script>

  <?php if (!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0): ?>
    <script type="text/javascript">
      var showWaitingMembers = function() {
        getSiteeventAjaxContent(url,null, null, rsvp, true);
      }
    </script>
  <?php endif; ?>


  <?php if (!$this->waiting): ?>
     <div class="sm-item-members-count">
      <select name="select_rsvps"  onchange="eventGuestsRsvp(this.value)">
        <option value="-1" id='select_all' <?php if ($this->rsvp == -1) echo "selected"; ?>><?php echo $this->translate('All') ?></option>
        <option value="2" id='select_attending'<?php if ($this->rsvp == 2) echo "selected"; ?>><?php echo $this->translate('Attending') ?></option>
        <option value="1" id='select_maybeattending' <?php if ($this->rsvp == 1) echo "selected"; ?>><?php echo $this->translate('Maybe Attending') ?></option>
        <option value="0" id='select_notattending'<?php if ($this->rsvp == 0) echo "selected"; ?>><?php echo $this->translate('Not Attending') ?></option>
      </select>
    </div>
     <div class="sm-item-members-count">
      <?php //SHOW EVENT OCCURRENCE DATE DROP-DOWN FOR FILTERING GUESTS  ?>
      <?php if (!empty($this->datesInfo) && count($this->datesInfo) > 1): ?>
        <select onchange="occurrence_id = this.value;
            eventGuestsRsvp(rsvp)" id='date_filter_occurrence'>
                <?php
                $filter_dates = Engine_Api::_()->siteevent()->getAllOccurrenceDate($this->datesInfo);
                foreach ($filter_dates as $key => $date):
                  ?> 
            <option value="<?php echo $key; ?>" <?php if ($this->occurrence_id == $key): ?> selected='selected' <?php endif; ?>><?php echo $date; ?></option>
          <?php endforeach;
          ?>
        </select>
      <?php endif; ?>
      <input id="siteevent_members_search_input" type="text" placeholder="<?php echo $this->translate('Search Guests'); ?>" role="search" data-type="search" class="ui-input-text" data-mini="true" value="<?php echo $this->search; ?>">
    </div>
    <?php if ($this->totalEventGuests > 0): ?>
      <?php if (count($this->datesInfo) > 1 || ((!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0) || (($this->level_id == 1 || $this->event->isOwner($this->viewer) || $this->canEdit) && $this->members->getTotalItemCount() > 0 ) || ($this->members->getTotalItemCount() > 0 && ($this->level_id == 1 || $this->event->isOwner($this->viewer) || $this->canEdit)))) : ?>
        <?php if (count($this->datesInfo) > 1) : ?>
          <?php echo $this->translate(array('%1$s Total Guest', '%1$s Total Guests', $this->totalEventGuests), $this->locale()->toNumber($this->totalEventGuests)) ?>
            <?php if ($this->event_Occurrence > 1): ?>
                | <?php echo $this->translate('This occurrence of the event has') . ' '; ?>
                    <?php echo $this->translate(array('%1$s guest', '%1$s guests', $this->totalOccurrenceMembers), $this->locale()->toNumber($this->totalOccurrenceMembers)) ?>
            <?php endif; ?>  
        <?php endif; ?>
        <?php if ((!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0) || (($this->level_id == 1 || $this->event->isOwner($this->viewer) || $this->canEdit) && $this->members->getTotalItemCount() > 0 ) || ($this->members->getTotalItemCount() > 0)) : ?>
          <div class="sm-item-members-count">
            <?php if (!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0): ?>
              <?php echo $this->htmlLink('javascript:void(0);', $this->translate('See Waiting'), array('onclick' => 'showWaitingMembers(); return false;')) ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    <?php endif; ?>
  <?php else: ?>
    <div class="sm-item-members-count">
      <?php //SHOW EVENT OCCURRENCE DATE DROP-DOWN FOR FILTERING GUESTS ?>
      <?php if (!empty($this->datesInfo) && count($this->datesInfo) > 1): ?>
        <select onchange="occurrence_id = this.value;
            showWaitingMembers()" id="date_filter_occurrence">
                <?php
                $filter_dates = Engine_Api::_()->siteevent()->getAllOccurrenceDate($this->datesInfo);
                foreach ($filter_dates as $key => $date):
                  ?> 
            <option value="<?php echo $key; ?>" <?php if ($this->occurrence_id == $key): ?> selected='selected' <?php endif; ?>><?php echo $date; ?></option>
          <?php endforeach;
          ?>
        </select>
      <?php endif; ?>
    </div>
    <div class="sm-item-members-count">
      <?php echo $this->translate(array('This event has %1$s member waiting for approval.', 'This event has %1$s members waiting for approval.', $this->members->getTotalItemCount()), $this->locale()->toNumber($this->members->getTotalItemCount())) ?>
    </div>
  <?php endif; ?>

    <?php if ($this->members->getTotalItemCount() > 0): ?>
      <div class="sm-content-list">	
        <ul data-role="listview" data-inset="false">
          <?php
          foreach ($this->members as $member):
            if (isset($member->resource_id) && !empty($member->resource_id)) {
              $memberInfo = $member;
              $member = $this->item('user', $memberInfo->user_id);
            } else {

              $memberInfo = $this->event->membership()->getMemberInfoCustom($member);
            }
            if(!isset($member->user_id)) continue;
            $listItem = $this->list->get($member);
            $isLeader = ( null !== $listItem );
            ?>
            <li>
              <a href="<?php echo $member->getHref(); ?>">
                <?php echo $this->itemPhoto($member, 'thumb.icon'); ?>
                <h3><?php echo $member->getTitle() ?></h3>
                <p>
                  <?php // Titles   ?>
                  <?php if ($this->event->getParent()->getGuid() == ($member->getGuid())): ?>
                    <strong><?php echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('owner'))) ?></strong>
                  <?php endif; ?>
                </p>
                <p>
                  <?php if ($memberInfo->rsvp == 0): ?>
                    <?php echo $this->translate('Not Attending') ?>
                  <?php elseif ($memberInfo->rsvp == 1): ?>
                    <?php echo $this->translate('Maybe Attending') ?>
                  <?php elseif ($memberInfo->rsvp == 2): ?>
                    <?php echo $this->translate('Attending') ?>
                  <?php else: ?>
                    <?php echo $this->translate('Awaiting Reply') ?>
                  <?php endif; ?>
                </p>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php if ($this->members->count() > 1): ?>
          <?php
          echo $this->paginationAjaxControl(
                  $this->members, $this->identity, 'siteevent_profile_guests_anchor', array("occurrence_id" => $this->occurrence_id));
          ?>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="tip"> 
        <span>
          <?php echo $this->translate('Nobody has joined this event that matches your search criteria.'); ?>
        </span>
      </div>

    <?php endif; ?>
<?php endif; ?>
<style type="text/css">
.layout_siteevent_profile_members > h3{display:none;}
</style>