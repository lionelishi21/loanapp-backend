<?php
/**
 * Created by PhpStorm.
 * LoanType: kevin
 * Date: 26/10/2018
 * Time: 12:22
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoanTypeRequest;
use App\Http\Resources\LoanTypeResource;
use App\Models\LoanType;
use App\SmartMicro\Repositories\Contracts\LoanTypeInterface;

use Illuminate\Http\Request;

class LoanTypeController  extends ApiController
{
    /**
     * @var \App\SmartMicro\Repositories\Contracts\LoanTypeInterface
     */
    protected $loanTypeRepository, $load;

    /**
     * LoanTypeController constructor.
     * @param LoanTypeInterface $loanTypeInterface
     */
    public function __construct(LoanTypeInterface $loanTypeInterface)
    {
        $this->loanTypeRepository   = $loanTypeInterface;
        $this->load = ['interestType', 'paymentFrequency'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->loanTypeRepository->listAll($this->formatFields($select), $this->load);
        } else
            $data = LoanTypeResource::collection($this->loanTypeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param LoanTypeRequest $request
     * @return mixed
     */
    public function store(LoanTypeRequest $request)
    {
        LoanType::create($request->all());
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $loanType = $this->loanTypeRepository->getById($uuid);

        if(!$loanType)
        {
            return $this->respondNotFound('LoanType not found.');
        }
        return $this->respondWithData(new LoanTypeResource($loanType));

    }

    /**
     * @param LoanTypeRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(LoanTypeRequest $request, $uuid)
    {
        $save = $this->loanTypeRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else

            return $this->respondWithSuccess('Success !! LoanType has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if($this->loanTypeRepository->delete($uuid)){
            return $this->respondWithSuccess('Success !! LoanType has been deleted');
        }
        return $this->respondNotFound('LoanType not deleted');
    }
}