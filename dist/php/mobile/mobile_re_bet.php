<?php

require_once("../report/common.php");
$common_class = new CommonClass();
$paramsb['startTime'] =  '00:00:00';
$paramsb['endTime'] = '23:59:59';
unset( $paramsb['pageSize'] );
$paramsb['pageLimit'] = 10;
$paramsb['betTimeBegin'] = $beginTime;
$paramsb['betTimeEnd'] = $endTime;
$md5_keys = md5( $paramsb['siteId'].$paramsb['username'].$paramsb['liveId'].$paramsb['gameKind'].$paramsb['gameType'].$paramsb['betTimeBegin'].$paramsb['betTimeEnd'].$paramsb['startTime'].$paramsb['endTime'] );
$paramsb['key'] = "12345".$md5_keys."963852";

$paramsb = json_encode($paramsb);
//echo $paramsb;
//echo "<br>";
//echo 6666;
//echo "<br>";
$res = $clientB->NewPostData(MY_HTTP_CENTER_HOST . 'listDetailReport', $paramsb);
//echo $res;
$ret = json_decode($res, TRUE);
//print_r($ret);
if( ( $ret['returnCode'] == 900000 ) && ( !empty( $ret['dataList'] ) ) && (  $ret['dataList'] != '[]' ) ){

    if( is_array( $ret['dataList'] ) ){
        $data = array();
//        $ret['dataList'] = json_decode( $ret['dataList'] );
        foreach( $ret['dataList'] as $key => $value ){
            $data[$key]['gameName']     = $value['gameName'] ;
            $data[$key]['billNo']       = $value['billNo'] ;
            $data[$key]['betamount']    = $value['betamount'] ;
            $data[$key]['winloseType']  = $value['winloseType'] ;
            $data[$key]['betTimes']  = $value['betTime'] ;
            $data[$key]['betTime'] = date('Y-m-d H:i:s',$data[$key]['betTimes']/1000);
//            print_r($data);die;
        }
        $ret['dataList'] = $data ;
    }
    $data = $ret;
    echo json_encode($data);
//    $pages = $common_class->getpageurl( $ret['pagation']['totalNumber'], $ret['pagation']['page'], $ret['pagation']['pageSize'], 're_bet');
}else {
    echo $res;
}
?>





