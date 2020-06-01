<?php


namespace app\service;


use app\base\BaseService;
use app\model\UserExtendModel;
use app\model\UserModel;
use think\facade\Db;
use Tool\Tool;
use Tool\wxBizDataCrypt;


class LoginService extends BaseService
{

    /**
     * 登录主方法
     * @param $data
     * @return array|bool
     */
    public function userLogin($data)
    {
        if (!isset($data['login_type'])) {
            recordError('请传递登录方式');
            return false;
        }
        if (!isset($data['code'])) {
            recordError('请传递code');
            return false;
        }
        if (!UserExtendModel::checkLoginType($data['login_type'])) {
            recordError('请传递正确的登录方式');
            return false;
        }
        $info = [];
        switch ($data['login_type']) {
            case UserExtendModel::APP_LOGIN_TYPE:
                $info = $this->appLogin($data);
                break;
            case UserExtendModel::SMALL_LOGIN_TYPE:
                $info = $this->webLogin($data);
                break;
            case UserExtendModel::WEB_LOGIN_TYPE:
                $info = $this->smallLogin($data);
                break;
        }
        if (empty($info)) {
            return false;
        }
        if (empty($info['unionid'])) {
            recordError('unionid不能为空');
            return false;
        }
        if (empty($info['openid'])) {
            recordError('openid不能为空');
            return false;
        }
        $info['ip'] = $data['ip'];
        Db::startTrans();
        try {
            $userInfo = UserExtendModel::instance()->getUserByUnionId($info['unionid']);
            if (empty($userInfo)) {
                //注册
                return $this->userRegister($info, $data['login_type']);
            }
            //证明用户存在，是登录
            if ($userInfo['status'] == UserExtendModel::STATUS_BAN) {
                recordError('你已经被拉黑了');
                return false;
            }
            $userUpdateData = [
                'nickname' => $info['nickname'],
                'head_image' => $info['headimgurl'],
            ];
            $userExtendData = [
                'last_app_login_time' => date("Y-m-d H:i:s"),
                'last_app_login_ip' => $info['ip']
            ];
            $tokenType = '';
            switch ($data['login_type']) {
                case UserExtendModel::APP_LOGIN_TYPE:
                    $tokenType = 'app';
                    if (empty($userInfo['app_openid'])) {
                        $userExtendData['app_openid'] = $info['openid'];
                    }
                    break;
                case UserExtendModel::SMALL_LOGIN_TYPE:
                    $tokenType = 'small';
                    if (empty($userInfo['web_openid'])) {
                        $userExtendData['web_openid'] = $info['openid'];
                    }
                    break;
                case UserExtendModel::WEB_LOGIN_TYPE:
                    $tokenType = 'web';
                    if (empty($userInfo['small_openid'])) {
                        $userExtendData['small_openid'] = $info['openid'];
                    }
                    break;
            }
            UserModel::instance()->edit($userUpdateData, ['id' => $userInfo['uid']]);
            UserExtendModel::instance()->edit($userExtendData, ['uid' => $userInfo['uid']]);
            $tool = new Tool();
            $res = [
                "token" => $tool->login($userInfo['uid'], $tokenType),
                "nickname" => $info['nickname'],
                "head_image" => $info['headimgurl'],
                "id" => $userInfo['uid']
            ];
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            recordError($e->getMessage());
            return false;
        }
        return $res;
    }


    /**
     * 用户注册
     * @param $info
     * @param $loginType
     * @return array
     */
    protected function userRegister($info, $loginType)
    {
        $installUserData = [
            "nickname" => $info['nickname'],
            "head_image" => $info['headimgurl'],
            'create_time' => date("Y-m-d H:i:s"),
        ];
        $uid = UserModel::instance()->insertGetId($installUserData);

        $installExtendData = [
            "uid" => $uid,
            "unionid" => $info['unionid'],
            "reg_time" => date("Y-m-d H:i:s"),
            "reg_ip" => $info['ip'],
            "last_app_login_time" => date("Y-m-d H:i:s"),
            "last_app_login_ip" => $info['ip']
        ];
        $tokenType = '';
        switch ($loginType) {
            case UserExtendModel::APP_LOGIN_TYPE:
                $tokenType = 'app';
                $installExtendData['app_openid'] = $info['openid'];
                break;
            case UserExtendModel::SMALL_LOGIN_TYPE:
                $tokenType = 'small';
                $installExtendData['small_openid'] = $info['openid'];
                break;
            case UserExtendModel::WEB_LOGIN_TYPE:
                $tokenType = 'web';
                $installExtendData['web_openid'] = $info['openid'];
                break;
        }

        UserExtendModel::instance()->insert($installExtendData);
        $tool = new Tool();
        $res = [
            "token" => $tool->login($uid, $tokenType),
            "nickname" => $info['nickname'],
            "head_image" => $info['headimgurl'],
            "id" => $uid
        ];
        return $res;
    }


    /**
     * app登录
     * @param $data
     * @return bool
     */
    private function appLogin($data)
    {
        recordError('暂未开发');
        return false;
    }


    /**
     * h5登录
     * @param $data
     * @return bool
     */
    private function webLogin($data)
    {
        recordError('暂未开发');
        return false;
    }


    /**
     * 小程序登录
     * @param $data
     * @return mixed
     */
    private function smallLogin($data)
    {
        $info = $this->smallGetUnionId($data);
        $info['headimgurl'] = $data['head_image'];
        $info['nickname'] = $data['nickname'];
        return $info;
    }


    /**
     * 小程序获取unionid
     * @param $data
     * @return mixed
     */
    private function smallGetUnionId($data)
    {
        $appId = UserExtendModel::SMALL_APP_ID;
        $secretId = UserExtendModel::SMALL_SECRET_ID;
        $jsCode = str_replace(' ', '+', $data['code']);
        $sessionKey = json_decode(file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid=" . $appId . "&secret=" . $secretId . "&js_code=" . $jsCode . "&grant_type=authorization_code"), true);
        //unionid注销
        if (isset($session_key["unionid"]) == false) {
            $encryptedData = $data['encryptedData'];
            $iv = $data['iv'];
            $DecodeData = $this->decryptData($appId, $sessionKey['session_key'], $encryptedData, $iv);
            $session_key["unionid"] = $DecodeData["unionId"];
            $session_key["openid"] = $DecodeData["openId"];
        }
        return $session_key;
    }


    /**
     * 小程序生成unionid
     * @param $appId
     * @param $sessionKey
     * @param $encryptedData
     * @param $iv
     * @return mixed
     */
    private function decryptData($appId, $sessionKey, $encryptedData, $iv)
    {
        $WXBizDataCrypt = new wxBizDataCrypt($appId, $sessionKey);
        $WXBizDataCrypt->decryptData($encryptedData, $iv, $data);
        return json_decode($data, true);
    }

}