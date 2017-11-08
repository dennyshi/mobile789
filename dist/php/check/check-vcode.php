<?php
session_start();
header('Access-Control-Allow-Origin:*');

// 响应类型
header('Access-Control-Allow-Methods:GET,POST,PUT');
header('Access-Control-Allow-Headers:x-requested-with,content-type');


// $t = json_decode($GLOBALS['HTTP_RAW_POST_DATA'],TRUE);
$t=$_REQUEST;
$vcode = $t['name'];
$vcodes = array();
$vcodes['vcode'] = $vcode;
$vcodes['vcode_session'] = $_SESSION['vcode_session'];
if ($vcode != (isset($_SESSION['vcode_session']) ? $_SESSION['vcode_session'] : null)) {
    echo 'false';
}else{
    echo 'true';
}
