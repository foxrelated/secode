<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<style type="text/css">
  select{
    float:left;
    margin-right:10px;
  }
</style>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  var maxRecipients = 10;

  function removeFromToValue(id, elmentValue, element) {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = $(elmentValue).value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti != -1) {
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++) {
        removeToValue(recipientsArray[i], toValueArray, elmentValue);
      }
    } else {
      removeToValue(id, toValueArray, elmentValue);
    }

    // hide the wrapper for element if it is empty
    if ($(elmentValue).value == "") {
      $(elmentValue + '-wrapper').setStyle('height', '0');
      $(elmentValue + '-wrapper').setStyle('display', 'none');
    }
    $(element).disabled = false;
  }

  function removeToValue(id, toValueArray, elmentValue) {
    for (var i = 0; i < toValueArray.length; i++) {
      if (toValueArray[i] == id)
        toValueIndex = i;
    }
    toValueArray.splice(toValueIndex, 1);
    $(elmentValue).value = toValueArray.join();
  }

  var packageRequest;
  var eventidsAutocomplete;
  var ticketidsAutocomplete;
  en4.core.runonce.add(function() {

    eventidsAutocomplete = new Autocompleter.Request.JSON('event_name', '<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'report', 'action' => 'suggestevents'), 'admin_default', true) ?>', {
      'postVar': 'search',
      'postData': {'event_ids': $('event_ids').value},
      'minLength': 1,
      'delay': 250,
      'selectMode': 'pick',
      'elementValues': 'event_ids',
      'autocompleteType': 'message',
      'multiple': true,
      'className': 'seaocore-autosuggest',
      'filterSubset': true,
      'tokenFormat': 'object',
      'tokenValueKey': 'label',
      'injectChoice': function(token) {
        var choice = new Element('li', {
          'class': 'autocompleter-choices',
          'html': token.photo,
          'id': token.label
        });

        new Element('div', {
          'html': this.markQueryValue(token.label),
          'class': 'autocompleter-choice'
        }).inject(choice);

        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      },
      onPush: function() {
        if ($('event_ids-wrapper')) {
          $('event_ids-wrapper').style.display = 'block';
        }

        if ($(this.options.elementValues).value.split(',').length >= maxRecipients) {
          this.element.disabled = true;
        }
        eventidsAutocomplete.setOptions({
          'postData': {'event_ids': $('event_ids').value}
        });

<?php if ( !empty($this->reportType) ) : ?>
          ticketidsAutocomplete.setOptions({
            'postData': {'event_ids': $('event_ids').value, 'select_event': $('select_event').value, 'ticket_ids': $('ticket_ids').value}
          });
<?php endif; ?>
      }
    });


    ticketidsAutocomplete = new Autocompleter.Request.JSON('ticket_name', '<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'report', 'action' => 'suggesttickets'), 'admin_default', true) ?>', {
      'postVar': 'search',
      'postData': {'event_ids': $('event_ids').value, 'select_event': $('select_event').value, 'ticket_ids': $('ticket_ids').value},
      'minLength': 1,
      'delay': 250,
      'selectMode': 'pick',
      'elementValues': 'ticket_ids',
      'autocompleteType': 'message',
      'multiple': true,
      'className': 'seaocore-autosuggest',
      'filterSubset': true,
      'tokenFormat': 'object',
      'tokenValueKey': 'label',
      'injectChoice': function(token) {
        var choice = new Element('li', {
          'class': 'autocompleter-choices',
          'html': token.photo,
          'id': token.label
        });

        new Element('div', {
          'html': this.markQueryValue(token.label),
          'class': 'autocompleter-choice'
        }).inject(choice);

        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      },
      onPush: function() {
        if ($('ticket_ids-wrapper')) {
          $('ticket_ids-wrapper').style.display = 'block';
        }

        if ($(this.options.elementValues).value.split(',').length >= maxRecipients) {
          this.element.disabled = true;
        }

        ticketidsAutocomplete.setOptions({
          'postData': {'event_ids': $('event_ids').value, 'select_event': $('select_event').value, 'ticket_ids': $('ticket_ids').value}
        });
      }
    });
  })

</script>

