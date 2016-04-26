<script type="text/javascript">
   function favourite_trophy()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'ynidea/trophies/favourite',
            'data' : {
                'id' : <?php echo $this->trophy->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {  
                obj = document.getElementById('favourite_id');
                obj.innerHTML = '<a class = "buttonlink menu_ynidea_unfavourite" href="javascript:;" onclick="unfavourite_trophy()">' + '<?php echo $this->translate("Unfavourite")?>' + '</a>';
            }
        });
        request.send();  
   } 
   function unfavourite_trophy()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'ynidea/trophies/un-favourite',
            'data' : {
                'id' : <?php echo $this->trophy->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {  
                obj = document.getElementById('favourite_id');
                obj.innerHTML = '<a class = "buttonlink menu_ynidea_favourite" href="javascript:;" onclick="favourite_trophy()">' + '<?php echo $this->translate("Favourite")?>' + '</a>';
            }
        });
        request.send();  
   } 
   function enable_trophy()
   {
   	   if(!confirm("<?php echo $this->translate('Are you sure you want to enable voting on this trophy?')?>"))
   	   		return false;
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'ynidea/trophies/enable-voting',
            'data' : {
                'id' : <?php echo $this->trophy->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {  
            	location.reload(); 
                obj = document.getElementById('enable_id');
                obj.innerHTML = '<a class = "buttonlink menu_ynidea_disable" href="javascript:;" onclick="disable_trophy()">' + '<?php echo $this->translate("Finish Voting")?>' + '</a>';
                if(document.getElementById('trophy_status'))
                {
                	document.getElementById('trophy_status').innerHTML = '<?php echo $this->translate("voting") ?>';	
                }
            }
        });
        request.send();  
   } 
   function disable_trophy()
   {
   	   if(!confirm("<?php echo $this->translate('Are you sure you want to finish voting on this trophy?')?>"))
   	   		return false;
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'ynidea/trophies/disable-voting',
            'data' : {
                'id' : <?php echo $this->trophy->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {  
            	location.reload(); 
                obj = document.getElementById('enable_id');
                obj.innerHTML = '<a class = "buttonlink menu_ynidea_enable" href="javascript:;" onclick="enable_trophy()">' + '<?php echo $this->translate("Enable Voting")?>' + '</a>';
                if(document.getElementById('trophy_status'))
                {
                	document.getElementById('trophy_status').innerHTML = '<?php echo $this->translate("finished") ?>';	
                }
            }
        });
        request.send();  
   } 
 </script> 
