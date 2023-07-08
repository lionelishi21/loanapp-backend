<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 24/01/2020
 * Time: 23:11
 */

namespace App\Http\Controllers\Api\Mpesa;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\MpesaCustomSendRequest;
use App\Http\Resources\MpesaCustomSendResource;
use App\SmartMicro\Repositories\Contracts\MpesaCustomSendInterface;
use Illuminate\Http\Request;

class MpesaCustomSendController extends ApiController
{
    /**
     * @var MpesaCustomSendInterface
     */
    protected $mpesaCustomSendRepository, $load, $mpesaCustomSendLedger;

    /**
     * MpesaCustomSendController constructor.
     * @param MpesaCustomSendInterface $mpesaCustomSendInterface
     */
    public function __construct(MpesaCustomSendInterface $mpesaCustomSendInterface)
    {
        $this->mpesaCustomSendRepository = $mpesaCustomSendInterface;
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
            return $this->mpesaCustomSendRepository->listAll($this->formatFields($select), ['member']);
        }
        $data = $this->mpesaCustomSendRepository->getAllPaginate($this->load);

        $data->map(function ($item) {
            $item['mpesaCustomSendBalance'] = $this->formatMoney($this->mpesaCustomSendRepository->mpesaCustomSendBalance($item['id']));
            return $item;
        });
        return $this->respondWithData(MpesaCustomSendResource::collection($data));
    }

    /**
     * @param MpesaCustomSendRequest $request
     * @return mixed
     */
    public function store(MpesaCustomSendRequest $request)
    {
        $save = $this->mpesaCustomSendRepository->create($request->all());
        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Mpesa CustomSend has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $mpesaCustomSend = $this->mpesaCustomSendRepository->getById($uuid);
        if (!$mpesaCustomSend) {
            return $this->respondNotFound('MpesaCustomSend not found.');
        }
        return $this->respondWithData(new MpesaCustomSendResource($mpesaCustomSend));
    }

    /**
     * @param MpesaCustomSendRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(MpesaCustomSendRequest $request, $uuid)
    {
        $save = $this->mpesaCustomSendRepository->update($request->all(), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! MpesaCustomSend has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->mpesaCustomSendRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! MpesaCustomSend has been deleted');
        }
        return $this->respondNotFound('MpesaCustomSend not deleted');
    }
}