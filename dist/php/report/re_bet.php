<?php

require_once("common.php");
$common_class = new CommonClass();
$paramsb['startTime'] =  '00:00:00';
$paramsb['endTime'] = '23:59:59';
$paramsb['betTimeBegin'] = $beginTime;
$paramsb['betTimeEnd'] = $endTime;
unset( $paramsb['pageSize'] );
$paramsb['pageLimit'] = 10 ;
$md5_keys = md5( $paramsb['siteId'].$paramsb['username'].$paramsb['liveId'].$paramsb['gameKind'].$paramsb['gameType'].$paramsb['betTimeBegin'].$paramsb['betTimeEnd'].$paramsb['startTime'].$paramsb['endTime'] );
$paramsb['key'] = "12345".$md5_keys."963852";

$paramsb = json_encode($paramsb);

$res = $clientB->NewPostData(MY_HTTP_CENTER_HOST . 'listDetailReport', $paramsb);
//print_r($res);
//die;
$ret = json_decode($res, TRUE);
//echo json_decode();

if( ( $ret['returnCode'] == 900000 ) && ( !empty( $ret['dataList'] ) ) && (  $ret['dataList'] != '[]' ) ){
        if( is_string( $ret['dataList'] ) ){
            $data = array();
            $ret['dataList'] = json_decode( $ret['dataList'] );
            foreach( $ret['dataList'] as $key => $value ){
                $data[$key]['gameName']     = $value->gameName ;
                $data[$key]['billNo']       = $value->billNo ;
                $data[$key]['betamount']    = $value->betamount ;
                $data[$key]['winloseType']  = $value->winloseType ;
            }
            $ret['dataList'] = $data ;
//            $count = count($data);
        }
//    print_r($data);

        foreach ($ret['dataList'] as $key => $value) {
            ?>
            <tr>
                <td style="width:130px;text-align:center;"><?php echo $value['gameName']; ?></td>
                <td style="width:300px;text-align:center;"><?php echo $value['billNo']; ?></td>
                <td style="width:130px;text-align:center;"><?php echo $value['betamount']; ?></td>
                <td style="width:130px;text-align:center;"><?php if ($value['winloseType'] == -1) {
                        echo '取消';
                    } elseif ($value['winloseType'] == 1) {
                        echo '输';
                    } elseif ($value['winloseType'] == 2) {
                        echo '赢';
                    } elseif ($value['winloseType'] == 3) {
                        echo '和';
                    } else {
                        echo '未结算';
                    } ?></td>
            </tr>
            <?php
        }
//        $total = $res['total']['betCount'] ? $res['total']['betCount'] : $count ;
        $pages = $common_class->getpageurl( $ret['pagation']['totalNumber'], $ret['pagation']['page'], 10, 're_deal');
        ?>
        <tr>
            <td colspan="5"><?php echo $pages; ?></td>
        </tr>
        <?php

}else{
    ?>
        <tr>
            <td colspan="4"><span>暂无投注记录</span></td>
        </tr>
<?php
}
?>





