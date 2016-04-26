<script type="text/javascript">
   function follow_campaign()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'ynfundraising/campaign/follow',
            'data' : {
                'id' : <?php echo $this->campaign->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {
                obj = document.getElementById('follow_id');
                obj.innerHTML = '<a class = "buttonlink menu_ynfundraising_unfollow" href="javascript:;" onclick="unfollow_campaign()">' + '<?php echo $this->translate("Unfollow")?>' + '</a>';
            }
        });
        request.send();
   }
   function unfollow_campaign()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'ynfundraising/campaign/un-follow',
            'data' : {
                'id' : <?php echo $this->campaign->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {
                obj = document.getElementById('follow_id');
                obj.innerHTML = '<a class = "buttonlink menu_ynfundraising_follow" href="javascript:;" onclick="follow_campaign()">' + '<?php echo $this->translate("Follow")?>' + '</a>';
            }
        });
        request.send();
   }
   function openPopup(url)
    {    	
     if(window.innerWidth <= 320)
      {
      	
       Smoothbox.open(url, {autoResize : true, width: 300});
      }
     else
      {
       if(window.innerWidth <= 768)
       {
       	Smoothbox.open(url, {autoResize : true, width: (window.innerWidth-20) });
       }
       else{
       	Smoothbox.open(url, {autoResize : true, width: 748 });
       }
       
      }
    }

 </script>
 <div id="profile_options">
	<ul>
	 <?php  $viewer = Engine_Api::_()->user()->getViewer();
	 		$parent = Engine_Api::_()->getApi('core', 'ynfundraising')->getItemFromType($this->campaign->toArray());?>
	    <?php if($this->campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_DRAFT_STATUS || $this->campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS):?>
		    <?php if($this->campaign->authorization()->isAllowed($viewer,'edit')):?>
		    <li>
		    <?php echo $this->htmlLink(array(
		                  'action' => 'edit-step-one',
		                  'campaignId' => $this->campaign->getIdentity(),
		                  'route' => 'ynfundraising_general',
		                  'reset' => true,
		                ), $this->translate('Edit Campaign Details'), array('class'=>'buttonlink menu_ynfundraising_edit'
		                )) ?>
		    </li>
		     <?php endif;?>
		 <?php endif;?>
		 <?php if($this->campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS):?>
		     <?php if($this->campaign->authorization()->isAllowed($viewer,'close') && $this->campaign->isOwner($viewer)):?>
		    <li>
		    <?php echo $this->htmlLink(array(
		                  'action' => 'close',
		                  'campaignId' => $this->campaign->getIdentity(),
		                  'route' => 'ynfundraising_general',
		                  'reset' => true,
		                ), $this->translate('Close Campaign'), array('class'=>'buttonlink menu_ynfundraising_close smoothbox',
		                )) ?>
		    </li>
		    <?php elseif(($parent && $parent->isOwner($viewer)) || $this->campaign->authorization()->isAllowed($viewer,'close')):?>
		    	<li>
			    <?php echo $this->htmlLink(array(
			                  'action' => 'owner-close',
			                  'campaignId' => $this->campaign->getIdentity(),
			                  'route' => 'ynfundraising_general',
			                  'reset' => true,
			                ), $this->translate('Close Campaign'), array('class'=>'buttonlink menu_ynfundraising_close smoothbox',
			                )) ?>
			    </li>
		    <?php endif;?>
	     <?php endif;?>
	     <?php if(($this->campaign->isOwner($viewer) || $viewer->isAdmin()) && $this->campaign->status != Ynfundraising_Plugin_Constants::CAMPAIGN_DRAFT_STATUS): ?>
	    <li>
	    	 <?php echo $this->htmlLink(array(
	                  'action' => 'view-statistics-chart',
	                  'controller' => 'campaign',
	                  'campaign_id' => $this->campaign->getIdentity(),
	                  'route' => 'ynfundraising_extended',
	                  'reset' => true,
	                ), $this->translate('View Statistic'), array('class'=>'buttonlink menu_ynfundraising_statistics'
	                )); ?>
	    </li>
	    <?php endif;?>
	     <?php if($this->campaign->isOwner($viewer) && $this->campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS):?>
	   
	    <li>
	    	
	    <a  href="javascript:void();" class="buttonlink menu_ynfundraising_email" onclick='openPopup("<?php 
	    echo $this->url(array(
	                  'action' => 'email-donors',
	                  'campaignId' => $this->campaign->getIdentity()	              
	                ),'ynfundraising_general')?>")'><?php echo $this->translate('Email Donors')?></a>	
	    	    
	    </li>
	     <?php endif;?>
	    <?php if($viewer->getIdentity() > 0 && $this->campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS):?>
	    <li>
	    	
	    <a  href="javascript:void();" class="buttonlink menu_ynfundraising_invite" onclick='openPopup("<?php 
	    echo $this->url(array(
	                  'action' => 'invite-friends',
	                  'campaignId' => $this->campaign->getIdentity(),
	                  'route' => 'ynfundraising_general',
	                  'reset' => true,	              
	                ),'ynfundraising_general')?>")'><?php echo $this->translate('Invite Friends')?></a>		    	
	    
	    </li>
	    <?php endif;?>
	    <?php if($this->campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS):?>
	     <li>	    
	    <a  href="javascript:void();" class="buttonlink menu_ynfundraising_promote" onclick='openPopup("<?php 
	    echo $this->url(array(
	                  'action' => 'promote',
	                  'campaignId' => $this->campaign->getIdentity()	              
	                ),'ynfundraising_general')?>")'><?php echo $this->translate('Promote Campaign')?></a>	
	    
	    </li>
	    <?php endif;?>
	    <?php if($viewer->getIdentity() > 0 && $viewer->getIdentity() != $this->campaign->getOwner()->getIdentity() && $this->campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS):?>
	    <li id = "follow_id">
		    <?php if($this->campaign->checkFollow($viewer->getIdentity())): ?>
		      <a class = 'buttonlink menu_ynfundraising_follow' href="javascript:;" onclick="follow_campaign()"><?php echo $this->translate('Follow')?></a>
		    <?php
		    else: ?>
		       <a class = 'buttonlink menu_ynfundraising_unfollow' href="javascript:;" onclick="unfollow_campaign()"><?php echo $this->translate('Unfollow')?></a>
		    <?php endif; ?>
	    </li>
	    <?php endif;?>
	</ul>
 </div>
