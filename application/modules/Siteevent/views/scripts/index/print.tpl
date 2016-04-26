<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_print.css');
?>
<link href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_print.css' ?>" type="text/css" rel="stylesheet" media="print">

<div class="seaocore_print_page">
    <div class="seaocore_print_title">
        <span class="right">
            <?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title')); ?>
        </span>
        <?php if ($this->siteevent->closed != 1): ?>
            <span class="left">
                <?php echo $this->translate($this->siteevent->getTitle()) ?>
            </span>
        <?php endif; ?>	
    </div>
    <div class='seaocore_print_profile_fields'>
        <?php if ($this->siteevent->closed == 1): ?>
            <div class="tip"> 
                <span> <?php echo $this->translate('This event has been cancelled by the owner.'); ?> </span>
            </div>
            <br/>
        <?php else: ?>
            <div class="seaocore_print_photo">
                <?php echo $this->itemPhoto($this->siteevent, 'thumb.profile', '', array('align' => 'left')); ?>
                <div id="printdiv" class="seaocore_print_button">
                    <a href="javascript:void(0);" style="background-image: url('<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/printer.png');" class="buttonlink" onclick="printData()" align="right"><?php echo $this->translate('Take Print') ?></a>
                </div>
            </div>
            <div class="seaocore_print_details">	      
                <h4>
                    <?php echo $this->translate("Event Information") ?>
                </h4>

                <ul>
                    <li>
                        <span><?php echo $this->translate('Led By :'); ?></span>
                        <span><?php echo $this->siteevent->getLedBys(); ?></span>
                    </li>
                    <li>
                        <span><?php echo $this->translate('Start Date :'); ?></span>
                        <span><?php echo $this->startEndDates['starttime']; ?></span>
                    </li>

                    <li>
                        <span><?php echo $this->translate('End Date :'); ?></span>
                        <span><?php echo $this->startEndDates['endtime']; ?></span>
                    </li>

                    <?php if (!Engine_Api::_()->siteevent()->isTicketBasedEvent() && !empty($this->siteevent->member_count)): ?>
                        <li>
                            <span><?php echo $this->translate('Guests :'); ?></span>
                            <span><?php echo $this->translate($this->siteevent->member_count) ?></span>
                        </li>
                    <?php endif; ?>                    

                    <?php if (!empty($this->siteevent->comment_count)): ?>
                        <li>
                            <span><?php echo $this->translate('Comments :'); ?></span>
                            <span><?php echo $this->translate($this->siteevent->comment_count) ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($this->siteevent->view_count)): ?>
                        <li>
                            <span><?php echo $this->translate('Views :'); ?></span>
                            <span><?php echo $this->translate($this->siteevent->view_count) ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($this->siteevent->like_count)): ?>
                        <li>
                            <span><?php echo $this->translate('Likes :'); ?></span>
                            <span><?php echo $this->translate($this->siteevent->like_count) ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($this->siteevent->review_count) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 2)): ?>
                        <li>
                            <span><?php echo $this->translate('Reviews :'); ?></span>
                            <span><?php echo $this->translate($this->siteevent->review_count) ?></span>
                        </li>
                    <?php endif; ?>            
                    <?php if ($this->category_name): ?>
                        <li>
                            <span><?php echo $this->translate('Category :'); ?></span> 
                            <span>
                                <?php echo $this->translate($this->category_name) ?>
                                <?php if ($this->subcategory_name): ?> &raquo;
                                    <?php echo $this->translate($this->subcategory_name) ?>

                                    <?php if ($this->subsubcategory_name): ?> &raquo;
                                        <?php echo $this->translate($this->subsubcategory_name) ?>
                                    <?php endif; ?>

                                <?php endif; ?>
                            </span>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->siteeventTags): $tagCount = 0; ?>
                        <li>
                            <span><?php echo $this->translate('Tags :'); ?></span>
                            <span>
                                <?php foreach ($this->siteeventTags as $tag): ?>
                                    <?php if (!empty($tag->getTag()->text)): ?>
                                        <?php if (empty($tagCount)): ?>
                                            <?php echo "#" . $tag->getTag()->text ?>
                                            <?php "#" . $tagCount++; ?>
                                        <?php else: ?>
                                            <?php echo $tag->getTag()->text ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </span>
                        </li>
                    <?php endif; ?>
                    <li>
                        <span><?php echo $this->translate('Description :'); ?></span>
                        <span><?php echo $this->translate(''); ?> <?php echo $this->siteevent->body ?></span>
                    </li>

                    <?php if ($this->siteevent->location && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)): ?>
                        <li>
                            <span><?php echo $this->translate('Location :'); ?></span>
                            <span><?php echo $this->siteevent->location ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
                <?php if ($this->siteevent->profile_type): ?>
                    <?php $str = $this->fieldValueLoop($this->siteevent, $this->fieldStructure); ?>
                    <?php if (!empty($str)): ?>
                        <h4>
                            <?php echo $this->translate('Profile Information') ?>
                        </h4>
                        <?php echo Engine_Api::_()->siteevent()->removeMapLink($this->fieldValueLoop($this->siteevent, $this->fieldStructure)) ?>					
                    <?php endif; ?>
                <?php endif; ?>
            </div>	
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    function printData() {
        document.getElementById('printdiv').style.display = "none";
        window.print();
        setTimeout(function() {
            document.getElementById('printdiv').style.display = "block";
        }, 500);
    }
</script>