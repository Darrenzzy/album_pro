<?php

namespace app\admin\validate;
use think\Validate;

/**
 * 角色规则验证
 * Class RoleValidate
 * @package app\admin\validate
 */
class RoleValidate extends Validate
{
    protected $rule = [
        ['title', 'unique:auth_group', '角色已经存在']
    ];

}