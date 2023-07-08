<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 10/11/2019
 * Time: 16:05
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\PenaltyTypeRequest;
use App\Http\Resources\PenaltyTypeResource;
use App\SmartMicro\Repositories\Contracts\PenaltyTypeInterface;

use Illuminate\Http\Request;

class PenaltyTypeController extends ApiController
{
    /**
     * @var PenaltyTypeInterface
     */
    protected $penaltyTypeRepository, $load;

    /**
     * PenaltyTypeController constructor.
     * @param PenaltyTypeInterface $penaltyTypeInterface
     */
    public function __construct(PenaltyTypeInterface $penaltyTypeInterface)
    {
        $this->penaltyTypeRepository = $penaltyTypeInterface;
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
            return $this->penaltyTypeRepository->listAll($this->formatFields($select));
        } else
            $data = PenaltyTypeResource::collection($this->penaltyTypeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param PenaltyTypeRequest $request
     * @return mixed
     */
    public function store(PenaltyTypeRequest $request)
    {
        $save = $this->penaltyTypeRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! PenaltyType has been created.');

        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $penaltyType = $this->penaltyTypeRepository->getById($uuid);

        if (!$penaltyType) {
            return $this->respondNotFound('PenaltyType not found.');
        }
        return $this->respondWithData(new PenaltyTypeResource($penaltyType));

    }

    /**
     * @param PenaltyTypeRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(PenaltyTypeRequest $request, $uuid)
    {
        $save = $this->penaltyTypeRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! PenaltyType has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->penaltyTypeRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! PenaltyType has been deleted');
        }
        return $this->respondNotFound('PenaltyType not deleted');
    }
}