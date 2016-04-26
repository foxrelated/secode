<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage-leaders.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript" >
    var submitformajax = 1;
</script>
<?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="siteevent_dashboard_content">
    <?php if (empty($this->is_ajax)) : ?>
        <div class="layout_middle">
            <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
            <div class="siteevent_edit_content">
                <div id="show_tab_content">
                <?php endif; ?> 
                <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js'); ?>
                <div class="siteevent_form">
                    <div>
                        <div>
                            <div class="siteevent_leaders">
                                <h3> <?php echo $this->translate('Manage Leaders'); ?> </h3>
                                <p class="form-description"><?php echo $this->translate("Below you can see all the leaders who can manage your event, like you can do. You can add new guests as leader of this event and remove any existing ones. Note that leaders selected by you for this page will get complete authority like you to manage this event, including deleting it. Thus you should be specific in selecting them.") ?></p>
                                <br />
                                <?php foreach ($this->members as $member): ?>

                                    <div id='<?php echo $member->user_id ?>_page_main'  class='siteevent_leaders_list'>
                                        <div class='siteevent_leaders_thumb' id='<?php echo $member->user_id ?>_pagethumb'>
                                            <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member->getOwner(), 'thumb.icon')) ?>
                                        </div> 
                                        <div id='<?php echo $member->user_id ?>_page' class="siteevent_leaders_detail">
                                            <?php if ($this->siteevent->owner_id != $member->user_id): ?>
                                                <div class="siteevent_leaders_cancel">

                                                    <?php if ($this->owner_id != $member->user_id) : ?>
                                                        <span class="siteevent_link_wrap mright5">
                                                            <i class="siteevent_icon icon_siteevents_demote"></i>
                                                            <?php
                                                            echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'member', 'action' => 'demote', 'event_id' => $this->siteevent->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Remove as Leader'), array(
                                                                //'class' => 'buttonlink smoothbox icon_siteevent_demote'
                                                                'class' => ' smoothbox'
                                                            ))
                                                            ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                            <span><?php echo $this->htmlLink($member->getHref(), $member->getTitle()) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <input type="hidden" id='count_div' value='<?php echo count($this->members) ?>' />
                            <form method='post' class="global_form mtop10" action='<?php echo $this->url(array('controller' => 'member', 'action' => 'manage-leaders', 'event_id' => $this->siteevent->event_id), 'siteevent_extended') ?>'>
                                <div class="fleft">
                                    <div>
                                        <?php if (!empty($this->message)): ?>
                                            <div class="tip">
                                                <span>
                                                    <?php echo $this->message; ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="siteevent_leaders_input">
                                            <?php echo $this->translate("Start typing the name of the guest...") ?> <br />	
                                            <input type="text" id="searchtext" name="searchtext" value="" />
                                            <input type="hidden" id="user_id" name="user_id" />
                                        </div>
                                        <div class="siteevent_leaders_button">	
                                            <button id="promoteButton" type="submit" disabled="disabled"  name="submit"><?php echo $this->translate("Make Event Leader") ?></button>
                                        </div>	
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <br />	
                <div id="show_tab_content_child">
                </div>
                <?php if (empty($this->is_ajax)) : ?>
                </div>
            </div>
        </div>
    <?php endif; ?> 	
</div>    
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
    en4.core.runonce.add(function()
    {
        var contentAutocomplete = new Autocompleter.Request.JSON('searchtext', '<?php echo $this->url(array('controller' => 'member', 'action' => 'manage-auto-suggest', 'event_id' => $this->siteevent->event_id), 'siteevent_extended', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'maxChoices': 40,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest seaocore-autosuggest',
            'customChoices': true,
            'filterSubset': true,
            'multiple': false,
            'injectChoice': function(token) {
                var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id': token.label});
                new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice1'}).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });

        contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
            document.getElementById('promoteButton').removeAttribute("disabled");
            document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
        });
    });
</script>

<style type="text/css">
    .global_form > div > div{background:none;border:none;padding:0px;}
</style>
