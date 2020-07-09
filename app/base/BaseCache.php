<?php

namespace app\base;

use app\traits\InstanceMulti;
use think\cache\driver\Redis;

class BaseCache
{
    use InstanceMulti;

    /**
     * 获取缓存
     * @param $key
     * @return mixed
     */
    protected function getCache($key)
    {
        return cache($key);
    }

    /**
     * 设置缓存
     * @param $key
     * @param $data
     * @param $exp
     * @return mixed
     */
    protected function setCache($key, $data, $exp)
    {
        return cache($key, $data, $exp);
    }

    /**
     * 删除缓存
     * @param $key
     * @return mixed
     */
    protected function rmCache($key)
    {
        return cache($key, null);
    }

}
