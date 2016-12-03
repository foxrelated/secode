<?php
/**
 * SocialEnginePro
 *
 * @package JetBrains PhpStorm.
 * @autor: azim
 * Date: 29.01.13
 *
 */
?>
<ul class="generic_list_widget">
    <li>
        <span class="money_icon">
            <?php echo $this->translate('Balance: %1s', $this->locale()->toCurrency($this->balans, $this->currency))?>
        </span>
    </li>
</ul>    
