<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: profileuserlikes.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/scripts/core.js'); ?>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<?php include APPLICATION_PATH . '/application/modules/Sitelike/views/scripts/settings_css.tpl' ; ?>
<script type="text/javascript">
  var profileuser_id = '<?php echo $this->profileuser_id ?>';
  var mutual_friend = '<?php echo $this->mutual; ?>';

  var current_page = '<?php echo $this->current_page; ?>';
  var paginateProfileLikes = function(page) {
    var url = en4.core.baseUrl + 'likes/profileuserlikes/' + profileuser_id + '/' + mutual_friend;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'page' : page
      },
      onSuccess :function(responseTree, responseElements, responseHTML, responseJavaScript) {
				en4.core.runonce.trigger();
      }
    }), {
      'element' : document.getElementById('sitelike_profile_members_anchor').getParent()
    });
  }

 var user_likes = function(mutual) {
    var url = en4.core.baseUrl + 'likes/profileuserlikes/' + profileuser_id + '/' + mutual;
    if (mutual == 0) {
    	document.getElementById('id_alllikes').set('class', 'selected');
    	document.getElementById('id_mutuallikes').erase('class');
    }
    else {
    	document.getElementById('id_mutuallikes').set('class', 'selected');
    	document.getElementById('id_alllikes').erase('class');
    }
    var  loder_image = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitelike/externals/images/spinner.gif';
    var tempflag = '<center><img src=" ' + loder_image + '" style="margin-top:10px;" /></center>';
		document.getElementById('likes_popup_content').innerHTML = tempflag;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'page' : 1
      }
    }), {
      'element' : document.getElementById( 'sitelike_profile_members_anchor').getParent()
    });
  }
</script>


