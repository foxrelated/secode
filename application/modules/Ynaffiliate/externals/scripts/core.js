window.addEvent('domready',function(){
	if (($('From_Date')) && $('From_Date').value != '') {
		$('sday').value = $('From_Date').value;
	}
	if (($('To_Date')) && $('To_Date').value != '') {
		$('eday').value = $('To_Date').value;
	}
});

