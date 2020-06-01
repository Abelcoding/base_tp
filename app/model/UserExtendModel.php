<?php


namespace app\model;


use app\base\BaseModel;


class UserExtendModel extends BaseModel
{
    protected $name = 'user_extend';

    const APP_APP_ID = '';  //app的appId

    const APP_SECRET_ID = ''; //app的 secretId

    const WEB_APP_ID = '';  //公众号的 appId

    const WEB_SECRET_ID = '';  //公众号的 secretId

    const SMALL_APP_ID = ''; //小程序的appId

    const SMALL_SECRET_ID = ''; //小程序的 secretId

    const SMALL_LOGIN_TYPE = 3; //小程序登录

    const WEB_LOGIN_TYPE = 2; // h5登录

    const APP_LOGIN_TYPE = 1; // app登录


    const STATUS_NORMAL = 1;

    const STATUS_BAN = 2;

    /**
     * 验证登录方式
     * @param $loginType
     * @return bool
     */
    public static function checkLoginType($loginType)
    {
        return in_array($loginType, [self::SMALL_LOGIN_TYPE, self::WEB_LOGIN_TYPE, self::APP_LOGIN_TYPE]);
    }


    /**
     * 通过unionId获取用户
     * @param $unionId
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserByUnionId($unionId)
    {
        $where = [
            'unionid' => $unionId
        ];
        return $this->where($where)->find();
    }
}