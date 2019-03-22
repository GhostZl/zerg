<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/21
 * Time: 10:50
 */

namespace app\api\validate;


class PagingParameter extends BaseValidate
{
    protected $rule = [
        'page' => 'isPositiveInteger',
        'size' => 'isPositiveInteger',
    ];
}