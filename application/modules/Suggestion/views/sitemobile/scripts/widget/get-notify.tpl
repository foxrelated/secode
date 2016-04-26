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
?>
<?php
if (empty($this->suggObj) || !empty($this->modNotEnable)) {
    return;
}
$profile_photo = false;
$modInfoArray = $this->modInfoArray;
$displayName = $modInfoArray['displayName'];
$getOwnerObjectForTitle = $this->modObj;
$buttonText = '';
switch ($this->suggObj->entity) {
    case 'friendfewfriend':
    case 'friend':
        $label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
        $label = $this->translate($label);
        $bodyText = $this->translate('%s %s %s.');
        $getIdForTitle = $this->suggObj->entity_id;
        $getOwnerObjectForTitle = Engine_Api::_()->getItem('user', $getIdForTitle);
        $buttonText = '<span class="button">' . $this->userFriendship($getOwnerObjectForTitle) . '</span>';
        break;

    case 'group':
        $label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
        $label = $this->translate($label);
        $bodyText = $this->translate('%s %s %s.');
        if ($this->viewer()->getIdentity() && !$this->modObj->membership()->isMember($this->viewer(), null)) {
            $buttonText = $this->htmlLink($this->modObj->getHref(), $this->translate('Join Group'));
        } else {
            // Delete group suggestion if already join the group.
            Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($this->suggObj->entity, $this->suggObj->entity_id, 'group_suggestion');
        }
        break;

    case 'event':
        $label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
        $label = $this->translate($label);
        $bodyText = $this->translate('%s %s %s.');
        if ($this->viewer()->getIdentity() && !$this->modObj->membership()->isMember($this->viewer(), null)) {
            $buttonText = $this->htmlLink($this->modObj->getHref(), $this->translate('Join Event'));
        } else {
            // Delete grouop suggestion if already join the group.
            Engine_Api::_()->getDbtable('suggestions', 'suggestion')->removeSuggestion($this->suggObj->entity, $this->suggObj->entity_id, 'event_suggestion');
        }
        break;

    case 'magentoint':
        $label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
        $label = $this->translate($label);
        $bodyText = $this->translate('%s %s %s.');
        $buttonText = $this->htmlLink($this->modObj->uri, $this->translate($modInfoArray['buttonLabel']));
        break;

    default:
        $label = $this->translate("has sent you a %s suggestion:", strtolower($displayName));
        $label = $this->translate($label);
        $bodyText = $this->translate('%s %s %s.');
        $buttonText = $this->htmlLink($this->modObj->getHref(), $this->translate($modInfoArray['buttonLabel']));
        break;
}
?>

<?php if (!empty($this->modObj)) { ?>
    <?php
    if (empty($bodyText)) {
        $bodyText = '';
    }

    if (strstr($this->suggObj->entity, 'magentoint')) {
        $show_label = Zend_Registry::get('Zend_Translate')->_($bodyText);
        $show_label = sprintf($show_label, '<b>'.$this->sender_name.'</b>', $label, $this->htmlLink($getOwnerObjectForTitle->uri, $getOwnerObjectForTitle->getTitle()));
    } else {
        if (empty($profile_photo)) {
            $show_label = Zend_Registry::get('Zend_Translate')->_($bodyText);
            $show_label = sprintf($show_label,'<b>'.$this->sender_name.'</b>', $label, $this->htmlLink($getOwnerObjectForTitle->getHref(), $getOwnerObjectForTitle->getTitle()));
        } else {
            $show_label = Zend_Registry::get('Zend_Translate')->_($bodyText);
            $show_label = sprintf($show_label, '<b>'.$this->sender_name.'</b>');
        }
    }
    ?>
    <li id="sugg_notification_<?php echo $this->notification->notification_id ?>">
        <div class="ui-btn">
            <?php echo $this->itemPhoto($this->senderObj, 'thumb.icon'); ?>
            <h3>
                <?php echo $this->translate($show_label); ?>
            </h3>
            <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
              <p class="sm-ui-lists-action">
                  <strong class="request_btn"><?php echo $buttonText; ?></strong>
                  <?php
                  echo '<a class="request_btn ignore_btn" href="javascript:void(0);" 
                      onclick="removeSuggNotification(\'' . $this->suggObj->entity . '\', \'' . $this->suggObj->entity_id . '\', \'' .
                  $this->notification->type . '\', \'' . 'sugg_notification_' . $this->notification->notification_id . '\', 0, 0);"> ' .
                  $this->translate("ignore suggestion") . ' </a>';
                  ?>
              </p>
            <?php else : ?>
              <p class="sm-ui-lists-action">
                  <strong><?php echo $buttonText; ?></strong>
                  <?php echo $this->translate('or'); ?>
                  <?php
                  echo '<a class="disabled" href="javascript:void(0);" 
                      onclick="removeSuggNotification(\'' . $this->suggObj->entity . '\', \'' . $this->suggObj->entity_id . '\', \'' .
                  $this->notification->type . '\', \'' . 'sugg_notification_' . $this->notification->notification_id . '\', 0, 0);"> ' .
                  $this->translate("ignore suggestion") . ' </a>';
                  ?>
              </p>
            <?php endif ?>
        </div>
    </li>
<?php } ?>
