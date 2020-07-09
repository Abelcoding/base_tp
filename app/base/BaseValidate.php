<?php


namespace app\base;


use think\Validate;

class BaseValidate extends Validate
{
    use \app\lingdianit\traits\instance\InstanceMulti;
}