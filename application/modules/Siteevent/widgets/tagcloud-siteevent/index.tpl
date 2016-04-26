<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Siteevent/externals/scripts/core.js'); ?>
<?php if($this->loaded_by_ajax && !$this->isajax):?>
<script>

  var browsetagparams = {
            requestParams:<?php echo json_encode($this->allParams) ?>,
            responseContainer: $$('.layout_siteevent_tagcloud_siteevent'),
            requestUrl: en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
            loading: false
        }
    
  en4.core.runonce.add(function() {  
    browsetagparams.responseContainer.each(function(element) {   
     new Element('div', {
        'class': 'siteevent_profile_loading_image'
      }).inject(element);
    });
  en4.siteevent.ajaxTab.sendReq(browsetagparams);
  });

 </script>           
<?php endif;?>

<?php if($this->showcontent):?>
<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$module = $request->getModuleName();
$controller = $request->getControllerName();
$action = $request->getActionName();
?>

<?php if ($module == 'siteevent' && $controller == 'index' && $action == 'top-rated'): ?>
    <?php $url_action = 'top-rated'; ?>
<?php else: ?>
    <?php $url_action = 'index'; ?>
<?php endif; ?>

<ul class="seaocore_sidebar_list" id="browse_siteevent_tagsCloud">
    <li>
        <div>
            <?php foreach ($this->tag_array as $key => $frequency): ?>
                <?php $string = $this->string()->escapeJavascript($key); ?>
                <?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency']) * $this->tag_data['step'] ?>
                <a href='<?php echo $this->url(array('action' => $url_action), "siteevent_general"); ?>?tag=<?php echo urlencode($key) ?>&tag_id=<?php echo $this->tag_id_array[$key] ?>' style="font-size:<?php echo $step ?>px;" title=''><?php echo $key ?><sup><?php echo $frequency ?></sup></a> 
                    <?php endforeach; ?>
        </div>		
    </li>

    <?php if (empty($this->notShowExploreTags)) : ?>
        <li>
            <?php echo $this->htmlLink(array('route' => "siteevent_general", 'action' => 'tagscloud'), $this->translate('Explore Tags &raquo;'), array('class' => 'more_link')) ?>
        </li>
    <?php endif; ?>
</ul>
<?php endif;?>

