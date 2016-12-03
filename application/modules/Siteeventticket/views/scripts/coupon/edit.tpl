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

<?php
    $this->headLink()
            ->appendStylesheet($this->layout()->staticBaseUrl
                    . 'application/modules/Siteeventticket/externals/styles/style_siteeventcoupon.css');

    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.js')
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.Local.js')
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.Request.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>

<div class="siteevent_viewevents_head">
    <?php echo $this->htmlLink($this->siteevent->getHref(), $this->itemPhoto($this->siteevent, 'thumb.icon', '', array('align' => 'left'))) ?>

    <div class="fright">
        <a href='<?php echo $this->url(array('action' => 'manage', 'event_id' => $this->siteevent->event_id), 'siteeventticket_coupon', true) ?>' class='buttonlink siteevent_icon_edit'><?php echo $this->translate('Dashboard');?></a>
    </div>

    <h2>	
        <?php echo $this->siteevent->__toString() ?>	
        <?php echo $this->translate('&raquo; '); ?>
        <?php echo $this->htmlLink($this->siteevent->getHref(array('tab' => $this->eventProfileCouponTabId)), $this->translate('Coupons')) ?>
    </h2>
</div>

<div class="clr siteevent_event_form">
  <?php echo $this->form->render($this) ?>
</div>

<?php
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
    
	$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
?>

<script type="text/javascript">
    
    var seao_dateFormat = '<?php echo Engine_Api::_()->seaocore()->getLocaleDateFormat(); ?>';

    //START CALENDAR WORK FOR COUPON START- END DATE
    en4.core.runonce.add(function()
    {
        var showCurrentTime = false;
        var currentTime = "<?php echo date('m/d/Y', time()); ?>";

        <?php if(strtotime($this->siteeventticketcoupon->start_time) > time() ) : ?>
            showCurrentTime = true;
        <?php endif; ?>

        initializeCalendarDate(seao_dateFormat, cal_start_date, cal_end_date, 'start_date', 'end_date', showCurrentTime, currentTime);
        //cal_start_date_onHideStart();
    });

//    var cal_start_time_onHideStart = function(){
//        cal_starttimeDate_onHideStart(seao_dateFormat, cal_start_time, cal_end_time, 'start_time', 'end_time');
//    };
    //END CALENDAR WORK FOR COUPON START- END DATE

    if($('discount_type')) {
        $('discount_type').addEvent('change', function(){
            if($('discount_type').value == 1)
            {
                document.getElementById('price-wrapper').style.display = 'block';
                document.getElementById('rate-wrapper').style.display = 'none';
            }
            else{
                document.getElementById('price-wrapper').style.display = 'none';
                document.getElementById('rate-wrapper').style.display = 'block';
            }
        });

        window.addEvent('domready', function() {
            if($('discount_type').value == 1)
            {
                document.getElementById('price-wrapper').style.display = 'block';
                document.getElementById('rate-wrapper').style.display = 'none';
            }
            else{
                document.getElementById('price-wrapper').style.display = 'none';
                document.getElementById('rate-wrapper').style.display = 'block';
            }
        });
    }
    
    en4.core.runonce.add(function(){

        // check end date and make it the same date if it's too
        cal_end_time.calendars[0].start = new Date( $('end_time-date').value );
        // redraw calendar
        cal_end_time.navigate(cal_end_time.calendars[0], 'm', 1);
        cal_end_time.navigate(cal_end_time.calendars[0], 'm', -1);

        cal_start_time.calendars[0].start = new Date( $('start_time-date').value );
        // redraw calendar
        cal_start_time.navigate(cal_start_time.calendars[0], 'm', 1);
        cal_start_time.navigate(cal_start_time.calendars[0], 'm', -1);

    });

    var myCalStart = false;
    var myCalEnd = false;

    var endsettingss = '<?php echo $this->siteeventticketcoupon->end_settings;?>';

    function updateTextFields(value) {
      if (value == 0 && $("end_time-wrapper")) {
          $("end_time-wrapper").style.display = "none";
      } else if (value == 1 && $("end_time-wrapper")) { 
          $("end_time-wrapper").style.display = "block";
      }
    }

    en4.core.runonce.add(updateTextFields(endsettingss));

</script>