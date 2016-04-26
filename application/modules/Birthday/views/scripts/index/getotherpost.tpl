<?php ?>

<div class="seaocore_members_popup seaocore_members_popup_notbs">
    <div class="top">
      <div class="heading"><?php echo $this->translate('Friends')?></div>
    </div>
    <div class="seaocore_members_popup_content">
      <?php foreach( $this->activityPostResult as $value ):
        $user_subject = Engine_Api::_()->user()->getUser($value->subject_id);
        $profile_url = $this->url(array('id' => $value->subject_id), 'user_profile');
      ?>
      <div class="item_member_list">
        <div class="item_member_thumb">
          <a href="<?php echo $profile_url ?>"  target="_blank"> <?php echo $this->itemPhoto($this->user($user_subject), 'thumb.icon') ?></a>
        </div>
        <div class="item_member_details">
          <div class="item_member_name">
            <a href="<?php echo $profile_url ?>" target="_blank"><?php echo $this->user($user_subject)->getTitle() ?></a>
          </div>
        </div>
      </div>
      <?php endforeach;?>
    </div>
  </div>
  <div class="seaocore_members_popup_bottom">
      <button  onclick='javascript:parent.Smoothbox.close()' ><?php echo $this->translate('Close') ?></button>
  </div>
