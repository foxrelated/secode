<script type="text/javascript">
   function follow_idea()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'ynidea/ideas/follow',
            'data' : {
                'id' : <?php echo $this->idea->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {
                obj = document.getElementById('follow_id');
                obj.innerHTML = '<a class = "buttonlink menu_ynidea_unfollow" href="javascript:;" onclick="unfollow_idea()">' + '<?php echo $this->translate("Unfollow")?>' + '</a>';
            }
        });
        request.send();
   }
   function unfollow_idea()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'ynidea/ideas/un-follow',
            'data' : {
                'id' : <?php echo $this->idea->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {
                obj = document.getElementById('follow_id');
                obj.innerHTML = '<a class = "buttonlink menu_ynidea_follow" href="javascript:;" onclick="follow_idea()">' + '<?php echo $this->translate("Follow")?>' + '</a>';
            }
        });
        request.send();
   }
   function favourite_idea()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'ynidea/ideas/favourite',
            'data' : {
                'id' : <?php echo $this->idea->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {
                obj = document.getElementById('favourite_id');
                obj.innerHTML = '<a class = "buttonlink menu_ynidea_unfavourite" href="javascript:;" onclick="unfavourite_idea()">' + '<?php echo $this->translate("Unfavourite")?>' + '</a>';
            }
        });
        request.send();
   }
   function unfavourite_idea()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'ynidea/ideas/un-favourite',
            'data' : {
                'id' : <?php echo $this->idea->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {
                obj = document.getElementById('favourite_id');
                obj.innerHTML = '<a class = "buttonlink menu_ynidea_favourite" href="javascript:;" onclick="favourite_idea()">' + '<?php echo $this->translate("Favourite")?>' + '</a>';
            }
        });
        request.send();
   }
 </script>
