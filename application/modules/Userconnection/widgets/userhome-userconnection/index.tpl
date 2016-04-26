<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div id="userconnection_ajax_responsed">
	<?php if( !empty($this->tempTitle) ): ?>
		<h3><?php echo $this->translate($this->tempTitle); ?></h3>
	<?php endif; ?>
  <?php
  $widId = !empty($this->identity) ? $this->identity : null;
  if (!empty($this->isAjaxEnabled) && empty($this->loadFlage)) {
    ?>
    <script type="text/javascript">
      window.addEvent('domready', function() {
        var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $widId) ?>;
        var request = new Request.HTML({
          url : url,
          method: 'get',
          data : {
            format : 'html',
            'loadFlage' : 1,
            'user_id': '<?php echo $this->user_id; ?>',
          },
          onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $("userconnection_ajax_responsed").innerHTML = responseHTML;
          }
        });
        request.send();
      });
    </script>


    <?php
  }
  if (empty($this->loadFlage) && empty($this->showContent)) {
    ?>
    <div class="generic_userconnection_widget">
      <div class="loader_text"> <?php echo $this->translate('Loading...'); ?> </div>
    </div>
  <?php
  }

  if (!empty($this->showContent)) {
    ?>
    <ul>
      <li>
        <div align="center" class="user-network">
          <div class="usernetwork-1stdegree">
  <?php echo $this->count_first_degree_contacts ?>
            <div class="usernetwork-contacts-text">  
              <a href="<?php echo $this->url(array("module" => "userconnection", "controller" => "index", "action" => "index"), 'default', true) ?>"><?php echo $this->translate('Direct Contacts'); ?></a>

            </div>
          </div>
          <div class="usernetwork-2nddegree">
  <?php echo $this->count_second_degree_contacts; ?>
            <div class="usernetwork-contacts-text">
              <a href="<?php echo $this->url(array(), 'secondlevelfriends') ?>"><?php echo $this->translate('Contacts of your contacts'); ?> </a>
            </div>
          </div>
          <div class="usernetwork-3rddegree">
  <?php echo $this->count_third_degree_contacts; ?>
            <div class="usernetwork-contacts-text">
              <a href="<?php echo $this->url(array(), 'thirdlevelfriends') ?>"><?php echo $this->translate('3rd Level contacts'); ?></a>
            </div>       
          </div>
          <div class="width-full user-network-bottom-link">
            <a href="<?php echo $this->url(array(), 'userconnection_invite'); ?>"><?php echo $this->translate('Expand your network now!'); ?> <b>&raquo;</b></a>
          </div>
        </div>
      </li>
    </ul>
<?php } ?>
</div>
