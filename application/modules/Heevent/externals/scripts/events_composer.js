/* $Id: events_composer.js 19.10.13 08:20 jungar $ */



Wall.Composer.Plugin.Heevent = new Class({

  Extends:Wall.Composer.Plugin.Interface,

  name:'heevents',
  els: {
    createForm: null,
    createFormBg: null
  },
  options:{
    title:'Add Event',
    lang:{},
    max_option:10,
    is_timeline:0
  },
  defaultFormValues: {},
  $container:null,


  initialize:function (options) {
//    console.info('initialize');
    this.params = new Hash(this.params);
    this.parent(options);
    this.initEls();
    this._bindEvents();
  },
  initEls: function(){
//    console.info('initEls');
    var cf = this.els.createForm = $('heevent-composer-create-form');
    cf.getElements('#heevent-create-delete-cover, #heevent-create-upload-cover').hide();
    cf.getElement('#heevent_cover .cover-wrapper').grab($('category_id-wrapper'));
    this.defaultFormValues = _hem.getFormValues(cf);
    this.els.createFormBg = $('background_create_form');
    this.els.cancelEvent = cf.getElement('#cancel');
    this.els.submitBtn = cf.getElement('#submit');
  },
  attach:function () {
//    console.info('attach');
    this.parent();
    this.makeActivator();
    return this;
  },

  detach:function () {
//    console.info('detach');
    this.parent();
    return this;
  },
  _bindEvents: function(){
    var self = this;
    var els = this.els;
    var detachCB = function(){
      self.close();
    };
    els.cancelEvent.set('onclick', '');
    els.createFormBg.addEvent('click', detachCB);
    els.cancelEvent.addEvent('click', detachCB);

    els.submitBtn.addEvent('click', function (e) {
      self.eventsSubmitAction();
    });
//    els.eventOptions.addEvent('click', function(){
//      $('options_event').show();
//    });
//    els.eventOptions.addEvent('mouseenter', function(){
//      this._mouseenter = true;
//    });
//    els.eventOptions.addEvent('mouseleave', function(){
//      if($('options_event').getStyle('display') != 'none')
//        this.focus();
//      this._mouseenter = false;
//    });
//    els.eventOptions.addEvent('blur', function(){
//        if(!this._mouseenter)
//          $('options_event').hide();
//    });
  },
  activate:function (no_focus) {
//    console.info('activate');
    this.is_composer_opened = true;
    var self = this;
    $('background_create_form').setStyle('display', 'block');
    $('heevent-composer-create-form').setStyle('display', 'block');
//    $$('.wallFeed .wallTextareaContainer, .wallFeed .wall-stream-header, .wallFeed .submitMenu').hide();
    setTimeout(function(){
      $('heevent-composer-create-form').setStyle('transform', 'translate(0,0)');
      var cf = self.els.createForm;
      var desc = cf.getElement('#description');
      var title = cf.getElement('#host');
      var styles = [
        'border-width:' + title.getStyle('border-width') + ' !important;',
        'border-style:' + title.getStyle('border-style') + ' !important;',
        'background-color:' + title.getStyle('background-color') + ' !important;',
        'border-color:' + title.getStyle('border-color') + ' !important;'
      ];
      desc.set('style', styles.join(' '));
      cf.setStyle('width', $$('.wallFormComposer')[0].getWidth() + 'px');
    }, 10);
  },

  showCreateForm:function () {
//    console.info('showCreateForm');
    var myVerticalSlide = new Fx.Slide('heevent-composer-create-form');
    event.stop();
    myVerticalSlide.slideIn();

  },
  showFormErrors: function(errors){

    for(var param in errors){
      var paramErrors = errors[param];
      var paramEl = $$(['#', param, '-element input, ', '#', param, '-element textarea'].join(''));
//      var errorCont = new Element('i');
//      errorCont.addClass('errors hei hei-warning-sign');
//      var error;
//      if('string' == typeof paramErrors){
//        paramErrors = {'code': paramErrors};
//      }
//      for(var errCode in paramErrors){
//        if(!paramErrors.hasOwnProperty(errCode))
//          continue;
//        var errorMessage = paramErrors[errCode];
//        errorCont.grab(error);
//      }
//      if(paramEl)
//        paramEl.grab(errorCont);
      paramEl.addClass('error');
    }
    (function(form){
      setTimeout(function(){
        form.getElements('.error').removeClass('error');
      }, 4000);
    })(this.els.createForm);
  },

  eventsSubmitAction:function () {
      if(window.submitCheck==1){
          return;
      }
      window.submitCheck = 1;
//    console.info('eventsSubmitAction');
    var self = this;
    var params = _hem.getFormValues(this.els.createForm);
    params.format = 'json';
    params.composer = true;
//    console.log(params);

    Wall.request(this.els.createForm.get('action'), params, function (obj) {

      if(obj.formErrors){
        self.showFormErrors(obj.formErrors);
        window.submitCheck = 0;
        return;
      }
      if (self.options.is_timeline && false) {
        var feed = timeline.feed.object.get();
        var data;
        if (timeline.feed.object.setLasts(obj.last_date, obj.last_id)) {
          feed.checkEmptyFeed();
          data = $merge(feed.params, {
              'minid': this.options.last_id,
              'checkUpdate': true
          });
          feed.feed.getElements('.container-get-last').destroy();
          feed.loadFeed(data, 'top', function (){
              feed.checkActive = false;
          });
        }
      } else {
        var wall = Wall.feeds.get(self.getComposer().options.feed_uid);
        wall.checkEmptyFeed();
        data = $merge(wall.params, {
            'minid': obj.last_id,
            'checkUpdate': false
        });
        wall.feed.getElements('.container-get-last').destroy();
        wall.loadFeed(data, 'top', function (){
          wall.checkActive = false;
        });

      }
        window.submitCheck = 0;
      self.close();
    });
  },

  open:function () {
//    console.info('open');
    this.resetForm();

    if (this.is_composer_opened) {
      return;
    }
    this.is_composer_opened = true;
  },

  close:function () {
//    console.info('close');
    if (!this.is_composer_opened) {
      return;
    }

    this.els.createForm.set('style', '');
    this.els.createForm.setStyle('display', 'block');
    (function(cf){
      setTimeout(function(){cf.set('style', '');}, 500);
    })(this.els.createForm);
    this.els.createFormBg.set('style', '');
//    $$('.wallFeed .wallTextareaContainer, .wallFeed .wall-stream-header, .wallFeed .submitMenu').show();
    var self = this;
    this.resetForm();
    this.deactivate();
    self.getComposer().close();
  },

  resetForm:function () {
    var values = this.defaultFormValues;
    var cf = this.els.createForm;
    cf.getElements('input[type="text"]').value = '';
    for(var param in values){
      if(!values.hasOwnProperty(param))
        continue;
      var value = values[param];
      var elem = cf.getElement('#' + param);
      if(elem){
        if(elem.type == 'checkbox'){
          elem.checked = !!value;
        }
        elem.value = value;
      } else {
        elem = cf.getElement(['#', param, '-', value].join(''));
        if(elem && elem.type == 'radio'){
          elem.checked = !!value;
          elem.set('value', value);
        }
      }
    }

    cf.getElements('.datepicker_container input + input').value = '';
    _hem.fireEvent(cf.getElement('#category_id option'), 'click');
    _hem.fireEvent(cf.getElement('#category_id'), 'change');

  },

  deactivate:function () {
//    console.info('deactivate');
    if (!this.active) return;
    this.parent();

    this.request = false;
  },

  makeActivator:function () {
//    console.info('makeActivator');
    if (!this.elements.activator) {

      if ($$('.wall-compose-heevent-activator').length == 0) { //
        this.elements.activator = new Element('a', {
          'class':'wall-compose-activator wall-compose-heevent-activator wall_blurlink hei hei-calendar',
          'href':'javascript:void(0);',
          'html':'&nbsp;',
          'title':this._lang(this.options.title),
          'events':{
            'click':this.activate.bind(this)
          }
        }).inject(this.getComposer().getMenu());

        new Wall.Tips(this.elements.activator);
        new Wall.BlurLink(this.elements.activator);

      }
    }
  }
});