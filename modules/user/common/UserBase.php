<?php
/**
 * Created by PhpStorm.
 * User: itwri
 * Date: 2019/10/23
 * Time: 2:39
 */

namespace app\user\common;


use app\user\model\UserToken;
use Jasmine\App;

class UserBase extends Base
{

    protected $user_token = '';
    protected $escapeRoutes = [];
    protected $user = null;

    function __construct(App $app = null)
    {
        parent::__construct($app);

        $route = implode('@', array(ucfirst($this->request()->getController()), strtolower($this->request()->getAction())));

        // 提取user_token
        $this->user_token = $this->request()->header('User-Token', '');
        $ip = $this->request()->ip();


        //  如果不在非验证列表里，则需要验证登录
        if (!in_array($route, $this->escapeRoutes)) {

            $UserTokenM = new UserToken();

            //校验token
            if (($this->user = $UserTokenM->check($this->user_token,$ip))==false) {
                echo $this->error("请登录");
                die();
            }
        }
    }


    /**
     * 添加非验证路由
     * Controller @ action
     * 可在继承实现的initialize()方法里使用
     * @param $route
     * @return $this
     */
    protected function addEscapeRoute($route)
    {
        if (is_array($route)) {
            foreach ($route as $item) {
                $this->addEscapeRoute($item);
            }
        } elseif (is_string($route)) {
            $arr = explode('@', $route);
            if (count($arr) > 1) {
                $this->escapeRoutes[] = implode('@', array(ucfirst($arr[0]), strtolower($arr[1])));
            }
        }
        return $this;
    }

    /**
     * @param $user_id
     * @return string
     * itwri 2019/7/31 12:22
     */
    protected function getLoginKey($user_id){
        return md5(implode(',',[$user_id,$this->request()->ip(),date('Y-m-d')]));
    }

    /**
     * @param $pass
     * @return string
     * itwri 2019/10/23 14:10
     */
    protected function getPassword($pass){
        return password($pass);
    }
}