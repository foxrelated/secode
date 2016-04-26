<?php ?>
<li>
<div class='feed_item_body'>
  <?php //$counter = 1;
  foreach ($this->viewmoreResult as $item) { //for display post user ?>
  <?php //if ($counter  > 1) :
  //break; ?>
  <?php //endif;?>
  <li>

    <div class='feed_item_photo'>
      <?php
        $action = Engine_Api::_()->getItem('activity_action', $item->action_id);
        $user_subject = Engine_Api::_()->user()->getUser($item->subject_id);
        $profile_url = $this->url(array('id' => $item->subject_id), 'user_profile'); ?>
        <a href="<?php echo $profile_url ?>"  target="_blank"> <?php echo $this->itemPhoto($this->user($user_subject), 'thumb.icon') ?></a>
    </div>
    <div class="feed_item_body">
      <span class="feed_item_posted">
        <a href="<?php echo $profile_url ?>" target="_blank"><?php echo $this->user($user_subject)->getTitle() ?></a>
      </span>

      <div class="feed_item_link_desc">
        <?php echo $action->body; //for display body text send by the user ?>
      </div>
      <?php // Icon, time since, action links ?>
      <?php
      $icon_type = 'activity_icon_'.$action->type;
      list($attachment) = $action->getAttachments();
      if( is_object($attachment) && $action->attachment_count > 0 && $attachment->item ):
      $icon_type .= ' item_icon_'.$attachment->item->getType() . ' ';
      endif;
      //$canComment = ( $action->getTypeInfo()->commentable &&
    // $this->viewer()->getIdentity() && Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') && !empty($this->commentForm) );
      ?>
      <div class="feed_item_date feed_item_icon activity_icon_post">
        <ul>
        <li>
          <?php echo $this->timestamp($action->getTimeValue()) ?>
        </li>
        <?php //if( $canComment ):   ?>
          <?php if( Engine_Api::_()->getApi('settings', 'core')->core_spam_comment ): // Comments - likes ?>
            <li class="feed_item_option_comment">
              <span>-</span>
              <?php echo $this->htmlLink(array('route'=>'default','module'=>'activity','controller'=>'index','action'=>'viewcomment','action_id'=>$action->getIdentity(),'format'=>'smoothbox'),
                $this->translate('Comment'), array('class'=>'smoothbox', )) ?>
            </li>
          <?php else: ?>
            <li class="feed_item_option_comment">
              <span>-</span>
              <?php //echo $this->htmlLink('javascript:void(0);', $this->translate('Comment'), array('onclick'=>'document.getElementById("'.$this->commentForm->getAttrib('id').'").style.display = "";
                //document.getElementById("'.$this->commentForm->submit->getAttrib('id').'").style.display = "block";
              //document.getElementById("'.$this->commentForm->body->getAttrib('id').'").focus();'))?>
            </li>
          <?php endif; ?>
        <?php //endif; ?>

        <?php if( $action->likes()->isLike($this->viewer()) ): ?>
          <li class="feed_item_option_unlike">
            <span>-</span>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Unlike'), array('onclick'=>'javascript:en4.activity.unlike('.$action->action_id.');')) ?>
          </li>
        <?php else: ?>
          <li class="feed_item_option_like">
          <span>-</span>
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('onclick'=>'javascript:en4.activity.like('.$action->action_id.');')) ?>
          </li>
        <?php endif; ?>
      <?php //if( $this->viewer()->getIdentity() && ($this->activity_moderate || ( $this->allow_delete && (
          // ('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) || ('user' == $action->object_type && $this->viewer()->getIdentity()
          // == $action->object_id) ))) ): ?>
        <li class="feed_item_option_delete">
          <span>-</span>
          <?php echo $this->htmlLink(array(
            'route' => 'default',
            'module' => 'advancedactivity',
            'controller' => 'index',
            'action' => 'delete',
            'action_id' => $action->action_id
          ), $this->translate('Delete'), array('class' => 'smoothbox')) ?>
        </li>
        </ul>
        </div>
      <?php //endif; ?>
    </div>
  </li>
  <?php //$counter++ ;
  } ?>
  </div>
 </li>