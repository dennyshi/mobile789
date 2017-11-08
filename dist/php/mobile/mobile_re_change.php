<?php

require_once("../report/common.php");

$common_class = new CommonClass();
$fromKey = '60000,60001,';

for ($i = 20001; $i <= 20018; $i++) {
    $fromKey .= $i . ',';
}
$fromKeyType = trim($fromKey, ',');
$paramsb['fromKeyType'] = $fromKeyType;

$res = $clientB->NewPostData(MY_HTTP_MONEY_HOST . 'memberMoneyLog', $paramsb);
//print_r($res);die;
$ret = json_decode($res, TRUE);

$data['param'] = $paramsb;
$data['return'] = $ret;
header('Content-type:text/json');
//echo  json_encode($data);


if ( ( $ret['code'] == '100000' ) && ( !empty( $ret['data'] ) ) && (  $ret['data'] != '[]' ) ) {
    if ($ret['data']) {
        $re_bet_info = array();
        $re_bet_info = $ret ;
        foreach ($ret['data'] as $i => $v) {
            $re_bet_info['data'][$i]['fromKeyType'] = isset($formKeyType[$v['fromKeyType']]) ? $formKeyType[$v['fromKeyType']] : '测试数据';
            //$return['data'][$i]['mnowmoney'] = sprintf("%.2f", $v['afterMoney']);
            if ($v['transType'] == 'in') {
                $re_bet_info['data'][$i]['remit'] = sprintf("%.2f", $v['remit']);
            } else {
                $re_bet_info['data'][$i]['remit'] = "-" . sprintf("%.2f", $v['remit']);
            }
            $re_bet_info['data'][$i]['memo'] = $v['memo'];

            $re_bet_info['data'][$i]['createTime'] = date('Y-m-d H:i:s',$v['createTime']/1000 );

        }
    }
    echo json_encode($re_bet_info);
//    return json_encode($re_bet_info);
//    $pages = $common_class->getpageurl($ret['pagation']['totalNumber'], $ret['pagation']['page'], 10,  "re_change");
} else {
    $ret['code'] = '20000';
    echo json_encode($ret);
}
?>
