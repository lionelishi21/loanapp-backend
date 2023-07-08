<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/07/2019
 * Time: 07:34
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\WitnessTypeRequest;
use App\Http\Resources\WitnessTypeResource;
use App\SmartMicro\Repositories\Contracts\WitnessTypeInterface;

use Illuminate\Http\Request;

class WitnessTypeController extends ApiController
{
    /**
     * @var WitnessTypeInterface
     */
    protected $witnessTypeRepository;

    /**
     * WitnessTypeController constructor.
     * @param WitnessTypeInterface $witnessTypeInterface
     */
    public function __construct(WitnessTypeInterface $witnessTypeInterface)
    {
        $this->witnessTypeRepository = $witnessTypeInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->witnessTypeRepository->listAll($this->formatFields($select));
        } else
            $data = WitnessTypeResource::collection($this->witnessTypeRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param WitnessTypeRequest $request
     * @return mixed
     */
    public function store(WitnessTypeRequest $request)
    {
        $save = $this->witnessTypeRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! WitnessType has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $witnessType = $this->witnessTypeRepository->getById($uuid);

        if (!$witnessType) {
            return $this->respondNotFound('WitnessType not found.');
        }
        return $this->respondWithData(new WitnessTypeResource($witnessType));
    }

    /**
     * @param WitnessTypeRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(WitnessTypeRequest $request, $uuid)
    {
        $save = $this->witnessTypeRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! WitnessType has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->witnessTypeRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! WitnessType has been deleted');
        }
        return $this->respondNotFound('WitnessType not deleted');
    }
}