<?php
/**
 * Created by PhpStorm.
 * User: CNDNS-JK
 * Date: 2018/1/19 0019
 * Time: 16:41
 */
namespace app\home\validate;
use think\Validate;
/**
 * 会员的验证器
 * Class MemberValidate
 */
class MemberValidate extends Validate
{ protected $rule = [
    'email' => 'email',
//    ['username', 'unique:member', '用户名已存在']
//    ['phone', 'unique:member', '手机号已存在']
//    ['email', 'email', '邮箱格式错误']
];

    protected $msg = [
'email'        => '邮箱格式错误',
];
    protected $scene = [
//        'add'   =>  ['name','email'],
        'edit'  =>  ['email'],
    ];

}