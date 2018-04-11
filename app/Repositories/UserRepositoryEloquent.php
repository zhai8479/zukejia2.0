<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Library\Password;

/**
 * Class UserMoneyLogRepositoryEloquent
 * @package namespace App\Repositories;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     *  用户注册表单验证
     *@Transaction({
     *      @Request({"code": "短信验证码", "mobile": "13517210601", "password": "123456", "user_name": "用户名"}),
     *      @Response(200, body={"msg": "success"}),
     *      @Response(422, body={"error": {"user_name": {"用户名必须传入"}}})
     * })
     * @Parameters({
     *      @Parameter("code", description="手机短信验证码", required=true, type="string"),
     *      @Parameter("mobile", description="手机号", required=true, type="string"),
     *      @Parameter("password", description="密码", required=true, type="string"),
     *      @Parameter("user_name", description="用户名", required=true, type="string")
     * })
     * @return mixed
     */
    public function check_forms($input)
    {
        $mobile = array_get($input, 'mobile');
        $password = array_get($input, 'password');
        if (empty($mobile) || empty($password)) throw new \Exception('mobile 与 password 必须传入');
        if (!is_string($mobile) || !preg_match("/^1[34578]{1}\d{9}$/", $mobile)) {
            throw new \Exception('手机号格式不正确');
        }
        if (!is_string($password) || strlen($password) < 6) {
            throw new \Exception('密码格式不正确');
        }
        $mobile_exists = User::whereMobile($mobile)->exists();
        if ($mobile_exists) throw new \Exception('手机号已被注册');

        if (isset($input['user_name'])) {
            $user_name = $input['user_name'];
            if (!is_string($user_name) || strlen($user_name) > 40) throw new \Exception('用户名格式不正确');
            if (User::whereUserName($user_name)->exists()) throw new \Exception('用户名已被使用');
        }
    }
}
