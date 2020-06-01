<?php


namespace app\model;


class CodeModel
{
    const OK = 1000;        //逻辑成功返回
    const ERROR = 1001;     //逻辑失败返回
    const NO_AUTH = 1002;   //身份验证错误或登录信息已过期
    const NEED_PAY = 1003;   //订单创建成功，需要跳转支付
    const NEED_FLUSH = 1004;    //页面cookie失效，请刷新页面获取
    const NEED_AUTH = 1005;    //用户游客身份，需要验证手机号码
    const NEED_SET_ADDRESS = 1006;    //用户需要先设置自己的默认收货地址
    const EXCEED_RANGE = 1007;    //用户下单默认地址超出范围
    const PAY_CONFIG_RETURN = 1008;     //配置微信自主支付参数，验证白名单错误
    const ENVELOPE_OVER = 1010;         //来晚了，金币已领完
    const ENVELOPE_OVERTIME = 1011;     //同一账户一天只能领取X次
    const ENVELOPE_HAVING_GET = 1012;     //您已经领取过金币了
    const INVITE_OFF_NOW = 1013;     //活动未开启
    const INVITE_OVER_NOW = 1014;     //活动已结束
    const INVITE_FOR_NEW_USER = 1015;     //仅限新用户领取
    const MEMBER_PAY_PASS_ERROR = 1016;     //会员卡支付密码错误
    const MEMBER_PAYING = 1017;     //支付中，等待输入密码
}