function ImageElement(elementId, params){

    this.type = 'image';
    this.id = elementId;
    this.editFormTitle = 'Add Image';

    this.params = {
        'link_to' : '',
        'file_id' : 0,
        'image_path': 'application/modules/Ynfullslider/externals/images/default_image_element.png',
        'css_border-radius' :   '0',
        'css_border-color' :    'rgb(200, 200, 200)',
        'css_border-width' :    '0',
        'random_transition':    0,
        'transition_id':        '',
        'transition_duration':  '500',
        'transition_delay':     '500',
        'show_all':             1,
        'show_desktop':         1,
        'show_mobile':          1,
        'show_tablet':          1
    };

    this.html = '<div id="' + elementId + '" class="ynfullslider_element image_element">' +
        '<div class="ynfullslider_element_content">' +
        '<img src="" />' +
        '<div>' +
        '</div>';

    this.elementCSS = {
        border : 0,
        height : 200,
        width: parseInt(this.gridSize * 4),
        position: 'absolute'
    };

    this.setParams(params);
    this.render();
}

ImageElement.prototype = new BaseElement();

ImageElement.prototype.customSetContent = function() {
    var content = jQuery(this.element).children(".ynfullslider_element_content");
    // content.css('background-image', 'url(' + this.params.image_path +')')
    //     .css('background-size', 'cover');
    content.find("img").attr("src", this.params.image_path).css("width","100%").css("height","100%");

};

ImageElement.prototype.customPopulateForm = function() {
    if (this.params.image_path) {
        jQuery('#ynfullslider_image_form_image').css('background-image', 'url(' + this.params.image_path + ')');
    }
};

ImageElement.prototype.customCollectForm = function() {
    return {};
};