<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: adsettings.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2 class="fleft"><?php echo $this->translate('Groups / Communities Plugin'); ?></h2>

<?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/manageExtensions.tpl'; ?>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>  

<div class='clear sitegroup_settings_form'>
  <div class='settings'> 
    <?php echo $this->form->render($this); ?>
  </div>
</div> 

<script type="text/javascript">
//var show_community_ad = '<?php //echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1); ?>';
  window.addEvent('domready', function() {
    showads('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1); ?>');
    //showlightboxads('<?php //echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.lightboxads', 1); ?>');
  });

//   function showlightboxads(option) {
//     if(show_community_ad == 1) {
// 			if($('sitegroup_adtype-wrapper')) {
// 				if(option == 1) {
// 					$('sitegroup_adtype-wrapper').style.display = 'block';
// 				}
// 				else {
// 					$('sitegroup_adtype-wrapper').style.display = 'none';
// 				}
// 			}
// 		}
//   }

  function showads(option) {	 	
    if(option == 1) {
//       if($('sitegroup_lightboxads-wrapper')) {
//         $('sitegroup_lightboxads-wrapper').style.display = 'block';
//         if($('sitegroup_lightboxads-1').checked) {
//           $('sitegroup_adtype-wrapper').style.display = 'block';
//         }
//         else {
//           $('sitegroup_adtype-wrapper').style.display = 'none';
//         }
//       }
			if($('sitegroup_admylikes-wrapper')) {
				$('sitegroup_admylikes-wrapper').style.display = 'block';
			}
      if(<?php echo $this->isnoteenabled ?>) {
        $('sitegroup_adnotewidget-wrapper').style.display = 'block';
        $('sitegroup_adnoteview-wrapper').style.display = 'block';
        $('sitegroup_adnotebrowse-wrapper').style.display = 'block';
        $('sitegroup_adnotecreate-wrapper').style.display = 'block';
        $('sitegroup_adnoteedit-wrapper').style.display = 'block';
        $('sitegroup_adnotedelete-wrapper').style.display = 'block';
        $('sitegroup_adnoteaddphoto-wrapper').style.display = 'block';
        $('sitegroup_adnoteeditphoto-wrapper').style.display = 'block';
        $('sitegroup_adnotesuccess-wrapper').style.display = 'block';
      }
		  
      if(<?php echo $this->iseventenabled ?>) {
        $('sitegroup_adeventwidget-wrapper').style.display = 'block';
        $('sitegroup_adeventcreate-wrapper').style.display = 'block';
        $('sitegroup_adeventedit-wrapper').style.display = 'block';
        $('sitegroup_adeventdelete-wrapper').style.display = 'block';
        $('sitegroup_adeventview-wrapper').style.display = 'block';
        $('sitegroup_adeventbrowse-wrapper').style.display = 'block';
        	$('sitegroup_adeventaddphoto-wrapper').style.display = 'block';
				$('sitegroup_adeventeditphoto-wrapper').style.display = 'block';
      }
		  
      if(<?php echo $this->isalbumenabled ?>) {
        $('sitegroup_adalbumwidget-wrapper').style.display = 'block';
        $('sitegroup_adalbumview-wrapper').style.display = 'block';
         $('sitegroup_adalbumbrowse-wrapper').style.display = 'block';
        $('sitegroup_adalbumcreate-wrapper').style.display = 'block';
        $('sitegroup_adalbumeditphoto-wrapper').style.display = 'block';
      }
		  
      if(<?php echo $this->isdiscussionenabled ?>) {
        $('sitegroup_addicussionwidget-wrapper').style.display = 'block';
        $('sitegroup_addiscussionview-wrapper').style.display = 'block';
        $('sitegroup_addiscussioncreate-wrapper').style.display = 'block';
        $('sitegroup_addiscussionreply-wrapper').style.display = 'block';				
      }
	
      if(<?php echo $this->isdocumentenabled ?>) {
        $('sitegroup_addocumentwidget-wrapper').style.display = 'block';
        $('sitegroup_addocumentview-wrapper').style.display = 'block';
        $('sitegroup_addocumentbrowse-wrapper').style.display = 'block';
        $('sitegroup_addocumentcreate-wrapper').style.display = 'block';
        $('sitegroup_addocumentedit-wrapper').style.display = 'block';
        $('sitegroup_addocumentdelete-wrapper').style.display = 'block';		 		
      }
		  
      if(<?php echo $this->isvideoenabled ?>) {
        $('sitegroup_advideowidget-wrapper').style.display = 'block';
        $('sitegroup_advideoview-wrapper').style.display = 'block';
        $('sitegroup_advideobrowse-wrapper').style.display = 'block';
        $('sitegroup_advideocreate-wrapper').style.display = 'block';
        $('sitegroup_advideoedit-wrapper').style.display = 'block';
        $('sitegroup_advideodelete-wrapper').style.display = 'block';			 		
      }
		  
      if(<?php echo $this->ispollenabled ?>) {
        $('sitegroup_adpollwidget-wrapper').style.display = 'block';
        $('sitegroup_adpollview-wrapper').style.display = 'block';
        $('sitegroup_adpollbrowse-wrapper').style.display = 'block';
        $('sitegroup_adpollcreate-wrapper').style.display = 'block';
        $('sitegroup_adpolldelete-wrapper').style.display = 'block';
      }
	
      if(<?php echo $this->isreviewenabled ?>) {
        $('sitegroup_adreviewwidget-wrapper').style.display = 'block';
        $('sitegroup_adreviewcreate-wrapper').style.display = 'block';
        $('sitegroup_adreviewedit-wrapper').style.display = 'block';
        $('sitegroup_adreviewdelete-wrapper').style.display = 'block';			
        $('sitegroup_adreviewview-wrapper').style.display = 'block';	
        $('sitegroup_adreviewbrowse-wrapper').style.display = 'block';		
      }
		  
      if(<?php echo $this->isofferenabled ?>) {
        $('sitegroup_adofferwidget-wrapper').style.display = 'block';		
        $('sitegroup_adoffergroup-wrapper').style.display = 'block';
        $('sitegroup_adofferlist-wrapper').style.display = 'block';
      }
			
      if(<?php echo $this->isformenabled ?>) {
        $('sitegroup_adformwidget-wrapper').style.display = 'block';
        $('sitegroup_adformcreate-wrapper').style.display = 'block';
      }
	
      if(<?php echo $this->isinviteenabled ?>) {
        $('sitegroup_adinvite-wrapper').style.display = 'block';
      }
			
      if(<?php echo $this->isbadgeenabled ?>) {
        $('sitegroup_adbadgeview-wrapper').style.display = 'block';
      }		
			
      if(<?php echo $this->ismoduleenabled ?>) {
        $('sitegroup_adlocationwidget-wrapper').style.display = 'block';
        $('sitegroup_adoverviewwidget-wrapper').style.display = 'block';
        $('sitegroup_adinfowidget-wrapper').style.display = 'block';
        $('sitegroup_adclaimview-wrapper').style.display = 'block';
        $('sitegroup_adtagview-wrapper').style.display = 'block';
      }		
      
      if(<?php echo $this->ismusicenabled ?>) {
        $('sitegroup_admusicwidget-wrapper').style.display = 'block'; 	
        $('sitegroup_admusicview-wrapper').style.display = 'block';
        $('sitegroup_admusicbrowse-wrapper').style.display = 'block';
        $('sitegroup_admusiccreate-wrapper').style.display = 'block';
        $('sitegroup_admusicedit-wrapper').style.display = 'block';
      }
      
	    //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
      if(<?php echo $this->issitegroupintegrationenabled ?>) {
       <?php if(!empty($this->mixSettingsResults)):?>
					<?php	foreach($this->mixSettingsResults as $modNameValue) { ?>
						$('sitegroup_ad_<?php echo $modNameValue['resource_type'] . '_' . $modNameValue['listingtype_id'] ?>-wrapper').style.display = 'block';
					<?php  } ?>
       <?php endif;?>
      }
   	  //END FOR INRAGRATION WORK WITH OTHER PLUGIN.
      if(<?php echo $this->istwitterenabled ?>) {
        $('sitegroup_adtwitterwidget-wrapper').style.display = 'block'; 	
      }
			if(<?php echo $this->ismemberenabled ?>) {
        $('sitegroup_admemberwidget-wrapper').style.display = 'block'; 	
        $('sitegroup_admemberbrowse-wrapper').style.display = 'block'; 	
      }

    } 
    else {
      if($('sitegroup_lightboxads-wrapper')) {
        $('sitegroup_lightboxads-wrapper').style.display = 'none';
        $('sitegroup_adtype-wrapper').style.display = 'none';
      }
			if($('sitegroup_admylikes-wrapper')) {
				$('sitegroup_admylikes-wrapper').style.display = 'none';
			}
      if(<?php echo $this->isnoteenabled ?>) {
        $('sitegroup_adnotewidget-wrapper').style.display = 'none';
        $('sitegroup_adnoteview-wrapper').style.display = 'none';
        $('sitegroup_adnotebrowse-wrapper').style.display = 'none';
        $('sitegroup_adnotecreate-wrapper').style.display = 'none';
        $('sitegroup_adnoteedit-wrapper').style.display = 'none';
        $('sitegroup_adnotedelete-wrapper').style.display = 'none';
        $('sitegroup_adnoteaddphoto-wrapper').style.display = 'none';
        $('sitegroup_adnoteeditphoto-wrapper').style.display = 'none';
        $('sitegroup_adnotesuccess-wrapper').style.display = 'none';
      }
			  
      if(<?php echo $this->iseventenabled ?>) {
        $('sitegroup_adeventwidget-wrapper').style.display = 'none';
        $('sitegroup_adeventcreate-wrapper').style.display = 'none';
        $('sitegroup_adeventedit-wrapper').style.display = 'none';
        $('sitegroup_adeventdelete-wrapper').style.display = 'none';
        $('sitegroup_adeventview-wrapper').style.display = 'none';
        $('sitegroup_adeventbrowse-wrapper').style.display = 'none';
        $('sitegroup_adeventaddphoto-wrapper').style.display = 'none';
				$('sitegroup_adeventeditphoto-wrapper').style.display = 'none';
      }
		  
      if(<?php echo $this->isalbumenabled ?>) {
        $('sitegroup_adalbumwidget-wrapper').style.display = 'none';
        $('sitegroup_adalbumview-wrapper').style.display = 'none';
        $('sitegroup_adalbumbrowse-wrapper').style.display = 'none';
        $('sitegroup_adalbumcreate-wrapper').style.display = 'none';
        $('sitegroup_adalbumeditphoto-wrapper').style.display = 'none';
      }
			  
      if(<?php echo $this->isdiscussionenabled ?>) {
        $('sitegroup_addicussionwidget-wrapper').style.display = 'none';
        $('sitegroup_addiscussionview-wrapper').style.display = 'none';
        $('sitegroup_addiscussioncreate-wrapper').style.display = 'none';
        $('sitegroup_addiscussionreply-wrapper').style.display = 'none';								
      }
		
      if(<?php echo $this->isdocumentenabled ?>) {
        $('sitegroup_addocumentwidget-wrapper').style.display = 'none';
        $('sitegroup_addocumentview-wrapper').style.display = 'none';
        $('sitegroup_addocumentbrowse-wrapper').style.display = 'none';
        $('sitegroup_addocumentcreate-wrapper').style.display = 'none';
        $('sitegroup_addocumentedit-wrapper').style.display = 'none';
        $('sitegroup_addocumentdelete-wrapper').style.display = 'none';
      }
			  
      if(<?php echo $this->isvideoenabled ?>) {
        $('sitegroup_advideowidget-wrapper').style.display = 'none';
        $('sitegroup_advideoview-wrapper').style.display = 'none';
        $('sitegroup_advideobrowse-wrapper').style.display = 'none';
        $('sitegroup_advideocreate-wrapper').style.display = 'none';
        $('sitegroup_advideoedit-wrapper').style.display = 'none';
        $('sitegroup_advideodelete-wrapper').style.display = 'none';			 		
      }
			  
      if(<?php echo $this->ispollenabled ?>) {
        $('sitegroup_adpollwidget-wrapper').style.display = 'none';
        $('sitegroup_adpollview-wrapper').style.display = 'none';
        $('sitegroup_adpollbrowse-wrapper').style.display = 'none';
        $('sitegroup_adpollcreate-wrapper').style.display = 'none';
        $('sitegroup_adpolldelete-wrapper').style.display = 'none';
      }
		
      if(<?php echo $this->isreviewenabled ?>) {
        $('sitegroup_adreviewwidget-wrapper').style.display = 'none';
        $('sitegroup_adreviewcreate-wrapper').style.display = 'none';
        $('sitegroup_adreviewedit-wrapper').style.display = 'none';
        $('sitegroup_adreviewdelete-wrapper').style.display = 'none';
        $('sitegroup_adreviewview-wrapper').style.display = 'none';
        $('sitegroup_adreviewbrowse-wrapper').style.display = 'none';						
      }
		  
      if(<?php echo $this->isofferenabled ?>) {
        $('sitegroup_adofferwidget-wrapper').style.display = 'none';		
        $('sitegroup_adoffergroup-wrapper').style.display = 'none';
        $('sitegroup_adofferlist-wrapper').style.display = 'none';
      }
		  			
      if(<?php echo $this->isformenabled ?>) {
        $('sitegroup_adformwidget-wrapper').style.display = 'none';
        $('sitegroup_adformcreate-wrapper').style.display = 'none';
      }
	
      if(<?php echo $this->isinviteenabled ?>) {
        $('sitegroup_adinvite-wrapper').style.display = 'none';
      }
			
      if(<?php echo $this->isbadgeenabled ?>) {
        $('sitegroup_adbadgeview-wrapper').style.display = 'none';
      }			
				
      if(<?php echo $this->ismoduleenabled ?>) {
        $('sitegroup_adlocationwidget-wrapper').style.display = 'none';
        $('sitegroup_adoverviewwidget-wrapper').style.display = 'none';
        $('sitegroup_adinfowidget-wrapper').style.display = 'none';
        $('sitegroup_adclaimview-wrapper').style.display = 'none';
        $('sitegroup_adtagview-wrapper').style.display = 'none';
      }
      
      if(<?php echo $this->ismusicenabled ?>) {
        $('sitegroup_admusicwidget-wrapper').style.display = 'none'; 	
        $('sitegroup_admusicview-wrapper').style.display = 'none';
        $('sitegroup_admusicbrowse-wrapper').style.display = 'none';
        $('sitegroup_admusiccreate-wrapper').style.display = 'none';
        $('sitegroup_admusicedit-wrapper').style.display = 'none';
       }

	    //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
      if(<?php echo $this->issitegroupintegrationenabled ?>) {
       <?php if(!empty($this->mixSettingsResults)):?>
					<?php	foreach($this->mixSettingsResults as $modNameValue) { ?>
						$('sitegroup_ad_<?php echo $modNameValue['resource_type'] . '_' . $modNameValue['listingtype_id'] ?>-wrapper').style.display = 'none';
					<?php  } ?>
       <?php endif;?>
      }

			if(<?php echo $this->ismemberenabled ?>) {
        $('sitegroup_admemberwidget-wrapper').style.display = 'none'; 	
        $('sitegroup_admemberbrowse-wrapper').style.display = 'none'; 	
      }

	    //END FOR INRAGRATION WORK WITH OTHER PLUGIN.
			if(<?php echo $this->istwitterenabled ?>) {
        $('sitegroup_adtwitterwidget-wrapper').style.display = 'none'; 	
      }
    } 	
  } 
</script>