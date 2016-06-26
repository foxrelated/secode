<div style="margin: 0px auto;width: 100%;box-sizing: border-box;text-align: center;font-size: 18px;font-weight: bold;"> <?php echo $this->translate('Events date: ') . $this->date?> </div>
<ul class='events_browse'>
  <?php
  if(count($this->events)>0) {
  foreach( $this->events as $event ): ?>
    <li class="calendar_view">
      <div class="events_photo">
        <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
      </div>
      <div class="events_info">
        <div class="events_title">
          <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
        </div>
        <div class="events_members">
          <?php echo $this->locale()->toDateTime($event->starttime) ?>
        </div>
        <div class="events_members">
          <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()),$this->locale()->toNumber($event->membership()->getMemberCount())) ?>
          <?php echo $this->translate('led by') ?>
          <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
        </div>
        <div class="events_desc">
          <?php echo $event->getDescription() ?>
        </div>
      </div>
    </li>
  <?php endforeach;
  }
  if(count($this->page_event)>0) {
    foreach( $this->page_event as $event ): ?>
      <li class="calendar_view">
        <div class="events_photo">
          <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
        </div>
        <div class="events_info">
          <div class="events_title">
            <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
          </div>
          <div class="events_members">
            <?php echo $this->locale()->toDateTime($event->starttime) ?>
          </div>

            <div class="events_members"><i
                class="hei hei-file-text"></i><span><?php echo $this->translate('on page ');echo $this->htmlLink($event->getPage()->getHref(), $event->getPage()->getTitle());?></span>
            </div>
          <div class="events_members">
            <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()),$this->locale()->toNumber($event->membership()->getMemberCount())) ?>
            <?php echo $this->translate('led by') ?>
            <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
          </div>
          <div class="events_desc">
            <?php echo $event->getDescription() ?>
          </div>
        </div>
      </li>
    <?php endforeach;
  }

  if(count($this->page_event)<0 && count($this->events)<0){
  ?>
  <div class="not_found" style="text-align: center;margin: 15px;"><?php echo $this->translate('No events in this date');?></div>

  <?php
  }?>
</ul>