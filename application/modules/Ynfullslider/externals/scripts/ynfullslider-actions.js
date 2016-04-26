function ynfullslider_set_color(el) {
    var color = jQuery('#' + el + '_color').val();
    jQuery('#' + el + '_color_picker').val(color);
}

function ynfullslider_update_color(el) {
    jQuery('#' + el + '_color').val(jQuery('#' + el + '_color_picker').val()).change();
}

function ynfullsliderReloadSlider(currentParams, previewSliderUrl) {
    // SET NEW PARAMS
    var newParams = {};
    var formElements = document.querySelectorAll('form.global_form')[0].elements;

    // GET PARAMS IN CURRENT FORM
    jQuery.each(formElements, function(index, el){
        if (el.type == 'text' || el.type == "select-one" || el.type == "hidden") {
            newParams[el.name] = el.value || 0;
        } else if (el.type == 'checkbox') {
            newParams[el.name] = el.checked;
        } else if (el.type == 'radio') {
            if (el.checked)
                newParams[el.name] = el.value;
        }
    });

    if (newParams['transition_duration']) {
        if (newParams['transition_duration'] > 4000) {
            newParams['transition_duration'] = 4000;
        }
    } else {
        newParams['transition_duration'] = 50;
    }

    // UPDATE SLIDER PARAMS WITH CURRENT PARAMS
    var previewParams = jQuery.extend(currentParams, newParams);
    var previewContainer = jQuery("#preview_container");

    // SET PREVIEW CONTAINER HEIGHT TO PREVENT SCREEN FLASHING
    previewContainer.css('min-height', parseInt(previewContainer.height()));

    // CALL THE AJAX AND UPDATE CONTENT
    previewContainer.load(previewSliderUrl, {preview_params: JSON.stringify(previewParams)}, function(){
        previewContainer.css('min-height', 50);
    });

    // SCROLL TO PREVIEW
    //jQuery('html, body').animate({
    //    scrollTop: previewContainer.offset().top
    //    //scrollTop: 0
    //}, 300);
}

var previewTimout;

function ynfullsliderRefreshPreviewSlider() {
    clearTimeout(previewTimout);
    previewTimout = setTimeout(ynfullsliderPreviewSlider, 200);
}