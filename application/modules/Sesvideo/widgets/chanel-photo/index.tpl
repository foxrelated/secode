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
<div class="sesvideo_profile_photo sesvideo_profile_photo sesbasic_clearfix sesbasic_bxs">
	<?php 
     if($this->photo == 'oPhoto'){
        $user = Engine_Api::_()->getItem('user',$this->subject->owner_id);
        echo $this->itemPhoto($user, 'thumb.profile');
      }else{?>
          <img src="<?php echo $this->subject->getPhotoUrl(); ?>" alt="" class="thumb_profile item_photo_user item_nophoto ">
   <?php  	}
   ?>
</div>