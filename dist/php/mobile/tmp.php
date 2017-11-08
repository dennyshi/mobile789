<?php
//echo 1111;
ini_set("display_errors", "on");
require_once("../base/__config.php");
$tmp = $_REQUEST;
//print_r(2222);
//print_r($tmp);
if( $tmp['name11'] ){
    $t[$tmp['name11']] = $tmp['name12'];
}
if( $tmp['name21'] ){
    $t[$tmp['name21']] = $tmp['name22'];
}
if( $tmp['name31'] ){
    $t[$tmp['name31']] = $tmp['name32'];
}
if( $tmp['name41'] ){
    $t[$tmp['name41']] = $tmp['name42'];
}
if( $tmp['name51'] ){
    $t[$tmp['name51']] = $tmp['name52'];
}
if( $tmp['name61'] ){
    $t[$tmp['name61']] = $tmp['name62'];
}
if( $tmp['name71'] ){
    $t[$tmp['name71']] = $tmp['name72'];
}
if( $tmp['name81'] ){
    $t[$tmp['name81']] = $tmp['name82'];
}
if( $tmp['name91'] ){
    $t[$tmp['name91']] = $tmp['name92'];
}

//上面的是固定模式，要调只能条下面这块

//print_r($t);
$params['site_id'] = SITE_ID ;
$params['platform'] = $t['platform'] ;
$params['gametype'] = $t['gametype'] ;
$params['page'] = $t['page'];
$params['page_size'] = $t['page_size'];
//print_r($params);
//echo json_encode( $tmp );die;
//die;


if( empty($params['page']) ){
    $params['page'] = 1;
}
if( empty($params['page_size']) ){
    $params['page_size'] = 10;
}

//$params['site_id'] = SITE_ID ;
//$params['platform'] = "13" ;
//$params['gametype'] = "" ;
//$params['page'] = 3;
//$params['page_size'] = 10;
//$t = file_get_contents('php://input') ;
//$t = json_decode($t,true);
//$params['username'] = $t['username'];
//$params['site_id'] = SITE_ID;
//$params['oid'] = $t['oid'];
//$params['ulevel'] = $ulevel = $t['ulevel'];
//$agent =$t['agent'];
$get_game_info = $clientA->get_game_info($params); //获取游戏图片
//header('Content-type:text/json');
echo json_encode($get_game_info);
//print_r($get_game_info);die;

?>
