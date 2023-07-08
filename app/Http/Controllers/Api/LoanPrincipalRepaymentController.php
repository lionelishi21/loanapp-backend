<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 21/09/2019
 * Time: 21:25
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoanPrincipalRepaymentRequest;
use App\Http\Resources\LoanPrincipalRepaymentResource;
use App\Models\LoanPrincipalRepayment;
use App\SmartMicro\Repositories\Contracts\LoanPrincipalRepaymentInterface;

use Illuminate\Http\Request;

class LoanPrincipalRepaymentController extends ApiController
{
    /**
     * @var \App\SmartMicro\Repositories\Contracts\LoanPrincipalRepaymentInterface
     */
    protected $loanPrincipalRepaymentRepository, $load;

    /**
     * LoanPrincipalRepaymentController constructor.
     * @param LoanPrincipalRepaymentInterface $loanPrincipalRepaymentInterface
     */
    public function __construct(LoanPrincipalRepaymentInterface $loanPrincipalRepaymentInterface)
    {
        $this->loanPrincipalRepaymentRepository = $loanPrincipalRepaymentInterface;
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
            return $this->loanPrincipalRepaymentRepository->listAll($this->formatFields($select));
        }
        $data = $this->loanPrincipalRepaymentRepository->getAllPaginate($this->load);

        $data->map(function($item) {
            $item['balance'] =  $this->formatMoney($item['amount'] - $this->loanPrincipalRepaymentRepository->paidAmount($item['id']));
            $item['paid_amount'] =  $this->formatMoney($this->loanPrincipalRepaymentRepository->paidAmount($item['id']));
            return $item;
        });

        return $this->respondWithData(LoanPrincipalRepaymentResource::collection($data));
    }

    /**
     * @param LoanPrincipalRepaymentRequest $request
     * @return mixed
     */
    public function store(LoanPrincipalRepaymentRequest $request)
    {
        LoanPrincipalRepayment::create($request->all());
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $loanPrincipalRepayment = $this->loanPrincipalRepaymentRepository->getById($uuid);

        if (!$loanPrincipalRepayment) {
            return $this->respondNotFound('LoanPrincipalRepayment not found.');
        }
        return $this->respondWithData(new LoanPrincipalRepaymentResource($loanPrincipalRepayment));

    }

    /**
     * @param LoanPrincipalRepaymentRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(LoanPrincipalRepaymentRequest $request, $uuid)
    {
        $save = $this->loanPrincipalRepaymentRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! LoanPrincipalRepayment has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->loanPrincipalRepaymentRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! LoanPrincipalRepayment has been deleted');
        }
        return $this->respondNotFound('LoanPrincipalRepayment not deleted');
    }
}