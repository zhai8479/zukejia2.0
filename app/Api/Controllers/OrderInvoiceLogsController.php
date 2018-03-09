<?php

namespace App\Api\Controllers;

use Dingo\Blueprint\Annotation\Resource;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\OrderInvoiceLogCreateRequest;
use App\Http\Requests\OrderInvoiceLogUpdateRequest;
use App\Repositories\OrderInvoiceLogRepository;
use App\Validators\OrderInvoiceLogValidator;

/**
 * 订单发票控制器
 *
 * @Resource("OrderInvoiceLog", uri="order_invoice_log")
 * Class OrderInvoiceLogsController
 * @package App\Api\Controllers
 */
class OrderInvoiceLogsController extends BaseController
{

    /**
     * @var OrderInvoiceLogRepository
     */
    protected $repository;

    /**
     * @var OrderInvoiceLogValidator
     */
    protected $validator;

    public function __construct(OrderInvoiceLogRepository $repository, OrderInvoiceLogValidator $validator)
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
        $orderInvoiceLogs = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $orderInvoiceLogs,
            ]);
        }

        return view('orderInvoiceLogs.index', compact('orderInvoiceLogs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  OrderInvoiceLogCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(OrderInvoiceLogCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $orderInvoiceLog = $this->repository->create($request->all());

            $response = [
                'message' => 'OrderInvoiceLog created.',
                'data'    => $orderInvoiceLog->toArray(),
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
        $orderInvoiceLog = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $orderInvoiceLog,
            ]);
        }

        return view('orderInvoiceLogs.show', compact('orderInvoiceLog'));
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

        $orderInvoiceLog = $this->repository->find($id);

        return view('orderInvoiceLogs.edit', compact('orderInvoiceLog'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  OrderInvoiceLogUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(OrderInvoiceLogUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $orderInvoiceLog = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'OrderInvoiceLog updated.',
                'data'    => $orderInvoiceLog->toArray(),
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
                'message' => 'OrderInvoiceLog deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'OrderInvoiceLog deleted.');
    }
}
