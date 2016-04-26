<?php ?>
<script type = "text/javascript">
  function getProfileRedirect(url) {
    window.parent.location.href = url; 
  }
</script>
<h6>Wish your friend!!!!</h6>
<div> 
  <table>
    <tr>
      <td>
	<a href="<?php echo $this->url(array('id' => $this->user_id), 'user_profile') ?>"> <?php echo $this->itemPhoto($this->user($this->user_id), 'thumb.icon') ?></a>  
	<a href="<?php echo $this->url(array('id' => $this->user_id), 'user_profile') ?>" onclick="getProfileRedirect(this)" > <?php echo $this->user($this->user_id)->getTitle() ?><?php //echo $this->username ?></a>   
      </td> 
    </tr>
    <tr>
      <td>
	<a class="buttonlink" href="<?php echo $this->url(array('id' => $this->user_id), 'user_profile') ?>" onclick="getProfileRedirect(this)" > <?php echo "Wish" ?></a>   
      </td>
    </tr>
     <tr>
      <td>
	<a style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>'application/modules/Messages/externals/images/send.png');" class="buttonlink" href="<?php echo $this->sugg_baseUrl; ?>/messages/compose/to/<?php echo $this->user_id; ?>" onclick="getProfileRedirect(this)"><?php echo $this->translate('Send Message'); ?></a>
      </td>
    </tr>
  </table>
</div>