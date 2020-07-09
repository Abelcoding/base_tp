<?php
// 应用公共文件
/**
 * 設置全局錯誤信息
 * @param $str $mix 变量
 * @param $tag
 * @return string
 */
function recordError($str, $tag = '')
{
    global $errorInfo;
    $errorInfo = $str;
    return true;
}

/**
 * 獲取全局錯誤信息
 * @return string
 */
function getLastError()
{
    global $errorInfo;
    if (!empty($errorInfo)) {
        return $errorInfo;
    } else {
        return "";
    }
}

/**
 * 二维数组根据某个字段排序
 * @param array $array 要排序的数组
 * @param string $keys 要排序的键字段
 * @param int $sort
 * @return array 排序后的数组
 */
function arraySort($array, $keys, $sort = SORT_DESC)
{
    $keysValue = [];
    foreach ($array as $k => $v) {
        $keysValue[$k] = $v[$keys];
    }
    array_multisort($keysValue, $sort, $array);
    return $array;
}

/**
 * 处理字符串函数
 * @param $string
 * @return mixed|string
 */
function urlsafe_b64encode($string)
{
    $data = base64_encode($string);
    $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
    return $data;
}

/**
 * 将金额除以一百返回
 * @param $number
 * @return float|int
 */
function float_fee($number)
{
    if (empty($number)) {
        return 0;
    } else {
        return (float)bcdiv($number, 100, 2);  // 去掉没用的0
    }
}

/**
 * 将金额乘以一百返回
 * @param $number
 * @return int|string
 */
function multiply_fee($number)
{
    if (empty($number)) {
        return 0;
    } else {
        return bcmul($number, 100);
    }
}

/**
 *  获取随机字符串，包含小写字母和数字
 * @param $length
 * @return string
 */
function getRandLowerStr($length = 6)
{
    $hex = array(
        'k',
        'y',
        's',
        '3',
        'q',
        'j',
        '5',
        'p',
        'n',
        '9',
        'g',
        'a',
        '7',
        'x',
        'w',
        '8',
        '4',
        'f',
        't',
        '2',
        'm',
        '1',
        'i',
        'c',
        'b',
        '6',
        '0',
        'e',
        'z',
        'l',
        'o',
        'u',
        'r',
        'd',
        'h',
        'v'
    ); //36位随机数
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $hex[mt_rand(0, 35)];
    }
    return $str;
}

/**
 *  获取随机字符串，包含大写字母和数字
 * @param $length
 * @return string
 */
function getRandStr($length = 6)
{
    $hex = array(
        'K',
        'Y',
        'S',
        '3',
        'Q',
        'J',
        '5',
        'P',
        'N',
        '9',
        'A',
        'G',
        '7',
        'X',
        'F',
        '8',
        '4',
        'W',
        'T',
        '2',
        'M',
        '1',
        'I',
        'C',
        'B',
        '6',
        '0',
        'E',
        'Z',
        'L',
        'O',
        'U',
        'R',
        'D',
        'H',
        'V'
    ); //36位随机数
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $hex[mt_rand(0, 35)];
    }
    return $str;
}

/**
 *  发送post请求
 * @param $uri
 * @param $data
 * @param $timeout
 * @param $header
 * @return mixed
 */
function http_post($uri, $data = [], $timeout = 10, $header = false)
{
    $ch = curl_init();
    if ($header) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    }
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $return = curl_exec($ch);;
    curl_close($ch);
    return $return;
}

/**
 * 发送get请求
 * @param $url
 * @param int $timeout
 * @param bool $header
 * @return bool|string
 */
function http_get($url, $timeout = 10, $header = false)
{
    $ch = curl_init($url);
    if ($header) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    }
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    $resposne = curl_exec($ch);
    if (false === $resposne) {
        return false;
    } else {
        return $resposne;
    }
}