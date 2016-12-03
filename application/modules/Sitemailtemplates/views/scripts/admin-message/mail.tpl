<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: mail.tpl 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
  $view->headScript()
				->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
				->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
				->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
				->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');?>

<h2><?php echo $this->translate("Email Templates Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php if (!empty($this->messageSent)): ?>
  <ul class="form-notices" >
    <li>
      <?php echo 'Your email has been queued for sending.'; ?>
    </li>
  </ul>
<?php endif;?>

<div class='clear  seaocore_settings_form'>
	<div class='settings'>
		<?php echo $this->form->render($this); ?>
	</div>
</div>

<style type="text/css">
.defaultSkin iframe {
	height:300px !important;
	width: 650px !important;
}
#send_mail-label{
	display:none;
}
</style>

<script type="text/javascript">
  var show_profietype =  '<?php echo $this->show_profietype;?>';
  window.addEvent('domready', function() {
  if(document.getElementById('sitemailtemplates_send_mail-1').checked) {
    var optionShow = 1;
  }
  else {
    var optionShow = 0;
  }
  $('toValues-wrapper').style.display='none';
  showMemeberlevel(optionShow);

	});

  function showMemeberlevel(optionShow) {
		if(optionShow == true) {
			$('member_levels-wrapper').style.display = 'block';
      $('user_ids-wrapper').style.display = 'none';
      if(show_profietype == 1) {
				$('profile_types-wrapper').style.display = 'block';
      }
      $('networks-wrapper').style.display = 'block';
		} else {
			$('member_levels-wrapper').style.display = 'none';
      if(show_profietype == 1) {
				$('profile_types-wrapper').style.display = 'none';
      }
      $('user_ids-wrapper').style.display = 'block';
      $('networks-wrapper').style.display = 'none';
      $('toValues-wrapper').style.display='none';
		}
  }

  // Populate data
  var maxRecipients = <?php echo sprintf("%d", $this->maxRecipients) ?> || 100;
  var to = {
    id : false,
    type : false,
    guid : false,
    title : false
  };
  var isPopulated = false;

  <?php if( !empty($this->isPopulated) && !empty($this->toObject) ): ?>
    isPopulated = true;
    to = {
      id : <?php echo sprintf("%d", $this->toObject->getIdentity()) ?>,
      type : '<?php echo $this->toObject->getType() ?>',
      guid : '<?php echo $this->toObject->getGuid() ?>',
      title : '<?php echo $this->string()->escapeJavascript($this->toObject->getTitle()) ?>'
    };
  <?php endif; ?>
 
  function removeFromToValue(id) {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = $('toValues').value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);
    if(toValueArray.length == 1) {
      $('toValues-wrapper').style.display='none';
    }
    // check if we are removing multiple recipients
    if (checkMulti!=-1){
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray);
      }
    }
    else{
      removeToValue(id, toValueArray);
    }

    // hide the wrapper for usernames if it is empty
    if ($('toValues').value==""){
      $('toValues-wrapper').setStyle('height', '0');
    }

    $('user_ids').disabled = false;
  }
 
  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    $('toValues').value = toValueArray.join();
  }

  en4.core.runonce.add(function() {
   // if( !isPopulated ) { // NOT POPULATED
      new Autocompleter.Request.JSON('user_ids', '<?php echo $this->url(array('module' => 'sitemailtemplates', 'controller' => 'message', 'action' => 'getitem'), 'admin_default', true) ?>', {
      'postVar' : 'user_ids',
			'minLength': 1,
			'delay' : 250,
			'selectMode': 'pick',
			'element': 'toValues',
			'autocompleteType': 'message',
			'multiple': false,
			'className': 'seaocore-autosuggest',
			'filterSubset' : true,
			'tokenFormat' : 'object',
			'tokenValueKey' : 'label',
        'injectChoice': function(token){

          //if(token.type == 'sitepage'){
            var choice = new Element('li', {
              'class': 'autocompleter-choices',
              'html': token.photo,
              'id':token.label
            });
            new Element('div', {
              'html': this.markQueryValue(token.label),
              'class': 'autocompleter-choice'
            }).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
            
        },
  
        onPush : function(){
          if ($('toValues-wrapper')) {
						$('toValues-wrapper').style.display='block';
					}
          if( $('toValues').value.split(',').length >= maxRecipients ){
            $('user_ids').disabled = true;
          }
        }
      });
      new Composer.OverText($('user_ids'), {
        'textOverride' : '<?php echo $this->translate('Start typing...') ?>',
        'element' : 'label',
        'isPlainText' : true,
        'positionOptions' : {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
      
   // } 
  });

	function sendEmail()
	{
		var confirm_mail =  confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to send this email to selected Members / Group of Members?")) ?>');
    if(confirm_mail == false) {
     return;
    }
    $('mail_form').submit();
	}

</script>