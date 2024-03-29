<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/18
 * Time: 18:08
 */

namespace app\api\controller\v1;


use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\ThemeException;

class Theme
{
    /**
     * @url /theme?ids=id1,id2,id3....
     * @return 一组theme模型
     */
    public function getSimpleList($ids='') {
        (new IDCollection())->goCheck();
        $ids = explode(',', $ids);
        //查询主题基本信息
        $result = ThemeModel::with(['topicImg', 'headImg'])
            ->select($ids);
        if ($result->isEmpty()) {
            throw new ThemeException();
        }
        return json($result);
    }

    public  function getComplexOne($id) {
        (new IDMustBePostiveInt())->goCheck();
        //查询主题详情
        $result = ThemeModel::with(['products','headImg'])->find($id);
        if (is_null($result)) {
            throw new ThemeException();
        }
        return $result;
    }
}