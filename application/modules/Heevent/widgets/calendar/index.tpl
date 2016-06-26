<?php
/**
* @category   Application_Extensions
* @package    Heevent
* @copyright  Copyright Hire-Experts LLC
* @license    http://www.hire-experts.com
* @author     Bolot
*/
?>
<div>
  <table id="calendar">
    <thead>
    <tr>
      <td style="text-align: center;">‹
      <td height="30px" colspan="5" style="text-align: center;">
      <td style="text-align: center;">›
        <tr>
          <td>
          <?php echo $this->translate('Mo') ?>
          <td>
          <?php echo $this->translate('Tu') ?>
          <td>
          <?php echo $this->translate('We') ?>
          <td>
          <?php echo $this->translate('Th') ?>
          <td>
          <?php echo $this->translate('Fr') ?>
          <td>
          <?php echo $this->translate('Sa') ?>
          <td>
            <?php echo $this->translate('Su');?>
    <tbody>
  </table>
</div>
<script>
  function calendar(id, year, month) {
    window.events = [];
    events = <?php  echo $this->events ? $this->events : '[]'; ?>;

    var Dlast = new Date(year, month + 1, 0).getDate(),
      D = new Date(year, month, Dlast),
      DNlast = new Date(D.getFullYear(), D.getMonth(), Dlast).getDay(),
      DNfirst = new Date(D.getFullYear(), D.getMonth(), 1).getDay(),
      calendar = '<tr>',
      month = ["<?php echo $this->translate('January')?>", "<?php echo $this->translate('February')?>", "<?php echo $this->translate('March')?>", "<?php echo $this->translate('April')?>", "<?php echo $this->translate('May')?>", "<?php echo $this->translate('June')?>", "<?php echo $this->translate('July')?>", "<?php echo $this->translate('August')?>", "<?php echo $this->translate('September')?>", "<?php echo $this->translate('October')?>", "<?php echo $this->translate('November')?>", "<?php echo $this->translate('December')?>"];
    if (DNfirst != 0) {
      for (var i = 1; i < DNfirst; i++) calendar += '<td>';
    } else {
      for (var i = 0; i < 6; i++) calendar += '<td>';
    }
    var event_in_day = 'class="event_none_in_calendar"';
	var today  = 0;
	var event_detail  = 0;
    for (var i = 1; i <= Dlast; i++) {
	today = 0;
	event_detail = 0;
		for(var j in events) {
		if(j.toInt() == i){
		today = i;
		event_detail = j;
		}
	}
      if (typeof(events[event_detail]) !== "undefined") {

	    if (events[event_detail].mouth == D.getMonth() + 1 && D.getFullYear() == events[event_detail].year) {
          var event_in_day = 'class="event_in_calendar"';
        } else {
          event_in_day = 'class="event_none_in_calendar"';
        }
      } else {
        event_in_day = 'class="event_none_in_calendar"';
      }
      if (i == new Date().getDate() && D.getFullYear() == new Date().getFullYear() && D.getMonth() == new Date().getMonth()) {
        calendar += '<td  height="30px" width="30px" class="today" onclick="getEvents_byDate(' + i + ', ' + D.getMonth() + ' , ' + D.getFullYear() + ')">' + '<div ' + event_in_day + ' >' + i + '</div>';
      } else {
        calendar += '<td height="30px" width="30px" onclick="getEvents_byDate(' + i + ', ' + D.getMonth() + ' , ' + D.getFullYear() + ')">' + '<div  ' + event_in_day + '>' + i + '</div>';
      }
      if (new Date(D.getFullYear(), D.getMonth(), i).getDay() == 0) {
        calendar += '<tr>';
      }
    }
    for (var i = DNlast; i < 7; i++) calendar += '<td>&nbsp;';
    document.querySelector('#' + id + ' tbody').innerHTML = calendar;
    document.querySelector('#' + id + ' thead td:nth-child(2)').innerHTML = month[D.getMonth()] + ' ' + D.getFullYear();
    document.querySelector('#' + id + ' thead td:nth-child(2)').dataset.month = D.getMonth();
    document.querySelector('#' + id + ' thead td:nth-child(2)').dataset.year = D.getFullYear();
    if (document.querySelectorAll('#' + id + ' tbody tr').length < 6) {
      document.querySelector('#' + id + ' tbody').innerHTML += '<tr><td>&nbsp;<td>&nbsp;<td>&nbsp;<td>&nbsp;<td>&nbsp;<td>&nbsp;<td>&nbsp;';
    }
  }
  calendar("calendar", new Date().getFullYear(), new Date().getMonth());
  document.querySelector('#calendar thead tr:nth-child(1) td:nth-child(1)').onclick = function () {
    calendar("calendar", document.querySelector('#calendar thead td:nth-child(2)').dataset.year, parseFloat(document.querySelector('#calendar thead td:nth-child(2)').dataset.month) - 1);
  }
  document.querySelector('#calendar thead tr:nth-child(1) td:nth-child(3)').onclick = function () {
    calendar("calendar", document.querySelector('#calendar thead td:nth-child(2)').dataset.year, parseFloat(document.querySelector('#calendar thead td:nth-child(2)').dataset.month) + 1);
  }
  function getEvents_byDate(day, mouth, year) {

    var data = {
      'day': day,
      'mouth': mouth + 1,
      'year': year
    };
    $('content_event_calendar').setStyle('display', 'block');
    $$('.background_page_calendar_event').setStyle('display', 'block');
    $('content_event_calendar').set('html', '<div class="loader_hevent"></div>');
    var left = (window.getSize().x - 600) / 2;
    $('content_event_calendar').setStyle('left', left + 'px');
    var arg = {
      url: en4.core.baseUrl + 'heevent/index/geteventst?format=html',
      method: 'post',
      data: data,
      evalScripts: true,
      onSuccess: function (a, b, html) {
        $('content_event_calendar').set('html', html);
        var left = (window.getSize().x - 600) / 2;
        var height = (window.getSize().y - 130);
        $('content_event_calendar').setStyle('display', 'block');
        $('content_event_calendar').setStyle('left', left + 'px');
        $('content_event_calendar').setStyle('height', height + 'px');
        $$('.background_page_calendar_event').setStyle('display', 'block');
      }
    };

    new Request.HTML(arg).send();
  }
  function close_calendar_heevent() {
    $('content_event_calendar').set('html', '');
    $('content_event_calendar').setStyle('display', 'none');
    $$('.background_page_calendar_event').setStyle('display', 'none');
  }
</script>
<div class="content_event_calendar" id="content_event_calendar" style=" "></div>
<div class="background_page_calendar_event" style=" " onclick="close_calendar_heevent()"></div>