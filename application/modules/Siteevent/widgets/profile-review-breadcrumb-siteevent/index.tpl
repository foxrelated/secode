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

<?php $categoryRouteName = Engine_Api::_()->siteevent()->getCategoryHomeRoute(); ?>
<div class="siteevent_event_breadcrumb">
    <a href="<?php echo $this->url(array('action' => 'home'), "siteevent_general"); ?>">
        <?php echo $this->translate("Events"); ?></a>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <?php if ($this->category_name): ?>
        <a href="<?php echo $this->url(array('category_id' => $this->siteevent->category_id, 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->category_id)->getCategorySlug()), $categoryRouteName); ?>">
            <?php echo $this->translate($this->category_name); ?>
        </a>
        <?php if (!empty($this->subcategory_name)): echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
            <a href="<?php echo $this->url(array('category_id' => $this->siteevent->category_id, 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->category_id)->getCategorySlug(), 'subcategory_id' => $this->siteevent->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->subcategory_id)->getCategorySlug()), "siteevent_general_subcategory") ?>">
                <?php echo $this->translate($this->subcategory_name); ?>
            </a>
            <?php if (!empty($this->subsubcategory_name)): echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
                <a href="<?php echo $this->url(array('category_id' => $this->siteevent->category_id, 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->category_id)->getCategorySlug(), 'subcategory_id' => $this->siteevent->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->subcategory_id)->getCategorySlug(), 'subsubcategory_id' => $this->siteevent->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->subsubcategory_id)->getCategorySlug()), 'siteevent_general_subsubcategory') ?>">
                    <?php echo $this->translate($this->subsubcategory_name); ?></a>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <?php echo $this->htmlLink($this->siteevent->getHref(), $this->siteevent->getTitle()) ?>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <a href='<?php echo $this->url(array('event_id' => $this->siteevent->event_id, 'slug' => $this->siteevent->getSlug(), 'tab' => $this->tab_id), 'siteevent_entry_view', true) ?>'><?php echo $this->translate('Reviews'); ?></a>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <?php echo $this->reviews->getTitle(); ?>
</div>