<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<?php
$event = $this->event;
$viewer = $this->viewer();

?>
<div class="heevent-block">
  <div class="heevent-widget">
    <div class="heevent-wg-title-options">
      <div class="heevents-options events_action heevent-widget-inner">
        <?php if ($event->authorization()->isAllowed($viewer, 'invite')) { ?>
        <button title="<?php echo $this->translate('Invite Guests') ?>" value="leave" name="option"
                class="option_btn"
                onclick="Smoothbox.open('<?php echo $this->url(array('controller' => 'member', 'action' => 'invite', 'event_id' => $event->getIdentity(), 'format' => 'smoothbox'), 'event_extended', true) ?>')"><?php echo $this->translate('Invite') ?>
          <i class="hei hei-arrow-down"></i></button>
        <?php } ?>
      </div>
      <h3>
        <?php echo $this->translate('Guests') ?>
      </h3>
    </div>
    <a></a>
    <?php if($goingCount = $this->going->getTotalItemCount()){ ?>
    <h4><?php echo $this->translate('HEEVENT_Going');?> (<?php echo $this->locale()->toNumber($goingCount) ?>)</h4>
    <div class="heevent-widget-inner">
      <ul>
        <?php foreach( $this->going as $member ):
          if( !empty($member->resource_id) ) {
            $memberInfo = $member;
            $member = $this->item('user', $memberInfo->user_id);
          } else {
            $memberInfo = $this->event->membership()->getMemberInfo($member);
          }
          ?>

          <li id="event_member_<?php echo $member->getIdentity() ?>">

            <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'), array('class' => 'heevent-guest-icon')) ?>
            <div style="display: none !important;" class='event_members_options'>

              <?php // Remove/Promote/Demote member ?>
              <?php if( $this->event->isOwner($this->viewer())): ?>

                <?php if( !$this->event->isOwner($member) && $memberInfo->active == true ): ?>
                  <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Remove Member'), array(
                    'class' => 'buttonlink smoothbox icon_friend_remove'
                  )) ?>
                <?php endif; ?>
                <?php if( $memberInfo->active == false && $memberInfo->resource_approved == false ): ?>
                  <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'approve', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Approve Request'), array(
                    'class' => 'buttonlink smoothbox icon_event_accept'
                  )) ?>
                  <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Reject Request'), array(
                    'class' => 'buttonlink smoothbox icon_event_reject'
                  )) ?>
                <?php endif; ?>
                <?php if( $memberInfo->active == false && $memberInfo->resource_approved == true ): ?>
                  <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'cancel', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Cancel Invite'), array(
                    'class' => 'buttonlink smoothbox icon_event_cancel'
                  )) ?>
                <?php endif; ?>


              <?php endif; ?>
            </div>
                  <a class="heevent-guest-title" href="<?php echo $member->getHref() ?>"><?php echo $member->getTitle() ?>
                    <?php if( $this->event->getParent()->getGuid() == ($member->getGuid())): ?>
                      <span>
                      <?php echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('owner') )) ?>
                      </span>
                    <?php endif; ?>
                  </a>


          </li>

        <?php endforeach;?>

      </ul>

    </div>
    <?php } ?>
  </div>
  <?php if($maybeCount = $this->maybe->getTotalItemCount()){ ?>
    <div class="heevent-widget">
      <h4><?php echo $this->translate('HEEVENT_Maybe');?> (<?php echo $this->locale()->toNumber($maybeCount) ?>)</h4>
      <div class="heevent-widget-inner">
        <ul>
          <?php foreach( $this->maybe as $member ):
            if( !empty($member->resource_id) ) {
              $memberInfo = $member;
              $member = $this->item('user', $memberInfo->user_id);
            } else {
              $memberInfo = $this->event->membership()->getMemberInfo($member);
            }
            ?>

            <li id="event_member_<?php echo $member->getIdentity() ?>">

              <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'), array('class' => 'heevent-guest-icon')) ?>
              <div style="display: none !important;" class='event_members_options'>

                <?php // Remove/Promote/Demote member ?>
                <?php if( $this->event->isOwner($this->viewer())): ?>

                  <?php if( !$this->event->isOwner($member) && $memberInfo->active == true ): ?>
                    <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Remove Member'), array(
                      'class' => 'buttonlink smoothbox icon_friend_remove'
                    )) ?>
                  <?php endif; ?>
                  <?php if( $memberInfo->active == false && $memberInfo->resource_approved == false ): ?>
                    <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'approve', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Approve Request'), array(
                      'class' => 'buttonlink smoothbox icon_event_accept'
                    )) ?>
                    <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Reject Request'), array(
                      'class' => 'buttonlink smoothbox icon_event_reject'
                    )) ?>
                  <?php endif; ?>
                  <?php if( $memberInfo->active == false && $memberInfo->resource_approved == true ): ?>
                    <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'cancel', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Cancel Invite'), array(
                      'class' => 'buttonlink smoothbox icon_event_cancel'
                    )) ?>
                  <?php endif; ?>


                <?php endif; ?>
              </div>
              <a class="heevent-guest-title" href="<?php echo $member->getHref() ?>"><?php echo $member->getTitle() ?>
                <?php if( $this->event->getParent()->getGuid() == ($member->getGuid())): ?>
                  <span>
                      <?php echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('owner') )) ?>
                      </span>
                <?php endif; ?>
              </a>


            </li>

          <?php endforeach;?>

        </ul>

      </div>
    </div>
  <?php } ?>
