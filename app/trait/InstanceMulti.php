<?php
/*
 * Copyright (c) 2012—2016 成都零点信息技术有限公司 All
 * InstanceMulti
 * 2019/08/26
 */
namespace app\traits;

trait InstanceMulti
{
    /**
     * instances
     * @var array
     */
    private static $traitsInstances;
    
    /**
     * get instance
     * @param  mixed $param
     * @return static
     */
    public static function instance($param = [])
    {
        $className = md5(get_called_class() . serialize($param));

        if (empty(self::$traitsInstances[$className])) {
            self::$traitsInstances[$className] = new static($param);
        }

        return self::$traitsInstances[$className];
    }
}