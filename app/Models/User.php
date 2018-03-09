<?php

namespace App\Models;

use App\Library\Password;
use App\Library\Recommend;
use Dingo\Api\Routing\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Validation\ValidationData;
use Tymon\JWTAuth\Contracts\JWTSubject;
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $user_name 用户名
 * @property string $mobile 手机号
 * @property string $password 密码
 * @property string|null $email 邮箱
 * @property string|null $real_name 真实姓名
 * @property string|null $id_card 身份证号
 * @property int $sex 性别: 0: 未知，1：男， 2：女
 * @property string|null $birthday 生日
 * @property int|null $country 国家代码
 * @property int|null $province 省代码
 * @property int|null $city 市代码
 * @property int|null $blood_type 血型
 * @property int|null $education 学历
 * @property string|null $profession 职位
 * @property string $ip 注册ip
 * @property int|null $from_platform 注册平台来源
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereBloodType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEducation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereFromPlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIdCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereProfession($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUserName($value)
 * @mixin \Eloquent
 * @property string|null $avatar_url 用户头像
 * @property string $recommend_code 用户唯一邀请码
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRecommendCode($value)
 */
class User extends Authenticatable implements JWTSubject
{
    public static $countries = [
        1 => '中国'
    ];

    // 注册平台
    const FROM_PLATFORM_PC = 1;
    const FROM_PLATFORM_APP = 2;
    const FROM_PLATFORM_WAP = 3;
    const FROM_ADMIN_ADD = 4;       // 管理员添加

    public static $from_platform = [
        1 => 'pc',
        2 => 'app',
        3 => 'wap',
        4 => 'admin_add'
    ];

    // 性别
    const SEX_UNKNOWN = 0;
    const SEX_MALE = 1;
    const SEX_WOMAN = 2;

    public static $sexes = [
        0 => '未知',
        1 => '男',
        2 => '女'
    ];

    public static $bloods = [
        1 => 'AB',
        2 => 'A',
        3 => 'B',
        4 => 'O',
        5 => '其他'
    ];

    // 血型
    const BLOOD_TYPE_AB = 1;
    const BLOOD_TYPE_A = 2;
    const BLOOD_TYPE_B = 3;
    const BLOOD_TYPE_O = 4;
    const BLOOD_TYPE_OTHER = 5;

    public static $educations = [
        1 => '初中',
        2 => '高中',
        3 => '中专',
        4 => '大专',
        5 => '本科',
        6 => '硕士',
        7 => '博士'
    ];

    // 教育学历
    const EDUCATION_CHUZHONG = 1;
    const EDUCATION_GAOZHONG = 2;
    const EDUCATION_ZHONGZHUAN = 3;
    const EDUCATION_DAZHUAN = 4;
    const EDUCATION_BENKE = 5;
    const EDUCATION_SUOSHI = 6;
    const EDUCATION_BOSHI = 7;

    protected $table = 'users';

    public $timestamps = true;

    protected $hidden = ['password'];

    protected $fillable = ['mobile', 'user_name', 'password', 'ip', 'from_platform', 'from_user_id'];

    /**
     * 处理用户头像为完整链接
     *
     * @param $url
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getAvatarUrlAttribute($url)
    {
//        return url(\Storage::disk('public')->url($url));
        return url(env("APP_URL").$url);
    }

    /**
     * 修改国家
     * @param $value
     * @return mixed
     */
    public function getCountryAttribute($value)
    {
        return intval($value);
    }

    /**
     * 修改省
     * @param $value
     * @return mixed
     */
    public function getProvinceAttribute($value)
    {
        return intval($value);
    }

    public function getCityAttribute($value)
    {
        return intval($value);
    }

    /**
     * 管理员添加用户
     *
     * @param array $input
     * - mobile required=true description="手机号" unique
     * - password required=true description="密码"
     * - user_name required=false description="用户名" unique
     * - id_card required=false description="身份证号"
     * - real_name required=false description="真实姓名"
     * - email required=false description=="邮箱" unique
     * - sex required=false default=0 description="性别, 0: 未知，1：男，2：女"
     * @return $this
     * @throws \Exception
     */
    public static function admin_add_user($input)
    {
        $pwd = new Password();
        $mobile = array_get($input, 'mobile');
        $password = array_get($input, 'password');
        $request = \Dingo\Api\Http\Request::capture();
        $ip = $request->ip();
        $ip = empty($ip)?'127.0.0.1':$ip;
        $create = [];
        if (empty($mobile) || empty($password)) throw new \Exception('mobile 与 password 必须传入');
        if (!is_string($mobile) || !preg_match("/^1[34578]{1}\d{9}$/", $mobile)) {
            throw new \Exception('手机号格式不正确');
        }
        if (!is_string($password) || strlen($password) < 6) {
            throw new \Exception('密码格式不正确');
        }
        $mobile_exists = self::whereMobile($mobile)->exists();
        if ($mobile_exists) throw new \Exception('手机号已被注册');

        $create['mobile'] = $mobile;
        $create['password'] = $pwd->create_password($password);
        $create['ip'] = $ip;
        if (isset($input['user_name'])) {
            $user_name = $input['user_name'];
            if (!is_string($user_name) || strlen($user_name) > 40) throw new \Exception('用户名格式不正确');
            if (self::whereUserName($user_name)->exists()) throw new \Exception('用户名已被使用');
            $create['user_name'] = $user_name;
        }

        if (isset($input['id_card'])) {
            $id_card = $input['id_card'];
            if (!is_string($id_card) || $id_card < 16 || $id_card > 18) {
                throw new \Exception('身份证号格式不正确');
            }
            $create['id_card'] = $id_card;
        }
        if (isset($input['real_name'])) {
            $real_name = $input['real_name'];
            if (!is_string($real_name) || strlen($real_name) > 40) throw new \Exception('真实姓名格式不正确');
            $create['real_name'] = $real_name;
        }
        if (isset($input['sex'])) {
            $sex = $input['sex'];
            if (false === array_key_exists($sex, self::$sexes)) throw new \Exception('sex 为错误的值');
            $create['sex'] = $sex;
        }
        if (isset($input['email'])) {
            $email = $input['email'];
            if (!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $email)) throw new \Exception('邮箱格式不正确');
            if (self::whereEmail($email)->exists()) throw new \Exception('邮箱已被使用');
            $create['email'] = $email;
        }
        $create['from_platform'] = self::FROM_ADMIN_ADD;
        $user = self::create($create);
        $user->recommend_code = Recommend::create_code($user->id);
        $user->save();
        return $user;
    }

    public function userMoney()
    {
        return $this->hasOne('App\Models\UserMoney');
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
