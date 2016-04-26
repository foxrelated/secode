<?php
if(!session_id())
    session_start();
     $payer_status = @$_POST['payer_status'];
     if($payer_status != "verified")
        $payer_status = "unverified";
     $pstatus = $_REQUEST['pstatus'];
     $is_complete = @$_POST['payment_status'];
     $req4 = $_REQUEST['req4'];
     $req5 = $_REQUEST['req5'];
     $url = curPageURL();
     $index = strpos($url,"/application/");
     $core_path = substr($url,0,$index);
     
     if($pstatus =='success' && $payer_status =="verified" && $is_complete == "Completed")
     {
         header('Location:'.$core_path.'/admin/groupbuy/request/1/req5/'.$pstatus.'/req6/'.$req4);
         exit;
     }
     if($pstatus == 'success' && $payer_status =="verified")
     {
         header('Location:'.$core_path.'/admin/groupbuy/request/1/req5/'.$pstatus.'/req6/'.$req5);
         exit;
     }
     header('Location:'.$core_path.'/admin/groupbuy/request/1/req5/'.$pstatus);
     
?>
<?php
     function curPageURL() {
         $pageURL = 'http';
     if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        $pageURL .= "://";
     if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
     } else {
         $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     }
     return $pageURL;
    }
?>
