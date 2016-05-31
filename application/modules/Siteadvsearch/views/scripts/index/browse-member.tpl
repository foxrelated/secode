<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: browse-member.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if(empty($this->widgetizePage)):?>
  <div class='layout_right'>
    <?php echo $this->form->render($this) ?>
  </div>
  <div class='layout_middle'>
    <div class='browsemembers_results' id='browsemembers_results'>
        <?php echo $this->render('_browseUsers.tpl') ?>
    </div>
  </div>
  <?php
    /* Include the common user-end field switching javascript */
    echo $this->partial('_jsSwitch.tpl', 'fields', array(
      'topLevelId' => (int) @$this->topLevelId,
      'topLevelValue' => (int) @$this->topLevelValue
    ))
  ?>

  <script type="text/javascript">
    en4.core.runonce.add(function() {

      window.addEvent('onChangeFields', function() {
        var firstSep = $$('li.browse-separator-wrapper')[0];
        var lastSep;
        var nextEl = firstSep;
        var allHidden = true;
        do {
          nextEl = nextEl.getNext();
          if( nextEl.get('class') == 'browse-separator-wrapper' ) {
            lastSep = nextEl;
            nextEl = false;
          } else {
            allHidden = allHidden && ( nextEl.getStyle('display') == 'none' );
          }
        } while( nextEl );
        if( lastSep ) {
          lastSep.setStyle('display', (allHidden ? 'none' : ''));
        }
      });
    });
  </script>
<?php endif;?>