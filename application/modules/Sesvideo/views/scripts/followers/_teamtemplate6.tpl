<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _teamtemplate6.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesteam/externals/styles/styles.css'); ?>
<?php $viewer = Engine_Api::_()->user()->getViewer();?>

<div class="sesteam_temp6_wrap">
  <div class="sesteam_temp6_list">
    <table>
        <?php foreach( $this->users as $user ): ?>
         <?php $user = Engine_Api::_()->getItem('user', $user->owner_id); ?>
        <tr>
          <td class="team_member_thumbnail">
              <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('title' => $user->getTitle())); ?>
          </td>
          <?php if(!empty($this->content_show) && in_array('displayname', $this->content_show)): ?>
            <td class='team_member_name'>
              <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title' => $user->getTitle())); ?>
            </td>
          <?php endif; ?>
          <?php $memberType = Engine_Api::_()->sesteam()->getProfileType($user); ?>
          <?php if($memberType && !empty($this->content_show) && in_array('profileType', $this->content_show)): ?>
            <td class='team_member_contact_info team_member_role sesbasic_text_light'>
                <?php echo $memberType; ?>
            </td>
          <?php endif; ?>
          <?php if($this->age): $age = 0; ?>  
            <?php $getFieldsObjectsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user); 
            if (!empty($getFieldsObjectsByAlias['birthdate'])) {
              $optionId = $getFieldsObjectsByAlias['birthdate']->getValue($user); 
              if ($optionId->value) {
                $age = floor((time() - strtotime($optionId->value)) / 31556926);
              }
            }
            ?>
            <td class="team_member_contact_info">
            <?php if($age && $optionId->value): ?>
              
                <i class="sesbasic_text_light"></i>
                <?php echo $this->translate(array('%s year old', '%s years old', $age), $this->locale()->toNumber($age)); ?>
              
            <?php endif; ?>
            </td>
            <?php endif; ?>
          <?php if(!empty($this->content_show)): ?>
            <td class="sesteam-social-icon <?php if(empty($this->sesteam_social_border)): ?>bordernone<?php endif; ?>">
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
            </td>
          <?php endif; ?>
          <?php if($user->status && !empty($this->content_show) && in_array('status', $this->content_show)): ?>
            <td class="team_member_more_link">
              <?php if(!empty($this->viewMoreText)): ?>
                <?php $viewMoreText = $this->translate($this->viewMoreText) . ' &raquo;'; ?>
              <?php else: ?>
                <?php $viewMoreText = $this->translate("View Details") . '&raquo;'; ?>
              <?php endif; ?>
              <?php if($user->status): ?>
                <?php echo $this->htmlLink($user->getHref(), $viewMoreText, array()) ?>
              <?php endif; ?>
            </td>
          <?php endif; ?>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>