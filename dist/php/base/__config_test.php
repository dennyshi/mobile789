<?php

// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');

// 响应类型
header('Access-Control-Allow-Methods:GET,POST,PUT');
header('Access-Control-Allow-Headers:x-requested-with,content-type');


header("Content-Type:text/html; charset=utf-8");
$t =  file_get_contents('php://input');
$_POST = json_decode($t,true);

$host = $_SERVER['HTTP_HOST'];
define("SITE_ID", 2018);
define("SITE_TYPE", 1);
define("IMG_SITE_ID", 49);
define("DS_SS_HANDICP", 'B');
define("DS_XG_HANDICP", 'C');
define("MY_HTTP_HOST", 'http://api.hdc100.ph/api.php');
define("MY_HTTP_HOST_URL", 'http://api.hdc100.ph');
define("NEW_KEYB", '11_2');


if (function_exists('date_default_timezone_set')) {
    @date_default_timezone_set('Etc/GMT-8');
}
// echo 111;
// print_r(__DIR__);die;
// print_r($_SERVER['HTTP_HOST']);die;

require_once(__DIR__."/src/Hprose.php");
// echo 999;die;
require_once(__DIR__."/CommonClass.php");
require_once(__DIR__."/__config_more.php");
require_once(__DIR__."/Fetch.class.php");
