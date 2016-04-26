<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: group-join.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
   $this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
            . 'application/modules/Seaocore/externals/styles/style_infotooltip.css');
$settings = Engine_Api::_()->getApi('settings', 'core');
?>
<script type="text/javascript">

  var CommentLikesTooltips;
  en4.core.runonce.add(function() {
 // Add hover event to get tool-tip
var feedToolTipAAFEnable=true;
   if(feedToolTipAAFEnable) {
   var show_tool_tip=false;
   var counter_req_pendding=0;
    $$('.sea_add_tooltip_link').addEvent('mouseover', function(event) {  
      var el = $(event.target); 
      ItemTooltips.options.offset.y = el.offsetHeight;
      ItemTooltips.options.showDelay = 0;
        if(!el.hasAttribute("rel")){
                  el=el.parentNode;      
           } 
       show_tool_tip=true;
      if( !el.retrieve('tip-loaded', false) ) {
       counter_req_pendding++;
       var resource='';
      if(el.hasAttribute("rel"))
         resource=el.rel;
       if(resource =='')
         return;
      
        el.store('tip-loaded', true);
        el.store('tip:title', '<div class="" style="">'+
 ' <div class="uiOverlay info_tip" style="width: 300px; top: 0px; ">'+
    '<div class="info_tip_content_wrapper" ><div class="info_tip_content"><div class="info_tip_content_loader">'+
  '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif" alt="Loading" /><?php echo $this->translate("Loading ...") ?></div>'+
'</div></div></div></div>'  
);
        el.store('tip:text', '');       
        // Load the likes
        var url = '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'feed', 'action' => 'show-tooltip-info'), 'default', true) ?>';
        el.addEvent('mouseleave',function(){
         show_tool_tip=false;  
        });       
     
        var req = new Request.HTML({
          url : url,
          data : {
          format : 'html',
          'resource':resource
        },
        evalScripts : true,
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {          
            el.store('tip:title', '');
            el.store('tip:text', responseHTML);
            ItemTooltips.options.showDelay=0;
            ItemTooltips.elementEnter(event, el); // Force it to update the text 
             counter_req_pendding--;
              if(!show_tool_tip || counter_req_pendding>0){               
              //ItemTooltips.hide(el);
              ItemTooltips.elementLeave(event,el);
             }           
            var tipEl=ItemTooltips.toElement();
            tipEl.addEvents({
              'mouseenter': function() {
               ItemTooltips.options.canHide = false;
               ItemTooltips.show(el);
              },
              'mouseleave': function() {                
              ItemTooltips.options.canHide = true;
              ItemTooltips.hide(el);                    
              }
            });
            Smoothbox.bind($$(".sea_add_tooltip_link_tips"));
          }
        });
        req.send();
      }
    });
    // Add tooltips
   var window_size = window.getSize()
   var ItemTooltips = new SEATips($$('.sea_add_tooltip_link'), {
      fixed : true,
      title:'',
      className : 'sea_add_tooltip_link_tips',
      hideDelay :200,
      offset : {'x' : 0,'y' : 0},
      windowPadding: {'x':370, 'y':(window_size.y/2)}
    }); 
  }
  });
var sitetagcheckin_id = '<?php echo $this->sitetagcheckin_id;?>';
</script>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>

<script>
var seaocore_like_url = '<?php echo $this->url(array('action' => 'global-likes' ), 'sitegroup_like', true);?>';
seaocore_content_type = 'sitegroup';
</script>
<?php $viewer = Engine_Api::_()->user()->getViewer()->getIdentity(); ?>

<?php //if(!empty ($this->showViewMore)): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
    hideViewMoreLink();
    });
    
    function getNextGroupViewMoreResults(){
        return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }
    
    function hideViewMoreLink(){
        if($('request_member_pops_view_more'))
            $('request_member_pops_view_more').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>';
    }

    function viewMoreTabMutualFriend()
    {
			var user_id = '<?php echo $this->user_id; ?>';
			document.getElementById('request_member_pops_view_more').style.display ='none';
			document.getElementById('request_member_pops_loding_image').style.display ='';
			en4.core.request.send(new Request.HTML({
				method : 'post',
				'url' : en4.core.baseUrl + 'sitegroupmember/index/group-join/user_id/' + user_id,
				'data' : {
						format : 'html',
						showViewMore : 1,
						group: getNextGroupViewMoreResults()
				},
				onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
					document.getElementById('members_results_friend').innerHTML = document.getElementById('members_results_friend').innerHTML + responseHTML;
					document.getElementById('request_member_pops_view_more').destroy();
					document.getElementById('request_member_pops_loding_image').style.display ='none';
				}
			}));
			return false;
    }
</script>

<?php //endif; ?>

