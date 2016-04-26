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
	function userWidgetRequestSend (member_id, group_id) 
	{
		var friendUrl = '<?php echo $this->url(array('controller' => 'index', 'action' => 'approve'), 'sitegroupmember_approve', true) ?>';
		$.ajax({
			url : friendUrl,
			type: "POST",
			dataType: "html",
			data : {
				format: 'html',
				member_id: member_id,
				group_id: group_id,

			},
			'success' : function(responseTree, responseElements, responseHTML, responseJavaScript)
			{
        parent.window.location.reload();
			}
		});
	}

	function rejectmemberRequestSend (member_id, group_id, user_id) {
		var friendUrl = '<?php echo $this->url(array('controller' => 'index', 'action' => 'reject'), 'sitegroupmember_approve', true) ?>';
		$.ajax({
			url : friendUrl,
			type: "POST",
			dataType: "html",
			data : {
				format: 'html',
				member_id: member_id,
				group_id:group_id,
				user_id: user_id
			},
			'success' : function(responseTree, responseElements, responseHTML, responseJavaScript) { 
        parent.window.location.reload();
			}
		});
	}
</script>

<div class="seaocore_members_popup seaocore_members_popup_notbs">
	<div class="top">
		<div class="heading"><?php echo $this->translate('Requested Members')?></div>
	</div>
</div><br />

<?php if (count($this->paginator) > 0) : ?>
  <div class="sm-content-list">
   <ul data-role="listview" data-inset="false">
		<?php foreach( $this->paginator as $value ): ?>
			<li data-icon="cog" data-inset="true">
       <a>
        <?php echo $this->itemPhoto($value->getOwner(), 'thumb.icon');?>
        <h3><?php echo $value->getTitle();?></h3>
       </a>
       <a href="#sitegroupmember_<?php echo $value->user_id;?>" data-rel="popup"></a>
				<div data-role="popup" id="sitegroupmember_<?php echo $value->user_id ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
					<div data-inset="true" style="min-width:150px;" class="sm-options-popup">
						<a href="javascript:void(0);" onclick="userWidgetRequestSend('<?php echo $value->member_id ?>', '<?php echo $value->group_id ?>');" class="ui-btn-default"><?php echo $this->translate('Approve Member')?></a>
						<a href="javascript:void(0);" onclick="rejectmemberRequestSend('<?php echo $value->member_id ?>', '<?php echo $value->group_id ?>', '<?php echo $value->user_id ?>');" class="ui-btn-default"><?php echo $this->translate('Reject Request')?></a>
						<a href="#" data-rel="back" class="ui-btn-default"><?php echo $this->translate('Cancel'); ?></a>
          </div>
				</div>
      </li>
		<?php endforeach;?>
   </ul>
  </div>
<?php else:?>
  <div class="tip">
   <span>
     <?php echo $this->translate('No members request.');?>
   </span>
  </div>
<?php endif;?>