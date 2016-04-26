<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list_both_like_dislike.tpl 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
    $replyLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.reply.link', 1);
    if(!empty($this->photoLightboxComment) ) {
        $replyLink = 0;
    }
?>

<?php $this->headTranslate(array('Are you sure you want to delete this?')); ?>
<?php if (empty($this->parent_div)): ?>
  <div id="parent_div" class="seaocore_replies_wrapper">
  <?php endif; ?>
  <?php if (!$this->page): ?>
    <div class='seaocore_replies <?php if ($this->parent_comment_id): ?>seaocore_replies_child<?php endif; ?>' id="comments_<?php echo $this->nested_comment_id ?>">
    <?php endif; ?>
    <?php if (empty($this->parent_comment_id)): ?>
      <div class='seaocore_replies_options seaocore_txt_light'>
        <span><?php
    echo $this->translate(array('%s Comment', '%s Comments',
        $this->commentsCount), $this->locale()->toNumber($this->commentsCount))
      ?></span>

         <?php if ($this->viewer()->getIdentity() && $this->canComment): ?>
           &nbsp;&middot;&nbsp;
						<!--WITHOUT ICON-->
						<?php if($this->showLikeWithoutIcon):?>
							<?php if(!Engine_Api::_()->getDbtable('likes', 'core')->isLike($this->subject,$this->viewer())):?>
								<a id="like_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?> onclick="en4.nestedcomment.nestedcomments.like('<?php echo
								$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>','', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');" <?php endif;?>><?php echo  $this->translate('Like') ?>
								</a>
							<?php else:?>
									<?php echo $this->translate('Like') ?>
							<?php endif;?>
						<?php else:?>
							<?php if(!Engine_Api::_()->getDbtable('likes', 'core')->isLike($this->subject,$this->viewer())):?>
								<a id="like_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?>onclick="en4.nestedcomment.nestedcomments.like('<?php echo
								$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>','', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');" <?php endif;?>" >
									<img class="mbot5" src="application/modules/Nestedcomment/externals/images/like.png" /> <?php echo $this->translate('Like') ?>
								</a>
							<?php else:?>
									<img class="mbot5" src="application/modules/Nestedcomment/externals/images/like_light.png" /> <?php echo $this->translate('Like') ?>
							<?php endif;?>

						<?php endif;?>
           &nbsp;&middot;&nbsp; 
						<!--WITHOUT ICON-->
						<?php if($this->showLikeWithoutIcon):?>
							<?php if(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislike($this->subject, $this->viewer())):?>
								<?php echo $this->translate('Dislike') ?>
							<?php else :?>
									<a id="unlike_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?>onclick="en4.nestedcomment.nestedcomments.unlike('<?php echo
									$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>', '', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"<?php endif;?>><?php echo $this->translate('Dislike') ?>
									</a>
							<?php endif;?>
						<?php else:?>
							<?php if(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislike($this->subject, $this->viewer())):?>
								<img src="application/modules/Nestedcomment/externals/images/dislike_light.png" /> <?php echo $this->translate('Dislike') ?>
							<?php else :?>
									<a id="unlike_comments_<?php echo $this->subject->getGuid();?>" style="display:inline-block;" href="javascript:void(0);" <?php if($this->viewer()->getIdentity()):?>onclick="en4.nestedcomment.nestedcomments.unlike('<?php echo
									$this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>', '', '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'parent', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"<?php endif;?>><img src="application/modules/Nestedcomment/externals/images/dislike.png" /> <?php echo $this->translate('Dislike') ?>
									</a>
							<?php endif;?>
						<?php endif; ?>
        <?php endif; ?>
        <?php if($this->likes->getTotalItemCount() > 0): ?>
        &nbsp;|&nbsp;<?php $this->likes->setItemCountPerPage($this->likes->getTotalItemCount()) ?>
             <?php if (Engine_Api::_()->getDbTable('likes', 'core')->getLike($this->subject, $this->viewer()) && $this->likes->getTotalItemCount() == 1) :?>
                <?php echo $this->translate(array('%s like this.', '%s likes this.', $this->likes->getTotalItemCount()), $this->fluentLikeList(Engine_Api::_()->getDbtable('likes', 'nestedcomment')->likes($this->subject)->getAllLikesUsers())) ?>
            <?php else:?>
                <?php echo $this->translate(array('%s likes this.', '%s like this.', $this->likes->getTotalItemCount()), $this->fluentLikeList(Engine_Api::_()->getDbtable('likes', 'nestedcomment')->likes($this->subject)->getAllLikesUsers())) ?>
            <?php endif;?>
        <?php endif;?>
        
        <?php if($this->dislikes->getTotalItemCount() > 0): ?>
        
          <?php if($this->likes->getTotalItemCount() > 0) :?>
           &nbsp;&middot;&nbsp;
          <?php else:?>
          &nbsp;|&nbsp;
          <?php endif;?>
          
          <?php if($this->showDislikeUsers) :?>
               <?php $this->dislikes->setItemCountPerPage($this->dislikes->getTotalItemCount()) ?> 
                <?php //echo $this->translate(array('%s dislikes this.', '%s dislike this.', $this->dislikes->getTotalItemCount()), $this->fluentDisLikeList(Engine_Api::_()->getDbTable('dislikes', 'nestedcomment')->getAllDislikesUsers($this->subject))) ?>
                
                
                <?php if (Engine_Api::_()->getDbTable('dislikes', 'nestedcomment')->getDislike($this->subject, $this->viewer()) && $this->dislikes->getTotalItemCount() == 1) :?>
                  <?php echo $this->translate(array('%s dislike this.', '%s dislikes this.', $this->dislikes->getTotalItemCount()), $this->fluentDisLikeList(Engine_Api::_()->getDbTable('dislikes', 'nestedcomment')->getAllDislikesUsers($this->subject))) ?>
               <?php else:?>
                  <?php echo $this->translate(array('%s dislikes this.', '%s dislike this.', $this->dislikes->getTotalItemCount()), $this->fluentDisLikeList(Engine_Api::_()->getDbTable('dislikes', 'nestedcomment')->getAllDislikesUsers($this->subject))) ?>
               <?php endif;?>
            
                 <?php else:?>  
                <b><?php echo $this->translate(array('%s person dislikes this.', '%s people dislike this.', $this->dislikes->getTotalItemCount()), $this->locale()->toNumber($this->dislikes->getTotalItemCount()));?></b>
        <?php endif;?>
           <?php endif;?>
      </div>
    
    <?php else: ?>
      <!-- <div class='nested_seaocore_replies_options'></div>-->
    <?php endif; ?>
    <?php
    if (isset($this->formComment)):
      if ($this->parent_comment_id):
        //echo $this->formComment->setAttribs(array('id' => 'comments-form_' . $this->nested_comment_id, 'style' => 'display:none;'))->render();
        ?>

		<form method="post" action=""  enctype="application/x-www-form-urlencoded" id='comments-form_<?php echo $this->nested_comment_id;?>' action-id="<?php echo $this->nested_comment_id;?>" style="display:none;" class="comments_form_nestedcomments_comments">
			<textarea id="<?php echo $this->nested_comment_id;?>" cols="1" rows="1" name="body" placeholder="<?php echo $this->escape($this->translate('Write a comment...')) ?>"></textarea>
			<?php if( $this->viewer() && $this->subject()): ?>
				<input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
			<?php endif; ?>
			<input type="hidden" name="type" value="<?php echo $this->subject()->getType();?>" id="type">
			<input type="hidden" name="identity" value="<?php echo $this->subject()->getIdentity();?>" id="identity"><input type="hidden" name="parent_comment_id" value="<?php echo $this->parent_comment_id;?>" id="parent_comment_id">

      <div id="compose-containe-menu-items_<?php echo $this->nested_comment_id; ?>" class="compose-menu <?php if($this->nestedCommentPressEnter):?> inside-compose-icons <?php endif;?> <?php if($this->showSmilies && $this->nestedCommentPressEnter):?> inside-smile-icon <?php endif;?>">
      <?php if($this->nestedCommentPressEnter):?>
         <button id="submit" type="submit" style="display: none;"><?php echo $this->translate("Post Reply") ?></button>
      <?php else:?>
         <button id="submit" type="submit"><?php echo $this->translate("Post Reply") ?></button>
         <div id="composer_container_icons_<?php echo $this->nested_comment_id; ?>"></div>
      <?php endif;?>   
       </div>
		</form>

        
        <?php
      else:
        //echo $this->formComment->setAttribs(array('id' => 'comments-form_' . $this->nested_comment_id))->render();
        ?>		

<form method="post" action=""  enctype="application/x-www-form-urlencoded" id='comments-form_<?php echo $this->nested_comment_id;?>' action-id="<?php echo $this->nested_comment_id;?>">
			<textarea id="<?php echo $this->nested_comment_id;?>" cols="1" rows="1" name="body" placeholder="<?php echo $this->escape($this->translate('Write a comment...')) ?>"></textarea>
			<?php if( $this->viewer() && $this->subject()): ?>
				<input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
			<?php endif; ?>
			<input type="hidden" name="type" value="<?php echo $this->subject()->getType();?>" id="type">
			<input type="hidden" name="identity" value="<?php echo $this->subject()->getIdentity();?>" id="identity"><input type="hidden" name="parent_comment_id" value="<?php echo $this->parent_comment_id;?>" id="parent_comment_id">
			

      <div id="compose-containe-menu-items_<?php echo $this->nested_comment_id; ?>" class="compose-menu <?php if($this->nestedCommentPressEnter):?> inside-compose-icons <?php endif;?> <?php if($this->showSmilies && $this->nestedCommentPressEnter):?> inside-smile-icon <?php endif;?>">
            <?php if($this->nestedCommentPressEnter):?>
              <button id="submit" type="submit" style="display: none;"><?php echo $this->translate("Post Comment") ?></button>
             <?php else:?>
                <button id="submit" type="submit" style="display: inline-block;"><?php echo $this->translate("Post Comment") ?></button>
                <div id="composer_container_icons_<?php echo $this->nested_comment_id; ?>"></div>
             <?php endif;?>
        </div>
		</form>
      <?php endif; ?>

    <?php endif; ?>

    <ul>
      <?php if((empty($this->parent_comment_id) && $this->dislikes->getTotalItemCount() > 0) || (empty($this->parent_comment_id) && $this->likes->getTotalItemCount() > 0) || ($this->comments->getTotalItemCount() > 0)):?>
       
            
          <?php if ($this->comments->getTotalItemCount() > 1): // REPLIES ------- ?>
            <?php if (empty($this->parent_comment_id)): ?>                             
               <li id="seaocore_replies_li"><div class="seaocore_replies_sorting fright" id="seaocore_replies_sorting">
                <div class="mright5" id="sort_<?php echo $this->nested_comment_id; ?>" style="display:none;"></div>
                <div class="seaocore_replies_pulldown seaonestcomment_pulldown">
                  <div class="seaocore_dropdown_menu_wrapper" id="sorting_dropdown_menu" style="display:none;">
                  	<div class="seaocore_dropdown_menu">
                      <ul>
                        <li  class="<?php if ($this->order == 'DESC'): ?> active <?php endif;?>" ><a href="javascript:void(0);" onclick="sortComments('DESC', '<?php echo $this->subject->getType(); ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $this->parent_comment_id ?>', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"><?php echo $this->translate("Newest"); ?></a></li>
                        <li class="<?php if ($this->order == 'ASC'): ?> active <?php endif;?>"><a href="javascript:void(0);" onclick="sortComments('ASC', '<?php echo $this->subject->getType(); ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $this->parent_comment_id ?>', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"><?php echo $this->translate("Oldest"); ?></a></li>
                        <?php if($this->showLikeWithoutIconInReplies == 3):?>
                        <li class="<?php if ($this->order == 'like_count'): ?> active <?php endif;?>"><a href="javascript:void(0);" onclick="sortComments('like_count', '<?php echo $this->subject->getType(); ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $this->parent_comment_id ?>', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"><?php echo $this->translate("Most Voted"); ?></a></li>
                        <?php else:?>
                        <li class="<?php if ($this->order == 'like_count'): ?> active <?php endif;?>"><a href="javascript:void(0);" onclick="sortComments('like_count', '<?php echo $this->subject->getType(); ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $this->parent_comment_id ?>', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');"><?php echo $this->translate("Most Liked"); ?></a></li>
                        <?php endif;?>
                      </ul>
                    </div>
                  </div>
                  <?php if ($this->order == 'DESC'): ?>
                  	<a href="javascript:void(0);" onclick="showSortComments();"><?php echo $this->translate("Sort by Newest"); ?><span class="seaocore_comment_dropbox"></span></a>
                  <?php elseif($this->order == 'ASC'):?>
                  	<a href="javascript:void(0);" onclick="showSortComments();"><?php echo $this->translate("Sort by Oldest"); ?><span class="seaocore_comment_dropbox"></span></a>
                  <?php elseif($this->order == 'like_count'):?>
                    <?php if($this->showLikeWithoutIconInReplies == 3):?>
                  	<a href="javascript:void(0);" onclick="showSortComments();"><?php echo $this->translate("Sort by Most Voted"); ?><span class="seaocore_comment_dropbox"></span></a>
                    <?php else:?>
                    <a href="javascript:void(0);" onclick="showSortComments();"><?php echo $this->translate("Sort by Most Liked"); ?><span class="seaocore_comment_dropbox"></span></a>
                    <?php endif;?>
                  <?php endif;?>
                </div>
                <!--<select onchange="sortComments(this.value, '<?php echo $this->subject->getType(); ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $this->parent_comment_id ?>', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>');" name="sortComments" class="searchTarget"> 
                  <option value="DESC" <?php if ($this->order == 'DESC'): ?> selected="selected" <?php endif; ?>><?php echo $this->translate("Newest"); ?></option> 
                  <option value="ASC" <?php if ($this->order == 'ASC'): ?> selected="selected" <?php endif; ?>><?php echo $this->translate("Oldest"); ?></option> 
                  <?php if($this->showLikeWithoutIconInReplies == 3):?>
                    <option value="like_count" <?php if ($this->order == 'like_count'): ?> selected="selected" <?php endif; ?>><?php echo $this->translate("Most Voted"); ?></option>
                  <?php else:?>
                    <option value="like_count" <?php if ($this->order == 'like_count'): ?> selected="selected" <?php endif; ?>><?php echo $this->translate("Most Liked"); ?></option>
                  <?php endif;?>
                </select>-->
              </div></li>  
            <?php endif; ?>
          <?php endif; ?>
        
      <?php endif; ?>
      <?php if ($this->comments->getTotalItemCount() > 0): // REPLIES ------- ?>

        <?php if ($this->page && $this->comments->getCurrentPageNumber() > 1): ?>
          <li class="seaocore_replies_list seaocore_prev_count">
            <div> </div>
            <div class="seaocore_replies_viewall">
              <?php
              echo $this->htmlLink('javascript:void(0);', $this->translate('View previous comments'), array(
                  'onclick' => 'en4.nestedcomment.nestedcomments.loadComments("' . $this->subject->getType() . '", "' . $this->subject->getIdentity() . '", "' . ($this->page - 1) . '", "' . $this->order . '", "' . $this->parent_comment_id . '", "' . $this->taggingContent . '", 2, "' . $this->showAsNested . '", "' . $this->showAsLike . '", "' . $this->showDislikeUsers . '", "' . $this->showLikeWithoutIcon . '", "' . $this->showLikeWithoutIconInReplies . '")', 'class' => 'mright5 icon_previous buttonlink'
              ))
              ?>
              <div id="view_previous_comments_<?php echo $this->parent_comment_id; ?>" style="display:none;"></div>
            </div>
          </li>
        <?php endif; ?>

        <?php
        // Iterate over the replies backwards (or forwards!)
        $replies = $this->comments->getIterator();
        $i = 0;
        $l = count($replies) - 1;
        $d = 1;
        $e = $l + 1;
        for (; $i != $e; $i += $d):
          $comment = $replies[$i];
          $poster = $this->item($comment->poster_type, $comment->poster_id);
          if($this->subject->getType() != 'sitestaticpage_page'):
          $canDelete = ( $this->canDelete || $poster->isSelf($this->viewer()) );
          $canEdit = ( $this->canEdit || $poster->isSelf($this->viewer()) );
          else:
          $canDelete = ( $this->canDelete);
          $canEdit = ( $this->canEdit);
          endif;
          ?>
          <li id="comment-<?php echo $comment->comment_id ?>" class="seaocore_replies_list">
            <div class="seaocore_replies_content">
              <div class="seaocore_replies_author_photo">
                <?php
                echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon', $poster->getTitle())
                )
                ?>
              </div>

              <div class="seaocore_replies_info">

                <span class="seaocore_replies_info_op">
    <?php if ($this->showAsNested) : ?>
                    <span class="seaocore_replies_showhide">
                      <span class="minus" onclick="showReplyData(1, '<?php echo $comment->comment_id ?>');" id="hide_<?php echo $comment->comment_id ?>" title="<?php echo $this->translate("Collapse"); ?>"></span> 
                      <span class="plus" onclick="showReplyData(0, '<?php echo $comment->comment_id ?>');" id="show_<?php echo $comment->comment_id ?>" style="display:none;" title="<?php echo $this->translate("Expand"); ?>"></span> 
                    </span>	
    <?php endif; ?>
    <?php if ($this->viewer_id): ?>
                    <span class="seaocore_replies_pulldown">
                      <div class="seaocore_dropdown_menu_wrapper">
                        <div class="seaocore_dropdown_menu">
                            
                            <?php 
   
   $attachMentArray  = array();
   if (!empty($comment->attachment_type) && null !== ($attachment = $this->item($comment->attachment_type, $comment->attachment_id))): ?>
    <?php if($comment->attachment_type == 'album_photo'):?>
      <?php $status = true; ?>
      <?php $photo_id = $attachment->photo_id; ?>
      <?php $album_id = $attachment->album_id; ?>
      <?php $src = $attachment->getPhotoUrl(); ?>
      <?php $attachMentArray = array('status' => $status, 'photo_id' => $photo_id , 'album_id' => $attachment->album_id, 'src' => $src);?>
   <?php elseif($comment->attachment_type == 'core_link') :?>
        <?php $status = true; ?>
        <?php $uri = $attachment->uri;?>
        <?php $attachMentArray = array('status' => $status, 'url' => $uri);?>
   <?php endif;?>
