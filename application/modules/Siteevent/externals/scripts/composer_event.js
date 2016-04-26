
/* $Id: composer_video.js 9572 2011-12-27 23:41:06Z john $ */



(function() { // START NAMESPACE
  var $ = 'id' in document ? document.id : window.$;


  Composer.Plugin.Siteevent = new Class({
    Extends: Composer.Plugin.Interface,
    name: 'siteevent',
    options: {
      title: 'Create Event',
      lang: {},
      loadJSFiles: [],
      // Options for the link preview request
      requestOptions: {},
    },
    initialize: function(options) {
      this.elements = new Hash(this.elements);
      this.params = new Hash(this.params);
      this.parent(options);
    },
    attach: function() {
      this.parent();
      this.makeActivator();
      
      var jsfile;
      while ((jsfile = this.options.loadJSFiles.shift())) {
        Asset.javascript(jsfile, {
          onLoad: function() {}})
      }
      this.options.loadJSFiles = [];
      return this;
    },
    detach: function() {
      this.parent();
      return this;
    },
    activate: function() {
      if (this.active)
        return;
    
        if(this.options.packageEnable) {
            window.location.href = this.options.requestOptions.url;
            return;
        } else {
            SmoothboxSEAO.open(this.options.requestOptions.url);
        }
      //  this.makeBody();
    },
    deactivate: function() {
      // clean video out if not attached
      if (!this.active)
        return;
      this.parent();
    }
  });



})(); // END NAMESPACE
