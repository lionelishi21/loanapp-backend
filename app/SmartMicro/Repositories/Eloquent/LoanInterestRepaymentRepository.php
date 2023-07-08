<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 21/09/2019
 * Time: 21:18
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Events\Payment\PaidLoan;
use App\Models\LoanInterestRepayment;
use App\SmartMicro\Repositories\Contracts\JournalInterface;
use App\SmartMicro\Repositories\Contracts\LoanInterestRepaymentInterface;
use App\SmartMicro\Repositories\Contracts\TransactionInterface;
use Illuminate\Support\Facades\DB;

class LoanInterestRepaymentRepository extends BaseRepository implements LoanInterestRepaymentInterface
{
    protected $model, $transactionRepository, $loanInterestRepaymentRepository, $journalRepository;

    /**
     * LoanInterestRepaymentRepository constructor.
     * @param LoanInterestRepayment $model
     * @param TransactionInterface $transactionRepository
     * @param JournalInterface $journalRepository
     */
    function __construct(LoanInterestRepayment $model, TransactionInterface $transactionRepository, JournalInterface $journalRepository)
    {
        $this->model = $model;
        $this->transactionRepository = $transactionRepository;
        $this->journalRepository = $journalRepository;
    }

    /**
     * @param $interestRepaymentId
     * @return mixed
     */
    public function paidAmount($interestRepaymentId) {
        return DB::table('transactions')
            ->select(DB::raw('COALESCE(sum(transactions.amount), 0.0) as totalPaid'))
            ->where('loan_interest_repayments_id', $interestRepaymentId)
            ->where(function($query) {
                $query->where('transaction_type', 'interest_payment');
            })
            ->first()->totalPaid;
    }

    /**
     * * For a given loan, pay its pending interest.
     *
     * Amount = available balance by the time of calling this function
     *
     * @param $amount
     * @param $loan
     * @param $date
     * @return int|mixed Amount assigned for interest payment
     */
    public function payDueInterest($amount, $loan, $date) {

        $paidInterestAmount = 0;
        $loanId = $loan['id'];

        $loanInterestRepaymentPendingRecords = $this->model
            ->where('loan_id', $loanId)
            ->where('paid_on', null)
            ->where('due_date', '<=', $date)
            ->orderBy('created_at', 'asc')
            ->get()->toArray();

        if (!is_null($loanInterestRepaymentPendingRecords) && count($loanInterestRepaymentPendingRecords) > 0) {

            foreach ($loanInterestRepaymentPendingRecords as $pendingRecord){

                 $interestDue = $pendingRecord['amount'];

                // Past partial payments
                $paidInterest = DB::table('transactions')
                    ->select(DB::raw('SUM(amount) as paid'))
                    ->where('loan_interest_repayments_id', $pendingRecord['id'])->get()->toArray();

                // Actual interest amount due
                foreach ($paidInterest as $paidAmount) {
                    if (null !== $paidAmount) {
                        $interestDue = $interestDue - ($paidAmount->paid);
                    }
                }
                // Now pay
                if($amount > 0) {
                    $interestPaid = $this->transactPayment($loanId, $interestDue, $amount, $pendingRecord['id']);
                    $paidInterestAmount = $paidInterestAmount + $interestPaid;
                }
                $amount = $amount - $paidInterestAmount;
            }
        }
        // Journal entry for loan repayment
        if ($paidInterestAmount > 0) {
            $this->journalRepository->repayLoanInterest($loan, $paidInterestAmount);
        }
        event(new PaidLoan($loanId));
        return $paidInterestAmount;
    }

    /**
     * Pay pending interest
     * @param $loanId
     * @param $interestDue
     * @param $walletAmount
     * @param $loanInterestRepaymentPendingId
     * @return int
     */
    private function transactPayment($loanId, $interestDue, $walletAmount, $loanInterestRepaymentPendingId) {

        $interestPaid = 0;

        if( $interestDue > 0 ) {
            switch ($walletAmount) {
                // pay all interest
                case  $walletAmount >= $interestDue:
                    {
                        $interestPaid = $interestDue;
                        $this->update(
                            ['paid_on' => now()],
                            $loanInterestRepaymentPendingId
                        );
                    }
                    break;
                // pay partial interest
                case  $walletAmount < $interestDue:
                    {
                        $interestPaid =  $walletAmount;
                    }
                    break;
                default: {
                    $interestPaid = 0;
                }
            }
            $this->transactionRepository->interestPaymentEntry($interestPaid, $loanInterestRepaymentPendingId, $loanId);
        }
        return $interestPaid;
    }

}