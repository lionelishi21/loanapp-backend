<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/08/2019
 * Time: 16:58
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\FiscalPeriodRequest;
use App\Http\Resources\FiscalPeriodResource;
use App\SmartMicro\Repositories\Contracts\FiscalPeriodInterface;

use Illuminate\Http\Request;

class FiscalPeriodController extends ApiController
{
    /**
     * @var FiscalPeriodInterface
     */
    protected $fiscalPeriodRepository;

    /**
     * FiscalPeriodController constructor.
     * @param FiscalPeriodInterface $fiscalPeriodInterface
     */
    public function __construct(FiscalPeriodInterface $fiscalPeriodInterface)
    {
        $this->fiscalPeriodRepository = $fiscalPeriodInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->fiscalPeriodRepository->listAll($this->formatFields($select));
        } else
            $data = FiscalPeriodResource::collection($this->fiscalPeriodRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param FiscalPeriodRequest $request
     * @return mixed
     */
    public function store(FiscalPeriodRequest $request)
    {
        $save = $this->fiscalPeriodRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! FiscalPeriod has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $fiscalPeriod = $this->fiscalPeriodRepository->getById($uuid);

        if (!$fiscalPeriod) {
            return $this->respondNotFound('FiscalPeriod not found.');
        }
        return $this->respondWithData(new FiscalPeriodResource($fiscalPeriod));

    }

    /**
     * @param FiscalPeriodRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(FiscalPeriodRequest $request, $uuid)
    {
        $save = $this->fiscalPeriodRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! FiscalPeriod has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->fiscalPeriodRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! FiscalPeriod has been deleted');
        }
        return $this->respondNotFound('FiscalPeriod not deleted');
    }
}