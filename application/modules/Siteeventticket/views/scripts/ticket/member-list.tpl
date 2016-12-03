<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: member-list.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<a class="pabsolute" id="siteevent_profile_members_anchor"></a>

<script type="text/javascript">
    var siteeventMemberPage = Number('<?php echo $this->paginator->getCurrentPageNumber() ?>');
    var occurrence_id = '<?php echo $this->occurrence_id; ?>';
    var siteeventContentUrl = '<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'ticket', 'action' => 'member-list', 'friendsonly' => $this->friendsonly, 'subject' => 'siteevent_event_' . $this->event->getIdentity(), 'event_id' => $this->event->getIdentity()), 'default', true); ?>';
</script>

<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>

<a id="like_members_profile" style="position:absolute;"></a>

<div class="seaocore_members_popup siteevent_guest_popup">
    <div class="top">
        <?php
        if($this->friendsonly)
          $title = $this->translate('Friends who have bought a ticket for this event');
        else
          $title = $this->translate('Member who have bought tickets for this event.');
        ?>
        <div class="heading"><?php echo $title; ?></div>
        <div class="seaocore_members_search_box o_hidden">
            <div class="fleft mtop5">

                <?php if (($this->level_id == 1 || $this->event->isOwner($this->viewer) || $this->canEdit)): ?>
                    <span class="siteevent_link_wrap mright5">
                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_message"></i>
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'siteevent', 'controller' => 'member', 'action' => 'compose', 'event_id' => $this->event->event_id, 'occurrence_id' => $this->occurrence_id), $this->translate("Message Member"), array('onclick' => 'SmoothboxSEAO.close();Smoothbox.open(this.href);return false;')); ?>
                    </span>
                <?php endif; ?>

                <?php if ($this->totalMembers && ($this->level_id == 1 || $this->event->isOwner($this->viewer) || $this->canEdit)): ?>
                    <span class="siteevent_link_wrap">
                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_download"></i>
                        <?php echo $this->htmlLink(array('module' => 'siteeventticket', 'controller' => 'ticket', 'action' => 'export-excel', 'event_id' => $this->event->getIdentity(), 'occurrence_id' => $this->occurrence_id), $this->translate('Download Members List')) ?>
                    </span>
                <?php endif; ?>

            </div>
         
            <div class="seaocore_members_search fright mtop5">
                <?php //SHOW EVENT OCCURRENCE DATE DROP-DOWN FOR FILTERING GUESTS  ?>
                <?php if (!empty($this->datesInfo) && count($this->datesInfo) > 1): ?>
                    <span class="siteevent_members_search_label"><?php echo $this->translate('Filter by Date'); ?></span>
                    <select onchange="occurrence_id = this.value; getMembersConditionally()" id='date_filter_occurrence'>
                        <?php
                        $filter_dates = Engine_Api::_()->siteevent()->getAllOccurrenceDate($this->datesInfo);
                        foreach ($filter_dates as $key => $date):
                            ?> 
                            <option value="<?php echo $key; ?>" <?php if ($this->occurrence_id == $key): ?> selected='selected' <?php endif; ?>><?php echo $date; ?></option>
                        <?php endforeach;
                        ?>
                    </select>
                <?php endif; ?>
            </div>
         
            <div class="clear"></div>
        </div>
    </div>
    <div class="seaocore_members_popup_content" id="lists_popup_content">
        <?php if ($this->totalMembers > 0): ?>
            <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
                <div class="seaocore_members_popup_paging">
                    <div id="user_like_members_previous" class="paginator_previous">
                        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => 'paginateEventMembers(siteeventMemberPage - 1)','style' => ''));?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($this->totalMembers > 0) { ?>
            <?php
            foreach ($this->paginator as $member):
                if (!empty($member->resource_id)) {
                    $memberInfo = $member;
                    $member = $this->item('user', $memberInfo->user_id);
                } else {

                    $memberInfo = $this->event->membership()->getMemberInfoCustom($member);
                }

                $listItem = $this->list->get($member);
                $isLeader = ( null !== $listItem );
                ?>
                <div class="item_member_list siteevent_popup_member_list">
                    <div class="item_member_thumb">
                        <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'), array()) ?>            
                    </div>
                    <div class="siteevent_popup_member_list_options fright">
                        <?php if ($this->event->isOwner($this->viewer())): ?>
                            <?php if ($memberInfo->active && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.leader', 1)): ?>
                                <?php if ($isLeader): ?>
                                    <span class="siteevent_link_wrap f_small dblock">
                                        <i class="siteevent_icon icon_siteevents_demote mright5"></i>
                                        <a href="javascript:void(0);" onclick = "SmoothboxSEAO.close();
                                            Smoothbox.open('<?php echo $this->url(array('controller' => 'member', 'action' => 'demote', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'occurrence_id' => $this->occurrence_id), "siteevent_extended", true); ?>');"><?php echo $this->translate('Remove as Leader'); ?></a>
                                    </span>
                                <?php elseif (!$this->event->isOwner($member)): ?>
                                    <span class="siteevent_link_wrap f_small dblock">
                                        <i class="siteevent_icon icon_siteevents_promote mright5"></i>
                                        <a href="javascript:void(0);" onclick = "SmoothboxSEAO.close();
                                            Smoothbox.open('<?php echo $this->url(array('controller' => 'member', 'action' => 'promote', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'occurrence_id' => $this->occurrence_id), "siteevent_extended", true); ?>');"><?php echo $this->translate('Make Event Leader'); ?></a>
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>  
                        <?php endif; ?>

                        <?php
                        if ($this->viewer()->getIdentity()) {
                            echo $this->userFriendshipAjax($this->user($member->getIdentity()));
                        }
                        ?>

                        <?php
                        //SHOW MESSAGE LINK 
                        $item = Engine_Api::_()->getItem('user', $member->getIdentity());
                        if (Engine_Api::_()->seaocore()->canSendUserMessage($item)) :
                            ?>
                            <span class="siteevent_link_wrap f_small ">
                                <i class="siteevent_icon mright5" style="background-image: url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Messages/externals/images/send.png);"></i>
                                <a href="<?php echo Zend_Controller_Front::getInstance()->getBaseUrl(); ?>/messages/compose/to/<?php echo $member->getIdentity() ?>"  class="smoothbox"><?php echo $this->translate('Message'); ?> </a>
                            </span>
                        <?php endif; ?> 
                    </div>

                    <div class="item_member_details">
                        <div class="item_member_name" id="siteevent_profile_list_title_<?php echo $member->user_id ?>">
                            <?php echo $this->htmlLink($member->getHref(), $member->getTitle(), array('class' => 'item_photo seao_common_add_tooltip_link', 'title' => $member->getTitle(), 'target' => '_parent', 'rel' => 'user' . ' ' . $member->user_id)); ?>
                            <?php if ($this->event->getParent()->getGuid() == ($member->getGuid())): ?>
                                <?php echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('owner'))) ?>
                            <?php elseif ($isLeader): ?>  
                                <?php echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('Leader'))) ?>
                            <?php endif; ?>  
                        </div> 
                    </div>
                </div>
            <?php endforeach; ?>
        <?php } else { ?>
            <div class='tip m10'>
                <span>
                    <?php echo $this->translate('No results were found.'); ?>
                </span>
            </div>
        <?php } ?>

        <?php if ($this->totalMembers > 1): ?>
            <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
              <div class="seaocore_members_popup_paging">   
              <div id="user_siteevent_members_next" class="paginator_next">
                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => 'paginateEventMembers(siteeventMemberPage + 1)'));?>
                </div>
              </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<div class="seaocore_members_popup_bottom siteevent_guest_popup_bottom">
    <button onclick="SmoothboxSEAO.close();"><?php echo $this->translate("Close") ?></button>
</div>	

<script type="text/javascript">
var getMembersConditionally =  function(event_occurrence) {
	
	event_occurrence = event_occurrence || 0;

    //CHECK EITHER TO SHOW ALL EVENTS OR ONLY EVENT OCCURENCE EVENT.  
    $('lists_popup_content').innerHTML = '<div class="siteevent_profile_loading_image"></div>';
    en4.core.request.send(new Request.HTML({
        'url' : siteeventContentUrl,
        'data' : $merge({
            'format' : 'html',
            'subject' : en4.core.subject.guid,           
            'is_ajax_load':1,
            occurrence_id: occurrence_id
                    
    })
    }), {
        'element' : $('lists_popup_content') != null ? $('lists_popup_content').getParent().getParent() : $('siteevent_profile_members_anchor').getParent()
    });
	
}    
</script>    