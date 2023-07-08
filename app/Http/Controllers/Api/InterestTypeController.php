<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 07/08/2019
 * Time: 08:55
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\InterestTypeRequest;
use App\Http\Resources\InterestTypeResource;
use App\SmartMicro\Repositories\Contracts\InterestTypeInterface;

use Illuminate\Http\Request;

class InterestTypeController extends ApiController
{
    /**
     * @var InterestTypeInterface
     */
    protected $interestTypeRepository, $load;

    /**
     * InterestTypeController constructor.
     * @param InterestTypeInterface $interestTypeInterface
     */
    public function __construct(InterestTypeInterface $interestTypeInterface)
    {
        $this->interestTypeRepository = $interestTypeInterface;
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
            return $this->interestTypeRepository->listAll($this->formatFields($select));
        } else
            $data = InterestTypeResource::collection($this->interestTypeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param InterestTypeRequest $request
     * @return mixed
     */
    public function store(InterestTypeRequest $request)
    {
        $save = $this->interestTypeRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! InterestType has been created.');

        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $interestType = $this->interestTypeRepository->getById($uuid);

        if (!$interestType) {
            return $this->respondNotFound('InterestType not found.');
        }
        return $this->respondWithData(new InterestTypeResource($interestType));

    }

    /**
     * @param InterestTypeRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(InterestTypeRequest $request, $uuid)
    {
        $save = $this->interestTypeRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! InterestType has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->interestTypeRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! InterestType has been deleted');
        }
        return $this->respondNotFound('InterestType not deleted');
    }
}