<?php
require_once("../report/common.php");
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
//print_r($res);die;

$ret = json_decode($res, TRUE);

if ( ( $ret['code'] == '100000' ) && ( !empty( $ret['data'] ) ) && (  $ret['data'] != '[]' ) ) {
    if ($ret['data']) {
        $re_bet_info = array();
        $re_bet_info = $ret ;
        foreach ($ret['data'] as $i => $v) {
            $re_bet_info['data'][$i]['fromKeyType'] = isset($formKeyType[$v['fromKeyType']]) ? $formKeyType[$v['fromKeyType']] : '';
            if ($v['transType'] == 'in') {
                $re_bet_info['data'][$i]['remit'] = sprintf("%.2f", $v['remit']);
            } else {
                $re_bet_info['data'][$i]['remit'] =  "-" . sprintf("%.2f", $v['remit']);
            }

            $re_bet_info['data'][$i]['createTime'] = date('Y-m-d H:i:s',$v['createTime']/1000 );
            $re_bet_info['data'][$i]['memo'] = $v['memo'];
            if (strstr($v['memo'], '操作者')) {
                $mn = explode('(', $v['memo']);
                if (isset($mn[1])) {
                    $re_bet_info['data'][$i]['memo'] = substr($mn[1], 0, -1);
                }
            }
        }
        echo json_encode($re_bet_info);
//        $pages = $common_class->getpageurl($ret['pagation']['totalNumber'], $ret['pagation']['page'], 10,  're_deal');
    } else {
        $ret['code'] = '100001';
        echo json_encode($ret);
    }
} else {
    echo json_encode($ret);
}
?>
