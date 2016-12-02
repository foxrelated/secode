<?php if (empty($this->is_ajax)) :?>
<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
<?php  include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>
  <div class="layout_middle">
    <?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/edit_tabs.tpl'; ?>
    <?php
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
    ?>
    <?php
    /* Include the common user-end field switching javascript */
    echo $this->partial('_jsSwitch.tpl', 'fields', array(
            //'topLevelId' => (int) @$this->topLevelId,
            //'topLevelValue' => (int) @$this->topLevelValue
    ))
    ?>

    <div class="sitegroup_edit_content">
      <div class="sitegroup_edit_header">
        <a class="sitestore_buttonlink" href='<?php echo $this->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($this->group_id)), 'sitegroup_entry_view', true) ?>'><?php echo $this->translate('VIEW_GROUP'); ?></a>
        <h3><?php echo $this->translate('Dashboard: ') . $this->sitegroup->title; ?></h3>
      </div>
			
      <div id="show_tab_content">
      <?php endif; ?>

      <div class="seaocore_tbs_cont" id="dynamic_menus_content"></div>

      <?php if (empty($this->is_ajax)) : ?>	
      </div>
    </div>	
  </div>
    </div>	
  </div>
<?php endif; ?>