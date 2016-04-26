<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Facebookse
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _fblikebutton.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
 <?php if (!empty($this->isajax)) : ?>
<script type="text/javascript">
	fblike_moduletype = '<?php echo $this->resource_type; ?>';
	fblike_moduletype_id = '<?php echo $this->resource_identity; ?>';
  en4.facebookse.post_like_session_active = '<?php echo $this->fbuser_active; ?>';
  en4.facebookse.objectToLikeOrig = '<?php echo $this->currurl_orig;?>';
  en4.facebookse.objectToLike = '<?php echo $this->curr_url;?>';
  en4.facebookse.fb_access_token = '<?php echo $this->fb_access_token;?>';
  <?php if($this->likeSettinginfo['action_type'] == 'og.likes'):?>
  en4.facebookse.objectActionType = '<?php echo $this->likeSettinginfo['action_type'];?>';
  <?php else:?>
    en4.facebookse.objectActionType = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('fbapp.namespace') . ':' . $this->likeSettinginfo['action_type'];?>';
  <?php endif;?>
  
  en4.facebookse.ActionObject = '<?php echo $this->likeSettinginfo['object_type'];?>';
  var fbpost_likecount = '<?php echo $this->likeCount;?>';
  en4.facebookse.fbuser_id = '<?php echo $this->fb_uid;?>';
  en4.facebookse.fbfriends = '<?php echo json_encode($this->likedFBFriens);?>';
  
</script>

	<?php //$hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($this->resource_type, $this->resource_id); ?>
	<div class="fblike_button" id="<?php echo $this->resource_type; ?>_fbunlike_<?php echo $this->resource_identity;?>" style ='display:<?php echo ($this->hasliked && $this->fbuser_active) ?"inline-block":"none"?>' >
		<a href = "javascript:void(0);" onclick = "en4.facebookse.fb_content_type_unlike('<?php echo $this->like_actionId;?>');">
      <?php $image_likethumbsdown = Engine_Api::_()->facebookse()->getDefaultLikeUnlikeIcon('unlike', false);?>
      <?php if($this->likeSettinginfo['show_customicon']) : ?>
        <i class="fblike_icon_liked"  style='background-image:url(<?php echo $logo_photo = !empty($this->likeSettinginfo['fbbutton_unlikeicon']) ? $this->layout()->staticBaseUrl . $this->likeSettinginfo['fbbutton_unlikeicon'] : $this->layout()->staticBaseUrl . $image_likethumbsdown; ?>);'></i>
      <?php endif;?>
			<span><?php echo $this->translate($this->likeSettinginfo['fbbutton_unliketext']) ?></span>
		</a>
	</div>
	<div class="fblike_button" id="<?php echo $this->resource_type; ?>_fblike_<?php echo $this->resource_identity;?>" style ='display:<?php echo empty($this->hasliked) ?"inline-block":"none"?>'>
		<a href = "javascript:void(0);" onclick = "en4.facebookse.fb_content_type_like();">
      <?php if($this->likeSettinginfo['show_customicon']) : ?>
      <?php $image_likethumbsup = Engine_Api::_()->facebookse()->getDefaultLikeUnlikeIcon('like', false);?>
        <i class="fblike_icon_like" style='background-image:url(<?php echo $logo_photo = !empty($this->likeSettinginfo['fbbutton_likeicon']) ? $this->layout()->staticBaseUrl . $this->likeSettinginfo['fbbutton_likeicon'] : $this->layout()->staticBaseUrl . $image_likethumbsup; ?>);'></i>
      <?php endif;?>
			<span><?php echo $this->translate($this->likeSettinginfo['fbbutton_liketext']) ?></span>
		</a>
	</div>
<?php if(!empty($this->likeSettinginfo['send_button']) && $this->likeSettinginfo['send_button'] != 'false') : ?>
	<div class="fblike_button">
			<a href = "javascript:void(0);" onclick = "en4.facebookse.OpenShareWindow();">			
				<span><?php echo $this->translate('Share') ?></span>
			</a>
	</div>
