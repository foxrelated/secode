function BaseElement(){

    // GRID SIZE, HARDCODED FOR NOW,... AND MAYBE FOR EVER 79x12 ~ 950 ADMIN LAYOUT WIDTH
    this.gridSize = 79;
    this.type = '';
    this.id = '';
    //PROPERTIES OF EDIT FORM
    this.editFormTitle = '';
    this.editFormWidth = 650;
    this.params = {};

    // HANDLE ELEMENTS FOR RESIZABLE WIDGET
    this.resizeHandleEles = '<div class="ui-resizable-handle ui-resizable-nw" id="nwgrip"></div>' +
        '<div class="ui-resizable-handle ui-resizable-ne" id="negrip"></div>' +
        '<div class="ui-resizable-handle ui-resizable-sw" id="swgrip"></div>' +
        '<div class="ui-resizable-handle ui-resizable-se" id="segrip"></div>' +
        '<div class="ui-resizable-handle ui-resizable-n" id="ngrip"></div>' +
        '<div class="ui-resizable-handle ui-resizable-s" id="sgrip"></div>' +
        '<div class="ui-resizable-handle ui-resizable-e" id="egrip"></div>' +
        '<div class="ui-resizable-handle ui-resizable-w" id="wgrip"></div>';

    // HANDLE VARIABLE TO PASSED TO JQ RESIZABLE
    this.resizeHandles = {
        'nw': '#nwgrip',
        'ne': '#negrip',
        'sw': '#swgrip',
        'se': '#segrip',
        'n': '#ngrip',
        'e': '#egrip',
        's': '#sgrip',
        'w': '#wgrip'
    };

    this.html = '';

    // BASE CSS FOR ALL ELEMENT TYPE, OVERRIDE IF NEEDED
    this.elementCSS = {
        border : 0,
        height : 80,
        width: parseInt(this.gridSize * 4),
        position: 'absolute'
    };
}

// GET PARAMS AND MERGE WITH DEFAULT PARAMS
BaseElement.prototype.setParams = function(params){
    this.params = jQuery.extend(this.params, params);
};

BaseElement.prototype.render = function(){

    // GET THE EDITOR CONTAINER
    var editor = jQuery('#slide_editor'),
    // CREATE JQUERY ELEMENT
        element = jQuery(jQuery.parseHTML(this.html)),
        parent = this;

    // ADD BUTTONS
    var editButtons =
        '<div class="ynfullslider_btn_element_list clearfix">' +
        '<a href="javascript:void(0)" title="' + ynfullsliderGetFormText("moveforward") + '" onclick="ynfullsliderMoveElement(\'' + this.id + '\', 1)" class="ynfullslider_btn_element ynfullslider_moveforward_element"><i class="fa fa-caret-up"></i></a>' +
        '<a href="javascript:void(0)" title="' + ynfullsliderGetFormText("movebackward") + '" onclick="ynfullsliderMoveElement(\'' + this.id + '\', 0)" class="ynfullslider_btn_element ynfullslider_moveback_element"><i class="fa fa-caret-down"></i></i></a>' +
        '<a href="javascript:void(0)" title="' + ynfullsliderGetFormText("edit") + '" onclick="ynfullsliderEditElement(\'' + this.id + '\')" class="ynfullslider_btn_element ynfullslider_edit_element"><i class="fa fa-magic"></i></a>' +
        '<a href="javascript:void(0)" title="' + ynfullsliderGetFormText("delete") + '" onclick="ynfullsliderDeleteElement(\'' + this.id + '\')" class="ynfullslider_btn_element ynfullslider_delete_element"><i class="fa fa-trash"></i></a>' +
        '</div>';

    element.append(editButtons);

    // INIT CSS
    jQuery.each(this.elementCSS, function(prop, value){
        element.css(prop, value);
    });

    // DRAGGABLE
    element.draggable({
        containment: "#slide_editor",
        cancel: false
    });

    // TOGGLE RESIZEABLE (SELECTED)
    element.mousedown(function(e){

        // PREVENT CLICK THROUGH ELEMENT AND TRIGGER EDITOR CLICK TO UN-SELECT ALL OTHER ELEMENTS
        e.stopPropagation();

        parent.selectElement();
    });

    // ADD ANIMATION ELEMENT
    var animationHTML = '<li class="ynfullslider_slide_editor_header-list-item clearfix" onclick="ynfullsliderAnimationSelect(this)">' +
        '<div class="ynfullslider_slide_editor_header-list-drag">' +
        '<i class="fa fa-bars"></i>' +
        '</div>' +
        '<div class="ynfullslider_slide_editor_header-list-title">' +
        '<span class="ynfullslider_anim_title"></span>' +
        '<span class="ynfullslider_anim_detail"></span>' +
        '</div>' +
        '<div class="ynfullslider_slide_editor_header-list-actions">' +
        '<a href="javascript:void(0)" onclick="ynfullsliderEditElement(\'' + this.id + '\')"><i class="fa fa-magic"></i></a>' +
        '<a href="javascript:void(0)" onclick="ynfullsliderUpdateElement(\'' + this.id + '\', {transition_id:\'\' , random_transition: 0})"><i class="fa fa-times"></i></a>' +
        '</div>' +
        '</li>';

    // STORE ANI ELEMENT
    this.animationElement = jQuery(jQuery.parseHTML(animationHTML));

    // ADD THE ELEMENT TO EDITOR
    element.appendTo(editor);

    // STORE ELEMENT
    this.element = element;

    // ASSIGN FORM
    this.form = document.getElementById("ynfullslider_form_" + this.type);

    // STORE PARAMS
    this.setContent(this.params);
};


