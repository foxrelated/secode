<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php
	$baseUrl = $this->layout()->staticBaseUrl;
	$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/List/externals/styles/style_list.css');
?>
<script type="text/javascript">
  var dateAction =function(start_date, end_date){ 
    $('start_date').value = start_date;
    $('end_date').value = end_date;
    $('filter_form').submit();
  }
</script>
<?php if (count($this->archive_list)): ?>
<ul class="seaocore_sidebar_list">
	<?php foreach ($this->archive_list as $archive): ?>
	  <li>
	    <a href='javascript:void(0);' onclick='javascript:dateAction(<?php echo $archive['date_start'] ?>, <?php echo $archive['date_end'] ?>);' <?php if ($this->start_date == $archive['date_start'])
	    echo " class='bold'"; ?>><?php echo $archive['label'] ?></a>
	  </li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>