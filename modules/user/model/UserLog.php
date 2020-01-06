<?php
/**
 * Created by PhpStorm.
 * User: itwri
 * Date: 2019/10/25
 * Time: 11:24
 */

namespace app\user\model;


use Jasmine\library\Model;

class UserLog extends Model
{
    /**
     * @param $user_id
     * @param $action
     * @param $event
     * @param $ip
     * @param $remark
     * @param string $content
     * @return int
     * itwri 2019/8/10 1:17
     */
    static function record($user_id,$action,$event,$ip,$remark,$content = ''){
        return (new static())->insert(['user_id' => $user_id, 'action' => $action, 'event' => $event, 'ip' => $ip, 'remark' => $remark,'content'=>$content]);
    }
}