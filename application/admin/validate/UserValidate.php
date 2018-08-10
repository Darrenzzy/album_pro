<?php

namespace app\admin\validate;

use think\Validate;

/**
 * 管理员规则验证
 * Class UserValidate
 * @package app\admin\validate
 */
class UserValidate extends Validate
{
    protected $rule = [
        ['username', 'unique:admin', '管理员已经存在']
    ];

}