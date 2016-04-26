<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent_dashboard.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js')
?>
<?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>

<?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
<div class="layout_middle">
    <?php //include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/edit_tabs.tpl';  ?>
    <div class="siteevent_event_form">

        <div id="show_tab_content">
            <?php //endif;  ?>

            <div class="siteevent_post_announcement">
                <?php echo $this->form->render($this); ?>
            </div>
            <br />	
            <div id="show_tab_content_child">
            </div>
            <?php //if (empty($this->is_ajax)) :  ?>
        </div>
    </div>
</div>
<?php //endif;  ?>

<?php
$dateFormat = $this->locale()->useDateLocaleFormat();
$calendarFormatString = trim(preg_replace('/\w/', '$0/', $dateFormat), '/');
$calendarFormatString = str_replace('y', 'Y', $calendarFormatString);
?>

<script type="text/javascript">
    seao_dateFormat = '<?php echo $this->locale()->useDateLocaleFormat(); ?>';
    function updateTextFields(option) {
        if (option == 0) {
            if ($('expirydate-wrapper'))
                $('expirydate-wrapper').style.display = 'none';
        }
        else {
            if ($('expirydate-wrapper'))
                $('expirydate-wrapper').style.display = 'block';
        }
    }
</script>
<script type="text/javascript">

    en4.core.runonce.add(function()
    {
        en4.core.runonce.add(function init()
        {
            monthList = [];
            myCal = new Calendar({'start_cal[date]': '<?php echo $calendarFormatString; ?>', 'end_cal[date]': '<?php echo $calendarFormatString; ?>'}, {
                classes: ['event_calendar'],
                pad: 0,
                direction: 0
            });
        });
    });

    en4.core.runonce.add(function() {
        var cal_bound_start = seao_getstarttime(document.getElementById('startdate-date').value);
        // check end date and make it the same date if it's too
        cal_startdate.calendars[0].start = new Date(cal_bound_start);
        // redraw calendar
        cal_startdate.navigate(cal_startdate.calendars[0], 'm', 1);
        cal_startdate.navigate(cal_startdate.calendars[0], 'm', -1);

        cal_startdate_onHideStart();
        // cal_endtime_onHideStart();
    });

    var cal_startdate_onHideStart = function() {
        var cal_bound_start = seao_getstarttime(document.getElementById('startdate-date').value);
        // check end date and make it the same date if it's too
        cal_expirydate.calendars[0].start = new Date(cal_bound_start);
        // redraw calendar
        cal_expirydate.navigate(cal_expirydate.calendars[0], 'm', 1);
        cal_expirydate.navigate(cal_expirydate.calendars[0], 'm', -1);
    }

    var cal_expirydate_onHideStart = function() {
        var cal_bound_start = seao_getstarttime(document.getElementById('expirydate-date').value);
        // check start date and make it the same date if it's too
        cal_startdate.calendars[0].end = new Date(cal_bound_start);
        // redraw calendar
        cal_startdate.navigate(cal_startdate.calendars[0], 'm', 1);
        cal_startdate.navigate(cal_startdate.calendars[0], 'm', -1);
    }

    window.addEvent('domready', function() {
        if ($('expirydate-minute')) {
            $('expirydate-minute').style.display = 'none';
        }

        if ($('expirydate-ampm')) {
            $('expirydate-ampm').style.display = 'none';
        }

        if ($('expirydate-hour')) {
            $('expirydate-hour').style.display = 'none';
        }

        if ($('startdate-minute')) {
            $('startdate-minute').style.display = 'none';
        }

        if ($('startdate-ampm')) {
            $('startdate-ampm').style.display = 'none';
        }

        if ($('startdate-hour')) {
            $('startdate-hour').style.display = 'none';
        }
    });

    if ($('expirydate-minute')) {
        $('expirydate-minute').style.display = 'none';
    }

    if ($('expirydate-ampm')) {
        $('expirydate-ampm').style.display = 'none';
    }

    if ($('expirydate-hour')) {
        $('expirydate-hour').style.display = 'none';
    }

    if ($('startdate-minute')) {
        $('startdate-minute').style.display = 'none';
    }

    if ($('startdate-ampm')) {
        $('startdate-ampm').style.display = 'none';
    }

    if ($('startdate-hour')) {
        $('startdate-hour').style.display = 'none';
    }
</script>