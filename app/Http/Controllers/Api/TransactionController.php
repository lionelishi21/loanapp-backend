<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 14:37
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\SmartMicro\Repositories\Contracts\TransactionInterface;

use Illuminate\Http\Request;

class TransactionController extends ApiController
{
    /**
     * @var TransactionInterface
     */
    protected $transactionRepository, $load;

    /**
     * TransactionController constructor.
     * @param TransactionInterface $transactionInterface
     */
    public function __construct(TransactionInterface $transactionInterface)
    {
        $this->transactionRepository = $transactionInterface;
        $this->load = ['loan', 'payment'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->transactionRepository->listAll($this->formatFields($select));
        } else
            $data = TransactionResource::collection($this->transactionRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param TransactionRequest $request
     * @return mixed
     */
    public function store(TransactionRequest $request)
    {
        $save = $this->transactionRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Transaction has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $transaction = $this->transactionRepository->getById($uuid);

        if (!$transaction) {
            return $this->respondNotFound('Transaction not found.');
        }
        return $this->respondWithData(new TransactionResource($transaction));

    }

    /**
     * @param TransactionRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(TransactionRequest $request, $uuid)
    {
        $save = $this->transactionRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! Transaction has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->transactionRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! Transaction has been deleted');
        }
        return $this->respondNotFound('Transaction not deleted');
    }
}