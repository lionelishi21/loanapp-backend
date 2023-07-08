<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 05/02/2020
 * Time: 16:35
 */

namespace App\Listeners\Loan;

use App\Events\Event;
use App\Events\Loan\LoanPendingAmountChecked;
use App\Events\Payment\PaymentReceived;
use App\Models\Account;
use App\SmartMicro\Repositories\Contracts\AccountInterface;
use App\SmartMicro\Repositories\Contracts\JournalInterface;
use App\SmartMicro\Repositories\Contracts\LoanInterestRepaymentInterface;
use App\SmartMicro\Repositories\Contracts\LoanInterface;
use App\SmartMicro\Repositories\Contracts\LoanPenaltyInterface;
use App\SmartMicro\Repositories\Contracts\LoanPrincipalRepaymentInterface;
use Illuminate\Support\Facades\DB;

/**
 * Pay pending amount from member deposit a/c
 * 1. Called on LoanPendingAmountChecked (daily) - to pay all loans payable at that date.
 * 2. Called on PaymentReceived to pay pending amount after a deposit has been made.
 *
 * Class CalculateLoanPaymentDue
 * @package App\Listeners\Loan
 */
class PayLoanPendingAmount
{
    /**
     * @var LoanInterface
     */
    protected $loanRepository, $penaltyRepository, $loanInterestRepaymentRepository, $loanPrincipalRepaymentRepository,
        $journalRepository, $accountRepository;

    private $accountBalance, $amountPaid, $today;

    /**
     * PayLoanPendingAmount constructor.
     * @param LoanInterface $loanInterface
     * @param AccountInterface $accountRepository
     * @param LoanPenaltyInterface $penaltyRepository
     * @param LoanInterestRepaymentInterface $loanInterestRepaymentRepository
     * @param LoanPrincipalRepaymentInterface $loanPrincipalRepaymentRepository
     * @param JournalInterface $journalRepository
     */
    public function __construct(LoanInterface $loanInterface, AccountInterface $accountRepository,
                                LoanPenaltyInterface $penaltyRepository, LoanInterestRepaymentInterface $loanInterestRepaymentRepository,
                                LoanPrincipalRepaymentInterface $loanPrincipalRepaymentRepository, JournalInterface $journalRepository)
    {
        $this->loanRepository = $loanInterface;
        $this->accountRepository = $accountRepository;

        $this->penaltyRepository = $penaltyRepository;
        $this->loanInterestRepaymentRepository = $loanInterestRepaymentRepository;
        $this->loanPrincipalRepaymentRepository = $loanPrincipalRepaymentRepository;

        $this->journalRepository = $journalRepository;

        $this->accountBalance = 0;
        $this->amountPaid = 0;

        $this->today = date('Y-m-d');
    }

    /**
     * Handle LoanPendingAmountChecked and PaymentReceived Events to repay loan.
     *
     * @param Event $event
     * @throws \Exception
     */
    public function handle(Event $event)
    {
        if ($event instanceof LoanPendingAmountChecked) {
            // an issue i suspect -- it might give even those whose due date is in future.
            // If such happens, we change this function to expect a date
            $loans = $this->loanRepository->loansWithPendingPrincipal();

            if (!is_null($loans) && count($loans)<1)
                $loans = $this->loanRepository->loansWithPendingInterest();

            if (isset($loans)){
                foreach ($loans as $loan) {
                    DB::beginTransaction();
                    try {
                        $branchId = $loan->branch_id;
                        $loanId = $loan->id;
                        $memberId = $loan->member_id;

                        $accountId = Account::where('account_name', $memberId)
                            ->where('account_code', MEMBER_DEPOSIT_CODE)
                            ->select('id')
                            ->first()['id'];

                        $this->accountBalance = -1 * ($this->accountRepository->accountBalance($accountId));

                        // If this member has a loan and have some amount in their account, lets repay the loan
                        if (!is_null($loan) && null !== $this->accountBalance && $this->accountBalance > 0) {
                            $this->pay($loan, $this->amountPaid, $this->accountBalance, $this->today);
                        }
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollback();
                        throw $e;
                    }
                }
            }

        } else if ($event instanceof PaymentReceived) {
            // Some payment has been deposited.
            // We check if the user has an active loan with pending amount
            // we charge to repay the pending amount

            DB::beginTransaction();
            try {
                $memberId = $event->memberId;

                $accountId = Account::where('account_name', $memberId)
                    ->where('account_code', MEMBER_DEPOSIT_CODE)
                    ->select('id')
                    ->first()['id'];

                $this->accountBalance = -1 * ($this->accountRepository->accountBalance($accountId));

                // Active loan for the member who just did a payment
                $activeLoan = $this->loanRepository->getActiveLoan($memberId);

                if (!is_null($activeLoan) && null !== $this->accountBalance && $this->accountBalance > 0) {
                    $this->pay($activeLoan, $this->amountPaid, $this->accountBalance, $this->today);
                }

            DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        }
    }


    /**
     * Actual pending amount payment
     * @param $loan
     * @param $amountPaid
     * @param $accountBalance
     * @param $date
     */
    private function pay($loan, $amountPaid, $accountBalance, $date) {

        $balance = $accountBalance;

        // 1. pay penalty
        $penaltyPaid = $this->penaltyRepository->payDuePenalty($balance, $loan, $date);
        $amountPaid = $amountPaid + $penaltyPaid;

        if ($penaltyPaid < $balance) {
            $balanceLessPenalty = $balance - $penaltyPaid;

            // 2. Pay interest
            $interestPaid = $this->loanInterestRepaymentRepository->payDueInterest($balanceLessPenalty, $loan, $date);
            $amountPaid = $amountPaid + $interestPaid;

            if ($interestPaid < $balanceLessPenalty) {
                $balanceLessInterest = $balanceLessPenalty - $interestPaid;

                // 3. pay principal
                $principalPaid = $this->loanPrincipalRepaymentRepository->payDuePrincipal($balanceLessInterest, $loan, $date);
                $amountPaid = $amountPaid + $principalPaid;
            }
        }
    }
}