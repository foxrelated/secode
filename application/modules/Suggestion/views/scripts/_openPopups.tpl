<?php
	$renderScript1 = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
	$renderScript2 = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
	if( !empty($modRedirectValue) && !empty($modName) ) {
		if( empty($isTimeout) ){ $isTimeout = 0; }
    if( empty($reviewListing) ){ $reviewListing = 0; }
		$script1 = <<<EOF
			var isTimeout = "$isTimeout";
			var baseURL = "$base_url";
			var modRedirectKey = "$modName";
			var modRedirectValue = "$modRedirectValue";
      var listingId = "$reviewListing";
			window.addEvent('load', function()
			{
				if( isTimeout == 1 ) {
					setTimeout('open_popup();', 1000);
				}else {
					open_popup();
				}
			});
			function open_popup()
			{
				if( isTimeout == 1 ) {
					var browserName=navigator.appName; 
					if (browserName=="Netscape"){ this.stop(); }
					else 
					{
						if (browserName=="Microsoft Internet Explorer") { }
						else{ this.stop(); }
					}
				}
				Smoothbox.open(baseURL + '/suggestion/index/switch-popup' + '/modName/' + modRedirectKey + '/modContentId/' + modRedirectValue + '/listingId/' + listingId);
			}
EOF;
	}
	if( !empty($entity) && !empty($entityId) )
	{
		if( empty($modSuggSecondClass) ){ $modSuggSecondClass = ''; }
		if( empty($modSecondClass) ){ $modSecondClass = ''; }
		$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
		$view = $this->getActionController()->view;
		$modRedirectURL = $base_url . '/suggestion/index/switch-popup/modName/' . $entity .'/modContentId/' . $entityId . '/modError/1';
		if( empty($isShowPipe) ) {
			$modRedirectLink = '<a href="javascript:void(0);" onclick = "smoothbox_open();">' . $view->translate('Suggest to Friends') . '</a>';
		}else {
			$modRedirectLink = '<a href="javascript:void(0);" onclick = "smoothbox_open();">' . $view->translate('Suggest to Friends') . '</a> | ';
		}
		$script2 = <<<EOF
		window.addEvent('domready', function()
		{
			if ( document.getElementById('{$modSuggFirstClass}') == null && document.getElementById('{$modSuggSecondClass}') == null ) 
			{
				var photocontainer = document.getElementById('global_content').getElement('{$modFirstClass}');
				if( photocontainer != null )
				{
					var newdiv = document.createElement('div');
					newdiv.id = '{$modSuggFirstClass}';
					newdiv.innerHTML = '{$modRedirectLink}'; 
					if (typeof photocontainer.childNodes[0] == 'undefined') {
		        photocontainer.innerHTML = '<div id="{$modSuggFirstClass}">{$modRedirectLink}</div>';
          }
          else {
					 photocontainer.insertBefore(newdiv, photocontainer.childNodes[0]);
          }
				}
				else
				{
					var photocontainer = document.getElementById('global_content').getElement('{$modSecondClass}');
					var newdiv = document.createElement('div');
					newdiv.id = '{$modSuggSecondClass}';
					newdiv.innerHTML = '{$modRedirectLink}';
					if (typeof photocontainer.childNodes[0] == 'undefined') {
						photocontainer.innerHTML = '<div id="{$modSuggSecondClass}">{$modRedirectLink}</div>';
					}else {
						photocontainer.insertBefore(newdiv, photocontainer.childNodes[0]);
					}
				}
			}
		});
		function smoothbox_open()
		{
			Smoothbox.open ('{$modRedirectURL}');							
		}
EOF;
	}
	if( !empty($script1) ){ $renderScript1->headScript()->appendScript($script1); }
	if( !empty($script2) ){ $renderScript2->headScript()->appendScript($script2); }
?>
