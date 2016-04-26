<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');

$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css')
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteeventprofile.css');
?>
<?php $options = array('gridview' => $this->translate('Event Grid View')); ?>
<?php $options['title'] = $this->translate('Title'); ?>
<?php $options['desc'] = $this->translate('Description'); ?>
<?php if ($this->siteevent->venue_name): ?>
    <?php $options['venue'] = $this->translate('Venue Name'); ?>
<?php endif; ?>
<?php $options['starttime'] = $this->translate('Start Time'); ?>
<?php $options['endtime'] = $this->translate('End Time'); ?>
<?php if ($this->siteevent->location): ?>
    <?php $options['location'] = $this->translate('Location'); ?>
<?php endif; ?>
<?php if ($this->siteevent->host_type): ?>
    <?php $options['host'] = $this->translate('Host'); ?>
<?php endif; ?>
<?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>
    <?php $options['attending'] = $this->translate('Attending Count'); ?>
<?php endif; ?>
<?php $value = array_keys($options); ?>
<?php $background_color = '#FFFFFF'; ?>
<?php $border_color = '#eaeaea'; ?>
<?php $text_color = '#555'; ?>
<?php $link_color = '#5f93b4'; ?>
<div style="width: 800px;" class="siteevent_badge_create">
    <?php if (!$this->forEvenyOne): ?>
        <div class="tip">
            <span>
                <?php echo $this->translate("View privacy of this event is not Everyone, still it will be shown wherever this embedded code placed."); ?>
            </span>
        </div>
    <?php endif; ?>

    <div class='siteevent_badge_right'>
        <?php
        echo $this->partial('badge/index.tpl', 'siteevent', array(
            'siteevent' => $this->siteevent,
            'preview' => true,
            'options' => $value,
            'background_color' => $background_color,
            'border_color' => $border_color,
            'text_color' => $text_color,
            'link_color' => $link_color,
            'occurrence_id' => $this->occurrence_id
        ));
        ?>
    </div>

    <div class='siteevent_badge_left mbot10 o_hidden'>
        <h3><?php echo $this->translate("Choose the properties to show in event badge"); ?></h3>
        <form id="se_badge_create">
            <div class="fright siteevent_badge_theme_options">
                <div class="clr mbot5">
                    <label for="background_color" class="optional">
                        <?php echo $this->translate('Background Color') ?>
                    </label>
                    <input name="background_color" id="background_color" value= '<?php echo $background_color ?>' type="text" style="width:80px;">
                    <input name="backgroundColor" id="backgroundColor" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
                </div>
                <div class="clr mbot5">
                    <label for="border_color" class="optional">
                        <?php echo $this->translate('Border Color') ?>
                    </label>
                    <input name="border_color" id="border_color" value='<?php echo $border_color ?>' type="text" style="width:80px;">
                    <input name="borderColor" id="borderColor" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
                </div>
                <div class="clr mbot5">
                    <label for="text_color" class="optional">
                        <?php echo $this->translate('Text Color') ?>
                    </label>
                    <input name="text_color" id="text_color" value= '<?php echo $text_color ?>' type="text" style="width:80px;">
                    <input name="textColor" id="textColor" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
                </div>
                <div class="clr">
                    <label for="link_color" class="optional">
                        <?php echo $this->translate('Link Color') ?>
                    </label>
                    <input name="link_color" id="link_color" value= '<?php echo $link_color ?>' type="text" style="width:80px;">
                    <input name="linkColor" id="linkColor" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
                </div>
            </div>
            <div class="o_hidden siteevent_badge_info_options">
                <?php echo $this->formMultiCheckbox('options', $value, array('onchange' => 'sebadgechange();'), $options, ''); ?>
            </div>
        </form>
    </div>

    <div class="siteevent_badge_code">
        <h3><?php echo $this->translate("Embedded Code") ?></h3>
        <textarea readonly="readonly" class="siteevent_badge_box_code" id="siteevent_badge_code_box" rows="7"></textarea>
    </div>
    <div class="txt_center clr mtop10 o_hidden">
        <a href="javascript:void(0);" onclick="parent.Smoothbox.close();" class="siteevent_buttonlink" style="display:inline-block;">
            <?php echo $this->translate("close") ?>     </a>
    </div>
