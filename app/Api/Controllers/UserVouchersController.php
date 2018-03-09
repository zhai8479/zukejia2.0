<?php

namespace App\Api\Controllers;

use App\Criteria\MyCriteria;
use App\Models\UserVoucher;
use App\Repositories\UserVoucherRepositoryEloquent;
use Dingo\Blueprint\Annotation\Method\Get;
use Dingo\Blueprint\Annotation\Resource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\UserVoucherCreateRequest;
use App\Http\Requests\UserVoucherUpdateRequest;
use App\Repositories\UserVoucherRepository;
use Dingo\Api\Http\Request as HttpRequest;


/**
 * Class UserVouchersController
 * @package App\Api\Controllers
 *
 * @Resource("UserVoucher", uri="voucher")
 */
class UserVouchersController extends BaseController
{

    /**
     * @var UserVoucherRepositoryEloquent
     */
    protected $repository;

    public function __construct(UserVoucherRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * 获取代金卷列表
     *
     * @Get(uri="index")
     *
     * @return \Illuminate\Http\Response
     */
    public function index(HttpRequest $request)
    {
        $request->validate([
            'length' => 'integer|min:1'
        ]);
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->pushCriteria(app(MyCriteria::class));
        /**
         * @var $userVouchers LengthAwarePaginator
         */
        $userVouchers = $this->repository->orderBy('id', 'desc')->paginate($request->input('length', 15));
        $data = $userVouchers->items();
        foreach ($data as $vouchers) {
            $rules = $vouchers->rules;
            $scheme_id = $vouchers->scheme_id;
            $rule_arr = explode(',', $rules);
            $rule_val_arr = [];
            foreach ($rule_arr as $key => $rule_id) {
                $rule = \DB::table('vouchers_rules')->find($rule_id);
                $rule_val_arr[$key] = [
                    'val' => $rule->val,
                    'type_str' => UserVoucher::$rule_types[$rule->type],
                    'type' => $rule->type
                ];
            }
            $scheme = \DB::table('vouchers_schemes')->find($scheme_id);
            $vouchers->rule_detail = $rule_val_arr;
            $vouchers->reduce = $scheme->reduce;
        }
        return $this->array_response($userVouchers);
    }

    /**
     * 根据id查询代金卷信息
     *
     * @Get(uri="show")
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $this->repository->pushCriteria(MyCriteria::class);
            $userVoucher = $this->repository->find($id);
            return $this->array_response($userVoucher);
        } catch (\Exception $exception) {
            return $this->no_found();
        }
    }
}
