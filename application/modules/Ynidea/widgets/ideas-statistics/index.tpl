<ul class = "global_form_box" style="margin-bottom: 15px;">
  <div>
      <div class="ynidea_statistic_field">
            <?php echo $this->translate('Trophies');?>
      </div>
      <div class="ynidea_statistic_info">
            <?php echo Ynidea_Api_Core::getCountTrophy(); ?>
      </div>
  </div>
  <br/>
    <div>
      <div class="ynidea_statistic_field">
             <?php echo $this->translate('Ideas');?>
      </div>
      <div class="ynidea_statistic_info">
             <?php echo Ynidea_Api_Core::getCountIdea();?>
      </div>
    </div>
  <br/>
    <div>
      <div class="ynidea_statistic_field">
             <?php echo $this->translate('Awards');?>
      </div>
      <div class="ynidea_statistic_info">
             <?php echo Ynidea_Api_Core::getCountAward();?>
      </div>
    </div>
 </ul>