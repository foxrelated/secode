<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _salesReport.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="layout_middle">
  <?php
  $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.Local.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/autocompleter/SEAOAutocompleter.Request.js');
  ?>

  <div class="siteevent_dashboard_content">
    <div id="show_tab_content">

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
        en4.core.runonce.add(function () {

          eventidsAutocomplete = new SEAOAutocompleter.Request.JSON('event_name', '<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'report', 'action' => 'suggestevents'), 'default', true) ?>', {
            'postVar': 'search',
            'postData': {'event_ids': $('event_ids').value},
            'minLength': 1,
            'delay': 250,
            'selectMode': 'pick',
            'elementValues': 'event_ids',
            'autocompleteType': 'message',
            'multiple': true,
            'className': 'tag-autosuggest seaocore-autosuggest',
            'filterSubset': true,
            'tokenFormat': 'object',
            'tokenValueKey': 'label',
            'injectChoice': function (token) {
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
            onPush: function () {
              if ($('event_ids-wrapper')) {
                $('event_ids-wrapper').style.display = 'block';
              }

              if ($(this.options.elementValues).value.split(',').length >= maxRecipients) {
                this.element.disabled = true;
              }
              eventidsAutocomplete.setOptions({
                'postData': {'event_ids': $('event_ids').value}
              });
              ticketidsAutocomplete.setOptions({
                'postData': {'event_ids': $('event_ids').value, 'select_event': $('select_event').value, 'ticket_ids': $('ticket_ids').value}
              });
            }
          });


          ticketidsAutocomplete = new SEAOAutocompleter.Request.JSON('ticket_name', '<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'report', 'action' => 'suggesttickets'), 'default', true) ?>', {
            'postVar': 'search',
            'postData': {'event_id': <?php echo $this->event_id ?>, 'event_ids': $('event_ids').value, 'select_event': $('select_event').value, 'ticket_ids': $('ticket_ids').value},
            'minLength': 1,
            'delay': 250,
            'selectMode': 'pick',
            'elementValues': 'ticket_ids',
            'autocompleteType': 'message',
            'multiple': true,
            'className': 'tag-autosuggest seaocore-autosuggest',
            'filterSubset': true,
            'tokenFormat': 'object',
            'tokenValueKey': 'label',
            'injectChoice': function (token) {
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
            onPush: function () {
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

        en4.core.runonce.add(function ()
        {

          monthList = [];

          if ($('start_cal-minute'))
            $('start_cal-minute').style.display = 'none';
          if ($('start_cal-ampm'))
            $('start_cal-ampm').style.display = 'none';
          if ($('start_cal-hour'))
            $('start_cal-hour').style.display = 'none';
          if ($('end_cal-minute'))
            $('end_cal-minute').style.display = 'none';
          if ($('end_cal-ampm'))
            $('end_cal-ampm').style.display = 'none';
          if ($('end_cal-hour'))
            $('end_cal-hour').style.display = 'none';
          if ($('event_name-wrapper'))
            $('event_name-wrapper').style.display = 'none';

          var empty = '<?php echo $this->empty; ?>';
          var no_ads = '<?php echo $this->no_ads ?>';

          form = $('event_report_form');
          form.setAttribute("method", "get");
          var e3 = $('ticket_name-wrapper');
          e3.setStyle('display', 'none');

          var e4 = $('event_name-wrapper');
          e4.setStyle('display', 'none');

          var e5 = $('select_ticket-wrapper');
          e5.setStyle('display', 'none');

          var e6 = $('event_ids-wrapper');
          e6.setStyle('display', 'none');

          var e7 = $('ticket_ids-wrapper');
          e7.setStyle('display', 'none');

          oneventChange($('select_event'));
          onticketChange($('select_ticket'));
          onChangeTime($('time_summary'));
          onchangeFormat($('format_report'));

          // display message tip
          if (empty == 1) {
            if (no_ads == 1) {
              $('tip2').style.display = 'block';
            } else {
              $('tip').style.display = 'block';
            }
          }

        });

        function oneventChange(formElement) {
          var e1 = formElement.value;
          if (e1 == 'all' || e1 == 'current_event')
          {
            $('event_name-wrapper').setStyle('display', 'none');

            if ($('event_ids').value)
            {
              $('event_ids').value = null;
              $('event_ids-element').getElements('.tag').destroy();

              eventidsAutocomplete.setOptions({
                'postData': {'event_ids': $('event_ids').value}
              });

              ticketidsAutocomplete.setOptions({
                'postData': {'event_ids': $('event_ids').value, 'select_event': $('select_event').value, 'ticket_ids': $('ticket_ids').value}
              });
            }

            if ($('ticket_ids').value)
            {
              $('ticket_ids').value = null;
              $('ticket_ids-element').getElements('.tag').destroy();

              ticketidsAutocomplete.setOptions({
                'postData': {'event_ids': $('event_ids').value, 'select_event': $('select_event').value, 'ticket_ids': $('ticket_ids').value}
              });
            }

          }
          else {
            $('event_name-wrapper').setStyle('display', 'block');

            if ($('ticket_ids').value)
            {
              $('ticket_ids').value = null;
              $('ticket_ids-element').getElements('.tag').destroy();

              ticketidsAutocomplete.setOptions({
                'postData': {'event_ids': $('event_ids').value, 'select_event': $('select_event').value, 'ticket_ids': $('ticket_ids').value}
              });
            }
          }
        }

        function onreportDependChange(formElement) {
          var e1 = formElement.value;
          if (e1 == 'order') {
            $('select_ticket-wrapper').setStyle('display', 'none');
            $('ticket_name-wrapper').setStyle('display', 'none');

          }
          else if (e1 == 'ticket') {
            $('select_ticket-wrapper').setStyle('display', 'block');
          }
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

          form = $('event_report_form');
          if (formElement.value == 1) {
            $('tip').style.display = 'none';
          }
        }

      </script>

      <div class="tip" id = 'tip' style='display:none;'>
        <span>
          <?php echo $this->translate("There are no activities found in the selected date range.") ?>
        </span>
      </div>
      <div class="tip" id ='tip2' style='display:none;'>
        <span>
          <?php echo $this->translate("No orders have been placed on your site yet.") ?>
        </span>
      </div>
      <br />
      <div>
        <?php echo $this->reportform->render($this) ?>
      </div>    
      <div id="report_loading_image"></div>
    </div> 
  </div> 
</div>
</div>