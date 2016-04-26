<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: invite.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->count > 0 || $this->isLeader): ?>
    <?php if ($this->isLeader):?>
    <script type="text/javascript">
       sm4.core.runonce.add(function() {
            if ($.mobile.activePage.find('#user_ids')) {
                  sm4.core.Module.autoCompleter.attach('user_ids', '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'member', 'action' => 'getmembers', 'event_id' => $this->event->event_id, 'occurrence_id' => $this->occurrence_id), 'default', true) ?>', {'singletextbox': false, 'limit':10, 'minLength': 1,'showPhoto' : true, 'search':'search'}, "toValues");
            }
        });
    </script>
    <?php endif;?>
    <?php if ($this->count > 0): ?>
    <script type="text/javascript">
    sm4.core.runonce.add(function(){
      $('#selectall').bind('click', function(event) {
        if(this.checked) {
          $("input[type='checkbox']").prop("checked",true).checkboxradio("refresh"); 
        } else {
          $("input[type='checkbox']").prop("checked",false).checkboxradio("refresh"); 
        }
      });
    });
    </script>
    <?php endif;?>
    <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
<?php else: ?>
    <div class="global_form_popup">
        <?php echo $this->translate('You have no friends to invite.'); ?>
        <br/><br/>
        <a href="#" data-rel="back" data-role="button">
        <?php echo $this->translate('Go Back') ?>
        </a>
    </div>
<?php endif; ?>