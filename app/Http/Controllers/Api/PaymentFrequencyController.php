<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 08/09/2019
 * Time: 22:30
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\PaymentFrequencyRequest;
use App\Http\Resources\PaymentFrequencyResource;
use App\SmartMicro\Repositories\Contracts\PaymentFrequencyInterface;

use Illuminate\Http\Request;

class PaymentFrequencyController extends ApiController
{
    /**
     * @var PaymentFrequencyInterface
     */
    protected $paymentFrequencyRepository, $load;

    /**
     * PaymentFrequencyController constructor.
     * @param PaymentFrequencyInterface $paymentFrequencyInterface
     */
    public function __construct(PaymentFrequencyInterface $paymentFrequencyInterface)
    {
        $this->paymentFrequencyRepository = $paymentFrequencyInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->paymentFrequencyRepository->listAll($this->formatFields($select));
        } else
            $data = PaymentFrequencyResource::collection($this->paymentFrequencyRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param PaymentFrequencyRequest $request
     * @return mixed
     */
    public function store(PaymentFrequencyRequest $request)
    {
        $save = $this->paymentFrequencyRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! PaymentFrequency has been created.');

        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $paymentFrequency = $this->paymentFrequencyRepository->getById($uuid);

        if (!$paymentFrequency) {
            return $this->respondNotFound('PaymentFrequency not found.');
        }
        return $this->respondWithData(new PaymentFrequencyResource($paymentFrequency));

    }

    /**
     * @param PaymentFrequencyRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(PaymentFrequencyRequest $request, $uuid)
    {
        $save = $this->paymentFrequencyRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! PaymentFrequency has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->paymentFrequencyRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! PaymentFrequency has been deleted');
        }
        return $this->respondNotFound('PaymentFrequency not deleted');
    }
}