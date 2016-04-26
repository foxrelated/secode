<?php
    $scriptStaticBaseUrl = $this->layout()->staticBaseUrl . 'application/modules/Ynfullslider/externals/scripts/';
    $this->headLink()->appendStylesheet($staticBaseUrl . 'application/modules/Ynfullslider/externals/styles/jquery-ui.min.css');
    $this->headLink()->appendStylesheet('https://fonts.googleapis.com/css?family=Open+Sans');
    $this->headLink()->appendStylesheet('https://fonts.googleapis.com/css?family=Lato');
    $this->headLink()->appendStylesheet('https://fonts.googleapis.com/css?family=Oswald');
    $this->headLink()->appendStylesheet('https://fonts.googleapis.com/css?family=Passion+One');
    $this->headLink()->appendStylesheet('https://fonts.googleapis.com/css?family=Questrial');
    $this->headLink()->appendStylesheet('https://fonts.googleapis.com/css?family=Yesteryear');
    $this->headScript()->appendFile($scriptStaticBaseUrl . 'jquery-ui.min.js');
    $this->headScript()->appendFile($scriptStaticBaseUrl . 'tinymce/tinymce.js');
    $this->headScript()->appendFile($scriptStaticBaseUrl . 'tinymce/jquery.tinymce.min.js');
    $this->headScript()->appendFile($scriptStaticBaseUrl . 'jqColorPicker.min.js');

    $slide = $this->slide;
    $slider = $this->slider;
    $slideParams = $slide->getParams();
    $sliderParams = $slider->getParams();
?>

<style>
    .ynfullslider_element_content{
        background-size: cover;
        background-position: center;
    }
    .form-error {
        display: none;
    }

    .ynfullslider_slide_editor_header{
        width: calc(100% + <?php echo ($sliderParams['background_border_width'] * 2) ?>px);
    }

    #ynfullslider_image_form_image{
        margin:auto;
        max-width: 400px;
        height: 195px;
        background-size: cover;
        background-position: center center;
        border: 5px solid #e3e2e2;
    }
    .ui-widget-overlay{
        background: none;
    }
    .cp-color-picker{
        z-index: 102;
    }
    #slide_editor {
        position: relative;
    }
    .ynfullslider_element_content{
        /*overflow: hidden;*/
        height:100%;
        width:100%;
        /*padding: 15px;*/
    }
    #nwgrip, #negrip, #swgrip, #segrip, #ngrip, #egrip, #sgrip, #wgrip {
        width: 8px;
        height: 8px;
        background-color: #ffffff;
        border: 1px solid #000000;
    }
    #nwgrip {
        left: -5px;
        top: -5px;
    }
    #negrip{
        top: -5px;
        right: -5px;
    }
    #swgrip{
        bottom: -5px;
        left: -5px;
    }
    #segrip{
        bottom: -5px;
        right:-5px;
    }
    #ngrip{
        left:-moz-calc(50% - 5px);
        left:-webkit-calc(50% - 5px);
        left:calc(50% - 5px);
    }
    #sgrip{
        left:-moz-calc(50% - 5px);
        left:-webkit-calc(50% - 5px);
        left:calc(50% - 5px);
    }
    #wgrip{
        top:-moz-calc(50% - 5px);
        top:-webkit-calc(50% - 5px);
        top:calc(50% - 5px);
    }
    #egrip{
        top:-moz-calc(50% - 5px);
        top:-webkit-calc(50% - 5px);
        top:calc(50% - 5px);
    }
    
    #ynfullslider_slide_editor_wrapper{
        margin: auto;
        width: <?php if(($sliderParams['width_option'] == 0) || ($sliderParams['width_option'] == 2)) {echo "100%";}else{echo "1140px";} ?>;
    }

    .ynfullslider_editor_background-slider{
        <?php if($sliderParams['background_option'] == 0) : ?> 
            background-color: <?php echo $sliderParams['background_color'] ?>; 
        <?php else :?>
            background-image: url('<?php echo $sliderParams['background_image_url'] ?>');
        <?php endif; ?>
                
        background-position: <?php echo $sliderParams['background_image_position'] ?>;
        background-repeat: <?php echo $sliderParams['background_image_repeat'] ?>;
        background-size: <?php echo $sliderParams['background_image_size'] ?>;
        border-width: <?php echo $sliderParams['background_border_width'] ?>px;
        border-style: <?php echo $sliderParams['background_border_style'] ?>;
        border-color: <?php echo $sliderParams['background_border_color'] ?>;
        padding-top: <?php echo $sliderParams['spacing_top'] ?>px ;
        padding-bottom: <?php echo $sliderParams['spacing_bottom'] ?>px;
        width: 100%;
        margin:auto;
        margin-bottom: 40px;
        -webkit-box-sizing: content-box;
        -moz-box-sizing: content-box;
        -ms-box-sizing: content-box;
        box-sizing: content-box;
    }

    .ynfullslider_editor_background-slide{
        width: <?php if($sliderParams['width_option'] == 2){echo "100%";}else{echo "1140px";} ?>;
        margin: auto;
        <?php if(($slideParams['background_option'] == 0) || ($slideParams['background_option'] == 2)) :?>
            background-color:<?php echo $slideParams['slide_background_color'] ?>;
        <?php endif ?>
        <?php if($slideParams['background_option'] == 1) :?>
            background-image: url(<?php echo $slide->getPhotoUrl() ?>) ;
            background-size: <?php echo $slideParams['background_size'] ?>;
            background-position: <?php echo $slideParams['background_position'] ?>;
            background-repeat: <?php echo $slideParams['background_repeat'] ?>;
        <?php elseif($slideParams['background_option'] == 2) :?>
            background-image: url(<?php echo $slide->getPhotoUrl() ?>) ;
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        <?php endif ?>
    } 

    #slide_editor{
        background-image: none !important;  
        <?php if($sliderParams['width_option'] == 2) : ?>
            background: rgba(0,0,0,0.1);
        <?php endif; ?>
    }

    .ynfullslider_element_button_wrapper p{
        margin-bottom: 0;
    }

