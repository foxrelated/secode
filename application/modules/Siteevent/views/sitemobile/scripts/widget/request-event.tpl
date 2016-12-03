<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: request-event.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
    var eventWidgetRequestSend = function (action, event_id, notification_id, rsvp)
    {
        var url;
        if (action == 'accept')
        {
            url = "<?php echo $this->url(array('controller' => 'member', 'action' => 'accept', 'occurrence_id' => $this->occurrence_id), 'siteevent_extended', true) ?>";
        }
        else if (action == 'reject')
        {
            url = "<?php echo $this->url(array('controller' => 'member', 'action' => 'reject', 'occurrence_id' => $this->occurrence_id), 'siteevent_extended', true) ?>";
        }
        else
        {
            return false;
        }


        $.ajax({
            url: url,
            dataType: 'json',
            data: {
                format: 'json',
                'method': 'post',
                'event_id': event_id,
                'rsvp': rsvp,
                'isAjax': 1
            },
            error: function () {
            },
            success: function (responseJSON) {
                if (!responseJSON.status)
                {
                    $('#event-widget-request-' + notification_id).html(responseJSON.error);
                }
                else
                {
                    $('#event-widget-request-' + notification_id).html(responseJSON.message);
                }
            }
        });

    }
</script>

<li id="event-widget-request-<?php echo $this->notification->notification_id ?>">

    <div class="ui-btn">
        <?php echo $this->itemPhoto($this->notification->getObject(), 'thumb.icon') ?>
        <?php if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite')): ?>
            <h3>
                <?php echo $this->translate('%1$s has invited you to the event %2$s', $this->htmlLink($this->notification->getSubject()->getHref(array('occurrence_id' => $this->occurrence_id)), $this->notification->getSubject()->getTitle()), $this->htmlLink($this->notification->getObject()->getHref(array('occurrence_id' => $this->occurrence_id)), $this->notification->getObject()->getTitle())); ?>
            </h3>
        <?php else : ?>
            <h3>
                <?php echo $this->translate('%1$s has invited you to the event %2$s', $this->htmlLink($this->notification->getSubject()->getHref(array('occurrence_id' => $this->occurrence_id)), $this->notification->getSubject()->getTitle()), $this->htmlLink($this->notification->getObject()->getHref(array('occurrence_id' => $this->occurrence_id)), $this->notification->getObject()->getTitle())); ?>
            </h3>
        <?php endif; ?>
        <p>
            <a href="javascript:void(0);" onclick='eventWidgetRequestSend("accept", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>, 2)'>
                <?php echo $this->translate('Attending'); ?>
            </a>
            <a href="javascript:void(0);" onclick='eventWidgetRequestSend("accept", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>, 1)'>
                <?php echo $this->translate('Maybe Attending'); ?>
            </a>
            <a  href="javascript:void(0);" onclick='eventWidgetRequestSend("accept", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>, 0)'>
                <?php echo $this->translate('Not Attending'); ?>
            </a>
            <?php echo $this->translate('or'); ?>
            <a href="javascript:void(0);" onclick='eventWidgetRequestSend("reject", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>)'>
                <?php echo $this->translate('ignore request'); ?>
            </a>
        </p>
    </div>
</li>
