function VideoElement(elementId, params){

    this.type = 'video';
    this.id = elementId;
    this.editFormTitle = 'Add Video';

    this.params = {
        'video_type':           "html5", // 0:html5,1:youtube
        'video_file_id':        0,
        'video_file_path':            '',
        'photo_id':             '',
        'photo_path':           'application/modules/Ynfullslider/externals/images/default_video_element.png',
        'youtube_iframe':       '',
        'youtube_code':       '',
        'css_border-radius':    '0',
        'css_border-color' :    'rgb(80, 80, 80)',
        'css_border-width' :    '0',
        'random_transition':    0,
        'transition_id':        '',
        'transition_duration':  500,
        'transition_delay':     500,
        'show_all':             1,
        'show_desktop':         1,
        'show_mobile':          1,
        'show_tablet':          1
    };

    this.html = '<div id="' + elementId + '" class="ynfullslider_element video_element">' +
        '<div class="ynfullslider_element_content video_content">' +
        '</div>' +
        '</div>';

    this.elementCSS = {
        border : 0,
        height : 180,
        width: parseInt(this.gridSize * 4),
        position: 'absolute'
    };

    this.setParams(params);
    this.render();
}

VideoElement.prototype = new BaseElement();

VideoElement.prototype.customSetContent = function() {
    var content = jQuery(this.element).children(".ynfullslider_element_content");
    if (this.params.photo_path == '')
        this.params.photo_path = 'application/modules/Ynfullslider/externals/images/default_video_element.png';
    content.css('background-image', 'url(' + this.params.photo_path +')')
        .css('background-size', 'cover');
};

VideoElement.prototype.customPopulateForm = function() {
    document.getElementById('fileName').innerHTML = '';
    document.getElementById('fileSize').innerHTML = '';
    document.getElementById('upload_status').innerHTML = '';
    document.getElementById('progress').style.display = 'none';
    document.getElementById('progressNumber').style.display = 'none';
    document.getElementById('demo-upload').style.display = 'none';

    ynfullsliderVideoFormTypeSelect();
    jQuery('input[name=video_type]').change(ynfullsliderVideoFormTypeSelect);
};

VideoElement.prototype.customCollectForm = function() {

    var youtube_video_id,
        params = {};

    params['photo_path'] = '';
    // GET YOUTUBE THUMBNAIL
    if (this.form.elements.video_type.value == "youtube") {
        var youtube_embedded = this.form.elements.youtube_iframe.value;
        if (youtube_embedded) {
            var p = /^.*(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?.*$/;
            youtube_video_id = youtube_embedded.match(p) ? RegExp.$1 : '';
            params['photo_path'] = youtube_video_id ? '//img.youtube.com/vi/' + youtube_video_id + '/0.jpg' : '';
            params['youtube_code'] = youtube_video_id;
        }
    } else {
        params['photo_path'] = this.form.elements.temp_photo_path.value != "undefined" ? this.form.elements.temp_photo_path.value : '';
    }
    return params;
};
