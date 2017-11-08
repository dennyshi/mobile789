<?php
require_once("common.php");
$common_class = new CommonClass();
$fromKey = '60000,60001,';

for ($i = 20001; $i <= 20018; $i++) {
    $fromKey .= $i . ',';
}
$fromKeyType = trim($fromKey, ',');
$paramsb['fromKeyType'] = $fromKeyType;
$res = $clientB->NewPostData(MY_HTTP_MONEY_HOST . 'memberMoneyLog', $paramsb);
$ret = json_decode($res, TRUE);

if ( ( $ret['code'] == '100000' ) && ( !empty( $ret['data'] ) ) && (  $ret['data'] != '[]' ) ) {
    if ($ret['data']) {
        foreach ($ret['data'] as $i => $v) {
            $mtype = isset($formKeyType[$v['fromKeyType']]) ? $formKeyType[$v['fromKeyType']] : '测试数据';
            //$return['data'][$i]['mnowmoney'] = sprintf("%.2f", $v['afterMoney']);
            if ($v['transType'] == 'in') {
                $mgold = sprintf("%.2f", $v['remit']);
            } else {
                $mgold = '<span style="color:red;"> -' . sprintf("%.2f", $v['remit']) . '</span>';
            }
            $mnote = $v['memo'];
            ?>
            <tr>
                <td><?php echo $i+1; ?></td>
                <td><?php echo date("Y-m-d H:i:s", $v['createTime'] / 1000); ?></td>
                <td><?php echo $mtype; ?></td>
                <td><?php echo $mnote; ?></td>
                <td><?php echo $mgold; ?></td>
            </tr>
            <?php
        }
    }

    $pages = $common_class->getpageurl($ret['pagation']['totalNumber'], $ret['pagation']['page'], 10,  "re_change");


    ?>
    <tr>
        <td colspan="5"><?php echo $pages; ?></td>
    </tr>
    <?php

} else {
    ?>
    <tr>
        <td   colspan="5">暂无转账记录</td>
    </tr>
    <?php
}
?>






