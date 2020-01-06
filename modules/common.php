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
function url($route, $params, $root = true)
{
    $route = trim($route, '/');

    $arr = explode('/', $route);

    switch (count($arr)) {
        case 1:
            $module = \Jasmine\App::init()->getRequest()->getModule();
            $controller = \Jasmine\App::init()->getRequest()->getController();
            $action = array_shift($arr);
            break;
        case 2:
            $module = \Jasmine\App::init()->getRequest()->getModule();
            $controller = array_shift($arr);
            $action = array_shift($arr);
            break;
        default:
            $module = array_shift($arr);
            $controller = array_shift($arr);
            $action = array_shift($arr);

    }
    $module = $module ? $module : \Jasmine\App::init()->getRequest()->getModule();
    $controller = $controller ? $controller : \Jasmine\App::init()->getRequest()->getController();
    $action = $action ? $action : \Jasmine\App::init()->getRequest()->getAction();

    $temp = implode('/', $arr);

    $params = array_merge(\Jasmine\library\http\Url::pathToParams($temp), $params);

    $rootUrl = '';
    if ($root == true) {
        $rootUrl = \Jasmine\App::init()->getRequest()->getRootUrl();
    } elseif (is_string($root)) {
        $rootUrl = $root;
    }
    return $rootUrl . "/" . implode('/', [$module, $controller, $action]) . "?" . http_build_query($params);
}

function password($pass)
{
    return md5(sha1($pass));
}