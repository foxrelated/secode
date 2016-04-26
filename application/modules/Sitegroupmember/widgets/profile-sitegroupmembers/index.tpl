<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitegroupmember/externals/styles/style_sitegroupmember.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');

include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/Adintegration.tpl';
?>
<?php if (empty($this->isajax)) : ?>
<script type="text/javascript">
  en4.sitegroupmember.profileTabParams[<?php echo $this->identity ?>]={
    type: 'member',
    requestParams :<?php echo json_encode($this->params) ?>,
    searchFormElement:'sp_m_search_<?php echo $this->identity ?>',
    loadingElement :'sitegroupmember_search_<?php echo $this->identity ?>',
    requestUrl: en4.core.baseUrl+'<?php echo ($this->user_layout) ? 'sitegroup/widget' :'widget';?>'
  };
  
</script>
<?php endif; ?>
<?php if (!empty($this->show_content)) : ?>
  <script type="text/javascript">     
    en4.core.runonce.add(function() {
       if($('sp_m_s_t_<?php echo $this->identity ?>')){ 
      var contentAutocomplete = new Autocompleter.Request.JSON('sp_m_s_t_<?php echo $this->identity ?>', '<?php echo $this->url(array('action' => 'get-item'), 'sitegroupmember_approve', true) ?>', {
        'postVar' : 'sitegroup_members_search_input_text',
        'minLength': 1,
        'selectMode': 'pick',
        'autocompleteType': 'tag',
        'className': 'searchbox_autosuggest',
        'customChoices' : true,
        'filterSubset' : true,
        'multiple' : false,
        'injectChoice': function(token){
          var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id':token.label});
          new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice1'}).inject(choice);
          this.addChoiceEvents(choice).inject(this.choices);
          choice.store('autocompleteChoice', token);
        }
      });
      contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
        document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
         en4.sitegroupmember.profileTabRequest({content_id : <?php echo $this->identity ?>});
      });
  
        $('sp_m_s_t_<?php echo $this->identity ?>').addEvent('keypress', function(e) {
          if( e.key != 'enter' ) return;
            en4.sitegroupmember.profileTabRequest({content_id : <?php echo $this->identity ?>});
        });
      }
    });
    	
          		  
    function userWidgetRequestSend (group_id, user_id) 
    { 
      var friendUrl = '<?php echo $this->url(array('action' => 'index'), 'sitegroup_manageadmins', true) ?>';
      en4.core.request.send(new Request.HTML({
        url : friendUrl,
        data : {
          format: 'html',
          group_id: group_id,
          user_id: user_id
        },
        'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
        {
          document.getElementById('make_group_admin').style.display = 'none';
          parent.window.location.reload();
        }
      }));
    }
          		
    function removeGroupAdmin (group_id, user_id) 
    { 
      var friendUrl = '<?php echo $this->url(array('action' => 'delete'), 'sitegroup_manageadmins', true) ?>';
      en4.core.request.send(new Request.HTML({
        url : friendUrl,
        data : {
          format: 'html',
          group_id: group_id,
          owner_id: user_id
        },
        'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
        {
          document.getElementById('remove_group_admin').style.display = 'none';
          parent.window.location.reload();
        }
      }));
    }

  </script>
<?php endif; ?>