<div id="profile_options">
<ul style="margin-bottom: 15px">
 <?php  $viewer = Engine_Api::_()->user()->getViewer(); ?>
 <?php if($this->idea->authorization()->isAllowed($viewer,'edit') && $viewer->getIdentity() > 0):?>
    <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'edit-new-version',
                  'id' => $this->idea->getIdentity(),
                  'route' => 'ynidea_specific',
                  'reset' => true,
                ), $this->translate('Edit New Version'), array('class'=>'buttonlink menu_ynidea_edit',
                )) ?>
    </li>
    <?php if($this->idea->publish_status == 'draft'): ?>
    <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'publish',
                  'id' => $this->idea->getIdentity(),
                  'route' => 'ynidea_specific',
                  'reset' => true,
                ), $this->translate('Publish New Version'), array('class'=>'buttonlink menu_ynidea_publish smoothbox'
                )) ?>
    </li>
    <?php endif; ?>
    <?php if($this->idea->isOwner($viewer)):?>
    <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'assign',
                  'id' => $this->idea->getIdentity(),
                  'route' => 'ynidea_specific',
                  'reset' => true,
                ), $this->translate('Assign Co-Authors'), array('class'=>'buttonlink menu_ynidea_assign smoothbox'
                )) ?>
    </li>
    <!--
    <?php if($this->manageauthor):?>
	    <li>
	    <?php echo $this->htmlLink(array(
	                  'action' => 'manage-authors',
	                  'id' => $this->idea->getIdentity(),
	                  'route' => 'ynidea_specific',
	                  'reset' => true,
	                ), $this->translate('Manage Co-Authors'), array('class'=>'buttonlink menu_ynidea_manageauthors smoothbox'
	                )) ?>
	    </li>
    <?php endif; ?>
    -->
     <?php endif;?>

     <?php endif;?>
     <?php if($this->idea->authorization()->isAllowed($viewer,'delete') && $viewer->getIdentity() > 0 && $this->idea->isOwner($viewer)):?>
    <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'delete',
                  'id' => $this->idea->getIdentity(),
                  'route' => 'ynidea_specific',
                  'reset' => true,
                ), $this->translate('Delete'), array('class'=>'buttonlink menu_ynidea_delete smoothbox',
                )) ?>
    </li>
     <?php endif;?>    
    
    <?php 
    $db = Engine_Db_Table::getDefaultAdapter();
	$select = "SELECT * FROM engine4_core_modules WHERE name = 'ynresponsive1'";
	$module = $db->fetchRow($select);
	
	$mobile = 0;
	if($module['enabled'])
    	$mobile = Engine_Api::_()->ynresponsive1()->isMobile();
    ?>
     
    <?php if (!$mobile) : ?>
    <li>
    	<a href="javascript:;" class="buttonlink menu_ynidea_print" onclick="window.print()" > <?php echo $this->translate('Print');?> </a>
    </li>
    <?php endif; ?>
    <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'download-pdf',
                  'id' => $this->idea->getIdentity(),
                  'route' => 'ynidea_specific',
                  'reset' => true,
                ), $this->translate('PDF'), array('class'=>'buttonlink menu_ynidea_download'
                )); ?>
    </li>
    <!--
    <li id = "favourite_id">
    <?php if($this->idea->checkFavourite()): ?>
      <a class = 'buttonlink menu_ynidea_favourite' href="javascript:;" onclick="favourite_idea()"><?php echo $this->translate('Favourite')?></a>
    <?php
    else: ?>
       <a class = 'buttonlink menu_ynidea_unfavourite' href="javascript:;" onclick="unfavourite_idea()"><?php echo $this->translate('Unfavourite')?></a>
    <?php
    endif;
    ?>
    </li>
    -->
    <?php if($viewer->getIdentity() > 0):?>
    <li id = "follow_id">
    <?php if($this->idea->checkFollow()): ?>
      <a class = 'buttonlink menu_ynidea_follow' href="javascript:;" onclick="follow_idea()"><?php echo $this->translate('Follow')?></a>
    <?php
    else: ?>
       <a class = 'buttonlink menu_ynidea_unfollow' href="javascript:;" onclick="unfollow_idea()"><?php echo $this->translate('Unfollow')?></a>
    <?php
    endif;
    ?>
    </li>
    <?php endif;?>
    <?php if($viewer->getIdentity() > 0 ):?>
    <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'history',
                  'id' => $this->idea->getIdentity(),
                  'route' => 'ynidea_specific',
                  'reset' => true,
                ), $this->translate('Version History'), array('class'=>'buttonlink menu_ynidea_history'
                )) ?>
    </li>
    <?php if(Ynidea_Api_Core::checkFundraisingPlugin() && $this->idea->publish_status == "publish" && Engine_Api::_()->authorization()->isAllowed('ynfundraising_campaign', $viewer, 'create')):
    	if(!$this->idea->checkExistCampaign() && !$this->idea->checkExistRequestApproved()):?>
    		<?php if($this->idea->isOwner($viewer)):?>
    			<li>
    				<a class="smoothbox buttonlink menu_ynidea_campaign" href="<?php echo $this->url(array('action'=>'confirm-create','parent_id'=>$this->idea->getIdentity(),'parent_type'=>'idea'),'ynfundraising_general')?>"><?php echo $this->translate("Create Campaign")?></a>
    			</li>
    		<?php elseif($this->idea->allow_campaign == 1):
    			$request = $this->idea->checkExistRequest();?>
    				<?php if(!$request):?>
    					<li>
    						<a class="smoothbox buttonlink menu_ynidea_campaign" href="<?php echo $this->url(array('action'=>'request-create','parent_id'=>$this->idea->getIdentity(),'parent_type'=>'idea'),'ynfundraising_general')?>"><?php echo $this->translate("Create Campaign")?></a>
    					</li>
					<?php elseif($request && $request->status != 'denied'):?>
						<li>
							<a class="smoothbox buttonlink menu_ynidea_cancel_request" href="<?php echo $this->url(array('action'=>'cancel-request','request_id'=>$this->idea->checkExistRequest()->request_id),'ynfundraising_general')?>"><?php echo $this->translate("Cancel Request")?></a>
		    		    </li>
					<?php endif;?>
    		<?php endif;?>

    <?php endif; endif;?>
    <?php endif;?>
     <li>
     <?php  echo $this->htmlLink(array(
                  'action' => 'report',
                  'id' => $this->idea->getIdentity(),
                  'route' => 'ynidea_specific',
                  'reset' => true,
                ), $this->translate('Report'), array('class' => 'smoothbox buttonlink menu_ynidea_report'
                )); ?>
      </li>
      <li>
      	<?php echo $this->translate("Idea's ID"); ?>: <?php echo $this->idea->getIdentity();?>
      </li>
    </ul>
    <div>
    </div>
 </div>
