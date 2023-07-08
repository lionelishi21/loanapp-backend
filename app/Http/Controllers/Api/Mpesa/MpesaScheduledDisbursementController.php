<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 31/01/2020
 * Time: 10:36
 */

namespace App\Http\Controllers\Api\Mpesa;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\MpesaScheduledDisbursementRequest;
use App\Http\Resources\MpesaScheduledDisbursementResource;
use App\SmartMicro\Repositories\Contracts\MpesaScheduledDisbursementInterface;

use Illuminate\Http\Request;

class MpesaScheduledDisbursementController extends ApiController
{
    /**
     * @var MpesaScheduledDisbursementInterface
     */
    protected $mpesaScheduledDisbursementRepository, $load;

    /**
     * MpesaScheduledDisbursementController constructor.
     * @param MpesaScheduledDisbursementInterface $mpesaScheduledDisbursementInterface
     */
    public function __construct(MpesaScheduledDisbursementInterface $mpesaScheduledDisbursementInterface)
    {
        $this->mpesaScheduledDisbursementRepository = $mpesaScheduledDisbursementInterface;
        $this->load = ['branch', 'createdBy'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->mpesaScheduledDisbursementRepository->listAll($this->formatFields($select));
        } else
            $data = MpesaScheduledDisbursementResource::collection($this->mpesaScheduledDisbursementRepository->getAllPaginate($this->load));
        return $this->respondWithData($data);
    }

    /**
     * @param MpesaScheduledDisbursementRequest $request
     * @return mixed
     */
    public function store(MpesaScheduledDisbursementRequest $request)
    {
        $save = $this->mpesaScheduledDisbursementRepository->create($request->all());

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! MpesaScheduledDisbursement has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $mpesaScheduledDisbursement = $this->mpesaScheduledDisbursementRepository->getById($uuid);

        if (!$mpesaScheduledDisbursement) {
            return $this->respondNotFound('MpesaScheduledDisbursement not found.');
        }
        return $this->respondWithData(new MpesaScheduledDisbursementResource($mpesaScheduledDisbursement));
    }

    /**
     * @param MpesaScheduledDisbursementRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(MpesaScheduledDisbursementRequest $request, $uuid)
    {
        $save = $this->mpesaScheduledDisbursementRepository->update($request->all(), $uuid);
        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! MpesaScheduledDisbursement has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->mpesaScheduledDisbursementRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! MpesaScheduledDisbursement has been deleted');
        }
        return $this->respondNotFound('MpesaScheduledDisbursement not deleted');
    }
}