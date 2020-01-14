<?php
/**
 * Created by PhpStorm.
 * User: itwri
 * Date: 2019/10/23
 * Time: 2:37
 */

namespace app\user\controller;


use app\user\common\UserBase;
use app\user\model\User;
use app\user\model\UserLog;
use app\user\model\UserToken;
use app\user\model\Vendor;
use Jasmine\App;
use Jasmine\library\http\Request;

class Index extends UserBase
{
    function __construct(App $app = null)
    {
        $this->addEscapeRoute('Index@login');
        $this->addEscapeRoute('Index@isLogin');
        parent::__construct($app);
    }


    /**
     * 检查是否已登录
     * @param Request $request
     * @return array|false|string
     * itwri 2019/11/19 20:51
     */
    public function isLogin(Request $request){
        $UserTokenM = new UserToken();
        if(($user = $UserTokenM->check($this->user_token,$request->ip())) == false){
            return $this->error('未登录',-1,$request->getHeader()->all());
        }
        return $this->success('已登录',$user);
    }

    /**
     * 用户登录
     * @param Request $request
     * @return array|false|string
     * @throws \Exception
     * itwri 2020/1/10 14:19
     */
    function login(Request $request)
    {

        if ($request->isPost()) {

            $requestUsername = $request->input('username', '');
            $requestPassword = $request->input('password', '');

            $UserM = new User();
            $UserTokenM = new UserToken();


            /**
             * 检查用户时否存在
             */
            $user = $UserM->fields('id,username,password')->where(['username' => $requestUsername])->find();
            if (!$user) {
                return $this->error('用户不存在');
            }

            /**
             * 是否已登录，避免被刷登录
             */
            $user_token = $this->getLoginKey($user['id']);
            if ($info = $UserTokenM->check($user_token, $request->ip()) !== false) {
                $UserTokenM->setInc('login_count', 1);
                return $this->success('已经登录', ['ip' => $request->ip(), 'user_token' => $user_token]);
            }

            /**
             * 校验密码
             */
            $password = $this->getPassword($requestPassword);
            if ($user['password'] != $password) {
                return $this->error('密码不正确');
            }

            /**
             * 去掉密码
             */
            unset($user['password']);

            /**
             * 来源
             */
            $from = $request->param('from','Api');

            /**
             * 保存到session表
             */
            $res = $UserTokenM->save($user['id'],$user_token,$request->ip(),$from,date('Y-m-d H:i:s',strtotime('+1 days')));

            if ($res !== false) {

                /*
                 * 记录登录动作
                 */
                UserLog::record($user['id'], __METHOD__, __FUNCTION__, $request->ip(), '登录',json_encode($user));

                /**
                 * 设置cookie
                 */
                setcookie('User-Token',$user_token);

                return $this->success('登录成功', ['ip' => $request->ip(), 'user_token' => $user_token]);
            }

        }

        return $this->error('非法操作');
    }

    /**
     * @param Request $request
     * @return array|false|string
     * itwri 2019/8/8 11:10
     */
    function logout(Request $request)
    {
        $UserTokenM = new UserToken();
        $res = $UserTokenM->where([['user_id' => $this->user['id']], 'login_ip' => $request->ip()])->update(['expire_time' => strtotime("-1hours")]);

        if($res){
            /*
             * 记录登录动作
             */
            UserLog::record($this->user['id'], __METHOD__, __FUNCTION__, $request->ip(), '登出');
            return $this->success('成功退出');
        }
        return $this->error('退出失败');
    }

    /**
     * 我所拥有的商家
     * @return array|false|string
     * itwri 2019/11/6 12:29
     */
    function getVendors(){
        /**
         * 生成实例
         */
        $VendorM = new Vendor();

        /**
         * 查出用户所拥有的商家
         */
        $list = $VendorM->fields('id,name,serial_code')->where(['user_id'=>$this->user['id']])->paginator();

        /**
         * 返回结果给前端
         */
        return $this->success('加载成功',$list);
    }
}