<script type="text/javascript">

  window.addEvent('domready', function() {
    $('start_cal-minute').style.display = 'none';
    if ($('start_cal-ampm'))
      $('start_cal-ampm').style.display = 'none';
    $('start_cal-hour').style.display = 'none';
    $('end_cal-minute').style.display = 'none';
    if ($('end_cal-ampm'))
      $('end_cal-ampm').style.display = 'none';
    $('end_cal-hour').style.display = 'none';

    var empty = '<?php echo $this->empty; ?>';
    var no_ads = '<?php echo $this->no_ads ?>';

    form = $('adminreport_form');
    form.setAttribute("method", "get");

    var e3 = $('event_name-wrapper');
    e3.setStyle('display', 'none');

    var e4 = $('event_ids-wrapper');
    e4.setStyle('display', 'none');

<?php if ( !empty($this->reportType) ) : ?>
  //      var e5 = $('select_ticket-wrapper');
  //      e5.setStyle('display', 'none');

      var e6 = $('ticket_name-wrapper');
      e6.setStyle('display', 'none');

      var e7 = $('ticket_ids-wrapper');
      e7.setStyle('display', 'none');
<?php endif; ?>

    oneventChange($('select_event'));
    onChangeTime($('time_summary'));
    onchangeFormat($('format_report'));
<?php if ( !empty($this->reportType) ) : ?>
      onticketChange($('select_ticket'));
<?php endif; ?>

    // display message tip
    if (empty == 1) {
      if (no_ads == 1)
        $('tip2').style.display = 'block';
      else
        $('tip').style.display = 'block';
    }
  });

  function oneventChange(formElement) {
    var e1 = formElement.value;
    if (e1 == 'all')
    {
      $('event_name-wrapper').setStyle('display', 'none');

      if ($('event_ids').value)
      {
        $('event_ids').value = null;
        $('event_ids-element').getElements('.tag').destroy();

        eventidsAutocomplete.setOptions({
          'postData': {'event_ids': $('event_ids').value}
        });

<?php if ( empty($this->reportType) ) : ?>
          ticketidsAutocomplete.setOptions({
            'postData': {'event_ids': $('event_ids').value, 'select_event': $('select_event').value}
          });
<?php else: ?>
          ticketidsAutocomplete.setOptions({
            'postData': {'event_ids': $('event_ids').value, 'select_event': $('select_event').value, 'ticket_ids': $('ticket_ids').value}
          });
<?php endif; ?>
      }
    }
    else if (e1 == 'specific_event')
      $('event_name-wrapper').setStyle('display', 'block');

<?php if ( !empty($this->reportType) ) : ?>
      if ($('ticket_ids').value)
      {
        $('ticket_ids').value = null;
        $('ticket_ids-element').getElements('.tag').destroy();

        ticketidsAutocomplete.setOptions({
          'postData': {'event_ids': $('event_ids').value, 'select_event': $('select_event').value, 'ticket_ids': $('ticket_ids').value}
        });
      }
<?php endif; ?>
  }

  function onticketChange(formElement) {
    var e1 = formElement.value;
    if (e1 == 'all') {
      $('ticket_name-wrapper').setStyle('display', 'none');
      if ($('ticket_ids').value)
      {
        $('ticket_ids').value = null;
        $('ticket_ids-element').getElements('.tag').destroy();

        ticketidsAutocomplete.setOptions({
          'postData': {'event_ids': $('event_ids').value, 'select_event': $('select_event').value, 'ticket_ids': $('ticket_ids').value}
        });
      }
    }
    else if (e1 == 'specific_ticket') {
      $('ticket_name-wrapper').setStyle('display', 'block');
    }
  }

  function onChangeTime(formElement) {

    if (formElement.value == 'Monthly') {
      $('start_group').setStyle('display', 'block');
      $('end_group').setStyle('display', 'block');
      $('time_group2').setStyle('display', 'none');
    }
    else if (formElement.value == 'Daily') {
      $('start_group').setStyle('display', 'none');
      $('end_group').setStyle('display', 'none');
      $('time_group2').setStyle('display', 'block');
    }

  }

  function onchangeFormat(formElement) {
    form = $('adminreport_form');
    if (formElement.value == 1) {
      $('tip').style.display = 'none';
    }
  }

</script>

<h2 class="fleft">
  <?php echo 'Advanced Events Plugin'; ?>
</h2>


<?php if ( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<?php if ( count($this->navigationGeneral) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
  </div>
<?php endif; ?>
<?php if ( !empty($this->tempReportForm) ): ?>
  <div class='tabs'>
    <ul class="navigation">
      <li class="<?php echo empty($this->reportType) ? 'active' : '' ?>">
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'report', 'type' => 0), 'Order Wise Sales Report') ?>
      </li>
      <li class="<?php echo!empty($this->reportType) ? 'active' : '' ?>">
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'report', 'type' => 1), 'Ticket Wise Sales Report') ?>
      </li>
    </ul>
  </div>

  <div class="tip" id = 'tip' style='display:none;'>
    <span>
      <?php echo "There are no activities found in the selected date range." ?>
    </span>
  </div>
  <div class="tip" id ='tip2' style='display:none;'>
    <span>
      <?php echo "No orders have been placed on your site yet." ?>
    </span>
  </div>
  <br />
  <?php
  if ( !empty($this->reportType) ):
    $this->reportform->setTitle("Ticket Wise Sales Report");
    $this->reportform->setDescription("Here, you may view performance report of tickets sold from the events on your site. You can also view the performance of sales of any desired ticket from all or any desired events. Report can be viewed over multiple durations and time intervals. Reports can also be viewed for any desired order status. You can also export and save the report.");
  endif;
  ?>
  <div class="seaocore_settings_form">
    <div class="settings">
  <?php echo $this->reportform->render($this) ?>
    </div>
  </div>	
<?php endif; ?>