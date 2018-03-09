<?php
/**
 * Created by PhpStorm.
 * Developer: <kelvenchi@perlface.net>
 * Company: EasyLifeHome Network Technology, HB, Ltd, co,.
 * Date: 2017/11/8
 * Time: 16:02
 */

namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SignUp;

class CustomPJaxController extends Controller
{
    /**
     * 更改报名记录状态
     *
     * @Post("mark")
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function batch_mark(Request $request)
    {
        foreach (SignUP::find($request->get('ids')) as $post) {
            $post->status = $request->get('status');
            $post->save();
        }
        return 1;
    }
}