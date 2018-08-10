<?php


return [
    '__pattern__' => [
        'name' => '\w+',
        '/' => 'index', // 首页访问路由
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
    'index'   => '/home/index',


];
