<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getstarted.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (empty($this->is_ajax)) : ?>
<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
	<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>

	<div class="layout_middle">
		<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/edit_tabs.tpl'; ?>
		<div class="sitegroup_edit_content">
			<div class="sitegroup_edit_header"> 
				<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroup->group_id, $this->sitegroup->owner_id, $this->sitegroup->getSlug()),$this->translate('VIEW_GROUP')) ?>
				<h3><?php echo $this->translate('Dashboard: ').$this->sitegroup->title; ?></h3>
			</div>
			<div id="show_tab_content">
     <?php endif; ?>
	<?php $canShowMessage=true;?>
    <div class="sitegroup_getstarted_head">
    	<?php  echo $this->translate('Welcome to your Group. Let\'s get started!'); ?>
    </div>
    <ul class="sitegroup_getstarted">
      <?php $i = 1; ?>
      <?php if($this->photo_id == 0): ?>
				<li>
					<div class="sitegroup_getstarted_num">
						<div>
							<?php echo $i; $i++;?> 
						</div>
					</div>
					<div class="sitegroup_getstarted_des">
						<b><?php echo $this->translate('Add an image'); ?></b>
						<p><?php echo $this->translate('Make your Group more recognized by adding an image as it\'s profile picture.'); ?></p><br />
						<div class="sitegroup_getstarted_upload">
							<div class="fleft">
								<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/nophoto_group_thumb_profile.png" alt="" class="photo" />
							</div>
							<div class="sitegroup_getstarted_upload_options">
								<a href='<?php echo $this->url(array('action' => 'profile-picture', 'group_id' => $this->group_id), 'sitegroup_dashboard', true) ?>'><?php echo $this->translate('Upload an image'); ?></a>
							</div>
						</div>
					</div>
				</li>
      <?php endif;?>
  
       <?php if($this->updatestab_id): ?>
				<li> <?php $canShowMessage=false;?>
          <div class="sitegroup_getstarted_num">
						<div>
							<?php echo $i; $i++;?> 
						</div>
          </div>
					<div class="sitegroup_getstarted_des">
						<b><?php echo $this->translate('Post updates'); ?></b>
						<p><?php echo $this->translate("Share your updates and latest news with the visitors of this group."); ?></p>
						<div class="sitegroup_getstarted_btn">
							<a href='<?php echo $this->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($this->group_id)),'sitegroup_entry_view', true) ?>'><?php echo $this->translate('Post Update');?></a>
						</div>
					</div>
				</li>		
      <?php endif; ?>
  
      <?php if($this->overviewPrivacy): ?>
				<li> <?php $canShowMessage=false;?>
          <div class="sitegroup_getstarted_num">
						<div>
							<?php echo $i; $i++;?> 
						</div>
          </div>
					<div class="sitegroup_getstarted_des">
						<b><?php echo $this->translate('Create Rich Overview'); ?></b>
						<p><?php echo $this->translate('Create a rich profile for your Group.'); ?></p>
						<div class="sitegroup_getstarted_btn">
							<a href='<?php echo $this->url(array('action' => 'overview', 'group_id' => $this->group_id), 'sitegroup_dashboard', true) ?>'><?php echo $this->translate('Edit Overview');?></a>
						</div>
					</div>
				</li>		
      <?php endif; ?>  

      <?php if($this->can_invite): ?>
				<li> <?php $canShowMessage=false;?>
					<div class="sitegroup_getstarted_num">
						<div>
							<?php echo $i; $i++;?>
						</div>
					</div>
					<div class="sitegroup_getstarted_des">
							<b><?php echo $this->translate('Promote to your fans'); ?></b>
							<p><?php echo $this->translate('Tell your friends, fans and customers about this group and make it popular.'); ?></p>
							<div class="sitegroup_getstarted_btn">
								<a href='<?php echo $this->url(array('user_id' => $this->viewer_id,'sitegroup_id' => $this->group_id), 'sitegroupinvite_invite', true) ?>'><?php echo $this->translate('Invite Friends &amp; Fans');?></a>
							</div>
				  </div>
				</li>
			<?php endif; ?>

      <?php if($this->moduleEnable && !empty($this->can_offer)): ?>
				<li> <?php $canShowMessage=false;?>
					<div class="sitegroup_getstarted_num">
						<div>
							<?php echo $i; $i++;?> 
						</div>
          </div>
					<div class="sitegroup_getstarted_des">
						<b><?php echo $this->translate('Create Offers'); ?></b>
						<p><?php echo $this->translate('Create and display attractive offers on your group.'); ?></p>
						<div class="sitegroup_getstarted_btn">
							<a href='<?php echo $this->url(array('action' => 'create','group_id' => $this->group_id, 'tab' => $this->offertab_id),'sitegroupoffer_general', true) ?>'><?php echo $this->translate('Add an Offer');?></a>
						</div>
					</div>
				</li>
      <?php endif; ?>

		  <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')):?>
        <?php if(!empty($this->allowed_upload_photo)): ?>
    		  <li> <?php $canShowMessage=false;?>
	      		<div class="sitegroup_getstarted_num">
	      			<div>
                <?php echo $i; $i++;?> 
              </div>
	      		</div>
	      		<div class="sitegroup_getstarted_des">
		        	<b><?php echo $this->translate('Add more photos'); ?></b>
							<p><?php echo $this->translate('Create albums and add photos for this group.'); ?></p>
							<div class="sitegroup_getstarted_btn">
							<a href='<?php echo $this->url(array('group_id' => $this->sitegroup->group_id, 'album_id' => $this->default_album_id, 'tab' => $this->albumtab_id), 'sitegroup_photoalbumupload', true) ?>'><?php echo $this->translate('Add Photos'); ?></a>
							</div>
						</div>	
	     	  </li> 
        <?php endif;?>
      <?php endif;?>
   
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
	      			<div>
                <?php echo $i; $i++;?> 
              </div>
	      		</div>
                        <div class="sitegroup_getstarted_des">
                            <b><?php echo $this->translate('Videos'); ?></b>
                            <p><?php echo $this->translate('Add and share videos for this group.'); ?></p>
                            <div class="sitegroup_getstarted_btn">
                                <?php if (Engine_Api::_()->sitevideo()->openPostNewVideosInLightbox()): ?>
                                <a class="seao_smoothbox" data-SmoothboxSEAOClass="seao_add_video_lightbox"  href='<?php echo $this->url(array('action' => 'create', 'tab' => $this->videotab_id, 'parent_type' => 'sitegroup_group', 'parent_id' => $this->group_id,), 'sitevideo_video_general', true) ?>'><?php echo $this->translate('Post a Video'); ?></a>
                                <?php else:?>
                                <a href='<?php echo $this->url(array('action' => 'create', 'tab' => $this->videotab_id, 'parent_type' => 'sitegroup_group', 'parent_id' => $this->group_id,), 'sitevideo_video_general', true) ?>'><?php echo $this->translate('Post a Video'); ?></a>
                                <?php endif;?>
                                <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab' => $this->videotab_id)), $this->translate('Manage Videos')) ?>
                            </div>
                        </div>
                    </li>
                    <?php endif;?>
                <?php endif; ?>

      <?php if($this->can_create_doc): ?>
                    
                    <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument') && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')): ?> 
      	<li> <?php $canShowMessage=false;?>
	    		<div class="sitegroup_getstarted_num">
	    			<div>
              <?php echo $i; $i++;?> </div>
	    		  </div>
	    		<div class="sitegroup_getstarted_des">
						<b><?php echo $this->translate('Add New Documents'); ?></b>
						<p><?php echo $this->translate('Add and showcase documents on your group.'); ?></p>
						<div class="sitegroup_getstarted_btn">
							<a href='<?php echo $this->url(array('group_id' => $this->group_id, 'tab' => $this->documenttab_id), 'sitegroupdocument_create', true) ?>'><?php echo $this->translate('Add a Document');?></a>
						</div>
					</div>
				</li>	
                                <?php else:?>
                                <li> <?php $canShowMessage=false;?>
	    		<div class="sitegroup_getstarted_num">
	    			<div>
              <?php echo $i; $i++;?> </div>
	    		  </div>
	    		<div class="sitegroup_getstarted_des">
						<b><?php echo $this->translate('Add New Documents'); ?></b>
						<p><?php echo $this->translate('Add and showcase documents on your group.'); ?></p>
						<div class="sitegroup_getstarted_btn">
							<a href='<?php echo $this->url(array('parent_type' => 'sitegroup_group', 'parent_id' => $this->group_id, 'tab' => $this->documenttab_id), 'document_create', true) ?>'><?php echo $this->translate('Add a Document');?></a>
						</div>
					</div>
				</li>	
                                <?php endif;?>
      <?php endif; ?>
    
      <?php if($this->can_create_notes): ?>
      	<li> <?php $canShowMessage=false;?>
	    		<div class="sitegroup_getstarted_num">
	    			<div>
              <?php echo $i; $i++;?> </div>
	    		  </div>
	    		<div class="sitegroup_getstarted_des">
						<b><?php echo $this->translate('Write Notes'); ?></b>
						<p><?php echo $this->translate('Share updates and lots more by publishing notes in this blog-like section of your group.'); ?></p>
						<div class="sitegroup_getstarted_btn">
							<a href='<?php echo $this->url(array('group_id' => $this->group_id, 'tab' => $this->notetab_id), 'sitegroupnote_create', true) ?>'><?php echo $this->translate('Write a Note');?></a>
						</div>
					</div>
				</li>		
      <?php endif; ?>
     
      <?php if($this->can_create_event): ?>
        <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')):?>
					<li> <?php $canShowMessage=false;?>
						<div class="sitegroup_getstarted_num">
							<div>
								<?php echo $i;$i++;?> </div>
							</div>
						<div class="sitegroup_getstarted_des">
							<b><?php echo $this->translate('Create New Events'); ?></b>
							<p><?php echo $this->translate('Organize events for this group.'); ?></p>
							<div class="sitegroup_getstarted_btn">
								<a href='<?php echo $this->url(array('group_id' => $this->group_id, 'tab_id' => $this->eventtab_id), 'sitegroupevent_create', true) ?>'><?php echo $this->translate('Create an Event');?></a>
							</div>
						</div>
					</li>		
				<?php else:?>
					<li> <?php $canShowMessage=false;?>
						<div class="sitegroup_getstarted_num">
							<div>
								<?php echo $i;$i++;?> </div>
							</div>
						<div class="sitegroup_getstarted_des">
							<b><?php echo $this->translate('Create New Events'); ?></b>
							<p><?php echo $this->translate('Organize events for this group.'); ?></p>
							<div class="sitegroup_getstarted_btn">
                <?php if (isset($this->siteeventVersion) && Engine_Api::_()->siteevent()->hasPackageEnable()):?>
                <a href='<?php echo $this->url(array('action' =>'index','parent_type' => 'sitegroup_group', 'parent_id' => $this->group_id, 'tab_id' => $this->eventtab_id), 'siteevent_package', true) ?>'><?php echo $this->translate('Create an Event');?></a>
                <?php else:?>
								<a href='<?php echo $this->url(array('action' =>'create','parent_type' => 'sitegroup_group', 'parent_id' => $this->group_id, 'tab_id' => $this->eventtab_id), 'siteevent_general', true) ?>'><?php echo $this->translate('Create an Event');?></a>
                <?php endif; ?>	
								
							</div>
						</div>
					</li>
				<?php endif;?>
      <?php endif; ?>

      <?php if($this->can_create_poll): ?>
      	<li> <?php $canShowMessage=false;?>
	    		<div class="sitegroup_getstarted_num">
	    			<div>
              <?php echo $i; $i++;?>
            </div>
	    		</div>
	    		<div class="sitegroup_getstarted_des">
						<b><?php echo $this->translate('Create New Polls'); ?></b>
						<p><?php echo $this->translate('Get feedback from visitors to your group.'); ?></p>
						<div class="sitegroup_getstarted_btn">
							<a href='<?php echo $this->url(array('group_id' => $this->group_id,'tab' => $this->polltab_id), 'sitegrouppoll_create', true) ?>'><?php echo $this->translate('Create a Poll');?></a>
						</div>
					</div>
				</li>		
      <?php endif; ?>
      
      <?php if($this->can_create_discussion):?>
      	<li> <?php $canShowMessage=false;?>
	    		<div class="sitegroup_getstarted_num">
	    			<div>
            <?php echo $i; $i++;?> </div>
	    		  </div>
	    		<div class="sitegroup_getstarted_des">
			      <b><?php echo $this->translate('Post New Topics'); ?></b>
			      <p><?php echo $this->translate('Enable interactions and information sharing on your group using threaded discussions.'); ?></p>
			      <div class="sitegroup_getstarted_btn">
							<?php echo $this->htmlLink(array(
								'route' => 'sitegroup_extended',
								'controller' => 'topic',
								'action' => 'create',
								'subject' => $this->subject()->getGuid(),
							   'tab' => $this->discussiontab_id,
							    'group_id' => $this->group_id
							), $this->translate('Post a Topic')) ?>
			     	</div>
			    </div>	
			  </li>  
     	<?php endif; ?>
     	
      <?php if($this->can_create_musics): ?>
      	<li> <?php $canShowMessage=false;?>
	    		<div class="sitegroup_getstarted_num">
	    			<div>
              <?php echo $i; $i++;?> </div>
	    		  </div>
	    		<div class="sitegroup_getstarted_des">
						<b><?php echo $this->translate('Upload Music'); ?></b>
						<p><?php echo $this->translate('Add and share music for this group.'); ?></p>
						<div class="sitegroup_getstarted_btn">
							<a href='<?php echo $this->url(array('group_id' => $this->group_id, 'tab' => $this->musictab_id), 'sitegroupmusic_create', true) ?>'><?php echo $this->translate('Upload Music');?></a>
						</div>
					</div>
				</li>		
      <?php endif; ?>

      <?php if($this->option_id && !empty ($this->can_form)): ?>
				<li> <?php $canShowMessage=false;?>
          <div class="sitegroup_getstarted_num">
						<div>
							<?php echo $i; $i++;?> 
						</div>
          </div>
					<div class="sitegroup_getstarted_des">
						<b><?php echo $this->translate('Configure your Form'); ?></b>
						<p><?php echo $this->translate('Gather useful information from visitors by creating your form with relevant questions.'); ?></p>
						<div class="sitegroup_getstarted_btn">
							 <?php $canAddquestions = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupform.add.question', 1);?>
							<a href='<?php echo $this->url(array('action' => 'index','option_id' => $this->option_id,'group_id' => $this->group_id, 'tab' => $this->formtab_id),'sitegroupform_general', true) ?>'><?php if($canAddquestions):?><?php echo $this->translate('Add a Question');?><?php else:?><?php echo $this->translate('Manage Form');?><?php endif;?></a>
						</div>
					</div>
				</li>		
      <?php endif; ?>

			<?php //START FOR INRAGRATION WORK WITH OTHER PLUGIN. ?>
        <?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/groupintergration_getstarted.tpl'; ?>
      <?php //START FOR INRAGRATION WORK WITH OTHER PLUGIN. ?>
    </ul>

    <?php if($canShowMessage): ?>
    <ul class="sitegroup_getstarted">
      <li>
        <div class="tip">
          <span>
            <?php  if (Engine_Api::_()->sitegroup()->hasPackageEnable()): ?>
              <?php echo $this->translate("Please click	<a  href='".$this->url(array('action' => 'update-package', 'group_id' => $this->group_id), 'sitegroup_packages', true)."'>". $this->translate('here')."</a> for upgrading the package of your Group.")?>
            <?php else:?>
              <?php echo $this->translate("Please upgrade your member level.")?>
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
     </div>
  </div>
<?php endif; ?>