<?php if (empty($this->showViewMore)): ?>
  <div class="seaocore_members_popup seaocore_members_popup_notbs">
    <div class="top">
    <?php $user = Engine_Api::_()->user()->getUser($this->user_id); ?>
      <div class="heading"><?php echo $this->translate('Groups joined by ')?><?php echo $user->displayname ?></div>
    </div>
    <div class="seaocore_members_popup_content" id="members_results_friend">
<?php endif; ?>

<?php if (count($this->paginator) > 0) : ?>
	<?php foreach( $this->paginator as $value ): ?>
		<?php $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $value->group_id); ?>
		<?php
		if(!empty($viewer)) {
		
			$MODULE_NAME = 'sitegroup';
			$RESOURCE_TYPE = 'sitegroup_group';
			$RESOURCE_ID = $sitegroup->group_id;

			// Check that for this 'resurce type' & 'resource id' user liked or not.
			$check_availability = Engine_Api::_()->$MODULE_NAME()->checkAvailability( $RESOURCE_TYPE, $RESOURCE_ID );
			if( !empty($check_availability) )
			{
				$label = 'Unlike this';
				$unlike_show = "display:block;";
				$like_show = "display:none;";
				$like_id = $check_availability[0]['like_id'];
			}
			else
			{
				$label = 'Like this';
				$unlike_show = "display:none;";
				$like_show = "display:block;";
				$like_id = 0;
			}
		}
		?>
		<div class="item_member_list">
			<div class="item_member_thumb">
				<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()), $this->itemPhoto($sitegroup, 'thumb.icon'), array('title' => $sitegroup->getTitle(), 'class'=>'sea_add_tooltip_link', 'rel'=>'sitegroup_group'.' '.$sitegroup->group_id)); ?>
			</div>
			<div class="item_member_details">
				<div class="item_member_name">
					<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()), $sitegroup->getTitle(), array('title' => $sitegroup->getTitle(), 'class'=>'sea_add_tooltip_link', 'rel'=>'sitegroup_group'.' '.$sitegroup->group_id)) ?>
				</div>
        <div class='item_member_stat'>
					<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1);
					if ($sitegroup->member_title && $memberTitle) : ?>
					<?php if ($sitegroup->member_count == 1) : ?><?php echo $sitegroup->member_count . ' member'; ?> <?php else: ?>	<?php echo $sitegroup->member_count . ' ' .  $sitegroup->member_title; ?><?php endif; ?>
					<?php else : ?>
					<?php echo $this->translate(array('%s member', '%s members', $sitegroup->member_count), $this->locale()->toNumber($sitegroup->member_count)) ?>
					<?php endif; ?>
        </div>
        <div class="popup_like_button">
          <?php if(!empty($viewer)) { ?>
            <div class="seaocore_like_button" id="sitegroup_unlikes_<?php echo $RESOURCE_ID;?>" style ='<?php echo $unlike_show;?>' >
              <a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $RESOURCE_ID; ?>', 'sitegroup_group');">
                <i class="seaocore_like_thumbdown_icon"></i>
                <span><?php echo $this->translate('Unlike') ?></span>
              </a>
            </div>
            <div class="seaocore_like_button" id="sitegroup_most_likes_<?php echo $RESOURCE_ID;?>" style ='<?php echo $like_show;?>'>
              <a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $RESOURCE_ID; ?>', 'sitegroup_group');">
                <i class="seaocore_like_thumbup_icon"></i>
                <span><?php echo $this->translate('Like') ?></span>
              </a>
            </div>
            <input type ="hidden" id = "sitegroup_like_<?php echo $RESOURCE_ID;?>" value = '<?php echo $like_id; ?>' />
          <?php } ?>
        </div>
			</div>	
			
		</div>
	<?php endforeach;?>
<?php else : ?>
<div class="tip" id='sitegroupmember_search'>
		  <span>
			  <?php echo $this->translate('No members request.');?>
		  </span>
	</div>
<?php endif; ?>

<?php if (empty($this->showViewMore)):  ?>
	<div class="seaocore_item_list_popup_more" id="request_member_pops_view_more" onclick="viewMoreTabMutualFriend()" >
	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => 'feed_viewmore_link', 'class' => 'buttonlink icon_viewmore' )); ?>
	</div>
	<div class="seaocore_item_list_popup_more" id="request_member_pops_loding_image" style="display: none;">
		<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
			<?php echo $this->translate("Loading ...") ?>
	</div>
<?php //if (empty($this->showViewMore)): ?>
    </div>
  </div>
  <div class="seaocore_members_popup_bottom">
      <button  onclick='smoothboxclose()' ><?php echo $this->translate('Close') ?></button>
  </div>
<?php endif; ?>

<script type="text/javascript">
 function smoothboxclose () {
  parent.Smoothbox.close () ;
 }
</script>