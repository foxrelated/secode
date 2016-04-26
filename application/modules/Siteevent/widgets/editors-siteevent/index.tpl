<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->viewType): ?>

    <ul class="siteevent_editor_event">
        <?php foreach ($this->editors as $user): ?>
            <li>
                <div class="siteevent_editor_event_photo">
                    <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile'), array('class' => '', 'title' => $user->displayname), array('title' => $user->getUserTitle($user->user_id))) ?>
                </div>
                <div class='siteevent_editor_event_info'>
                    <div class='siteevent_editor_event_name'>
                        <?php echo $this->htmlLink($user->getHref(), $user->getUserTitle($user->user_id), array('title' => $user->getUserTitle($user->user_id))) ?>
                    </div>

                    <?php if (!empty($user->designation)): ?>
                        <div class='siteevent_editor_event_stat'>
                            <?php echo $user->designation ?>
                        </div>
                    <?php endif; ?>          

                    <div class='siteevent_editor_event_stat seaocore_txt_light'>
                        <?php
                        $params = array();
                        $params['owner_id'] = $user->user_id;
                        $params['type'] = 'editor';
                        ?>  
                        <?php $totalReviews = Engine_Api::_()->getDbTable('reviews', 'siteevent')->totalReviews($params); ?>
                        <?php echo $this->translate(array('%s Review', '%s Reviews', $totalReviews), $this->locale()->toNumber($totalReviews)); ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="siteevent_editor_event_more">
        <?php echo $this->htmlLink(array('route' => "siteevent_review_editor", 'action' => 'home'), $this->translate('View all Editors &raquo;')) ?>
    </div>

<?php else: ?>

    <ul class="seaocore_sidebar_list o_hidden">
        <?php foreach ($this->editors as $user): ?>
            <li>
                <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'popularmembers_thumb', 'title' => $user->displayname), array('title' => $user->getUserTitle($user->user_id))) ?>
                <div class='seaocore_sidebar_list_info'>
                    <div class='seaocore_sidebar_list_title'>
                        <?php echo $this->htmlLink($user->getHref(), $user->getUserTitle($user->user_id), array('title' => $user->getUserTitle($user->user_id))) ?>
                    </div>

                    <?php if (!empty($user->designation)): ?>
                        <div class='seaocore_sidebar_list_details'>
                            <?php echo $user->designation ?>
                        </div>
                    <?php endif; ?>
                    <div class='seaocore_sidebar_list_details'>
                        <?php
                        $params = array();
                        $params['owner_id'] = $user->user_id;
                        $params['type'] = 'editor';
                        ?>
                        <?php $totalReviews = Engine_Api::_()->getDbTable('reviews', 'siteevent')->totalReviews($params); ?>
                        <?php echo $this->translate(array('%s Review', '%s Reviews', $totalReviews), $this->locale()->toNumber($totalReviews)); ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
        <li class="seaocore_sidebar_more_link bold"><?php echo $this->htmlLink(array('route' => "siteevent_review_editor", 'action' => 'home'), $this->translate('View all Editors &raquo;')) ?></li>
    </ul>
<?php endif; ?>