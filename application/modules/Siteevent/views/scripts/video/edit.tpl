<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
?>
<?php
include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/Adintegration.tpl';
?>

<div class="siteevent_view_top">
    <?php echo $this->htmlLink($this->siteevent->getHref(), $this->itemPhoto($this->siteevent, 'thumb.icon', '', array('align' => 'left'))) ?>
    <h2>	
        <?php echo $this->siteevent->__toString() ?>	
        <?php echo $this->translate('&raquo; '); ?>
        <?php echo $this->htmlLink($this->siteevent->getHref(array('tab' => $this->tab_selected_id)), $this->translate('Videos')) ?>
    </h2>
</div>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
    en4.core.runonce.add(function()
    {
        new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest',
            'customChoices': true,
            'filterSubset': true,
            'multiple': true,
            'injectChoice': function(token) {
                var choice = new Element('li', {'class': 'autocompleter-choices', 'value': token.label, 'id': token.id});
                new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
                choice.inputValue = token;
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });
    });
</script>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.advideoedit', 3) && $event_communityad_integration): ?>
    <div class="layout_right" id="communityad_videoedit">
			<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.advideoedit', 3),"loaded_by_ajax" => 0,'widgetId'=>'event_advideoedit'));?>
    </div>
<?php endif; ?>

<div class="layout_middle">
    <?php
    echo $this->form->render();
    ?>
</div>