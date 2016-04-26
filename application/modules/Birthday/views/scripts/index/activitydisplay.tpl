<?php ?>

<script>
var flag = 0;
function content_show(username, users_id) {

  if (flag == 0) {
  $('activity_textarea-'+users_id).style.display='block';
  flag = 1;
  } else {
  $('activity_textarea-'+users_id).style.display='none';
  flag = 0;
  }
}

var activitywriteonclick = function (thisobj, wallvalue, event, object_id) {

  if (event == 1) {
    if (thisobj.value == wallvalue) { 
      thisobj.value = '';
      $('activity-write-body-' + object_id).value = '';
    }
  }
  else if (event == 2 && thisobj.value == '') {

    (function() {
      thisobj.value = wallvalue;
    }).delay(250);
  }
}
  
//   function activitywriteonclick() { 
//     if( flag == 0 ) {
//       $('activity-write-body').value = '';
//     }
//     //flag = 1;
//   }
//   function activitywritewallbody(wallvalue) {  
//   if( flag == 0 ) {
//       $('activity-write-body').value = 'Write on ' + wallvalue + ' wall...';
//     }
//   }

  function statusubmit(e, users_id, boxvalue) {
    var keycode=null;
    var url = '';
    if (e!=null){ 
      if (window.event!=undefined){
      if (window.event.keyCode) keycode = window.event.keyCode;
        else if (window.event.charCode) keycode = window.event.charCode;
      } else{
        keycode = e.keyCode;
      }
    }
    if( keycode != 13 ) {
      if( $('activity-write-body-' + users_id).value == '' ) {
        flag = 0;
      } else {
        flag = 1;
      }
    } else {
      if( $('activity-write-body-' + users_id).value == '' ) {
        return;
      }
      url = en4.core.baseUrl + 'birthday/index/statusubmit';
      en4.core.request.send(new Request.HTML({
        url : url,
        data : {
          format : 'html',
          object_id : users_id,
          body : $('activity-write-body-' +users_id).value
        },
        'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
        {
          if($('birthday_show_1_' + users_id)){ $('birthday_show_1_' + users_id ).innerHTML = $('birthday_show_0_' + users_id).innerHTML; }

          $('birthday_show_0_' + users_id).innerHTML = responseHTML;
        }
      }))

      $('activity_textarea-'+users_id).style.display='none';
      $('activity-write-body-' +users_id).value = 'Write on ' + boxvalue + ' wall...';
      flag = 0;
    }
  }


</script>



<ul class="feed">
<?php 
foreach ($this->member_birthday as $item):  //for dispaly birthday user user ?>

