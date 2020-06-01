<?php


namespace app\controller;


use app\base\BaseController;
use app\model\CodeModel;
use app\model\UserExtendModel;
use app\service\LoginService;


class User extends BaseController
{

    /**
     * 所有的登录接口
     * @return \think\Response
     * @api /login/user_login
     * @api_param login_type {int} 登录方式
     * @api_param code {string} code
     */
    public function userLogin()
    {
        //登录方式 1.app登录   2.h5登录   3.小程序登录
        $loginType = $this->request->param('login_type', '');
        $appCode = $this->request->param('code', '');
        if (empty($loginType) || empty($appCode)) return $this->toJson(CodeModel::ERROR, '参数错误');
        $data = [
            'login_type' => $loginType,
            'code' => $appCode
        ];
        if ($loginType == UserExtendModel::SMALL_LOGIN_TYPE) {
            $data['head_image'] = $this->request->param('head_image');
            $data['nickname'] = $this->request->param('nickname');
            $data['encryptedData'] = $this->request->param('encryptedData');
            $data['iv'] = $this->request->param('iv');
        }
        $data['ip'] = $this->request->ip();
        $res = LoginService::instance()->userLogin($data);
        if (empty($res)) {
            return $this->toJson(CodeModel::ERROR, getLastError() ?: '登录失败');
        }
        return $this->toJson(CodeModel::OK, '登录成功', $res);
    }


    /**
     * 获取用户详情
     * @return \think\Response
     * @api /login/get_user_info
     * @api_param login_type {int} 登录方式
     * @api_param code {string} code
     */
    public function getUserInfo()
    {
        
    }


}