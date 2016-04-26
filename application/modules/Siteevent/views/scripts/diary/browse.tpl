<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: browse.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->activeClass = 'siteevent_main_diary_browse';
if(!Engine_Api::_()->seaocore()->checkModuleNameAndNavigation()):
include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/navigation_views.tpl';
endif;
?>

<div class='layout_right'>
    <?php echo $this->form->render($this) ?>
</div>

<div class='layout_middle'>
    <?php if (count($this->paginator) > 0): ?>
        <ul class='seaocore_browse_list'>
            <?php foreach ($this->paginator as $diary): ?>
                <li>
                    <div class='seaocore_browse_list_photo'>
                        <?php echo $this->htmlLink($diary->getOwner()->getHref(), $this->itemPhoto($diary->getOwner(), 'thumb.normal')) ?>
                    </div>

                    <div class="seaocore_browse_list_info">
                        <div class="seaocore_browse_list_info_title">
                            <h3><?php echo $this->htmlLink($diary->getHref(), $diary->title) ?></h3>
                        </div>
                        <div class='seaocore_browse_list_info_blurb'>
                            <?php echo $diary->body; ?>
                        </div>
                        <div class="seaocore_browse_list_info_date">
                            <?php echo $this->translate('Created %s by %s', $this->timestamp($diary->creation_date), $diary->getOwner()->toString()) ?>
                            <br />
                            <?php echo $this->translate('Total Events: %s', $diary->total_item) ?>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <div>
        <?php echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues, 'pageAsQuery' => true)); ?>
        </div>
    <?php else: ?>
        <div class="tip">
            <span>
                <?php echo $this->translate('Nobody has created a diary yet. Be the first to %1$screate%2$s one!', '<a class="smoothbox" href="' . $this->url(array('action' => 'create'), "siteevent_diary_general") . '">', '</a>'); ?>
            </span>
        </div>
    <?php endif; ?>
</div>