<?php if (empty($this->isajax)) : ?>
  <div id="id_<?php echo $this->identity; ?>">
  <?php endif; ?>


  <?php if (!empty($this->show_content)) : ?>
    <?php if ($this->showtoptitle == 1): ?>
      <div class="layout_simple_head" id="layout_member">
        <?php echo $this->translate($this->sitegroup->getTitle()); ?><?php echo $this->translate("'s Members"); ?>
      </div>
    <?php endif; ?>	
    <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admemberwidget', 3) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)): ?>
      <div class="layout_right" id="communityad_member">
			<?php
				echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admemberwidget', 3),"loaded_by_ajax"=>1,'widgetId'=>"group_member")); 			 
			?>
      </div>
      <div class="layout_middle">
      <?php endif; ?>


      <?php //if (!empty($this->hasMember)) :  ?>


			<div class="sitegroupmember_profile_top_links">
				<?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($this->viewer_id, $this->sitegroup->group_id, $params = 'Invite'); ?>
				<?php if (!empty($hasMembers) && !empty($this->can_edit)) : ?>
					<?php echo $this->htmlLink(array('route' => 'sitegroup_profilegroupmember', 'action' => 'invite-members', 'group_id' => $this->sitegroup->group_id), $this->translate("Add People"), array('class' => 'buttonlink icon_sitegroup_ad_member smoothbox')); ?>
				<?php elseif (!empty($hasMembers) && empty($this->sitegroup->member_invite)): ?>
					<?php echo $this->htmlLink(array('route' => 'sitegroup_profilegroupmember', 'action' => 'invite', 'group_id' => $this->sitegroup->group_id), $this->translate("Add People"), array('class' => 'buttonlink icon_sitegroup_ad_member smoothbox')); ?>
				<?php endif; ?>
			 
				<?php if (!empty($hasMembers)): ?>
					<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitegroupmember', 'controller' => 'index', 'action' => 'compose', 'resource_id' => $this->sitegroup->group_id), $this->translate("Message Members"), array('class' => 'sitegroup_gutter_messageowner buttonlink smoothbox')); ?>
				<?php endif; ?>
				
				<?php //if (count($this->paginator) > 0): ?>
					<?php //echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'member-join', 'group_id' => $this->sitegroup->group_id), $this->translate('See All'), array('class' => 'icon_sitegroup_member buttonlink smoothbox')); ?>
				<?php //endif; ?>
			</div>

      <div class="sitegroup_list_filters sitegroupmember_profile_filters" <?php if ($this->paginator->count() <= 0 && empty($this->search)): ?> style="display:none;" <?php endif; ?> >
        <form id="sp_m_search_<?php echo $this->identity ?>"  name="sp_m_search_<?php echo $this->identity ?>" ><input  type="hidden" name="search" value="1" />
          <div class="sitegroup_list_filter_field">
            <?php echo $this->translate("Search:"); ?>

            <input  type="text" id="sp_m_s_t_<?php echo $this->identity ?>" name="search_text" value="<?php echo $this->search_text; ?>" />

            <input type="hidden" id="user_id" name="user_id" />
          </div>

          <div class="sitegroup_list_filter_field">
            <?php echo $this->translate('Browse by:'); ?>
            <select name="visibility"  onchange = "en4.sitegroupmember.profileTabRequest({content_id : <?php echo $this->identity ?>});">
              <option value="join_date" <?php if ($this->visibility == 'join_date'): ?> selected='selected' <?php endif; ?>><?php echo $this->translate("Members by Joined Date"); ?></option>        
              <option value="featured" <?php if ($this->visibility == 'featured'): ?> selected='selected' <?php endif; ?>><?php echo $this->translate("Featured Members"); ?></option>
              <option value="highlighted" <?php if ($this->visibility == 'highlighted'): ?> selected='selected' <?php endif; ?>><?php echo $this->translate("Highlighted Members"); ?></option>             
              <option value="displayname" <?php if ($this->visibility == 'displayname'): ?> selected='selected' <?php endif; ?>><?php echo $this->translate("Members By Name"); ?></option>
              <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1)): ?>
                <option value="groupadmin" <?php if ($this->visibility == 'groupadmin'): ?> selected='selected' <?php endif; ?> ><?php echo $this->translate("Group Admins"); ?></option>
              <?php endif; ?>
            </select>
          </div>
          <?php if (count($this->roleParamsArray) > 1): ?>
            <div class="sitegroup_list_filter_field">
              <?php echo $this->translate('Roles:'); ?>
              <select name="role_id" onchange = "en4.sitegroupmember.profileTabRequest({content_id : <?php echo $this->identity ?>});">
                <option value="0" ></option>
                <?php foreach ($this->roleParamsArray as $key => $roleLabel): ?>	
                  <option value="<?php echo $key ?>" <?php if ($this->role_id == $key): ?> selected="selected" <?php endif; ?>><?php echo $this->translate($roleLabel); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php endif; ?>
        </form>
      </div>
      <?php if ($this->request_count->getTotalItemCount() > 0) : ?>
        <?php if (!empty($this->can_edit)) : ?>
          <div class="sitegroup_member_see_waiting"><?php echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'request-member', 'group_id' => $this->sitegroup->group_id, 'tab' => $this->identity), $this->translate(array('View %s membership request waiting for approval.', 'View %s membership requests waiting for approval.', $this->request_count->getTotalItemCount()), $this->request_count->getTotalItemCount()), array(' class' => 'buttonlink icon_sitegroup_member smoothbox')); ?></div>	
        <?php endif; ?>
      <?php endif; ?>

      <div id='sitegroupmember_search_<?php echo $this->identity ?>' class="sitegroupmember_profile_list">
        <?php if (count($this->paginator) > 0): ?>
          <ul>
            <?php foreach ($this->paginator as $sitegroupmember): //print_r($sitegroupmember->toarray());die; ?>
              <?php $isGroupAdmin = $this->sitegroup->isGroupAdmin($sitegroupmember->user_id); ?>
              <?php if ($sitegroupmember->highlighted == 1): ?>
                <li id="sitegroupmember-item-<?php echo $sitegroupmember->member_id ?>" class="sitegroup_list_highlight">
                <?php else: ?>
                <li id="sitegroupmember-item-<?php echo $sitegroupmember->member_id ?>">
                <?php endif; ?>
                <div class="sitegroupmember_profile_list_photo b_medium">
									<table><tr><td>
	                  <?php echo $this->htmlLink($sitegroupmember->getHref(), $this->itemPhoto($sitegroupmember->getOwner(), 'thumb.profile')); ?>
									</td></tr></table>	
                </div>

                <div class="sitegroupmember_profile_list_info">
                  <div class="sitegroupmember_profile_list_title" id="sitegroup_profile_list_title_<?php echo $sitegroupmember->member_id ?>">
                    <?php if ($sitegroupmember->featured == 1): ?>
											<i class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured') ?>"></i>
                    <?php endif; ?>
                    <?php echo $this->htmlLink($sitegroupmember->getHref(), $sitegroupmember->getTitle(), array('class' => 'item_photo sea_add_tooltip_link', 'title' => $sitegroupmember->getTitle(), 'target' => '_parent', 'rel'=> 'user'.' '.$sitegroupmember->user_id)); ?>
                  </div>
                  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.title')) : ?>
                    <?php if (!empty($sitegroupmember->title)) : ?>
                      <div class="sitegroupmember_profile_list_stats">
                        <?php $roleCount = count(json_decode($sitegroupmember->title));
                        $a  = json_decode($sitegroupmember->title);
                        if($roleCount == 1) :
													echo implode(", ", json_decode($sitegroupmember->title));
                        elseif($roleCount == 2):
													echo implode(" and ", json_decode($sitegroupmember->title));
                        else:
                        $otherRoles = '<span class="sitegroupmember_show_tooltip_wrapper">' . $this->translate('%s others', ($roleCount - 1)) . '<span class="sitegroupmember_show_tooltip" style="margin-left:-8px;"><img src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitegroupmember/externals/images/tooltip_arrow.png" />';
												for ($i = 1; $i < $roleCount; $i++):
												$otherRoles.= $a[$i] . "<br />";
												endfor;
												$otherRoles.='</span></span>';
                        $roleNames = json_decode($sitegroupmember->title);
                        if(isset($roleNames[0]))
                        echo $this->translate('%1$s and %2$s ', $roleNames[0], $otherRoles);
                        endif;
                        ?>
                      </div>
                    <?php endif; ?>
                  <?php endif; ?>
                  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.date')): ?>
                    <div class="sitegroupmember_profile_list_stats seaocore_txt_light">
                      <?php if (!empty($sitegroupmember->date)) : ?>
                        <?php echo $this->translate('MEMBER_DATE') ?>
                        <?php
                        $DATE = explode('-', $sitegroupmember->date);
                        $YY = $DATE['0'];
                        $MM = $DATE['1'];
                        $DD = $DATE['2'];
                        if ($MM == '00' && $DD == '00') {
                          $dateShow = ' in ' . $YY;
                        } elseif ($DD == '00') {
                          $dateShow = ' in ' . date('F', strtotime($MM)) . ', ' . $YY;;
                         // $dateShow = $YY . ' ' . date('M', strtotime($MM));
                        } elseif ($YY != '00' && $MM != '00' && $DD != '00') {
                         $dateShow = ' on ' . date('F', strtotime($sitegroupmember->date)) . ' ' . $DD . ', ' . $YY;;
                        //  $dateShow = $YY . ' ' . date('M', strtotime($MM)) . ', ' . $DD;
                        }
                        echo $dateShow;
                        ?>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>


                  <?php if (!empty($hasMembers)): ?>
                    <div class="sitegroupmember_profile_list_options_wrapper">
                      <?php if (($sitegroupmember->group_owner_id != $sitegroupmember->user_id && $sitegroupmember->user_id == $this->viewer_id) || !empty($this->can_edit)): ?>
                        <span class="settings_icon"><i></i></span>
                      <?php endif; ?>
                      <div class="sitegroupmember_profile_list_options">
                        <?php if ($sitegroupmember->group_owner_id != $sitegroupmember->user_id) : ?>
                          <?php if ($sitegroupmember->active == 1 && $sitegroupmember->user_approved == 1 && empty($this->can_edit)):
                            echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'remove', 'member_id' => $sitegroupmember->member_id, 'tab' => $this->identity, 'params' => 'leave'), $this->translate('Leave Group'), array(' class' => 'buttonlink icon_friend_remove smoothbox')) ?>
                          <?php else: ?>
                            <?php echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'remove', 'member_id' => $sitegroupmember->member_id, 'tab' => $this->identity), $this->translate('Remove Member'), array(' class' => 'buttonlink icon_friend_remove smoothbox')) ?>
                          <?php endif; ?>

                          <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1) && !empty($this->can_edit)): ?> 
                            <?php if (empty($isGroupAdmin)) : ?>
                              <a id="make_group_admin" href="javascript:void(0);" onclick="userWidgetRequestSend('<?php echo $sitegroupmember->group_id ?>', '<?php echo $sitegroupmember->user_id ?>');" class="icon_sitegroups_group-owner buttonlink"><?php echo $this->translate('Make Group Admin') ?></a>
                            <?php else: ?>
                              <a id="remove_group_admin" href="javascript:void(0);" onclick="removeGroupAdmin('<?php echo $sitegroupmember->group_id ?>', '<?php echo $sitegroupmember->user_id ?>');" class="icon_sitegroups_group-owner buttonlink"><?php echo $this->translate('Remove Group Admin') ?></a>
                            <?php endif; ?>
                          <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($sitegroupmember->user_id != $this->viewer_id) : ?>
                          <?php if (!empty($this->can_edit)): ?> 
                            <?php if ($sitegroupmember->active == 0 && $sitegroupmember->user_approved == 0) :
                              echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'approve', 'member_id' => $sitegroupmember->member_id, 'group_id' => $sitegroupmember->group_id, 'tab' => $this->identity), $this->translate('Approve Member'), array(' class' => 'buttonlink icon_sitegroup_unhighlighted smoothbox')); ?>
                              <?php echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'reject', 'group_id' => $sitegroupmember->group_id, 'member_id' => $sitegroupmember->member_id, 'tab' => $this->identity), $this->translate('Reject Request'), array(' class' => 'buttonlink smoothbox icon_sitegroup_unhighlighted')); ?>
                            <?php endif; ?>
                          <?php endif; ?>
                        <?php endif; ?>
                        <?php if (!empty($this->can_edit)): ?>
                          <?php if ($sitegroupmember->highlighted == 0 && $sitegroupmember->active == 1)
                            echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'highlighted', 'member_id' => $sitegroupmember->member_id, 'tab' => $this->identity), $this->translate('Make Highlighted'), array(' class' => 'smoothbox buttonlink icon_sitegroup_highlighted')) ?>
                          <?php if ($sitegroupmember->highlighted == 1)
                            echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'highlighted', 'member_id' => $sitegroupmember->member_id, 'tab' => $this->identity), $this->translate('Make Un-highlighted'), array(' class' => 'buttonlink smoothbox icon_sitegroup_unhighlighted')) ?>
                        <?php endif; ?>

                        <?php if ($sitegroupmember->user_id == $this->viewer_id) : ?>
                          <?php echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'notification-settings', 'member_id' => $sitegroupmember->member_id, 'tab' => $this->identity), $this->translate('Notification Settings'), array(' class' => 'buttonlink smoothbox icon_sitegroup_notification')) ?>
                        <?php endif; ?>
                        <?php if ($sitegroupmember->user_id == $this->viewer_id || !empty($this->can_edit)) : ?>
                          <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.date') || Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.title')) : ?>
                            <?php echo $this->htmlLink(array('route' => 'sitegroupmember_approve', 'action' => 'edit', 'member_id' => $sitegroupmember->member_id, 'group_id' => $sitegroupmember->resource_id, 'tab' => $this->identity), $this->translate('Edit'), array(' class' => 'icon_sitegroups_edit  smoothbox buttonlink')) ?>
                          <?php endif; ?>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endif; ?>

                </div>
              </li>
            <?php endforeach; ?>
          </ul>
          <?php if ($this->paginator->count() > 1): ?>
            <div class="seaocore_pagination">
              <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
                <div id="user_sitegroup_members_previous" class="paginator_previous">
                  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => 'en4.sitegroupmember.profileTabRequest({content_id : '. $this->identity .',requestParams:{group:'.($this->paginator->getCurrentPageNumber()-1).'}});', 'class' => 'buttonlink icon_previous')); ?>
                </div>
              <?php endif; ?>
              <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
                <div id="user_sitegroup_members_next" class="paginator_next">
                  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => 'en4.sitegroupmember.profileTabRequest({content_id : '. $this->identity .',requestParams:{group:'.($this->paginator->getCurrentPageNumber()+1).'}});', 'class' => 'buttonlink_right icon_next')); ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        <?php elseif ($this->paginator->count() <= 0 && $this->search): ?>	
          <div class="tip">
            <span>
              <?php echo $this->translate('No members were found matching your search criteria.'); ?>
            </span>
          </div>
        <?php else: ?>
          <div class="tip">
            <span>
              <?php echo $this->translate('No such members have joined this group.'); ?>
            </span>
          </div>
        <?php endif; ?>
      </div>
      <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admemberwidget', 3) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)): ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <?php if (empty($this->isajax)) : ?>
  </div>
