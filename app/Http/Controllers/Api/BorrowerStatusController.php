<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 12:51
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\BorrowerStatusRequest;
use App\Http\Resources\BorrowerStatusResource;
use App\SmartMicro\Repositories\Contracts\BorrowerStatusInterface;

use Illuminate\Http\Request;

class BorrowerStatusController  extends ApiController
{
    /**
     * @var \App\SmartMicro\Repositories\Contracts\BorrowerStatusInterface
     */
    protected $borrowerStatusRepository;

    /**
     * BorrowerStatusController constructor.
     * @param BorrowerStatusInterface $borrowerStatusInterface
     */
    public function __construct(BorrowerStatusInterface $borrowerStatusInterface)
    {
        $this->borrowerStatusRepository   = $borrowerStatusInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->borrowerStatusRepository->listAll($this->formatFields($select));
        } else
        $data = BorrowerStatusResource::collection($this->borrowerStatusRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param BorrowerStatusRequest $request
     * @return mixed
     */
    public function store(BorrowerStatusRequest $request)
    {
        $save = $this->borrowerStatusRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else{
            return $this->respondWithSuccess('Success !! BorrowerStatus has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $borrowerStatus = $this->borrowerStatusRepository->getById($uuid);

        if(!$borrowerStatus)
        {
            return $this->respondNotFound('BorrowerStatus not found.');
        }
        return $this->respondWithData(new BorrowerStatusResource($borrowerStatus));

    }

    /**
     * @param BorrowerStatusRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(BorrowerStatusRequest $request, $uuid)
    {
        $save = $this->borrowerStatusRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else

            return $this->respondWithSuccess('Success !! BorrowerStatus has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if($this->borrowerStatusRepository->delete($uuid)){
            return $this->respondWithSuccess('Success !! BorrowerStatus has been deleted');
        }
        return $this->respondNotFound('BorrowerStatus not deleted');
    }
}