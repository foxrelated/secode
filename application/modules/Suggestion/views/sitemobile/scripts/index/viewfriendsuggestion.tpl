<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partial.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
//$this->headScriptSM()->prependFile($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/scripts/friends_mobile.js');
//$getWidLimit = '10';
?>
<!-- Friend show code from here -->
<?php if (empty($getSignupAction)): ?>
  <?php if (!Engine_Api::_()->sitemobile()->isApp()) : ?>
    <h3><?php echo $this->translate("People You May Know"); ?></h3>
  <?php endif; ?>
  <?php
  $modCount = $numOfContent = @COUNT($this->modArray);
  if ($modCount > 1)
    $numOfContent = @COUNT($this->modArray) - 1;
  ?>
  <?php
  if (!empty($numOfContent)):
    $recommendedEndFlag = $numOfContent;
    $recommendedFlag = 0;
    ?>
    <!--Browse member code-->
    <?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemobile/modules/User/View/Helper', 'User_View_Helper'); ?>
    <?php $viewer = Engine_Api::_()->user()->getViewer(); ?>
    <div class="sm-content-list">
      <ul id="browsemembers_ul" class="ui-member-list sm-ui-lists" data-role="listview" data-icon="none">
        <?php
        foreach ($this->modArray as $modArrays):
          foreach ($modArrays['mod_array'] as $modInfos):
            foreach ($modInfos as $modType => $modObjects):
              foreach ($modObjects as $user):
                ?>
                <?php
                $table = Engine_Api::_()->getDbtable('block', 'user');
                $select = $table->select()
                        ->where('user_id = ?', $user->getIdentity())
                        ->where('blocked_user_id = ?', $viewer->getIdentity())
                        ->limit(1);
                $row = $table->fetchRow($select);
                ?>
                <?php if ($row == NULL && $this->viewer()->getIdentity() && $this->userFriendshipSM($user)): ?>
                  <li>       
                    <div class="ui-item-member-action">
                      <?php echo $this->userFriendshipSM($user) ?>
                    </div>
                    <div class="ui-btn">  
                      <?php echo $this->itemPhoto($user, 'thumb.icon') ?>
                      <div class="ui-list-content">
                        <h3><a href="<?php echo $user->getHref() ?>"><?php echo $user->getTitle() ?></a></h3>
                        <?php if ($this->userMutualFriendship($user)): ?>
                          <?php $link = $this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' => 'mutualfriend', 'friend_id' => $user->getIdentity()), 'default', true); ?>
                          <a href="<?php echo $link; ?>"><p><?php echo $this->userMutualFriendship($user) ?></p></a>
                        <?php endif; ?>
                      </div>
                    </div>
                  </li>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php endforeach; ?>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php $recommendedFlag++; ?>
    <?php
  else:
    echo "<div class='tip' style='margin-top:10px;'><span>" . $this->translate("You do not have any more friend suggestions.") . "</span></div>";
  endif;
  ?>
<?php endif; ?>

<!--<div data-role="popup" id="popupDialog-AddFriend" data-overlay-theme="a" data-theme="c" data-dismissible="false" style="max-width:400px;" class="ui-corner-all">
    <div data-role="header" data-theme="a" class="ui-corner-top">
      <h1><?php //echo $this->translate('Add Friend?'); ?></h1>
    </div>
    <div data-role="content" data-theme="d" class="ui-corner-bottom ui-content">
      <h3 class="ui-title"></h3>
      <p><?php //echo $this->translate('Would you like to add this member as a friend?'); ?></p>              

      <a href="#" data-role="button" data-inline="true" data-rel="back" data-transition="flow" data-theme="b" onclick="javascript:sm4.activity.addFriend()"><?php //echo $this->translate("Add Friend"); ?></a>
      <a href="#" data-role="button" data-inline="true" data-rel="back" data-theme="c"><?php echo $this->translate("Cancel"); ?></a>
    </div>
</div>

<div data-role="popup" id="popupDialog-CancelFriend" data-overlay-theme="a" data-theme="c" data-dismissible="false" style="max-width:400px;" class="ui-corner-all">
    <div data-role="header" data-theme="a" class="ui-corner-top">
      <h1><?php //echo $this->translate('Cancel Friend Request'); ?></h1>
    </div>
    <div data-role="content" data-theme="d" class="ui-corner-bottom ui-content">
      <h3 class="ui-title"></h3>
      <p><?php //echo $this->translate('Do you want to cancel your friend request?'); ?></p>              

      <a href="#" data-role="button" data-inline="true" data-rel="back" data-transition="flow" data-theme="b" onclick="javascript:sm4.activity.addFriend()"><?php //echo $this->translate("Cancel Request"); ?></a>
      <a href="#" data-role="button" data-inline="true" data-rel="back" data-theme="c"><?php echo $this->translate("Cancel"); ?></a>
    </div>
</div>-->
<!-- Friend show code end here -->
