<?php

class clientGetObj {

    function getBrowse() {
        global $_SERVER;
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $br = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/MSIE/i', $br)) {
                $br = 'MSIE';
            } elseif (preg_match('/Firefox/i', $br)) {
                $br = 'Firefox';
            } elseif (preg_match('/Chrome/i', $br)) {
                $br = 'Chrome';
            } elseif (preg_match('/Safari/i', $br)) {
                $br = 'Safari';
            } elseif (preg_match('/Opera/i', $br)) {
                $br = 'Opera';
            } else {
                $br = 'Other';
            }
            return $br;
        } else {
            return "获取浏览器信息失败！";
        }
    }

    function getOS() {
        global $_SERVER;
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $os = false;
        if (stripos($agent,'win') && strpos($agent, '95')) {
            $os = 'Windows 95';
        } else if (stripos($agent,'win 9x') && strpos($agent, '4.90')) {
            $os = 'Windows ME';
        } else if (stripos($agent, 'win' ) && strpos($agent, '98')) {
            $os = 'Windows 98';
        } else if (stripos($agent, 'win' ) && strpos($agent, 'nt 5.1')) {
            $os = 'Windows XP';
        } else if (stripos($agent, 'win' ) && strpos($agent, 'nt 5')) {
            $os = 'Windows 2000';
        } else if (stripos($agent, 'win' ) && strpos($agent, 'nt')) {
            $os = 'Windows NT';
        } else if (stripos($agent, 'win' ) && strpos($agent, '32')) {
            $os = 'Windows 32';
        } else if (stripos( $agent, 'linux')) {
            $os = 'Linux';
        } else if (stripos( $agent, 'unix')) {
            $os = 'Unix';
        } else if (stripos( $agent, 'sun') && stripos( $agent , 'os')) {
            $os = 'SunOS';
        } else if (stripos( $agent , 'ibm') && stripos( $agent , 'os')) {
            $os = 'IBM OS/2';
        } else if (stripos( $agent , 'Mac') && stripos( $agent , 'PC')) {
            $os = 'Macintosh';
        } else if (stripos( $agent , 'PowerPC')) {
            $os = 'PowerPC';
        } else if (stripos( $agent , 'AIX')) {
            $os = 'AIX';
        } else if (stripos( $agent , 'HPUX')) {
            $os = 'HPUX';
        } else if (stripos( $agent , 'NetBSD')) {
            $os = 'NetBSD';
        } else if (stripos( $agent , 'BSD')) {
            $os = 'BSD';
        } else if (strpos( $agent , 'OSF1')) {
            $os = 'OSF1';
        } else if (strpos($agent , 'IRIX')) {
            $os = 'IRIX';
        } else if (stripos($agent , 'FreeBSD')) {
            $os = 'FreeBSD';
        } else if (stripos($agent , 'teleport')) {
            $os = 'teleport';
        } else if (stripos($agent , 'flashget')) {
            $os = 'flashget';
        } else if (stripos($agent , 'webzip')) {
            $os = 'webzip';
        } else if (stripos($agent , 'offline')) {
            $os = 'offline';
        } else if (strpos($agent , 'iPad')) {
            $os = 'iPad';
        } else if (stripos($agent , 'Android')) {
            $os = 'Android';
        } else if (stripos($agent , 'iPhone')) {
            $os = 'iPhone';
        } else {
            $os = 'Unknown';
        }
        return $os;
    }

}

?>