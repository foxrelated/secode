/* $Id: core.js 2011-05-05 9:40:21Z SocialEngineAddOns $ */

en4.sitegroupalbum = {

  composer : false,

  getComposer : function(){ 
    if( !this.composer ){
      this.composer = new en4.sitegroupalbum.compose();
    }

    return this.composer;
  }

};



en4.sitegroupalbum.compose = new Class({

  Extends : en4.activity.compose.icompose,

  name : 'sitegroupphoto',

  active : false,

  options : {},

  frame : false,

  photo_id : false,

  initialize : function(element, options){ 
    if( !element ) element = $('activity-compose-sitegroupphoto');
    this.parent(element, options);
  },
  
  activate : function(){
    this.parent();
    this.element.style.display = '';
    $('activity-compose-sitegroupphoto-input').style.display = '';
    $('activity-compose-sitegroupphoto-loading').style.display = 'none';
    $('activity-compose-sitegroupphoto-preview').style.display = 'none';
    $('activity-form').addEvent('beforesubmit', this.checkSubmit.bind(this));
    this.active = true;

    // @todo this is a hack
    $('activity-post-submit').style.display = 'none';
  },

  deactivate : function(){
    if( !this.active ) return;
    this.active = false
    this.photo_id = false;
    if( this.frame ) this.frame.destroy();
    this.frame = false;
    $('activity-compose-sitegroupphoto-preview').empty();
    $('activity-compose-sitegroupphoto-input').style.display = '';
    this.element.style.display = 'none';
    $('activity-form').removeEvent('submit', this.checkSubmit.bind(this));;

    // @todo this is a hack
    $('activity-post-submit').style.display = 'block';
    $('activity-compose-sitegroupphoto-activate').style.display = '';
    $('activity-compose-link-activate').style.display = '';
  },

  process : function(){
    if( this.photo_id ) return;
    
    if( !this.frame ){
      this.frame = new IFrame({
        src : 'about:blank',
        name : 'albumSitegroupComposeFrame',
        styles : {
          display : 'none'
        }
      });
      this.frame.inject(this.element);
    }

    $('activity-compose-sitegroupphoto-input').style.display = 'none';
    $('activity-compose-sitegroupphoto-loading').style.display = '';
    $('activity-compose-sitegroupphoto-form').target = 'albumSitegroupComposeFrame';
    $('activity-compose-sitegroupphoto-form').submit();
  },

  processResponse : function(responseObject){
    if( this.photo_id ) return;
    
    (new Element('img', {
      src : responseObject.src,
      styles : {
        //'max-width' : '100px'
      }
    })).inject($('activity-compose-sitegroupphoto-preview'));
    $('activity-compose-sitegroupphoto-loading').style.display = 'none';
    $('activity-compose-sitegroupphoto-preview').style.display = '';
    this.photo_id = responseObject.photo_id;

    // @todo this is a hack
    $('activity-post-submit').style.display = 'block';
    $('activity-compose-sitegroupphoto-activate').style.display = 'none';
    $('activity-compose-link-activate').style.display = 'none';
  },

  checkSubmit : function(event)
  {
    if( this.active && this.photo_id )
    {
      //event.stop();
      $('activity-form').attachment_type.value = 'sitegroup_photo';
      $('activity-form').attachment_id.value = this.photo_id;
    }
  }
  
});
