<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<div class="heevent-block heevent-widget">
  <div class="heevent-widget-inner">
    <?php if( $this->form ): ?>
      <?php echo $this->form->render($this) ?>
    <?php endif ?>
  </div>
</div>
 <script type="text/javascript">
   en4.core.runonce.add(function (){
     $$('form.filters')[0].getElements('select').set('onchange', '_hem.ajaxSearch($(this).getParent("form"), $(\'global_content\').getElement(\'.layout_core_content\'))');
     $$('form.filters')[0].getElement('#search_text, #search').set('onkeypress', 'if(!this.value) return; clearTimeout(this._to); var self = $(this); this._to = setTimeout(function(){_hem.ajaxSearch(self.getParent("form"), $(\'global_content\').getElement(\'.layout_core_content\'))}, 400)');
   });
 </script>

<script type="text/javascript">

</script>
