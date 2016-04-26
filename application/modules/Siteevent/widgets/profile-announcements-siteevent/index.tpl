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

<div id='id_<?php echo $this->content_id; ?>'>
    <?php if (count($this->announcements) > 0): ?>
        <ul class="siteevent_profile_announcements">
            <?php foreach ($this->announcements as $item): ?>
                <li>
                    <?php if ($this->showTitle): ?>
                        <div class="siteevent_profile_announcement_title mbot5"><?php echo $item->title; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($item->body)): ?>
                        <div class="siteevent_profile_list_info_des show_content_body">
                            <?php echo $item->body; ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="tip">
            <span>
                <?php echo $this->translate('No announcements have been created yet.'); ?>
            </span>
        </div>
    <?php endif; ?>
</div>

<script type="text/javascript">

    function scrollToTopForSiteevent(id) {
      if(document.getElement('body').get('id'))	{
        var scroll = new Fx.Scroll(document.getElement('body').get('id'), {
          wait: false,
          duration: 1000,
          offset: {
            'x': -200, 
            'y': -100
          },
          transition: Fx.Transitions.Quad.easeInOut
        });

        scroll.toElement(id);  
      }
      return;
    }
    
    $$('.tab_<?php echo $this->identity; ?>').addEvent('click', function(event)
    {
        prev_tab_id = '<?php echo $this->content_id; ?>';
        prev_tab_class = 'layout_siteevent_profile_announcements_siteevent';
        $('id_' + <?php echo $this->content_id ?>).style.display = "block";
        if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
            $$('.' + prev_tab_class).setStyle('display', 'none');
        }

        if ($(event.target).get('tag') != 'div' && ($(event.target).getParent('.layout_siteevent_profile_announcements_siteevent') == null)) {
            scrollToTopForSiteevent($("global_content").getElement(".layout_siteevent_profile_announcements_siteevent"));
        }
    });
    
</script>