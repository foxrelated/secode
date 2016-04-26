<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _teamtemplate3.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesteam/externals/styles/styles.css'); ?>
<?php $viewer = Engine_Api::_()->user()->getViewer();?>

<div class="sesteam_temp3_wrap">
  <div class="sesteam_temp3_list sesbasic_clearfix<?php if ($this->center_block): ?> iscenter<?php endif; ?>">
    <?php foreach( $this->users as $user ): ?>
      <?php $user = Engine_Api::_()->getItem('user', $user->owner_id); ?>
      <div class="team_box">
        <div class="team_box_inner">
          <div class="team_member_thumbnail" style="width:<?php echo $this->width ?>px;">
            <div class="team_member_thumbnail_inner" style="height:<?php echo $this->height ?>px;"> 
              <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile', $user->getTitle()), array('title' => $user->getTitle())); ?>
            </div>
          </div>
          <?php if(!empty($this->content_show)): ?>
          <div class="team_member_details">
          <?php if(!empty($this->content_show) && in_array('displayname', $this->content_show)): ?>
            <p class='team_member_name'>
              <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title' => $user->getTitle())); ?>              
            </p>
          <?php endif; ?>
            <?php $memberType = Engine_Api::_()->sesteam()->getProfileType($user); ?>
          <?php if($memberType && !empty($this->content_show) && in_array('profileType', $this->content_show)): ?>
              <div class='team_member_stats team_member_role'>
                <?php echo $memberType; ?>
              </div>
            <?php endif; ?>
            <?php if($this->age): $age = 0; ?>  
            <?php $getFieldsObjectsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user); 
            if (!empty($getFieldsObjectsByAlias['birthdate'])) {
              $optionId = $getFieldsObjectsByAlias['birthdate']->getValue($user); 
              if (@$optionId->value) {
                $age = floor((time() - strtotime($optionId->value)) / 31556926);
              }
            }  
            ?>
            <?php if($age && $optionId->value): ?>
              <div class='team_member_stats team_member_role'><?php echo $this->translate(array('%s year old', '%s years old', $age), $this->locale()->toNumber($age)); ?>
              </div>
            <?php endif; ?>
          <?php endif; ?>
            <?php if(!empty($this->content_show) && ($user->email || $user->status || $user->message || $user->addFriend)): ?>
            <div class="team_member_stats team_member_contact_info sesbasic_text_light">
              <?php if($user->email && !empty($this->content_show) && in_array('email', $this->content_show)): ?>
                <span>
                  <i class="fa fa-envelope sesbasic_text_light"></i>
                  <a href="mailto:<?php echo $user->email ?>" title="<?php echo $user->email ?>">
                    <?php echo $user->email ?>
                  </a> 
                </span>
              <?php endif; ?>
              <?php //Show Profile Fields of members
              if(!empty($this->content_show) && in_array('profileField', $this->content_show)): ?>
              <div class='team_member_stats team_member_role'>
                <span>
                  <?php echo $this->membersFieldValueLoop($user, $this->content_show, $this->labelBold,$this->profileFieldCount); ?>
                </span>
                </div>
              <?php endif; ?>    
            </div>
            <?php endif; ?>

            <?php if($user->status && !empty($this->content_show) && in_array('status', $this->content_show)): ?>
              <div class='team_member_stats sesbasic_text_light team_member_des'>
                <?php echo $user->status; ?>                
                <?php if(!empty($this->content_show) && in_array('viewMore', $this->content_show)): ?>
                  <span class="team_member_more_link team_member_stats">
                    <?php if(!empty($this->viewMoreText)): ?>
                      <?php $viewMoreText = $this->translate($this->viewMoreText) . ' &raquo;'; ?>
                    <?php else: ?>
                      <?php $viewMoreText = $this->translate("more") . '&raquo;'; ?>
                    <?php endif; ?>
                    <?php if($user->status): ?>
                     <?php echo $this->htmlLink($user->getHref(), $viewMoreText, array()) ?>
                    <?php endif; ?>
                  </span>
                <?php endif; ?>
              </div>
            <?php endif; ?>
            <div class="sesteam-social-icon <?php if(empty($this->sesteam_social_border)): ?>bordernone<?php endif; ?>">
              <?php if($user->email && !empty($this->content_show) && in_array('email', $this->content_show)): ?>
                <a href="mailto:<?php echo $user->email ?>" title="<?php echo $user->email ?>">
                  <i class="fa fa-envelope sesbasic_text_light"></i>
                </a>
              <?php endif; ?>
              
              <?php if (Engine_Api::_()->sesteam()->hasCheckMessage($user) && !empty($this->content_show) && in_array('message', $this->content_show)): ?>
              <a href="<?php echo $this->baseUrl() ?>/messages/compose/to/<?php echo $user->user_id ?>" target="_parent" title="<?php echo $this->translate('Message'); ?>" class="smoothbox"><i class="fa fa-envelope sesbasic_text_light"></i></a>
              <?php endif; ?>              
              
              <?php $row = Engine_Api::_()->sesteam()->getBlock(array('user_id' => $user->getIdentity(), 'blocked_user_id' => $viewer->getIdentity())); ?>
              <?php if( $row == NULL && !empty($this->content_show) && in_array('addFriend', $this->content_show)): ?>
                <?php if( $this->viewer()->getIdentity()): ?>
                    <?php echo $this->userTeamFriendship($user); ?>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>