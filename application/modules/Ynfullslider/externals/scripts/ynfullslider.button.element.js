function ButtonElement(elementId, params){

    this.type = 'button';
    this.id = elementId;
    this.editFormTitle = 'Add Button';
    this.editFormWidth = 650;

    this.params = {
        'body': '<p style="text-align: center;"><span style="color: #000000; font-size: 12pt;">Button</span></p>',
        'link_to' : '',
        'css_border-radius' :   '3',
        'css_background-color' :    'rgb(97, 157, 190)',
        'css_border-color' :    'rgb(80, 128, 155)',
        'css_border-width' :     '1',
        'random_transition':    0,
        'transition_id':        '',
        'transition_duration':  '500',
        'transition_delay':     '500',
        'show_all':             1,
        'show_desktop':         1,
        'show_mobile':          1,
        'show_tablet':          1
    };

    this.elementCSS = {
        top: 0,
        left: 0,
        width: this.gridSize * 2,
        height: 50,
        position: 'absolute'
    };

    //@TODO css button
    this.html = '<a id="' + elementId + '" class="ynfullslider_element button_element ynfullslider_element_content">' +
        '<div class="ynfullslider_element_button_wrapper">' +
            '<div class="ynfullslider_element_content">Button</div>' +
        '</div>' +
        '</a>';

    this.setParams(params);
    this.render();
}

ButtonElement.prototype = new BaseElement();

ButtonElement.prototype.customSetContent = function() {
    var content = jQuery(this.element).find(".ynfullslider_element_content");
    var element = this.element;

    // APPLY CSS
    jQuery.each(this.params, function(prop, value){
        if (prop.substring(0,4) == 'css_') {
            element.css(prop.slice(4, prop.length), Number(value) ? parseInt(value) : value);
        }
    });
    content.html(this.params.body);
};

ButtonElement.prototype.customPopulateForm = function() {
    jQuery(this.form).find('textarea[name="body"]').tinymce({
        menubar: false,
        statusbar: false,
        height: 100,
        fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt 48pt 60pt 72pt',
        plugins: 'link textcolor paste',
        paste_auto_cleanup_on_paste : true,
        paste_remove_styles: true,
        paste_remove_styles_if_webkit: true,
        paste_strip_class_attributes: true,
        toolbar: 'fontselect fontsizeselect forecolor bold italic underline strikethrough',
        content_css: 'https://fonts.googleapis.com/css?family=Open+Sans,'
        + 'https://fonts.googleapis.com/css?family=Lato,'
        + 'https://fonts.googleapis.com/css?family=Oswald,'
        + 'https://fonts.googleapis.com/css?family=Passion+One,'
        + 'https://fonts.googleapis.com/css?family=Questrial,'
        + 'https://fonts.googleapis.com/css?family=Yesteryear,'
    });
    //RESET TINYMCE CONTENT
    if (tinyMCE.activeEditor)
        tinyMCE.activeEditor.setContent(this.params['body']);
};

ButtonElement.prototype.customCollectForm = function() {
    var params = {};
    if (tinyMCE.activeEditor)
        params.body = tinyMCE.activeEditor.getContent();
    return params;
};
ButtonElement.prototype.getDimensions = function() {
    var content = jQuery(this.element).find(".ynfullslider_element_content");
    var dimensions = {
        top : this.element.position().top,
        left : this.element.position().left,
        width : this.element.width(),
        height : this.element.height()
    };
    this.params['padding-top'] = Math.round((dimensions.height - content.height())/2);
    this.params['padding-left'] = Math.round((dimensions.width - content.width())/2);
    return dimensions;
};