<?php endif;?>
<?php  
      if ($this->likeCount > 0) { 
          $Like_Content = '';
          if (!empty($this->likedFBFriens)) {
             foreach ($this->likedFBFriens as $key => $friends) {
               
               $Like_Content .=  '<a href=https://facebook.com/' . $friends['id'] . ' target="_blank">' .   $friends['name'] . '</a>';
               
               $temp = false;
               if (count($this->likedFBFriens) > 1 && ((count($this->likedFBFriens) == $this->likeCount) || ($this->hasliked && count($this->likedFBFriens) + 1 == $this->likeCount))) {
                 if ($key == (count($this->likedFBFriens)-2)) 
                  $Like_Content .=  $this->translate('and') . ' ';  
                 $temp = true;
               }
               if ($key < (count($this->likedFBFriens) - 1) && !$temp)
                 $Like_Content .=  ', ';
               
                }
             
//             if (count($this->likedFBFriens) > 1 && $this->hasliked) {
//               $Like_Content =  ', ' . $Like_Content;
//             }
//             else if (count($this->likedFBFriens) == 1 && $this->hasliked) {
//               $Like_Content =  'and ' . $Like_Content;
//             }
          }
         if (!$this->hasliked) { 
           if (!empty($this->likedFBFriens)) {
                          
            if ($this->likeCount > count($this->likedFBFriens)) 
								$Like_Content =  $Like_Content . $this->translate(' and ') . $this->translate('%s others like this', ($this->likeCount - count($this->likedFBFriens)));
						else
								$Like_Content = $Like_Content . ' ' . $this->translate('like this.');
           }   
           else if ($this->likeCount == 1)
							$Like_Content = $this->translate('One person likes this.');
           else
             $Like_Content = $this->likeCount . ' ' . $this->translate('people like this.');
           
         }
         else { 
          
           if (!empty($Like_Content) && $this->likeCount == (count($this->likedFBFriens) + 1)) { 
             if ($this->likeCount == 2) {
               $Like_Content =  $this->translate('You and') .  ' ' . $Like_Content . ' ' . $this->translate('like this.');
             }
             else 
                $Like_Content =  $this->translate('You') . ', ' . $Like_Content . ' ' . $this->translate('like this.');
           }
           else if ($this->likeCount == 1) {
             $Like_Content = $this->translate('You like this.');
           }
           elseif (count($this->likedFBFriens) < 1) {
             $Like_Content = $this->translate('You and %s others like this.', --$this->likeCount );
           }
           else
             $Like_Content = $this->translate('You, ') . $Like_Content . $this->translate(' and ') . $this->translate('%s others like this.', ($this->likeCount - count($this->likedFBFriens) - 1)); 
             
            //MAKE A STRING OF FRIENDS PROFILE PICTURES.

            
            
         }
         
         
      }
      else { 
        if ($this->hasliked)
          $Like_Content = $this->translate('You like this.');
        else
          $Like_Content = $this->translate('Be the first to like this content.');
      }
      echo '<span class="fblikebutton_counts" id=post_likecount>' . $Like_Content .  '</span>'?>
     

  
  <?php endif;?>

