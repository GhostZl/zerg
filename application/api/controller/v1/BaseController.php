<?php 
namespace app\api\controller\v1;

use app\api\service\Token as TokenService;
use think\Controller;
/**
 * 
 */
class BaseController extends Controller
{
    protected function checkExclusiceScope($value='')
    {
        TokenService::needExclusiveScope();
    }

    protected function checkPrimaryScope()
    {
        TokenService::needPrimaryScope();
    }
}