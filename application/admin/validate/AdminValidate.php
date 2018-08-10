<?php

namespace app\admin\validate;

use think\Validate;

/**
 * 管理员注册认证
 * Class AdminValidate
 * @package app\admin\validate
 */
class AdminValidate extends Validate
{
    protected $rule = [
        ['username', 'require', '用户名不能为空'],
//        ['password', 'require', '密码不能为空']
    ];

}