</style>

<h2>
    <?php echo $this->translate('YouNet Full Slider Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class="ynfullslider_manage_sliders">
    <h2 class="ynfullslider_title">
        <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'sliders', 'action' => 'index'),
            $this->translate("Manage Sliders"), array())
        ?>
        &nbsp;/&nbsp;
        <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'slider', 'action' => 'manage-slides', 'id'=>$slider->getIdentity()),
            $slider->getTitle(), array())
        ?>
        &nbsp;/&nbsp;
        <?php
            echo $slide->getTitle();
        ?>
    </h2>
</div>

<div id="slide_preview_wrapper" style="display:none;">
    <div id="slide_preview_container"></div>
</div>

<div id="ynfullslider_slide_editor_wrapper">
    <p align="center" style="color: #999"><?php echo $this->translate("Elements container max width is 1140px, regardless of slider width.") ?></p>
    <p align="center" style="color: #999"><?php echo $this->translate("Changing Slider Max-Height may affect the appearance of existing contents.") ?></p>
    <br/>
    <div class="ynfullslider_slide_editor_header clearfix">
        <div class="ynfullslider_slide_editor_header-title">
            <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfullslider', 'controller' => 'slider', 'action' => 'manage-slides', 'id' => $slider->getIdentity()),
                '<i class="fa fa-chevron-left"></i>', array('class'=>'ynfullslider_slide_editor_header-back'))
            ?>
            <span><?php echo $this->translate("content grid editor") ?></span>
        </div>
        <div id="slide_editor_buttons_bar" class="ynfullslider_slide_editor_header-bar">
            <a href="javascript:void(0)" onclick="ynfullsliderCreateElement('text')"><i class="fa fa-align-left"></i><?php echo $this->translate('Text') ?></a>
            <a href="javascript:void(0)" onclick="ynfullsliderCreateElement('image')"><i class="fa fa-picture-o"></i><?php echo $this->translate('Image') ?></a>
            <a href="javascript:void(0)" onclick="ynfullsliderCreateElement('video')"><i class="fa fa-video-camera"></i><?php echo $this->translate('Video') ?></a>
            <a href="javascript:void(0)" onclick="ynfullsliderCreateElement('button')"><i class="fa fa-hand-o-up"></i><?php echo $this->translate('Button') ?></a>
            <span class="ynfullslider_slide_editor_header-btn-animation"><i class="fa fa-magic"></i> <?php echo $this->translate('Animation') ?>(<span id="ynfullslider_animation_count">0</span>) &nbsp;<i class="fa fa-angle-down"></i></span>
            <ul id="ynfullslider_animation_pane" class="ynfullslider_slide_editor_header-list-items" style="display: none;">
            </ul>
        </div>
    </div>
    <div class="ynfullslider_editor_background-slider">
        <div class="ynfullslider_editor_background-slide">
            <div id="slide_editor" style="<?php echo $slide->getThumbnailStyle(true) ?>"></div>
        </div>

    </div>
