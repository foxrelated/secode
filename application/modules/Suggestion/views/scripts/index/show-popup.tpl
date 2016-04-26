<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: group.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

	$modName = $this->modName;
	$moduleId = $this->modContentId;
	$modItemType = $this->modItemType;
	switch($modName) {
		case 'friendfewfriend':
		case 'friend':
                        $contentObj = Engine_Api::_()->getItem('user', $moduleId);                                 
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$siteName = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
			if(strpos($this->findFriendFunName, 'few_friend') !== FALSE){
				$popupHeaderTitle = $this->translate("Help <u>%1s</u> find more friends on %2s", $contentName, $siteName);
				$popupHeaderDiscription = $this->translate("%s will get suggestions from you to add your selected friends as friends.", $contentName);
			}else {
				$popupHeaderTitle = $this->translate("Select friends of yours who know <u>%s</u>", $contentName);
				$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to add %s as a friend", $contentName);
			}
		break;
		case 'accept_request':
			$changeButtonText = $this->translate("Add Friends");
                        $contentObj = Engine_Api::_()->getItem('user', $moduleId);
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("You are now friends with <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("You may also know some of %s's friends :", $contentName);
		break;
		case 'group':
                        $contentObj = Engine_Api::_()->getItem($modName, $moduleId);
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to join this group.");
		break;
		case 'event':
                        $contentObj = Engine_Api::_()->getItem($modName, $moduleId);
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to attend this event.");
		break;
		case 'blog':
                        $contentObj = Engine_Api::_()->getItem($modName, $moduleId);
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to view this blog entry.");
		break;
		case 'album':
                        $contentObj = Engine_Api::_()->getItem($modName, $moduleId);
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to view this album.");
		break;
		case 'classified':
                        $contentObj = Engine_Api::_()->getItem($modName, $moduleId);
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to view this classified.");
		break;
		case 'music':
                        $contentObj = Engine_Api::_()->getItem('music_playlist', $moduleId);
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to listen to this music.");
		break;
		case 'video':
                        $contentObj = Engine_Api::_()->getItem($modName, $moduleId);
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to view this video.");
		break;
		case 'forum':
                        $contentObj = Engine_Api::_()->getItem('forum_topic', $moduleId);
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to discuss on this forum topic.");
		break;
		case 'poll':
                        $contentObj = Engine_Api::_()->getItem($modName, $moduleId);
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to vote on this poll.");
		break;
		case 'document':
                        $contentObj = Engine_Api::_()->getItem($modName, $moduleId);
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to view this document entry.");
		break;
		case 'list':
                        $contentObj = Engine_Api::_()->getItem('list_listing', $moduleId);
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to view this listing entry.");
		break;
		case 'sitepage':
 		case 'page_video':
 		case 'page_poll':
		case 'page_note':
		case 'page_document':
		case 'page_album':
    case 'page_report':
		case 'page_review':
		case 'page_event':
		case 'page_music':
		case 'page_offer':
		case 'sitebusiness':
 		case 'business_video':
 		case 'business_poll':
		case 'business_note':
		case 'business_document':
		case 'business_album':
		case 'business_review':
		case 'business_event':
		case 'business_music':
		case 'business_offer':      
		case 'sitegroup':
 		case 'group_video':
 		case 'group_poll':
		case 'group_note':
		case 'group_document':
		case 'group_album':
		case 'group_review':
		case 'group_event':
		case 'group_music':
		case 'group_offer':
                  $contentObj = Engine_Api::_()->getItem($modItemType, $moduleId);
		  $contentName = $contentObj->getTitle();
                  $contentLink = $contentObj->getHref();
		  $popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
		  $popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to view this.");
		break;
		case 'recipe':
                  $contentObj = Engine_Api::_()->getItem('recipe_recipe', $moduleId);
		  $contentName = $contentObj->getTitle();
                  $contentLink = $contentObj->getHref();
		  $popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
		  $popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to view this recipe entry.");
		break;
		case 'sitestore':
                        $contentObj = Engine_Api::_()->getItem($modItemType, $moduleId);
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to view this store entry.");
		break;
		case 'sitestoreproduct':
                        $contentObj = Engine_Api::_()->getItem($modItemType, $moduleId);
			$contentName = $contentObj->getTitle();
                        $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to view this product entry.");
		break;
		case 'siteevent':
      $contentObj = Engine_Api::_()->getItem($modItemType, $moduleId);
			$contentName = $contentObj->getTitle();
      $contentLink = $contentObj->getHref();
			$popupHeaderTitle = $this->translate("Select friends of yours who might be interested in <u>%s</u>", $contentName);
			$popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to view this event entry.");
		break;
		default:
		  $popupHeaderTitle = $this->translate("Select friends of yours who might be interested in this suggestion");
		  $popupHeaderDiscription = $this->translate("Selected friends will get a suggestion from you to view this.");
		break;
	}
  $this->headTranslate(array('Please select at-least one entry above to send suggestion to.', 'Search Members', 'Selected', 'No more suggestions are available.', 'Sorry, no more suggestions.'));
	include_once(APPLICATION_PATH ."/application/modules/Suggestion/views/scripts/_showPopupContent.tpl");
?>

<script type="text/javascript">
	var action_module = "<?php echo $modName;  ?>";
	var action_session_id = "<?php echo $moduleId;  ?>";
	var select_text_flag = "<?php echo $this->translate("Selected"); ?>";
</script>