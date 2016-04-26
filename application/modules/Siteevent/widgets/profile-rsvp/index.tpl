<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Siteevent/externals/scripts/core.js'); ?>
<?php if ($this->viewer_id): ?>

    <script type="text/javascript">
        en4.core.runonce.add(function() {
            $$('#rsvp_options input[type=radio]').addEvent('click', function() {
                var option_id = this.get('value');
                
                if(option_id == 2) {
                    isEventFull(<?php echo $this->occurrence_id; ?>, selectRSVP, {rsvp:option_id});
                }
                else {
                    selectRSVP({rsvp:option_id});
                }
            });
        });
        
        function selectRSVP(options) {
            var option_id = options.rsvp;
            $('siteevent_radio_' + option_id).className = 'siteevent_radio_loading';
            new Request.JSON({
                url: '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'widget', 'action' => 'profile-rsvp', 'subject' => $this->subject()->getGuid()), 'default', true); ?>',
                method: 'post',
                data: {
                    format: 'json',
                    'event_id': <?php echo $this->subject()->event_id ?>,
                    'option_id': option_id,
                    occurrence_id: <?php echo $this->occurrence_id; ?>
                },
                onComplete: function(responseJSON, responseText)
                {
                    $('siteevent_radio_' + option_id).className = 'siteevent_radio';
                    $$('#rsvp_options input').each(function(radio) {
                        if (radio.type == 'radio') {
                            radio.style.display = null;
                            radio.blur();
                        }
                    });
                    if (responseJSON.error) {
                        alert(responseJSON.error);
                    } else {
                        <?php if (!$this->canChangeVote): ?>
                            $$('.poll_radio input').set('disabled', true);
                        <?php endif ?>
                    }
                }
            }).send(); 
        }
        
        var refreshEventStats = function() {
            new Request.HTML({
                method: 'get',
                url: '<?php echo $this->url(Array('module' => 'siteevent', 'controller' => 'widget', 'action' => 'information-siteevent', 'subject' => $this->subject()->getGuid(), 'format' => 'html'), 'default', true); ?>',
                data: {
                },
                onSuccess: function(responseJSON, responseText, responseHTML, responseJavaScript) {
                    $('siteevent_profile_index-main-left-siteevent_information_siteevent').innerHTML = responseHTML;
                }
            }).send();
        }
    </script>

    <h3>
        <?php echo $this->translate('Your RSVP'); ?>
    </h3>
    <form class="siteevent_rsvp_form siteevent_side_widget" action="<?php echo $this->url() ?>" method="post" onsubmit="return false;">
        <div class="siteevents_rsvp" id="rsvp_options">
            <div class="siteevent_radio" id="siteevent_radio_2">
                <input id="rsvp_option_2" type="radio" class="rsvp_option" name="rsvp_options" <?php if ($this->rsvp == 2): ?>checked="true"<?php endif; ?> value="2" /><?php echo $this->translate('Attending'); ?>
            </div>
            <div class="siteevent_radio" id="siteevent_radio_1">
                <input id="rsvp_option_1" type="radio" class="rsvp_option" name="rsvp_options" <?php if ($this->rsvp == 1): ?>checked="true"<?php endif; ?> value="1" /><?php echo $this->translate('Maybe Attending'); ?>
            </div>
            <div class="siteevent_radio" id="siteevent_radio_0">
                <input id="rsvp_option_0" type="radio" class="rsvp_option" name="rsvp_options" <?php if ($this->rsvp == 0): ?>checked="true"<?php endif; ?> value="0" /><?php echo $this->translate('Not Attending'); ?>
            </div>
        </div>
    </form>

<?php endif; ?>
