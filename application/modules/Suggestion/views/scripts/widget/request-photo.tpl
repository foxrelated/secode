<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: request-photo.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
var cancelSuggphoto = function(notification_id, user_id, object_id)
{
  en4.core.request.send(new Request.HTML({      	
    url : en4.core.baseUrl + 'suggestion/main/notification-cancel',
    data : {
	      format : 'html',		   
	      notification_id : notification_id,
	      object_id : object_id
    }
  }), {
    'element' : $('sugg_photo_' + user_id)
  })
};
</script>
<?php if(!empty($this->photo_object))  {?>
<?php if($this->message != 1){ ?>
<li class="suggestions-newupdate">
		<?php	
		$photo_obj = Engine_Api::_()->getItem('user', $this->sender_id);
		echo $this->htmlLink($photo_obj->getHref(), $this->itemPhoto($photo_obj->getOwner(), 'thumb.icon'), array('style'=>'float:left;')); ?>
	<div>
		<div>
			<?php
				$show_label = Zend_Registry::get('Zend_Translate')->_('%s has sent you a profile photo suggestion.');
				$show_label = sprintf($show_label, $this->sender_name);
			?>
			<?php echo $show_label; ?>
		</div>	
		<div id="sugg_photo_<?php echo $this->photo_object->suggestion_id; ?>">
			<?php	echo $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => 'suggestion_photo_' . $this->photo_object->entity_id, 'format' => 'smoothbox'), $this->translate('View Photo Suggestion'), array('class' => 'smoothbox button', 'style' => 'float:left;')); ?>
			<span style="padding-top:5px;">
				<?php echo $this->translate('or'); ?>
				 <?php	echo '<a class="disabled" title="' . $this->translate('Cancel this suggestion') . '" href="javascript:void(0);" onclick="cancelSuggphoto(\'' . $this->notification->notification_id . '\', \'' . $this->photo_object->suggestion_id . '\', \'' . $this->notification->object_id . '\');"> ' . $this->translate("ignore suggestion") . ' </a><br />'; ?>
			</span>
		</div>
	</div>
</li>
<?php }  }?>