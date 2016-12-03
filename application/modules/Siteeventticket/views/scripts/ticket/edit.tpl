<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<!--TAX MANDATORY MESSAGE DISPLAY-->
<?php if ($this->taxMandatoryMessage): ?> 
  <div class="tip"><span>
      <?php echo $this->translate('Admin has set the Tax as mandatory, you need to set tax rate before ticket creation. Please set tax rate %1$shere%2$s', '<a href =' . $this->url(array('controller' => 'tax', 'action' => 'index', 'event_id' => $this->event_id), 'siteeventticket_tax_general') . ' >', '</a>') ?></span>
  </div>
  <?php return;
endif;
?>

<script type="text/javascript">
  SmoothboxSEAO.addScriptFiles.push("<?php echo $this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js' ?>");
  SmoothboxSEAO.addStylesheets.push("<?php echo $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/calendar/styles.css' ?>");

en4.core.runonce.add(function () {
  //  DISPLAY END TIME OPTIONS
  showCustomEndTimeOption();
  
  //  FORM SUBMIT & DISPLAY THE RESPONSE html
  $("siteeventticket_add_quick").removeEvents('submit').addEvent('submit', function (e) {
    e.stop();
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'siteeventticket/ticket/edit/event_id/<?php echo $this->event_id ?>/ticket_id/<?php echo $this->ticket_id ?>',
      method: 'POST',
      data: $("siteeventticket_add_quick").toQueryString() + '&format=html&seaoSmoothbox=1',
      evalScripts: true,
      onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElement('.seao_smoothbox_lightbox_content_html').innerHTML = responseHTML;
        en4.core.runonce.trigger();
        SmoothboxSEAO.doAutoResize();
      }
     })
    );
  });
});

  function showCustomEndTimeOption()
  {
    if ($('sell_endtime-wrapper')) {
      if ($('is_same_end_date-1').checked) {
        $('sell_endtime-wrapper').style.display = 'none';
      }
      else {
        $('sell_endtime-wrapper').style.display = 'block';
      }
    }
  }

</script>

<div class="siteevent_event_form">
<?php echo $this->form->render($this); ?>
</div>

<!--JS FOR CALENDAR => DISABLE CALENDAR ELEMENTS -->
<script type="text/javascript">
  //START CALENDAR WORK FOR TICKET START-END DATE
  
  var cal_sell_starttime_onHideStart = function () {
    // check end date and make it the same date if it's too
    cal_sell_endtime.calendars[0].start = new Date(document.getElementById('sell_starttime-date').value);
    // redraw calendar
    cal_sell_endtime.navigate(cal_sell_endtime.calendars[0], 'm', 1);
    cal_sell_endtime.navigate(cal_sell_endtime.calendars[0], 'm', -1);
  };
  var cal_sell_endtime_onHideStart = function () {
    // check start date and make it the same date if it's too
    cal_sell_starttime.calendars[0].end = new Date(document.getElementById('sell_endtime-date').value);
    // redraw calendar
    cal_sell_starttime.navigate(cal_sell_starttime.calendars[0], 'm', 1);
    cal_sell_starttime.navigate(cal_sell_starttime.calendars[0], 'm', -1);
  };

// END CALENDAR WORK FOR TICKET START-END DATE  
  en4.core.runonce.add(function () {
    // check end date and make it the same date if it's too
    cal_sell_starttime.calendars[0].start = new Date(document.getElementById('current_date').value);    
    cal_sell_starttime.calendars[0].end = new Date(document.getElementById('sell_endtime-date').value);
    
    cal_sell_endtime.calendars[0].start = new Date(document.getElementById('sell_starttime-date').value);    
    cal_sell_endtime.calendars[0].end = new Date(document.getElementById('event_endtime').value);

    cal_sell_starttime_onHideStart();
    cal_sell_endtime_onHideStart();
  });

</script>
