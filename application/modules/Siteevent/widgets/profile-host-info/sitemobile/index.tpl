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

<?php if ($this->placeWidget == 'smallColumn'): ?>
  <div class="sm-widget-block">
    <div class="sm-ui-cont-head"> 
       <div class="sm-ui-cont-author-photo">
          <?php echo $this->htmlLink($this->host->getHref(), $this->itemPhoto($this->host, 'thumb.icon', '', array('align' => 'center'))); ?>
      </div>
      <div class="sm-ui-cont-cont-info">
        <div class="sm-ui-cont-author-name">
            <?php echo $this->htmlLink($this->host->getHref(), $this->host->getTitle()); ?>
        </div>
        <?php if (in_array('body', $this->allowedInfo) && $this->getDescription) : ?>
          <div class="sm-ui-cont-cont-date">
            <?php echo $this->getDescription; ?>
          </div>
        <?php endif; ?>
      </div> 
    </div>
      <?php if (in_array('totalevent', $this->showInfo) || in_array('totalguest', $this->showInfo) || $this->totalRating): ?>  
          <div class="o_hidden host_info_stats clr b_medium mbot10">
              <?php if (in_array('totalevent', $this->showInfo)) : ?>
                  <div class="mbot5 clr">
                      <?php //echo $this->translate("%s events hosted.", "<b>" .$this->subject()->countOrganizedEvent(). "</b>"); ?>
                      <?php $countOrganizedEvent = $this->subject()->countOrganizedEvent(); ?> 
                      <?php echo $this->translate(array('<b>%s</b> event hosted.', '<b>%s</b> events hosted.', $countOrganizedEvent), $this->locale()->toNumber($countOrganizedEvent)); ?>
                  </div>
              <?php endif; ?>
              <?php if (in_array('totalguest', $this->showInfo)) : ?>
                  <div class="mbot5 clr">
                      <?php //echo $this->translate("%s guests joined.", "<b>" .$this->totalGuest. "</b>"); ?>
                      <?php echo $this->translate(array('<b>%s</b> guest joined', '<b>%s</b> guests joined.', $this->totalGuest), $this->locale()->toNumber($this->totalGuest)); ?>  
                  </div>
              <?php endif; ?>
              <?php if ($this->totalRating): ?>
                  <div class="clr">
                      <div class="mright5">
                          <?php echo $this->translate("Total ratings:"); ?>
                      </div>
                      <div>
                          <?php echo $this->showRatingStarSiteeventSM($this->totalRating, 'overall', 'big-star'); ?>
                      </div>
                  </div>
              <?php endif; ?>
          </div>
      <?php endif; ?>
  </div>
<?php else: ?>
    <h3 class="event_profile_host_info_main_heading">
        <?php echo $this->translate("Hosted by <b>%s</b>", $this->htmlLink($this->host->getHref(), $this->host->getTitle())); ?>
    </h3>
    <div class="siteevent_profile_host_info siteevent_side_widget">
        <div class="o_hidden">
            <span class="host_photo">
                <?php echo $this->htmlLink($this->host->getHref(), $this->itemPhoto($this->host, 'thumb.icon')); ?>
            </span>

            <div class="o_hidden host_info_stats mbot5">
                <?php if (in_array('totalevent', $this->showInfo)) : ?>
                    <div class="mbot5 clr">
                        <?php echo $this->translate("%s events hosted.", "<b>" . $this->subject()->countOrganizedEvent() . "</b>"); ?>
                    </div>
                <?php endif; ?>
                <?php if (in_array('totalguest', $this->showInfo)) : ?>
                    <div class="mbot5 clr">
                        <?php echo $this->translate("%s guests joined.", "<b>" . $this->totalGuest . "</b>"); ?>
                    </div>
                <?php endif; ?>
                <?php if ($this->totalRating): ?>
                    <div class="mbot5 clr">
                        <div class="mright5">
                            <?php echo $this->translate("Total ratings:"); ?>
                        </div>
                        <div>
                            <?php echo $this->showRatingStarSiteevent($this->totalRating, 'overall', 'big-star'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (in_array('body', $this->allowedInfo) && $this->getDescription) : ?>
                <div class="host_body show_content_body clr">
                    <?php echo $this->getDescription; ?>
                </div>
            <?php endif; ?>
        </div>  
    </div>
<?php endif; ?>