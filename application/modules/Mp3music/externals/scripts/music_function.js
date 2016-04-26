function openPage(url, vHeight, vWidth)
{ 
     //vWidth = 885;
     //winDef = 'status=yes,resizable=no,scrollbars=yes,fullscreen=no,titlebar=no,width='.concat(vWidth).concat(',');
     //winDef = winDef.concat('left=').concat((screen.width - vWidth)/2);
     
     //winmedia= window.open(url,'music_popup', winDef);
     window.location  = url;
     return false;
}

function openPageOpener(url)
{
	opener= window.open(url);
	opener.focus();
	return false;
}
function getKeyDown(evt)
{
    evt = evt || window.event;
		var charCode = evt.keyCode || evt.which;
		if (charCode == 13) 
		{
			var key_search = $("title").value;
	    key_search = key_search.replace(/^\s+|\s+$/g,'');
	    if (key_search == "")
	    {
	        return false;
	    }
	    else
	    {
	        $('frm_search').submit();
	        return true;
	    }
		}

}
function roundNumber(num, dec)
{
        var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
        return result;
}
function show_price(item_id,type,price,currency,album)
{
    price = roundNumber(price,2);
    if(album == 1)
        document.getElementById(type+'_price1_id_'+item_id).innerHTML = price + " " + currency; 
    else
        document.getElementById(type+'_price_id_'+item_id).innerHTML = price + " " + currency; 
}
function hide_price(type,item_id,currency,album)
{
     if(album == 1)    
        document.getElementById(type+'_price1_id_'+item_id).innerHTML = ''; 
     else
         document.getElementById(type+'_price_id_'+item_id).innerHTML = '';  
}
function addtocartfromblock(item_id,type)
{
    
}
function addtocartPlay(item_id,type,album)
{
     var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'mp3music/cart/add-to-cart',
            'data' : {
                'item_id' : item_id,            
                'type' : type                                   
            },
            'onComplete':function(responseObject)
            {  
                if( typeof(responseObject)!="object")
                {
                     alert('ERR');
                }
                else
                {                                
                    if(responseObject.result == 1)
                    {
                        document.getElementById('button_buy').style = "background-color: #CCC; border: none;"; 
                    }
                    alert(responseObject.message);
                }
            }
        });
        request.send();
}
function addtocart(item_id,type,album)
{
     var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'mp3music/cart/add-to-cart',
            'data' : {
                'item_id' : item_id,            
                'type' : type                                   
            },
            'onComplete':function(responseObject)
            {  
                if( typeof(responseObject)!="object")
                {
                     alert('ERR');
                }
                else
                {                                
                    if(responseObject.result == 1)
                    {
                        if(type == 'song')
                        {
							if(album == 'player')
							{
								document.getElementById(type + '_id_cart_'+ item_id).innerHTML =  '<a class = "yn-add-cart ynmp3music-disable" href="javascript:alert(\''+ en4.core.language.translate("The ") + type + en4.core.language.translate(" already in your cartshop or in download list. Please check again!") + '\')"> <img src="./application/modules/Mp3music/externals/images/audio.png" /></a>';
							}
							else
							{
                              	document.getElementById(type + '_id_cart_'+ item_id).innerHTML =  '<a id="'+type+'_id_cart_'+item_id+'" href="javascript:alert(\''+ en4.core.language.translate("The ") + type + en4.core.language.translate(" already in your cartshop or in download list. Please check again!") + '\')"> <img src="./application/modules/Mp3music/externals/images/addtocart_ic_ds.jpg" /></a>';
							}
                        }
                        else if(album == 'featured')
                        {
                              document.getElementById('featuredalbum_id_cart_'+ item_id).innerHTML =  '<a id="featuredalbum_id_cart_'+item_id+'" href="javascript:alert(\'' + en4.core.language.translate("The ") + type + en4.core.language.translate(" already in your cartshop or in download list. Please check again!") + '\')"> <img src="./application/modules/Mp3music/externals/images/addtocart_ic_ds.jpg" /></a>';
                              if(document.getElementById(type + '_id_cart_'+ item_id) !== null && document.getElementById(type + '_id_cart_'+ item_id) !== undefined)
                              	document.getElementById(type + '_id_cart_'+ item_id).innerHTML =  '<a style="position: absolute;" id="'+type+'_id_cart_'+item_id+'" href="javascript:alert(\'' + en4.core.language.translate("The ") + type + en4.core.language.translate(" already in your cartshop or in download list. Please check again!") + '\')"> <img src="./application/modules/Mp3music/externals/images/addtocart_ic_ds.jpg" /></a>';
                              if(document.getElementById(type + '_newid_cart_'+ item_id) !== null && document.getElementById(type + '_newid_cart_'+ item_id) !== undefined)
                             	document.getElementById(type + '_newid_cart_'+ item_id).innerHTML =  '<a style="position: absolute;" id="'+type+'_newid_cart_'+item_id+'" href="javascript:alert(\''+ en4.core.language.translate("The ") + type + en4.core.language.translate(" already in your cartshop or in download list. Please check again!") + '\')"> <img src="./application/modules/Mp3music/externals/images/addtocart_ic_ds.jpg" /></a>';
                        }
                        else if(album == 1)
                        {
                             document.getElementById(type + '_id_cart1_'+ item_id).innerHTML =  '<a id="'+type+'_id_cart_'+item_id+'" href="javascript:alert(\''+ en4.core.language.translate("The ") + type + en4.core.language.translate(" already in your cartshop or in download list. Please check again!") + '\')"> <img src="./application/modules/Mp3music/externals/images/addtocart_ic_ds.jpg" /></a>';
                        }
                        else
                        {
                        	if(document.getElementById(type + '_newid_cart_'+ item_id) !== null && document.getElementById(type + '_newid_cart_'+ item_id) !== undefined)
                             	document.getElementById(type + '_newid_cart_'+ item_id).innerHTML =  '<a style="position: absolute;" id="'+type+'_newid_cart_'+item_id+'" href="javascript:alert(\''+ en4.core.language.translate("The ") + type + en4.core.language.translate(" already in your cartshop or in download list. Please check again!") + '\')"> <img src="./application/modules/Mp3music/externals/images/addtocart_ic_ds.jpg" /></a>';
                            if(document.getElementById(type + '_id_cart_'+ item_id) !== null && document.getElementById(type + '_id_cart_'+ item_id) !== undefined)
                             	document.getElementById(type + '_id_cart_'+ item_id).innerHTML =  '<a style="position: absolute;" id="'+type+'_id_cart_'+item_id+'" href="javascript:alert(\''+ en4.core.language.translate("The ") + type + en4.core.language.translate(" already in your cartshop or in download list. Please check again!") + '\')"> <img src="./application/modules/Mp3music/externals/images/addtocart_ic_ds.jpg" /></a>';   
                            if(document.getElementById('featuredalbum_id_cart_'+ item_id) !== null && document.getElementById('featuredalbum_id_cart_'+ item_id) !== undefined)
                            	document.getElementById('featuredalbum_id_cart_'+ item_id).innerHTML =  '<a id="featuredalbum_id_cart_'+item_id+'" href="javascript:alert(\'' + en4.core.language.translate("The ") + type + en4.core.language.translate(" already in your cartshop or in download list. Please check again!") + '\')"> <img src="./application/modules/Mp3music/externals/images/addtocart_ic_ds.jpg" /></a>';
                        }
                    }
                    alert(responseObject.message);
                }
            }
        });
        request.send();
}
function addalbumtocart(item_id,type,album)
{
     var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'mp3music/cart/add-to-cart',
            'data' : {
                'item_id' : item_id,            
                'type' : type                                   
            },
            'onComplete':function(responseObject)
            {  
                if( typeof(responseObject)!="object")
                {
                     alert('ERR');
                }
                else
                {                                
                    if(responseObject.result == 1)
                    { 
                        document.getElementById(type + '_id_cart_'+ item_id).innerHTML =  '<a id="'+type+'_id_cart_'+item_id+'" href="javascript:alert(\'The '+type+' already in your cartshop or in download list.\n Please check again\')"> <button style="width: 120px;  background-color: #CCC;border: medium none;" type="button">Buy Album</button></a>';
                    }
                    alert(responseObject.message);
                }
            }
        });
        request.send();
}
function removecartitem(item_id,type,index)
{
    var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'mp3music/cart/remove-cart',
            'data' : {
                'item_id' : item_id,            
                'type' : type                                   
            },
            'onComplete':function(responseObject)
            {  
                if( typeof(responseObject)!="object")
                {
                     alert('ERR');
                }
                else
                {   
                    if(responseObject.result == 1)
                    {
                         //var tr = document.getElementById('car_id_item_'+ index);
                         //var table = document.getElementById('table');
                         //table.removeChild(tr);
                         //document.getElementById('total_product_amount_in').innerHTML =  responseObject.message + " ";
                    	window.location.reload(true);

                    }
                         
                    if(responseObject.result == 2)     
                        document.getElementById('cart_list_info').innerHTML =  responseObject.message;
                }
            }
        });
        request.send();  
}
 function loadanblumitem(item_id)
{
    var l = document.getElementById('album_item_download_'+item_id);
    l.innerHTML = '<img src="./application/modules/Mp3music/externals/images/ajax-loader.gif"/>';
     var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'mp3music/cart/loadalbumitem',
            'data' : {
                'item_id' : item_id                                
            },
            'onComplete':function(responseObject)
            {  
                if( typeof(responseObject)!="object")
                {
                     alert('ERR');
                }
                else
                {   
                    document.getElementById('album_item_download_'+item_id).innerHTML =  responseObject.html;    
                }
            }
        });
        request.send();
}
function loadMessageFromRequest(id,url)
{
    var l = document.getElementById('message_request_'+id);
    l.innerHTML = '<img src="./application/modules/Mp3music/externals/images/ajax-loader.gif"/>';
    var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'mp3music/cart/load-message',
            'data' : {
                'item_id' : id 
            },
            'onComplete':function(responseObject)
            {  
                if( typeof(responseObject)!="object")
                {
                     alert('ERR');
                }
                else
                { 
                	
                      document.getElementById('message_request_'+id).innerHTML =  responseObject.html;
                }
        	
            }
        });
        request.send();  
}
function close(id)
{
    document.getElementById('message_request_'+id).innerHTML =  '';
}
function requestMoney()
{
    var currentmoney = document.getElementById('txtrequest_money').value; 
    var reason = document.getElementById('textarea_request').value; 
    var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'mp3music/account/requestmoney',
            'data' : {
                'currentmoney' : currentmoney, 
                'reason' : reason
            },
            'onComplete':function(responseObject)
            {  
                if( typeof(responseObject)!="object")
                {
                     alert('ERR');
                }
                else
                {   
                      document.getElementById('request').innerHTML = responseObject.html;
                      if(responseObject.current_request_money)
                      {
                          parent.document.getElementById('current_request_money').innerHTML = responseObject.current_request_money;
                          parent.document.getElementById('current_money_money').innerHTML = responseObject.current_money_money;
                          parent.Smoothbox.close();
                      }
                }
            }
        });
        request.send();   
}
function showAlbumArt(slideshowURL) {
        TINY.box.show({iframe:slideshowURL,width:738,height:588,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}});
        return false;
}

