<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    send.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<h2>
    <?php echo $this->translate('Android Mobile Application') ?>
</h2>
<script type="text/javascript">

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

        $('to').disabled = false;
    }

    function removeToValue(id, toValueArray) {
        for (var i = 0; i < toValueArray.length; i++) {
            if (toValueArray[i] == id)
                toValueIndex = i;
        }

        toValueArray.splice(toValueIndex, 1);
        $('toValues').value = toValueArray.join();
        if (toValueArray.length == 0)
            $('toValues-wrapper').setStyle('display', 'none');
    }

    en4.core.runonce.add(function () {
        if (!isPopulated) { // NOT POPULATED
            new Autocompleter.Request.JSON('to', '<?php echo $this->url(array('module' => 'siteandroidapp', 'controller' => 'push-notification', 'action' => 'suggest'), 'admin_default', true) ?>', {
                'minLength': 1,
                'delay': 250,
                'selectMode': 'pick',
                'autocompleteType': 'message',
                'multiple': false,
                'className': 'seaocore-autosuggest',
                'filterSubset': true,
                'tokenFormat': 'object',
                'tokenValueKey': 'label',
                'injectChoice': function (token) {
                    if (token.type == 'user') {
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
                    }
                    else {
                        new Element('div', {
                            'html': this.markQueryValue(token.label),
                            'class': 'autocompleter-choice'
                        }).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }

                },
                onPush: function () {
                    if ($('toValues').value.split(',').length >= maxRecipients) {
                        $('to').disabled = true;
                    }
                }
            });

            new Composer.OverText($('to'), {
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

        } else { // POPULATED

            var myElement = new Element("span", {
                'id': 'tospan' + to.id,
                'class': 'tag tag_' + to.type,
                'html': to.title /* + ' <a href="javascript:void(0);" ' +
                 'onclick="this.parentNode.destroy();removeFromToValue("' + toID + '");">x</a>"' */
            });
            $('to-element').appendChild(myElement);
            $('to-wrapper').setStyle('height', 'auto');

            // Hide to input?
            $('to').setStyle('display', 'none');
            $('toValues-wrapper').setStyle('display', 'none');
        }
    });
</script>
<script type="text/javascript">
    window.addEvent('domready', function () {
        hide_Others();
    });
    function hide_Others()
    {
        $('toValues-wrapper').setStyle('display', 'none');
        if ($('send_to').value == 'network') {
            document.getElementById('network-wrapper').style.display = 'block';
            document.getElementById('member_level-wrapper').style.display = 'none';
            document.getElementById('to-wrapper').style.display = 'none';

        } else if ($('send_to').value == 'member_level') {
            document.getElementById('member_level-wrapper').style.display = 'block';
            document.getElementById('to-wrapper').style.display = 'none';
            document.getElementById('network-wrapper').style.display = 'none';

        } else if ($('send_to').value == 'specific_user') {
            document.getElementById('member_level-wrapper').style.display = 'none';
            document.getElementById('to-wrapper').style.display = 'block';
            $('toValues-wrapper').setStyle('display', 'block');
            document.getElementById('network-wrapper').style.display = 'none';

        } else {
            document.getElementById('network-wrapper').style.display = 'none';
            document.getElementById('member_level-wrapper').style.display = 'none';
            document.getElementById('to-wrapper').style.display = 'none';
        }
    }
</script>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<?php
$this->form->setDescription('Using this form, you will be able to send a push notification out to your members by choosing robust targeting options for who should receive your push notification based on "Member Levels" and "Networks", depending on which of these are being used on your site. You can also choose to send push notification to particular members by choosing their names from the auto-suggest box.<br /><br />Note: The push notification title and message should both be short and crisp. Also, the push notification will only be sent to users using your Android App, and who have enabled push notifications.');
$this->form->getDecorator('Description')->setOption('escape', false);
?>


<div class='settings'>
    <?php echo $this->form->render($this); ?>
</div>
