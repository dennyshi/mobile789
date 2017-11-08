<?php
// echo 888;
// print_r(__DIR__);

// die;
require_once(__DIR__.'/common.php');
$common_class = new CommonClass();
$fromKey = '';
if( $paramsb['re_deal_code'] == 0 ){
    for ($i = 10001; $i <= 10067; $i++) {
        $fromKey .= $i . ',';
    }
    for ($i = 20001; $i <= 20018; $i++) {
        $fromKey .= $i . ',';
    }
    for ($i = 60000; $i <= 60001; $i++) {
        $fromKey .= $i . ',';
    }

}else if( $paramsb['re_deal_code'] == 1 ){
    $fromKey = implode(  ",", array_keys( $add_money_code ) );
}else if( $paramsb['re_deal_code'] == 2 ){
    $fromKey = implode(  ",", array_keys( $reduce_money_code ) );
}else if( $paramsb['re_deal_code'] == 3 ){
    $fromKey = implode(  ",", array_keys( $zhuang_zhang_code ) );
}

$fromKeyType = trim($fromKey, ',');
$paramsb['fromKeyType'] = $fromKeyType;

$res = $clientB->NewPostData(MY_HTTP_MONEY_HOST . 'memberMoneyLog', $paramsb);

echo CommonClass::ajax_return($res, $jsonp, $jsonpcallback);
?>
