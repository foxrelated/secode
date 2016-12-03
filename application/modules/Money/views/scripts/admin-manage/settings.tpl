<?php
?>
<script type="text/javascript">
  var supportedCurrencyIndex;
  var gateways;
  var displayCurrencyGateways = function() {
    var currency = $('money_currency').get('value');
    var has = [], hasNot = [];
    gateways.each(function(title, id) {
      console.log(id, title);
      if( !supportedCurrencyIndex.has(title) ) {
        hasNot.push(title);
      } else if( !supportedCurrencyIndex.get(title).contains(currency) ) {
        hasNot.push(title);
      } else {
        has.push(title);
      }
      var supportString = '';
      if( has.length > 0 ) {
        supportString += '<span class="currency-gateway-supported">'
            + 'Supported Gateways: ' + has + '</span>';
      }
      if( has.length > 0 && hasNot.length > 0 ) {
        supportString += '<br />';
      }
      if( hasNot.length > 0 ) {
        supportString += '<span class="currency-gateway-unsupported">'
            + 'Unsupported Gateways: ' + hasNot + '</span>';
      }
      $$('#money_currency-element .description')[0].set('html', supportString);
    });

  }
  window.addEvent('load', function() {
    supportedCurrencyIndex = new Hash(<?php echo Zend_Json::encode($this->supportedCurrencyIndex) ?>);
    gateways = new Hash(<?php echo Zend_Json::encode($this->gateways) ?>);
    $('money_currency').addEvent('change', displayCurrencyGateways);
    displayCurrencyGateways();
  });
</script>
<h2>
  <?php echo $this->translate("E-money") ?>
</h2>
<div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<div class='clear'>
    <div class='settings'>

      <?php echo $this->form->render($this); ?>

    </div>
  </div>