<?php if(!empty($this->isajax)) {
    $imageUrl = '';
    $description = '';
    $title = '';
    $url = '';
    
  
   if (!empty($this->resource_type) && !empty($this->resource_identity) && isset($_SESSION[$this->resource_type . '_' . $this->resource_identity])) { 
     $imageUrl = $_SESSION[$this->resource_type . '_' . $this->resource_identity]['image'];
     $description = $_SESSION[$this->resource_type . '_' . $this->resource_identity]['description'];
     $title = $_SESSION[$this->resource_type . '_' . $this->resource_identity]['title'];
     $url = $_SESSION[$this->resource_type . '_' . $this->resource_identity]['url'];
     unset ($_SESSION[$this->resource_type . '_' . $this->resource_identity]);
     
   }
   elseif (isset($_SESSION['opengraphinfo'])) {
     
     $imageUrl = $_SESSION['opengraphinfo']['image'];
     $description = $_SESSION['opengraphinfo']['description'];
     $title = $_SESSION['opengraphinfo']['title'];
     $url = $_SESSION['opengraphinfo']['url'];
     unset($_SESSION['opengraphinfo']);
   }
  
  
    ?>

<?php if ($this->likeSettinginfo['like_commentbox']) : ?>
<div style="display:none;" id="show_fbPostCommentbox" class="fblikebutton_postcommentbox_wrap">
  <div class="fblikebutton_postcommentbox_arrow fleft prelative"></div>
  <div id="fblike_postcommentbox" class="fblikebutton_postcommentbox">
    <img src="https://graph.facebook.com/<?php echo $this->fb_uid;?>/picture" class="fleft fb_thummb" alt="" />
    <div class="o_hidden">
      <div class="fblikebutton_postcommentbox_txtbox">
        <textarea id="post_comment" name="body"></textarea>
      </div>
      <img src="<?php echo $imageUrl;?>" class="fleft fb_thummb" alt="" width="50px;" height="50px;"  id="fbpost_image"/>
      <div class="o_hidden">
        <p id="fbpost_title" class="bold">
          <?php echo $title;?>
        </p>
        <p id="fbpost_url" class="f_small seaocore_txt_light">
          <?php echo $url;?>
        </p>
        <p class="f_small">
          <?php echo $description;?>
        </p>
      </div>
    </div>  
  </div>
  <div class="fblikebutton_postcommentbox_buttons clr dblock">
    <button onclick="en4.facebookse.fb_content_type_update();"><?php echo $this->translate('Post to Facebook');?></button>
    <button onclick="en4.facebookse.resetCommentBox();"><?php echo $this->translate('Close');?></button>
  </div>
</div>
<?php endif;?>  


 <?php if ($this->likeSettinginfo['like_faces']) : ?>
  <div id='fblikebutton_showfaces' class="fblikebutton_showfaces clr o_hidden">
    <?php
    
//MAKE A STRING OF FRIENDS PROFILE PICTURES.
if ($this->hasliked) :            
	$Liked_Friends_ProfilePicture =  '<span class="dblock fleft"><a href=https://facebook.com/' . $this->fb_uid . ' target="_blank"><img src=https://graph.facebook.com/' . $this->fb_uid . '/picture></a></span>';
		if (!empty($this->likedFBFriens)) {
		foreach ($this->likedFBFriens as $key => $friends) {

			$Liked_Friends_ProfilePicture .=  '<span class="dblock fleft"><a href=https://facebook.com/profile/' . $friends['id'] . ' target="_blank"><img src=https://graph.facebook.com/' . $friends['id'] . '/picture></a></span>';

		}
	}
			echo $Liked_Friends_ProfilePicture;
    endif;
    ?>
    <?php if (!$this->hasliked): ?>
			<span class="dnone fleft" id="fbuser_image">
				<img src="" alt="" id="fbuser_image"/>
			</span>
    <?php endif;?>
  </div>
<?php endif;?>



  <?php 
} ?>
<?php $settings = Engine_Api::_()->getApi( 'settings' , 'core' );?>
<style type="text/css">
  .fblike_button a{
    background-color:<?php echo $settings->getSetting( 'fblike.background.color' , '#f1f2f1' )?>;
    border: 1px solid #CECECE;
    border-radius: 3px 3px 3px 3px;
    display: block;
    font-size: 12px;
    font-weight: bold;
    outline: medium none;
    padding:5px 7px;
  }
 .fblike_button a:hover
  {
    background-color:<?php echo $settings->getSetting( 'fblike.background.haourcolor' , '#f1f2f1' )?>;
    border: 1px solid #cccccc;
    text-decoration:none;
  }  
  .fblike_button a span{
    color:<?php echo $settings->getSetting( 'fblike.text.color' , '#666666' )?>;
  }
  .fblike_button a:hover span
  {
    color:<?php echo $settings->getSetting('fblike.haour.color' , '#666666' )?>;
  }

</style>