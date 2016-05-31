(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;



Composer.Plugin.Photo = new Class({

  Extends : Composer.Plugin.Interface,

  name : 'photo',
  options : {
    title : 'Add Photo',
    lang : {},
    requestOptions : false,
    fancyUploadEnabled : true,
    fancyUploadOptions : {}
  },

  initialize : function(options) {
    this.elements = new Hash(this.elements);
    this.params = new Hash(this.params);
    this.parent(options);
    this.scrollbar= false;
  },

  attach : function() {
    this.parent();
    this.makeActivator();
    return this;
  },

  detach : function() {
    this.parent();
    return this;
  },

  activate : function() {
    if( this.active ) return;
    this.parent();

    this.makeMenu();
    this.makeBody();
    if($$('.compose-container')[0].getElement('.overTxtLabel'))
    $$('.compose-container')[0].getElement('.overTxtLabel').innerHTML = en4.core.language.translate('Say something about this photo...');
    // Generate form
    var fullUrl = this.options.requestOptions.url;
    //var flashEnable = this.options.requestOptions.flashEnable;
    this.elements.form = new Element('form', {
      'id' : 'compose-photo-form',
      'class' : 'compose-form',
      'method' : 'post',
      'action' : fullUrl,
      'enctype' : 'multipart/form-data'
    }).inject(this.elements.body);
    
    this.elements.formInput = new Element('input', {
      'id' : 'compose-photo-form-input',
      'class' : 'compose-form-input',
      'type' : 'file',
      'name' : 'Filedata',
      'events' : {
        'change' : this.doRequest.bind(this)
      }
    }).inject(this.elements.form);

    // Try to init fancyupload
    if( this.options.fancyUploadEnabled && this.options.fancyUploadOptions ) {
        this.elements.formFancyContainer = new Element('div', {
          'styles' : {
          'display' : 'none',
          'visibility' : 'hidden'
          }
        }).inject(this.elements.body);

        this.elements.scrollbarBefore = new Element('div', {
            'id' : 'scrollbar_before',
            'class':'scrollbarArea'
        }).inject(this.elements.formFancyContainer);
        
        this.elements.scrollmain = new Element('div', {
            'id' : 'aaf-scroll-main'
        }).inject(this.elements.formFancyContainer);
        
        this.elements.scrollarea = new Element('div', {
            'id' : 'aaf-scroll-area',
            'styles' : {
            'overflow' : 'hidden',
            'width' : '400px'
          }
        }).inject(this.elements.scrollmain);
        this.elements.scrollcontent = new Element('div', {
            'id' : 'aaf-scroll-content'
        }).inject(this.elements.scrollarea);
        
        // This is the browse button
        this.elements.formFancyUl = new Element('ul', {
           'id' : 'aaf-demo-list',
            'class': 'demo-list',
             'styles' : {
            'float' : 'left'
          }
       }).inject(this.elements.scrollcontent);
       
       this.elements.formFancyUl1 = new Element('ul', {
          'id' : 'add-more',
            'styles' : {
            'float' : 'left'
          }
       }).inject(this.elements.scrollcontent);

        this.elements.scrollbarAfter = new Element('div', {
            'id' : 'scrollbar_after_1',
            'class':'scrollbarArea',
             'styles' : {
                'width' : $('compose-tray').clientWidth - 10
            }
        }).inject(this.elements.formFancyContainer);
       
        this.elements.formFancyliInner = new Element('li', {
            'class': 'sitealbum_addphotos_btn',
            'id': 'sitealbum_addphotos_btn' 
        }).inject(this.elements.formFancyUl1);

        this.elements.formFancyFile = new Element('a', {
            'href' : 'javascript:void(0);',
            'id' : 'demo-browse',
            'class' : 'buttonlink',
            'html' : en4.core.language.translate('Add Photos')
        }).inject(this.elements.formFancyliInner);
     
        // This is the status
        this.elements.formFancyStatus = new Element('div', {
          'html' : 
  '<div class="hide" id="demo-status">\n\
    <div class="demo-status-overall" id="demo-status-overall" style="display:none;">\n\
      <div class="overall-title" style="display:none;"></div>\n\
      <img src="'+en4.core.staticBaseUrl + 'application/modules/Seaocore/externals/images/loading.gif'+'" class="progress overall-progress" />\n\
    </div>\n\
    <div class="demo-status-current" id="demo-status-current" style="display:none;">\n\
      <div class="current-title" style="display:none;"></div>\n\
      <img src="'+en4.core.staticBaseUrl + 'application/modules/Seaocore/externals/images/loading.gif'+'" class="progress current-progress" />\n\
    </div>\n\
    <div class="current-text" style="display:none;"></div>\n\
  </div>'
        }).inject(this.elements.formFancyContainer);

        // This is the list
        this.elements.formFancyList = new Element('div', {
          'styles' : {
            'display' : 'none'
          }
        }).inject(this.elements.formFancyContainer);
         $('aaf-scroll-area').setStyle('width', $('compose-tray').clientWidth -10);
         $('aaf-scroll-content').setStyle('width', (parseInt($('aaf-scroll-content').getElements('li').length)) * 104);
        var self = this;
          (function() {
                self.scrollbar = new SEAOMooHorizontalScrollBar('aaf-scroll-main', 'aaf-scroll-area', {
             'arrows': false,
             'horizontalScroll': true,
             'horizontalScrollElement':'scrollbar_after_1',
             'horizontalScrollBefore':false,
             'horizontalScrollBeforeElement':'scrollbar_before'
            } );
            self.scrollbar.update();
           }).delay(500);
        var uploadCount = 0;
        var opts = $merge({
        policyFile : ('https:' == document.location.protocol ? 'https://' : 'http://')
            + document.location.host
            + en4.core.baseUrl + 'cross-domain',
        url : fullUrl,
        appendCookieData: true,
        multiple : true,
        typeFilter: {
          'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
        },
        target : this.elements.formFancyFile,
        //container : self.elements.body,
        // Events
        onLoad : function() {
          self.elements.formFancyContainer.setStyle('display', '');
          self.elements.formFancyContainer.setStyle('visibility', 'visible');
          self.elements.form.destroy();
          self.makeFormInputs();
          
          if(document.getElementsByName('attachment[photo_id]')[0] ) {
            document.getElementsByName('attachment[photo_id]')[0].value = '';
          }
          this.target.addEvents({
            click: function() {
                    return false;
            },
            mouseenter: function() {
                    this.addClass('hover');
            },
            mouseleave: function() {
                    this.removeClass('hover');
                    this.blur();
            },
            mousedown: function() {
                    this.focus();
            }
          });
        },
        onFail: function (error) {
            switch (error) {
                case 'flash':
                   self.options.requestOptions.flashEnable = false;
                   $$('.swiff-uploader-box').destroy();
                  // break;
            }
        },
        onFileStart: function () {
            uploadCount += 1;
        },
        onFileRemove: function (file) {
            uploadCount -= 1;
            file_id = file.photo_id;
            request = new Request.JSON({
                'format': 'json',
                'url': en4.core.baseUrl + 'sitealbum/photo/delete',
                'data': {
                    'photo_id': file_id,
                    'isAjax' : 1
                },
                'onSuccess': function (responseJSON) {
                    
                    $('aaf-scroll-content').setStyle('width', ($('aaf-scroll-content').getElements('li').length) * 104)
                    if(self.scrollbar)
                    self.scrollbar.update();
                    return false;
                }
            });
            request.send();
            var fileids = document.getElementsByName('attachment[photo_id]')[0];
            fileids.value = fileids.value.replace(file_id, "");
            if(document.getElementsByName('attachment[photo_id]')[0].value.trim() == '') {
                var demolist = document.getElementById("aaf-demo-list");
                demolist.style.display = "none";    
            }
        },
        onSelectSuccess : function() {
            this.start();
            selectedImgs = $('aaf-demo-list').getElements('li:not(.file-success,.validation-error,.autocompleter-choices,.sitealbum_addphotos_btn)');
            selectedImgs.each(function (item, index)
            {
                var demoStatus = new Element('div', {
                    'class': 'demo-status-progress',
                });
                demoStatus.inject(item);
                demoStatus.innerHTML = $('demo-status').innerHTML;
                demoStatus.getElements('.demo-status-overall')[0].style.display = 'block';
            });
            $('aaf-scroll-content').setStyle('width', ($('aaf-scroll-content').getElements('li').length) * 104);
            if(self.scrollbar)
             self.scrollbar.update();
            
        },
        onFileSuccess : function(file, response) {
          var json = new Hash(JSON.decode(response, true) || {});
          
          if (json.get('status') == '1') {
                self.SortablesInstance();
                var photo_id = json.get('photo_id');
                file.element.addClass('file-success scroll-content-item');
                file.element.setStyle('width', '100px');
                file.element.id = 'thumbs-photo-' + photo_id;
                file.element.getElement('.file-size').destroy();
                file.element.getElement('.file-name').destroy();
                var el = file.element.getElement('.file-remove');
                el.innerHTML="";
                el.inject(file.info, 'before');
                var mediaPhotoDetails = "<img id='media_photo_" + json.get('photo_id') + "' style=''src=" + json.get('src') + " />";
                file.info.set('html', mediaPhotoDetails);
               var fileids = document.getElementsByName('attachment[photo_id]')[0];
                fileids.value = fileids.value + json.get('photo_id') + " ";
                file.photo_id = json.get('photo_id');
            }  else {
                file.element.addClass('file-failed');
                file.info.set('html',  (json.get('error') ? (json.get('error')) : response));
            }
                
            file.element.getElements('.demo-status-progress .demo-status-overall').each(function(item,inex)
            {
                item.style.display = 'none';
            });
           $('aaf-scroll-content').setStyle('width', (parseInt($('aaf-scroll-content').getElements('li').length)) * 104);
        if(self.scrollbar)
             self.scrollbar.update();
        }
      }, this.options.fancyUploadOptions);

             try {
        this.elements.formFancyUpload = new FancyUpload2(this.elements.formFancyStatus, this.elements.formFancyUl, opts);
      } catch( e ) {
        //if( $type(console) ) console.log(e);
      }
   
    }

    /*
    this.elements.formSubmit = new Element('button', {
      'id' : 'compose-photo-form-submit',
      'class' : 'compose-form-submit',
      'html' : 'Attach',
      'events' : {
        'click' : function(e) {
          e.stop();
          this.doAttach();
        }.bind(this)
      }
    }).inject(this.elements.body);
    */
  },

  deactivate : function() {
    if( !this.active ) return;

    if(document.getElementsByName('attachment[photo_id]')[0]) {
       var fileids = document.getElementsByName('attachment[photo_id]')[0];
       if(fileids.value.trim()) {
          request = new Request.JSON({
                'format': 'json',
                'url': en4.core.baseUrl + 'sitealbum/index/cancel-photos',
                'data': {
                    'photo_ids': fileids.value,
                    'isAjax' : 1
                },
                'onSuccess': function (responseJSON) {
                }
            });
            request.send();
       } 
    }
    
            
    this.parent();
  },

  doRequest : function() {
    this.elements.iframe = new IFrame({
      'name' : 'composePhotoFrame',
      'src' : 'javascript:false;',
      'styles' : {
        'display' : 'none'
      },
      'events' : {
        'load' : function() {
          this.doProcessResponse(window._composePhotoResponse);
          window._composePhotoResponse = false;
        }.bind(this)
      }
    }).inject(this.elements.body);

    window._composePhotoResponse = false;
    this.elements.form.set('target', 'composePhotoFrame');

    // Submit and then destroy form
    this.elements.form.submit();
    this.elements.form.destroy();

    // Start loading screen
    this.makeLoading();
  },
  SortablesInstance :function() {
      var SortablesInstance;
        $$('demo-list > li').addClass('sortable');
        SortablesInstance = new Sortables($$('demo-list'), {
            clone: true,
            constrain: true,
            //handle: 'span',
            onComplete: function (e) {
                var ids = [];
                $$('demo-list > li').each(function (el) {
                    ids.push(el.get('id').match(/\d+/)[0]);
                });
                var vArray = ids;
                var photo_ids = '';
                for (i = 0; i < (vArray.length); i++) {
                    photo_ids = photo_ids + vArray[i] + " ";
                }
                fileids = document.getElementsByName('attachment[photo_id]')[0];
                fileids.value = photo_ids;
            }
        });
  },
  doProcessResponse : function(responseJSON) {
    // An error occurred
    if( ($type(responseJSON) != 'hash' && $type(responseJSON) != 'object') || $type(responseJSON.src) != 'string' || $type(parseInt(responseJSON.photo_id)) != 'number' ) {
      this.elements.loading.destroy();
     // this.elements.body.empty();
      if(responseJSON.error == 'Invalid data'){
        this.makeError(this._lang('The image you tried to upload exceeds the maximum file size.'), 'empty');
      }
      else{
        this.makeError(this._lang('Unable to upload photo.'), 'empty');
      }
      return;
      //throw "unable to upload image";
    }
    
    // Success
    this.params.set('rawParams', responseJSON);
    this.params.set('photo_id', responseJSON.photo_id);
    
    if(document.getElementsByName('attachment[photo_id]')[0]) {
        document.getElementsByName('attachment[photo_id]')[0].value = responseJSON.photo_id;
    }
    this.elements.preview = Asset.image(responseJSON.src, {
      'id' : 'compose-photo-preview-image',
      'class' : 'compose-preview-image',
      'onload' : this.doImageLoaded.bind(this)
    });
  },

  doImageLoaded : function() {
    //compose-photo-error
    if($('compose-photo-error')){
      $('compose-photo-error').destroy();
    }

    if(this.options.requestOptions.flashEnable === false) {
        if( this.elements.loading ) this.elements.loading.destroy();
        if( this.elements.formFancyContainer ) {
          this.elements.formFancyContainer.destroy();
        }
    } else {
          if( this.elements.formFancyContainer ) {
          this.elements.formFancyContainer.setStyle('display', 'block');
          this.elements.formFancyContainer.setStyle('visibility', 'visible');
        } 
    }
   
    this.elements.preview.erase('width');
    this.elements.preview.erase('height');
    this.elements.preview.inject(this.elements.body);
  },

  makeFormInputs : function() {
    this.ready();
    this.parent({
      'photo_id' : this.params.photo_id
    });
  }
});



})(); // END NAMESPACE
