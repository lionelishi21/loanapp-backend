<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 14:37
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\TransactionTypeRequest;
use App\Http\Resources\TransactionTypeResource;
use App\SmartMicro\Repositories\Contracts\TransactionTypeInterface;

use Illuminate\Http\Request;

class TransactionTypeController extends ApiController
{
    /**
     * @var TransactionTypeInterface
     */
    protected $transactionTypeRepository, $load;

    /**
     * TransactionTypeController constructor.
     * @param TransactionTypeInterface $transactionTypeInterface
     */
    public function __construct(TransactionTypeInterface $transactionTypeInterface)
    {
        $this->transactionTypeRepository = $transactionTypeInterface;
        $this->load = ['account', 'transactionTypeMethod'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->transactionTypeRepository->listAll($this->formatFields($select));
        } else
            $data = TransactionTypeResource::collection($this->transactionTypeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param TransactionTypeRequest $request
     * @return mixed
     */
    public function store(TransactionTypeRequest $request)
    {
        $save = $this->transactionTypeRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! TransactionType has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $transactionType = $this->transactionTypeRepository->getById($uuid);

        if (!$transactionType) {
            return $this->respondNotFound('TransactionType not found.');
        }
        return $this->respondWithData(new TransactionTypeResource($transactionType));

    }

    /**
     * @param TransactionTypeRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(TransactionTypeRequest $request, $uuid)
    {
        $save = $this->transactionTypeRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! TransactionType has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->transactionTypeRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! TransactionType has been deleted');
        }
        return $this->respondNotFound('TransactionType not deleted');
    }
}