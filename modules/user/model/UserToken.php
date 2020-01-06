<?php
/**
 * Created by PhpStorm.
 * User: itwri
 * Date: 2019/10/23
 * Time: 2:51
 */

namespace app\user\model;

use Jasmine\library\Model;

class UserToken extends Model
{
    /**
     * 校验
     * @param $token
     * @param $ip
     * @return bool|mixed
     * itwri 2019/10/23 2:56
     */
    function check($token, $ip)
    {
        /**
         * 查询对应的token
         */
        return $this->alias('ut')
            ->fields('u.id,u.username,u.points,u.create_time,u.status,ut.login_ip')
            ->join('user u', 'u.id = ut.user_id')
            ->where('ut.expire_time', '>', date('Y-m-d H:i:s'))
            ->where(['ut.token' => $token])
            ->where('login_ip', '=', $ip)
            ->find();
    }

    /**
     * @param $user_id
     * @param $token
     * @param $ip
     * @param $from
     * @param $expire_time
     * @return int
     * itwri 2019/11/28 12:34
     */
    function save($user_id, $token, $ip, $from, $expire_time = null)
    {
        $expire_time = is_null($expire_time) ? date('Y-m-d H:i:s', strtotime('+1 hours')) : $expire_time;
        if ($this->where(['user_id' => $user_id])->count() > 0) {
            return $this
                ->where(['user_id' => $user_id])
                ->update(['token' => $token, 'login_ip' => $ip, 'login_from' => $from, 'expire_time' => $expire_time, 'update_time' => date('Y-m-d H:i:s')]);
        }
        return $this->insert(['token' => $token, 'login_ip' => $ip, 'expire_time' => $expire_time, 'login_from' => $from, 'user_id' => $user_id, 'login_count' => 1, 'update_time' => date('Y-m-d H:i:s'), 'create_time' => date('Y-m-d H:i:s')]);
    }
}