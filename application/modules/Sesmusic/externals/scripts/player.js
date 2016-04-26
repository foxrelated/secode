var playSongId = 0;
var autoPlay = false;
var songPlayWithId = 0;
var playingSongId = 0;
var addClassToBody = 0;
(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;

if( !('en4' in window) ) {
  en4 = {};
}

if( !('music' in en4) ) {
  en4.music = {};
}


//soundManager.url = en4.core.staticBaseUrl + 'externals/soundmanager/swf/';

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
  ignoreSeek: false,
  
  _isAttached : false,


  /**
   * Initialize
   */
  initialize: function(el, options) {

    this.setOptions(options);

    // attach container to this player
    this.container = $(el);
    this.container.getElement('div.sesmusic_player_scrub_downloaded').hide();
   
    this.songs = this.container.getElements('a.music_player_tracks_url');
    songs = this.songs;
    // attach
    this._attachEvents();
    this._attachScrub();
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
    if(counter == 0) { 
     this.songs;
    }
   else {
     this.songs = songs;
   }

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
    this.setTitle(this.songs[index].get('title'));
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
    var soundOptions = {
      id:             soundId,
      url:            this.songs[index].href,
      autoload:       true,
      useVideo:       false,
      volume:         en4.music.player.getVolume(),
      onload:         function() { this.fireEvent('soundmanager_onload'); }.bind(this), //function() { this.setDuration(this); }.bind(this),
      whileloading:   function() { this.fireEvent('soundmanager_whileloading'); }.bind(this), //function() { this.setDuration(this); }.bind(this),
      whileplaying:   function() { this.fireEvent('soundmanager_whileplaying'); }.bind(this), //function() { this.setElapsed(this); this.setScrub(this); }.bind(this),
      onplay:         function() { this.fireEvent('soundmanager_onplay'); }.bind(this), //function() { this.container.getElement('.sesmusic_player_button_play').addClass('sesmusic_player_button_pause'); this.logPlay(this); }.bind(this),
      onresume:       function() { 
												if(playingSongId>0){
													setCookie("sesmusic_playSongId", playingSongId, 1);
												}
												this.fireEvent('soundmanager_onresume'); 
											
											}.bind(this), //function() { this.container.getElement('.sesmusic_player_button_play').addClass('sesmusic_player_button_pause'); }.bind(this),
      onpause:        function() {  
												var getSongPlaying = document.getElementById('sesmusic_player_tracks').getElementsByTagName('li');
												for(var i = 0; i < getSongPlaying.length ; i++) {
														if($(getSongPlaying[i]).hasClass('song_playing')){
															playingSongId =  getSongPlaying[i].id.replace("sesmusic_playlist_", "");
														}
												}	
													setCookie("sesmusic_playSongId", 0, 0);										
												this.fireEvent('soundmanager_onpause'); 
											}.bind(this), //function() { this.container.getElement('.sesmusic_player_button_play').removeClass('sesmusic_player_button_pause'); }.bind(this),
      onstop:         function() { setCookie("sesmusic_playSongId", 0, 0);this.fireEvent('soundmanager_onstop'); }.bind(this), //function() { this.container.getElement('.sesmusic_player_button_play').removeClass('sesmusic_player_button_pause'); }.bind(this),
      onfinish:       function() { setCookie("sesmusic_playSongId", 0, 0);this.fireEvent('soundmanager_onfinish'); }.bind(this), //function() { this.container.getElement('.sesmusic_player_button_play').removeClass('sesmusic_player_button_pause'); this.playNext(); }.bind(this),
      onbeforefinish: function(index) { this.fireEvent('soundmanager_onbeforefinish', [index]); }.bind(this) //function() { var nextsong = this.create(song_id+1); nextsong.load(); }
    };
    
    //if( soundId.replace(/\?[^?]+?$/, '').match(/\.(mp3)$/i) ) {
      this.getSoundManager().createSound(soundOptions);
    //} else {
    //  this.getSoundManager().createVideo(soundOptions);
    //}

    // Set volume this way to initialize display
    this._updateVolume();

    return this.getSoundManager().getSoundById(soundId);
  },

  /**
   * logPlay
   */
  logPlay : function() {
  if(addClassToBody == 1){
   var htmlElement = document.getElementsByTagName("body")[0];
    htmlElement.addClass('sesmusic_music_player_full');
  }
	 $('sesmusic_showseprator').style.display = 'block';
   if(counter == 0) { 
     this.songs;
    }else {
     this.songs = songs;
   }
   this._updateDuration();
    var song_id = this.songs[this.selected].rel;
    var songUrl = this.songs[this.selected].href; 
    var songUrlMatch = songUrl.match('http://api.soundcloud.com/tracks/');
    var notShowDownload = 0;
    if(songUrlMatch == 'http://api.soundcloud.com/tracks/') {
      notShowDownload = 1;
    }
		
    	setCookie("sesmusic_playSongId", song_id, 1);
	
    // Change classes
    this.songs.each(function(song, index) {
      if( index == this.selected ) {
        song.getParent('li').addClass('song_playing');
      } else {
        song.getParent('li').removeClass('song_playing');
      }
    }.bind(this));

    //Increase play count Tally song
    if( !this.tallied[song_id] ) {
      this.tallied[song_id] = true;
      new Request.JSON({
        url: en4.core.baseUrl + 'music/song/' + song_id + '/tally',
        noCache: true,
        data: {
          format: 'json',
          albumsong_id: song_id,
        },
        onSuccess: function(responseJSON) {
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
    if( !this.sound || this.ignoreSeek ) {
      return;
    }
   
    var ms_total = this.sound.duration;
    var ms_dest  = Math.round(ms_total * (pos / this.options.sliderWidth));
    var diff     = Math.abs(ms_dest - this.sound.position);
    var tolerance = Math.round(ms_total / this.options.sliderWidth);
    if( this.slider.element.hasClass('mousedown') || diff > tolerance ) {
      this.sound.setPosition(ms_dest);
    }
  },

  /**
   * playNext
   */
  playNext : function() {
    
    if(counter == 0) {
     this.songs;
    } else {
     this.songs = songs;
    }

    //Set herf
    if(this.songs.length == this.selected + 1) {
      $$('.sesmusic_player_button_prev').fireEvent('click');
      return false;
    }
		
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
    var href = this.container.getElement('a.sesmusic_player_button_launch').href;
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
    this.container.getElement('div.sesmusic_player_time_total').get('text');
  },

  /**
   * getElapsed
   */
  getElapsed : function() {
    this.container.getElement('div.sesmusic_player_time_elapsed').get('text');
  },

  /**
   * getTitle
   */
  getTitle : function() {
    this.container.getElement('div.sesmusic_player_trackname').get('text');
  },

  /**
   * setTitle
   */
  setTitle : function(sText) {
    this.container.getElement('div.sesmusic_player_trackname').set('text', sText);
  },

  /**
   * setDownloaded
   */
  setDownloaded: function(pl) {
    var self = pl;
    if (self.sound) {
      var percent = 100;
      if (self.sound.isBuffering)
        percent = (self.sound.position/self.sound.duration)*100;
      self.getElement('div.sesmusic_player_scrub_downloaded').setStyle('width', percent);
    }
  },

  // Utility
  _getSoundId : function(index) {
    if( !$type(this.songs[index]) ) {
      return false;
    }
    return '_song_' +
      this.container.getParent('.sesmusic_player_wrapper').id + '_' +
      this.songs[index].href; //.replace(/[^A-Za-z0-9./\\]+/ig, '');
  },

  _attachEvents : function() {
    if( this._isAttached ) {
      return;
    }
    // play
    this.container.getElement('.sesmusic_player_button_play').addEvent('click', function(event) {
      this.play();
      if(event)
      event.stop();
      return false;
    }.bind(this));
		// play
    this.container.getElement('#sesmusic_player_button_autoPlay').addEvent('click', function(event) {
			var totalSongSelected = document.getElementById('sesmusic_player_tracks').getElementsByTagName('li');
			if(playSongId){
				songid = playSongId;
				setCookie("sesmusic_playSongId", 0, 1);
				
			}
			if(songPlayWithId != 0){
				songid = songPlayWithId;
			}
			for(var i = 0; i < totalSongSelected.length ; i++) {
				if(totalSongSelected[i].id.replace("sesmusic_playlist_", "") == songid) {
					this.selected = i;
				}
			}
      this.play();
      if(event)
      	event.stop();
      return false;
    }.bind(this));
    // previous
    this.container.getElement('.sesmusic_player_button_prev').addEvent('click', function(event) {
      this.playPrev();
      if(event)
      event.stop();
      return false;
    }.bind(this));

    // next
    this.container.getElement('.sesmusic_player_button_next').addEvent('click', function(event) {
      this.playNext();
      if(event)
      event.stop();
      return false;
    }.bind(this));

    // popout
    this.container.getElement('.sesmusic_player_button_launch').addEvent('click', function(event) {
      this.launch();
      event.stop();
      return false;
    }.bind(this));
    
    // mute
    this.container.getElement('.sesmusic_player_controls_volume_toggle').addEvent('click', function(event) {
      en4.music.player.toggleMute();
      this._updateVolume();
      event.stop();
      return false;
    }.bind(this));
    
    // volume
    this.container.getElements('.sesmusic_player_controls_volume_bar').addEvents({
      'click' : function(event) {
        var bar = $(event.target);
        if( bar.hasClass('sesmusic_player_controls_volume_bar') ) {
          bar = bar.getElement('span');
        }
        var level = bar.className.split('_');
        level = parseInt( level[level.length-1] );
        //this.setVolume(level * 20);
        en4.music.player.setVolume(level * 20);
        this._updateVolume();
      }.bind(this),
      'mouseover' : function(event) {
        var bar = $(event.target);
        if( bar.hasClass('sesmusic_player_controls_volume_bar') ) {
          bar = bar.getElement('span');
        }
        var level = bar.className.split('_');
        level = parseInt( level[level.length-1] );
        // are we missing something here?
      }.bind(this)
    });


    // tracks
    $(document.body).addEvent("click:relay(.sesmusicplaylistmore)", function(event, element) {
      var targetId = event.target.id;
      
      if(targetId.split('_')[0] != 'delete') {
        

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
    }
    }.bind(this));

    // sound manager events
    this.addEvents({
      soundmanager_onload : this._updateDuration.bind(this),
      soundmanager_whileloading : this._updateDuration.bind(this),
      soundmanager_whileplaying : this._updateScrub.bind(this),
      soundmanager_onplay : function() {
        this.container.getElement('.sesmusic_player_button_play').addClass('sesmusic_player_button_pause');
        this.logPlay(this);
      }.bind(this),
      soundmanager_onresume : function() {
        this.container.getElement('.sesmusic_player_button_play').addClass('sesmusic_player_button_pause');
      }.bind(this),
      soundmanager_onpause : function() {
        this.container.getElement('.sesmusic_player_button_play').removeClass('sesmusic_player_button_pause');
      }.bind(this),
      soundmanager_onstop : function() {
        this.container.getElement('.sesmusic_player_button_play').removeClass('sesmusic_player_button_pause');
        this.slider.set(0);
        if( this.sound ) {
          this.container.getElement('div.sesmusic_player_time_elapsed').set('text', '0:00');
        }
      }.bind(this),
      soundmanager_onfinish : function() {
        this.container.getElement('.sesmusic_player_button_play').removeClass('sesmusic_player_button_pause');
        this.playNext();
      }.bind(this),
      soundmanager_onbeforefinish : function(song_id) {
        var nextsong = this.create(song_id+1);
        nextsong.load();
      }.bind(this)
    });
  },

  _attachScrub : function() {
    if( this._isAttached ) {
      return;
    }
    var width_slider = this.container.getElement('.sesmusic_player_control').clientWidth;

    // attach scrub bar
    var scrubBar = this.container.getElement('div.sesmusic_player_scrub');
    var chrome = this.container.getElement('.sesmusic_player_trackname').measure(function() {
      return this.getDimensions();
    });
    var img = this.container.getElement('.sesmusic_player_art').measure(function() {
      return this.getDimensions();
    });
    if( this.container.getElement('.sesmusic_player_art').isDisplayed() ) {
      this.options.sliderWidth = width_slider - img.width;
    } else {
      this.options.sliderWidth = width_slider;
    }
    scrubBar.setStyle('width', this.options.sliderWidth + 'px');
    scrubBar.addEvent('mousedown', function() {
      this.addClass('mousedown');
    });
    scrubBar.addEvent('mouseup', function() {
      this.removeClass('mousedown');
    });
    this.slider = new Slider(
      this.container.getElement('div.sesmusic_player_scrub'),
      this.container.getElement('div.sesmusic_player_scrub_cursor'), {
        snap: false,
        offset: 0,
        range: [0, this.options.sliderWidth],
        wheel: true,
        steps: this.options.sliderWidth,
        initialStep: 0,
        onComplete: this.seekTo.bind(this)
      }
    );

    //if( $type(en4.orientation) && en4.orientation == 'rtl' ) {
    //  this.slider.property = 'right';
    //}
  },

  _updateDuration : function() {
    if( !this.sound ) {
      return;
    }

    // Update duration
    var ms = this.sound.duration;
    var d = new Date(ms);
    var hms = d.getMinutes().toString() - 30 +':'+ (d.getSeconds().toString().length==1?'0':'') + d.getSeconds().toString();
    this.container.getElement('div.sesmusic_player_time_total').set('text', hms);
  },

  _updateScrub : function() {
    if( !this.sound ) {
      return;
    }
    
    // Update elapsed
    var ms = this.sound.position;

    var d = new Date(ms);
    var hms = d.getMinutes().toString() - 30  +':'+ (d.getSeconds().toString().length==1?'0':'') + d.getSeconds().toString();
    this.container.getElement('div.sesmusic_player_time_elapsed').set('text', hms);

    // Update scrub
    if( !this.slider.element.hasClass('mousedown') ) {
      var percent = (this.sound.position / this.sound.duration) * 100;
      var steps = Math.round(percent * (this.options.sliderWidth / 100));
      this.ignoreSeek = true;
      this.slider.set(steps);
      this.ignoreSeek = false;
    }
  },

  _updateVolume : function() {
    var mute = en4.music.player.mute;
    var volume = en4.music.player.getVolume();

    var muteButton = this.container.getElement('.sesmusic_player_controls_volume_toggle');
    var volumeElements = this.container.getElements('.sesmusic_player_controls_volume_bar');

    // Mute
    if( mute ) {
      // Sound
      if( this.sound ) {
        this.sound.mute();
      }
      // UI
      muteButton.addClass('sesmusic_player_controls_volume_toggle_mute');
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
      muteButton.removeClass('sesmusic_player_controls_volume_toggle_mute');
      volumeElements.each(function(el) {
        el.show();
        var bar = el.getChildren()[0];
        var level = bar.get('class').split('_');
        level = level[level.length - 1];
        if( (level * 20) <= volume ) {
          el.addClass('sesmusic_player_controls_volume_enabled');
        } else {
          el.removeClass('sesmusic_player_controls_volume_enabled');
        }
      });
    }
  }

});
})(); // END NAMESPACE