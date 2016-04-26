<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: app.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript" >

    function owner(thisobj) {
        var Obj_Url = thisobj.href;

        Smoothbox.open(Obj_Url);
    }
</script>

<?php if (empty($this->is_ajax)) : ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>
    <div class="layout_middle">
        <?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/edit_tabs.tpl'; ?>
        <div class="sitegroup_edit_content">
            <div class="sitegroup_edit_header"> 
                <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroup->group_id, $this->sitegroup->owner_id, $this->sitegroup->getSlug()), $this->translate('VIEW_GROUP')) ?>
                <h3><?php echo $this->translate('Dashboard: ') . $this->sitegroup->title; ?></h3>
            </div>				

            <div id="show_tab_content">
            <?php endif; ?>  
            <?php $canShowMessage = true; ?>
            <ul class="sitegroup_getstarted">
                <?php $i = 1; ?>		   	
                <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')): ?>
                    <?php if (!empty($this->allowed_upload_photo)): ?>
                        <li> <?php $canShowMessage = false; ?>
                            <div class="sitegroup_getstarted_num">																
                                <a href='<?php echo $this->url(array('group_id' => $this->sitegroup->group_id, 'album_id' => $this->default_album_id, 'tab' => $this->albumtab_id), 'sitegroup_photoalbumupload', true) ?>'><i class="icon_app_photo"></i></a>						
                            </div>
                            <div class="sitegroup_getstarted_des">
                                <b><?php echo $this->translate('Photos'); ?></b>
                                <p><?php echo $this->translate('Create albums and add photos for this group.'); ?></p>
                                <div class="sitegroup_getstarted_btn">
                                    <a href='<?php echo $this->url(array('group_id' => $this->sitegroup->group_id, 'album_id' => $this->default_album_id, 'tab' => $this->albumtab_id), 'sitegroup_photoalbumupload', true) ?>'><?php echo $this->translate('Add Photos'); ?></a>
                                    <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab' => $this->albumtab_id)), $this->translate('Manage Albums')) ?>
                                </div>
                            </div>	
                        </li>
                    <?php endif; ?> 
                <?php endif; ?>

                <?php if ($this->can_invite): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitegroup_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitegroupinvite_invite', 'user_id' => $this->viewer_id, 'sitegroup_id' => $this->group_id), '<i class="icon_app_invite"></i>') ?>
                        </div>
                        <div class="sitegroup_getstarted_des">
                            <b><?php echo $this->translate('Invite &amp; Promote'); ?></b>
                            <p><?php echo $this->translate('Tell your friends, fans and customers about this group and make it popular.'); ?></p>
                            <div class="sitegroup_getstarted_btn">
                                <a href='<?php echo $this->url(array('user_id' => $this->viewer_id, 'sitegroup_id' => $this->group_id), 'sitegroupinvite_invite', true) ?>'><?php echo $this->translate('Invite Friends &amp; Fans'); ?></a>
                            </div>
                        </div>
                    </li>
                <?php endif; ?>

                <?php if ($this->can_create_poll): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitegroup_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitegrouppoll_create', 'group_id' => $this->group_id, 'tab' => $this->polltab_id), '<i class="icon_app_poll"></i>') ?>
                        </div>
                        <div class="sitegroup_getstarted_des">
                            <b><?php echo $this->translate('Polls'); ?></b>
                            <p><?php echo $this->translate('Get feedback from visitors to your group.'); ?></p>
                            <div class="sitegroup_getstarted_btn">
                                <a href='<?php echo $this->url(array('group_id' => $this->group_id, 'tab' => $this->polltab_id), 'sitegrouppoll_create', true) ?>'><?php echo $this->translate('Create a Poll'); ?></a> 
                                <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab' => $this->polltab_id)), $this->translate('Manage Polls')) ?>
                            </div>
                        </div>
                    </li>		
                <?php endif; ?>

                <?php if ($this->can_create_doc): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitegroup_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitegroupdocument_create', 'group_id' => $this->group_id, 'tab' => $this->documenttab_id), '<i class="icon_app_document"></i>') ?>
                        </div>	
                        <div class="sitegroup_getstarted_des">
                            <b><?php echo $this->translate('Documents'); ?></b>
                            <p><?php echo $this->translate('Add and showcase documents on your group.'); ?></p>
                            <div class="sitegroup_getstarted_btn">
                                <a href='<?php echo $this->url(array('group_id' => $this->group_id, 'tab' => $this->documenttab_id), 'sitegroupdocument_create', true) ?>'><?php echo $this->translate('Add a Document'); ?></a>
                                <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab' => $this->documenttab_id)), $this->translate('Manage Documents')) ?>
                            </div>
                        </div>
                    </li>		
                <?php endif; ?>

                <?php if ($this->moduleEnable && !empty($this->can_offer)): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitegroup_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitegroupoffer_general', 'group_id' => $this->group_id, 'tab' => $this->offertab_id), '<i class="icon_app_offer"></i>') ?>
                        </div>
                        <div class="sitegroup_getstarted_des">
                            <b><?php echo $this->translate('Offers'); ?></b>
                            <p><?php echo $this->translate('Create and display attractive offers on your group.'); ?></p>
                            <div class="sitegroup_getstarted_btn">
                                <a href='<?php echo $this->url(array('action' => 'create', 'group_id' => $this->group_id, 'tab' => $this->offertab_id), 'sitegroupoffer_general', true) ?>'><?php echo $this->translate('Add an Offer'); ?></a>
                                <a href='<?php echo $this->url(array('action' => 'index', 'group_id' => $this->group_id, 'tab' => $this->offertab_id), 'sitegroupoffer_general', true) ?>'><?php echo $this->translate('Manage Offers'); ?></a>
                            </div>
                        </div>
                    </li>
                <?php endif; ?>

                <?php if ($this->option_id && !empty($this->can_form)): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitegroup_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitegroupform_general', 'option_id' => $this->option_id, 'group_id' => $this->group_id, 'tab' => $this->formtab_id), '<i class="icon_app_question"></i>') ?>
                        </div>
                        <div class="sitegroup_getstarted_des">
                            <b><?php echo $this->translate('Form'); ?></b>
                            <p><?php echo $this->translate('Gather useful information from visitors by creating your form with relevant questions.'); ?></p>
                            <div class="sitegroup_getstarted_btn">
                                <a href='<?php echo $this->url(array('action' => 'index', 'option_id' => $this->option_id, 'group_id' => $this->group_id, 'tab' => $this->formtab_id), 'sitegroupform_general', true) ?>'><?php echo $this->translate('Manage Form'); ?></a>
                                <?php $can_edit_tabname = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupform.edit.name', 1); ?>
                                <?php if (!empty($can_edit_tabname)): ?>
                                    <?php echo $this->htmlLink(array('route' => 'default', 'group_id' => $this->group_id, 'module' => 'sitegroupform', 'controller' => 'siteform', 'action' => 'edit-tab'), $this->translate("Edit Form Tab’s Name"), array('onclick' => 'owner(this);return false')) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>		
                <?php endif; ?>

                                <?php if ($this->can_create_video): ?>
                    <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo') && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitegroup_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitegroupvideo_create', 'group_id' => $this->group_id, 'tab' => $this->videotab_id), '<i class="icon_app_video"></i>') ?>
                        </div>
                        <div class="sitegroup_getstarted_des">
                            <b><?php echo $this->translate('Videos'); ?></b>
                            <p><?php echo $this->translate('Add and share videos for this group.'); ?></p>
                            <div class="sitegroup_getstarted_btn">
                                <a href='<?php echo $this->url(array('group_id' => $this->group_id, 'tab' => $this->videotab_id), 'sitegroupvideo_create', true) ?>'><?php echo $this->translate('Post a Video'); ?></a>
                                <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab' => $this->videotab_id)), $this->translate('Manage Videos')) ?>
                            </div>
                        </div>
                    </li>
                    <?php else:?>
                     <li> <?php $canShowMessage = false; ?>
                        <div class="sitegroup_getstarted_num">
                            
                            <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
                            <?php echo $this->htmlLink(array('route' => 'sitevideo_video_general', 'action' => 'create', 'tab' => $this->videotab_id, 'parent_type' => 'sitegroup_group', 'parent_id' => $this->group_id,), '<i  class="icon_app_video"></i>', array('class' => 'seao_smoothbox', 'data-SmoothboxSEAOClass' => 'seao_add_video_lightbox')); ?> 
                            <?php else:?>
                            <?php echo $this->htmlLink(array('route' => 'sitevideo_video_general', 'action' => 'create', 'tab' => $this->videotab_id, 'parent_type' => 'sitegroup_group', 'parent_id' => $this->group_id,), '<i class="icon_app_video"></i>'); ?> 
                            <?php endif;?>
                        </div>
                        <div class="sitegroup_getstarted_des">
                            <b><?php echo $this->translate('Videos'); ?></b>
                            <p><?php echo $this->translate('Add and share videos for this group.'); ?></p>
                            <div class="sitegroup_getstarted_btn">
                                <a class="seao_smoothbox" data-SmoothboxSEAOClass="seao_add_video_lightbox" href='<?php echo $this->url(array('action' => 'create', 'tab' => $this->videotab_id, 'parent_type' => 'sitegroup_group', 'parent_id' => $this->group_id,), 'sitevideo_video_general', true) ?>'><?php echo $this->translate('Post a Video'); ?></a>
                                <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab' => $this->videotab_id)), $this->translate('Manage Videos')) ?>
                            </div>
                        </div>
                    </li>
                    <?php endif;?>
                <?php endif; ?>

                <?php if ($this->can_create_event): ?>
                    <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent') && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')): ?>
                        <li> 
                            <?php $canShowMessage = false; ?>
                            <div class="sitegroup_getstarted_num">
                                <?php echo $this->htmlLink(array('route' => 'sitegroupevent_create', 'group_id' => $this->group_id, 'tab_id' => $this->eventtab_id), '<i class="icon_app_event"></i>') ?>
                            </div>
                            <div class="sitegroup_getstarted_des">
                                <b><?php echo $this->translate('Events'); ?></b>
                                <p><?php echo $this->translate('Organize events for this group.'); ?></p>
                                <div class="sitegroup_getstarted_btn">
                                    <a href='<?php echo $this->url(array('group_id' => $this->group_id, 'tab_id' => $this->eventtab_id), 'sitegroupevent_create', true) ?>'><?php echo $this->translate('Create an Event'); ?></a>
                                    <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab' => $this->eventtab_id)), $this->translate('Manage Events')) ?>
                                </div>
                            </div>
                        </li>		
                    <?php else: ?>
                        <li> 
                            <?php $canShowMessage = false; ?>
                            <div class="sitegroup_getstarted_num">
                                <?php if (isset($this->siteeventVersion) && Engine_Api::_()->siteevent()->hasPackageEnable()): ?>
                                    <?php echo $this->htmlLink(array('route' => 'siteevent_package', 'action' => 'index', 'parent_type' => 'sitegroup_group', 'parent_id' => $this->group_id, 'tab_id' => $this->eventtab_id), '<i class="icon_app_event"></i>') ?>
                                <?php else: ?>
                                    <?php echo $this->htmlLink(array('route' => 'siteevent_general', 'action' => 'create', 'parent_type' => 'sitegroup_group', 'parent_id' => $this->group_id, 'tab_id' => $this->eventtab_id), '<i class="icon_app_event"></i>') ?>
                                <?php endif; ?>
                            </div>
                            <div class="sitegroup_getstarted_des">
                                <b><?php echo $this->translate('Events'); ?></b>
                                <p><?php echo $this->translate('Organize events for this group.'); ?></p>
                                <div class="sitegroup_getstarted_btn">

                                    <?php if (isset($this->siteeventVersion) && Engine_Api::_()->siteevent()->hasPackageEnable()): ?>
                                        <a href='<?php echo $this->url(array('parent_type' => 'sitegroup_group', 'action' => 'index', 'parent_id' => $this->group_id, 'tab_id' => $this->eventtab_id), "siteevent_package", true) ?>'><?php echo $this->translate("Create an Event"); ?></a>
                                    <?php else: ?>
                                        <a href='<?php echo $this->url(array('parent_type' => 'sitegroup_group', 'action' => 'create', 'parent_id' => $this->group_id, 'tab_id' => $this->eventtab_id), 'siteevent_general', true) ?>'><?php echo $this->translate('Create an Event'); ?></a>
                                    <?php endif; ?>

                                    <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab' => $this->eventtab_id)), $this->translate('Manage Events')) ?>
                                </div>
                            </div>
                        </li>		
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($this->can_create_notes): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitegroup_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitegroupnote_create', 'group_id' => $this->group_id, 'tab' => $this->notetab_id), '<i class="icon_app_note"></i>') ?>
                        </div>
                        <div class="sitegroup_getstarted_des">
                            <b><?php echo $this->translate('Notes'); ?></b>
                            <p><?php echo $this->translate('Share updates and lots more by publishing notes in this blog-like section of your group.'); ?></p>
                            <div class="sitegroup_getstarted_btn">
                                <a href='<?php echo $this->url(array('group_id' => $this->group_id, 'tab' => $this->notetab_id), 'sitegroupnote_create', true) ?>'><?php echo $this->translate('Write a Note'); ?></a>
                                <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab' => $this->notetab_id)), $this->translate('Manage Notes')) ?>
                            </div>
                        </div>
                    </li>		
                <?php endif; ?>

                <?php if ($this->can_create_discussion): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitegroup_getstarted_num">
                            <?php
                            echo $this->htmlLink(array(
                                'route' => 'sitegroup_extended',
                                'controller' => 'topic',
                                'action' => 'create',
                                'subject' => $this->subject()->getGuid(),
                                'tab' => $this->discussiontab_id,
                                'group_id' => $this->group_id
                                    ), '<i class="icon_app_topic"></i>')
                            ?>
                        </div>
                        <div class="sitegroup_getstarted_des">
                            <b><?php echo $this->translate('Discussions'); ?></b>
                            <p><?php echo $this->translate('Enable interactions and information sharing on your group using threaded discussions.'); ?></p>
                            <div class="sitegroup_getstarted_btn">
                                <?php
                                echo $this->htmlLink(array(
                                    'route' => 'sitegroup_extended',
                                    'controller' => 'topic',
                                    'action' => 'create',
                                    'subject' => $this->subject()->getGuid(),
                                    'tab' => $this->discussiontab_id,
                                    'group_id' => $this->group_id
                                        ), $this->translate('Post a Topic'))
                                ?>
                                <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab' => $this->discussiontab_id)), $this->translate('Manage Discussions')) ?>
                            </div>
                        </div>	
                    </li>  
                <?php endif; ?>
                <?php if ($this->can_create_musics): ?>
                    <li> <?php $canShowMessage = false; ?>
                        <div class="sitegroup_getstarted_num">
                            <?php echo $this->htmlLink(array('route' => 'sitegroupmusic_create', 'group_id' => $this->group_id, 'tab' => $this->musictab_id), '<i class="icon_app_music"></i>') ?>
                        </div>
                        <div class="sitegroup_getstarted_des">
                            <b><?php echo $this->translate('Music'); ?></b>
                            <p><?php echo $this->translate('Add and share music for this group.'); ?></p>
                            <div class="sitegroup_getstarted_btn">
                                <a href='<?php echo $this->url(array('group_id' => $this->group_id, 'tab' => $this->notetab_id), 'sitegroupmusic_create', true) ?>'><?php echo $this->translate('Upload Music'); ?></a>
                                <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab' => $this->musictab_id)), $this->translate('Manage Music')) ?>
                            </div>
                        </div>
                    </li>		
                <?php endif; ?>

                <?php //START FOR INRAGRATION WORK WITH OTHER PLUGIN.//  ?>
                <?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/groupintergration_app.tpl'; ?>
                <?php //END FOR INRAGRATION WORK WITH OTHER PLUGIN//  ?>
            </ul>

            <?php if ($canShowMessage): ?>
                <ul class="sitegroup_getstarted">
                    <li> 
                        <div class="tip">
                            <span>
                                <?php if (Engine_Api::_()->sitegroup()->hasPackageEnable()): ?>
                                    <?php
                                    $a = "<a  href='" . $this->url(array('action' => 'update-package', 'group_id' => $this->group_id), 'sitegroup_packages', true) . "'>" . $this->translate('here') . "</a>";
                                    echo $this->translate("Your current package does not provide any apps for your group. Please click %s to upgrade your group package.", $a)
                                    ?>
                                <?php else: ?>
                                    <?php echo $this->translate("Please upgrade member level.") ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    </li>
                </ul>
            <?php endif; ?>
            <?php if (empty($this->is_ajax)) : ?>
            </div>
        </div>
    </div>
<?php endif; ?>