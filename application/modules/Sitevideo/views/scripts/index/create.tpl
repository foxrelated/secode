<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
?>
<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
));
?>
<script type="text/javascript">

    window.addEvent('domready', function () {
        var e4 = $('channel_url_msg-wrapper');
        $('channel_url_msg-wrapper').setStyle('display', 'none');

        var channelurlcontainer = $('channel_url-element');
        var language = '<?php echo $this->string()->escapeJavascript($this->translate('Check Availability')) ?>';
        var newdiv = document.createElement('div');
        newdiv.id = 'url_varify';
        newdiv.innerHTML = "<a href='javascript:void(0);'  name='check_availability' id='check_availability' onclick='ChannelUrlBlur();return false;' class='check_availability_button'>" + language + "</a> <br />";

        channelurlcontainer.insertBefore(newdiv, channelurlcontainer.childNodes[2]);
    });

    function ChannelUrlBlur(channel_id) {
        if ($('channel_url_alert') == null) {
            var channelurlcontainer = $('channel_url-element');
            var newdiv = document.createElement('span');
            newdiv.id = 'channel_url_alert';
            newdiv.innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitevideo/externals/images/loading.gif" />';
            channelurlcontainer.insertBefore(newdiv, channelurlcontainer.childNodes[3]);
        }
        else {
            $('channel_url_alert').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitevideo/externals/images/loading.gif" />';
        }
        //alert($('channel_url').value);
        var channel_url = $('channel_url').value;
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'sitevideo/index/channelurlvalidation',
            method: 'get',
            data: {
                channel_url: channel_url, check_url: 1,
                channel_id: channel_id,
                format: 'html'
            },
            onSuccess: function (responseJSON) {
                if (responseJSON.success == 0) {
                    $('channel_url_alert').innerHTML = responseJSON.error_msg;
                    if ($('channel_url_alert')) {
                        $('channel_url_alert').innerHTML = responseJSON.error_msg;
                    }
                }
                else {
                    $('channel_url_alert').innerHTML = responseJSON.success_msg;
                    if ($('channel_url_alert')) {
                        $('channel_url_alert').innerHTML = responseJSON.success_msg;
                    }
                }
            }
        }));
    }


<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.tags.enabled', 1)): ?>
        en4.core.runonce.add(function ()
        {
            new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest', 'resourceType' => 'sitevideo_channel'), 'default', true) ?>', {
                'postVar': 'text',
                'minLength': 1,
                'selectMode': 'pick',
                'autocompleteType': 'tag',
                'className': 'tag-autosuggest',
                'customChoices': true,
                'filterSubset': true, 'multiple': true,
                'injectChoice': function (token) {
                    var choice = new Element('li', {'class': 'autocompleter-choices', 'value': token.label, 'id': token.id});
                    new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
                    choice.inputValue = token;
                    this.addChoiceEvents(choice).inject(this.choices);
                    choice.channel('autocompleteChoice', token);
                }
            });
        });
<?php endif; ?>

    var getProfileType = function (category_id) {
        var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getMapping(array('category_id', 'profile_type'))); ?>;
        for (i = 0; i < mapping.length; i++) {
            if (mapping[i].category_id == category_id)
                return mapping[i].profile_type;
        }
        return 0;
    }
    en4.core.runonce.add(function () {
        var defaultProfileId = '<?php echo '0_0_' . $this->defaultProfileId ?>' + '-wrapper';
        if ($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
            $(defaultProfileId).setStyle('display', 'none');
        }
    });
</script>
<?php echo $this->form->render($this) ?>
