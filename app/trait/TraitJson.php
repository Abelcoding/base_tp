<?php
/**
 * Created by PhpStorm.
 * User: ccong
 * Date: 19-6-11
 * Time: 下午12:52
 */

namespace app\traits;


use app\model\CodeModel;


trait TraitJson
{
    /**
     * 输出json
     * @param int $code //状态码
     * @param string $msg //信息
     * @param array $data //数据
     * @param bool $throw //是否抛出返回异常(主要处理控制器初始化方法中返回)
     * @return \think\Response
     * @throws \think\exception\HttpResponseException
     */
    public function toJson($code = codeModel::OK, $msg = '', $data = [], $throw = false)
    {
        $returnData = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
        $response = \think\response\Json::create($returnData);

        if ($throw) {
            throw new \think\exception\HttpResponseException($response);
        }

        return $response;
    }




}