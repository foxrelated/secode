<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: compose.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');

$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js');
?>

<script type="text/javascript">

    en4.core.runonce.add(function() {
        $('toValues-wrapper').style.display = 'none';
        selectGuestsType(0);
    });

    function selectGuestsType(options) { 
       if($('searchGuests-wrapper')) { 
        if (options == 4) {
             $('searchGuests-wrapper').style.display = 'block';
         }
         else {
             $('searchGuests-wrapper').style.display = 'none';
         }
       }
    }

    // Populate data
    var maxRecipients = <?php echo sprintf("%d", $this->maxRecipients) ?> || 10;
    var to = {
        id: false,
        type: false,
        guid: false,
        title: false
    };
    var isPopulated = false;

    <?php if (!empty($this->isPopulated) && !empty($this->toObject)): ?>
        isPopulated = true;
        to = {
            id: <?php echo sprintf("%d", $this->toObject->getIdentity()) ?>,
            type: '<?php echo $this->toObject->getType() ?>',
            guid: '<?php echo $this->toObject->getGuid() ?>',
            title: '<?php echo $this->string()->escapeJavascript($this->toObject->getTitle()) ?>'
        };
    <?php endif; ?>

    function removeFromToValue(id) {
        // code to change the values in the hidden field to have updated values
        // when recipients are removed.
        var toValues = $('toValues').value;
        var toValueArray = toValues.split(",");
        var toValueIndex = "";

        var checkMulti = id.search(/,/);
        if (toValueArray.length == 1) {
            $('toValues-wrapper').style.display = 'none';
        }
        // check if we are removing multiple recipients
        if (checkMulti != -1) {
            var recipientsArray = id.split(",");
            for (var i = 0; i < recipientsArray.length; i++) {
                removeToValue(recipientsArray[i], toValueArray);
            }
        }
        else {
            removeToValue(id, toValueArray);
        }

        // hide the wrapper for usernames if it is empty
        if ($('toValues').value == "") {
            $('toValues-wrapper').setStyle('height', '0');
        }

        $('searchGuests').disabled = false;
    }

    function removeToValue(id, toValueArray) {
        for (var i = 0; i < toValueArray.length; i++) {
            if (toValueArray[i] == id)
                toValueIndex = i;
        }

        toValueArray.splice(toValueIndex, 1);
        $('toValues').value = toValueArray.join();
    }
    var messageAutocomplete;
    var msgoccurr_id;
    en4.core.runonce.add(function() {
        if ($('filter_occurrence_date') != null && typeof ($('filter_occurrence_date')) != 'undefined')
            msgoccurr_id = $('filter_occurrence_date').value;
        else
            msgoccurr_id = 'all';

        // if( !isPopulated ) { 
        messageAutocomplete = new Autocompleter.Request.JSON('searchGuests', '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'member', 'action' => 'get-guests', 'event_id' => $this->event->event_id), 'default', true) ?>', {
            'postVar': 'searchGuests',
            'postData': {'occurrence_id': msgoccurr_id},
            'minLength': 1,
            'delay': 250,
            'selectMode': 'pick',
            'element': 'toValues',
            'autocompleteType': 'message',
            'multiple': false,
            'className': 'seaocore-autosuggest tag-autosuggest',
            'filterSubset': true,
            'tokenFormat': 'object',
            'tokenValueKey': 'label',
            'injectChoice': function(token) {

                //if(token.type == 'siteevent'){
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
            onCommand: function(e) {
                //this.options.postData = { 'couponId' : $('coupon_id').value };
            },
            onPush: function() {
                if ($('toValues-wrapper')) {
                    $('toValues-wrapper').style.display = 'block';
                }
                if ($('toValues').value.split(',').length >= maxRecipients) {
                    $('searchGuests').disabled = true;
                }
            }
        });
        new Composer.OverText($('searchGuests'), {
            'textOverride': '<?php echo $this->translate('Start typing...') ?>',
            'element': 'label',
            'isPlainText': true,
            'positionOptions': {
                position: (en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft'),
                edge: (en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft'),
                offset: {
                    x: (en4.orientation == 'rtl' ? -4 : 4),
                    y: 2
                }
            }
        });
    });

    var composeInstance;
    en4.core.runonce.add(function() {
        var tel = new Element('div', {
            'id': 'compose-tray',
            'styles': {
                'display': 'none'
            }
        }).inject($('submit'), 'before');

        var mel = new Element('div', {
            'id': 'compose-menu'
        }).inject($('submit'), 'after');

            if ( '<?php 
         $id = Engine_Api::_()->user()->getViewer()->level_id;
         echo Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('messages', $id, 'editor');
         ?>' == 'plaintext' ) {
      if( !Browser.Engine.trident && !DetectMobileQuick() && !DetectIpad() ) {
        composeInstance = new Composer('body', {
          overText : false,
          menuElement : mel,
          trayElement: tel,
          baseHref : '<?php echo $this->baseUrl() ?>',
          hideSubmitOnBlur : false,
          allowEmptyWithAttachment : false,
          submitElement: 'submit',
          type: 'message'
        });
      }
    }

        $('messages_compose').addEvent('submit', function(event) {
            if ($('guests')) {
                if ($('guests').value == 4 && $('toValues').value == '')
                    event.stop();
            }
        });
    });

    var checkGuests = function() {
        $('guests-wrapper').getAllNext().setStyle('display', 'none');
        $('siteeventmsg_form_popup').innerHTML = '';
        $('siteeventmsg_form_popup').inject($('submit-wrapper'), 'after');
        $('siteeventmsg_form_popup').addClass('seaocore_loading_image');
        $('siteeventmsg_form_popup').setStyle('display', 'block');
        en4.core.request.send(new Request.JSON({
            'url': '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'member', 'action' => 'get-member-count'), 'default', true) ?>',
            'data': {
                'format': 'json',
                event_id: '<?php echo $this->event->event_id; ?>',
                'occurrence_id': $('filter_occurrence_date') != null ? $('filter_occurrence_date').value : 'all',
                'rsvp': $('guests').value,
            },
            onComplete: function(responseJSON, responseText) {
                $('siteeventmsg_form_popup').removeClass('seaocore_loading_image');
                if (!responseJSON.member_count) {
                    if ($('guests').value == 3 && $('filter_occurrence_date-wrapper')) {
                        $('filter_occurrence_date-wrapper').getAllNext().setStyle('display', 'none');
                    }
                    else
                        $('guests-wrapper').getAllNext().setStyle('display', 'none');

                    $('siteeventmsg_form_popup').innerHTML = '<ul class="form-errors errors" id="guestlist_error"><li>' + '<?php echo Engine_Api::_()->siteevent()->isTicketBasedEvent() ? $this->translate('You do not have any member that match your search criteria.') : $this->translate('You do not have any guest that match your search criteria.'); ?>' + '</li></ul>';
                    $('siteeventmsg_form_popup').setStyle('display', 'block');
                } else {
                    if ($('filter_occurrence_date-wrapper'))
                        $('filter_occurrence_date-wrapper').getAllNext().setStyle('display', 'block');
                    else
                        $('guests-wrapper').getAllNext().setStyle('display', 'block');
                    if ($('guests').value != 4) {
                        $('searchGuests-wrapper').setStyle('display', 'none');
                        if($('toValues-wrapper'))
                          $('toValues-wrapper').setStyle('display', 'none');
                    }
                }
            }

        }));
    }
</script>

<?php foreach ($this->composePartials as $partial): ?>
    <?php echo $this->partial($partial[0], $partial[1]) ?>
<?php endforeach; ?>

<div class="siteevent_form_popup" >
    <?php echo $this->form->render($this) ?>
    <div id="siteeventmsg_form_popup"></div>
</div>