(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;

if( !('en4' in window) ) {
  en4 = {};
}
if( !('music' in en4) ) {
  en4.music = {};
}

en4.music.playlistAbstract = new Class({
  
  Implements: [Options, Events],

  options : {
    mode : 'linear',
    repeat : false,
    sliderWidth : 385, // pixels wide
    containerWidth : 115 // pixels of the "chrome" surrounding the slider
  },

  container : false,

  songs : [],
  tallied : {},
  slider : null,
  sound : null,
  selected: 0,
 
 container: null,
 slider_volume:    null,
 slider_w_Volume:  100, // pixels wide
  _isAttached : false,


  /**
   * Initialize
   */
  initialize: function(el, options) {

    this.setOptions(options);

    // attach container to this player
    this.container = $(el);
    this.container.getElement('div.music_player_scrub_downloaded').hide();
    this.songs = this.container.getElements('a.music_player_tracks_url');

    // attach
    this._attachEvents();
    this._attachScrub();
    this._attachScrubVolume();
    this._isAttached = true;

    // set volume
    this._updateVolume();

    // set current playing title
    if( this.songs.length ) {
      //this.create(0).load();
      if( this.songs[0] ) {
        this.setTitle(this.songs[0].getParent().get('title'));
      }
    }
  },

  /**
   * getSoundManager
   */
  getSoundManager : function() {
    return en4.music.player.getSoundManager();
  },

  /**
   * play
   */
  play : function(index) {
    if( !$type(index) ) {
      index = this.selected;
    }
    if( index >= this.songs.length || index < 0 || !$type(this.songs[index]) ) {
      index = 0;
    }
    var soundId = this._getSoundId(index);
    
    // Just toggle play state
    if( this.sound && this.sound.sID == soundId && this.sound.playState ) {
      this.sound.togglePause();
      return this;
    }

    // Stop all sounds
    this.getSoundManager().stopAll();
    
    // Create new
    this.selected = index;
    this.setTitle(this.songs[index].getParent().get('title'));
    this.sound = this.create(index);
    if( this.sound ) {
      this.sound.setPosition(0);
      this.sound.play();
    }

    return this;
  },
  
  /**
   * create
   */
  create : function(index) {
    if( index >= this.songs.length || index < 0 || !$type(this.songs[index]) ) {
      return false;
    }
    var soundId = this._getSoundId(index);
    if( this.getSoundManager().getSoundById(soundId) ) {
      return this.getSoundManager().getSoundById(soundId);
    }
    var url_song = "";
    if(unescape(this.songs[index].id) == "")
        url_song = this.songs[index].href;
    else
        url_song = unescape(this.songs[index].id);
    var soundOptions = {
      id:             soundId,
      url:            url_song, 
      autoload:       true,
      useVideo:       false,
      volume:         en4.music.player.getVolume(),
      onload:         function() { this.fireEvent('soundmanager_onload'); }.bind(this), //function() { this.setDuration(this); }.bind(this),
      whileloading:   function() { this.fireEvent('soundmanager_whileloading'); }.bind(this), //function() { this.setDuration(this); }.bind(this),
      whileplaying:   function() { this.fireEvent('soundmanager_whileplaying'); }.bind(this), //function() { this.setElapsed(this); this.setScrub(this); }.bind(this),
      onplay:         function() { this.fireEvent('soundmanager_onplay'); }.bind(this), //function() { this.container.getElement('.music_player_button_play').addClass('music_player_button_pause'); this.logPlay(this); }.bind(this),
      onresume:       function() { this.fireEvent('soundmanager_onresume'); }.bind(this), //function() { this.container.getElement('.music_player_button_play').addClass('music_player_button_pause'); }.bind(this),
      onpause:        function() { this.fireEvent('soundmanager_onpause'); }.bind(this), //function() { this.container.getElement('.music_player_button_play').removeClass('music_player_button_pause'); }.bind(this),
      onstop:         function() { this.fireEvent('soundmanager_onstop'); }.bind(this), //function() { this.container.getElement('.music_player_button_play').removeClass('music_player_button_pause'); }.bind(this),
      onfinish:       function() { this.fireEvent('soundmanager_onfinish'); }.bind(this), //function() { this.container.getElement('.music_player_button_play').removeClass('music_player_button_pause'); this.playNext(); }.bind(this),
      onbeforefinish: function(index) { this.fireEvent('soundmanager_onbeforefinish', [index]); }.bind(this) //function() { var nextsong = this.create(song_id+1); nextsong.load(); }
    };
    
    if( soundId.replace(/\?[^?]+?$/, '').match(/\.(mp3)$/) ) {
      this.getSoundManager().createSound(soundOptions);
    } else {
      this.getSoundManager().createSound(soundOptions); 
    }

    // Set volume this way to initialize display
    this._updateVolume();

    return this.getSoundManager().getSoundById(soundId);
  },

  /**
   * logPlay
   */
  logPlay : function() {
    var song_id = this.songs[this.selected].rel;
    var playlist_id = this.container.getElement('ul.music_player_tracks').className.split('_');
    playlist_id = playlist_id[playlist_id.length-1];

    // Change classes
    this.songs.each(function(song, index) {
      if( index == this.selected ) {
        song.getParent('li').addClass('song_playing');
      } else {
        song.getParent('li').removeClass('song_playing');
      }
    }.bind(this));

    // Tally song
    if( !this.tallied[song_id] ) {
      this.tallied[song_id] = true;
      new Request.JSON({
        url: en4.core.baseUrl + 'mp3music/album/song-play-tally',     
        noCache: true,
        data: {
          format: 'json',
          song_id: song_id,
          playlist_id: playlist_id
        },
        onSuccess: function(responseJSON) {
          if( responseJSON &&
              $type(responseJSON) == 'object' &&
              'song' in responseJSON &&
              'play_count' in responseJSON.song ) {
            this.songs[this.selected].getParent('li')
              .getElement('.music_player_tracks_plays span')
              .set('text', responseJSON.play_count);
          }
        }.bind(this)
      }).send();
    }
  },

  /**
   * seekTo
   *
   * called by Slider after moving position
   */
  seekTo : function(pos) {
    if( !this.sound ) {
      return;
    }
    
    var ms_total = this.sound.durationEstimate;
    var ms_dest  = Math.round(ms_total * (pos / this.options.sliderWidth));
    var diff     = Math.abs(ms_dest - this.sound.position);
    if( this.slider.element.hasClass('mousedown') || diff > 2000 ) {
      this.sound.setPosition(ms_dest);
    }
  },

  /**
   * playNext
   */
  playNext : function() {
    if( this.songs[this.selected + 1] ) {
      this.play( this.selected + 1 );
    } else if( this.options.repeat ) {
      this.play(0);
    } else {
      this.slider.set(0);
    }
  },

  /**
   * playPrev
   */
  playPrev : function() {
    if( this.songs.length == 1 ) {
      this.seekTo(0);
    } else if( this.selected == 0 && this.options.repeat ) {
      this.play(this.songs.length - 1);
    } else if( this.selected == 0 ) {
      this.seekTo(0);
    } else if( this.selected > 0 ) {
      this.play(this.selected - 1);
    }
  },

  /**
   * launch
   */
  launch : function() {
    var href = this.container.getElement('a.music_player_button_launch').href;
    window.open(href, 'player',
      'status=0,' +
      'toolbar=0,' +
      'location=0,' +
      'menubar=0,' +
      'directories=0,' +
      'scrollbars=0,' +
      'resizable=0,' +
      'height=500,' +
      'width=600');
  },

  /**
   * setVolume
   */
  setVolume: function(volume) {
   en4.music.player.setVolume(volume);
    this._updateVolume();
  },         

  /**
   * toggleMute
   */
  toggleMute: function(flag) {
    en4.music.player.toggleMute(flag);
    this._updateVolume();
  },

  /**
   * getDuration
   */
  getDuration : function() {
    this.container.getElement('div.music_player_time_total').get('text');
  },

  /**
   * getElapsed
   */
  getElapsed : function() {
    this.container.getElement('div.music_player_time_elapsed').get('text');
  },

  /**
   * getTitle
   */
  getTitle : function() {
    this.container.getElement('div.music_player_trackname').get('text');
  },

  /**
   * setTitle
   */
  setTitle : function(sText) {
    this.container.getElement('div.music_player_trackname').set('text', sText);
  },

  /**
   * setDownloaded
   */
  setDownloaded: function(pl) {
    var self = pl;
    if (self.sound) {
      var percent = 100;
      if (self.sound.isBuffering)
        percent = (self.sound.position/self.sound.durationEstimate)*100;
      self.getElement('div.music_player_scrub_downloaded').setStyle('width', percent);
    }
  },
  // Utility

  _getSoundId : function(index) {
    if( !$type(this.songs[index]) ) {
      return false;
    }
    var url_song = "";
    if(unescape(this.songs[index].id) == "")
        url_song = this.songs[index].href;
    else
        url_song = unescape(this.songs[index].id);
    return '_song_' +
      this.container.getParent('.music_player_wrapper').id + '_' +
      url_song; //.replace(/[^A-Za-z0-9./\\]+/ig, '');
  },

  _attachEvents : function() {
    if( this._isAttached ) {
      return;
    }
    
    // play
    this.container.getElement('.music_player_button_play').addEvent('click', function(event) {
      this.play();
      event.stop();
      return false;
    }.bind(this));

    // previous
    this.container.getElement('.music_player_button_prev').addEvent('click', function(event) {
      this.playPrev();
      event.stop();
      return false;
    }.bind(this));

    // next
    this.container.getElement('.music_player_button_next').addEvent('click', function(event) {
      this.playNext();
      event.stop();
      return false;
    }.bind(this));
    
    // mute
    this.container.getElement('.music_player_controls_volume_toggle').addEvent('click', function(event) {
      en4.music.player.toggleMute();
      this._updateVolume();
      event.stop();
      return false;
    }.bind(this));
    
    // tracks
    this.container.getElements('ul.music_player_tracks li').addEvent('click', function(event) {
      var el;
      if( !event ||
          !('target' in event) ||
          !(el = $(event.target)) ||
          el.hasClass('smoothbox') ) {
        return true;
      }

      if( el.get('tag').toLowerCase() != 'li' ) {
        el = el.getParent('li');
      }

      if( this.getSoundManager().supported() ) {
        this.play(el.getAllPrevious().length);
        if( $type(event) ) {
          event.stop();
        }
        return false;
      } else {
        return true;
      }
    }.bind(this));

    // sound manager events
    this.addEvents({
      soundmanager_onload : this._updateDuration.bind(this),
      soundmanager_whileloading : this._updateDuration.bind(this),
      soundmanager_whileplaying : this._updateScrub.bind(this),
      soundmanager_onplay : function() {
        this.container.getElement('.music_player_button_play').addClass('music_player_button_pause');
        this.logPlay(this);
      }.bind(this),
      soundmanager_onresume : function() {
        this.container.getElement('.music_player_button_play').addClass('music_player_button_pause');
      }.bind(this),
      soundmanager_onpause : function() {
        this.container.getElement('.music_player_button_play').removeClass('music_player_button_pause');
      }.bind(this),
      soundmanager_onstop : function() {
        this.container.getElement('.music_player_button_play').removeClass('music_player_button_pause');
        this.slider.set(0);
        if( this.sound ) {
          this.container.getElement('div.music_player_time_elapsed').set('text', '0:00');
        }
      }.bind(this),
      soundmanager_onfinish : function() {
        this.container.getElement('.music_player_button_play').removeClass('music_player_button_pause');
        this.playNext();
      }.bind(this),
      soundmanager_onbeforefinish : function(song_id) {
        var nextsong = this.create(song_id+1);
        nextsong.load();
      }.bind(this)
    });
  },
  _attachScrubVolume : function() {   
    // attach scrub volume
    var scrubBarVolume  = this.container.getElement('div.music_player_scrub_volume');
    if(scrubBarVolume)
    {
        scrubBarVolume.setStyle('width', this.slider_w_Volume);
        scrubBarVolume.addEvent('mousedown', function(){
            this.addClass('mousedown');
        });
        scrubBarVolume.addEvent('mouseup', function(){
            this.removeClass('mousedown');
        });
        this.slider_volume = new Slider(
            this.container.getElement('div.music_player_scrub_volume'),
            this.container.getElement('div.music_player_scrub_cursor_volume'), {
                snap: false,
                offset: 0,
                range: [0,this.slider_w_Volume],
                wheel: true,
                steps: this.slider_w_Volume,
                initialStep: this.slider_w_Volume,
                onComplete: function(step) {
                    en4.music.player.setVolume(step);
                    this._updateVolume;
                }
            }
            );
    }
  },
  _attachScrub : function() {
    if( this._isAttached ) {
      return;
    }
    // attach scrub bar
    var scrubBar = this.container.getElement('div.music_player_scrub');
    var chrome = this.container.getElement('.music_player_top').measure(function() {
      return this.getDimensions();
    });
    var img = this.container.getElement('.music_player_art').measure(function() {
      return this.getDimensions();
    });
    if( this.container.getElement('.music_player_art').isDisplayed() ) {
      this.options.sliderWidth = chrome.width - img.width ;
    } else {
      this.options.sliderWidth = chrome.width;
    }
    scrubBar.setStyle('width', this.options.sliderWidth + 'px');
    scrubBar.addEvent('mousedown', function() {
      this.addClass('mousedown');
    });
    scrubBar.addEvent('mouseup', function() {
      this.removeClass('mousedown');
    });
    this.slider = new Slider(
      this.container.getElement('div.music_player_scrub'),
      this.container.getElement('div.music_player_scrub_cursor'), {
        snap: false,
        offset: 0,
        range: [0, this.options.sliderWidth],
        wheel: true,
        steps: this.options.sliderWidth,
        initialStep: 0,
        onComplete: this.seekTo.bind(this)
      }
    );     
  },

  _updateDuration : function() {
    if( !this.sound ) {
      return;
    }

    // Update duration
    var ms = this.sound.durationEstimate;
    var d = new Date(ms);
    var hms = d.getMinutes().toString() +':'+ (d.getSeconds().toString().length==1?'0':'') + d.getSeconds().toString();
    this.container.getElement('div.music_player_time_total').set('text', hms);
  },

  _updateScrub : function() {
    if( !this.sound ) {
      return;
    }
    // Update elapsed
    var ms = this.sound.position;
    var d = new Date(ms);
    var hms = d.getMinutes().toString() +':'+ (d.getSeconds().toString().length==1?'0':'') + d.getSeconds().toString();
    this.container.getElement('div.music_player_time_elapsed').set('text', hms);

    // Update scrub
    if( !this.slider.element.hasClass('mousedown') ) {
      var percent = (this.sound.position / this.sound.durationEstimate) * 100;
      var steps = Math.round(percent * (this.options.sliderWidth / 100));
      this.slider.set(steps);
    }
  },

  _updateVolume : function() {
    var mute = en4.music.player.mute;
    var volume = en4.music.player.getVolume();

    var muteButton = this.container.getElement('.music_player_controls_volume_toggle');
    var volumeElements = this.container.getElements('.music_player_controls_volume_bar');

    // Mute
    if( mute ) {
      // Sound
      if( this.sound ) {
        this.sound.mute();
      }
      // UI
      muteButton.addClass('music_player_controls_volume_toggle_mute');
      volumeElements.each(function(el) {
        el.hide();
      });
    }

    // Volume
    else {
      // Sound
      if( this.sound ) {
        this.sound.unmute();
        this.sound.setVolume(volume);
      }
      // UI
      muteButton.removeClass('music_player_controls_volume_toggle_mute');
      volumeElements.each(function(el) {
        el.show();
        var bar = el.getChildren()[0];
        var level = bar.get('class').split('_');
        level = level[level.length - 1];
        if( (level * 20) <= volume ) {
          el.addClass('music_player_controls_volume_enabled');
        } else {
          el.removeClass('music_player_controls_volume_enabled');
        }
      });
    }
  }

});

})(); // END NAMESPACE