<?php endif;?>
<?php if(empty($attachMentArray)):?>
    <script type="text/javascript">  
        en4.core.runonce.add(function() {
          en4.nestedcomment.editCommentInfo['<?php echo $comment->getIdentity() ?>'] = { 'body': '<?php echo $this->string()->escapeJavascript($comment->body);?>', 'attachment_type':'<?php echo $comment->attachment_type ?>', 'attachment_body':<?php echo Zend_Json_Encoder::encode($attachMentArray);?>}
        });
    </script>
<?php else:?>
    <script type="text/javascript">  
        en4.core.runonce.add(function() {
          en4.nestedcomment.editCommentInfo['<?php echo $comment->getIdentity() ?>'] = { 'body': '<?php echo $this->string()->escapeJavascript($comment->body);?>', 'attachment_type':'<?php echo $comment->attachment_type ?>', 'attachment_body':<?php echo Zend_Json_Encoder::encode($attachMentArray);?>}
        });
    </script>
<?php endif;?>
                            
                          <ul>     
                              <?php if ($canEdit): ?>
                            <li>
                                <?php if ($this->parent_comment_id): ?>
                                    <?php $title = $this->translate("Edit"); ?>
                                  <?php else: ?>
                                    <?php $title = $this->translate("Edit"); ?>
                                 <?php endif; ?>
                              <a href='javascript:void(0);' title="<?php echo $this->translate('Edit') ?>" onclick="showEditForm('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $comment->getIdentity() ?>','<?php echo $comment->parent_comment_id ?>');"><?php echo $title ?></a>
                            </li>
                            <?php endif;?>
                              <?php if ($canDelete): ?>                 
                              <li>
                                <?php if ($this->parent_comment_id): ?>
                                  <?php $title = $this->translate("Delete"); ?>
                                <?php else: ?>
          <?php $title = $this->translate("Delete"); ?>
                              <?php endif; ?>
                                <a href="javascript:void(0);" title="<?php echo $title; ?>" onclick="en4.nestedcomment.nestedcomments.deleteComment('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>', '<?php echo $comment->comment_id ?>','<?php echo $comment->parent_comment_id ?>', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>')"><?php echo $this->translate('Delete'); ?></a>
                              </li>
                              <?php endif; ?>

                            <li>
      <?php echo $this->htmlLink($this->url(array('action' => 'create', 'module' => 'core', 'controller' => 'report', 'subject' => $comment->getGuid()), 'default', true), $this->translate("Report"), array('title' => $this->translate("Report"), 'class' => "smoothbox")) ?>
                            </li>
                          </ul>
                        </div>
                      </div>
                      <span class="seaocore_comment_dropbox"></span>
                    </span>
    <?php endif; ?>
                </span>

                <div class='seaocore_replies_author seaocore_txt_light'>
                    
                    
                    <?php if($comment->parent_comment_id):?>
                      <?php $item = Engine_Api::_()->getItem($comment->getType(), $comment->parent_comment_id);?>
                      <?php $posterParent = Engine_Api::_()->getItem($item->poster_type, $item->poster_id);?>
                      
                        <?php echo $this->htmlLink($poster->getHref(), $poster->getTitle()); ?>
                      
                        <div class="seaocore_comment_tips_wrap">
                          <span class="seaocore_comment_tips">
                            <a href="<?php echo $posterParent->getHref()?>">
                              <img src="application/modules/Nestedcomment/externals/images/reply.png"/>
                              <?php echo $posterParent->getTitle() ?>
                              <?php //if($posterParent->getIdentity() != $poster->getIdentity() ):?>
                              <?php //echo $this->htmlLink($posterParent->getHref(), $posterParent->getTitle()); ?>
                              <?php //endif;?>
                            </a>
                          </span>
                          <div class='seaocore_replies_author_photo_tooltip info_tip_content_wrapper'>
                            <div class="tip_main_photo fleft mright5">
                              <?php echo $this->itemPhoto($posterParent, 'thumb.icon', $posterParent->getTitle())?> 
                            </div>
                            <div class="tip_main_body">
                              <b><?php echo $posterParent->getTitle(); ?></b>
                              <div>
                                  
                                  <?php //$content = $this->viewMore($item->body) ?>
                              <?php //echo $this->smileyToEmoticons($content);?>
                              
                              
                              <?php $content = $item->body ?>
                    <?php
                    if (isset($comment->params)) {
                      $actionParams = (array) Zend_Json::decode($item->params);
                      if (isset($actionParams['tags'])) {
                        foreach ((array) $actionParams['tags'] as $key => $tagStrValue) {

                          $tag = Engine_Api::_()->getItemByGuid($key);
                          if (!$tag) {
                            continue;
                          }
                          $replaceStr = '<a class="sea_add_tooltip_link" '
                                  . 'href="' . $tag->getHref() . '" '
                                  . 'rel="' . $tag->getType() . ' ' . $tag->getIdentity() . '" >'
                                  . $tag->getTitle()
                                  . '</a>';

                          $content = preg_replace("/" . preg_quote($tagStrValue) . "/", $replaceStr, $content);
                        }
                        echo $this->smileyToEmoticons($content);
                      } else {
                        echo $this->smileyToEmoticons($this->viewMore($item->body));
                      }
                    }
                    ?>
    <?php if (!empty($item->attachment_type) && null !== ($attachment = $this->item($item->attachment_type, $item->attachment_id))): ?>
                      <div class="seaocore_comments_attachment" id="seaocore_comments_attachment">
                        <div class="seaocore_comments_attachment_photo">
                          <?php if (null !== $attachment->getPhotoUrl()): ?>
                           <?php if (SEA_ACTIVITYFEED_LIGHTBOX && strpos($item->attachment_type, '_photo')): ?>
                                 <?php if(!Engine_Api::_()->hasModuleBootstrap('sitealbum') ) :?>
                                    <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo')), array('onclick' => 'openSeaocoreLightBox("' . $attachment->getHref() . '");return false;')) ?>
                                 <?php else:?>
                                    <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo')), array('onclick' => 'openLightBoxAlbum("' . $attachment->getHref() . '", "'. Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($attachment). '");return false;')) ?>
                                 <?php endif;?>
                                 <?php else:?>
                                 <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo'))) ?>
                                 <?php endif;?>
      <?php endif; ?>
                        </div>
                        <div class="seaocore_comments_attachment_info">
                          <div class="seaocore_comments_attachment_title">
      <?php echo $this->htmlLink($attachment->getHref(array('message' => $item->comment_id)), $attachment->getTitle()) ?>
                          </div>
                          <div class="seaocore_comments_attachment_des">
      <?php echo $attachment->getDescription() ?>
                          </div>
                        </div>
                      </div>
    <?php endif; ?>	
    
                              
                              </div>
                            </div>
                          </div>
                        </div>
                      
                    <?php else:?>
                      <?php echo $this->htmlLink($poster->getHref(), $poster->getTitle()); ?>
                    <?php endif;?>
                    
                    
                  &nbsp;&nbsp;&#183;&nbsp;&nbsp;<?php echo $this->translate("%s", $this->timestamp($comment->creation_date)); ?>
                </div>
                <div id="seaocore_data-<?php echo $comment->comment_id ?>">
                  <div class="seaocore_replies_comment" id="seaocore_comment_data-<?php echo $comment->comment_id ?>">

                    <?php $content = $comment->body ?>
                    <?php $item = $comment;?>
                    <?php 
                        include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_commentBody.tpl';
                    ?>
    <?php if (!empty($comment->attachment_type) && null !== ($attachment = $this->item($comment->attachment_type, $comment->attachment_id))): ?>
                      <div class="seaocore_comments_attachment" id="seaocore_comments_attachment">
                        <div class="seaocore_comments_attachment_photo">
                          <?php if (null !== $attachment->getPhotoUrl()): ?>
                           <?php if (SEA_ACTIVITYFEED_LIGHTBOX && strpos($comment->attachment_type, '_photo')): ?>
                                 <?php if(!Engine_Api::_()->hasModuleBootstrap('sitealbum') ) :?>
                                    <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo')), array('onclick' => 'openSeaocoreLightBox("' . $attachment->getHref() . '");return false;')) ?>
                                 <?php else:?>
                                    <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo')), array('onclick' => 'openLightBoxAlbum("' . $attachment->getHref() . '", "'. Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($attachment). '");return false;')) ?>
                                 <?php endif;?>
                                 <?php else:?>
                                 <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo'))) ?>
                                 <?php endif;?>
      <?php endif; ?>
                        </div>
                        <div class="seaocore_comments_attachment_info">
                          <div class="seaocore_comments_attachment_title">
      <?php echo $this->htmlLink($attachment->getHref(array('message' => $comment->comment_id)), $attachment->getTitle()) ?>
                          </div>
                          <div class="seaocore_comments_attachment_des">
      <?php echo $attachment->getDescription() ?>
                          </div>
                        </div>
                      </div>
    <?php endif; ?>	
                  </div>
                    <div id="seaocore_edit_comment_<?php echo $comment->comment_id;?>" style="display: none;" class="comment_edit">
                    <?php include APPLICATION_PATH . '/application/modules/Nestedcomment/views/scripts/comment/edit.tpl'; ?>
                  </div>
                    
                    <?php if($this->viewer()->getIdentity()):?>
                        <div id="close_edit_box-<?php echo $comment->comment_id;?>" class="seaocore_txt_light f_small comment_close" style="display:none;">
                             <?php echo $this->translate("Press Esc to ");?><a href='javascript:void(0);' onclick="closeEdit('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $comment->getIdentity() ?>','<?php echo $comment->parent_comment_id ?>');"><?php echo $this->translate('cancel');?></a>
                        </div>
                    
                    
                    <?php endif;?>
                  <div class="seaocore_replies_date seaocore_txt_light">

                        <?php if ($this->canComment && $this->showLikeWithoutIconInReplies != 3): ?>
                          <?php if ($this->showAsNested): ?>
                            <?php if (isset($this->formComment)):?>
                            <?php if($this->photoLightboxComment): ?>
                              <?php if($comment->parent_comment_id == 0) :?>
                                    <a href='javascript:void(0);' onclick="showReplyForm('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $comment->getIdentity() ?>');"><?php echo $this->translate('SEREPLY') ?></a> &nbsp;&nbsp;&#183;&nbsp;&nbsp;
                                <?php endif; ?>
                             <?php else:?>
                                    <?php if($replyLink):?>
                                        <a href='javascript:void(0);' onclick="showReplyForm('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $comment->getIdentity() ?>');"><?php echo $this->translate('SEREPLY') ?></a> &nbsp;&nbsp;&#183;&nbsp;&nbsp;
                                    <?php else:?>
                                        <?php if($comment->parent_comment_id == 0) :?>
                                            <a href='javascript:void(0);' onclick="showReplyForm('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $comment->getIdentity() ?>');"><?php echo $this->translate('SEREPLY') ?></a> &nbsp;&nbsp;&#183;&nbsp;&nbsp;
                                        <?php endif; ?>
                                    <?php endif;?>
                              <?php endif;?>
                            <?php endif; ?>
                          <?php endif; ?>
                        <?php endif; ?>
                    
                      <?php if ($comment->likes()->getLikeCount() > 0 && $this->showLikeWithoutIconInReplies == 3): ?>
                        <?php if($this->viewer()->getIdentity()):?>
                           
                            
                            <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => $this->showLikeWithoutIconInReplies), 'default', true);?>
                      <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $comment->likes()->getLikeCount()?></a>
                      <?php else:?>
                        
                      <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => $this->showLikeWithoutIconInReplies), 'default', true);?>
                      <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s vote up', '%s vote ups', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount()))?></a>
                      
                      
                      <?php endif;?>
                        
                      <?php endif ?>
                      
											<?php if($this->showLikeWithoutIconInReplies == 1 && $this->viewer()->getIdentity()):?>
													<?php if(!Engine_Api::_()->getDbtable('likes', 'core')->isLike($comment,$this->viewer())):?>
													<a id="like_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?> onclick="en4.nestedcomment.nestedcomments.like(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?> ><?php echo $this->translate('like') ?></a>
													<?php else: ?>
														<?php echo $this->translate('like') ?>
													<?php endif;?>
											<?php elseif($this->showLikeWithoutIconInReplies == 0 && $this->viewer()->getIdentity()):?>
											<?php if(!Engine_Api::_()->getDbtable('likes', 'core')->isLike($comment,$this->viewer())):?>
													<a id="like_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?>onclick="en4.nestedcomment.nestedcomments.like(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?>>
														<img class="mbot5" src="application/modules/Nestedcomment/externals/images/like.png" /> <?php echo $this->translate('like') ?></a>
													<?php else: ?>
														<img class="mbot5" src="application/modules/Nestedcomment/externals/images/like_light.png" /> <?php echo $this->translate('like') ?>
													<?php endif;?>
											<?php elseif($this->showLikeWithoutIconInReplies == 2 && $this->viewer()->getIdentity()):?>
											<?php if(!Engine_Api::_()->getDbtable('likes', 'core')->isLike($comment,$this->viewer())):?>
													<a id="like_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?>onclick="en4.nestedcomment.nestedcomments.like(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?>>
														<img class="mbot5" src="application/modules/Nestedcomment/externals/images/like.png" /></a>
													<?php else: ?>
														<img class="mbot5" src="application/modules/Nestedcomment/externals/images/like_light.png" />
													<?php endif;?>
                      <?php elseif($this->showLikeWithoutIconInReplies == 3 && $this->viewer()->getIdentity()):?>
											<?php if(!Engine_Api::_()->getDbtable('likes', 'core')->isLike($comment,$this->viewer())):?>
													<a id="like_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?>onclick="en4.nestedcomment.nestedcomments.like(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>', '<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?>>
														<img title="<?php echo $this->translate('Vote up');?>" src="application/modules/Nestedcomment/externals/images/arrow-up.png" /></a>
													<?php else: ?>
														<img title="<?php echo $this->translate('Vote up');?>" src="application/modules/Nestedcomment/externals/images/arrow-up_light.png" />
													<?php endif;?>    
											<?php endif;?>
                      
                      <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0 && $this->showLikeWithoutIconInReplies == 3): ?>
                      
                       
                      
                        <?php if($this->viewer()->getIdentity()):?> &nbsp;&nbsp;|&nbsp;&nbsp;
                            <?php if($this->showDislikeUsers) :?>
                                
                                 <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => $this->showLikeWithoutIconInReplies), 'default', true);?>
                      <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )?></a>
                                
                                
                                
                            <?php else:?>
                              <b><?php echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment );?></b>
                            <?php endif;?>
                        <?php else:?>
                            <?php if ($comment->likes()->getLikeCount() > 0):?>
                             &nbsp;&nbsp;|&nbsp;&nbsp;
                           <?php endif;?>
                            <?php if($this->showDislikeUsers) :?>
                                
                                <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => $this->showLikeWithoutIconInReplies), 'default', true);?>
                      <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s vote down', '%s vote downs', Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )))?></a>
                                
                                
                            <?php else:?>
                                <b><?php echo $this->translate(array('%s vote down', '%s vote downs', Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )));?></b>
                            <?php endif;?>
                        <?php endif;?>
                    <?php endif ?>

										<?php if($this->showLikeWithoutIconInReplies == 1 && $this->viewer()->getIdentity()):?>
											<?php if(!Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislike($comment, $this->viewer())):?>
													&nbsp;&nbsp;&middot;&nbsp;&nbsp;<a id="unlike_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?>onclick="en4.nestedcomment.nestedcomments.unlike(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>','<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?>><?php echo $this->translate('dislike') ?></a>
											<?php else: ?>
													&nbsp;&nbsp;&middot;&nbsp;&nbsp;<?php echo $this->translate('dislike') ?>
											<?php endif;?>
											<?php elseif($this->showLikeWithoutIconInReplies ==0 && $this->viewer()->getIdentity()):?>
											<?php if(!Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislike($comment, $this->viewer())):?>
													&nbsp;&nbsp;&middot;&nbsp;&nbsp;<a id="unlike_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?>onclick="en4.nestedcomment.nestedcomments.unlike(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>','<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?>> <img src="application/modules/Nestedcomment/externals/images/dislike.png" /> <?php echo $this->translate('dislike') ?></a>
											<?php else: ?>
													&nbsp;&nbsp;&middot;&nbsp;&nbsp;<img src="application/modules/Nestedcomment/externals/images/dislike_light.png" /> <?php echo $this->translate('dislike') ?>
											<?php endif;?>
											<?php elseif($this->showLikeWithoutIconInReplies == 2 && $this->viewer()->getIdentity()):?>
											<?php if(!Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislike($comment, $this->viewer())):?>
													&nbsp;&nbsp;&middot;&nbsp;&nbsp;<a id="unlike_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?>onclick="en4.nestedcomment.nestedcomments.unlike(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>','<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?>><img src="application/modules/Nestedcomment/externals/images/dislike.png" /></a>
											<?php else: ?>
													&nbsp;&nbsp;&middot;&nbsp;&nbsp;<img src="application/modules/Nestedcomment/externals/images/dislike_light.png" />
											<?php endif;?>
                      <?php elseif($this->showLikeWithoutIconInReplies == 3 && $this->viewer()->getIdentity()):?>
											<?php if(!Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislike($comment, $this->viewer())):?>
                      
                      <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) == 0 && $this->showLikeWithoutIconInReplies == 3): ?>
                     &nbsp;&nbsp;|&nbsp;&nbsp;
                      <?php endif;?>
													 	<a id="unlike_comments_<?php echo $comment->getIdentity(); ?>" style="display:inline-block;" href="javascript:void(0)" <?php if($this->viewer()->getIdentity()):?>onclick="en4.nestedcomment.nestedcomments.unlike(<?php echo sprintf("'%s', %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->order ?>','<?php echo $this->parent_comment_id ?>', 'child', '<?php echo $this->taggingContent ?>', '<?php echo $this->showAsNested ?>', '<?php echo $this->showAsLike ?>', '<?php echo $this->showDislikeUsers ?>', '<?php echo $this->showLikeWithoutIcon ?>', '<?php echo $this->showLikeWithoutIconInReplies ?>', '<?php echo $this->page ;?>')" <?php endif;?>><img title="<?php echo $this->translate('Vote down');?>" src="application/modules/Nestedcomment/externals/images/arrow-down.png" /></a>
											<?php else: ?>
												 	<img title="<?php echo $this->translate('Vote down');?>" src="application/modules/Nestedcomment/externals/images/arrow-down_light.png" />
											<?php endif;?>
											<?php endif; ?>
 
                    
                    <?php if ($comment->likes()->getLikeCount() > 0 && $this->showLikeWithoutIconInReplies != 3): ?>
                           <?php if($this->canComment):?>
														&nbsp;&nbsp;&middot;&nbsp;&nbsp;
                            <?php endif;?>
											
                      
                      
                      <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                      <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s likes this.', '%s like this.', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount()));?></a>
                      

                    <?php endif ?>
                    
                    <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0 && $this->showLikeWithoutIconInReplies != 3): ?>
                    
                    
                    <?php if($this->canComment || $comment->likes()->getLikeCount() > 0):?>
                      &nbsp;&nbsp;&middot;&nbsp;&nbsp;
                      <?php endif;?>
                      
                      
											<?php if($this->showDislikeUsers) :?>
												
                        
                        <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                      <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s dislikes this.', '%s dislike this.', Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )))?></a>
                      
                      
												<?php else:?>
													<b><?php echo $this->translate(array('%s dislikes this.', '%s dislikes this.', Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )));?></b>
												<?php endif;?>
                    <?php endif ?>
                    
                    <?php if ($this->canComment && $this->showLikeWithoutIconInReplies == 3): ?>

                      <?php if ($this->showAsNested): ?>
        <?php if (isset($this->formComment)):?>
