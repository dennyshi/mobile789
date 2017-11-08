<?php

class Fetch {
    public $debug = FALSE;
    public function __construct() {
        header("Content-Type: text/html; charset=utf-8");
    }
    /*
    * 新接口统一POST提交请求
    */
    public function NewPostData($url,$param,$type=1) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if($type == 2){//java调用设置post头
            $header[] = 'Accept:application/json; q=0.01';
            $header[] = 'Content-Type:application/json; charset=utf-8';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $html_data = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http_status == '200') {
            if ($this->debug) {
                echo '<br>访问地址:', $url, '<br>返回内容:';
                echo '<pre>';
                print_r($html_data);
                echo '</pre>';
            }
            return $html_data;
        } else {
            if ($this->debug) {
                echo 'http_status:' . $http_status . '<br>';
                echo '<br>访问地址:', $url, '<br>';
                echo '连接超时或无法获取数据';
                 echo '<br>请求参数：<br>';
                echo '<pre>';
                print_r($param);
                echo '</pre>';
            }
            return '';
        }
    }
}
