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

<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
?>

<script type="text/javascript">
    var dateAction = function(start_date, end_date) {
        $('start_date').value = start_date;
        $('end_date').value = end_date;
        $('filter_form_archives').submit();
    }
</script>

<form id='filter_form_archives' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'siteevent_general', true) ?>' style='display: none;'>
	<input type="hidden" id="start_date" name="start_date"  value=""/>
	<input type="hidden" id="end_date" name="end_date"  value=""/>
</form>

<?php if (count($this->archive_siteevent)): ?>
    <ul class="seaocore_sidebar_list">
        <?php foreach ($this->archive_siteevent as $archive): ?>
            <li>
                <a href='javascript:void(0);' onclick='javascript:dateAction(<?php echo $archive['date_start'] ?>, <?php echo $archive['date_end'] ?>);' <?php
                if ($this->start_date == $archive['date_start'])
                    echo " class='bold'";
                ?>><?php echo $archive['label'] ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>