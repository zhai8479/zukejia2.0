<?php

namespace App\Api\Controllers;

use Illuminate\Http\Request as HttpRequest;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Redis;
use App\Repositories\SmsRepository;

class SmsController  extends BaseController
{

    protected $repository;

    /**
     * @var ApartmentValidator
     */
    protected $validator;

    public function __construct(SmsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
 * 获取图形验证码
 * @return mixed
 */
    public function image(HttpRequest $request)
    {
        $width = $request->get('width');
        $height = $request->get('height');
        //生成验证码图片的Builder对象，配置相应属性

        $builder = new CaptchaBuilder;
        //设置验证码的内容
        $phrase = strtoupper(substr($builder->getPhrase(),0,6));
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


    /**
     * 发送短信验证码
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $mobile = $request->input('mobile');
        $image_code = strtoupper($request->input('image_code'));
        $acid = $request->input('acid');
        $key = "image_code:" . $acid;
        if (Redis::exists($key)) {
            $code = Redis::get($key);
            if ($code == $image_code) {
                //清除redis
                Redis::del($key);
                return $this->array_response($this->repository->sendSmsCode($mobile),'success');
            }
            return $this->fail(10503);
        }
        return $this->fail(10503);
    }
}
