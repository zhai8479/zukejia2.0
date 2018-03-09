<?php
/**
 * @desc
 * @author zhan <grianchan@gmail.com>
 * @since 2017/9/11 9:33
 */

namespace App\Library;


class Password
{
    /**
     * 创建密码
     * @param $password
     * @return bool|string
     */
    public function create_password($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * 检测密码是否正确
     * @param $password
     * @param $hash_password
     * @return bool
     */
    public function check_password($password, $hash_password)
    {
        return password_verify($password, $hash_password);
    }
}