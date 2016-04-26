/* This script and many more are available free online at
The JavaScript Source!! http://javascript.internet.com
Created by: Paul Tuckey | http://tuckey.org/
Modified by: EZboy yuriy.demchenko at gmail.com */

function countLines(strtocount, cols) {
  var hard_lines = 1;
  var last = 0;
  while ( true ) {
    last = strtocount.indexOf("\n", last+1);
    hard_lines ++;
    if ( last == -1 ) break;
  }
  var soft_lines = Math.round(strtocount.length / (cols-1));
  var hard = eval("hard_lines  " + unescape("%3e") + "soft_lines;");
  if ( hard ) soft_lines = hard_lines;
  if (soft_lines >= 10) {
		soft_lines = 10;
  }
  return soft_lines;
}

function cleanForm() {
  //for(var no=0;no<document.facebook_userfeed.length;no++) {
    var the_form = document.forms.facebook_userfeed;
    for( var x in the_form ) {
      if ( ! the_form[x] ) continue;
      if( typeof the_form[x].rows != "number" ) continue;
      if(!the_form[x].onkeyup) {
				the_form[x].onkeyup=function() {
					this.rows = countLines(this.value,this.cols)+1;
					};
		
				}
    }
  //}
}

window.addEvent('domready', function () {
	cleanForm();

});