BaseElement.prototype.deleteElement = function() {
    var acceptDelete = confirm('Are you sure you want to delete this ' + this.type + ' element?');
    if (acceptDelete) {
        selectedId = '';
        this.element.remove();
        // REMOVE ANIMATION ELEMENT
        jQuery('#ynfs_anim_' + this.id).remove();
        ynfullsliderUpdateAnimationCount();
        delete ynfullsliderElements[this.id];
    }
};

BaseElement.prototype.selectElement = function() {
    // REMOVE OTHER RESIZEABLE
    var currentEle = this.element;
    var parent = this;
    jQuery('.ynfullslider_element').each(function(){
        if (currentEle[0] != this && jQuery(this).data('ui-resizable')) {
            jQuery(this).resizable("destroy");
        }
    });

    if (!jQuery(currentEle).data('ui-resizable')) {
        jQuery(currentEle).append(parent.resizeHandleEles).resizable({
            containment: "parent",
            handles: parent.resizeHandles
        });
        selectedId = this.id;
    }
};

BaseElement.prototype.editContent = function() {
    var elementObject = this,
        elementType = elementObject.type,
        dialog;

    dialog = jQuery('#ynfullslider_dialog_' + elementType).dialog({
        title: ynfullsliderGetFormText(elementType),
        width: elementObject.editFormWidth,
        //modal: true,
        buttons: [
            {
                text: ynfullsliderGetFormText("Cancel"),
                click: function() {
                    dialog.dialog( "close" );
                }
            },
            {
                text: ynfullsliderGetFormText("Save"),
                click: function() {
                    elementObject.saveDialog(dialog);
                }
            }
        ],
        open: function( event, ui ) {
            elementObject.populateForm();
            //jQuery('.color').colorPicker({
            //    animationSpeed: 0
            //});
            jQuery('.cp-color-picker').hide();
        },
        beforeClose: function( event, ui ) {
            elementObject.form.reset();
            //jQuery().colorPicker.destroy();
        }

    });
};

BaseElement.prototype.setContent = function(params) {

    // SAVE NEW PARAMS
    this.params = params;
    var content = jQuery(this.element).children(".ynfullslider_element_content");

    // APPLY CSS
    jQuery.each(params, function(prop, value){
        if (prop.substring(0,4) == 'css_') {
            content.css(prop.slice(4, prop.length), Number(value) ? parseInt(value) : value);
        }
    });

    this.setAnimationItem();
    this.customSetContent();
};