&nbsp;&nbsp;&#183;&nbsp;&nbsp;
                          <?php if($this->photoLightboxComment): ?>
                              <?php if($comment->parent_comment_id == 0) :?>
                                    <a href='javascript:void(0);' onclick="showReplyForm('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $comment->getIdentity() ?>');"><?php echo $this->translate('SEREPLY') ?></a>
                                <?php endif; ?>
                            
                             <?php else:?>
                                    <?php if($replyLink):?>
                                <a href='javascript:void(0);' onclick="showReplyForm('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $comment->getIdentity() ?>');"><?php echo $this->translate('SEREPLY') ?></a>
                                    <?php else:?>
                                        <?php if($comment->parent_comment_id == 0) :?>
                                            <a href='javascript:void(0);' onclick="showReplyForm('<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $comment->getIdentity() ?>');"><?php echo $this->translate('SEREPLY') ?></a> 
                                    <?php endif; ?>
                                <?php endif;?>
                                
                              <?php endif;?>
                        <?php endif; ?>
                      <?php endif; ?>

	<?php endif; ?>
  
                  </div>  
                </div>
              </div>
            </div>
            <?php if ($this->showAsNested): ?>
              <?php if ($this->format): ?>
                <?php echo $this->action("list", "comment", "nestedcomment", array("type" => $this->subject->getType(), "id" =>
                    $this->subject->getIdentity(), 'format' => 'html', 'parent_comment_id' => $comment->comment_id, 'page' => 0, 'parent_div' => 1, 'taggingContent' => $this->taggingContent, 'showAsNested' => $this->showAsNested, 'showAsLike' => $this->showAsLike, 'showDislikeUsers' => $this->showDislikeUsers, 'showLikeWithoutIcon' => $this->showLikeWithoutIcon, 'showLikeWithoutIconInReplies' => $this->showLikeWithoutIconInReplies, 'showSmilies' => $this->showSmilies, 'showComposerOptions' => $this->showComposerOptions, 'photoLightboxComment' => $this->photoLightboxComment, 'commentsorder' => $this->commentsorder)); ?>
              <?php else: ?>
                <?php echo $this->action("list", "comment", "nestedcomment", array("type" => $this->subject->getType(), "id" =>
                    $this->subject->getIdentity(), 'parent_comment_id' => $comment->comment_id, 'page' => 0, 'parent_div' => 1, 'taggingContent' => $this->taggingContent, 'showAsNested' => $this->showAsNested, 'showAsLike' => $this->showAsLike, 'showDislikeUsers' => $this->showDislikeUsers, 'showLikeWithoutIcon' => $this->showLikeWithoutIcon, 'showLikeWithoutIconInReplies' => $this->showLikeWithoutIconInReplies, 'showSmilies' => $this->showSmilies, 'showComposerOptions' => $this->showComposerOptions, 'photoLightboxComment' => $this->photoLightboxComment, 'commentsorder' => $this->commentsorder)); ?>
              <?php endif; ?>  
          <?php endif; ?>  
          </li>
        <?php endfor; ?>

  <?php if ($this->comments->getCurrentPageNumber() < $this->comments->count()): ?>
          <li>
            <div> </div>
            <div class="seaocore_replies_viewall">
              <?php
              echo $this->htmlLink('javascript:void(0);', $this->translate('View later comments'), array(
                  'onclick' => 'en4.nestedcomment.nestedcomments.loadComments("' . $this->subject->getType() . '", "' . $this->subject->getIdentity() . '", "' . ($this->page + 1) . '", "' . $this->order . '", "' . $this->parent_comment_id . '", "' . $this->taggingContent . '", 1, "' . $this->showAsNested . '", "' . $this->showAsLike . '", "' . $this->showDislikeUsers . '", "' . $this->showLikeWithoutIcon . '", "' . $this->showLikeWithoutIconInReplies . '")', 'class' => 'mright5 icon_next buttonlink'
              ))
              ?>
              <div id="view_later_comments_<?php echo $this->parent_comment_id; ?>" style="display:none;"></div>
            </div>
          </li>
      <?php endif; ?>
      </ul>
  <?php endif; ?>

  <?php if (!$this->page): ?>
    </div>
