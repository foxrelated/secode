<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: module-detail.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<div class="global_form_popup sr_sitemenu_view">
  <div class="top clr">
    <h3>
      <?php
      echo 'Module Details';
      $item = $this->moduleItem;
      ?>
    </h3>
  </div>
  <table class="clr">

    <tr>
      <td width="250"><b><?php echo "ID"; ?></b></td>
      <td><?php
        if (!empty($item->module_id)) :
          echo $item->module_id;
        else :
          echo 'N/A';
        endif;
        ?>
      </td>
    </tr>

    <tr>
      <td width="250"><b><?php echo "Content Module"; ?></b></td>
      <td><?php
        if (!empty($item->module_name)) :
          echo $item->module_name;
        else :
          echo 'N/A';
        endif;
        ?>
      </td>
    </tr>

    <tr >
      <td><b><?php echo "Content Title"; ?></b></td>
      <td><?php
        if (!empty($item->module_title)) :
          echo $item->module_title;
        else :
          echo 'N/A';
        endif;
        ?>
      </td>
    </tr>

    <tr>
      <td><b><?php echo "Database Table Item"; ?></b></td>
      <td><?php
        if (!empty($item->item_type)) :
          if (strstr($item->item_type, "sitereview")) :
            echo "sitereview_listing";
          else :
            echo $item->item_type;
          endif;
        else :
          echo 'N/A';
        endif;
        ?>
      </td>
    </tr>

    <tr>           
      <td><b><?php echo "Owner Field"; ?></b></td>
      <td><?php
        if (!empty($item->owner_field)) :
          echo $item->owner_field;
        else :
          echo 'N/A';
        endif;
        ?>
      </td>
    </tr>  

    <tr>
      <td><b><?php echo "Title Field"; ?></b></td>
      <td><?php
        if (!empty($item->title_field)) :
          echo $item->title_field;
        else :
          echo 'N/A';
        endif;
        ?> 
      </td>
    </tr>

    <tr>
      <td><b><?php echo "Body Field"; ?></b></td>
      <td><?php
        if (!empty($item->body_field)) :
          echo $item->body_field;
        else :
          echo 'N/A';
        endif;
        ?>
      </td>
    </tr>

    <tr>
      <td><b><?php echo "Like Field"; ?></b></td>
      <td><?php
        if (!empty($item->like_field)) :
          echo $item->like_field;
        else :
          echo 'N/A';
        endif;
        ?></td>
    </tr>     

    <tr>
      <td><b><?php echo "Comment Field"; ?></b></td>
      <td><?php
        if (!empty($item->comment_field)) :
          echo $item->comment_field;
        else :
          echo 'N/A';
        endif;
        ?></td>
    </tr>

    <tr>
      <td><b> <?php echo "Date Field"; ?></b></td>
      <td><?php
        if (!empty($item->date_field)) :
          echo $item->date_field;
        else :
          echo 'N/A';
        endif;
        ?> </td>
    </tr>

    <tr>
      <td><b><?php echo "Featured Field"; ?></b></td>
      <td><?php
        if (!empty($item->featured_field)) :
          echo $item->featured_field;
        else :
          echo 'N/A';
        endif;
        ?></td>
    </tr>

    <tr>
      <td><b><?php echo "Sponsored Field"; ?></b></td>
      <td><?php
        if (!empty($item->sponsored_field)) :
          echo $item->sponsored_field;
        else :
          echo 'N/A';
        endif;
        ?></td>
    </tr>

    <tr>           
      <td><b><?php echo 'Database Category Table Item'; ?></b></td>
      <td>
        <?php
        if (!empty($item->category_name)) :
          echo $item->category_name;
        else :
          echo 'N/A';
        endif;
        ?>
      </td>
    </tr>
    
    <tr>           
      <td><b><?php echo 'Category Title Column Name'; ?></b></td>
      <td>
        <?php
        if (!empty($item->category_title_field)) :
          echo $item->category_title_field;
        else :
          echo 'N/A';
        endif;
        ?>
      </td>
    </tr>
  </table>
  <br />
  <button  onclick='javascript:parent.Smoothbox.close()' ><?php echo 'Close' ?></button>
</div>