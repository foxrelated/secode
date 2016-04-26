<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: request-blog.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

	if( empty($this->suggObj) || !empty($this->modNotEnable) ){ return; }
	$profile_photo = false;
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/scripts/core.js');
	$modInfoArray = $this->modInfoArray;
	$displayName = $modInfoArray['displayName'];
	$getOwnerObjectForTitle = $this->modObj;
	$buttonText = '';
	switch($this->suggObj->entity) {
		case 'friendfewfriend':
		case 'friend':
			$image = $this->htmlLink($this->senderObj->getHref(), $this->itemPhoto($this->senderObj, 'thumb.icon'), array('style'=> 'float:left;'));
			$label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
			$label = $this->translate($label);
			$bodyText = $this->translate('%s %s %s.');
			$getIdForTitle = $this->suggObj->entity_id;
			$getOwnerObjectForTitle = Engine_Api::_()->getItem('user', $getIdForTitle);
			$buttonText = '<span class="button">'. $this->userFriendship($getOwnerObjectForTitle) . '</span>';
		break;
	
		case 'photo':
			$image = $this->htmlLink($this->senderObj->getHref(), $this->itemPhoto($this->senderObj, 'thumb.icon'), array('style'=> 'float:left;'));
               		$profile_photo = true;
 			$bodyText = $this->translate('%s has sent you a profile photo suggestion.');
			$buttonText = $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => 'suggestion_photo_' . $this->suggObj->entity_id, 'format' => 'smoothbox'), $this->translate('View Photo Suggestion'), array('class' => 'smoothbox button fleft'));
		break;

		case 'group':
			$image = $this->htmlLink($this->senderObj->getHref(), $this->itemPhoto($this->senderObj, 'thumb.icon'), array('style'=> 'float:left;'));
			$label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
			$label = $this->translate($label);
			$bodyText = $this->translate('%s %s %s.');
			if($this->viewer()->getIdentity() && !$this->modObj->membership()->isMember($this->viewer(), null) ){
				$link = $this->url(array('controller' => 'member', 'action' => 'join', 'group_id' => $this->modObj->getIdentity()), 'group_extended', true);
				$buttonText = '<a href="javascript:void(0);" onclick = "getPopup(\'' . $link . '\');" class ="button fleft">' . $this->translate('Join Group') . '</a>';
			}else {
				// Delete grouop suggestion if already join the group.
				Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($this->suggObj->entity, $this->suggObj->entity_id, 'group_suggestion');
			}
		break;

		case 'event':
			$image = $this->htmlLink($this->senderObj->getHref(), $this->itemPhoto($this->senderObj, 'thumb.icon'), array('style'=> 'float:left;'));
			$label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
			$label = $this->translate($label);
			$bodyText = $this->translate('%s %s %s.');
			if($this->viewer()->getIdentity() && !$this->modObj->membership()->isMember($this->viewer(), null) ){
				$link = $this->url(array('controller' => 'member', 'action' => 'join', 'event_id' => $this->modObj->getIdentity()), 'event_extended', true);
				$buttonText = '<a href="javascript:void(0);" onclick = "getPopup(\'' . $link . '\');" class ="button fleft">' . $this->translate('Join Event') . '</a>';
			}else {
				// Delete grouop suggestion if already join the group.
				Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($this->suggObj->entity, $this->suggObj->entity_id, 'event_suggestion');
			}
		break;
		
		case 'magentoint':
			$image = $this->htmlLink($this->senderObj->getHref(), $this->itemPhoto($this->senderObj, 'thumb.icon'), array('style'=> 'float:left;'));
			$label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
			$label = $this->translate($label);
			$bodyText = $this->translate('%s %s %s.');
			$buttonText = $this->htmlLink($this->modObj->uri, $this->translate($modInfoArray['buttonLabel']), array('class'=>'button', 'style'=>'float:left;' ));
		break;

		default:
			$image = $this->htmlLink($this->senderObj->getHref(), $this->itemPhoto($this->senderObj, 'thumb.icon'), array('style'=> 'float:left;'));
			$label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
			$label = $this->translate($label);
			$bodyText = $this->translate('%s %s %s.');
			$buttonText = $this->htmlLink($this->modObj->getHref(), $this->translate($modInfoArray['buttonLabel']), array('class'=>'button', 'style'=>'float:left;' ));
		break;

	}
?>

<?php if(!empty($this->modObj)) {?>
<li class="suggestions-newupdate">
	<?php	if( !empty($image) ){ echo $image; } ?>
	<div>
    <div>
			<?php
			    if( empty($bodyText) ){ $bodyText = ''; }
			    
				if( strstr($this->suggObj->entity, 'magentoint' ) ) {
				  $show_label = Zend_Registry::get('Zend_Translate')->_($bodyText);
			      $show_label = sprintf($show_label, $this->sender_name, $label, $this->htmlLink($getOwnerObjectForTitle->uri, $getOwnerObjectForTitle->getTitle()));
				}else {
					if( empty($profile_photo) ) {
					  $show_label = Zend_Registry::get('Zend_Translate')->_($bodyText);
					  $show_label = sprintf($show_label, $this->sender_name, $label, $this->htmlLink($getOwnerObjectForTitle->getHref(), $getOwnerObjectForTitle->getTitle()));
					}else {
					  $show_label = Zend_Registry::get('Zend_Translate')->_($bodyText);
					  $show_label = sprintf($show_label, $this->sender_name);
					}
				}
			?>
			<?php echo $this->translate($show_label); ?>
		</div>
		<div id="sugg_notification_<?php echo $this->notification->notification_id; ?>">
			<?php echo $buttonText; ?>
			<span style="padding-top:5px;">
			<?php echo $this->translate('or'); ?>
			 <?php echo '<a class="disabled" title="' . $this->translate('Cancel this suggestion') . '" href="javascript:void(0);" onclick="removeSuggNotification(\'' . $this->suggObj->entity . '\', \'' . $this->suggObj->entity_id . '\', \'' . $this->notification->type . '\', \'' . 'sugg_notification_' . $this->notification->notification_id .'\', 0, 0);"> ' . $this->translate("ignore suggestion") . ' </a><br />';?></span>
		</div>	
	</div>
</li>
<?php }?>
