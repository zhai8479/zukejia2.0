<?php

namespace App\Http\Controllers\SmS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Redis;

class SmsController extends Controller
{
    /**
 * 获取图形验证码
 * @return mixed
 */
    public function image(Request $request)
    {
        $width = $request->get('width');
        $height = $request->get('height');
        //生成验证码图片的Builder对象，配置相应属性

        $builder = new CaptchaBuilder;
        //设置验证码的内容
        $phrase = strtoupper(substr($builder->getPhrase(),0,4));
        $builder->setPhrase($phrase);
        //可以设置图片宽高及字体
        $builder->build($width, $height, $font = null);

        //将验证码放入redis中
        $key = "image_code:" . $request->get('acid');
        //$redis = Redis::connection();
        //$redis->set($key, $phrase);
        //return response()->json($redis->get($key));
        Redis::set($key, $phrase, 'EX', 300);
        //生成图片
        return response($builder->output())->header("Content-type", "image/jpeg");
    }
}
