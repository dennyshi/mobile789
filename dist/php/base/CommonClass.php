<?php

class CommonClass {

    public function __construct() {

    }

    static function get_user_ip() {
        if (@$_SERVER["HTTP_X_FORWARDED_FOR"]) {
            $ips = explode(',', $_SERVER["HTTP_X_FORWARDED_FOR"]);
            $ip = $ips[0];
        } else if (@$_SERVER["HTTP_X_REAL_IP"])
            $ip = $_SERVER["HTTP_X_REAL_IP"];
        else if (@$_SERVER["HTTP_CLIENT_IP"])
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        else if (@$_SERVER["REMOTE_ADDR"])
            $ip = $_SERVER["REMOTE_ADDR"];
        else if (getenv("HTTP_X_REAL_IP"))
            $ip = getenv("HTTP_X_REAL_IP");
        else if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "Unknown";
        return $ip;
    }

    static function get_md5_pwd($pwd) {
        return trim($pwd);
    }

    /**
     * ajax返回包装
     * @param type $params 返回的参数
     * @param type $jsonpcallback 跨域回调函数名
     * @param type $jsonp 类型json，jsonp,
     */
    static function ajax_return($params, $jsonp = 'json', $jsonpcallback = '') {
        $re = '';
        if ($jsonp == 'json') {
            $re = json_encode($params);
        } else if ($jsonp == 'jsonp') {
            $re = $jsonpcallback . "(" . json_encode($params) . ")";
        }
        return $re;
    }

