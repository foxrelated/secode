<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: memberlike.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/scripts/core.js'); ?>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<?php include APPLICATION_PATH . '/application/modules/Sitelike/views/scripts/settings_css.tpl' ; ?>
<a id="like_members_profile" class="pabsolute"></a>

<script type="text/javascript">
// Function for Searching.
 var likeMemberPage = <?php if(empty($this->no_result_msg)){ echo sprintf('%d', $this->user_obj->getCurrentPageNumber()); } else { echo 1; } ?>;
 var call_status = '<?php echo $this->call_status; ?>';
 var resource_id = '<?php echo $this->resource_id; ?>';// Resource Id which are send to controller in the 'pagination' & 'searching'.
 var resource_type = '<?php echo $this->resource_type; ?>';// Resource Type which are send to controller in the 'pagination' & 'searching'.
 var url = en4.core.baseUrl + 'sitelike/index/memberlike';// URL where send ajax request.
 en4.core.runonce.add(function() {
		// Code for 'searching', where send the request and set the result which are return.
    document.getElementById('like_members_search_input').addEvent('keyup', function(e) {
		$('like_memberlikeme_content').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitelike/externals/images/spinner.gif" alt="" style="margin-top:50px;" /></center>';
  
       var request = new Request.HTML({
      'url' : url,
        'data' : {
          'format' : 'html',
					'resource_type' : resource_type,
					'resource_id' : resource_id,
					'call_status' : call_status,
          'search' : this.value,
					'is_ajax':1
        },
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
			document.getElementById('like_memberlikeme_content').innerHTML = responseHTML;
			en4.core.runonce.trigger();
			}
				});
									
        request.send();

    });
  });

 // Code for 'Pagination' which decide that how many entry will show in popup.
 var paginateLikeMembers = function(page, call_status_local) {
		var search_value = $('like_members_search_input').value;
		if (search_value == '') {
			search_value = '';
		}
		if (call_status) {
			document.getElementById('show_all_' + call_status).erase('class');
		}
		$('like_memberlikeme_content').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitelike/externals/images/spinner.gif" alt="" style="margin-top:50px;" /></center>';

   var request = new Request.HTML({
     'url' : url,
      'data' : {
        'format' : 'html',
				'resource_type' : resource_type,
				'resource_id' : resource_id,
        'search' : search_value,
				'call_status' : call_status_local,
        'page' : page,
				'is_ajax':1
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				document.getElementById('like_memberlikeme_content').innerHTML = responseHTML;
				document.getElementById('show_all_' + call_status_local).set('class', 'selected');
        en4.core.runonce.trigger();
			}
				});
									
        request.send();

  }

 //Showing 'friend' which liked this content.
 var likedStatus = function(call_status_local, thisobj) {
	if (call_status) {
		document.getElementById('show_all_' + call_status).erase('class');
	}
		$('like_memberlikeme_content').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitelike/externals/images/spinner.gif" alt="" style="margin-top:50px;" /></center>';


   var request = new Request.HTML({
           'url' : url,
      'data' : {
        'format' : 'html',
				'resource_type' : resource_type,
				'resource_id' : resource_id,
				'call_status' : call_status_local,
				'is_ajax':1
      },
              onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
								document.getElementById('like_memberlikeme_content').innerHTML = responseHTML;
                thisobj.set('class', 'selected');
                en4.core.runonce.trigger();
      
							}
				});
									
        request.send();

  }
</script>
<?php  if(empty($this->is_ajax)) { ?>
<div class="headline">
	<h2><?php echo $this->translate('Likes'); ?></h2>
	<div class='tabs'><?php echo $this->navigation($this->navigation)->render() ?></div>
</div>

	<div class="sitelike_member_likes_user_tabs">
		<div class="link">
    	<a href="javascript:void(0)" class="<?php if($this->call_status == 'public') { echo $this->translate('selected'); } ?>" id="show_all_public" onclick="likedStatus('public', this);"><?php echo $this->translate('All'); ?>&nbsp;(<?php echo number_format($this->public_count); ?>)</a>
			<a href="javascript:void(0)" class="<?php if($this->call_status == 'friend') { echo $this->translate('selected'); } ?>" onclick="likedStatus('friend', this);" id="show_all_friend"><?php echo $this->translate('Friends'); ?>&nbsp;(<?php echo number_format($this->friend_count); ?>)</a>
		</div>

		<div class="fright">
			<input id="like_members_search_input" type="text" value="<?php echo $this->search; ?>" onfocus="if(this.value=='')this.value='';" onblur="if(this.value=='')this.value='';" />
		</div>
    <?php  // Member level setting check ?>
		<?php if( ($this->public_count > 1) && ($this->can_view != 'none') ) { ?>
		<a href="" class="buttonlink icon_type_message_likes right-link"><?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitelike', 'controller' => 'index', 'action' => 'compose', 'resource_type' => 'user', 'resource_id' => $this->user_id), $this->translate("Message All"), array('class' => 'smoothbox buttonlink icon_type_message_likes right-link')); ?></a>
		<?php } ?>
	</div>
	<div class="sitelike_likes_content" id="like_memberlikeme_content">
<?php } ?>
		<?php $a = count($this->user_obj);
				if(!empty($a)) {
 		foreach( $this->user_obj as $userinfo ):?>
    <div class="list_items" style="float:<?php echo $this->cycle(array("left", "right")) ->next()?>">
			<div class="item_photo">
     		<?php echo $this->htmlLink($userinfo->getHref(), $this->itemPhoto($userinfo, 'thumb.icon'), array('title'=>$userinfo->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> 'user'.' '.$userinfo->user_id)) ?>
      </div>
	    <div class="friends_option">
	     	<?php echo $this->userFriendship($userinfo)?>
				<?php
            //Member level setting check
						if (($this->can_view != 'none') && ($this->user_id != $userinfo->user_id)) {
				?>
	     	<a style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Messages/externals/images/send.png);" class="buttonlink" href="<?php echo $this->base_url; ?>/messages/compose/to/<?php echo $userinfo->user_id ?>"><?php echo $this->translate('Send Message'); ?></a>
				<?php } ?>
