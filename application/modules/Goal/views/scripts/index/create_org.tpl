<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */
?>
<?php echo $this->content()->renderWidget('goal.browse-menu') ?>

<?php echo $this->form->render() ?>

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

<script type="text/javascript">
  $$('.core_main_goal').getParent().addClass('active');
</script>
