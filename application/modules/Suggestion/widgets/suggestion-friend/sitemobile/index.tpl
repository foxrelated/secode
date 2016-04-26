<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<!--Browse member code-->
<?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemobile/modules/User/View/Helper', 'User_View_Helper'); ?>
<?php $viewer = Engine_Api::_()->user()->getViewer(); ?>
<div class="sm-content-list">
    <?php if($this->suggestionView == 'list'):?>
        <ul id="browsemembers_ul" class="ui-member-list" data-role="listview" data-icon="none">
            <?php ?>
            <?php
            foreach ($this->modArray['mod_array'] as $modInfos):
                foreach ($modInfos as $modType => $modObjects):
                    foreach ($modObjects as $user):
                        ?>
                        <?php
                        $table = Engine_Api::_()->getDbtable('block', 'user');
                        $select = $table->select()
                                ->where('user_id = ?', $user->getIdentity())
                                ->where('blocked_user_id = ?', $viewer->getIdentity())
                                ->limit(1);
                        $row = $table->fetchRow($select);
                        ?>
                        <?php if ($row == NULL && $this->viewer()->getIdentity() && $this->userFriendshipSM($user)): ?>
                            <li>       
                                <div class="ui-item-member-action">
                                    <?php echo $this->userFriendshipSM($user) ?>
                                </div>

                                <a href="<?php echo $user->getHref() ?>">
                                    <?php echo $this->itemPhoto($user, 'thumb.icon') ?>
                                    <div class="ui-list-content">
                                        <h3><?php echo $user->getTitle() ?></h3>
                                        <p><?php echo $this->userMutualFriendship($user) ?></p>
                                    </div>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
<?php elseif($this->suggestionView == 'grid'):?>
        <div  class="iscroll_wapper">
        <ul class="sugg_list_grid <?php if($this->carouselView): ?> iscroll_container <?php endif;?>">
            <?php ?>
            <?php
            foreach ($this->modArray['mod_array'] as $modInfos):
                foreach ($modInfos as $modType => $modObjects):
                    foreach ($modObjects as $user):
                        ?>
                        <?php
                        $table = Engine_Api::_()->getDbtable('block', 'user');
                        $select = $table->select()
                                ->where('user_id = ?', $user->getIdentity())
                                ->where('blocked_user_id = ?', $viewer->getIdentity())
                                ->limit(1);
                        $row = $table->fetchRow($select);
                        ?>
                        <?php if ($row == NULL && $this->viewer()->getIdentity() && $this->userFriendshipSM($user)): ?>
                            <li style="height:140px;" class="iscroll_item">                                    
                              <a href="<?php echo $user->getHref() ?>" class="ui-link-inherit">
                                <div class="sugg_list_grid_img">
                                    <?php echo $this->itemPhoto($user, 'thumb.profile') ?>
                                </div>
                              </a>
                              <div class="sugg_list_grid_cnt">
                                <div class="sugg_list_grid_cnt_rel ui-item-member-action">
                                  <?php echo $this->userFriendshipSM($user) ?>
                                </div>
                                <div class="sugg_list_grid_title">
                                  <a href="<?php echo $user->getHref() ?>" class="ui-link-inherit">
                                    <?php echo $this->string()->chunk($this->string()->truncate($user->getTitle(), 45), 10); ?>
                                  </a>  
                                </div>
                                <div class="sugg_list_grid_stats">
                                  <?php echo $this->userMutualFriendship($user) ?>
                                </div>
                              </div> 
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
        </div>   
    <?php endif; ?>
    <div class="t_l sugg_seemore">
        <?php
        $is_activityPlugin = !empty($this->is_activityPlugin) ? true : false;

        // This is the "See All" link which will be display only in "PeopleYouMayKnow widgets". 
        if (empty($is_activityPlugin)) {
            echo '<div class="suggestion_view_all"><a id="pymk_see_al" href="' . $this->url(array(), 'friends_suggestions_viewall') . '" title="' . $this->translate("Find your Friends") . '">' . $this->translate("See All") . '</a></div>';
        }
        ?>
    </div>
</div>
