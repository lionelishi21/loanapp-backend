<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 16/12/2018
 * Time: 11:23
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\PaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\SmartMicro\Repositories\Contracts\PaymentMethodInterface;

use Illuminate\Http\Request;

class PaymentMethodController  extends ApiController
{
    /**
     * @var \App\SmartMicro\Repositories\Contracts\PaymentMethodInterface
     */
    protected $paymentMethodRepository;

    /**
     * PaymentMethodController constructor.
     * @param PaymentMethodInterface $paymentMethodInterface
     */
    public function __construct(PaymentMethodInterface $paymentMethodInterface)
    {
        $this->paymentMethodRepository   = $paymentMethodInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->paymentMethodRepository->listAll($this->formatFields($select));
        } else
            $data = PaymentMethodResource::collection($this->paymentMethodRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param PaymentMethodRequest $request
     * @return mixed
     */
    public function store(PaymentMethodRequest $request)
    {
        $save = $this->paymentMethodRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else{
            return $this->respondWithSuccess('Success !! PaymentMethod has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $paymentMethod = $this->paymentMethodRepository->getById($uuid);

        if(!$paymentMethod)
        {
            return $this->respondNotFound('PaymentMethod not found.');
        }
        return $this->respondWithData(new PaymentMethodResource($paymentMethod));

    }

    /**
     * @param PaymentMethodRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(PaymentMethodRequest $request, $uuid)
    {
        $save = $this->paymentMethodRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else

            return $this->respondWithSuccess('Success !! PaymentMethod has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if($this->paymentMethodRepository->delete($uuid)){
            return $this->respondWithSuccess('Success !! PaymentMethod has been deleted');
        }
        return $this->respondNotFound('PaymentMethod not deleted');
    }
}