<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: request-group.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
    var groupWidgetRequestSend = function(action, group_id, notification_id, user_id)
    {
        var friendUrl = '<?php echo $this->url(array('controller' => 'index', 'action' => 'approve'), 'sitegroupmember_approve', true) ?>';

                    $.ajax({
        url: friendUrl ,
        dataType: 'html',
        method: 'post',
        data: {
          format: 'html',
          member_id: user_id,
          group_id: group_id
        },
        error: function() {
//          $.mobile.activePage.find("#sitecoupan_show_content").css('display', 'none');
//          $.mobile.activePage.find("#sitecoupan_add_content").css('display', 'block');
//          $.mobile.activePage.find('#buttons-wrapper').css('display', 'block');
        },
        success: function(response, textStatus, xhr) {
    
    
    $('#group-widget-approve-' + notification_id).html("You have successfully approve this member.");
    
        }
        
        
                    });





    }
    
    function rejectmemberRequestSend (action, group_id, notification_id, member_id, user_id) {
			var friendUrl = '<?php echo $this->url(array('controller' => 'index', 'action' => 'reject'), 'sitegroupmember_approve', true) ?>';

                         $.ajax({
        url: friendUrl ,
        dataType: 'html',
        data: {
          format: 'html',
          member_id: user_id,
          group_id: group_id
        },
        error: function() {
//          $.mobile.activePage.find("#sitecoupan_show_content").css('display', 'none');
//          $.mobile.activePage.find("#sitecoupan_add_content").css('display', 'block');
//          $.mobile.activePage.find('#buttons-wrapper').css('display', 'block');
        },
        success: function(response, textStatus, xhr) {
    
    
    $('#group-widget-approve-' + notification_id).html("You have ignored the invite to the group.");
    
        }
        
        
                    });





		}
    
</script>

<li id="group-widget-approve-<?php echo $this->notification->notification_id ?>">
	<div class="ui-btn">
    <?php echo $this->itemPhoto($this->notification->getObject(), 'thumb.icon') ?>
    <h3>
        <?php echo $this->translate('%1$s has requested to join the group %2$s.', $this->htmlLink($this->notification->getSubject()->getHref(array()), $this->notification->getSubject()->getTitle()), $this->htmlLink($this->notification->getObject()->getHref(array()), $this->notification->getObject()->getTitle())); ?>
    </h3>
    <p>
        <a href="javascript:void(0);" onclick='groupWidgetRequestSend("approve", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>, <?php echo $this->member_id ?>)'>
            <?php echo $this->translate('Approve Request'); ?>
        </a>
        <?php echo $this->translate('or'); ?>
        <a href="javascript:void(0);" onclick='rejectmemberRequestSend("reject", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>, <?php echo $this->member_id ?>, <?php echo $this->notification->getSubject()->getIdentity();?>)'>
            <?php echo $this->translate('Ignore Request'); ?>
        </a>
    </p>
  </div>
</li>