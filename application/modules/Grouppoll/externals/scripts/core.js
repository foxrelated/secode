/* $Id: core.js 2010-12-08 9:40:21Z SocialEngineAddOns Copyright 2009-2010 BigStep Technologies Pvt. Ltd. $ */
en4.grouppoll = {

  urls : {
    vote : 'grouppolls/vote/',
    login : 'login'
  },

  data : {},

  addGrouppollData : function(identity, data) {
    if( $type(data) != 'object' ) {
      data = {};
    }
    data = $H(data);
    this.data[identity] = data;
    return this;
  },

  getPollDatum : function(identity, key, defaultValue) {
    if( !defaultValue ) {
      defaultValue = false;
    }
    if( !(identity in this.data) ) {
      return defaultValue;
    }
    if( !(key in this.data[identity]) ) {
      return defaultValue;
    }
    return this.data[identity][key];
  },

  toggleResults : function(identity) {
    var grouppollContainer = $('grouppoll_form_' + identity);
    if( 'none' == grouppollContainer.getElement('.grouppoll_options div.grouppoll_has_voted').getStyle('display') ) {
      grouppollContainer.getElements('.grouppoll_options div.grouppoll_has_voted').show();
      grouppollContainer.getElements('.grouppoll_options div.grouppoll_not_voted').hide();
      grouppollContainer.getElement('.grouppoll_toggleResultsLink').set('text', en4.core.language.translate('Show Question'));
    } else {
      grouppollContainer.getElements('.grouppoll_options div.grouppoll_has_voted').hide();
      grouppollContainer.getElements('.grouppoll_options div.grouppoll_not_voted').show();
      grouppollContainer.getElement('.grouppoll_toggleResultsLink').set('text', en4.core.language.translate('Show Result'));
    }
  },

  renderResults : function(identity, answers, votes) {
    if( !answers || 'array' != $type(answers) ) {
      return;
    }
    var grouppollContainer = $('grouppoll_form_' + identity);
    answers.each(function(option) {
      var div = $('grouppoll-answer-' + option.poll_option_id);
      var pct = votes > 0
              ? Math.floor(100*(option.votes / votes))
              : 1;
      if (pct < 1)
          pct = 1;
      // set width to 70% of actual width to fit text on same line
      div.style.width = (.7*pct)+'%';
      div.getNext('div.grouppoll_answer_total')
         .set('text',  option.votesTranslated + ' (' + en4.core.language.translate('%1$s%%', (option.votes ? pct : '0')) + ')');
      if((!this.getPollDatum(identity, 'canChangeVote') )) {
        grouppollContainer.getElement('.grouppoll_radio input').set('disabled', true);
      }
    }.bind(this));
  },

  vote: function(identity, option) {
    if( !en4.user.viewer.id ) {
      window.location.href = this.urls.login + '?return_url=' + encodeURIComponent(window.location.href);
      return;
    }
    //if( en4.core.subject.type != 'grouppoll' ) {
    //  return;
    //}
    if( $type(option) != 'element' ) {
      return;
    }
    option = $(option);

    var grouppollContainer = $('grouppoll_form_' + identity);
    var value = option.value;

    $('grouppoll_radio_' + option.value).toggleClass('grouppoll_radio_loading');

    var request = new Request.JSON({
      url: this.urls.vote,
      method: 'post',
      data : {
       'format' : 'json',
        'poll_id' : identity,
        'option_id' : value
      },
      onComplete: function(responseJSON) {
        $('grouppoll_radio_' + option.value).toggleClass('grouppoll_radio_loading');
        if( $type(responseJSON) == 'object' && responseJSON.error ) {
          Smoothbox.open(new Element('div', {
            'html' : responseJSON.error
              + '<br /><br /><button onclick="parent.Smoothbox.close()">'
              + en4.core.language.translate('Close')
              + '</button>'
          }));
        } else {
          grouppollContainer.getElement('.grouppoll_vote_total')
            .set('text', en4.core.language.translate(['%1$s vote', '%1$s votes', responseJSON.votes_total], responseJSON.votes_total));
          this.renderResults(identity, responseJSON.grouppollOptions, responseJSON.votes_total);
          this.toggleResults(identity);
        }
        if( !this.getPollDatum(identity, 'canChangeVote') ) {
          grouppollContainer.getElements('.grouppoll_radio input').set('disabled', true);
        }
      }.bind(this)
    });
    
    request.send()
  }

};