<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: request-member.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var groupWidgetRequestSend = function(action, group_id, notification_id)
  {
    var url;
    if( action == 'accept' )
    {
      url = '<?php echo $this->url(array('controller' => 'member', 'action' => 'accept'), 'sitegroup_profilegroupmember', true) ?>';
    }
    else if( action == 'reject' )
    {
      url = '<?php echo $this->url(array('controller' => 'member', 'action' => 'reject'), 'sitegroup_profilegroupmember', true) ?>';
    }
    else
    {
      return false;
    }

    (new Request.JSON({
      'url' : url,
      'data' : {
        'group_id' : group_id,
        'format' : 'json'
        //'token' : '<?php //echo $this->token() ?>'
      },
      'onSuccess' : function(responseJSON)
      {
        if( !responseJSON.status )
        {
          $('sitegroupmember-widget-request-' + notification_id).innerHTML = responseJSON.error;
        }
        else
        {
          $('sitegroupmember-widget-request-' + notification_id).innerHTML = responseJSON.message;
        }
      }
    })).send();
  }
</script>

<li id="sitegroupmember-widget-request-<?php echo $this->notification->notification_id ?>">
  <?php echo $this->itemPhoto($this->notification->getObject(), 'thumb.icon') ?>
  <div>
    <div>
      <?php if($this->notification->type == 'sitegroupmember_invite'):?>  
      <?php echo $this->translate('%1$s has invited you to the group %2$s', $this->htmlLink($this->notification->getSubject()->getHref(), $this->notification->getSubject()->getTitle()), $this->htmlLink($this->notification->getObject()->getHref(), $this->notification->getObject()->getTitle())); ?>
      <?php elseif($this->notification->type == 'sitegroupmember_approve'):?>
       <?php echo $this->translate('%1$s has requested to join the group %2$s', $this->htmlLink($this->notification->getSubject()->getHref(), $this->notification->getSubject()->getTitle()), $this->htmlLink($this->notification->getObject()->getHref(), $this->notification->getObject()->getTitle())); ?>
      <?php endif;?> 
    </div>
    <div>
        
      <?php if($this->notification->type == 'sitegroupmember_invite'):?>    
        <button type="submit" onclick='groupWidgetRequestSend("accept", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>)'>
          <?php echo $this->translate('Accept Invite');?>
        </button>
      <?php elseif($this->notification->type == 'sitegroupmember_approve'):?>   
         <button type="submit" onclick='groupWidgetRequestSend("accept", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>)'>
          <?php echo $this->translate('Request Invite');?>
        </button>
      <?php endif;?> 
      <?php echo $this->translate('or');?>
      <a href="javascript:void(0);" onclick='groupWidgetRequestSend("reject", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>)'>
        <?php echo $this->translate('ignore request');?>
      </a>
    </div>
  </div>
</li>
