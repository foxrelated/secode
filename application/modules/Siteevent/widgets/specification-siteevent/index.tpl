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
            responseContainer: $$('.layout_siteevent_specification_siteevent')
        };
        en4.siteevent.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>

<?php if ($this->showContent): ?>
    <div class='siteevent_pro_specs'>
        <?php if (!empty($this->otherDetails)): ?>
            <?php echo Engine_Api::_()->siteevent()->removeMapLink($this->fieldValueLoop($this->siteevent, $this->fieldStructure)) ?>
        <?php else: ?>
            <div class="tip">
                <span ><?php echo$this->translate("There no any information."); ?></span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>