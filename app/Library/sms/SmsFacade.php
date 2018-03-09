<?php
/**
 * @desc
 * @author zhan <grianchan@gmail.com>
 * @since 2017/9/11 16:26
 */

namespace App\Library\sms;


use Illuminate\Support\Facades\Facade;

class SmsFacade extends Facade
{
    protected static function getFacadeAccessor() { return 'App\Library\sms\Sms'; }
}