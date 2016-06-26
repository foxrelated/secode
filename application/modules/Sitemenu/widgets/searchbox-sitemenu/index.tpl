<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<?php if($this->productSearch == 1 ) : ?>
  <div id="global_search_form_container" class="fright">
  	<?php if( !empty($this->isMainMenu) && $this->isMainMenu == 1 ) :  ?>
    	<div id="sitemenu_search_toggle" onmouseover="sitemenuSearchToggle(1)" onmouseout="sitemenuSearchToggle(2)">
        <a class="sitemenu_search-toggle" href="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>"><i></i></a>
        <div id="sitemenu_search_toggle_content" class="sitestore_quick_search">
            <form id="global_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
              <input type='text' class='text <?php if( !empty($this->isMainMenu) && $this->isMainMenu == 1  ) : echo 'main_menu_suggested'; else: echo 'suggested'; endif; ?>' name='query' id='global_search_field' size='20' maxlength='100' placeholder="<?php echo $this->translate('Search...'); ?>" />
            </form>
        </div>
      </div>
  	<?php else: ?>
      <form id="global_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
      <input type='text' class='text <?php if( !empty($this->isMainMenu) && $this->isMainMenu == 1  ) : echo 'main_menu_suggested'; else: echo 'suggested'; endif; ?>' name='query' id='global_search_field' size='20' maxlength='100' placeholder="<?php echo $this->translate('Search...'); ?>" />
      </form>
    <?php endif; ?>
  </div>
<?php elseif($this->productSearch == 2): ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css'); ?>
<?php if( !empty($this->isMainMenu) && $this->isMainMenu == 1 ) :  ?>
  <div id="sitemenu_search_toggle" onmouseover="sitemenuSearchToggle(1)" onmouseout="sitemenuSearchToggle(2)">
    <a class="sitemenu_search-toggle" href="<?php echo $this->url(array('action' => 'index'), "sitestoreproduct_general", true) ?>"><i></i></a>
    <div id="sitemenu_search_toggle_content" class="sitestore_quick_search">
      <?php echo $this->form->setAttrib('class', 'sitestoreproduct-search-box')->render($this) ?>
    </div>	
  </div>
<?php else: ?>
  <div class="sitestore_quick_search">
    <?php echo $this->form->setAttrib('class', 'sitestoreproduct-search-box')->render($this) ?>
  </div>	
<?php endif; ?>

<?php
  $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl .  'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  var advancedMenuContainerId;
  var advancedMenuContentAutocomplete;
  
  en4.core.runonce.add(function()
  {
    <?php if( empty($this->isMainMenu) ) : ?>
      advancedMenuContainerId = '<?php echo 'miniMenuProductSearch' ?>';
      getStoreProductSuggest(advancedMenuContainerId, '_0', '<?php echo $this->url(array('action' => 'get-search-products'), "sitestoreproduct_general", true) ?>');
    <?php elseif(!empty($this->isMainMenu) && $this->isMainMenu == 1): ?>
      advancedMenuContainerId = '<?php echo 'mainMenuProductSearch' ?>';
      getStoreProductSuggest(advancedMenuContainerId, '_1', '<?php echo $this->url(array('action' => 'get-search-products'), "sitestoreproduct_general", true) ?>');
    <?php else: ?>
      advancedMenuContainerId = '<?php echo 'footerMenuProductSearch' ?>';
      getStoreProductSuggest(advancedMenuContainerId, null);
    <?php endif; ?>
    
    advancedMenuContentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      window.addEvent('keyup', function(e) {
        storeProductSelect(e, selected);
      });      
    });
  });
  
  function advancedMenuSeemore() {
    $('stopevent').removeEvents('click');
    var url = '<?php echo $this->url(array('action' => 'index'), "sitestoreproduct_general", true); ?>';
  	window.location.href= url + "?"+advancedMenuContainerId+"=" + encodeURIComponent($(advancedMenuContainerId).value);
  }

  
	
</script>
<?php elseif($this->productSearch == 3): ?>
 <?php if( empty($this->isMainMenu) ) : ?>
      <?php echo $this->content()->renderWidget("siteadvsearch.search-box", array("widgetName" => "advmenu_mini", "advsearch_search_box_width" => $this->searchbox_width, 'showLocationBasedContent' => $this->showLocationBasedContent))?>
    <?php elseif(!empty($this->isMainMenu) && $this->isMainMenu == 1): ?>
      <?php echo $this->content()->renderWidget("siteadvsearch.search-box", array("widgetName" => "advmenu_main","advsearch_search_box_width" => 0, 'showLocationBasedContent' => $this->showLocationBasedContent))?>
    <?php else: ?>
     <?php echo $this->content()->renderWidget("siteadvsearch.search-box", array("widgetName" => "advmenu_footer","advsearch_search_box_width" => 0, 'showLocationBasedContent' => $this->showLocationBasedContent))?>
    <?php endif; ?>
<?php elseif($this->productSearch == 5): ?>
    <div id="sitemenu_search_toggle" onmouseover="sitemenuSearchToggle(1)" onmouseout="sitemenuSearchToggle(2)">
    <a class="sitemenu_search-toggle" href="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>"><i></i></a>
    <div id="sitemenu_search_toggle_content" class="sitestore_quick_search" style="overflow:visible;">
      <?php if (empty($this->isMainMenu)) : ?>
        <?php echo $this->content()->renderWidget("siteadvsearch.search-box", array("widgetName" => "advmenu_mini", "advsearch_search_box_width" => 324, 'showLocationBasedContent' => $this->showLocationBasedContent)) ?>
      <?php elseif (!empty($this->isMainMenu) && $this->isMainMenu == 1): ?>
        <?php echo $this->content()->renderWidget("siteadvsearch.search-box", array("widgetName" => "advmenu_main", "advsearch_search_box_width" => 324, 'showLocationBasedContent' => $this->showLocationBasedContent)) ?>
      <?php else: ?>
        <?php echo $this->content()->renderWidget("siteadvsearch.search-box", array("widgetName" => "advmenu_footer", "advsearch_search_box_width" => 324, 'showLocationBasedContent' => $this->showLocationBasedContent)) ?>
      <?php endif; ?>
    </div>	
  </div>
<?php endif;