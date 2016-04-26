<div class="headline">
  <h2>
    <?php echo $this->translate('Affiliate');?>
  </h2>
  <div class="tabs ynaffiliate-menu-top">
  <?php if( count($this->navigation) > 0 ): ?>
      <?php echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function() 
  {
        $$('a.ynaffiliate_main_more').each(function(e)
        {
            <?php $session = new Zend_Session_Namespace('mobile');
            if($session -> mobile):?>
                var parent = e.getParent();
                var sub = parent.getChildren('ul');
                var sub_html = "";
                if(sub.length > 0)
                    sub_html = sub[0].innerHTML;
                var parent_parent = parent.getParent();
                parent.destroy();
                parent_parent.set('html', parent_parent.get("html") + sub_html);
            <?php else:?>
            e.addEvent('click', function() 
            {
                if(this.getParent().hasClass('ynaffiliate_more_show'))
                {
                    this.getParent().removeClass('ynaffiliate_more_show');
                }
                else
                {
                    this.getParent().addClass('ynaffiliate_more_show');
                }
            });
            <?php endif;?>
        });
  });
</script>