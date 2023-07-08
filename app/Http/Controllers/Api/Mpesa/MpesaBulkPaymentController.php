<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 24/01/2020
 * Time: 09:21
 */

namespace App\Http\Controllers\Api\Mpesa;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\MpesaBulkPaymentRequest;
use App\Http\Resources\MpesaBulkPaymentResource;
use App\SmartMicro\Repositories\Contracts\MpesaBulkPaymentInterface;

use Illuminate\Http\Request;

/**
 * Successful mpesa bulk payments
 *
 * Class MpesaBulkPaymentController
 * @package App\Http\Controllers\Api\Mpesa
 */
class MpesaBulkPaymentController extends ApiController
{
    /**
     * @var MpesaBulkPaymentInterface
     */
    protected $mpesaBulkPaymentRepository;

    /**
     * MpesaBulkPaymentController constructor.
     * @param MpesaBulkPaymentInterface $mpesaBulkPaymentInterface
     */
    public function __construct(MpesaBulkPaymentInterface $mpesaBulkPaymentInterface)
    {
        $this->mpesaBulkPaymentRepository = $mpesaBulkPaymentInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->mpesaBulkPaymentRepository->listAll($this->formatFields($select));
        } else
            $data = MpesaBulkPaymentResource::collection($this->mpesaBulkPaymentRepository->getAllPaginate());
        return $this->respondWithData($data);
    }

    /**
     * @param MpesaBulkPaymentRequest $request
     * @return mixed
     */
    public function store(MpesaBulkPaymentRequest $request)
    {
        $save = $this->mpesaBulkPaymentRepository->create($request->all());

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! MpesaBulkPayment has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $mpesaBulkPayment = $this->mpesaBulkPaymentRepository->getById($uuid);

        if (!$mpesaBulkPayment) {
            return $this->respondNotFound('MpesaBulkPayment not found.');
        }
        return $this->respondWithData(new MpesaBulkPaymentResource($mpesaBulkPayment));
    }

    /**
     * @param MpesaBulkPaymentRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(MpesaBulkPaymentRequest $request, $uuid)
    {
        $save = $this->mpesaBulkPaymentRepository->update($request->all(), $uuid);
        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! MpesaBulkPayment has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->mpesaBulkPaymentRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! MpesaBulkPayment has been deleted');
        }
        return $this->respondNotFound('MpesaBulkPayment not deleted');
    }
}