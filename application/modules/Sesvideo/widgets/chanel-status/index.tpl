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
<div id='chanel_status' class="sesvideo_channel_profile_title sesbasic_bxs sesbasic_clearfix">
  <h2>
  	<?php echo $this->subject->getTitle() ?>             
  	<?php if(in_array('verified',$this->option) && $this->subject->is_verified){ ?>
    	<i class="sesvideo_verified fa fa-check-square" title="<?php echo $this->translate('Verified') ;?>"></i>
    <?php } ?>
   </h2>
   <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0){ ?>
  <span class="sesvideo_channel_profile_title_btns">
           <?php if(in_array('follow',$this->option) && $this->subject->follow){ ?>
           <?php  $followbutton =  Engine_Api::_()->getDbtable('chanelfollows', 'sesvideo')->checkFollow(Engine_Api::_()->user()->getViewer()->getIdentity(),$this->subject->chanel_id); ?>
            <a href="javascript:;" data-url="<?php echo $this->subject->chanel_id ; ?>" class="sesbasic_icon_btn sesvideo_chanel_follow sesbasic_icon_btn_count sesbasic_icon_follow_btn <?php echo ($followbutton)  ? 'button_active' : '' ?>"> <i class="fa fa-check"></i><span><?php echo $this->subject->follow_count; ?></span></a>
          
           <?php } ?>
            <?php
                $canComment =  $this->subject->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
                if(in_array('like',$this->option) && Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && $canComment){
            ?>
                <!--Like Button-->
                <?php $LikeStatus = Engine_Api::_()->sesvideo()->getLikeStatusVideo($this->subject->chanel_id,$this->subject->getType()); ?>
              <a href="javascript:;" data-url="<?php echo $this->subject->chanel_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesvideo_like_sesvideo_chanel <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $this->subject->like_count; ?></span></a>
              <?php } ?>              
             <?php if(in_array('favourite',$this->option) &&  isset($this->subject->favourite_count) && Engine_Api::_()->user()->getViewer()->getIdentity() != '0'){ ?>
              <?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesvideo')->isFavourite(array('resource_type'=>'sesvideo_chanel','resource_id'=>$this->subject->chanel_id)); ?>
              <a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesvideo_favourite_sesvideo_chanel <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $this->subject->chanel_id ; ?>"><i class="fa fa-heart"></i><span><?php echo $this->subject->favourite_count; ?></span></a>
            <?php } ?>
          <?php if(in_array('report',$this->option)){ ?>
            <a href="<?php echo $this->url(array("module"=> "core", "controller" => "report", "action" => "create", "route" => "default", "subject" => $this->subject->getGuid()),'default',true); ?>" onclick='opensmoothboxurl(this.href);return false;' class="sesbasic_icon_btn sesbasic_icon_share_btn" ><i class="fa fa-flag"></i></a>
          <?php } ?>
          <?php if(in_array('share',$this->option)){ ?>
            <a href="<?php echo $this->url(array('module'=> 'sesvideo', 'controller' => 'index','action' => 'share','route' => 'default','type' => 'sesvideo_chanel','id' => $this->subject->getIdentity(),'format' => 'smoothbox'),'default',true); ?>" class="sesbasic_icon_btn sesbasic_icon_share_btn" onclick='opensmoothboxurl(this.href);return false;' ><i class="fa fa-share"></i></a>
            <?php } ?>
          <?php if(in_array('edit',$this->option)){ ?>
           <?php if($this->can_edit){ ?>
            <a href="<?php echo $this->url(array('module' => 'sesvideo', 'action' => 'edit', 'chanel_id' => $this->subject->chanel_id), 'sesvideo_chanel', true); ?>" class="sesbasic_icon_btn" title="<?php echo $this->translate('Edit Channel'); ?>" class="sesbasic_icon_btn sesbasic_icon_edit_btn" ><i class="fa fa-pencil"></i></a>
           <?php } ?>
          <?php } ?>  
         <?php if(in_array('delete',$this->option)){ ?>
           <?php if($this->can_delete){ ?>
            <a title="<?php echo $this->translate('Delete Channel'); ?>" href="<?php echo $this->url(array('module' => 'sesvideo', 'action' => 'delete', 'chanel_id' => $this->subject->chanel_id), 'sesvideo_chanel', true); ?>" class="sesbasic_icon_btn sesbasic_icon_delete_btn" onclick='opensmoothboxurl(this.href);return false;'><i class="fa fa-trash"></i></a>
           <?php } ?>
          <?php } ?>
  </span>
 <?php } ?>
</div>