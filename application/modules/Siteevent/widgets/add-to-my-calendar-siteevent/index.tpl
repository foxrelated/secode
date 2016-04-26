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

<div id="calendar" class="siteevent_calender_button">
   <a class="siteevent_buttonlink" onclick="AddToCalendar();return false;"><span><?php echo $this->translate("Add to Calendar") ?></span></a>
    <ul id="dropdown-menu" class="dropdown_menu" style="display:none;">
        <?php if (in_array('google', $this->calendarOptions)): ?>  
            <li><?php echo $this->googlelink; ?></li>
        <?php endif; ?>
        <?php if (in_array('iCal', $this->calendarOptions)): ?>  
            <li><a title="<?php echo $this->translate("Add to iCal"); ?>" href="<?php echo $this->url(array('action' => 'ical-outlook', 'event_id' => $this->siteevent->getIdentity()), 'siteevent_dashboard', true); ?>"><span class="seao_icon_ical">&nbsp;</span><?php echo $this->translate("iCal"); ?></a></li>
        <?php endif; ?>
            <?php if (in_array('outlook', $this->calendarOptions)): ?> 
            <li><a title="<?php echo $this->translate("Add to Outlook Calendar"); ?>" href="<?php echo $this->url(array('action' => 'ical-outlook', 'event_id' => $this->siteevent->getIdentity()), 'siteevent_dashboard', true); ?>"><span class="seao_icon_outlook">&nbsp;</span><?php echo $this->translate("Outlook Calendar"); ?></a></li>
            <?php endif; ?>
        <?php if (in_array('yahoo', $this->calendarOptions)): ?>  
            <li><?php echo $this->yahoolink; ?></a></li>
<?php endif; ?>
    </ul>
</div>

<script type="text/javascript">
    var addToCalendarEnable = true;
    function AddToCalendar() {
        if( $('dropdown-menu'))
        $('dropdown-menu').toggle();
        addToCalendarEnable = true;
    }
    en4.core.runonce.add(function () {
     var addToCalendarHideClickEvent=function() {
        if(!addToCalendarEnable && $('dropdown-menu'))
          $('dropdown-menu').style.display = 'none';
        addToCalendarEnable=false; 
      };
      //hide on body clicdk
      $(document.body).addEvent('click',addToCalendarHideClickEvent.bind());
    });

</script>