<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 21/09/2019
 * Time: 21:24
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Events\Payment\PaidLoan;
use App\Models\Loan;
use App\Models\LoanPrincipalRepayment;
use App\SmartMicro\Repositories\Contracts\JournalInterface;
use App\SmartMicro\Repositories\Contracts\LoanPrincipalRepaymentInterface;
use App\SmartMicro\Repositories\Contracts\TransactionInterface;
use Illuminate\Support\Facades\DB;

class LoanPrincipalRepaymentRepository extends BaseRepository implements LoanPrincipalRepaymentInterface
{
    protected $model, $transactionRepository, $journalRepository;

    /**
     * LoanPrincipalRepaymentRepository constructor.
     * @param LoanPrincipalRepayment $model
     * @param TransactionInterface $transactionRepository
     * @param JournalInterface $journalRepository
     */
    function __construct(LoanPrincipalRepayment $model, TransactionInterface $transactionRepository, JournalInterface $journalRepository)
    {
        $this->model = $model;
        $this->transactionRepository = $transactionRepository;
        $this->journalRepository = $journalRepository;
    }

    /**
     * @param $principalRepaymentId
     * @return mixed
     */
    public function paidAmount($principalRepaymentId) {
        return DB::table('transactions')
            ->select(DB::raw('COALESCE(sum(transactions.amount), 0.0) as totalPaid'))
            ->where('loan_principal_repayments_id', $principalRepaymentId)
            ->where(function($query) {
                $query->where('transaction_type', 'principal_payment');
            })
            ->first()->totalPaid;
    }

    /**
     * For a given loan, pay any pending Principal amount
     *
     * @param $amount
     * @param $loan
     * @param $date
     * @return int|mixed
     */
    public function payDuePrincipal($amount, $loan, $date) {
        $paidPrincipalAmount = 0;
        $loanId = $loan['id'];

        $loanPrincipalRepaymentDueRecords = $this->model
            ->where('loan_id', $loanId)
            ->where('paid_on', null)
            ->where('due_date', '<=', $date)
            ->orderBy('created_at', 'asc')
            ->get()->toArray();

        if (!is_null($loanPrincipalRepaymentDueRecords) && count($loanPrincipalRepaymentDueRecords) > 0) {

            foreach ($loanPrincipalRepaymentDueRecords as $dueRecord){

                $principalDue = $dueRecord['amount'];

                // Past partial payments
                $paidPrincipal = DB::table('transactions')
                    ->select(DB::raw('SUM(amount) as paid'))
                    ->where('loan_principal_repayments_id', $dueRecord['id'])->get()->toArray();

                // Actual principal amount due
                foreach ($paidPrincipal as $paidAmount) {
                    if (null !== $paidAmount) {
                        $principalDue = $principalDue - ($paidAmount->paid);
                    }
                }
                // Now pay
                if($amount > 0) {
                    $principalPaid = $this->transactPayment($loanId, $principalDue, $amount, $dueRecord['id']);
                    $paidPrincipalAmount = $paidPrincipalAmount + $principalPaid;
                }
                $amount = $amount - $paidPrincipalAmount;
            }
        }
        // Journal entry for loan repayment
        if ($paidPrincipalAmount > 0) {
            $this->journalRepository->repayLoanPrincipal($loan, $paidPrincipalAmount);
        }
        event(new PaidLoan($loanId));
        return $paidPrincipalAmount;
    }

    /**
     * Pay pending principal
     * @param $loanId
     * @param $principalDue
     * @param $walletAmount
     * @param $loanPrincipalRepaymentDueId
     * @return int
     */
    private function transactPayment($loanId, $principalDue, $walletAmount, $loanPrincipalRepaymentDueId) {

        $principalPaid = 0;

        if( $principalDue > 0 ) {
            switch ($walletAmount) {
                // pay all principal
                case  $walletAmount >= $principalDue:
                    {
                        $principalPaid = $principalDue;
                        $this->update(
                            ['paid_on' => now()],
                            $loanPrincipalRepaymentDueId
                        );
                    }
                    break;
                // pay partial principal
                case  $walletAmount < $principalDue:
                    {
                        $principalPaid =  $walletAmount;
                    }
                    break;
                default: {
                    $principalPaid = 0;
                }
            }
            $this->transactionRepository->principalPaymentEntry($principalPaid, $loanPrincipalRepaymentDueId, $loanId);
        }
        return $principalPaid;
    }

}