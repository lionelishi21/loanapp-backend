<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 14:19
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Transaction;
use App\SmartMicro\Repositories\Contracts\TransactionInterface;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransactionRepository extends BaseRepository implements TransactionInterface
{
    protected $model, $today;

    /**
     * TransactionRepository constructor.
     * @param Transaction $model
     */
    function __construct(Transaction $model)
    {
        $this->model = $model;
        $this->today = date('Y-m-d');
    }

    /**
     * @param $amount
     * @param $loanPenaltyRepaymentId
     * @param $loanId
     * @return mixed|void
     */
    public function penaltyPaymentEntry($amount, $loanPenaltyRepaymentId, $loanId) {
        $data = [
            'loan_penalties_id' => $loanPenaltyRepaymentId,
            'payment_id' => null,
            'transaction_type' => 'penalty_payment',
            'transaction_date' =>  $this->today,
            'loan_id' => $loanId,
            'amount' => $amount,
        ];
        $this->create($data);
    }

    /**
     * @param $loanPenaltyRepaymentId
     * @param $amount
     * @param $loanId
     */
    public function penaltyWaiverEntry($loanPenaltyRepaymentId, $amount, $loanId) {
        $data = [
            'loan_penalties_id' => $loanPenaltyRepaymentId,
            'payment_id' => null,
            'transaction_type' => 'penalty_waiver',
            'transaction_date' =>  $this->today,
            'loan_id' => $loanId,
            'amount' => $amount,
        ];
        $this->create($data);
    }

    /**
     * @param $amount
     * @param $loanInterestRepaymentId
     * @param $loanId
     * @return mixed|void
     */
    public function interestPaymentEntry($amount, $loanInterestRepaymentId, $loanId) {
        $data = [
            'loan_interest_repayments_id' => $loanInterestRepaymentId,
            'payment_id' => null,
            'transaction_type' => 'interest_payment',
            'transaction_date' =>  $this->today,
            'loan_id' => $loanId,
            'amount' => $amount,
        ];
        $this->create($data);
    }

    /**
     * @param $amount
     * @param $loanInterestRepaymentId
     * @param $loanId
     * @return mixed|void
     */
    public function interestWaiverEntry($amount, $loanInterestRepaymentId, $loanId) {
        $data = [
            'loan_interest_repayments_id' => $loanInterestRepaymentId,
            'payment_id' => '',
            'transaction_type' => 'interest_waiver',
            'transaction_date' =>  $this->today,
            'loan_id' => $loanId,
            'amount' => $amount,
        ];
        $this->create($data);
    }

    /**
     * @param $amount
     * @param $loanPrincipalRepaymentId
     * @param $loanId
     * @return mixed|void
     */
    public function principalPaymentEntry($amount, $loanPrincipalRepaymentId, $loanId) {
        $data = [
            'loan_principal_repayments_id' => $loanPrincipalRepaymentId,
            'payment_id' => null,
            'transaction_type' => 'principal_payment',
            'transaction_date' => $this->today,
            'loan_id' => $loanId,
            'amount' => $amount,
        ];
        $this->create($data);
    }

    /**
     * Reduce Principal balance. Either by excess periodical payment or manual adjustment
     * @param $amount
     * @param $paymentId
     * @param $loan
     * @return mixed|void
     * @throws \Exception
     */
    public function balanceReductionEntry($amount, $paymentId, $loan) {
        DB::beginTransaction();
        try
        {
            $loanBalance = $this->checkLoanBalance($loan);
            if($loanBalance > 0 && $amount <= $loanBalance) {
                // reduce the balance
                $this->create([
                    'payment_id' => $paymentId,
                    'transaction_type' => 'balance_reduction',
                    'transaction_date' => $this->today,
                    'loan_id' => $loan['id'],
                    'amount' => $amount,
                ]);
            }else{
                // Loan has no balance or amount provided is more than balance.
                throw new NotFoundHttpException('Amount is more than loan balance. We do not take deposits. ');
            }
        DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Check loan balance
     * @param $loan
     * @return mixed
     */
    private function checkLoanBalance($loan) {
        $loanAmount = $loan['amount_approved'];
        $paidAmount = $this->model
            ->whereIn('transaction_type', ['balance_reduction', 'principal_payment'])
            ->where('loan_id', $loan['id'])
            ->sum('amount');
        return $loanAmount - $paidAmount;
    }

}