</div>
<a id="sitelike_profile_members_anchor" style="position:ansolute;"></a>
<div class="seaocore_members_popup" id="likes_members_popup">
	<div class="top">
		<?php if (empty($this->ownerview)) { ?>
			<div class="heading"><?php echo $this->ownername;?>'s Likes</div>
			<div class="seaocore_members_search_box">
				<div class="link">
		    	<a href="javascript:void(0);" onclick="user_likes(0);" id="id_alllikes" <?php if ($this->activetab) echo "class='selected'";?>><?php echo $this->translate('All') ?> (<?php echo $this->alllikes;?>)</a>
          <?php $viewer = $this->viewer()->getIdentity();
                if (!empty($viewer)) :?>
					<a href="javascript:void(0);" onclick="user_likes(1);" id="id_mutuallikes" <?php if (!$this->activetab) echo "class='selected'";?> ><?php echo $this->translate('Mutual Likes ') ?>(<?php echo $this->mutuallikes;?>)</a>
					<?php endif; ?>
				</div>
			</div>
		<?php } else { ?>
    	<div class="heading"><?php echo $this->translate('Your Likes') ?></div>
  	<?php } ?>
  </div>
	<?php if (empty($this->ownerview)) { ?>
  	<div class="seaocore_members_popup_content" id="likes_popup_content" >
  <?php } else { ?>
    <div class="seaocore_members_popup_content" id="likes_popup_content" style="height:285px;">
  <?php }  ?>
		<?php if( $this->total_count_profilelike > 1 ): ?>
			<?php if( $this->current_page > 1 ): ?>
				<div class="seaocore_members_popup_paging">
					<div id="user_group_members_previous" class="paginator_previous">
						<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
						'onclick' => 'paginateProfileLikes(parseInt(current_page) - parseInt(1))'
						)); ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if (count($this->profileuser_likes) > 0) {
			foreach ($this->profileuser_likes as $row_mix_fetch)
			{
				$show_like = 0;
				$item = $row_mix_fetch['object'][0];
				if(!empty($item))	{
					$show_like = 1;
					$type_id = $item->getIdentity();
					?>
					<div class="item_member_list">
						<div class="item_member_thumb">
							<?php if ($row_mix_fetch['type'] == 'blog') {
							echo $this->htmlLink($item->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon'), array('title' => $item->getTitle(), 'target' => '_blank', 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$type_id));
							} else {
							echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title' => $item->getTitle(), 'target' => '_parent', 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$type_id));
							}
							?>
						</div>
						<div class="item_member_details">
							<div class="item_member_name">
								<?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->sitelike()->turncation( $item->getTitle()), array('title' => $item->getTitle(), 'target' => '_parent', 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$type_id)); ?>
							</div>
							<div class="item_member_stat">
								<?php
									$getShortType = $item->getShortType();
									if($getShortType == 'user') {
										$getShortType = 'Member';
									} elseif($getShortType == 'playlist') {
										$getShortType = 'Music';
									} elseif($getShortType == 'topic') {
										$getShortType = 'Forum Topic';
									} else {
											if($getShortType == 'photo')
											$getShortType = $item->getType();
											$getShortTypeArray = explode('_',$getShortType);
											foreach ($getShortTypeArray as  $k=>$str)
												$getShortTypeArray[$k] = ucfirst($str);
												$getShortType = implode(' ',$getShortTypeArray);
									}
									$title = $getShortType;
									echo $this->translate($title);
								?>
							</div>
							<?php
				}
				if (!empty($show_like) && $this->profileuser_id != $this->viewer()->getIdentity()) {
					$like_ids = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($row_mix_fetch['type'],$type_id);
					
					if (!empty($like_ids[0]['like_id'])) {
						$unlike_show = "display:block";
						$like_show = "display:none";
						$like_id = $like_ids[0]['like_id'];
					} else {
						$unlike_show = "display:none;";
						$like_show = "display:block;";
						$like_id = 0;
					} ?>
					<?php if(!empty($this->viewer_id)) { ?>
           <?php if(!empty( $this->like_setting_button)) { ?>
            <div class="popup_like_button">
							<div class="sitelike_button" id="<?php echo $row_mix_fetch['type'];?>_unlikes_<?php echo $type_id;?>" style ='display:none;' >
								<a href = "javascript:void(0);" onclick = "user_likes_profile('<?php echo $type_id; ?>', '<?php echo $row_mix_fetch['type'];?>');">
									<i class="like_thumbdown_icon"></i>
									<span><?php echo $this->translate('Unlike') ?></span>
								</a>
							</div>
							<div class="sitelike_button" id="<?php echo $row_mix_fetch['type'];?>_most_likes_<?php echo $type_id;?>" style ='<?php echo $like_show;?>'>
								<a href = "javascript:void(0);" onclick = "user_likes_profile('<?php echo $type_id; ?>', '<?php echo $row_mix_fetch['type'];?>');">
									<i class="like_thumbup_icon"></i>
									<span><?php echo $this->translate('Like') ?></span>
								</a>
							</div>
						</div>
              <?php } ?>
					<?php } ?>
					<input type ="hidden" id = "<?php echo $row_mix_fetch['type'];?>_like_<?php echo $type_id;?>" value = "<?php echo $like_id; ?>"  />
					</div>
				</div>
 	<?php	}
     else if(!empty($show_like)) { ?>
				</div>
			</div>
 <?php
		 } }
}
 else
  { ?>


   <div class="tip" style="margin-top:10px;margin-left:200px;"><span>THERE IS NO MUTUAL LIKES.</span></div>

  <?php
  }
?>


 <?php if( $this->total_count_profilelike > 1 ): ?>
    <?php if( $this->current_totallikes < $this->total_count_profilelike ): ?>
    	<div class="seaocore_members_popup_paging">
        <div id="user_group_members_next" class="paginator_next">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
            'onclick' => 'paginateProfileLikes(parseInt(current_page) + parseInt(1))'
          )); ?>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>

	</div>
</div>
<div class="seaocore_members_popup_bottom">
	<button onclick="smoothboxclose();"><?php echo $this->translate("Close") ?></button>
</div>
<script type="text/javascript">
 function smoothboxclose () {
  parent.window.location.reload();
  parent.Smoothbox.close () ;
 }
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitelike/Api/likesettings.php'; ?>