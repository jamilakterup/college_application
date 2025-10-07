<?php
if(isset($_GET['code']))
$code=$_GET['code'];
define('IN_CB',true);

include('include/index.php');
include('include/FColor.php');
include('include/BarCode.php');
include('include/FDrawing.php');

include('include/code39.barcode.php');


$color_black = new FColor(0,0,0);
$color_white = new FColor(255,255,255);


$code_generated = new code39(30,$color_black,$color_white,2,$code,2);


$drawing = new FDrawing(1024,1024,'',$color_white);
$drawing->init(); // You must call this method to initialize the image
$drawing->add_barcode($code_generated);
$drawing->draw_all();
$im = $drawing->get_im();


$im2 = imagecreate($code_generated->lastX,$code_generated->lastY);
imagecopyresized($im2, $im, 0, 0, 0, 0, $code_generated->lastX, $code_generated->lastY, $code_generated->lastX, $code_generated->lastY);
$drawing->set_im($im2);


header('Content-Type: image/png');


$drawing->finish(IMG_FORMAT_PNG);
?>