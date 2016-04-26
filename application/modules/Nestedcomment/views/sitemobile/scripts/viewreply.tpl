<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: viewreply.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  $this->headTranslate(array(
     'Write a reply...',
     'Unlike',
     '% likes this',
     'like',    
     '% like',
     'Like'
   
  ));
?>

<?php //GET VERSION OF SITEMOBILE APP.
  $RemoveClassDone = true;
  if(Engine_Api::_()->sitemobile()->isApp()) {
    if(!Engine_Api::_()->nestedcomment()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitemobileapp')->version, '4.8.6p1'))
      $RemoveClassDone = false;
  }

?>

<?php  
$action = $this->action;
$canComment = ( $action->getTypeInfo()->commentable && $action->commentable &&
        $this->viewer()->getIdentity() &&
        Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') &&
        !empty($this->replyForm) );

?>
<?php  
  include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/_activitySettings.tpl';
?> 
<div id='reply-activity-item-<?php echo $action->getIdentity() ?>' class="ps-carousel-comments sm-ui-popup sm-ui-popup-container-wrapper" style="<?php echo $this->translate(($action->commentable) ? 'display:block' : 'display:block;') ?>">

  <?php if ($action->getTypeInfo()->commentable): // Comments - likes  ?>
  
    <div class="" id="showhide-comments-<?php echo $this->comment_id ?>" style="display:block">

      <div class="sm-ui-popup-top ui-header ui-bar-a">
        
        
        <a href="javascript:void(0);" data-role="button" data-iconpos="notext" data-icon="remove" data-corners="true" data-shadow="true" data-iconshadow="true" class="ui-btn-right ui-link ui-btn ui-icon-remove ui-btn-icon-notext ui-shadow-icon ui-shadow ui-corner-all" onclick=" $('.ui-page-active').removeClass('dnone'); $('#replypopup').remove();$(window).scrollTop(parentScrollTop)" role="button"></a>
        
        <?php $replyCount = $action->getReplies($this->comment_id, $this->viewAllComments, true);?>
        <h2 class="ui-title" id="count-feedreplies"><?php echo $this->translate(array('%s Reply', '%s Replies', $replyCount), $this->locale()->toNumber($replyCount)); ?></h2>
      </div>
      <div class="sm-ui-popup-container" style="bottom:95px;">
        <div class="comments">
          <ul class="viewreply">
            
            <?php if ($action->getReplies($this->comment_id, $this->viewAllComments, true) > 5 || $this->viewAllComments): ?>
              <li class="comments_likes" onclick="sm4.activity.getOlderReplies(this, '<?php echo $action->getObject()->getType() ?>', '<?php echo $action->getObject()->getIdentity() ?>', '2', '<?php echo $this->action_id ?>', '<?php echo $this->comment_id; ?>');">
                <a href="javascript:void(0);" ><?php echo $this->translate('Load Previous Replies') ?></a>
              </li>
            <?php endif; ?>
            <?php if (count($action->getReplies($this->comment_id)) > 0): ?>
              <?php foreach ($action->getReplies($this->comment_id) as $this->comment): ?>
                <?php $comment = $this->comment;?>
                <li id="reply-<?php echo $this->comment->getIdentity(); ?>">  
                  <?php 
                    include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/view.tpl';
                  ?>
                </li>                         
              <?php endforeach; ?>
            <?php else : ?>
              <li>
                <div class="no-comments">
                  <i class="ui-icon ui-icon-comment-alt"></i>
                  <span><?php echo $this->translate('No Replies') ?></span>
                </div>	
              </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>

      </div>
    <?php 
    if ($canComment && $showAsNested) :
      $this->replyForm->setActionIdentity($this->comment_id);
      ?>
      <div style="display:none;" class="sm-comments-post-comment-form"  id="hide-commentform-<?php echo $this->comment_id ?>">
        
        <table cellspacing="0" cellpadding="0">
          <tr>
            <td class="sm-cmf-left">
                <?php echo $this->replyForm->render(); ?>
            </td>
            <td>
              <button class="ui-btn-default ui-btn-c" data-role="none" type="submit"  onclick="sm4.activity.attachReply($('#activity-reply-form-<?php echo $this->comment_id ?>'), '<?php echo $this->comment_id ?>', '<?php echo $this->action->action_id ?>');" style="background-color:transparent !important;"><i class="ui-icon ui-icon-post"></i></button>
            </td>
          </tr>
        </table>		
        
        <?php if($photoEnabled || $smiliesEnabled || $taggingEnabled):?>  
        		<div class="cont-sep t_l b_medium"></div>
            <div id="activitypost-container-temp" action-id='<?php echo "activity-reply-form-$this->comment_id" ;?>'>
            	<div class="compose_buttons">
                <?php if($photoEnabled):?>
                    <div id="composer-nested-reply-options_<?php echo $this->comment_id;?>" class="fleft">
                      <!--<div id="smactivityoptions-popup" class="sm-post-composer-options">-->
                          <?php if (Engine_Api::_()->sitemobile()->enableComposer('photo')) : ?>
                              <a href="javascript:void(0);" onclick="return sm4.activity.composer.showReplyPluginForm(this, 'photo', '#activity-reply-body-<?php echo $action->action_id ?>', '<?php echo $this->comment_id;?>');" class="ui-link-inherit">
                                <i class="cm-icons cm-icon-photo"></i>
                              </a> 
                          <?php endif; ?>
                        
                      <!--</div>-->     
                    </div>
                <?php endif; ?>
               <?php if ($smiliesEnabled): ?>
                    <?php
                    $enableSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy"));
                    $SEA_EMOTIONS_TAG = unserialize(SEA_EMOTIONS_TAG);
                    if (in_array("withtags", $enableSettings)):
                        ?>
                        <a href="javascript:void(0);" data-role="none" id="emoticons-reply-button_<?php echo $this->comment_id;?>"  class="emoticons-button"  onclick="setEmoticonsReplyBoard(<?php echo $this->comment_id;?>);
                                  sm4.activity.statusbox.toggleReplyEmotions($(this), '<?php echo $this->comment_id;?>');" >
                            <i class="cm-icons cm-icon-emoticons"></i>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if ($taggingEnabled): ?>
                    <a id="comment-add-people_<?php echo $this->comment_id;?>" href="javascript:void(0);" data-role="none" onclick="sm4.activity.composer.showReplyPluginForm(this, 'addpeople', '#activity-reply-body-<?php echo $this->comment_id ?>', '<?php echo $this->comment_id;?>');">
                        <i class="cm-icons cm-icon-user"></i>
                    </a>
                <?php endif; ?>
              </div>
              <div id="reply_options_box_<?php echo $this->comment_id;?>">
                <?php if ($smiliesEnabled): ?>
                    <div id="emoticons-reply-board_<?php echo $this->comment_id;?>" class="compose_embox_cont ui-page-content <?php if (Engine_Api::_()->sitemobile()->isApp()) echo 'compose-footer'; ?>" style="display:none;">
                        <div class="sm-seaocore-embox">
                            <span class="sm-seaocore-embox-arrow ui-icon ui-icon-caret-up"></span>
                                <?php foreach ($SEA_EMOTIONS_TAG[0] as $tag_key => $tag): ?>         
                                    <span class="sm-seaocore-embox-icon" onmouseover='setEmotionLabelPlate("<?php echo $this->string()->escapeJavascript($this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag))) ?>", "<?php echo $this->string()->escapeJavascript($tag_key) ?>")' onclick='addEmotionIconNestedComment("<?php echo $this->string()->escapeJavascript($tag_key) ?>", "#activity-reply-body-"+<?php echo $this->comment_id;?>,<?php echo $this->comment_id;?> )' title="<?php echo $this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag)) . "&nbsp;" . $tag_key; ?>">
                                        <?php
                                        echo preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "<img src=\"" . $this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/emoticons/$1\" border=\"0\" alt=\"$2\" />", $tag);
                                        ?>
                                    </span>
                                <?php endforeach; ?>
                        </div>
                    </div>
                                <?php endif; ?>
                
                <?php if ($taggingEnabled): ?>
                    <div id="adv_reply_post_container_tagging_<?php echo $this->comment_id;?>" class="post_container_tags ui-page-content" style="display:none;" title="<?php echo $this->translate('Who are you with?') ?>" >
                        <div id="aff_reply_mobile_aft_search_<?php echo $this->comment_id;?>-element">
                            <div class="sm-post-search-fields">
                                <table width="100%">
                                    <tr>
                                        <td class="sm-post-search-fields-left">
                                            <input class="ui-input-field " type="text" autocomplete="off" value="" id="aff_reply_mobile_aft_search_<?php echo $this->comment_id;?>" name="aff_mobile_aft_search" placeholder='<?php echo $this->translate("Start typing a name..."); ?>' data-role="none" />
                                        </td>
                                    </tr>
                                </table>			
                                <span role="status" aria-live="polite"></span>
                            </div>
                            <div id="toReplyValues-temp_<?php echo $this->comment_id;?>-wrapper" style="border:none;display:none;">
                                <div id="toReplyValues-temp_<?php echo $this->comment_id;?>-element">
                                    <input type="hidden" id="toReplyValues-temp_<?php echo $this->comment_id;?>" value=""  name="toValues-temp" />
                                    <input type="hidden" id="toReplyValues_<?php echo $this->comment_id;?>" value=""  name="toValues" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="toReplyValuesdone-wrapper_<?php echo $this->comment_id;?>" style="display:none;"></div>
                    <div class="ui-header o_hidden" id="ui-post-reply-header-addpeople_<?php echo $this->comment_id;?>" style="display:none;">
                        <button id="compose-submit" data-role="button" data-icon="false" class="ui-btn-left ui-btn-default" onclick="$('#aff_reply_mobile_aft_search_'+<?php echo $this->comment_id;?>).val('');
                          sm4.activity.composer.addpeople.addReplyFriends('#reply-form_'+<?php echo $this->comment_id;?>, '<?php echo $this->comment_id;?>');
                          return false;" style="background-color:transparent !important;"><?php echo $this->translate("Add") ?></button>
                        <?php if (!Engine_Api::_()->sitemobile()->isApp()) : ?>
                            <a data-role="button" data-icon="false" href="" data-wrapperels="span"  class="ui-btn-right" onclick="$('#aff_reply_mobile_aft_search_'+<?php echo $this->comment_id;?>).val(''); sm4.activity.toggleReplyPostArea('<?php echo $this->comment_id;?>');"><?php echo $this->translate('Cancel'); ?></a>
                        <?php else: ?>
                            <a href='javascript://' class='ui-btn-right'  data-role="button" data-icon='arrow-l'  data-iconpos="notext"  data-logo="true" onclick="$('#aff_reply_mobile_aft_search_'+<?php echo $this->comment_id;?>).val('');sm4.activity.toggleReplyPostArea('<?php echo $this->comment_id;?>');" ></a>
                    <?php endif; ?>
                    </div>
            <?php endif; ?>
               </div>
            </div> 
            <?php endif; ?>
       	
        <div style="display:none;"> 
          <script type="text/javascript">
              sm4.core.runonce.add(function(){
                $('#activity-reply-body-<?php echo $this->comment_id ?>').autoGrow();          
                $('.sm-comments-post-comment-<?php echo $this->comment_id ?>').on('click',function(){
                sm4.activity.toggleReplyArea(this, '<?php echo $this->comment_id ?>');
              });
<?php if ($this->writereply): ?>
                    sm4.activity.toggleReplyArea($('.sm-comments-post-comment-<?php echo $this->comment_id ?>'), '<?php echo $this->comment_id ?>');                 
<?php endif; ?>
              });                   
          </script>
        </div>
      </div>
      <div class="sm-comments-post-comment sm-comments-post-comment-<?php echo $this->comment_id ?>" >
        <div>
          <input type="text" placeholder="<?php echo $this->translate('Write a reply...'); ?>" data-role="none" class="ui-input-field" />
        </div> 
      </div>
  <?php endif;?>
  
  <?php endif; ?>

</div> <!-- End of Comment Likes -->

<div id='like-activity-item-<?php echo $action->getIdentity() ?>' class="feed_item_show_comments_likes_wrapper" style="display:none;"></div>
<div id='like-reply-item-<?php echo $action->getIdentity() ?>' class="feed_item_show_comments_likes_wrapper" style="display:none;"></div>
<div id='dislike-reply-item-<?php echo $action->getIdentity()?>' class="feed_item_show_comments_likes_wrapper" style="display:none;"></div>