BaseElement.prototype.setAnimationItem = function() {
    // SET ANIMATION ELEMENT
    var animationPane = jQuery('#ynfullslider_animation_pane');
    this.animationElement.attr('id', 'ynfs_anim_' + this.id);

    // BUILD TITLE
    var title = this.id.replace('_', ' ');
    if (this.type == 'text' && this.params.body.length > 1) {
        var html = this.params.body;
        var div = document.createElement("div");
        div.innerHTML = html;
        title = div.textContent || div.innerText || this.id.replace('_', ' ');
    }
    title = title.charAt(0).toUpperCase() + title.slice(1);

    // BUILD ANIMATION TIME, NAME, AND STRING
    var animationString = '',
        duration = this.params.transition_duration,
        delay = this.params.transition_delay,
        animationName = '';
    duration = duration > 1000 ? Math.round(duration/1e3 * 10)/10 + 's' : duration + 'ms';
    delay = delay > 1000 ? Math.round(delay/1e3 * 10)/10 + 's' : delay + 'ms';
    animationName = this.params.random_transition ? 'Random' : animationArrays[this.params.transition_id];
    animationString += animationName + ' (duration: ' + duration + ' , delay: ' + delay + ')';
    this.animationElement.find('.ynfullslider_anim_title').html(title);
    this.animationElement.find('.ynfullslider_anim_detail').html(animationString);

    var animationElementDOM = jQuery('#ynfs_anim_' + this.id);

    // ADD OR REMOVE ANIMATION ITEM
    if (this.params.transition_id.length > 0 || this.params.random_transition) {
        if (!animationElementDOM.length)
            this.animationElement.appendTo(animationPane);
    } else {
        if (animationElementDOM.length)
            animationElementDOM.remove();
    }

    ynfullsliderUpdateAnimationCount();
};

BaseElement.prototype.populateForm = function(){
    var element = this;
    var params = element.params;
    jQuery('.form-error').hide();
    jQuery.each(this.form.elements, function(index, el){
        if (el.type == "text" || el.type == "number" || el.type == "textarea" || el.type == "select-one" || el.type == "hidden") {
            el.value = params[el.name];
        } else if (el.type == 'checkbox') {
            el.checked = params[el.name] ? true : false;
        } else if (el.type == 'radio') {
            el.checked = params[el.name] && el.value == params[el.name];
        }
    });
    jQuery(this.form).find('.color').each(function(){
        var el = jQuery(this);
        // CALCULATE LIGHT TO SET COLOR OF TEXT
        var rgb = el.val();
        var rgbA = rgb.replace(/[^\d,]/g, '').split(',');
        var max = Math.max(rgbA[0], rgbA[1], rgbA[2]), min = Math.min(rgbA[0], rgbA[1], rgbA[2]);
        var l = (max + min) / 2;
        el.css('background-color', rgb);
        el.css('color', l > 129 ? 'black' : 'white');
    });
    // MORE ACTION SPECIFIC TO ELEMENT TYPE
    this.customPopulateForm();
};

BaseElement.prototype.collectForm = function() {
    var params = {};
    jQuery.each(this.form.elements, function(index, el){
        if (el.type == 'text' || el.type == 'number' || el.type == "select-one" || el.type == "hidden") {
            params[el.name] = el.value;
        } else if (el.type == 'checkbox') {
            params[el.name] = el.checked;
        } else if (el.type == 'radio') {
            if (el.checked)
                params[el.name] = el.value;
        }
    });
    params = jQuery.extend(params, this.customCollectForm());
    return params;
};

BaseElement.prototype.cancelDialog = function(dialog){
        dialog.dialog( "close" );
};

BaseElement.prototype.saveDialog = function(dialog){
    if (this.validateForm()) {
        this.setContent(this.collectForm());
        //this.cancelDialog(dialog);
        dialog.dialog( "close" );
    }
};

BaseElement.prototype.validateForm = function() {
    return true;
};

BaseElement.prototype.getDimensions = function() {

    var dimensions = {
        top : this.element.position().top,
        left : this.element.position().left,
        width : this.element.width(),
        height : this.element.height()
    };
    // NO LONGER USING GRID AS NEW REQUIREMENTS
    //dimensions.gridLeft = Math.round(dimensions.left/this.gridSize);
    //dimensions.gridWidth = Math.round(dimensions.width/this.gridSize);
    return dimensions;
};

BaseElement.prototype.setDimensions = function(dimensions) {
    var element = this.element;
    var editor = jQuery('#slide_editor');

    jQuery.each(dimensions, function(prop, value){
        element.css(prop, value);
    });
    // MOVE ELEMENT DOWN IF SLIDER HEIGHT IS REDUCED
    if(element.height() > editor.height())
        element.height(editor.height());
    if (element.position().top + element.height() > editor.height())
        element.css('top', editor.height() - element.height());
};

// PROTOTYPE FUNCTIONS THAT MAYBE REPLACED
BaseElement.prototype.customSetContent = function() {
};

BaseElement.prototype.customPopulateForm = function() {
};

BaseElement.prototype.customCollectForm = function() {
};