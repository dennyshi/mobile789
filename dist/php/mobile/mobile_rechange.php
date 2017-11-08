<?php
require_once("../base/__config.php");
$t = $_REQUEST ;
$params['username'] = $t['username'];
$params['oid'] = $t['oid'];
$params['site_id'] = SITE_ID;
$params['ulevel'] = $t['ulevel'];

//$params['username'] = "yilufaqqq85";
//$params['oid'] = "2f9b4ca7ce74f2677dc1f5ebe4991fbb";
//$params['site_id'] = SITE_ID;
//$params['ulevel'] = "200";

if( $action == "bank" ){
    $params['ty'] = 'bank';
}elseif( $action == "zhifubao" ){
    $params['ty'] = 'zhifubao';
}elseif( $action == "wechat" ){
    $params['ty'] = 'wechat';
}

?>