<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
?>
<?php $randonNumber = 'sesvideo_chanel_followers'; ?>
<?php $viewer = Engine_Api::_()->user()->getViewer();?>
<?php if(!$this->is_ajax){ ?>
<div class="sesvideo_channel_followers sesbasic_clearfix">
  <div class="video_search_result">
  	<?php echo $this->translate(array('%s follower found.', '%s followers found.', $this->totalUsers),$this->locale()->toNumber($this->totalUsers)) ?>
	</div>
  <ul id="browsemembers_ul<?php echo $randonNumber; ?>">
<?php } ?>
    <?php if(empty($this->showData)): ?>
      <?php if($this->template_settings == 1): ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/followers/_teamtemplate1.tpl'; ?> 
      <?php elseif($this->template_settings == 2): ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/followers/_teamtemplate2.tpl'; ?>
      <?php elseif($this->template_settings == 3): ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/followers/_teamtemplate3.tpl'; ?>
      <?php elseif($this->template_settings == 4): ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/followers/_teamtemplate4.tpl'; ?>
      <?php elseif($this->template_settings == 5): ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/followers/_teamtemplate5.tpl'; ?>
      <?php elseif($this->template_settings == 6): ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/followers/_teamtemplate6.tpl'; ?>
      <?php endif; ?>
    <?php else: ?>
    <?php foreach( $this->users as $user ){ ?>
    	<?php $userDetail = Engine_Api::_()->getItem('user',$user->owner_id); ?>
      <li class="sesvideo_channel_followers_list sesbasic_clearfix sesbasic_bxs">
      	<div class="sesvideo_channel_followers_list_photo">
        	<?php echo $this->htmlLink($userDetail->getHref(), $this->itemPhoto($userDetail, 'thumb.icon')) ?>
        </div>
        <div class='sesvideo_channel_followers_list_info'>
        	<div class="sesvideo_channel_followers_list_name">
						<?php echo $this->htmlLink($userDetail->getHref(), $userDetail->getTitle()) ?>
          </div>
          <?php 
            $table = Engine_Api::_()->getDbtable('block', 'user');
            $select = $table->select()
              ->where('user_id = ?', $userDetail->getIdentity())
              ->where('blocked_user_id = ?', $viewer->getIdentity())
              ->limit(1);
            $row = $table->fetchRow($select);
          ?>
            <?php if( $row == NULL ){ ?>
              <?php if( $this->viewer()->getIdentity() ){ ?>
              <p class='sesvideo_channel_followers_list_info_link'>
                <?php echo str_replace('smoothbox','smoothboxOpen',$this->userFriendship($userDetail)); ?>
              </p>
            <?php } ?>
        	<?php } ?>
        </div>
      </li>
    <?php } ?>
    <?php  if($this->paginator->getTotalItemCount() == 0){  ?>
            <div class="tip">
              <span>
                <?php echo $this->translate("There are currently no followers.");?>
              </span>
            </div>    
    			<?php } ?>
    <?php endif; ?>
<?php if(!$this->is_ajax){ ?>
  </ul>
</div>
  <div class="sesbasic_view_more" id="view_more_<?php echo $randonNumber; ?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'buttonlink icon_viewmore')); ?> </div>
  <div class="sesbasic_view_more_loading" id="loading_image_<?php echo $randonNumber; ?>" style="display: none;"> 
  	<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sesbasic/externals/images/loading.gif" /> 
  </div>
<?php } ?>
<script type="application/javascript">
<?php if($this->loadOptionData == 'auto_load'){ ?>
		window.addEvent('load', function() {
		 sesJqueryObject(window).scroll( function() {
			  var heightOfContentDiv_<?php echo $randonNumber; ?> = sesJqueryObject('#scrollHeightDivSes_<?php echo $randonNumber; ?>').offset().top;
        var fromtop_<?php echo $randonNumber; ?> = sesJqueryObject(this).scrollTop();
        if(fromtop_<?php echo $randonNumber; ?> > heightOfContentDiv_<?php echo $randonNumber; ?> - 100 && sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display') == 'block' ){
						document.getElementById('feed_viewmore_link_<?php echo $randonNumber; ?>').click();
				}
     });
	});
<?php } ?>
viewMoreHide_<?php echo $randonNumber; ?>();
  function viewMoreHide_<?php echo $randonNumber; ?>() {
    if ($('view_more_<?php echo $randonNumber; ?>'))
      $('view_more_<?php echo $randonNumber; ?>').style.display = "<?php echo ($this->paginator->count() == 0 ? 'none' : ($this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' )) ?>";
  }
	var page<?php echo $randonNumber; ?> = '<?php echo $this->page + 1; ?>';
  function viewMore_<?php echo $randonNumber; ?> (){
    document.getElementById('view_more_<?php echo $randonNumber; ?>').style.display = 'none';
    document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = '';    
    if (typeof(requestTab_<?php echo $randonNumber; ?>) != 'undefined') {
				 requestTab_<?php echo $randonNumber; ?>.cancel();
 			 }
		requestTab_<?php echo $randonNumber; ?> = 
		(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'widget/index/mod/sesvideo/name/channel-follow-user/',
      'data': {
        format: 'html',
        page: page<?php echo $randonNumber; ?>,    
				loadOptionData :"<?php echo $this->loadOptionData; ?>" , 
				chanel_id : "<?php echo $this->chanel_id; ?>",
        showData : "<?php echo $this->showData; ?>",
				is_ajax : 1,
        params: '<?php echo json_encode($this->all_params); ?>',
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('browsemembers_ul<?php echo $randonNumber; ?>').innerHTML = document.getElementById('browsemembers_ul<?php echo $randonNumber; ?>').innerHTML + responseHTML;
				document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
      }
    })).send();
    return false;
  }
</script>