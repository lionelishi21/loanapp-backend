<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 10/11/2019
 * Time: 15:58
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\PenaltyFrequencyRequest;
use App\Http\Resources\PenaltyFrequencyResource;
use App\SmartMicro\Repositories\Contracts\PenaltyFrequencyInterface;

use Illuminate\Http\Request;

class PenaltyFrequencyController extends ApiController
{
    /**
     * @var PenaltyFrequencyInterface
     */
    protected $penaltyFrequencyRepository, $load;

    /**
     * PenaltyFrequencyController constructor.
     * @param PenaltyFrequencyInterface $penaltyFrequencyInterface
     */
    public function __construct(PenaltyFrequencyInterface $penaltyFrequencyInterface)
    {
        $this->penaltyFrequencyRepository = $penaltyFrequencyInterface;
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
            return $this->penaltyFrequencyRepository->listAll($this->formatFields($select));
        } else
            $data = PenaltyFrequencyResource::collection($this->penaltyFrequencyRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param PenaltyFrequencyRequest $request
     * @return mixed
     */
    public function store(PenaltyFrequencyRequest $request)
    {
        $save = $this->penaltyFrequencyRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! PenaltyFrequency has been created.');

        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $penaltyFrequency = $this->penaltyFrequencyRepository->getById($uuid);

        if (!$penaltyFrequency) {
            return $this->respondNotFound('PenaltyFrequency not found.');
        }
        return $this->respondWithData(new PenaltyFrequencyResource($penaltyFrequency));

    }

    /**
     * @param PenaltyFrequencyRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(PenaltyFrequencyRequest $request, $uuid)
    {
        $save = $this->penaltyFrequencyRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! PenaltyFrequency has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->penaltyFrequencyRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! PenaltyFrequency has been deleted');
        }
        return $this->respondNotFound('PenaltyFrequency not deleted');
    }
}