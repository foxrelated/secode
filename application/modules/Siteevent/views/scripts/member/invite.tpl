<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: invite.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->count > 0 || $this->isLeader): ?>
    <?php
    $this->headLink()
            ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteeventprofile.css')
    ?>

    <?php
//put event admin check here.
    if ($this->isLeader) {
        $this->headScript()
                ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
                ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
                ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
                ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
    }
    ?>
    <script type="text/javascript">
        en4.core.runonce.add(function() {
            if ($('selectall'))
                $('selectall').addEvent('click', function(event) {
                    var el = $(event.target);
                    $$('input[type=checkbox]').set('checked', el.get('checked'));
                });
        });
    </script>

    <script type="text/javascript">

        window.addEvent('domready', function() {
            if ($('user_ids')) {
                $('toValues-wrapper').style.display = 'none';
            }
        });

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

            $('user_ids').disabled = false;
        }

        function removeToValue(id, toValueArray) {
            for (var i = 0; i < toValueArray.length; i++) {
                if (toValueArray[i] == id)
                    toValueIndex = i;
            }

            toValueArray.splice(toValueIndex, 1);
            $('toValues').value = toValueArray.join();
        }

        en4.core.runonce.add(function() {
            if ($('user_ids')) {
                new Autocompleter.Request.JSON('user_ids', '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'member', 'action' => 'getmembers', 'event_id' => $this->event->event_id, 'occurrence_id' => $this->occurrence_id), 'default', true) ?>', {
                    'postVar': 'user_ids',
                    'postData': false,
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

                        //if(token.type == 'sitepage'){
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
                            $('user_ids').disabled = true;
                        }
                    }
                });
                new Composer.OverText($('user_ids'), {
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
            }
        });

    </script>
    <?php echo $this->form->setAttrib('class', 'global_form_popup siteevent_invite_friend_popup')->render($this) ?>
<?php else: ?>
    <div class="global_form_popup">
        <?php echo $this->translate('You have no friends to invite.'); ?>
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Close'), array('onclick' => 'parent.Smoothbox.close();')) ?>
    </div>
<?php endif; ?>