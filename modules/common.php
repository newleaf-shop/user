<?php
/**
 * Created by PhpStorm.
 * User: itwri
 * Date: 2019/10/23
 * Time: 14:08
 */

/**
 * @param $route
 * @param $params
 * @param bool|string $root
 * @return string
 * itwri 2019/8/18 11:58
 */
/**
 * @param $pass
 * @return string
 * itwri 2020/1/6 22:31
 */
function password($pass)
{
    return md5(sha1($pass));
}