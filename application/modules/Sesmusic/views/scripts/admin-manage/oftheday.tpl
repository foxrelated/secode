<?php 

?>
<script type="text/javascript">
  var cal_starttime_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_endtime.calendars[0].start = new Date( $('starttime-date').value );
    // redraw calendar
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
  }
  var cal_endtime_onHideStart = function(){
    // check start date and make it the same date if it's too
    cal_starttime.calendars[0].end = new Date( $('endtime-date').value );
    // redraw calendar
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
  }
</script>
<div class="global_form_popup sesmusic_add_itemoftheday_popup">
  <?php echo $this->form->render($this) ?>
</div>

<script type="text/javascript">
  $('starttime-hour').hide();
  $('starttime-minute').hide();
  $('starttime-ampm').hide();
  $('endtime-hour').hide();
  $('endtime-minute').hide();
  $('endtime-ampm').hide();
</script>
