<?php
session_start();
$phone = explode(", ", $_SESSION['phone']);
$phone1 = isset($phone[0]) ? $phone[0] : '';
$phone2 = isset($phone[1]) ? $phone[1] : '';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <style>
        .head {font: 18px Tahoma; text-align:center}
        .ticket {display:block; float:left; width:105px; text-align:right; margin-right:10px}
        .line {font:14px Tahoma; padding:5px;}
        #tel {font:normal 13px Tahoma; text-align:center}
    </style>
</head>
<body>
<div class='head'><?php echo $_SESSION['travel']; ?></div>
<div id='tel'>
    Tel: <?php echo $phone1; ?><br />
    <div style='padding-left:30px'><?php echo $phone2; ?></div>
</div>
<div style='text-align:center'>------------------------------------------</div>
<div style='font-style:italic; text-align:center; font-size:17px; margin-bottom:15px'>
    <span style='font-size:14px'>Service provided by</span><br>
    <b>www.travelhub.com.ng</b>
</div>

<div id='details'></div>
</body>
</html>