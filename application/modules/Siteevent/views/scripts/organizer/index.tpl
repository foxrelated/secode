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

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css'); ?>
<?php
include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/Adintegration.tpl';
?>

<div class="siteevent_view_top">
    <?php echo $this->htmlLink($this->siteevent->getHref(), $this->itemPhoto($this->siteevent, 'thumb.icon', '', array('align' => 'left'))) ?>
    <h2>	
        <?php echo $this->siteevent->__toString() ?>	
        <?php echo $this->translate('&raquo; '); ?>
        <?php echo $this->htmlLink($this->siteevent->getHref(array('tab' => $this->tab_selected_id)), $this->translate('Discussions')) ?>
    </h2>
</div>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.adtopicview', 3) && $event_communityad_integration): ?>
    <div class="layout_right" id="communityad_adtopicview">
			<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.adtopicview', 3),"loaded_by_ajax"=>0,'widgetId'=>'event_adtopicview'))?>
    </div>
<?php endif; ?>

<div class="layout_middle">
    <div class="siteevent_siteevents_options">

        <?php echo $this->htmlLink($this->siteevent->getHref(), $this->translate("Back to Event"), array('class' => 'buttonlink icon_back')) ?>
        <?php
        if ($this->can_post) {
            echo $this->htmlLink(array('route' => "siteevent_extended", 'controller' => 'topic', 'action' => 'create', 'subject' => $this->siteevent->getGuid(), 'content_id' => $this->tab_selected_id), $this->translate('Post New Topic'), array('class' => 'buttonlink icon_siteevent_post_new'));
        }
        ?>
    </div>

    <?php if ($this->paginator->count() > 1): ?>
        <div>
            <br />
            <?php echo $this->paginationControl($this->paginator) ?>
            <br />
        </div>
    <?php endif; ?>

    <ul class="siteevent_siteevents">
        <?php foreach ($this->paginator as $topic): ?>
            <?php
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
                    <?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
                    <div class="siteevent_siteevents_lastreply_info">
                        <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Post')) ?> <?php echo $this->translate('by'); ?> <?php echo $lastposter->__toString() ?>
                        <br />
                        <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'siteevent_siteevents_lastreply_info_date seaocore_txt_light')) ?>
                    </div>
                </div>

                <div class="siteevent_siteevents_info">
                    <h3<?php if ($topic->sticky): ?> class='siteevent_siteevents_sticky'<?php endif; ?>>
                        <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
                    </h3>
                    <div class="siteevent_siteevents_blurb">
                        <?php echo $this->viewMore(strip_tags($topic->getDescription())) ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($this->paginator->count() > 1): ?>
        <div>
            <?php echo $this->paginationControl($this->paginator) ?>
        </div>
    <?php endif; ?>
</div>