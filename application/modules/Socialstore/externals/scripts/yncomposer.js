(function() { // START NAMESPACE
	
var $ = 'id' in document ? document.id : window.$;

yncomposer = new Class({

  Extends : Composer,
  
  initialize : function(element, options) {
	    this.setOptions(options);
	    this.elements = new Hash(this.elements);
	    this.plugins = new Hash(this.plugins);
	    
	    this.elements.textarea = $(element);
	    this.elements.textarea.store('Composer');

	    this.attach();
	    this.getTray();
	    this.getMenu();

	    this.pluginReady = false;
	    console.log('fuck');
  },
  
  makeLoading : function(action) {
	    if( !this.elements.loading ) {
	      if( action == 'empty' ) {
	    	  console.log('vo1');
	        //this.elements.body.empty();
	      } else if( action == 'hide' ) {
	        this.elements.body.getChildren().each(function(element){ element.setStyle('display', 'none')});
	      } else if( action == 'invisible' ) {
	        this.elements.body.getChildren().each(function(element){ element.setStyle('height', '0px').setStyle('visibility', 'hidden')});
	      }
	      this.elements.loading = new Element('div', {
	        'id' : 'compose-' + this.getName() + '-loading',
	        'class' : 'compose-loading'
	      }).inject(this.elements.body);

	      var image = this.elements.loadingImage || (new Element('img', {
	        'id' : 'compose-' + this.getName() + '-loading-image',
	        'class' : 'compose-loading-image'
	      }));
	      image.inject(this.elements.loading);

	      new Element('span', {
	        'html' : this._lang('Loading...')
	      }).inject(this.elements.loading);
	    }
	  }
});
})();