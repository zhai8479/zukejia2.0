<?php

namespace App\Api\Controllers;

use Dingo\Blueprint\Annotation\Resource;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\OrderRefundCreateRequest;
use App\Http\Requests\OrderRefundUpdateRequest;
use App\Repositories\OrderRefundRepository;
use App\Validators\OrderRefundValidator;


/**
 * 入住人信息控制器
 *
 * @Resource("OrderCheckInUsers", uri="order_check_in_user")
 *
 * Class OrderCheckInUsersController
 * @package App\Api\Controllers
 */
class OrderCheckInUsersController extends BaseController
{

    /**
     * @var OrderRefundRepository
     */
    protected $repository;

    /**
     * @var OrderRefundValidator
     */
    protected $validator;

    public function __construct(OrderRefundRepository $repository, OrderRefundValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $orderRefunds = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $orderRefunds,
            ]);
        }

        return view('orderRefunds.index', compact('orderRefunds'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  OrderRefundCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRefundCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $orderRefund = $this->repository->create($request->all());

            $response = [
                'message' => 'OrderRefund created.',
                'data'    => $orderRefund->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $orderRefund = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $orderRefund,
            ]);
        }

        return view('orderRefunds.show', compact('orderRefund'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $orderRefund = $this->repository->find($id);

        return view('orderRefunds.edit', compact('orderRefund'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  OrderRefundUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(OrderRefundUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $orderRefund = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'OrderRefund updated.',
                'data'    => $orderRefund->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'OrderRefund deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'OrderRefund deleted.');
    }
}
