<?php
/**
 * Created by PhpStorm.
 * User: itwri
 * Date: 2019/10/23
 * Time: 2:39
 */

namespace app\user\common;


use Jasmine\App;
use Jasmine\library\Controller;

class Base extends Controller
{

    function __construct(App $app = null)
    {
        parent::__construct($app);
    }
}