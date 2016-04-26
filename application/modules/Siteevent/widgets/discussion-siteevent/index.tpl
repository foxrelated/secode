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

<?php if ($this->loaded_by_ajax): ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_siteevent_discussion_siteevent')
        }
        en4.siteevent.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>

<?php if ($this->showContent): ?>
    <?php if ($this->canPost || $this->paginator->count() > 1): ?>
        <?php $count = $this->paginator->getTotalItemCount(); ?>
        <div class="seaocore_add">
            <?php if ($this->canPost): ?>
                <?php
                echo $this->htmlLink(array(
                    'route' => "siteevent_extended",
                    'controller' => 'topic',
                    'action' => 'create',
                   // 'subject' => $this->subject()->getGuid(),
										'event_id' => $this->siteevent->event_id,
                    'content_id' => $this->identity
                        ), $this->translate('Post New Topic'), array(
                    'class' => 'buttonlink icon_siteevent_post_new'
                ))
                ?>
            <?php endif; ?>
            <?php if ($this->paginator->count() > 1): ?>
                <?php
                echo $this->htmlLink(array(
                    'route' => "siteevent_extended",
                    'controller' => 'topic',
                    'action' => 'index',
                    'content_id' => $this->identity,
                    'subject' => $this->subject()->getGuid(),
                        ), $this->translate("View all %s Topics", $count), array(
                    'class' => 'buttonlink icon_viewmore'
                ))
                ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($this->paginator->getTotalItemCount() > 0): ?>
        <div class="siteevent_siteevents_siteevent">
            <ul class="siteevent_siteevents">
                <?php
                foreach ($this->paginator as $topic):
                    $lastpost = $topic->getLastPost();
                    $lastposter = Engine_Api::_()->getItem('user', $topic->lastposter_id);
                    ?>
                    <li>
                        <div class="siteevent_siteevents_replies seaocore_txt_light">
                            <span>
                                <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
                            </span>
                            <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
                        </div>
                        <div class="siteevent_siteevents_lastreply">
                            <?php echo $this->htmlLink($lastposter->getHref(array('content_id' => $this->identity)), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
                            <div class="siteevent_siteevents_lastreply_info">
                                <?php echo $this->htmlLink($lastpost->getHref(array('content_id' => $this->identity)), $this->translate('Last Post')) ?> <?php echo $this->translate('by'); ?> <?php echo $lastposter->__toString() ?>
                                <br />
                                <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'siteevent_siteevents_lastreply_info_date seaocore_txt_light')) ?>
                            </div>
                        </div>
                        <div class="siteevent_siteevents_info">
                            <h3<?php if ($topic->sticky): ?> class='siteevent_siteevents_sticky'<?php endif; ?>>
                                <?php echo $this->htmlLink($topic->getHref(array('content_id' => $this->identity)), $topic->getTitle()) ?>
                            </h3>
                            <div class="siteevent_siteevents_blurb">
                                <?php echo $this->viewMore(strip_tags($topic->getDescription())) ?>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <div class="tip">
            <span>
                <?php
                if ($this->canPost):
                    $show_link = $this->htmlLink(array('route' => "siteevent_extended", 'controller' => 'topic', 'action' => 'create', 'event_id' => $this->siteevent->event_id, 'content_id' => $this->identity), $this->translate('here'));
                    $show_label = Zend_Registry::get('Zend_Translate')->_('No discussion topics have been posted in this event yet. Click %1$s to start a discussion.');
                    $show_label = sprintf($show_label, $show_link);
                    echo $show_label;
                endif;
                ?>
            </span>
        </div>
    <?php endif; ?>
<?php endif; ?>