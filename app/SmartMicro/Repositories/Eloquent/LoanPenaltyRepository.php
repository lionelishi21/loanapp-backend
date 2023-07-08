<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 21/09/2019
 * Time: 10:48
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Events\Payment\PaidLoan;
use App\Models\Loan;
use App\Models\LoanPenalty;
use App\SmartMicro\Repositories\Contracts\JournalInterface;
use App\SmartMicro\Repositories\Contracts\LoanPenaltyInterface;
use App\SmartMicro\Repositories\Contracts\TransactionInterface;
use Illuminate\Support\Facades\DB;

class LoanPenaltyRepository extends BaseRepository implements LoanPenaltyInterface
{
    protected $model, $transactionRepository, $loanPenaltyRepository, $journalRepository;

    /**
     * LoanPenaltyRepository constructor.
     * @param LoanPenalty $model
     * @param TransactionInterface $transactionRepository
     * @param JournalInterface $journalRepository
     */
    function __construct(LoanPenalty $model, TransactionInterface $transactionRepository, JournalInterface $journalRepository)
    {
        $this->model = $model;
        $this->transactionRepository = $transactionRepository;
        $this->journalRepository = $journalRepository;
    }

    /**
     * @param $penaltyRepaymentId
     * @return mixed
     */
    public function paidAmount($penaltyRepaymentId) {
        return DB::table('transactions')
            ->select(DB::raw('COALESCE(sum(transactions.amount), 0.0) as totalPaid'))
            ->where('loan_penalties_id', $penaltyRepaymentId)
            ->where(function($query) {
                $query->where('transaction_type', 'penalty_payment')
                        ->orWhere('transaction_type', 'penalty_waiver');
            })
            ->first()->totalPaid;
    }

    /**
     * @param $loanPenaltyRepaymentId
     * @param $amount
     * @param $loanId
     */
    public function waivePenalty($loanPenaltyRepaymentId, $amount, $loanId) {
        $this->transactionRepository->penaltyWaiverEntry($loanPenaltyRepaymentId, $amount, $loanId);
    }

    /**
     * Take a loan, pay any pending penalty
     *
     * @param $amount
     * @param $loan
     * @param $date
     * @return int|mixed Value assigned for penalty payment
     */
    public function payDuePenalty($amount, $loan, $date) {

        $paidPenaltyAmount = 0;
        $loanId = $loan['id'];

        $loanPenaltyPaymentDueRecords = $this->model
            ->where('loan_id', $loanId)
            ->where('paid_on', null)
            ->where('due_date', '<=', $date)
            ->orderBy('created_at', 'asc')
            ->get()->toArray();

        /*$overDuePrincipal = DB::table('loan_principal_repayments')
            ->select(DB::raw('SUM(amount) as total'))
            ->where('loan_id', $loanId)
            ->whereDate ('due_date', '<', $date)
            ->first()->total;*/

        if (!is_null($loanPenaltyPaymentDueRecords) && count($loanPenaltyPaymentDueRecords) > 0) {

                foreach ($loanPenaltyPaymentDueRecords as $dueRecord){

                    $penaltyDue = $dueRecord['amount'];

                    // Past partial payments
                    $paidPenalty = DB::table('transactions')
                        ->select(DB::raw('SUM(amount) as paid'))
                        ->where('loan_penalties_id', $dueRecord['id'])->get()->toArray();

                    // Actual penalty amount due
                    foreach ($paidPenalty as $paidAmount) {
                        if (null !== $paidAmount) {
                            $penaltyDue = $penaltyDue - ($paidAmount->paid);
                        }
                    }
                    // Now pay
                    if($amount > 0) {
                        $penaltyPaid = $this->transactPayment($loanId, $penaltyDue, $amount, $dueRecord['id']);
                        $paidPenaltyAmount = $paidPenaltyAmount + $penaltyPaid;
                    }
                    $amount = $amount - $paidPenaltyAmount;
                }
            }
        // Journal entry for loan repayment
        if ($paidPenaltyAmount > 0) {
            $this->journalRepository->repayLoanPenalty($loan, $paidPenaltyAmount);
        }
       event(new PaidLoan($loanId));
       return $paidPenaltyAmount;
    }

    /**
     * Pay pending Penalty
     * @param $loanId
     * @param $penaltyDue
     * @param $walletAmount
     * @param $loanPenaltyPaymentDueId
     * @return int
     */
    private function transactPayment($loanId, $penaltyDue, $walletAmount, $loanPenaltyPaymentDueId) {

        $penaltyPaid = 0;

        if( $penaltyDue > 0 ) {
            switch ($walletAmount) {
                // pay all interest
                case  $walletAmount >= $penaltyDue:
                    {
                        $penaltyPaid = $penaltyDue;
                        $this->update(
                            ['paid_on' => now()],
                            $loanPenaltyPaymentDueId
                        );
                    }
                    break;
                // pay partial interest
                case  $walletAmount < $penaltyDue:
                    {
                        $penaltyPaid =  $walletAmount;
                    }
                    break;
                default: {
                    $penaltyPaid = 0;
                }
            }
            $this->transactionRepository->penaltyPaymentEntry($penaltyPaid, $loanPenaltyPaymentDueId, $loanId);
        }
        return $penaltyPaid;
    }

}