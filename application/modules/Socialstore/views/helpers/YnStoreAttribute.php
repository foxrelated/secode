<?php
class Socialstore_View_Helper_YnStoreAttribute extends Zend_View_Helper_Abstract
{
  public function ynStoreAttribute($meta)
  {

    // Prepare translations
    $translate = Zend_Registry::get('Zend_Translate');

    $containerClass = 'field';
	$key = '0_0_'.$meta->type_id;
    //$key = $map->getKey();
    $label = $this->view->translate($meta->label);
    $type = $meta->type;
    
    $typeLabel = Engine_Api::_()->fields()->getFieldInfo($type, 'label');
    $typeLabel = $this->view->translate($typeLabel);

    // Options data
    $optionContent = '';
    $dependentFieldContent = '';
    if (in_array($type, Engine_Api::_()->fields()->getFieldInfo('dependents'))&& ($this->view->productAttr == 1)) {
      $extraOptionsClass = 'field_extraoptions ' . $this->_generateClassNames($key, 'field_extraoptions_');
      $optionContent .= <<<EOF
<div class="{$extraOptionsClass}" id="field_extraoptions_{$key}">
  <div class="field_extraoptions_contents_wrapper">
    <div class="field_extraoptions_contents">
      <div class="field_extraoptions_add">
        {$this->view->formText('text', '', array('title' => '', 'onkeypress' => 'void(0);',  'onmousedown' => "void(0);"))}
      </div>
EOF;
      $options = $meta->getOptions($this->view->pro_id);
      
      if( !empty($options) ) {
        $extraOptionsChoicesClass = 'field_extraoptions_choices ' . $this->_generateClassNames($key, 'field_extraoptions_choices_');
        $optionContent .= <<<EOF
      <ul class="{$extraOptionsChoicesClass}" id="admin_field_extraoptions_choices_{$key}">
EOF;
        foreach( $options as $option ) {
          $optionId = $option->option_id;
          $optionLabel = $this->view->translate($option->label);

          $optionClass = 'field_option_select field_option_select_' . $optionId . ' ' . $this->_generateClassNames($key, 'field_option_select_');
          $optionContent .= <<<EOF
        <li id="field_option_select_{$key}_{$optionId}" class="{$optionClass}">
          <span class="field_extraoptions_choices_options">
            <a href="javascript:void(0);" onclick="void(0);">{$translate->_('edit - adjust price')}</a>
            | <a href="javascript:void(0);" onclick="void(0);">X</a>
          </span>
          <span class="field_extraoptions_choices_label">
            {$optionLabel} 
          </span>
        </li>
EOF;
        }
        
        $optionContent .= <<<EOF
      </ul>
EOF;
      }

      $optionContent .= <<<EOF
    </div>
  </div>
  <a href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">
    {$translate->_('add choices')}
  </a>
</div>
EOF;
    }

    // Generate
    $contentClass = 'admin_field ' . $this->_generateClassNames($key, 'admin_field_');
    $content = <<<EOF
  <li id="admin_field_{$key}" class="{$contentClass}">
    <span class='{$containerClass}'>
      <div class='item_handle'>
        &nbsp;
      </div>
      <div class='item_options'>
EOF;
	if ($this->view->productAttr == 0) {
$content .= <<<EOF
	        <a href='javascript:void(0);' onclick='void(0);' onmousedown="void(0);">{$translate->_('edit')}</a>
	        | <a href='javascript:void(0);' onclick='void(0);' onmousedown="void(0);">{$translate->_('delete')}</a>
	      </div>
	      <div class='item_title'>
	        {$label}
	        <span>({$typeLabel})</span>
	      </div>
	      {$optionContent}
	    </span>
	    {$dependentFieldContent}
	  </li>
EOF;
	}
  else if ($this->view->productAttr == 1 && $type == 'text') {
		$content .= <<<EOF
	        <a href='javascript:void(0);' onclick='void(0);' onmousedown="void(0);">{$translate->_('add content')}</a>
	        | <a href='javascript:void(0);' onclick='void(0);' onmousedown="void(0);">{$translate->_('remove content')}</a>
	      </div>
	      <div class='item_title'>
	        {$label}
	        <span>({$typeLabel})</span>
	      </div>
	      {$optionContent}
	    </span>
	    {$dependentFieldContent}
	  </li>
EOF;
	}
	else {
		$content .= <<<EOF
	        
	      </div>
	      <div class='item_title'>
	        {$label}
	        <span>({$typeLabel})</span>
	      </div>
	      {$optionContent}
	    </span>
	    {$dependentFieldContent}
	  </li>
EOF;
	}
    
    return $content;
  }

  protected function _generateClassNames($key, $prefix = '')
  {
    list($parent_id, $option_id, $child_id) = explode('_', $key);
    return
      $prefix . 'parent_' . $parent_id . ' ' .
      $prefix . 'option_' . $option_id . ' ' .
      $prefix . 'child_' . $child_id
      ;
  }
}