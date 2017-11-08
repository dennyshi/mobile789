<?php
ini_set("display_errors", "on");
require_once("../base/__config.php");
$t = $_REQUEST;
$params['site_id'] = SITE_ID ;
if( isset($t['platform']) ){
    $params['platform'] = $t['platform'] ;
}
if( isset($t['gametype']) ){
    $params['gametype'] = $t['gametype'] ;
    if( $params['gametype'] == "all" ){
        unset($params['gametype']);
    }
    if( $params['gametype'] == "Table Card" ){
        $params['gametype'] = "Table & Card";
    }
}
if( isset($t['page'])){
    $params['page'] = $t['page'];
}
if( isset($t['page_size']) ){
    $params['page_size'] = $t['page_size'];
}

if( empty($params['page_size']) ){
    $params['page_size'] = 16;
}
$params['hg_is_html'] = 1;
$get_game_info = $clientA->get_game_info($params); //获取游戏图片

echo $get_game_info;

?>