<div id="profile_options">
<ul style="margin-bottom: 15px">
 <?php  $viewer = Engine_Api::_()->user()->getViewer(); ?>
 <?php if($this->trophy->authorization()->isAllowed($viewer,'edit') && $viewer->getIdentity() > 0 && $this->trophy->user_id == $viewer->getIdentity()):?>
    <li  id = "enable_id">
    <?php if($this->trophy->status == 'pending'): ?>
      <a class = 'buttonlink menu_ynidea_enable' href="javascript:;" onclick="enable_trophy()"><?php echo $this->translate('Enable Voting')?></a>
    <?php
	elseif($this->trophy->status == 'voting'): ?>
       <a class = 'buttonlink menu_ynidea_disable' href="javascript:;" onclick="disable_trophy()"><?php echo $this->translate('Finish Voting')?></a>
    <?php
	else: ?>
	<a class = 'buttonlink menu_ynidea_enable' href="javascript:;" onclick="enable_trophy()">
		<?php echo $this->translate("Enable Voting"); ?>
	</a>
    <?php endif; 
    ?>
    </li>
    <li>
    	<?php echo $this->htmlLink(array(
                  'action' => 'reset-votes',
                  'id' => $this->trophy->getIdentity(),
                  'route' => 'ynidea_trophies',
                  'reset' => true,
                ), $this->translate("Reset All Votes"), array('class'=>'buttonlink menu_ynidea_reset smoothbox')) ?> 
    </li>
  <?php endif;?>
    <?php if($this->trophy->user_id == $viewer->getIdentity() && !$this->trophy->checkJudges($viewer)):?>
    <li>
    		<?php echo $this->htmlLink(array(
                  'action' => 'judge',
                  'id' => $this->trophy->getIdentity(),
                  'route' => 'ynidea_trophies',
                  'reset' => true,
                ), $this->translate("I'm a Judge"), array('class'=>'buttonlink menu_ynidea_judge smoothbox')) ?> 
    </li>
    <?php endif;?> 	
    
     <?php if($this->trophy->authorization()->isAllowed($viewer,'delete') && $viewer->getIdentity() > 0 && $this->trophy->user_id == $viewer->getIdentity()):?>
    <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'delete',
                  'id' => $this->trophy->getIdentity(),
                  'route' => 'ynidea_trophies',
                  'reset' => true,
                ), $this->translate('Delete Trophy'), array('class'=>'buttonlink menu_ynidea_delete smoothbox',
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
    	<a href="javascript:;" class="buttonlink menu_ynidea_print" onclick="window.print()"> <?php echo $this->translate('Print');?> </a>
    </li>
    <?php endif; ?>
    <?php if(Ynidea_Api_Core::checkFundraisingPlugin() && $viewer->getIdentity() > 0 && Engine_Api::_()->authorization()->isAllowed('ynfundraising_campaign', $viewer, 'create')):
    	if(!$this->trophy->checkExistCampaign()):?>
    		<?php if($this->trophy->isOwner($viewer)):?>
    			<li>
    			<a class="smoothbox buttonlink menu_ynidea_campaign" href="<?php echo $this->url(array('action'=>'confirm-create','parent_id'=>$this->trophy->getIdentity(),'parent_type'=>'trophy'),'ynfundraising_general')?>"><?php echo $this->translate("Create Campaign")?></a>
    			</li>
    		<?php elseif($this->trophy->allow_campaign == 1):
    			$request = $this->trophy->checkExistRequest();?>
    			<li>
    				<?php if(!$request):?>
    					<a class="smoothbox buttonlink menu_ynidea_campaign" href="<?php echo $this->url(array('action'=>'request-create','parent_id'=>$this->trophy->getIdentity(),'parent_type'=>'trophy'),'ynfundraising_general')?>"><?php echo $this->translate("Create Campaign")?></a>
    				<?php elseif($request && $request->status != 'denied'):?>
    					<a class="smoothbox buttonlink menu_ynidea_cancel_request" href="<?php echo $this->url(array('action'=>'cancel-request','request_id'=>$this->trophy->checkExistRequest()->request_id),'ynfundraising_general')?>"><?php echo $this->translate("Cancel Request")?></a>
    				<?php endif;?>	
    			</li>
    		<?php endif;?>
    <?php endif; endif;?>
    <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'download-pdf',
                  'id' => $this->trophy->getIdentity(),
                  'route' => 'ynidea_trophies',
                  'reset' => true,
                ), $this->translate('PDF'), array('class'=>'buttonlink menu_ynidea_download'
                )); ?>
    </li>  
    <!--<li id = "favourite_id">
    <?php if($this->trophy->checkFavourite()): ?>
      <a class = 'buttonlink menu_ynidea_favourite' href="javascript:;" onclick="favourite_trophy()"><?php echo $this->translate('Favourite')?></a>
    <?php
    else: ?>
       <a class = 'buttonlink menu_ynidea_unfavourite' href="javascript:;" onclick="unfavourite_trophy()"><?php echo $this->translate('Unfavourite')?></a>
    <?php
    endif; 
    ?>
    </li>  -->
      <li>
      	<?php echo $this->translate("Trophy's ID"); ?>: <?php echo $this->trophy->getIdentity();?>
      </li>
    </ul> 
    <div>
    </div>
 </div>     