</div>
<div>
    <?php echo $this->form->render($this) ?>
</div>

<!-- HIDDEN FORMS -->
<div class="ynfullslider_hidden_form" style="display: none;">
    <?php echo $this->partial('_slide_edit_form_text.tpl', 'ynfullslider') ?>
    <?php echo $this->partial('_slide_edit_form_image.tpl', 'ynfullslider') ?>
    <?php echo $this->partial('_slide_edit_form_button.tpl', 'ynfullslider') ?>
    <?php echo $this->partial('_slide_edit_form_video.tpl', 'ynfullslider') ?>
</div>

<script type="text/javascript">

    // SLIDE AND SLIDER PARAMS TO INIT EDITOR HEIGHT, BACKGROUND....
    var slideParams = <?php echo json_encode($slideParams) ?>;
    var sliderParams = <?php echo json_encode($sliderParams) ?>;
    slideParams.height = parseInt(sliderParams.max_height);

    // GLOBAL ELEMENTS HOLDER
    var ynfullsliderElements = { };

    // ELEMENTS COUNT TRACKER
    var elementsCount = { };

    // ELEMENT TYPES ARRAY
    var elementType = {
        text: TextElement,
        button: ButtonElement,
        image: ImageElement,
        video: VideoElement
    };

    var animationArrays = {
        '': '<?php echo $this->translate("No Animation") ?>',
        'sft': '<?php echo $this->translate("Short from Top") ?>',
        'sfb': '<?php echo $this->translate("Short from Bottom") ?>',
        'sfr': '<?php echo $this->translate("Short from Right") ?>',
        'sfl': '<?php echo $this->translate("Short from Left") ?>',
        'lft': '<?php echo $this->translate("Long from Top") ?>',
        'lfb': '<?php echo $this->translate("Long from Bottom") ?>',
        'lfr': '<?php echo $this->translate("Long from Right") ?>',
        'lfl': '<?php echo $this->translate("Long from Left") ?>',
        'skewfromleft': '<?php echo $this->translate("Skew from Left") ?>',
        'skewfromright': '<?php echo $this->translate("Skew from Right") ?>',
        'skewfromleftshort': '<?php echo $this->translate("Skew Short from Left") ?>',
        'skewfromrightshort': '<?php echo $this->translate("Skew Short from Right") ?>',
        'fade': '<?php echo $this->translate("Fading") ?>',
        'randomrotate': '<?php echo $this->translate("Fade in and Random Rotate") ?>'
    };
    // TRANSLATE FORM TITLE
    var editFormTitle = {
        "text": "<?php echo $this->translate('Add Text') ?>",
        "image": "<?php echo $this->translate('Add Image') ?>",
        "button": "<?php echo $this->translate('Add Button') ?>",
        "video": "<?php echo $this->translate('Add Video') ?>",
        "Cancel": "<?php echo $this->translate('Cancel') ?>",
        "Save": "<?php echo $this->translate('Save') ?>",
        "moveforward": "<?php echo $this->translate('Move forward') ?>",
        "movebackward": "<?php echo $this->translate('Move backward') ?>",
        "edit": "<?php echo $this->translate('Edit') ?>",
        "delete": "<?php echo $this->translate('Delete') ?>",
    };

    var preViewMode = false;
    // SELECTED ELEMENT
    var selectedId = '';
    // clipboard contain element
    var elementClip = { };

    var arrowKeys=[27,37,38,39,40];

    jQuery.noConflict();
    jQuery(document).ready(function(){
        window.editor = jQuery('#slide_editor');
        initEditor();
        // INSERT SAVED ELEMENTS
        var slideElementsValue = jQuery('#slide_elements').val(),
                elements = slideElementsValue ? JSON.parse(slideElementsValue) : { },
                elementsCountValue = jQuery('#elements_count').val(),
                elementsOrderValue = jQuery('#elements_order').val(),
                elementsOrder = elementsOrderValue ? JSON.parse(elementsOrderValue) : [],
                animationOrderValue = jQuery('#animation_order').val(),
                animationOrder = animationOrderValue ? JSON.parse(animationOrderValue) : [],
        //  INIT COUNT
                elementsCountInit = {
                    text: 0,
                    button: 0,
                    image: 0,
                    video: 0
                };
        elementsCount = elementsCountValue ? JSON.parse(elementsCountValue) : elementsCountInit;
        elementsCount = jQuery.extend(elementsCountInit, elementsCount);

        // INSERT OLD ELEMENTS
        if (!jQuery.isEmptyObject(elements) && elementsOrder.length) {
            var len = elementsOrder.length;
            for (var i=0 ; i < len; i++) {
                ynfullsliderInsertElement(elementsOrder[i], elements[elementsOrder[i]]);
            }
        }

        // ORDER ANIMATION PANE
        var animationPane = jQuery("#ynfullslider_animation_pane");
        var len = animationOrder.length;
        for (var i=0; i<len; i++){
            jQuery('#' + animationOrder[i]).insertAfter("#ynfullslider_animation_pane li:eq(" + i + ")");
        }

        // SORTABLE ANIMATION PANE
        animationPane.sortable({
            handle: 'div.ynfullslider_slide_editor_header-list-drag',
            scrollSensitivity: 30,
            scrollSpeed: 5
        });

        // PREVENT ENTER TO CREATE NEW ELEMENT
        jQuery("#slide_editor_buttons_bar").children().on( 'keydown', function( e ) {
            if( e.which == 13 ) {
                e.preventDefault();
            }
        });
    });

    // SETUP EDITOR
    function initEditor() {
        jQuery("#global_content").css("width", "<?php echo $this->content_width ?>");
        var editor = window.editor;
        editor.css('height', slideParams.height + 'px');
        editor.mousedown(ynfullsliderUnselectAll);
        // APPLY STYLE FOR SLIDE FROM SETTINGS
        if (slideParams.background_option == 1) {
            editor.css('background-repeat', slideParams.background_repeat);
            editor.css('background-position', slideParams.background_position);
            editor.css('background-size', slideParams.background_size);
        } else {
            editor.css('background-size', 'cover');
            editor.css('background-repeat', 'repeat');
        }
        jQuery('.color').colorPicker({
            animationSpeed: 50
        });

        var editorWidth = editor.width(),
            editorHeight = editor.height();
        // EVENT FOR KEYBOARD
        jQuery(document).on("keydown", function(e) {
            var key = e.which;
            var webkitDelete=(key==46 ? 1 : 0);
            var webKitCtrlC = (key == 67 && e.ctrlKey ? 1: 0);
            var webKitCtrlV = (key == 86 && e.ctrlKey ? 1: 0);
    //            var checkMoz=(e.which==122 && e.ctrlKey ? 1 : 0);
            if (jQuery(".ui-dialog:visible").length){
                return;
            } else if (webKitCtrlV && !jQuery.isEmptyObject(elementClip)) {
                elementsCount[elementClip.type] += 1;
                ynfullsliderInsertElement(elementClip.type + '_' + elementsCount[elementClip.type], elementClip);
            } else if (!selectedId || !ynfullsliderElements.hasOwnProperty(selectedId)){
                return;
            } else if (webkitDelete) {
                ynfullsliderDeleteElement(selectedId);
            } else if (webKitCtrlC) {
                elementClip = ynfullsliderGetElement(selectedId);
                elementClip.dimensions = ynfullsliderGetElement(selectedId).getDimensions();
                elementClip.dimensions.top = 0;
                elementClip.dimensions.left = 0;
            // PASTE
            } else if(jQuery.inArray(key, arrowKeys) > -1) {
                var elementObject = ynfullsliderGetElement(selectedId);
                var element = elementObject.element;
//                var currentPostion = element.getDimensions();
                e.preventDefault();
                switch(key) {
                    case 27:
                        ynfullsliderUnselectAll();
                        break;
                    case 37:
                        if (e.altKey && element.width() > 10) {
                            element.stop().animate({width:'-=1'}, 0);
                        } else if (e.shiftKey && element.position().left > 0) {
                            element.stop().animate({left:'-=1'}, 0);
                            element.stop().animate({width:'+=1'}, 0);
                        } else if (!e.altKey && element.position().left > 0){
                            element.stop().animate({left:'-=1'},0);
                        }
                        break;
                    case 38:
                        if (e.altKey && element.height() > 12) {
                            element.stop().animate({height:'-=1'},0);
                        } else if (e.shiftKey && element.position().top > 0) {
                            element.stop().animate({top:'-=1'},0);
                            element.stop().animate({height:'+=1'},0);
                        } else if (!e.altKey && element.position().top > 0){
                            element.stop().animate({top:'-=1'},0);
                        }
                        break;
                    case 39:
                        if (e.altKey && element.position().left + element.width() < editorWidth) {
                            element.stop().animate({width:'+=1'}, 0);
                        } else if (e.shiftKey && element.width() > 10) {
                            element.stop().animate({left:'+=1'},0);
                            element.stop().animate({width:'-=1'},0);
                        } else if (!e.shiftKey && element.position().left + element.width() < editorWidth) {
                            element.stop().animate({left:'+=1'},0);
                        }
                        break;
                    case 40:
                        if (e.altKey && element.position().top + element.height() < editorHeight) {
                            element.stop().animate({height:'+=1'},0);
                        } else if (e.shiftKey && element.height() > 12) {
                            element.stop().animate({top:'+=1'},0);
                            element.stop().animate({height:'-=1'},0);
                        } else if (!e.shiftKey && element.position().top + element.height() < editorHeight){
                            element.stop().animate({top:'+=1'},0);
                        }
                        break;
                }
            }
        });
    }

    // GET ELEMENT
    function ynfullsliderGetElement(id){
        return ynfullsliderElements[id];
    }
    // ADD A NEW ELEMENT
    function ynfullsliderCreateElement(type) {
        // UPDATE ELEMENT COUNT
        elementsCount[type] += 1;
        var params = { };
        // USE ELEMENT TYPE _ ELEMENT COUNT FOR ELEMENT ID
//        var randomId = Math.random().toString(36).substr(2, 9);
        var elementId = type + '_' + elementsCount[type];
        ynfullsliderElements[elementId] = new elementType[type](elementId, params);
    }

    // INSERT A SAVED ELEMENT
    function ynfullsliderInsertElement(id, element){
        ynfullsliderElements[id] = new elementType[element.type](id, element.params);
        ynfullsliderElements[id].setDimensions(element.dimensions);
    }

    // MOVE ELEMENT TO TOP OR BOTTOM
    function ynfullsliderMoveElement(id, direction){
        var element = ynfullsliderGetElement(id).element;
        direction ? jQuery(element).appendTo(window.editor) : jQuery(element).prependTo(window.editor);
    }

    // DELETE AN ELEMENT
    function ynfullsliderDeleteElement(id) {
        ynfullsliderGetElement(id).deleteElement();
    }

    // COMMON EDIT ELEMENT
    function ynfullsliderEditElement(id) {

        ynfullsliderGetElement(id).editContent();
    }

    // UPDATE A SPECIFIC PARAM AND REDRAW ON EDITOR AND ANIMATION PANE
    function ynfullsliderUpdateElement(id, params) {
        var element = ynfullsliderGetElement(id);
        element.setParams(params);
        element.setContent(element.params);
    }

    // SELECT ELEMENT FROM ANIMATION PANE
    function ynfullsliderAnimationSelect(el) {
        var element = jQuery(el).closest('.ynfullslider_slide_editor_header-list-item');
        var elementId = element.attr('id').substring(10,19);
        if (elementId) {
            var elementObject = ynfullsliderElements[elementId];
            elementObject.selectElement();
        }
    }

    // REMOVE ALL SELECTION
    function ynfullsliderUnselectAll() {
        jQuery('.ynfullslider_element').each(function () {
            if (jQuery(this).data('ui-resizable')) {
                jQuery(this).resizable("destroy");
            }
        });
        selectedId = '';
    }

    // IMAGE ELEMENT, HANDLE ELEMENT UPLOAD
    function ynfullsliderImageSelected() {
        var file = document.getElementById('imageToUpload').files[0];
        if (file) {
            ynfullsliderUploadImage(file);
        }
    }

    // IMAGE ELEMENT, HANDLE ELEMENT UPLOAD
    function ynfullsliderUploadImage(file) {
        var fd = new FormData();
        fd.append('fileToUpload', file);
        var xhr = new XMLHttpRequest();
        xhr.addEventListener("load", ynfullsliderUploadImageComplete, false);
        xhr.addEventListener("error", ynfullsliderUploadImageFailed, false);
        xhr.addEventListener("abort", ynfullsliderUploadImageCanceled, false);
        xhr.open("POST", "<?php echo $this->url(array('module' => 'ynfullslider', 'controller' => 'index', 'action' => 'upload-image', 'parent_type'=> $slide->getType(), 'parent_id' => $slide->getIdentity()), 'admin_default', true)?>", true);
        xhr.send(fd);
    }

    // IMAGE ELEMENT, HANDLE ELEMENT UPLOAD
    function ynfullsliderUploadImageComplete(evt) {
        // @TODO PUT FILE ID BACK TO FORM
        var json = JSON.decode(evt.target.responseText);

        if (json.status) {
            jQuery('.form-error').hide();
            jQuery('#file_id').val(json.file_id);
            jQuery('#image_path').val(json.file_path);
            jQuery('#ynfullslider_image_form_image').css('background-image', 'url(' + json.file_path + ')');
        } else {
            jQuery('.form-error').html(json.error).show();
        }
    }

    function ynfullsliderUploadImageFailed(evt) {
        // DO SOMETHING?
    }

    function ynfullsliderUploadImageCanceled(evt) {
        // DO SOMETHING?
    }

    // COLLECT DATA, POPULATE FORM FOR SAVING SLIDE
    function ynfullsliderSaveSlide()
    {
        var slide_elements = { };
        var elements_order = [];
        var animation_order = [];
        // GET ELEMENT AND INSERT TO ELEMENTS OBJECT
        if (!jQuery.isEmptyObject(ynfullsliderElements)) {
            for (var key in ynfullsliderElements) {
                if (ynfullsliderElements.hasOwnProperty(key)) {
                    var elementObject = ynfullsliderElements[key];
                    slide_elements[key] = { };
                    slide_elements[key]['type'] = elementObject.type;
                    slide_elements[key]['dimensions'] = elementObject.getDimensions();
                    slide_elements[key]['params'] = elementObject.params;
                }
            }
        }

        // GET ELEMENT ORDER
        jQuery('#slide_editor').find('.ynfullslider_element').each(function(index, el){
            elements_order.push(el.id);
        });

        // GET ANIMATION ITEM ORDER
        jQuery('#ynfullslider_animation_pane').find('.ynfullslider_slide_editor_header-list-item').each(function(index, el){
            animation_order.push(el.id);
        });

        // GET ANIMATION ITEM ORDER
        document.getElementById('elements_count').value = JSON.stringify(elementsCount);

        //SET FORM FIELD TO BE SAVED
        document.getElementById('slide_elements').value = JSON.stringify(slide_elements);
        document.getElementById('elements_order').value = JSON.stringify(elements_order);
        document.getElementById('animation_order').value = JSON.stringify(animation_order);
    }

    function ynfullsliderUpdateAnimationCount(){
        jQuery("#ynfullslider_animation_count").html(jQuery("#ynfullslider_animation_pane").find("li.ynfullslider_slide_editor_header-list-item").length);
    }

    function ynfullsliderVideoFormTypeSelect() {
        if (jQuery("input[name=video_type]:checked").val() == "youtube") {
            jQuery("#file-wrapper").hide();
            jQuery("input[name=youtube_iframe]").show();
        } else {
            jQuery('#file-wrapper').show();
            jQuery("input[name=youtube_iframe]").hide();
        }
    }

    function ynfullsliderPreviewSlide() {

        var previewButton = jQuery('#preview');
        var editorWrapper = jQuery("#ynfullslider_slide_editor_wrapper");
        var previewContainer = jQuery("#slide_preview_container");
        var previewWrapper = jQuery("#slide_preview_wrapper");

        if (preViewMode) {
            jQuery("#slide_preview_wrapper").hide();
            previewContainer.html('');
            editorWrapper.show();
            jQuery("#submit").show();
            previewButton.html('Preview');
            preViewMode = false;
            return;
        }

        preViewMode = true;
        // GET ELEMENT ORDER
        var elements_order = [];

        jQuery('#slide_editor').find('.ynfullslider_element').each(function(index, el){
            elements_order.push(el.id);
        });

        // GET ELEMENT AND INSERT TO ELEMENTS OBJECT
        var slide_elements = { };
        var len = elements_order.length;
        for (var i=0; i<len; i++) {
            var elementId = elements_order[i];
            if (ynfullsliderElements.hasOwnProperty(elementId)) {
                var elementObject = ynfullsliderElements[elementId];
                slide_elements[elementId] = { };
                slide_elements[elementId]['type'] = elementObject.type;
                slide_elements[elementId]['dimensions'] = elementObject.getDimensions();
                slide_elements[elementId]['params'] = elementObject.params;
            }
        }

        var slideId = <?php echo $slide->getIdentity() ?>;
        var previewSlideUrl = "<?php echo $this->url(array('module'=>'ynfullslider', 'controller' => 'slides', 'action'=>'preview-slide'), 'admin_default') ?>";

        // CALL THE AJAX AND UPDATE CONTENT
        previewContainer.load(previewSlideUrl, {layers: JSON.stringify(slide_elements), id: slideId}, function(){
            previewWrapper.css('min-height', editorWrapper.height() + 40);
            previewWrapper.show();
            jQuery("#submit").hide();
            editorWrapper.hide();
            previewButton.html('Done');
        });
    }

    function ynfullsliderGetFormText(type){
        return editFormTitle[type];
    }
    //Check popup Animation
    jQuery('.ynfullslider_slide_editor_header-btn-animation').click(function() {
        jQuery('.ynfullslider_slide_editor_header-list-items').toggle('fade',100);
        jQuery(this).toggleClass('active');
    });
</script>