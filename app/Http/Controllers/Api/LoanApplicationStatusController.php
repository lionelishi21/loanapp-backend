<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 12:45
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoanApplicationStatusRequest;
use App\Http\Resources\LoanApplicationStatusResource;
use App\SmartMicro\Repositories\Contracts\LoanApplicationStatusInterface;

use Illuminate\Http\Request;

class LoanApplicationStatusController  extends ApiController
{
    /**
     * @var \App\SmartMicro\Repositories\Contracts\LoanApplicationStatusInterface
     */
    protected $loanApplicationStatusRepository;

    /**
     * LoanApplicationStatusController constructor.
     * @param LoanApplicationStatusInterface $loanApplicationStatusInterface
     */
    public function __construct(LoanApplicationStatusInterface $loanApplicationStatusInterface)
    {
        $this->loanApplicationStatusRepository   = $loanApplicationStatusInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->loanApplicationStatusRepository->listAll($this->formatFields($select));
        } else
            $data = LoanApplicationStatusResource::collection($this->loanApplicationStatusRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param LoanApplicationStatusRequest $request
     * @return mixed
     */
    public function store(LoanApplicationStatusRequest $request)
    {
        $save = $this->loanApplicationStatusRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else{
            return $this->respondWithSuccess('Success !! LoanApplicationStatus has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $loanApplicationStatus = $this->loanApplicationStatusRepository->getById($uuid);

        if(!$loanApplicationStatus)
        {
            return $this->respondNotFound('LoanApplicationStatus not found.');
        }
        return $this->respondWithData(new LoanApplicationStatusResource($loanApplicationStatus));

    }

    /**
     * @param LoanApplicationStatusRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(LoanApplicationStatusRequest $request, $uuid)
    {
        $save = $this->loanApplicationStatusRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else

            return $this->respondWithSuccess('Success !! LoanApplicationStatus has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if($this->loanApplicationStatusRepository->delete($uuid)){
            return $this->respondWithSuccess('Success !! LoanApplicationStatus has been deleted');
        }
        return $this->respondNotFound('LoanApplicationStatus not deleted');
    }
}