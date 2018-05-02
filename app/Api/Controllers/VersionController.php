<?php

namespace App\Api\Controllers;

use App\Models\Version;
use Dingo\Api\Http\Request as HttpRequest;

/**
 * Class VersionController
 * @package App\Api\Controllers
 * @Resource("Version", uri="/version")
 */
class VersionController extends BaseController {

    /**
     * 发布新版本
     *
     * @Post("store")
     * @param HttpRequest $request
     * @return array
     * @throws \InvalidArgumentException
     */
   public function store(HttpRequest $request){
       $this->validate($request,[
           'type' => 'required|int|max:5',
           'version' => 'required|string|max:50',
           'message' => 'required|string|max:150',
           'url' => 'required|string|max:50',
       ]);
       $input = $request->only(['type','version','message','url']);
       $version = Version::create($input);
       return $version;
   }

    /**
     * 检测版本号是否为最新
     */
    public function check(HttpRequest $request){
        $this->validate($request,[
            'version' => 'required|string|max:50',
        ]);
        $version =$request->input('version');

        $server = Version::orderBy('created_at','desc')->first();
        $server_id =$server->version;
        if ($server_id == $version) return $this->array(['msg'=>'已是最新版本','code'=> 200]);
        else return $this->array_response($server);
    }

}