<?php endif; ?>
<?php if (empty($this->parent_div)): ?>
  </div>
<?php endif; ?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    $($('comments-form_<?php echo $this->nested_comment_id ?>').body).autogrow();
    if(($('comments-form_<?php echo $this->subject->getGuid() ?>_0').getElementById('compose-container')) == null) {
      makeComposer($($('comments-form_<?php echo $this->subject->getGuid() ?>_0').body).id, '<?php echo $this->subject->getType() ?>', '<?php echo $this->subject->getIdentity() ?>', 0);
      tagContentComment();
    }
  });
  
//  function showSortComments() {
//			 $('sorting_dropdown_menu').toggle();
//		}
</script>

<?php if($this->showSmilies && !$this->nestedCommentPressEnter): ?>
<style type="text/css">
	#nested-comment-compose-link-activator, #nested-compose-link-menu span {
		margin-right: 28px;
	}
</style>
<?php endif; ?>
<?php if($this->showSmilies && $this->showComposerOptions && !in_array('addLink', $this->showComposerOptions) && !$this->nestedCommentPressEnter): ?>
<style type="text/css">
	#nested-comment-compose-photo-activator, #nested-compose-link-menu span {
		margin-right: 28px;
	}
</style>
<?php endif; ?>
<?php if(!$this->nestedCommentPressEnter): ?>
<style type="text/css">
	.seaocore_replies .compose-menu {
		width: 100%;
		display: inline-table;
	}
</style>
<?php endif; ?>
