<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 12/11/2019
 * Time: 16:43
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoanPenaltyRequest;
use App\Http\Resources\LoanPenaltyResource;
use App\Models\LoanPenalty;
use App\SmartMicro\Repositories\Contracts\LoanPenaltyInterface;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanHistoryController extends ApiController
{
    /**
     * @var \App\SmartMicro\Repositories\Contracts\LoanPenaltyInterface
     */
    protected $loanPenaltyRepository, $load;

    /**
     * LoanPenaltyController constructor.
     * @param LoanPenaltyInterface $loanPenaltyInterface
     */
    public function __construct(LoanPenaltyInterface $loanPenaltyInterface)
    {
        $this->loanPenaltyRepository = $loanPenaltyInterface;
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
            return $this->loanPenaltyRepository->listAll($this->formatFields($select), $this->load);
        } else
            $data = LoanPenaltyResource::collection($this->loanPenaltyRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param $id
     * @return \Illuminate\Support\Collection
     */
    public function show($id) {

        $allLoanReceivableAccounts = DB::table('loans')
            ->where('loans.id', $id)
            ->join('loan_penalties', function($join) use ($id) {
                $join->on('loan_penalties.loan_id', '=', $id);
            })
            ->join('loan_interest_repayments', function($join) use ($id) {
                $join->on('loan_interest_repayments.loan_id', '=', $id);
            })
            ->join('loan_principal_repayments', function($join) use ($id) {
                $join->on('loan_principal_repayments.loan_id', '=', $id);
            })
            ->leftJoin('loan_penalties', 'loan_penalties.loan_id', '=', 'loans.id')
            ->leftJoin('loan_interest_repayments', 'loan_interest_repayments.loan_id', '=', 'loans.id')
            ->leftJoin('loan_principal_repayments', 'loan_principal_repayments.loan_id', '=', 'loans.id')
            ->get();

            return $allLoanReceivableAccounts;

    }
}