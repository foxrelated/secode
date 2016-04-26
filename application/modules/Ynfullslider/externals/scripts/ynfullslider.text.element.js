function TextElement(elementId, params){

    this.type = 'text';
    this.id = elementId;
    this.editFormTitle = 'Add Text';
    this.editFormWidth = 770;

    this.params = {
        'body' :                '<p style="text-align: center;"><span style="font-size: 14pt; color: #000000;">This is a text</span></p>',
        'css_background' :      'rgba(200, 200, 200, 0)',
        'css_border-radius' :   '0',
        'css_letter-spacing':   'normal',
        'random_transition':    0,
        'transition_id':        '',
        'transition_duration':  '500',
        'transition_delay':     '500',
        'show_all':             1,
        'show_desktop':         1,
        'show_mobile':          1,
        'show_tablet':          1
    };

    this.html = '<div id="' + elementId + '" class="ynfullslider_element text_element" data-type="text">' +
        '<div class="ynfullslider_element_content">' +
        '</div>' +
        '</div>';

    this.setParams(params);
    this.render();
}

// EXTEND BASE ELEMENT
TextElement.prototype = new BaseElement();

// SPECIFIC FUNCTION TO MODIFY EACH ELEMENT
TextElement.prototype.customSetContent = function() {
    var content = jQuery(this.element).children(".ynfullslider_element_content");
    content.html(this.params.body);
};

// ADDITIONAL ACTION WHEN POPULATING FORM
TextElement.prototype.customPopulateForm = function() {
    jQuery(this.form).find('textarea[name="body"]').tinymce({
        menubar: false,
        statusbar: false,
        height: 100,
        fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt 48pt 60pt 72pt',
        plugins: 'link textcolor paste',
        toolbar: 'fontselect fontsizeselect forecolor bold italic underline strikethrough numlist bullist outdent indent alignleft aligncenter alignright link',
        paste_auto_cleanup_on_paste : true,
        paste_remove_styles: true,
        paste_remove_styles_if_webkit: true,
        paste_strip_class_attributes: true,
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

TextElement.prototype.customCollectForm = function() {
    var params = {};
    if (tinyMCE.activeEditor)
        params.body = tinyMCE.activeEditor.getContent();
    return params;
};