<li >
  <?php   if($this->countResult) {
  $user_subject = Engine_Api::_()->user()->getUser($item->object_id);
  $profile_url = $this->url(array('id' => $item->object_id), 'user_profile');

  $getActivityResult = Engine_Api::_()->birthday()->get_activityDisplay($item->object_id);  ?>
  <div class='feed_item_photo'>
   <?php echo $this->translate($getActivityResult['image']); ?>
  </div>
  <div class='feed_item_body'>
    <span class="feed_item_posted">
    <?php
    echo $this->translate($getActivityResult['titleStr'] . ' wrote on ') ?><a href="<?php echo $profile_url ?>" target="_blank"><?php echo $this->user($user_subject)->getTitle() ?></a><?php echo
    $this->translate(' for his birthday.' ); ?>
    </span>

    <div class='feed_item_attachments' id="feed_item_attachments-<?php echo $item->object_id ?>">
      <span class="feed_attachment_core_link">
        <div>
          <a href="<?php echo $profile_url ?>"  target="_blank"> <?php echo $this->itemPhoto($this->user($user_subject), 'thumb.normal') ?></a>
          <div>
            <div class="feed_item_link_title">
              <a href="<?php echo $profile_url ?>" target="_blank"><?php echo $this->user($user_subject)->getTitle() ?></a>
            </div>
            <div class="feed_item_link_desc">
              <?php echo $this->translate("Birthday: " . $this->timestamp(strtotime($item->date))); ?>
            </div>
            <div class="feed_item_link_desc">
              <a href="javascript:void(0);" onclick="content_show('<?php echo $this->user($user_subject)->getTitle() ?>', '<?php echo $item->object_id ?>');return false;"><?php echo
              $this->translate("Write on " . $this->user($user_subject)->getTitle() . "s Wall"); ?></a>
            </div>
          </div>
        </div>
      </span>
    </div>
    <ul class="feed" id="birthday_status_show_<?php echo $item->object_id ?>">
    <li id="activity_textarea-<?php echo $item->object_id ?>" style='display:none;'>
      <?php $viewer = Engine_Api::_()->user()->getViewer();?>
      <div class="feed_item_photo">
        <?php echo $this->htmlLink($viewer->getHref(), $this->itemPhoto($viewer, 'thumb.icon'));?>
      </div>
      <textarea rows="2" cols="10" id="activity-write-body-<?php echo $item->object_id ?>" onfocus="activitywriteonclick($(this), '<?php echo $this->translate("Write on " .
      $this->user($user_subject)->getTitle() . "s wall..."); ?>', 1, '<?php echo $item->object_id ?>')" onblur="activitywriteonclick($(this),'<?php echo $this->translate("Write on " .
      $this->user($user_subject)->getTitle() . "s wall..."); ?>', 2, '<?php echo $item->object_id ?>')"
      onkeyup="statusubmit(event, '<?php echo $item->object_id ?>', '<?php echo $this->translate($this->user($user_subject)->getTitle()); ?>')"><?php echo $this->translate("Write on " .
      $this->user($user_subject)->getTitle() . "s wall..."); ?></textarea>
    </li>
    <?php } ?>
  <?php
  $counter = 1;
  $countflag = 0;
  foreach ($this->result as $itemResult) {

if ($counter == 2) { $actionId = $itemResult->action_id;  }
  //for display post user ?>
  <?php if ($counter  > 2) :

    //$profileUrl = $this->url(array('id' => $itemResult->object_id), 'user_profile');
  ?>
<div id= "view_more_<?php echo $item->object_id?>">
 <a href="javascript:void(0);" onclick="viewMoreActivityResults('<?php echo $item->object_id ?>', '<?php echo $actionId ?>')"><?php echo $this->translate("See more wish post"); ?></a>
</div>
  <div id="loding_image_<?php echo $item->object_id?>" style="display: none;">
    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
    <?php echo $this->translate("Loading ...") ?>
  </div>
<?php break; ?>
  <?php endif;?>
  <?php if ($itemResult->object_id == $item->object_id) {  ?>
  <li id="birthday_show_<?php echo $countflag; ?>_<?php echo $itemResult->object_id ?>">
<?php $countflag++; ?>


    <div class='feed_item_photo'>
      <?php
        $action = Engine_Api::_()->getItem('activity_action', $itemResult->action_id);
        $user_subject = Engine_Api::_()->user()->getUser($itemResult->subject_id);
        $profile_url = $this->url(array('id' => $itemResult->subject_id), 'user_profile'); ?>
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

  <?php $counter++ ; } ?>
  <?php } ?>
 </ul>
 </div>
  
</li>

<?php endforeach; ?>



</ul>



<script type="text/javascript">
//for view more result for birthday post.
function viewMoreActivityResults(object_id,action_id) {
  $('view_more_' +object_id).style.display ='none';
  $('loding_image_' +object_id).style.display ='';
  en4.core.request.send(new Request.HTML({
    method : 'post',
    'url' : en4.core.baseUrl + 'birthday/index/viewmore',
    'data' : {
      format : 'html',
      'object_id' : object_id,
      'action_id' : action_id,
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
      Elements.from(responseHTML).inject($('birthday_status_show_' + object_id));
      $('loding_image_' +object_id).style.display ='none';
   
    }
  }));
}
</script>