    static function check_username_str($str, $minlength, $maxlength) {
        $reg = "/^[a-z0-9]+$/";
        if (preg_match($reg, $str)) {
            $len = strlen($str);
            if ($len < $minlength || $len > $maxlength) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

    static function check_truename_str($str) {
        $Ch = "/^[\x{4e00}-\x{9fa5}]+$/u";
        $En = "/^([a-zA-Z]+)$/";
        if (preg_match($Ch, $str) || preg_match($En, $str)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function check_email_str($str) {
        if (filter_var($str, FILTER_VALIDATE_EMAIL) === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    static function check_agentname_str($str, $minlength, $maxlength) {
        $reg = "/^d[a-z0-9]+$/";
        if (preg_match($reg, $str)) {
            $len = strlen($str);
            if ($len < $minlength || $len > $maxlength) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

    static function check_password_str($str, $minlength, $maxlength) {
        $reg = "/^[A-Za-z0-9]+$/";
        if (preg_match($reg, $str)) {
            $len = strlen($str);
            if ($len < $minlength || $len > $maxlength) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

    static function check_mobile_str($str) {
        $reg = "/^1[0-9]{10}/";
        if (preg_match($reg, $str)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function check_qq_str($str) {
        $reg = "/^[0-9]+$/";
        if (preg_match($reg, $str)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function check_money($money) {
        $reg = "/^\d+(\.\d+)?$/";
        if (preg_match($reg, $money)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 生成base64加密的sessionkey，方便电子游戏调用
     * 随机7位+去掉两位等号(base64_encode(site_id + _ + 用户名))+随机两位
     * @param type $username
     * @return type
     */
    static function get_session_key_forgane($username) {
        $data = strtoupper(md5(rand(11111, 99999) . time() . 'aadsadsdsafdsafdsafdafhjhjklhabbcc'));
        $str1 = substr($data, 2, 7);
        $str2 = substr($data, 12, 2);
        $key = SITE_ID . '_' . $username;
        return $str1 . str_replace('=', '', base64_encode($key)) . $str2;
    }

    /**
     * 生成key作为调用各平台的api参数
     * @param type $str
     * @param type $pre
     * @param type $next
     * @return type
     */
    static function get_key_param($str, $pre, $next) {
        $data = strtolower(md5(rand(11111, 99999) . time() . 'aadsadsdsafdsafdsafdafhjhjklhabbcc'));
        $str1 = substr($data, 2, $pre);
        $str2 = substr($data, 12, $next);
        return $str1 . md5($str) . $str2;
    }

    /**
     * 生成订单编号
     * @param type $company
     * @param type $username
     * @param type $money
     * @return type
     */
    static function get_billno($company, $username, $money = '') {
        return substr(md5($money . $username . $company . time() . 'aadsadsdsafdsafdsafdafhjhjklhabbcc' . rand(11111, 99999)), 8, -8);
    }

    /**
     * 需要配置环境支持iconv，否则中文参数不能正常处理
     * @param type $data
     * @param type $key
     * @return type
     */
    static function dcEncrypt($data, $key) {
        $key = iconv("GB2312", "UTF-8", $key);
        $data = iconv("GB2312", "UTF-8", $data);

        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*", md5($key));
        }
        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;
        return md5($k_opad . pack("H*", md5($k_ipad . $data)));
    }

    static function getLoadingPage($logo, $color = '#3d3d3d') {
        if ($color == '#ffffff') {
            $fcolor = '#3d3d3d';
        } else {
            $fcolor = 'white';
        }
        $str = '<html><head><style>
            body{background-color:' . $color . '; text-align:center}
                </style>

        </head><body>
        <div style="width: 1000px; height: 197px; position: absolute; top: 50%; left: 50%; margin-left: -500px; margin-top: -100px;">
            <div style="background:url(http://static.ds88online.com/public/images/Loading/' . $logo . '.png?i=12) no-repeat;height: 61px;width: 134px;margin: 0 auto;"></div>
         <br>
         <div style="background:url(http://static.ds88online.com/public/images/Loading/loading1.gif) no-repeat;height: 23px;width: 220px;margin: 0 auto;"></div>
         <br>
         <span><a style="font: small-caption; color:' . $fcolor . ';">加载中，请稍候...</a></span>
         </div>
        </body></html>';
        return $str;
    }

    static function is_wap() {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        $uachar = "/(nokia|sony|ericsson|mot|samsung|sgh|lg|sie|philips|panasonic|alcatel|lenovo|cldc|midp|wap|mobile)/i";
        if (($ua == '' || preg_match($uachar, $ua)) && !strpos(strtolower($_SERVER['REQUEST_URI']), 'wap')) {
            return true;
        } else {
            return false;
        }
    }

    //DS开牌结果
    static function disassemble_type($bankerResult) {
        $poker_number = array(array('1' => '庄', '2' => '闲', '3' => '和'), array('', '庄对', '闲对', '庄对闲对'), array('1' => '小', '2' => '大'));
        if (!empty($bankerResult)) {
            $poker_result = array();
            foreach ($bankerResult as $pk_child_key => $pk_child_val) {
                if ($pk_child_key == 3) {
                    break;
                }
                $poker_result[] = $poker_number[$pk_child_key][$pk_child_val];
            }
            $poker_result[3] = $bankerResult[3];
            return $poker_result;
        }
    }

    //DS牌型分解
    static function disassemble_poker($poker_array) {
        $poker_point = array();
        foreach ($poker_array as $point_key => $point_val) {
            foreach ($point_val as $point_child_key => $point_child_val) {
                $poker_point[$point_key][$point_child_key] = $point_child_val;
            }
        }
        return $poker_point;
    }

    //DS会员下注详情
    static function amount_bet($details) {
        $xztype = array('BC_BANKER' => '庄', 'BC_PLAYER' => '闲', 'BC_TIE' => '和', 'BC_BANKER_PAIR' => '庄对', 'BC_PLAYER_PAIR' => '闲对',
            'BC_BIG' => '大', 'BC_SMALL' => '小', 'BC_BANKER_INSURANCE' => '庄保险', 'BC_PLAYER_INSURANCE' => '闲保险');
        if (isset($details[0])) {
            $player_details = array();
            foreach ($details as $rd_child_key => $rd_child_val) {
                foreach ($rd_child_val as $xz_key => $xz_value) {
                    if ($xz_key == 'betType') {
                        $player_details[$rd_child_key][$xz_key] = $xztype[$xz_value];
                    } elseif ($xz_key == 'betTime') {
                        $player_details[$rd_child_key][$xz_key] = date('Y-m-d H:i:s', $xz_value / 1000);
                    } else {
                        $player_details[$rd_child_key][$xz_key] = $xz_value;
                    }
                }
            }
            return $player_details;
        }
    }

    //BBIN牌型分解
    static function disassemble_poker_bbin($poker_str) {
        $poker_bbin = explode('*', $poker_str);
        $poker_array = array();
        foreach ($poker_bbin as $pk_key => $pk_val) {
            $result[$pk_key] = explode(',', $pk_val);
        }
        foreach ($result as $re_key => $re_val) {
            foreach ($re_val as $child_key => $child_val) {
                $rechild = explode('.', $child_val);
                switch ($rechild[0]) {
                    case 'S':
                        $poker_array[$re_key][$child_key] = $rechild[1];
                        break;
                    case 'H':
                        $result_h = $rechild[1] + 13;
                        $poker_array[$re_key][$child_key] = "$result_h";
                        break;
                    case 'C':
                        $result_c = $rechild[1] + 26;
                        $poker_array[$re_key][$child_key] = "$result_c";
                        break;
                    case 'D':
                        $result_d = $rechild[1] + 39;
                        $poker_array[$re_key][$child_key] = "$result_d";
                        break;
                }
            }
        }
        return $poker_array;
    }

    static function pxceshi() {
        $ceshi_r = array("liveMemberReportDetails" => array(
                array("betType" => "BC_BANKER", "betAmount" => 10, "winLossAmount" => 19.5, "betTime" => 1432970871000),
                array("betType" => "BC_SMALL", "betAmount" => 10, "winLossAmount" => 0.0, "betTime" => 1432970770000)));
        $ceshi = '{"bankerResult":[2,2,2,8]}';

        $ceshi_d = '{"pokerList":[[25,51,8],[49,15,28]]}';

        $ceshi_c = 'H.6,C.7,D.13*C.12,S.13,C.13';

        $ceshi_r = CommonClass::amount_bet($ceshi_r); //DS会员下注详情
        $ceshi = CommonClass::disassemble_type($ceshi); //DS开牌结果
        $ceshi_d = CommonClass::disassemble_poker($ceshi_d); //DS牌型分解
        //$ceshi_c = CommonClass::disassemble_poker_bbin($ceshi_c);//BBIN牌型分解

        echo "<pre>";
        echo "<br />DS会员下注详情:<br />";
        print_r($ceshi_r);
        echo "<br /><br />DS开牌结果:<br />";
        print_r($ceshi);
        echo "<br /><br />DS牌型分解:<br />";
        print_r($ceshi_d);
        //echo "<br /><br />BBIN牌型分解:<br />";
        //print_r($ceshi_c);
        echo "</pre>";
    }

    static function get_notice_type($site_type, $type) {
        switch ($type) {
            case 1://滚动公告
                if ($site_type == 1) {//主站
                    $re = 1;
                } else if ($site_type == 2) {
                    $re = 11;
                }
                break;
            case 2://弹出公告
                if ($site_type == 1) {//主站
                    $re = 2;
                } else if ($site_type == 2) {
                    $re = 21;
                }
                break;
            case 3://会员中心表格显示
                if ($site_type == 1) {//主站
                    $re = 99;
                } else if ($site_type == 2) {
                    $re = 98;
                }
                break;
        }
    }

    public function get__re_change_page_html($currentPage, $totalPages) {
        $linkPage = ( $currentPage > 1 ) ? "<a target='_self' href='javascript:;' onclick='get_re_change_list(1)' class='prev disabled'>首页</a>" : '<span class="prev disabled">首页</span>';
        $linkPage .= ( $currentPage < $totalPages ) ? "&nbsp&nbsp<a target='_self'  href='javascript:;' onclick='get_re_change_list( $totalPages)' class='next pagegbk'>尾页</a>&nbsp&nbsp" : "&nbsp&nbsp<span class='next disabled'>尾页</span>&nbsp&nbsp";
        $linkPage .= '<span class="current">当前页:' . $currentPage . '</span>&nbsp&nbsp&nbsp&nbsp';
        $linkPage .= '共<strong>' . $totalPages . '</strong>页，跳转到：<input class="ipage" type="input" value="' . $currentPage . '" />';
        $linkPage .= " <a href='javascript:;' onclick='get_re_change_list( $(\".ipage\").val())'>确定</a>";
        return $linkPage;
    }

    public function get__re_deal_page_html($currentPage, $totalPages) {
        $linkPage = ( $currentPage > 1 ) ? "<a target='_self' href='javascript:;' onclick='get_re_deal_list(1)' class='prev disabled'>首页</a>" : '<span class="prev disabled">首页</span>';
        $linkPage .= ( $currentPage < $totalPages ) ? "&nbsp&nbsp<a target='_self'  href='javascript:;' onclick='get_re_deal_list( $totalPages)' class='next pagegbk'>尾页</a>&nbsp&nbsp" : "&nbsp&nbsp<span class='next disabled'>尾页</span>&nbsp&nbsp";
        $linkPage .= '<span class="current">当前页:' . $currentPage . '</span>&nbsp&nbsp&nbsp&nbsp';
        $linkPage .= '共<strong>' . $totalPages . '</strong>页，跳转到：<input class="ipage" type="input" value="' . $currentPage . '" />';
        $linkPage .= " <a href='javascript:;' onclick='get_re_deal_list( $(\".ipage\").val())'>确定</a>";
        return $linkPage;
    }

    public function get__re_bet_page_html($currentPage, $totalPages) {
        $linkPage = ( $currentPage > 1 ) ? "<a target='_self' href='javascript:;' onclick='get_re_bet_list(1)' class='prev disabled'>首页</a>" : '<span class="prev disabled">首页</span>';
        $linkPage .= ( $currentPage < $totalPages ) ? "&nbsp&nbsp<a target='_self'  href='javascript:;' onclick='get_re_bet_list( $totalPages)' class='next pagegbk'>尾页</a>&nbsp&nbsp" : "&nbsp&nbsp<span class='next disabled'>尾页</span>&nbsp&nbsp";
        $linkPage .= '<span class="current">当前页:' . $currentPage . '</span>&nbsp&nbsp&nbsp&nbsp';
        $linkPage .= '共<strong>' . $totalPages . '</strong>页，跳转到：<input class="ipage" type="input" value="' . $currentPage . '" />';
        $linkPage .= " <a href='javascript:;' onclick='get_re_bet_list( $(\".ipage\").val())'>确定</a>";
        return $linkPage;
    }

    public function getpageurl($count, $page, $lm = 10, $ty) {
        $totalpages = ceil($count / $lm);
        if ($totalpages <= 1) {
            return false;
        }
        if (!$page || $page < 1) {
            $page = 1;
        } else if ($page > $totalpages) {
            $page = $totalpages;
        }
        if ($ty == "re_change") {
            $pages = $this->get__re_change_page_html($page, $totalpages);
        } else if ($ty == "re_bet") {
            $pages = $this->get__re_bet_page_html($page, $totalpages);
        } else if ($ty == "re_deal") {
            $pages = $this->get__re_deal_page_html($page, $totalpages);
        }

        return $pages;
    }

    static function is_test_user($username, $usertype = -1) {
        if ($usertype == 10 || $usertype == 20) {
            return 1;
        }
        if ($usertype == 11 || $usertype == 21) {
            return 0;
        }
        $arr = ['shiwan01', 'shiwan02', 'shiwan03', 'shiwan04', 'shiwan05', 'shiwan06', 'shiwan07', 'shiwan08', 'shiwan09', 'shiwan10', 'denny123', 'denny234'];
        if (in_array($username, $arr)) {
            return 0;
        } else {
            return 1;
        }
    }

    static function get_user_os() {
        $agent = $_SERVER["HTTP_USER_AGENT"];
        static $os;
        if (isset($os)) {
            return $os;
        }
        if (preg_match("/win/i", $agent) && strpos($agent, "95")) {
            $os = "Windows 95";
        } else if (preg_match("/win 9x/i", $agent) && strpos($agent, "4.90")) {
            $os = "Windows ME";
        } else if (preg_match("/win/i", $agent) && preg_match('/98/', $agent)) {
            $os = "Windows 98";
        } else if (preg_match("/win/i", $agent) && preg_match('/nt 5.0/i', $agent)) {
            $os = "Windows 2000";
        } else if (preg_match("/win/i", $agent) && preg_match('/nt 5.1/i', $agent)) {
            $os = "Windows XP";
        } else if (preg_match("/win/i", $agent) && preg_match('/nt 6.0/i', $agent)) {
            $os = "Windows Vista";
        } else if (preg_match("/win/i", $agent) && preg_match('/nt 6.1/i', $agent)) {
            $os = "Windows 7";
        } else if (preg_match("/win/i", $agent) && preg_match('/nt 6.2/i', $agent)) {
            $os = "Windows 8";
        } else if (preg_match("/win/i", $agent) && preg_match('/nt 6.3/i', $agent)) {
            $os = "Windows 8.1";
        } else if (preg_match("/win/i", $agent) && preg_match('/nt 10/i', $agent)) {
            $os = "Windows 10";
        } else if (preg_match("/win/i", $agent) && preg_match('/32/', $agent)) {
            $os = "Windows 32";
        } else if (preg_match("/linux/i", $agent)) {
            $os = "Linux";
        } else if (preg_match("/unix/i", $agent)) {
            $os = "Unix";
        } else if (preg_match("/sun/i", $agent) && preg_match("/os/i", $agent)) {
            $os = "SunOS";
        } else if (preg_match("/ibm/i", $agent) && preg_match("/os/i", $agent)) {
            $os = "IBM OS/2";
        } else if (preg_match("/mac/i", $agent) && preg_match("/pc/i", $agent)) {
            $os = "Macintosh";
        } else if (preg_match("/powerpc/i", $agent)) {
            $os = "PowerPC";
        } else if (preg_match("/aix/i", $agent)) {
            $os = "AIX";
        } else if (preg_match("/HPUX/i", $agent)) {
            $os = "HPUX";
        } else if (preg_match("/netbsd/i", $agent)) {
            $os = "NetBSD";
        } else if (preg_match("/bsd/i", $agent)) {
            $os = "BSD";
        } else if (preg_match("/OSF1/i", $agent)) {
            $os = "OSF1";
        } else if (preg_match("/IRIX/i", $agent)) {
            $os = "IRIX";
        } else if (preg_match("/FreeBSD/i", $agent)) {
            $os = "FreeBSD";
        } else if (preg_match("/teleport/i", $agent)) {
            $os = "teleport";
        } else if (preg_match("/flashget/i", $agent)) {
            $os = "flashget";
        } else if (preg_match("/webzip/i", $agent)) {
            $os = "webzip";
        } else if (preg_match("/offline/i", $agent)) {
            $os = "offline";
        } else {
            $os = "Unknown or APP";
        }
        return $os;
    }

}
