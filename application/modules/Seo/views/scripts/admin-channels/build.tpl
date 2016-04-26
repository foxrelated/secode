<?php


?>

<form method="post" class="global_form_popup" action="<?php echo $this->url(array()) ?>">
  <div>
    <h3><?php echo $this->translate("Build Sitemap File") ?></h3>
    <p>
      <?php echo $this->translate("Do you really want to update your xml sitemap file? For big site with a lot of users and content, this process may take a while depending on your settings, espcially if you enable User channel, or channels that have a lot of records.") ?>
    </p>
    <br />
    <p><?php echo $this->translate('NOTICE: If you get "PHP Fatal error: Allowed memory size of XXX bytes exhausted ..", it is because your server does not have enough memory to generate sitemap file for your entire site. Either contact your host and ask them to increase the PHP allowed memory or you have to limit the channel max items output.')?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->seo_id?>"/>
      <button type='submit'><?php echo $this->translate("Generate Sitemap") ?></button>
      <?php echo $this->translate("or") ?>
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?>
      </a>
    </p>
  </div>
</form>

<?php if( @$this->closeSmoothbox ): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
