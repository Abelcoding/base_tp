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