</div>

  <?php if($ticket = $this->tickets->getTotalItemCount()){ ?>
    <div class="heevent-widget">
      <h4><?php echo $this->translate('HEEVENT_Maybe');?> (<?php echo $this->locale()->toNumber($maybeCount) ?>)</h4>
      <div class="heevent-widget-inner">
        <ul>
          <?php foreach( $this->tickets as $member ):

            if( !empty($member->event_id) ) {
              $memberInfo = $member;
              $member = $this->item('user', $memberInfo->user_id);
            } else {
              $memberInfo = $this->event->membership()->getMemberInfo($member);
            }
            ?>

            <li id="event_member_<?php echo $member->getIdentity() ?>">

              <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'), array('class' => 'heevent-guest-icon')) ?>
              <div style="display: none !important;" class='event_members_options'>

                <?php // Remove/Promote/Demote member ?>
                <?php if( $this->event->isOwner($this->viewer())): ?>

                  <?php if( !$this->event->isOwner($member) && $memberInfo->active == true ): ?>
                    <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Remove Member'), array(
                      'class' => 'buttonlink smoothbox icon_friend_remove'
                    )) ?>
                  <?php endif; ?>
                  <?php if( $memberInfo->active == false && $memberInfo->resource_approved == false ): ?>
                    <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'approve', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Approve Request'), array(
                      'class' => 'buttonlink smoothbox icon_event_accept'
                    )) ?>
                    <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Reject Request'), array(
                      'class' => 'buttonlink smoothbox icon_event_reject'
                    )) ?>
                  <?php endif; ?>
                  <?php if( $memberInfo->active == false && $memberInfo->resource_approved == true ): ?>
                    <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'cancel', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Cancel Invite'), array(
                      'class' => 'buttonlink smoothbox icon_event_cancel'
                    )) ?>
                  <?php endif; ?>


                <?php endif; ?>
              </div>
              <a class="heevent-guest-title" href="<?php echo $member->getHref() ?>"><?php echo $member->getTitle() ?>
                <?php if( $this->event->getParent()->getGuid() == ($member->getGuid())): ?>
                  <span>
                      <?php echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('owner') )) ?>
                      </span>
                <?php endif; ?>
              </a>


            </li>

          <?php endforeach;?>

        </ul>

      </div>
    </div>
  <?php } ?>

