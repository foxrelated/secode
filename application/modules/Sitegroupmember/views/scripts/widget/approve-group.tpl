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
			  en4.core.request.send(new Request.HTML({
				url : friendUrl,
				data : {
					format: 'html',
					member_id: user_id,
					group_id: group_id
				},
				'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
				{
            $('group-widget-approve-' + notification_id).innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("You have successfully approve this member.")); ?>';
        }
			}));  
    }
    
    function rejectmemberRequestSend (action, group_id, notification_id, member_id, user_id) {
			var friendUrl = '<?php echo $this->url(array('controller' => 'index', 'action' => 'reject'), 'sitegroupmember_approve', true) ?>';
			en4.core.request.send(new Request.HTML({
				url : friendUrl,
				data : {
					format: 'html',
					member_id: member_id,
					group_id:group_id,
          'user_id':user_id
				},
				'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript) { 
					$('group-widget-approve-' + notification_id).innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("You have ignored the invite to the group.")); ?>';
					//alert(document.getElementById('members_results_friend').getChildren().length);
				}
			}));
		}
    
</script>

<li id="group-widget-approve-<?php echo $this->notification->notification_id ?>">
    <?php echo $this->itemPhoto($this->notification->getObject(), 'thumb.icon') ?>
    <div>
        
            <div>
                <?php echo $this->translate('%1$s has requested to join the group %2$s.', $this->htmlLink($this->notification->getSubject()->getHref(array()), $this->notification->getSubject()->getTitle()), $this->htmlLink($this->notification->getObject()->getHref(array()), $this->notification->getObject()->getTitle())); ?>
            </div>
        <div>
            <button type="submit" onclick='groupWidgetRequestSend("approve", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>, <?php echo $this->member_id ?>)'>
                <?php echo $this->translate('APPROVE REQUEST'); ?>
            </button>
            <?php echo $this->translate('or'); ?>
            <a href="javascript:void(0);" onclick='rejectmemberRequestSend("reject", <?php echo $this->string()->escapeJavascript($this->notification->getObject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>, <?php echo $this->member_id ?>, <?php echo $this->notification->getSubject()->getIdentity();?>)'>
                <?php echo $this->translate('ignore request'); ?>
            </a>
        </div>
    </div>
</li>