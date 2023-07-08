<?php
/**
 * Created by PhpStorm.
 * Borrower: kevin
 * Date: 26/10/2018
 * Time: 12:10
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\BorrowerRequest;
use App\Http\Resources\BorrowerResource;
use App\SmartMicro\Repositories\Contracts\BorrowerInterface;

use Illuminate\Http\Request;

class BorrowerController  extends ApiController
{
    /**
     * @var \App\SmartMicro\Repositories\Contracts\BorrowerInterface
     */
    protected $borrowerRepository;

    /**
     * BorrowerController constructor.
     * @param BorrowerInterface $borrowerInterface
     */
    public function __construct(BorrowerInterface $borrowerInterface)
    {
        $this->borrowerRepository   = $borrowerInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->borrowerRepository->listAll($this->formatFields($select));
        } else
        $data = BorrowerResource::collection($this->borrowerRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param BorrowerRequest $request
     * @return mixed
     */
    public function store(BorrowerRequest $request)
    {
        $save = $this->borrowerRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else{
            return $this->respondWithSuccess('Success !! Borrower has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $borrower = $this->borrowerRepository->getById($uuid);

        if(!$borrower)
        {
            return $this->respondNotFound('Borrower not found.');
        }
        return $this->respondWithData(new BorrowerResource($borrower));

    }

    /**
     * @param BorrowerRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(BorrowerRequest $request, $uuid)
    {
        $save = $this->borrowerRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else

            return $this->respondWithSuccess('Success !! Borrower has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if($this->borrowerRepository->delete($uuid)){
            return $this->respondWithSuccess('Success !! Borrower has been deleted');
        }
        return $this->respondNotFound('Borrower not deleted');
    }
}