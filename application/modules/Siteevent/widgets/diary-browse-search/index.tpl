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
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<?php if ($this->viewType == 'horizontal'): ?>
    <div class="seaocore_searchform_criteria  siteevent_diary_browse_search">
        <?php echo $this->form->setAttrib('class', 'siteevent_item_filters')->render($this) ?>
    </div>
<?php else: ?>
    <div class="seaocore_searchform_criteria">
        <?php echo $this->form->render($this) ?>
    </div>
<?php endif; ?>

<script type="text/javascript">
    showMemberNameSearch();
    function showMemberNameSearch() {
        if ($('search_diary') && $('member')) {
            $('member-wrapper').setStyle('display', ($('search_diary').get('value') == '' ? 'block' : 'none'));
            $('member').value = $('search_diary').get('value') == '' ? $('member').value : '';
        }
    }
</script>  
