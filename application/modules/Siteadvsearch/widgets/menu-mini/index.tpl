<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $baseUrl = $this->layout()->staticBaseUrl;?>

<?php $this->headLink()
					->appendStylesheet($baseUrl . 'application/modules/Siteadvsearch/externals/styles/style_siteadvsearch.css'); ?>

<div class="layout_core_menu_mini">
	 <div id='core_menu_mini_menu'>
    <?php
     // Reverse the navigation order (they're floating right)
     $count = count($this->navigation);
     foreach( $this->navigation->getPages() as $item ) $item->setOrder(--$count);
    ?>
		  <ul>
      <?php if($this->viewer_id) :?>
        <li id='core_menu_mini_menu_update'>
          <span onclick="toggleUpdatesPulldown(event, this, '4');" style="display: inline-block;" class="updates_pulldown">
            <div class="pulldown_contents_wrapper">
              <div class="pulldown_contents">
                <ul class="notifications_menu" id="notifications_menu">
                  <div class="notifications_loading" id="notifications_loading">
                    <img src='<?php echo $baseUrl ?>application/modules/Siteadvsearch/externals/images/loading.gif' style='float:left; margin-right: 5px;' />
                    <?php echo $this->translate("Loading ...") ?>
                  </div>     
                </ul>
              </div>
              <div class="pulldown_options">
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
                $this->translate('View All Updates'),
                array('id' => 'notifications_viewall_link')) ?>
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
                'id' => 'notifications_markread_link',
                )) ?>
              </div>
            </div>
            <a href="javascript:void(0);" id="updates_toggle" <?php if( $this->notificationCount ):?> class="new_updates"<?php endif;?>><?php echo $this->translate(array('%s Update', '%s Updates', $this->notificationCount), $this->locale()->toNumber($this->notificationCount)) ?></a>
          </span>
        </li>
      <?php endif; ?>
      <?php foreach( $this->navigation as $item ): ?>
       <li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array_filter(array(
         'class' => ( !empty($item->class) ? $item->class : null ),
         'alt' => ( !empty($item->alt) ? $item->alt : null ),
         'target' => ( !empty($item->target) ? $item->target : null ),
        ))) ?></li>
      <?php endforeach; ?>
      <?php if($this->search_check):?>
        <li>
          <?php echo $this->content()->renderWidget("siteadvsearch.search-box", array("widgetName" => "advmenu_mini_menu", "advsearch_search_box_width" => $this->searchbox_width))?>
        </li>
      <?php endif;?>	
		  </ul>
	</div>
</div>
 
<script type='text/javascript'>
  var notificationUpdater;
  en4.core.runonce.add(function(){
    if($('titleAjax')){
      new OverText($('titleAjax'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }

    if($('notifications_markread_link')){
      $('notifications_markread_link').addEvent('click', function() {
        //$('notifications_markread').setStyle('display', 'none');
        en4.activity.hideNotifications('<?php echo $this->string()->escapeJavascript($this->translate("0 Updates"));?>');
      });
    }

    <?php if ($this->updateSettings && $this->viewer_id): ?>
    notificationUpdater = new NotificationUpdateHandler({
			'delay' : <?php echo $this->updateSettings;?>
		});
    notificationUpdater.start();
    window._notificationUpdater = notificationUpdater;
    <?php endif;?>
  });

  var toggleUpdatesPulldown = function(event, element, user_id) {
    if( element.className=='updates_pulldown' ) {
      element.className= 'updates_pulldown_active';
      showNotifications();
    } else {
      element.className='updates_pulldown';
    }
  }

  var showNotifications = function() {
    en4.activity.updateNotifications();
    new Request.HTML({
      'url' : en4.core.baseUrl + 'activity/notifications/pulldown',
      'data' : {
        'format' : 'html',
        'page' : 1
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if( responseHTML ) {
          // hide loading icon
          if($('notifications_loading')) $('notifications_loading').setStyle('display', 'none');

          $('notifications_menu').innerHTML = responseHTML;
          $('notifications_menu').addEvent('click', function(event){
            event.stop(); //Prevents the browser from following the link.

            var current_link = event.target;
            var notification_li = $(current_link).getParent('li');

            // if this is true, then the user clicked on the li element itself
            if( notification_li.id == 'core_menu_mini_menu_update' ) {
              notification_li = current_link;
            }

            var forward_link;
            if( current_link.get('href') ) {
              forward_link = current_link.get('href');
            } else{
              forward_link = $(current_link).getElements('a:last-child').get('href');
            }

            if( notification_li.get('class') == 'notifications_unread' ){
              notification_li.removeClass('notifications_unread');
              en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + 'activity/notifications/markread',
                data : {
                  format     : 'json',
                  'actionid' : notification_li.get('value')
                },
                onSuccess : function() {
                  window.location = forward_link;
                }
              }));
            } else {
              window.location = forward_link;
            }
          });
        } else {
          $('notifications_loading').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("You have no new updates."));?>';
        }
      }
    }).send();
  };
</script>