<?php endif; ?>

<script type="text/javascript">
  var adwithoutpackage = '<?php echo Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup) ?>';
  var member_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admemberwidget', 3); ?>';
  var is_ajax_divhide = '<?php echo $this->isajax; ?>';
  var execute_Request_Member = '<?php echo $this->show_content; ?>';
  //window.addEvent('domready', function () {
  var show_widgets = '<?php echo $this->widgets ?>';
  var MembertabId = '<?php echo $this->module_tabid; ?>';
  var MemberTabIdCurrent = '<?php echo $this->identity_temp; ?>';
  var group_communityad_integration = '<?php echo $group_communityad_integration; ?>';
  if (MemberTabIdCurrent == MembertabId) {
    if(group_showtitle != 0) {
      if($('profile_status') && show_widgets == 1) {
        $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitegroup->getTitle()) ?><?php echo $this->translate(' &raquo; '); ?><?php echo $this->translate('Members'); ?></h2>";	
      }
      if($('layout_member')) {
        $('layout_member').style.display = 'block';
      }  		
    }    	
    hideWidgetsForModule('sitegroupmember');
    prev_tab_id = '<?php echo $this->identity; ?>'; 
    prev_tab_class = 'layout_sitegroupmember_profile_sitegroupmembers_<?php echo $this->identity; ?>';		
    execute_Request_Member = true;
    hideLeftContainer (member_ads_display, group_communityad_integration, adwithoutpackage);
  } 
  else if (is_ajax_divhide != 1) {  	
    if($('global_content').getElement('.layout_sitegroupmember_profile_sitegroupmembers_<?php echo $this->identity; ?>')) {
      $('global_content').getElement('.layout_sitegroupmember_profile_sitegroupmembers_<?php echo $this->identity; ?>').style.display = 'none';
    } 	
  }
  // });
  if($("id_<?php echo $this->identity; ?>")){
    $("id_<?php echo $this->identity; ?>").getParent('.layout_sitegroupmember_profile_sitegroupmembers').addClass("layout_sitegroupmember_profile_sitegroupmembers_<?php echo $this->identity; ?>");
  }
  $$('.tab_<?php echo $this->identity; ?>').addEvent('click', function() {
    $('global_content').getElement('.layout_sitegroupmember_profile_sitegroupmembers_<?php echo $this->identity; ?>').style.display = 'block';
    if(group_showtitle != 0) {
      if($('profile_status') && show_widgets == 1) {
        $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitegroup->getTitle()) ?><?php echo $this->translate(' &raquo; '); ?><?php echo $this->translate('Members'); ?></h2>";	
      }
    }
    hideWidgetsForModule('sitegroupmember');
    $('id_' + <?php echo $this->identity ?>).style.display = "block";
    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->identity; ?>') {
      $$('.'+ prev_tab_class).setStyle('display', 'none');
    }
    if (prev_tab_id != '<?php echo $this->identity; ?>') {
      execute_Request_Member = false;
      prev_tab_id = '<?php echo $this->identity; ?>';
      prev_tab_class = 'layout_sitegroupmember_profile_sitegroupmembers_<?php echo $this->identity; ?>';
    }
    if(execute_Request_Member == false) {
      ShowContent('<?php echo $this->identity; ?>', execute_Request_Member, '<?php echo $this->identity_temp ?>', 'member', 'sitegroupmember', 'profile-sitegroupmembers', group_showtitle, 'null', member_ads_display, group_communityad_integration, adwithoutpackage);

      execute_Request_Member = true;
    }   		
    if('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1); ?>' && member_ads_display == 0)
{setLeftLayoutForGroup(); 	} 
  });
</script>