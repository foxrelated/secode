
<div class="global_form_popup">
  <h3><?php echo $this->translate('Channel: %s', $this->channel->getTitle())?></h3>
  <?php 
    echo $this->translate('Built Sitemap File: %s', 
      $this->htmlLink($this->channel->getSitemapFileUrl(), $this->channel->getSitemapFileUrl(), array('target'=>'_blank')
    ));
  ?>
  <br />
  <p>
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("close window") ?>
      </a>
  </p>
</div>
<?php if( @$this->closeSmoothbox ): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