<?php
             $like_ids = Engine_Api::_()->getApi('like', 'seaocore')->hasLike('user',$userinfo->user_id );
            
             	if (!empty($like_ids[0]['like_id'])) {
								$unlike_show = "display:block";
								$like_show = "display:none"; 
								$like_id = $like_ids[0]['like_id'];
	             	}
	            else {
	              $unlike_show = "display:none;";
					      $like_show = "display:block;"; 
					      $like_id = 0;
            	}
	 	     		?>
             <?php
          if(!empty( $this->like_setting_button)) {
            $like_setting_button = $unlike_show;
          }
          else {
            $like_setting_button = "display:none;";
            }
        ?>
				<?php //if(!empty($this->viewer_id)) { ?>
					<div class="sitelike_button" id =  "member_unlikes_<?php echo $userinfo->user_id;?>" style='<?php echo $like_setting_button;?>' >
						<a href="javascript:void(0);" onclick = "app_likes('<?php echo $userinfo->user_id; ?>', 'user', 'member');">
							<i class="like_thumbdown_icon"></i>
							<span><?php echo $this->translate('Unlike') ?></a></span>
					</div>
					<div class="sitelike_button" id="member_most_likes_<?php echo $userinfo->user_id;?>" style ='<?php echo $like_show;?>'>
						<a href = "javascript:void(0);" onclick = "app_likes('<?php echo $userinfo->user_id; ?>', 'user', 'member');">
							<i class="like_thumbup_icon"></i>
							<span><?php echo $this->translate('Like') ?></span>
						</a> 
					</div>
				<?php //} ?>
				<input type ="hidden" id = "member_like_<?php echo $userinfo->user_id;?>" value = '<?php echo $like_id; ?>' />
	    </div> 
      <div class='item_details'>
	    	<div class='item_title'>
	      	<?php echo $this->htmlLink($userinfo->getHref(), $userinfo->getTitle(),array('title'=> $userinfo->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> 'user'.' '.$userinfo->user_id)) ?>
	      </div>  
	     	<div class='item_stat' id="member_num_of_like_<?php echo $userinfo->user_id;?>">
	      	<?php
						// Members 'Number of likes'.
						$friend_like = Engine_Api::_()->getApi('like', 'seaocore')->likeCount($this->resource_type, $userinfo->user_id );
						echo $this->translate(array('%s like', '%s likes', $friend_like),$this->locale()->toNumber($friend_like)) ?>
	      </div>
	    </div>
	  </div>
	 	<?php endforeach;
		if( $this->user_obj->count() > 1 ): ?>
				<div style="clear:both;margin:10px 0;float:left;width:100%;">
					<?php if( $this->user_obj->getCurrentPageNumber() > 1 ): ?>
						<div id="user_like_members_previous" class="paginator_previous">
							<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
								'onclick' => 'paginateLikeMembers(likeMemberPage - 1, call_status)',
								'class' => 'buttonlink icon_previous'
							)); ?>
						</div>
					<?php endif; ?>
					<?php if( $this->user_obj->getCurrentPageNumber() < $this->user_obj->count() ): ?>
						<div id="user_like_members_next" class="paginator_next">
							<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
								'onclick' => 'paginateLikeMembers(likeMemberPage + 1, call_status)',
								'class' => 'buttonlink_right icon_next'
							)); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php } else { ?>
			 <div class='tip' style="margin:50px 0 0 350px;"><span>
			 		<?php 
			 			echo $this->no_result_msg;
			 		?>
			 </span></div>
			<?php } ?>

<?php  if(empty($this->is_ajax)) { ?>
 </div>
<?php } ?>