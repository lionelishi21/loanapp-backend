<?php
/**
 * Created by PhpStorm.
 * LoanStatus: kevin
 * Date: 26/10/2018
 * Time: 12:32
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoanStatusRequest;
use App\Http\Resources\LoanStatusResource;
use App\SmartMicro\Repositories\Contracts\LoanStatusInterface;

use Illuminate\Http\Request;

class LoanStatusController  extends ApiController
{
    /**
     * @var \App\SmartMicro\Repositories\Contracts\LoanStatusInterface
     */
    protected $loanStatusRepository;

    /**
     * LoanStatusController constructor.
     * @param LoanStatusInterface $loanStatusInterface
     */
    public function __construct(LoanStatusInterface $loanStatusInterface)
    {
        $this->loanStatusRepository   = $loanStatusInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->loanStatusRepository->listAll($this->formatFields($select));
        } else
            $data = LoanStatusResource::collection($this->loanStatusRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param LoanStatusRequest $request
     * @return mixed
     */
    public function store(LoanStatusRequest $request)
    {
        $save = $this->loanStatusRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else{
            return $this->respondWithSuccess('Success !! LoanStatus has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $loanStatus = $this->loanStatusRepository->getById($uuid);

        if(!$loanStatus)
        {
            return $this->respondNotFound('LoanStatus not found.');
        }
        return $this->respondWithData(new LoanStatusResource($loanStatus));

    }

    /**
     * @param LoanStatusRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(LoanStatusRequest $request, $uuid)
    {
        $save = $this->loanStatusRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else

            return $this->respondWithSuccess('Success !! LoanStatus has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if($this->loanStatusRepository->delete($uuid)){
            return $this->respondWithSuccess('Success !! LoanStatus has been deleted');
        }
        return $this->respondNotFound('LoanStatus not deleted');
    }

}