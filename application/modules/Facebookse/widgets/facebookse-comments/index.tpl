<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 8427 2011-02-09 23:11:24Z john $
 * @author     John
 */



?>
<?php $this->headScript()
				  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Facebookse/externals/scripts/core.js');?>
<script type="text/javascript">
var call_advfbjs = '1';
<?php if ($this->isajax != 2):?>
en4.core.runonce.add(function() { 
  setTimeout('callFBParse(\'fbcomment_form\');', 50);
});
<?php endif;?>
 FB_Comment_Setting = '<?php echo $this->comment_setting;?>';
defalutCommentClass = '<?php echo $this->removeCommentBoxClass; ?>';
window.addEvent('domready', function () { 

<?php if ($this->comment_setting == 1 && empty($this->isajax)){?>
  
if (document.getElement(defalutCommentClass) != null) {
  SeaoCommentbox_obj = document.getElement(defalutCommentClass).getParent();
  SeaoCommentbox_obj.innerHTML = '';
}
<?php } ?>
  });
</script>

<div id="temp_postcontent" style="display:none;">
</div>
<div class='comments facebookse_comments' id="FB_comments_options">
  <div class='fb_comments_options'>
   <span id="comment-info"> 
   
    <?php $commentcount = '<fb:comments-count href="' . $this->curr_url . '" ></fb:comments-count>';
    
    
    echo $commentcount . ' ' . $this->translate('comments');?>
   
  <?php //echo $this->translate(array('%s comment', '%s comments', $commentcount), $this->locale()->toNumber($commentcount)) ?></span>
  
  <?php 
        if ($this->show_likeunlike):
  if( $this->viewer()->getIdentity() && ($this->canComment && $this->comment_setting != 2)): ?>
  	
	    <?php if( $this->islike ):?>
	       - <a href="javascript:void(0);" onclick="FB_unlike('<?php echo $this->type?>', '<?php echo $this->identity ?>')"><?php echo $this->translate('Unlike This'); ?></a>
	    <?php else: ?>
	       - <a href="javascript:void(0);" onclick="FB_like('<?php echo $this->type?>', '<?php echo $this->identity ?>')"><?php echo $this->translate('Like This') ?></a>
	    <?php endif; ?>
	    
  <?php endif; ?>
  </div>       
  <ul class="clr">
    <?php if( $this->likes && $this->likes->getTotalItemCount() > 0  && ($this->comment_setting == 1 || $this->comment_setting == 3) ): // LIKES ------------- ?>
      <li>
        <?php if( $this->viewAllLikes || $this->likes->getTotalItemCount() <= 3 ): ?>
          <?php $this->likes->setItemCountPerPage($this->likes->getTotalItemCount()) ?>
          <div> </div>
          <div class="comments_likes" id="comments_likes">
            <?php echo $this->translate(array('%s likes this', '%s like this', $this->likes->getTotalItemCount()), $this->fluentList($this->getAllLikesUsers)) ?>
          </div>
        <?php else: ?>
          <div> </div>
          <div class="comments_likes" id="comments_likes">
            <?php echo $this->htmlLink('javascript:void(0);',
                          $this->translate(array('%s person likes this', '%s people like this', $this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount())),
                          array('onclick' => 'FB_showLikes("'.$this->type.'", "'.$this->identity.'");')
                      ); ?>
          </div>
        <?php endif; ?>
        </li>
    <?php endif; ?>
  </ul>
  <?php else : echo '</div>';
    endif; ?>
 </div> 
<?php //if((empty($this->isajax) || ($this->viewer()->getIdentity() && ($this->canComment || $this->comment_setting == 3)) )): ?>
<?php  //if (!empty($this->success_scrapefburl) && (empty($this->like_unlike) || empty($this->isajax))) {  

   $_SESSION['comment_box'] = 1;

 if (empty($this->isajax)) {
   $style = 'display:block;';
 }
 else {
   $style = 'display:none;';
 }
 if ($this->color == 'dark') { 
   echo '<div id="fbcomment_form" class="clr facebookse_comments_dark facebookse_comments_box" style="display:none;"><fb:comments href="' . $this->curr_url . '" width="'. $this->width .'" colorscheme="'. $this->color .'" ></fb:comments></div>'; 
   
 }
 else {
   echo '<br /><div id="fbcomment_form" class="clr facebookse_comments_box" style="display:none;"><fb:comments href="' . $this->curr_url . '" width="'. $this->width .'" colorscheme="'. $this->color .'"></fb:comments></div>'; 
 }

//}
?>