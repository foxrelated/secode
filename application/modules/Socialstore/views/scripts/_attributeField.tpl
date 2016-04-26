<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _jsAdmin.tpl 9659 2012-03-21 20:36:16Z john $
 * @author     John
 */
?>

<script type="text/javascript">

  var fieldType = '<?php echo $this->fieldType ?>';
  var topLevelFieldId = '<?php echo sprintf('%d', $this->topLevelFieldId) ?>';
  var topLevelOptionId = '<?php echo sprintf('%d', $this->topLevelOptionId) ?>';
  var logging = true;
  var sortablesInstance;
  var urls = {
    option : {
      create : '<?php echo $this->url(array('action' => 'option-create')) ?>',
      edit : '<?php echo $this->url(array('action' => 'option-edit')) ?>',
      remove : '<?php echo $this->url(array('action' => 'option-delete')) ?>'
    },
    attribute : {
      create : '<?php echo $this->url(array('action' => 'attribute-create','set_id'=>$this->set_id)) ?>',
      edit : '<?php echo $this->url(array('action' => 'attribute-edit','set_id'=>$this->set_id)) ?>',
      remove : '<?php echo $this->url(array('action' => 'attribute-delete','set_id'=>$this->set_id))?>'
    },
    order : '<?php echo $this->url(array('action' => 'order')) ?>',
    index : '<?php echo $this->url(array('action' => 'index')) ?>'
  };

  window.addEvent('domready', function() {
    registerEvents();
  });

  // Register all events
  var registerEvents = function() {

    // Attach change profile type
   

    // Attach create field (top level)
    $$('.socialstore_attributes_addattribute').removeEvents().addEvent('click', uiSmoothTopAttributeCreate);

    // Attach options activator
    $$('.field_extraoptions > a').removeEvents().addEvent('click', uiToggleOptions);

    // Attach create options input
    $$('.field_extraoptions_add > input').removeEvents().addEvent('keypress', uiTextOptionCreate);

    // Attach edit options activator
    $$('.field_extraoptions_choices_options > a:first-child').removeEvents().addEvent('click', uiSmoothOptionEdit);

    // Attach delete options activator
    $$('.field_extraoptions_choices_options > a + a').removeEvents().addEvent('click', uiConfirmOptionDelete);

    // Attach toggle dependent fields
    //$$('.field_option_select > span + span').removeEvents().addEvent('click', uiToggleOptionDepFields);
    $$('.dep_hide_field_link').removeEvents().addEvent('click', uiToggleOptionDepFields);

    // Attach create field in option
    $$('.dep_add_field_link').removeEvents().addEvent('click', uiSmoothCreateField);

    // Attach edit field
    $$('.field > .item_options > a:first-child').removeEvents().addEvent('click', uiSmoothEditField);

    // Attach delete field
    $$('.field > .item_options > a + a').removeEvents().addEvent('click', uiConfirmDeleteField);


    // Attach over text
    $$('.field_extraoptions_add input').each(function(el){ new OverText(el); });


   
  }

  // Read the parent-option-child identifiers
  var readIdentifiers = function(string, throwException) {
    var m;

    // Find in ID
    m = string.match(/([0-9]+)_([0-9]+)_([0-9]+)(_([0-9]+))?/);
    if( $type(m) && $type(m[2]) ) {
      var dat = new Hash({
        parent_id : m[1],
        option_id : m[2],
        child_id : m[3]
      });
      if( $type(m[5]) ) {
        dat.set('suboption_id', m[5]);
      }
      return dat;
    }

    // Find in CLASS
    m = string.match(/parent_([0-9]+).+option_([0-9]+).+child_([0-9]+)/);
    if( $type(m) && $type(m[2]) ) {
      return new Hash({
        parent_id : m[1],
        option_id : m[2],
        child_id : m[3]
      });
    }

    // Not found
    if( !$type(throwException) || throwException ) {
      throw '<?php echo $this->string()->escapeJavascript($this->translate("Unable to find identifiers in text:")) ?> ' + string;
    } else {
      return false;
    }
  }

  var consoleLog = function() {
    //if( logging && typeof(console) != 'undefined' && console != null ) {
    if( logging ) {
      //if( typeof(console) !== 'undefined' && console != null ) {
      //  console.log(arguments);
        //console.log.apply(null, arguments);
      //}
    }
  }

  var genericUpdateKeys = function(htmlArr) {
    consoleLog(htmlArr);
    $H(htmlArr).each(function(html, key) {
      var oldEl = $('admin_field_' + key);
      var newEl = Elements.from(html)[0];
      if( oldEl && !newEl ) { // Remove
        consoleLog('remove', key);
        oldEl.destroy();
      } else if( oldEl && newEl ) { // Replace
        consoleLog('replace', key);
        newEl.replaces(oldEl);
      } else if( !oldEl && newEl ) { // Add
        consoleLog('add', key);
        // This could cause future replaces
        var ids = readIdentifiers(key);
        if( ids.option_id == topLevelOptionId ) {
          var targetEl = $$('.ynstore_attributes')[0];
          if( !targetEl ) {
            //throw '<?php echo $this->string()->escapeJavascript($this->translate("could not find target element")) ?>';
          } else {
            newEl.inject(targetEl, 'bottom');
          }
        } else {
          var selector =
            '.admin_field_dependent_field_wrapper_' + ids.option_id +
            ' .ynstore_attributes';
          var targetEl = $$(selector)[0];
          if( !targetEl ) {
            //throw '<?php echo $this->string()->escapeJavascript($this->translate("could not find target element")) ?>';
          } else {
            newEl.inject(targetEl, 'bottom');
          }
        }
      }
    });
    registerEvents();
  }

  var showSaveOrderButton = function() {
    //$$('.admin_fields_options_saveorder')[0].setStyle('display', '').removeEvents().addEvent('click', function() {
      saveOrder();
    //});
  }

  var saveOrder = function() {
    $$('.admin_fields_options_saveorder')[0].setStyle('display', 'none');

    // Generate order structure
    var fieldOrder = [];
    var optionOrder = [];

    // Fields (maps) order
    $$('.admin_field').each(function(el) {
      var ids = readIdentifiers(el.get('id'));
      fieldOrder.push(ids.getClean());
    });

    // Options order
    $$('.field_option_select').each(function(el) {
      var ids = readIdentifiers(el.get('id'));
      optionOrder.push(ids.getClean());
    });

    // Send request
    var request = new Request.JSON({
      'url' : urls.order,
      'data' : {
        'fieldType' : fieldType,
        'format' : 'json',
        'fieldOrder' : fieldOrder,
        'optionOrder' : optionOrder
      },
      onSuccess : function(responseJSON, responseHTML) {
        //alert('Order saved!');
      }
    });

    request.send();
  }

  /* --------------------------- OPTION - GENERAL --------------------------- */

  var uiToggleOptions = function(spec, forceClose) {
	if (spec instanceof Object) {
		element = spec.target;
	}
	if (spec instanceof Element) {
		element = spec;
	}
    element = element.getParent('.admin_field').getElement('.field_extraoptions');
    var targetState = !element.hasClass('active');
    if( $type(forceClose) && !forceClose ) targetState = false;
    !targetState ? element.removeClass('active') : element.addClass('active');
    OverText.update();
  }

  var uiToggleOptionDepFields = function(event) {
    element = $(event.target);
    element = element.getParent('.field_option_select') || element.getParent('.admin_field_dependent_field_wrapper');
    var ids = readIdentifiers(element.get('id'));
    var wrapper = element.getParent('.admin_field').getElement('.admin_field_dependent_field_wrapper_' + ids.suboption_id);
    var hadClass = wrapper.hasClass('active');
    $$('.admin_field_dependent_field_wrapper').removeClass('active');
    hadClass ? wrapper.removeClass('active') : wrapper.addClass('active');
    uiToggleOptions(element, false);

    // Make sure parents stay open
    var tmpEl = element;
    while( null != (tmpEl = tmpEl.getParent('.admin_field_dependent_field_wrapper')) ) {
      tmpEl.addClass('active');
    }
  }



  /* --------------------------- OPTION - CREATE --------------------------- */

  // Handle the ui stuff for creating an option using a text input
  var uiTextOptionCreate = function(event) {
    if( event.key != 'enter' ) {
      return;
    }
    var ids = readIdentifiers(this.getParent('.field_extraoptions').get('id'));
    doOptionCreate(ids.child_id, this.value);
    this.value = '';
    this.blur();
  }

  // Handle ui stuff for creating an option using a smoothbox
  var uiSmoothOptionCreate = function(field_id) {
    var url = urls.option.create;
    url += '/field_id/' + field_id + '/format/smoothbox';
    Smoothbox.open(url);
  }

  var uiSmoothTopOptionCreate = function(spec) {
    var url = urls.type.create;
    url += '/field_id/' + topLevelFieldId + '/format/smoothbox';
    Smoothbox.open(url);
  }

  // Handle data for option creation
  var doOptionCreate = function(field_id, label) {
    var url = urls.option.create;
    var request = new Request.JSON({
      'url' : url,
      'data' : {
        'format' : 'json',
        'fieldType' : fieldType,
        'field_id' : field_id,
        'product_id' : '<?php if ($this->product) echo $this->product->product_id?>',
        'label' : label
      },
      onSuccess : function(responseJSON) {
        onOptionCreate(responseJSON.option, responseJSON.htmlArr);
      }
    });
    request.send();
  }

  var onOptionCreate = function(option, htmlArr) {
    genericUpdateKeys(htmlArr);
  }

  var onTypeCreate = function(option) {
    (new Element('option', {
      'label' : option.label,
      'html' : option.label,
      'value' : option.option_id
    })).inject($('profileType'), 'bottom');
  }

  /* ---------------------------- OPTION - EDIT ---------------------------- */

  // Handle ui stuff for creating an option using a smoothbox
  var uiSmoothOptionEdit = function(option) {
    if( $type(option) == 'event' ) {
    	if (option instanceof Object) {
    		el = option.target;
    	}
    	if (option instanceof Element) {
    		el = option;
    	}
      var ids = readIdentifiers(el.getParent('.field_option_select').get('id'));
      if( !$type(ids.suboption_id) ) {
        throw "no option id found";
      }
      option = ids.suboption_id;
      uiToggleOptions(el);
    }
	if (option instanceof Object) {
		el = option.target;
	}
	if (option instanceof Element) {
		el = option;
	}
    list_option =el.getParent().getParent('.field_option_select').get('id');
    list_split = list_option.split("_");
    option = list_split[list_split.length - 1];
    var url = urls.option.edit;
    url += '/option_id/' + option + '/format/smoothbox';
    Smoothbox.open(url);
    uiToggleOptions(el);
  }

  var uiSmoothTopOptionEdit = function(spec) {
    var url = urls.type.edit;
    url += '/option_id/' + topLevelOptionId + '/format/smoothbox';
    Smoothbox.open(url);
  }

  var onOptionEdit = function(option, htmlArr) {
    genericUpdateKeys(htmlArr);
  }

  var onTypeEdit = function(option) {
    $('profileType').getChildren().each(function(el){
      if( el.value == option.option_id ) {
        el.set('label', option.label);
        el.set('html', option.label);
      }
    });
  }

  /* --------------------------- OPTION - DELETE --------------------------- */

  var uiConfirmOptionDelete = function(spec) {
		if (spec instanceof Object) {
			element = spec.target;
		}
		if (spec instanceof Element) {
			element = spec;
		}
    element = element.getParent('.field_option_select');
    var ids = readIdentifiers(element.get('id'));
    if( !$type(ids.suboption_id) ) {
      throw '<?php echo $this->string()->escapeJavascript($this->translate("unable to find option_id")) ?>';
    }
    if( confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete this option?")) ?>') ) {
      doOptionDelete(ids.suboption_id);
    }
  }

  var uiSmoothTopOptionDelete = function(spec) {
    if( confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the current profile type?")) ?>') ) {
      var url = urls.type.remove;
      url += '/option_id/' + topLevelOptionId + '/format/smoothbox';
      var request = new Request.JSON({
        url : url,
        onComplete : function() {
          onTypeDelete();
        }
      });
      request.send();
    }
    //Smoothbox.open(url);
  }

  var doOptionDelete = function(option_id) {
    $$('.field_option_select_' + option_id).destroy();
    $$('.admin_field_dependent_field_wrapper_' + option_id).destroy();
    var url = urls.option.remove;
    var request = new Request.JSON({
      'url' : url,
      'data' : {
        'format' : 'json',
        'fieldType' : fieldType,
        'option_id' : option_id
      }
    });
    request.send();
  }

  var onTypeDelete = function() {
    window.location = urls.index;
  }

  /* ---------------------------- FIELD - CREATE ---------------------------- */

  var uiSmoothCreateField = function(spec) {
    if( $type(spec) == 'event' || $type(spec) == 'domevent' ) {
      element = $(spec.target);
    } else if( $type(spec) == 'element' || $type(spec) == 'domelement' ) {
      element = $(element);
    } else {
      throw '<?php echo $this->string()->escapeJavascript($this->translate("unable to find option_id for field")) ?>';
    }
    var parentEl = element.getParent('.admin_field_dependent_field_wrapper');
    var ids = readIdentifiers(parentEl.get('id'));
    var url = urls.attribute.create;
    url += '/option_id/' + ids.suboption_id + '/parent_id/' + ids.parent_id + '/format/smoothbox';
    Smoothbox.open(url);
  }

  var uiSmoothTopAttributeCreate = function(spec) {
    var url = urls.attribute.create;
    url += '/set_id/' + '<?php if ($this->set) echo $this->set->set_id?>' + '/option_id/' + topLevelOptionId + '/parent_id/' + topLevelFieldId + '/format/smoothbox';
    Smoothbox.open(url);
  }

  var onFieldCreate = function(field, htmlArr) {
    genericUpdateKeys(htmlArr);
  }

  /* ----------------------------- FIELD - EDIT ----------------------------- */

  var uiSmoothEditField = function(spec) {
    if( $type(spec) == 'event' || $type(spec) == 'domevent' ) {
      element = $(spec.target);
    } else if( $type(spec) == 'element' || $type(spec) == 'domelement' ) {
      element = $(element);
    } else {
      throw '<?php echo $this->string()->escapeJavascript($this->translate("unable to find option_id for field")) ?>';
    }
    var parentEl = element.getParent('.admin_field');
    var ids = readIdentifiers(parentEl.get('id'));
    var url = urls.attribute.edit;
    var pro_id = '<?php echo $this->pro_id?>';
    if (pro_id == '') {
        pro_id = 0;
    }
    url += '/field_id/' + ids.child_id + '/pro_id/' + pro_id + '/format/smoothbox';
    Smoothbox.open(url);
  }

  var onFieldEdit = function(field, htmlArr) {
    genericUpdateKeys(htmlArr);
  }

  /* ---------------------------- FIELD - DELETE ---------------------------- */

  var uiConfirmDeleteField = function(spec) {
    if( $type(spec) == 'event' || $type(spec) == 'domevent' ) {
      element = $(spec.target);
    } else if( $type(spec) == 'element' || $type(spec) == 'domelement' ) {
      element = $(element);
    } else {
      throw '<?php echo $this->string()->escapeJavascript($this->translate("unable to find option_id for field")) ?>';
    }
    var parentEl = element.getParent('.admin_field');
    var ids = readIdentifiers(parentEl.get('id'));
    var url = urls.attribute.edit;
    var pro_id = '<?php echo $this->pro_id?>';
    if (pro_id == '') {
	    if( confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete this attribute?")) ?>') ) {
	      doFieldDelete(ids.child_id);
	      //doFieldUnMap(ids.parent_id, ids.option_id, ids.child_id);
	    }
    }
    else {
    	if( confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to remove content of this attribute?")) ?>') ) {
  	      doAttributeDelete(ids.child_id,pro_id);
  	      //doFieldUnMap(ids.parent_id, ids.option_id, ids.child_id);
  	    }
    }
  }

  var doFieldDelete = function(field_id) {
    $$('.admin_field_child_' + field_id).destroy();
    var url = urls.attribute.remove;
    var request = new Request.JSON({
      'url' : url,
      'data' : {
        'format' : 'json',
        'fieldType' : fieldType,
        'field_id' : field_id
      }
    });
    request.send();
  }
  var doAttributeDelete = function(field_id, pro_id) {
    var url = urls.attribute.remove;
    var request = new Request.JSON({
      'url' : url,
      'data' : {
        'format' : 'json',
        'fieldType' : fieldType,
        'field_id' : field_id,
        'pro_id' : pro_id
      }
    });
    request.send();
  }

  var doFieldUnMap = function(parent_id, option_id, child_id) {
    $$('.admin_field_child_' + child_id).destroy();
    var url = urls.map.remove;
    var request = new Request.JSON({
      'url' : url,
      'data' : {
        'format' : 'json',
        'fieldType' : fieldType,
        'parent_id' : parent_id,
        'option_id' : option_id,
        'child_id' : child_id
      }
    });
    request.send();
  }


</script>
