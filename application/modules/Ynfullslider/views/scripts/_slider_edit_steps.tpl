<div class="ynfullslider_global_content">
    <div class="ynfullslider_create_tabs">
        <?php $stepIndex = 1 ?>
        <?php $currentStepIndex = $this->currentStepIndex ?>
        <?php foreach($this->steps as $step => $stepTitle): ?>
            <?php
                $stepClass = '';
                if ($stepIndex == $currentStepIndex)
                    $stepClass = 'ynfullslider-edit-step-current';
                if ($stepIndex < $currentStepIndex)
                    $stepClass = 'ynfullslider-edit-step-completed';
            ?>
            <?php if($this->slider_id): ?>
                <?php echo $this->htmlLink(array(
                        'route'=>'admin_default',
                        'module'=>'ynfullslider',
                        'controller'=>'slider',
                        'action'=>$step,
                        'id'=>$this->slider_id
                    ),
                        '<span>'.$stepIndex.'</span>'.$this->translate($stepTitle),
                    array('class' => $stepClass)
                ) ?>
            <?php else: ?>
                <a href="javascript:void(0);" class = "<?php echo $stepClass ?>">
                    <span>
                        <?php echo $stepIndex ?>
                    </span>
                        <?php echo $this->translate($stepTitle) ?>
                </a>
            <?php endif; ?>
            <?php $stepIndex += 1 ?>
        <?php endforeach; ?>
    </div>
</div>