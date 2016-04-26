<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @channel   Application_Extensions
 * @package    Seo
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>


<script type="text/javascript">

  var SortablesInstance;

  window.addEvent('load', function() {
    SortablesInstance = new Sortables('channel_list', {
      clone: false,
      constrain: true,
      handle: 'td.move-me',
      onComplete: function(e) {
        reorder(e);
      }
    });
  });

  var reorder = function(e) {

	     var channelitems = e.parentNode.childNodes;
	     var ordering = {};
	     var i = 1;
	     for (var channelitem in channelitems)
	     {
	       var child_id = channelitems[channelitem].id;

	       if ((child_id != undefined) && (child_id.substr(0, 5) == 'admin'))
	       {
	         ordering[child_id] = i;
	         i++;
	       }
	     }
	    ordering['format'] = 'json';

	    // Send request
	    var url = '<?php echo $this->url(array('action' => 'order')) ?>';
	    var request = new Request.JSON({
	      'url' : url,
	      'method' : 'POST',
	      'data' : ordering,
	      onSuccess : function(responseJSON) {
	      }
	    });

	    request.send();

	  }

  function ignoreDrag()
  {
    event.stopPropagation();
    return false;
  }

</script>


<h2><?php echo $this->translate("SEO Sitemap Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

    <p class="description">
      <?php echo $this->translate("This page lists all of the available channels for XML sitemap builder.") ?>
      <?php echo $this->translate("Beside building global sitemap, you can also build individual sitemap file for each supported channel per your needs.")?>
    
    </p>
    <br />
    <div>
        <?php echo $this->htmlLink($this->url(array('action'=>'create')), 
          $this->translate('Add New Channel'),
          array('class' => 'buttonlink icon_seo_sitemap_add')
        )?>    
        <?php echo $this->htmlLink($this->url(array('action'=>'build')), 
          $this->translate('Build Global Sitemap File'),
          array('class' => 'buttonlink icon_seo_sitemap_build smoothbox')
        )?>  
        <?php echo $this->htmlLink($this->url(array('action'=>'notify')), 
          $this->translate('Submit Sitemap to Search Engine'),
          array('class' => 'buttonlink icon_seo_sitemap_notify smoothbox')
        )?>   
        <?php echo $this->htmlLink($this->url(array('action'=>'schedule')), 
          $this->translate('Task Scheduler Settings'),
          array('class' => 'buttonlink icon_seo_sitemap_schedule smoothbox')
        )?> 
    </div>
    <br />
    
    <div>
      <?php echo $this->translate('Last Build: %s', 
        $this->sitemap['last_update'] ? $this->timestamp($this->sitemap['last_update']) : $this->translate('Never')
      );?>
      |
      <?php echo $this->translate('Last Submit: %s', 
        $this->sitemap['last_submit'] ? $this->timestamp($this->sitemap['last_submit']) : $this->translate('Never')
      )?>
      |
      <?php
        if ($this->sitemap['last_update'])
        { 
          $file = $this->htmlLink($this->sitemap['file']['url'], 
            $this->sitemap['file']['name'],
            array('target' => '_blank')
          );
          
          if ($this->sitemap['gzip'])
          {
            $file .= '  - ' . $this->htmlLink($this->sitemap['gzipfile']['url'], 
              $this->sitemap['gzipfile']['name'],
              array('target' => '_blank')
            );
          }          
        }
        else 
        {
          $file = $this->translate('None');
        }
      ?>
      
      <?php echo $this->translate('Sitemap File: %s', $file);?>
    </div>
    <br />
    <?php if(count($this->channels)>0):?>
    
       <table class='admin_table'>
        <thead>
          <tr>
            <th>&nbsp;</th>
            <th><?php echo $this->translate("Title") ?></th>
            <th><?php echo $this->translate("Description") ?></th>
            <th><?php echo $this->translate("Change Frequency") ?></th>
            <th><?php echo $this->translate("Priority") ?></th>
            <th><?php echo $this->translate("Max Items") ?></th>
            <th><?php echo $this->translate("Supported") ?></th>
            <th><?php echo $this->translate("Enabled") ?></th>
            <th><?php echo $this->translate("Options") ?></th>
          </tr>
        </thead>
        <tbody id='channel_list'>
          <?php foreach ($this->channels as $channel): ?>
            <tr id='admin_channel_item_<?php echo $channel->name; ?>'>
              <td class='move-me' style='cursor: move;'><img src="application/modules/Core/externals/images/admin/sortable.png" width="16" height="16"/></td>
              <td><?php echo $channel->getTitle()?></td>
              <td><?php echo $this->radcodes()->text()->truncate($channel->getDescription(), 48); ?></td>
              <td>
                <?php if ($channel->changefreq): ?>
                  <?php echo $this->translate($channel->changefreq); ?>
                <?php else: ?>
                  <?php echo $this->translate('default'); ?>  
                <?php endif; ?>
              </td>
              <td>
                <?php if ($channel->priority): ?>
                  <?php echo $this->translate($channel->priority); ?>
                <?php else: ?>
                  <?php echo $this->translate('default'); ?>  
                <?php endif; ?>
              </td>          
              <td>
                <?php if ($channel->maxitems): ?>
                  <?php echo $channel->maxitems; ?>
                <?php else: ?>
                  <?php echo $this->translate('unlimited'); ?>  
                <?php endif; ?>
              </td>
              <td><?php echo $this->translate($channel->isSupported() ? 'yes' : 'no'); ?></td>
              <td><?php echo $this->translate($channel->enabled ? 'yes' : 'no'); ?></td>
              <td style="font-size: 11px">
                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'seo', 'controller' => 'channels', 'action' => 'edit', 'name' =>$channel->name), $this->translate('edit'), array(
                  //'class' => 'smoothbox',
                )) ?>
                |
                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'seo', 'controller' => 'channels', 'action' => 'generate', 'name' =>$channel->name), $this->translate('build sitemap'), array(
                  'class' => 'smoothbox',
                )) ?>
                <br />
                <?php if ($channel->hasSitemapFile()): ?>
                  <?php echo $this->htmlLink($channel->getSitemapFileUrl(), $channel->getSitemapFilename(), array('target'=>'_blank'))?>
                <?php else: ?>
                  <?php echo $this->translate('no sitemap found')?>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no channels.") ?></span>
      </div>
    <?php endif;?>