</div>
<script type="text/javascript">
            function sebadgechange() {

                if ($('options-gridview').checked) {
                    $$('.se_badge').removeClass('se_badge_listview');
                    $$('.se_badge').addClass('se_badge_gridview');
                    $$('.siteevent_badge_left').setStyles({
                        'width': 800 - 30 - $$('.siteevent_badge_right')[0].clientWidth
                    });
                    $$('.siteevent_badge_code').setStyles({
                        'width': $$('.siteevent_badge_left')[0].clientWidth
                    });
                } else {
                    $$('.se_badge').removeClass('se_badge_gridview');
                    $$('.se_badge').addClass('se_badge_listview');
                    $$('.siteevent_badge_left').setStyles({
                        'width': 800 - 30 - $$('.siteevent_badge_right')[0].clientWidth
                    });
                    $$('.siteevent_badge_code').setStyles({
                        'width': 775
                    });
                }

                if ($('options-title').checked) {
                    $$('.se_b_title').show();
                } else {
                    $$('.se_b_title').hide();
                }

                if ($('options-desc').checked) {
                    $$('.se_b_desc').show();
                } else {
                    $$('.se_b_desc').hide();
                }
                if ($('options-venue')) {
                    if ($('options-venue').checked) {
                        $$('.se_b_vanue').show();
                    } else {
                        $$('.se_b_vanue').hide();
                    }
                }
                if ($('options-location')) {
                    if ($('options-location').checked) {
                        $$('.se_b_location').show();
                    } else {
                        $$('.se_b_location').hide();
                    }
                }

                if ($('options-starttime')) {
                    if ($('options-starttime').checked) {
                        $$('.se_b_starttime').show();
                    } else {
                        $$('.se_b_starttime').hide();
                    }
                }
                if ($('options-endtime')) {
                    if ($('options-endtime').checked) {
                        $$('.se_b_endtime').show();
                    } else {
                        $$('.se_b_endtime').hide();
                    }
                }
                if ($('options-host')) {
                    if ($('options-host').checked) {
                        $$('.se_b_host_title').show();
                    } else {
                        $$('.se_b_host_title').hide();
                    }
                }
                if($('options-attending')) {
                    if ($('options-attending').checked) {
                        $$('.se_b_attending').show();
                    } else {
                        $$('.se_b_attending').hide();
                    }
                }
                $$('.se_badge').setStyles({
                    'color': $('text_color').value,
                    'borderColor': $('border_color').value,
                    'backgroundColor': $('background_color').value
                });

                $$('.se_badge a').setStyles({
                    'color': $('link_color').value
                });
                $$('.se_badge div').setStyles({
                    'color': $('text_color').value
                });
                var iframeUrl = '<?php echo (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'index', 'controller' => 'badge', 'event_id' => $this->siteevent->event_id), 'siteevent_extended', true); ?>';

                var query = $('se_badge_create').toQueryString();
                var width = '225', height = '225';
                width = $$('.se_badge')[0].clientWidth + 10;
                height = $$('.se_badge')[0].clientHeight + 50;
                var code = '<iframe src="' + iframeUrl + "?" + query + '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:' + width + 'px; height:' + height + 'px;" allowTransparency="true" ></iframe>';
                $('siteevent_badge_code_box').value = code;
                $('siteevent_badge_code_box').select();
            }
            en4.core.runonce.add(function() {
                sebadgechange();
                var s = new MooRainbow('textColor', {
                    id: 'rainbow_text',
                    'startColor': hexcolorTonumbercolor("#5F93B4"),
                    'onChange': function(color) {
                        $('text_color').value = color.hex;
                        sebadgechange();
                    }
                });
                var s = new MooRainbow('linkColor', {
                    id: 'rainbow_link',
                    'startColor': hexcolorTonumbercolor("#4E81A1"),
                    'onChange': function(color) {
                        $('link_color').value = color.hex;
                        sebadgechange();
                    }
                });
                var s = new MooRainbow('backgroundColor', {
                    id: 'rainbow_background',
                    'startColor': hexcolorTonumbercolor("#FFFFFF"),
                    'onChange': function(color) {
                        $('background_color').value = color.hex;
                        sebadgechange();
                    }
                });
                var s = new MooRainbow('borderColor', {
                    id: 'rainbow_border',
                    'startColor': hexcolorTonumbercolor("#000000"),
                    'onChange': function(color) {
                        $('border_color').value = color.hex;
                        sebadgechange();
                    }
                });
            });
</script>