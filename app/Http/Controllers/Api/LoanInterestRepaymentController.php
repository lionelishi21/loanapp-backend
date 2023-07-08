<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 21/09/2019
 * Time: 21:19
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoanInterestRepaymentRequest;
use App\Http\Resources\LoanInterestRepaymentResource;
use App\Models\LoanInterestRepayment;
use App\SmartMicro\Repositories\Contracts\LoanInterestRepaymentInterface;

use Illuminate\Http\Request;

class LoanInterestRepaymentController extends ApiController
{
    /**
     * @var \App\SmartMicro\Repositories\Contracts\LoanInterestRepaymentInterface
     */
    protected $loanInterestRepaymentRepository, $load;

    /**
     * LoanInterestRepaymentController constructor.
     * @param LoanInterestRepaymentInterface $loanInterestRepaymentInterface
     */
    public function __construct(LoanInterestRepaymentInterface $loanInterestRepaymentInterface)
    {
        $this->loanInterestRepaymentRepository = $loanInterestRepaymentInterface;
        $this->load = ['loan'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->loanInterestRepaymentRepository->listAll($this->formatFields($select));
        }
        $data = $this->loanInterestRepaymentRepository->getAllPaginate($this->load);

        $data->map(function($item) {
            $item['balance'] =  $this->formatMoney($item['amount'] - $this->loanInterestRepaymentRepository->paidAmount($item['id']));
            $item['paid_amount'] =  $this->formatMoney($this->loanInterestRepaymentRepository->paidAmount($item['id']));
            return $item;
        });

        return $this->respondWithData(LoanInterestRepaymentResource::collection($data));
    }

    /**
     * @param LoanInterestRepaymentRequest $request
     * @return mixed
     */
    public function store(LoanInterestRepaymentRequest $request)
    {
        LoanInterestRepayment::create($request->all());
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $loanInterestRepayment = $this->loanInterestRepaymentRepository->getById($uuid);

        if (!$loanInterestRepayment) {
            return $this->respondNotFound('LoanInterestRepayment not found.');
        }
        return $this->respondWithData(new LoanInterestRepaymentResource($loanInterestRepayment));

    }

    /**
     * @param LoanInterestRepaymentRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(LoanInterestRepaymentRequest $request, $uuid)
    {
        $save = $this->loanInterestRepaymentRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! LoanInterestRepayment has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->loanInterestRepaymentRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! LoanInterestRepayment has been deleted');
        }
        return $this->respondNotFound('LoanInterestRepayment not deleted');
    }
}